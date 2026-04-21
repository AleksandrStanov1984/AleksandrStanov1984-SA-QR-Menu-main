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
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <span>{{ __('admin.menu_builder.show_deleted') }}</span>

            <label class="mb-switch">
                <input type="checkbox" id="mbShowDeleted" checked>
                <span class="mb-switch__ui"></span>
            </label>
        </div>

        <div class="mb-muted" style="margin-top:6px;">
            {{ __('admin.menu_builder.show_deleted_hint') }}
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
             data-type="category"
             style="padding:12px;">

            <div class="mb-row">

                <div class="mb-left">

                    {{-- CATEGORY SWITCH --}}
                    @if(Permissions::can($user, 'categories.toggle'))
                        <form method="POST" action="{{ route('admin.restaurants.sections.toggle', [$restaurant, $cat]) }}">
                            @csrf

                            <div style="display:flex; align-items:center; justify-content:space-between; width:100%; gap:10px;">
            <span class="mb-item-title">
              {{ $tTitle($cat, $defaultLocale) ?: ('Category #'.$cat->id) }}
            </span>

                                <label class="mb-switch">
                                    <input type="checkbox"
                                           @checked($cat->is_active)
                                           onchange="this.form.submit()">
                                    <span class="mb-switch__ui"></span>
                                </label>
                            </div>
                        </form>
                    @else
                        <div style="display:flex; align-items:center; justify-content:space-between; width:100%; gap:10px;">
          <span class="mb-item-title">
            {{ $tTitle($cat, $defaultLocale) ?: ('Category #'.$cat->id) }}
          </span>

                            <label class="mb-switch">
                                <input type="checkbox" disabled @checked($cat->is_active)>
                                <span class="mb-switch__ui"></span>
                            </label>
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
                    @if(Permissions::can($user, 'categories.edit'))
                        <button class="btn small secondary" type="button"
                                {{ $catLocked ? 'disabled' : '' }}
                                data-mb-edit="section"
                                data-section-id="{{ $cat->id }}">
                            {{ __('admin.common.edit') }}
                        </button>
                    @endif

                    @if(Permissions::can($user, 'subcategories.create'))
                        <button class="btn small secondary" type="button"
                            {{ $catLocked ? 'disabled' : '' }}>
                            + {{ __('admin.menu_builder.add_subcategory') }}
                        </button>
                    @endif

                    @if(Permissions::can($user, 'items.create'))
                        <button class="btn small" type="button"
                            {{ $catLocked ? 'disabled' : '' }}>
                            + {{ __('admin.menu_builder.add_item') }}
                        </button>
                    @endif

                    @if(Permissions::can($user, 'categories.delete'))
                        <button class="btn small danger" type="button"
                            {{ $catLocked ? 'disabled' : '' }}>
                            {{ __('admin.actions.delete') }}
                        </button>
                    @endif
                </div>

            </div>

            {{-- ITEMS --}}
            @include('admin.restaurants.components.menu-builder._items-list', [
              'restaurant' => $restaurant,
              'section' => $cat,
              'defaultLocale' => $defaultLocale,
              'ancestorLocked' => $catLocked,
            ])

            {{-- SUBCATEGORIES --}}
            <div class="mb-sub" style="margin-top:12px;">
                @foreach($cat->children as $sub)
                    @php
                        $subInactive = !$sub->is_active;
                        $subDeleted  = $isTrashed($sub);
                        $subLocked = $catLocked;
                        $subItemsLocked = $catLocked || $subInactive;
                    @endphp

                    <div class="{{ ($subInactive || $catLocked) ? 'mb-inactive' : '' }} {{ $subDeleted ? 'mb-deleted' : '' }}"
                         style="padding:12px; margin-top:10px;">

                        <div class="mb-row">

                            <div class="mb-left">

                                {{-- SUBCATEGORY SWITCH --}}
                                @if(Permissions::can($user, 'subcategories.toggle'))
                                    <form method="POST" action="{{ route('admin.restaurants.sections.toggle', [$restaurant, $sub]) }}">
                                        @csrf

                                        <div style="display:flex; align-items:center; justify-content:space-between; width:100%; gap:10px;">
                  <span class="mb-item-title">
                    {{ $tTitle($sub, $defaultLocale) ?: ('Subcategory #'.$sub->id) }}
                  </span>

                                            <label class="mb-switch">
                                                <input type="checkbox"
                                                       @checked($sub->is_active)
                                                       {{ $subLocked ? 'disabled' : '' }}
                                                       onchange="this.form.submit()">
                                                <span class="mb-switch__ui"></span>
                                            </label>
                                        </div>

                                    </form>
                                @else
                                    <div style="display:flex; align-items:center; justify-content:space-between; width:100%; gap:10px;">
                <span class="mb-item-title">
                  {{ $tTitle($sub, $defaultLocale) ?: ('Subcategory #'.$sub->id) }}
                </span>

                                        <label class="mb-switch">
                                            <input type="checkbox" disabled @checked($sub->is_active)>
                                            <span class="mb-switch__ui"></span>
                                        </label>
                                    </div>
                                @endif

                                <span class="mb-mini">ID: {{ $sub->id }}</span>

                                @if($subDeleted)
                                    <span class="pill red">{{ __('admin.common.deleted') }}</span>
                                @elseif($catLocked || $subInactive)
                                    <span class="pill red">{{ __('admin.common.disabled') }}</span>
                                @endif

                            </div>

                            <div class="mb-right mb-actions">
                                @if(Permissions::can($user, 'subcategories.edit'))
                                    <button class="btn small secondary" type="button"
                                        {{ $subLocked ? 'disabled' : '' }}>
                                        {{ __('admin.common.edit') }}
                                    </button>
                                @endif

                                @if(Permissions::can($user, 'items.create'))
                                    <button class="btn small" type="button"
                                        {{ $subLocked ? 'disabled' : '' }}>
                                        + {{ __('admin.menu_builder.add_item') }}
                                    </button>
                                @endif

                                @if(Permissions::can($user, 'subcategories.delete'))
                                    <button class="btn small danger" type="button"
                                        {{ $subLocked ? 'disabled' : '' }}>
                                        {{ __('admin.actions.delete') }}
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

    @empty <div class="mb-muted" style="margin-top:10px;">
        {{ __('admin.menu_builder.empty') }} </div>
    @endforelse

</div>
