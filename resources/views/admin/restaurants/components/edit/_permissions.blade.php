@php
    // В этом месте у тебя уже есть:
    // $restaurant
    // $restaurantUser (может быть null)
    // auth()->user()

    $user = auth()->user();
@endphp

{{-- Super Admin: редактирование прав пользователя ресторана --}}
@if($user?->is_super_admin)
    @include('admin.restaurants.components.permissions.index', [
        'restaurant' => $restaurant,
        'restaurantUser' => $restaurantUser,
    ])
@else
    {{-- Обычный пользователь: только просмотр своих прав (без чекбоксов) --}}
    @include('admin.restaurants.components.permissions.index', [
        'restaurant' => $restaurant,
        'restaurantUser' => null, // внутри index сам переключит на auth()->user()
    ])
@endif
