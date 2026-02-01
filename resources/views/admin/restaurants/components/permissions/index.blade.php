@php
    $user = auth()->user();
    $isSuper = (bool)($user?->is_super_admin);

    // user whose permissions we view/edit
    $targetUser = $restaurantUser ?? null;

    // если не super admin — показываем только свой список прав (read-only)
    if (!$isSuper) {
        $targetUser = $user;
    }

    $allPerms = \App\Support\Permissions::registry();

    // grouped: group => [permKey => label]
    $grouped = [];
    foreach ($allPerms as $key => $def) {
        if (!is_string($key) || !is_array($def)) continue;

        $group = $def['group'] ?? 'other';
        $label = $def['label'] ?? null;

        if (!is_string($group) || trim($group) === '') $group = 'other';
        if (!is_string($label) || trim($label) === '') continue;

        $grouped[$group][$key] = $label;
    }
    ksort($grouped);

    // permissions array of target user
    $p = $targetUser?->permissions ?? [];

    // режим экрана
    $mode = $isSuper ? 'edit' : 'view';
@endphp

<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.permissions.h2') }}</h2>

    @if($mode === 'edit')
        @if(!$targetUser)
            <div class="errors">{{ __('admin.permissions.no_user') }}</div>
        @else
            <div class="perm-userline">
                {{ __('admin.permissions.user') }}:
                <strong>{{ $targetUser->name }}</strong> ({{ $targetUser->email }})
            </div>

            <form method="POST" action="{{ route('admin.restaurants.user_permissions', $restaurant) }}" data-perm-form="1">
                @csrf

                @include('admin.restaurants.components.permissions._groups-bar', [
                    'grouped' => $grouped,
                ])

                @include('admin.restaurants.components.permissions._modal', [
                    'mode' => $mode,
                ])

                @include('admin.restaurants.components.permissions._templates', [
                    'grouped' => $grouped,
                    'p' => $p,
                    'mode' => $mode,
                ])

                @include('admin.restaurants.components.permissions._scripts', [
                    'grouped' => $grouped,
                    'mode' => $mode,
                ])
            </form>
        @endif
    @else
        {{-- VIEW ONLY: показываем только те права, которые есть --}}
        @php
            $enabled = [];
            foreach ($grouped as $g => $items) {
                foreach ($items as $permKey => $label) {
                    if (!empty($p[$permKey])) {
                        $enabled[$g][$permKey] = $label;
                    }
                }
            }
        @endphp

        @if(empty($enabled))
            <div class="mut" style="font-size:13px; margin-top:8px;">
                {{ __('admin.profile.permissions.no_permissions') }}
            </div>
        @else
            @include('admin.restaurants.components.permissions._groups-bar', [
                'grouped' => $enabled,
                'viewOnly' => true,
            ])

            @include('admin.restaurants.components.permissions._modal', [
                'mode' => 'view',
            ])

            @include('admin.restaurants.components.permissions._templates', [
                'grouped' => $enabled,
                'p' => $p,
                'mode' => 'view',
            ])

            @include('admin.restaurants.components.permissions._scripts', [
                'grouped' => $enabled,
                'mode' => 'view',
            ])
        @endif
    @endif
</div>
