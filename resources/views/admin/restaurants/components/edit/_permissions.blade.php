@php
    $authUser = auth()->user();
    $isSuper = $authUser?->is_super_admin;

    $targetUser = $isSuper
        ? ($restaurantUser ?? null)
        : $authUser;
@endphp

@if(!$targetUser)
    <div class="errors">
        {{ __('admin.permissions.no_user') ?? 'User not found' }}
    </div>
@else

    @include('admin.restaurants.components.permissions.index', [
        'restaurant' => $restaurant,
        'targetUser' => $targetUser,
        'isSuper' => $isSuper,
    ])

@endif
