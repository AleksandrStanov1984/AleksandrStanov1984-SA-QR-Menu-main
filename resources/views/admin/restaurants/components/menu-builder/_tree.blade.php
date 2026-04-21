{{-- resources/views/admin/restaurants/components/menu-builder/_tree.blade.php --}}

@php
  use App\Support\Permissions;

  $user = auth()->user();
  $isSuper = (bool)($user->is_super_admin ?? false);

  $isTrashed = function($m): bool {
    return method_exists($m, 'trashed') ? (bool)$m->trashed() : false;
  };
@endphp

@if($isSuper)
  <div class="card" style="padding:10px; margin-top:10px;">
    <label style="display:flex; align-items:center; gap:10px; margin:0;">
      <input type="checkbox" id="mbShowDeleted" checked>
      <span>{{ __('admin.menu_builder.show_deleted') }}</span>
    </label>
    <div class="mb-muted" style="margin-top:6px;">
      {{ __('admin.menu_builder.show_deleted_hint')  }}
    </div>
  </div>
@endif

<div style="margin-top:14px; display:flex; flex-direction:column; gap:10px;">
  @forelse($menuTree as $cat)
    @php
      $catInactive = !$cat->is_active;
      $catDeleted  = $isTrashed($cat);

      $catLocked = $catInactive;
    @endphp

        <div class="card {{ $catInactive ? 'mb-inactive' : '' }} {{ $catDeleted ? 'mb-deleted' : '' }}"
             data-section-id="{{ $cat->id }}"
             data-section-row="category"
             data-deleted="{{ $catDeleted ? '1' : '0' }}"
             data-search="{{ mb_strtolower($tTitle($cat, $defaultLocale)) }}"
             data-type="category"
             style="padding:12px;">
      <div class="mb-row">
        <div class="mb-left">
          {{-- TOGGLE CATEGORY --}}
          @if(Permissions::can($user, 'categories.toggle'))
            <form method="POST" action="{{ route('admin.restaurants.sections.toggle', [$restaurant, $cat]) }}">
              @csrf
              <label style="margin:0; display:flex; align-items:center; gap:8px;">
                <input type="checkbox" @checked($cat->is_active) onchange="this.form.submit()">
                <span class="mb-item-title">
                  {{ $tTitle($cat, $defaultLocale) ?: ('Category #'.$cat->id) }}
                </span>
              </label>
            </form>
          @else
            <div style="display:flex; align-items:center; gap:8px;">
              <input type="checkbox" @checked($cat->is_active) disabled>
              <span class="mb-item-title">
                {{ $tTitle($cat, $defaultLocale) ?: ('Category #'.$cat->id) }}
              </span>
            </div>
          @endif

          <span class="mb-mini">ID: {{ $cat->id }}</span>

          @if($catDeleted)
            <span class="pill red">{{ __('admin.common.deleted') }}</span>
          @elseif($catInactive)
            <span class="pill red">{{ __('admin.common.disabled') }}</span>
          @endif
        </div>

        <div class="mb-right mb-actions">
          {{-- EDIT CATEGORY --}}
          @if(Permissions::can($user, 'categories.edit'))
            <button class="btn small secondary" type="button"
                    {{ $catLocked ? 'disabled' : '' }}
                    data-mb-edit="section"
                    data-section-id="{{ $cat->id }}"
                    data-section-update-url="{{ route('admin.restaurants.sections.update', [$restaurant, $cat]) }}"
                    data-section-titles='@json($cat->translations->pluck("title","locale"))'
                    data-mb-open="mbModalCategory">
              {{ __('admin.common.edit') ?? 'Edit' }}
            </button>
          @endif

          {{-- CREATE SUBCATEGORY --}}
          @if(Permissions::can($user, 'subcategories.create'))
            <button class="btn small secondary" type="button"
                    {{ $catLocked ? 'disabled' : '' }}
                    data-mb-open="mbModalSubcategory"
                    data-parent-id="{{ $cat->id }}">
              + {{ __('admin.menu_builder.add_subcategory') }}
            </button>
          @endif

          {{-- CREATE ITEM --}}
          @if(Permissions::can($user, 'items.create'))
            <button class="btn small" type="button"
                    {{ $catLocked ? 'disabled' : '' }}
                    data-mb-open="mbModalItem"
                    data-section-id="{{ $cat->id }}">
              + {{ __('admin.menu_builder.add_item') }}
            </button>
          @endif

          {{-- DELETE CATEGORY (via modal) --}}
          @if(Permissions::can($user, 'categories.delete'))
            <button class="btn small danger" type="button"
                    {{ $catLocked ? 'disabled' : '' }}
                    data-confirm-delete="1"
                    data-delete-url="{{ route('admin.restaurants.sections.destroy', [$restaurant, $cat]) }}"
                    data-delete-text="{{ __('admin.confirm.delete_category') }}"
                    data-delete-hint="{{ $tTitle($cat, $defaultLocale) ?: ('Category #'.$cat->id) }}">
              {{ __('admin.actions.delete') ?? 'Delete' }}
            </button>
          @endif
        </div>
      </div>

      {{-- Items under category --}}
      @include('admin.restaurants.components.menu-builder._items-list', [
        'restaurant' => $restaurant,
        'section' => $cat,
        'defaultLocale' => $defaultLocale,
        'ancestorLocked' => $catLocked,
      ])

      {{-- Subcategories --}}
      <div class="mb-sub" style="margin-top:12px;">
        @foreach($cat->children as $sub)
          @php
            $subInactive = !$sub->is_active;
            $subDeleted  = $isTrashed($sub);

            $subLocked = $catLocked;

            $subItemsLocked = $catLocked || $subInactive;
          @endphp

              <div class="card-no-border {{ ($subInactive || $catLocked) ? 'mb-inactive' : '' }} {{ $subDeleted ? 'mb-deleted' : '' }}"
                   data-section-id="{{ $sub->id }}"
                   data-section-row="subcategory"
                   data-deleted="{{ $subDeleted ? '1' : '0' }}"
                   data-search="{{ mb_strtolower($tTitle($sub, $defaultLocale)) }}"
                   data-type="subcategory"
                   style="padding:12px; margin-top:10px;">
            <div class="mb-row">
              <div class="mb-left">
                {{-- TOGGLE SUBCATEGORY --}}
                @if(Permissions::can($user, 'subcategories.toggle'))
                  <form method="POST" action="{{ route('admin.restaurants.sections.toggle', [$restaurant, $sub]) }}">
                    @csrf
                    <label style="margin:0; display:flex; align-items:center; gap:8px;">
                      <input type="checkbox"
                             @checked($sub->is_active)
                             {{ $subLocked ? 'disabled' : '' }}
                             onchange="this.form.submit()">
                      <span class="mb-item-title">
                        {{ $tTitle($sub, $defaultLocale) ?: ('Subcategory #'.$sub->id) }}
                      </span>
                    </label>
                  </form>
                @else
                  <div style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" @checked($sub->is_active) disabled>
                    <span class="mb-item-title">
                      {{ $tTitle($sub, $defaultLocale) ?: ('Subcategory #'.$sub->id) }}
                    </span>
                  </div>
                @endif

                <span class="mb-mini">ID: {{ $sub->id }}</span>

                @if($subDeleted)
                  <span class="pill red">{{ __('admin.common.deleted') ?? 'deleted' }}</span>
                @elseif($catLocked || $subInactive)
                  <span class="pill red">{{ __('admin.common.disabled') ?? 'disabled' }}</span>
                @endif
              </div>

              <div class="mb-right mb-actions">
                {{-- EDIT SUBCATEGORY --}}
                @if(Permissions::can($user, 'subcategories.edit'))
                  <button class="btn small secondary" type="button"
                          {{ $subLocked ? 'disabled' : '' }}
                          data-mb-edit="section"
                          data-section-id="{{ $sub->id }}"
                          data-section-update-url="{{ route('admin.restaurants.sections.update', [$restaurant, $sub]) }}"
                          data-section-titles='@json($sub->translations->pluck("title","locale"))'
                          data-parent-id="{{ $cat->id }}"
                          data-mb-open="mbModalSubcategory">
                    {{ __('admin.common.edit') ?? 'Edit' }}
                  </button>
                @endif

                {{-- CREATE ITEM --}}
                @if(Permissions::can($user, 'items.create'))
                  <button class="btn small" type="button"
                          {{ $subLocked ? 'disabled' : '' }}
                          data-mb-open="mbModalItem"
                          data-section-id="{{ $sub->id }}">
                    + {{ __('admin.menu_builder.add_item') }}
                  </button>
                @endif

                {{-- DELETE SUBCATEGORY (via modal) --}}
                @if(Permissions::can($user, 'subcategories.delete'))
                  <button class="btn small danger" type="button"
                          {{ $subLocked ? 'disabled' : '' }}
                          data-confirm-delete="1"
                          data-delete-url="{{ route('admin.restaurants.sections.destroy', [$restaurant, $sub]) }}"
                          data-delete-text="{{ __('admin.confirm.delete_subcategory') }}"
                          data-delete-hint="{{ $tTitle($sub, $defaultLocale) ?: ('Subcategory #'.$sub->id) }}">
                    {{ __('admin.actions.delete') ?? 'Delete' }}
                  </button>
                @endif
              </div>
            </div>

            @include('admin.restaurants.components.menu-builder._items-list', [
              'restaurant' => $restaurant,
              'section' => $sub,
              'defaultLocale' => $defaultLocale,
              'ancestorLocked' => $subItemsLocked,
            ])
          </div>
        @endforeach
      </div>
    </div>
  @empty
    <div class="mb-muted" style="margin-top:10px;">
      {{ __('admin.menu_builder.empty') }}
    </div>
  @endforelse
</div>
