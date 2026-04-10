@php
    use App\Support\Permissions;

    $items = $section->items ?? collect();
    $user  = auth()->user();

    // если родитель (категория/подкатегория) выключен — блокируем всё внутри списка
    $ancestorLocked = (bool)($ancestorLocked ?? false);

    $title = function($it) use ($defaultLocale) {
      $trs = $it->translations ?? collect();
      $tr  = $trs->firstWhere('locale', $defaultLocale) ?? $trs->first();
      $val = $tr?->title ?? null;
      $val = is_string($val) ? trim($val) : '';
      return $val !== '' ? $val : ('Item #'.$it->id);
    };

    // плановые фичи
    $canImagesFeature     = (bool) $restaurant->feature('images');
    $canSpicyFeature      = (bool) $restaurant->feature('spicy');
    $canIsNewFeature      = (bool) $restaurant->feature('is_new');
    $canDishOfDayFeature  = (bool) $restaurant->feature('dish_of_day');
    $canLongDescFeature   = (bool) $restaurant->feature('long_description');
    $canCarouselFeature   = (bool) $restaurant->feature('carousel');

    // права
    $canActive     = Permissions::can($user, 'items.toggle.active');
    $canShowImage  = Permissions::can($user, 'items.toggle.show_image');
    $canIsNew      = Permissions::can($user, 'items.flag.new');
    $canDishOfDay  = Permissions::can($user, 'items.flag.dish_of_day');
    $canSpicy      = Permissions::can($user, 'items.flag.spicy');
    $canDelete     = Permissions::can($user, 'items.delete');
    $canEdit       = Permissions::can($user, 'items.edit');

    // для текста: показывать description/details как кнопки
    $canViewDesc   = Permissions::can($user, 'items.view.description') || $canEdit;
    $canViewDetails= Permissions::can($user, 'items.view.details') || $canEdit;

    // для загрузки/просмотра картинки в админке
    $canImageUpload = Permissions::can($user, 'items.image.upload') || $canEdit;

    $metaUrlBase = url('/admin/restaurants/'.$restaurant->id.'/items');

    $isTrashed = function($m): bool {
      return method_exists($m, 'trashed') ? (bool)$m->trashed() : false;
    };

    // дефолтная картинка
    $fallbackImg = asset('assets/classic-menu/images/image-fallback.png');

    $itemImageUrl = function ($it) use ($fallbackImg) {
      if (!empty($it->image_url)) return $it->image_url;
      if (!empty($it->image))     return asset($it->image);

      if (!empty($it->image_path)) {
        try {
          return \Illuminate\Support\Facades\Storage::url($it->image_path);
        } catch (\Throwable $e) {}
      }

      return $fallbackImg;
    };

@endphp

<div class="mb-items"
     data-sortable-items
     data-reorder-url="{{ route('admin.restaurants.items.reorder', [$restaurant, $section]) }}"
     data-item-meta-base="{{ $metaUrlBase }}">

    @foreach($items as $it)
        @php
            $m = is_array($it->meta ?? null) ? $it->meta : (json_decode($it->meta ?? '[]', true) ?: []);
            $inactive = !$it->is_active;
            $deleted  = $isTrashed($it);

            $isNew      = !empty($m['is_new']);
            $isDay      = !empty($m['dish_of_day']);
            $isBest     = !empty($m['bestseller']);
            $showImage  = array_key_exists('show_image', $m) ? (bool)$m['show_image'] : true;
            $spicy      = (int)($m['spicy'] ?? 0);

            // carousel ui-prep only
            $carouselEnabled = !empty($m['carousel_enabled']);
            $carouselSource  = $m['carousel_source'] ?? 'bestseller';

            // цена (берём item->price или meta.price)
            $priceVal = $it->price ?? ($m['price'] ?? null);
            $priceTxt = ($priceVal !== null && $priceVal !== '') ? (string)$priceVal : '';

            // тексты для модалки
            $trs = $it->translations ?? collect();
            $tr  = $trs->firstWhere('locale', $defaultLocale) ?? $trs->first();
            $descTxt = trim((string)($tr?->description ?? ''));
            $detTxt  = trim((string)($tr?->details ?? ''));

            $editPayload = [
              'id' => $it->id,
              'section_id' => $it->section_id,
              'translations' => ($trs)->map(fn($tr) => [
                'locale' => $tr->locale,
                'title' => $tr->title,
                'description' => $tr->description,
                'details' => $tr->details,
              ])->values(),
              'style' => $m['style'] ?? null,
              'price' => $priceVal,
            ];

            // если родитель выключен — блокируем ВСЁ (включая is_active item)
            $rowLocked = $ancestorLocked;

            // active для UI: активна только если item активен И родитель включен
            $rowActiveForUi = (!$inactive && !$ancestorLocked) ? '1' : '0';

            $getImage = function ($it) {
              foreach (['image_path', 'image', 'image_url'] as $field) {
                  if (!empty($it->{$field})) {
                      return $it->{$field};
                  }
              }
              return null;
            };

            $imgUrl = app(\App\Services\ImageService::class)->url($getImage($it));

            // id аккордеона
            $accId = 'mbItemAcc_'.$it->id;
        @endphp

        <div class="mb-item {{ ($inactive || $ancestorLocked) ? 'mb-inactive' : '' }} {{ $deleted ? 'mb-deleted' : '' }}"
             data-item-id="{{ $it->id }}"
             data-item-row
             data-item-active="{{ $rowActiveForUi }}"
             data-deleted="{{ $deleted ? '1' : '0' }}"
             style="border:1px solid var(--line); border-radius:16px; padding:10px; background:rgba(255,255,255,.03);">

            {{-- АККОРДЕОН --}}
            <details id="{{ $accId }}" open style="margin:0;">
                <summary class="mb-acc-summary"
                         style="list-style:none; cursor:pointer; padding-left:10px;"
                         onclick="return true;">

                    {{-- HEADER --}}
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; width:100%;">

                        {{-- LEFT: фиксированная зона под handle+checkbox --}}
                        <div style="display:flex; align-items:center; gap:10px; min-width:0; flex:1 1 auto;">

                            {{-- handle --}}
                            <span class="mb-handle"
                                  title="Drag"
                                  data-no-accordion
                                  onclick="event.stopPropagation();"
                                  style="display:inline-flex; align-items:center; justify-content:center;">
                ≡
              </span>

                            {{-- active checkbox --}}
                            @if($canActive)
                                <input type="checkbox"
                                       data-no-accordion
                                       onclick="event.stopPropagation();"
                                       data-item-meta="is_active"
                                       data-item-id="{{ $it->id }}"
                                       @checked($it->is_active)
                                       {{ $rowLocked ? 'disabled' : '' }}
                                       style="margin:0; transform:translateY(1px);">
                            @else
                                <input type="checkbox"
                                       data-no-accordion
                                       onclick="event.stopPropagation();"
                                       disabled
                                       @checked($it->is_active)
                                       style="margin:0; transform:translateY(1px);">
                            @endif

                            {{-- TITLE: без ellipsis--}}
                            <span class="mb-item-title"
                                  style="display:block; min-width:0; flex:1 1 auto; white-space:normal; overflow:visible;">
                {{ $title($it) }}
              </span>
                        </div>

                        {{-- RIGHT: price + caret --}}
                        <div style="display:flex; align-items:center; gap:10px; flex:0 0 auto;">

                            <div style="font-weight:700; color:var(--text); text-align:right; min-width:80px;">
                                @if($priceTxt !== '')
                                    {{ $priceTxt }}
                                @else
                                    <span style="opacity:.55;">&nbsp;</span>
                                @endif
                            </div>

                            {{-- caret  --}}
                            <span class="mb-acc-caret" aria-hidden="true"></span>
                        </div>
                    </div>
                </summary>

                {{-- BODY --}}
                <div style="margin-top:10px; display:flex; gap:14px; align-items:stretch; justify-content:space-between;">

                    {{-- LEFT block --}}
                    <div style="flex:1 1 auto; min-width:0;">

                        {{-- IMAGE block  --}}
                        <div style="margin-bottom:10px; display:flex; gap:12px; align-items:flex-start;">
                            <div style="width:140px; height:90px; border-radius:12px; overflow:hidden; border:1px solid var(--line); background:rgba(255,255,255,.04); flex:0 0 auto;">
                                <img src="{{ $imgUrl }}"
                                     alt="item image"
                                     style="width:100%; height:100%; object-fit:cover; display:block;">
                            </div>

                            {{-- Кнопки текста (Description/Details) --}}
                            <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center; min-width:0;">
                                @if($canViewDesc && $descTxt !== '')
                                    <button type="button"
                                            class="btn small secondary"
                                            data-open-text-modal="1"
                                            data-text-title="{{ __('admin.menu_builder.description') ?? 'Описание' }}"
                                            data-text-body='@json($descTxt)'>
                                        {{ __('admin.menu_builder.description') ?? 'Описание' }}
                                    </button>
                                @endif

                                @if($canLongDescFeature && $canViewDetails && $detTxt !== '')
                                    <button type="button"
                                            class="btn small secondary"
                                            data-open-text-modal="1"
                                            data-text-title="{{ __('admin.menu_builder.details') ?? 'Подробно' }}"
                                            data-text-body='@json($detTxt)'>
                                        {{ __('admin.menu_builder.details') ?? 'Подробно' }}
                                    </button>
                                @endif

                                @if($canImagesFeature && $canImageUpload)
                                    <span style="opacity:.65; font-size:13px;">
                    {{ __('admin.menu_builder.image_hint') ?? 'Изображение: показывается загруженное или дефолтное.' }}
                  </span>
                                @endif
                            </div>
                        </div>

                        {{-- чекбоксы --}}
                        <div style="display:flex; flex-direction:column; gap:8px; margin-top:4px;">
                            @if($canImagesFeature && $canShowImage)
                                <label class="perm-item" style="margin:0; display:flex; align-items:center; gap:8px;">
                                    <input type="checkbox"
                                           data-item-meta="show_image"
                                           data-item-id="{{ $it->id }}"
                                           @checked($showImage)
                                           data-disable-when-inactive
                                        {{ $rowLocked ? 'disabled' : '' }}>
                                    <span>{{ __('admin.menu_builder.show_image_modal') }}</span>
                                </label>
                            @endif

                            @if($canIsNewFeature && $canIsNew)
                                <label class="perm-item" style="margin:0; display:flex; align-items:center; gap:8px;">
                                    <input type="checkbox"
                                           data-item-meta="is_new"
                                           data-item-id="{{ $it->id }}"
                                           @checked($isNew)
                                           data-disable-when-inactive
                                        {{ $rowLocked ? 'disabled' : '' }}>
                                    <span>{{ __('admin.menu_builder.flag_new') }}</span>
                                </label>
                            @endif

                            @if($canDishOfDayFeature && $canDishOfDay)
                                <label class="perm-item" style="margin:0; display:flex; align-items:center; gap:8px;">
                                    <input type="checkbox"
                                           data-item-meta="dish_of_day"
                                           data-item-id="{{ $it->id }}"
                                           @checked($isDay)
                                           data-disable-when-inactive
                                        {{ $rowLocked ? 'disabled' : '' }}>
                                    <span>{{ __('admin.menu_builder.flag_dish_of_day') }}</span>
                                </label>
                            @endif

                            @if($canCarouselFeature)
                                <label class="perm-item" style="margin:0; display:flex; align-items:center; gap:8px;">
                                    <input type="checkbox"
                                           data-item-meta="bestseller"
                                           data-item-id="{{ $it->id }}"
                                           @checked($isBest)
                                           data-disable-when-inactive
                                        {{ $rowLocked ? 'disabled' : '' }}>
                                    <span>{{ __('menu.bestseller') }}</span>
                                </label>
                            @endif

                        </div>

                        {{-- Острота --}}
                        @if($canSpicyFeature && $canSpicy)
                            <div style="margin-top:12px;" >
                                <div style="margin-bottom:6px;">{{ __('admin.menu_builder.spicy') }}</div>
                                <select data-item-meta="spicy"
                                        data-item-id="{{ $it->id }}"
                                        data-disable-when-inactive
                                        {{ $rowLocked ? 'disabled' : '' }}
                                        class="ui-select-native"
                                        style="width:120px; color: var(--text); background: rgba(255,255,255,.08); border:1px solid var(--line); border-radius:10px; padding:6px 10px;">
                                    @for($i=0;$i<=5;$i++)
                                        <option value="{{ $i }}" @selected($spicy === $i)>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        @endif

                    </div>

                    {{-- RIGHT block: кнопки снизу справа --}}
                    <div style="flex:0 0 auto; display:flex; flex-direction:column; align-items:flex-end; justify-content:flex-end;">
                        <div class="mb-item-actions" style="display:flex; align-items:center; gap:10px; margin-top:auto;">
                            @if($canEdit)
                                <button class="btn small secondary"
                                        type="button"
                                        data-edit-item="1"
                                        data-item='@json($editPayload)'
                                        data-disable-when-inactive
                                    {{ $rowLocked ? 'disabled' : '' }}>
                                    {{ __('admin.common.edit') }}
                                </button>
                            @endif

                            @if($canDelete)
                                <button class="btn small danger"
                                        type="button"
                                        data-confirm-delete="1"
                                        data-disable-when-inactive
                                        {{ $rowLocked ? 'disabled' : '' }}
                                        data-delete-url="{{ route('admin.restaurants.items.destroy', [$restaurant, $it]) }}"
                                        data-delete-text="{{ __('admin.confirm.delete_item') }}"
                                        data-delete-hint="{{ $title($it) }}">
                                    {{ __('admin.actions.delete') }}
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            </details>
        </div>
    @endforeach
</div>
