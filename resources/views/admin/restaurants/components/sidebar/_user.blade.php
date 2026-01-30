@php
    use Illuminate\Support\Facades\Storage;

    $user = auth()->user();

    // текущий ресторан (его обычно кладёт ResolveAdminRestaurant)
    $restaurant = $currentRestaurant ?? $restaurant ?? null;

    $logoUrl = (!empty($restaurant?->logo_path))
        ? Storage::url($restaurant->logo_path)
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
                    style="width:100%; height:100%; object-fit:cover; border-radius:50%; display:block;">
            @else
                {{ __('admin.sidebar.logo') }}
            @endif
        </div>
    </div>

    <div class="sb-username">
        {{ $user->name }}
    </div>
</div>
