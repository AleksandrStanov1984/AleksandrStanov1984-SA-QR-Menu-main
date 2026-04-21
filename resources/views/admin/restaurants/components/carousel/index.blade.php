{{-- resources/views/admin/restaurants/components/carousel/index.blade.php --}}

@php
    $meta = is_array($restaurant->meta ?? null) ? $restaurant->meta : [];

    $enabled = (bool)($meta['carousel_enabled'] ?? false);
    $source  = $meta['carousel_source'] ?? 'bestseller';

    $hasCarousel = $restaurant->feature('carousel');
    $hasAdvanced = $restaurant->feature('carousel_advanced');
@endphp

<div class="card carousel-card">

    <h2>{{ __('admin.carousel_settings') }}</h2>

    {{-- LOCKED --}}
    @if(!$hasCarousel)

        <div class="carousel-locked">
            🔒 {{ __('admin.carousel_locked') }}
        </div>

    @else

        <form method="POST"
              action="{{ route('admin.restaurants.carousel.update', $restaurant) }}">
            @csrf

            {{-- ENABLE --}}
            <div class="carousel-row">

                <span>{{ __('menu.carousel') }}</span>

                <label class="lang-switch">
                    <input type="checkbox"
                           name="carousel_enabled"
                           id="carouselToggle"
                           value="1"
                        @checked($enabled)>
                    <span class="lang-switch__ui"></span>
                </label>

            </div>

            {{-- BASIC --}}
            @if(!$hasAdvanced)
                <div class="carousel-hint">
                    {{ __('menu.carousel') }}: {{ __('menu.bestseller') }}
                </div>
            @endif

            {{-- ADVANCED --}}
            @if($hasAdvanced)
                <div id="carouselSourceBlock"
                     class="carousel-source {{ $enabled ? 'active' : '' }}">

                    <label>{{ __('menu.carousel_source') }}</label>

                    <select name="carousel_source">
                        <option value="bestseller" @selected($source === 'bestseller')>
                            {{ __('menu.bestseller') }}
                        </option>

                        <option value="is_new" @selected($source === 'is_new')>
                            {{ __('menu.new') }}
                        </option>

                        <option value="dish_of_day" @selected($source === 'dish_of_day')>
                            {{ __('menu.dish_of_day') }}
                        </option>
                    </select>

                </div>
            @endif

            <br>

            <div class="carousel-actions">
                <button class="btn ok">
                    {{ __('admin.common.save') }}
                </button>
            </div>

        </form>

    @endif

</div>

@include('admin.restaurants.components.carousel._scripts')
