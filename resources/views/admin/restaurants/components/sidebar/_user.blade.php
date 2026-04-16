{{-- resources/views/admin/restaurants/components/sidebar/_user.blade.php --}}
@php

    $user = auth()->user();

    // текущий ресторан (его обычно кладёт ResolveAdminRestaurant)
    $restaurant = $currentRestaurant ?? $restaurant ?? null;

    $logoUrl = !empty($restaurant?->logo_path)
        ? app(\App\Services\ImageService::class)->url($restaurant->logo_path)
        : null;
@endphp

<div class="sb-user">
    {{-- logo / placeholder --}}
    <div class="sb-logo">
        <div class="sb-logo-circle">
            @if($logoUrl)
                <img
                    src="{{ $logoUrl }}"
                    alt="logo"
                    data-sidebar-logo
                    style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:block;">
            @else
                <img
                    src="{{ app(\App\Services\ImageService::class)->logo($restaurant->logo_path ?? null) }}"
                    alt="logo"
                    data-sidebar-logo
                    style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:block;">
            @endif
        </div>
    </div>

    <div class="sb-username">
        {{ $user->name }}
    </div>
</div>
