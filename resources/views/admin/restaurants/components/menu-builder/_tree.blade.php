{{-- resources/views/admin/restaurants/components/menu-builder/_tree.blade.php --}}

@php
    use App\Support\Permissions;

    $user = auth()->user();
    $isSuper = (bool)($user->is_super_admin ?? false);

    $isTrashed = fn($m) => method_exists($m, 'trashed') ? (bool)$m->trashed() : false;
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
            $catLocked   = $catInactive;
        @endphp

        <div
            id="section-{{ $cat->id }}"
        class="card {{ $catInactive ? 'mb-inactive' : '' }} {{ $catDeleted ? 'mb-deleted' : '' }}"
            data-section-id="{{ $cat->id }}"
            data-section-wrapper="1"
            data-type="category"
            style="padding:12px;"
        >

            <div class="mb-row">

                <div class="mb-left">

                    @if(Permissions::can($user, 'categories.toggle'))
                        <form method="POST" action="{{ route('admin.restaurants.sections.toggle', [$restaurant, $cat]) }}">
                            @csrf

                            <div style="display:flex; justify-content:space-between; width:100%;">
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
                    @endif

                    <span class="mb-mini">ID: {{ $cat->id }}</span>
                </div>

                <div class="mb-right mb-actions">

                    <button class="btn small secondary"
                            type="button"
                            {{ $catLocked ? 'disabled' : '' }}
                            data-mb-edit="section"
                            data-mb-open="mbModalCategory"
                            data-section-id="{{ $cat->id }}"
                            data-search="{{ mb_strtolower($tTitle($cat, $defaultLocale) ?: ('Category '.$cat->id)) }}"
                            data-section-update-url="{{ route('admin.restaurants.sections.update', [$restaurant, $cat]) }}"
                            data-section-titles='@json($cat->translations->pluck("title","locale"))'>
                        {{ __('admin.common.edit') }}
                    </button>

                    <button class="btn small secondary"
                            type="button"
                            {{ $catLocked ? 'disabled' : '' }}
                            data-mb-open="mbModalSubcategory"
                            data-parent-id="{{ $cat->id }}">
                        + {{ __('admin.menu_builder.add_subcategory') }}
                    </button>

                    <button class="btn small"
                            type="button"
                            {{ $catLocked ? 'disabled' : '' }}
                            data-mb-open="mbModalItem"
                            data-section-id="{{ $cat->id }}">
                        + {{ __('admin.menu_builder.add_item') }}
                    </button>

                    <button class="btn small danger"
                            type="button"
                            {{ $catLocked ? 'disabled' : '' }}
                            data-confirm-delete="1"
                            data-delete-url="{{ route('admin.restaurants.sections.destroy', [$restaurant, $cat]) }}"
                            data-delete-text="{{ __('admin.confirm.delete_category') }}"
                            data-delete-hint="{{ $tTitle($cat, $defaultLocale) }}">
                        {{ __('admin.actions.delete') }}
                    </button>

                </div>

            </div>

            @include('admin.restaurants.components.menu-builder._items-list', [
                'restaurant' => $restaurant,
                'section' => $cat,
                'defaultLocale' => $defaultLocale,
                'ancestorLocked' => $catLocked,
            ])

            <div class="mb-sub" style="margin-top:12px;">
                @foreach($cat->children as $sub)

                    @php
                        $subInactive = !$sub->is_active;
                        $subLocked   = $catLocked;
                    @endphp

                    <div
                        id="section-{{ $sub->id }}"
                    class="{{ $subInactive ? 'mb-inactive' : '' }}"
                        data-section-id="{{ $sub->id }}"
                        data-type="subcategory"
                        style="padding:12px; margin-top:10px;"
                    >

                        <div class="mb-row">

                            <div class="mb-left">

                                <form method="POST" action="{{ route('admin.restaurants.sections.toggle', [$restaurant, $sub]) }}">
                                    @csrf

                                    <div style="display:flex; justify-content:space-between; width:100%;">
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

                                <span class="mb-mini">ID: {{ $sub->id }}</span>
                            </div>

                            <div class="mb-right mb-actions">

                                <button class="btn small secondary"
                                        type="button"
                                        {{ $subLocked ? 'disabled' : '' }}
                                        data-mb-edit="section"
                                        data-mb-open="mbModalSubcategory"
                                        data-section-id="{{ $sub->id }}"
                                        data-parent-id="{{ $cat->id }}"
                                        data-search="{{ mb_strtolower($tTitle($sub, $defaultLocale) ?: ('Subcategory '.$sub->id)) }}"
                                        data-section-update-url="{{ route('admin.restaurants.sections.update', [$restaurant, $sub]) }}"
                                        data-section-titles='@json($sub->translations->pluck("title","locale"))'>
                                    {{ __('admin.common.edit') }}
                                </button>

                                <button class="btn small"
                                        type="button"
                                        {{ $subLocked ? 'disabled' : '' }}
                                        data-mb-open="mbModalItem"
                                        data-section-id="{{ $sub->id }}">
                                    + {{ __('admin.menu_builder.add_item') }}
                                </button>

                                <button class="btn small danger"
                                        type="button"
                                        {{ $subLocked ? 'disabled' : '' }}
                                        data-confirm-delete="1"
                                        data-delete-url="{{ route('admin.restaurants.sections.destroy', [$restaurant, $sub]) }}"
                                        data-delete-text="{{ __('admin.confirm.delete_subcategory') }}"
                                        data-delete-hint="{{ $tTitle($sub, $defaultLocale) }}">
                                    {{ __('admin.actions.delete') }}
                                </button>

                            </div>

                        </div>

                        @include('admin.restaurants.components.menu-builder._items-list', [
                            'restaurant' => $restaurant,
                            'section' => $sub,
                            'defaultLocale' => $defaultLocale,
                            'ancestorLocked' => $subLocked,
                        ])

                    </div>

                @endforeach
            </div>

        </div>

    @empty
        <div class="mb-muted">{{ __('admin.menu_builder.empty') }}</div>
    @endforelse
</div>
