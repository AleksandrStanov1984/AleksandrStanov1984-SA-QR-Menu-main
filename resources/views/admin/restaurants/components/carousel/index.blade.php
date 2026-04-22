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

                    <div class="ui-select" data-name="carousel_source">

                        <button type="button" class="ui-select-btn">
                            {{
                                $source === 'bestseller' ? __('menu.bestseller') :
                                ($source === 'is_new' ? __('menu.new') : __('menu.dish_of_day'))
                            }}
                        </button>

                        <div class="ui-select-menu">

                            <div class="ui-select-option {{ $source === 'bestseller' ? 'active' : '' }}"
                                 data-value="bestseller">
                                {{ __('menu.bestseller') }}
                            </div>

                            <div class="ui-select-option {{ $source === 'is_new' ? 'active' : '' }}"
                                 data-value="is_new">
                                {{ __('menu.new') }}
                            </div>

                            <div class="ui-select-option {{ $source === 'dish_of_day' ? 'active' : '' }}"
                                 data-value="dish_of_day">
                                {{ __('menu.dish_of_day') }}
                            </div>

                        </div>

                        <input type="hidden" name="carousel_source" value="{{ $source }}">

                    </div>

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
