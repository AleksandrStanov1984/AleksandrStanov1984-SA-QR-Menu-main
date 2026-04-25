{{-- resources/views/admin/restaurants/components/menu-builder/_item-modal.blade.php --}}

@php
    use App\Support\Permissions;

    $user = auth()->user();
    $isSuper = (bool)($user?->is_super_admin);

    $canImagesFeature   = (bool) $restaurant->feature('images');
    $canDetailsFeature  = (bool) $restaurant->feature('long_description');

    $imageService = app(\App\Services\ImageService::class);
    $fallbackFood = $imageService->food(null);

@endphp

<div class="modal" id="mbModalItem" aria-hidden="true">
    <div class="modal__backdrop" data-mb-close></div>
    <div class="modal__panel">
        <div class="mb-row">
            <strong>{{ __('admin.menu_builder.add_item') }}</strong>
            <button class="btn small" type="button" data-mb-close aria-label="{{ __('admin.actions.close') }}">✕</button>
        </div>

        <form method="POST" enctype="multipart/form-data"
              action="#"
              id="mbItemForm">
            @csrf

            <input type="hidden" id="mbItemSectionId" name="_section_id" value="">

            <div class="grid" style="margin-top:10px;">
                <div class="col6">
                    <label>{{ __('admin.menu_builder.price') ?? 'Price' }}</label>
                    <input
                        name="price"
                        maxlength="20"
                        inputmode="decimal"
                        pattern="^\d+(?:[.,]\d{1,2})?$"
                        placeholder="0.00"
                        required
                    >
                    <div class="mb-muted" style="margin-top:6px;">
                        {{ __('admin.menu_builder.price_hint') }}
                    </div>
                </div>

                <div class="col6"></div>
            </div>

            @if($isSuper)
                <hr style="border:0;border-top:1px solid var(--line); margin:12px 0;">

                <div class="grid">
                    <div class="col12">
                        <div class="mb-muted">{{ __('admin.menu_builder.styles_hint') }}</div>
                    </div>

                    @foreach(['title','desc','details'] as $k)
                        <div class="col4">
                            <label>{{ __('admin.menu_builder.style_font', ['field' => __('admin.menu_builder.field_'.$k)]) }}</label>
                            <select name="style[{{ $k }}][font]">
                                <option value="">{{ __('admin.common.dash') }}</option>
                                <option value="inter">Inter</option>
                                <option value="poppins">Poppins</option>
                                <option value="roboto">Roboto</option>
                                <option value="playfair">Playfair</option>
                            </select>
                        </div>

                        <div class="col4">
                            <label>{{ __('admin.menu_builder.style_color', ['field' => __('admin.menu_builder.field_'.$k)]) }}</label>
                            <input name="style[{{ $k }}][color]" placeholder="#FFFFFF">
                        </div>

                        <div class="col4">
                            <label>{{ __('admin.menu_builder.style_size', ['field' => __('admin.menu_builder.field_'.$k)]) }}</label>
                            <input type="number" name="style[{{ $k }}][size]" min="8" max="72" step="1" value="14">
                        </div>
                    @endforeach
                </div>
            @endif
            @php
                $locale = $restaurant->default_locale ?? 'de';
            @endphp

            <div class="grid" style="margin-top:8px;">

                <div class="col12">

                    {{-- TITLE --}}
                    <label>
                        {{ __('admin.menu_builder.title_locale', ['locale' => strtoupper($locale)]) }}
                    </label>

                    <input
                        name="translations[{{ $locale }}][title]"
                        maxlength="50"
                        required
                        style="width:100%;"
                    >

                    {{-- DESCRIPTION --}}
                    <label>
                        {{ __('admin.menu_builder.description_locale', ['locale' => strtoupper($locale)]) }}
                    </label>

                    <textarea
                        name="translations[{{ $locale }}][description]"
                        maxlength="100"
                        style="width:100%; min-height:90px; padding:10px 12px; border-radius:10px; border:1px solid var(--line); background:rgba(255,255,255,.03); color:var(--text);"
                    ></textarea>

                    {{-- DETAILS (PRO ONLY) --}}
                    @if($canDetailsFeature)
                        <label>
                            {{ __('admin.menu_builder.details_locale', ['locale' => strtoupper($locale)]) }}
                        </label>

                        <textarea
                            name="translations[{{ $locale }}][details]"
                            maxlength="255"
                            style="width:100%; min-height:90px; padding:10px 12px; border-radius:10px; border:1px solid var(--line); background:rgba(255,255,255,.03); color:var(--text);"
                        ></textarea>
                    @endif

                </div>

            </div>

            @if($canImagesFeature)
                <div class="sidebar-divider"></div>

                <div class="mb-image-block">

                    <div class="mb-image-preview">
                        <img id="mbItemImagePreview"
                             src="{{ $fallbackFood }}"
                             data-fallback-src="{{ $fallbackFood }}">

                        <button type="button"
                                id="mbItemImageDelete"
                                class="mb-image-delete"
                                style="display:none;">
                            ✕
                        </button>
                    </div>

                    <div class="mb-image-controls">

                        <label class="mb-file-btn">
                            {{ __('menu.image_select') }}
                            <input type="file"
                                   name="image"
                                   id="mbItemImageInput"
                                   accept=".jpg,.jpeg,.png,.webp">
                        </label>

                        <div class="mb-image-hint">
                            {{ __('menu.image_hint') }}
                        </div>

                    </div>

                </div>

                <input type="hidden" name="remove_image" id="mbRemoveImage" value="0">
            @endif

            <div style="margin-top:12px; display:flex; justify-content:flex-end; gap:10px;">
                <button class="btn ok" type="submit">{{ __('admin.actions.create') }}</button>
            </div>
        </form>
    </div>
</div>
