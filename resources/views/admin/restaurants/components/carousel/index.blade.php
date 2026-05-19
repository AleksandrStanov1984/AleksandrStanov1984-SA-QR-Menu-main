{{-- resources/views/admin/restaurants/components/carousel/index.blade.php --}}

@php

    $tTitle = function ($section, $fallbackLocale = null) {

        if (!$section) {
            return '';
        }

        $translations = $section->translations ?? collect();

        $locale = app()->getLocale();

        return
            $translations
                ->firstWhere('locale', $locale)
                ?->title

            ?? $translations
                ->firstWhere('locale', $fallbackLocale)
                ?->title

            ?? $translations
                ->first()
                ?->title

            ?? ('#' . $section->id);
    };

    $carousel = $restaurant->carouselConfig();

    $enabled = (bool) data_get($carousel, 'enabled', false);

    $source = data_get(
        $carousel,
        'source',
        'bestseller'
    );

    $categoryId = data_get($carousel, 'category_id');

    $subcategoryId = data_get($carousel, 'subcategory_id');

    $hasCarousel = $restaurant->feature('carousel');

    $hasAdvanced = $restaurant->feature('carousel_advanced');

    $defaultLocale = $restaurant->default_locale;

    $categories = \App\Models\Section::query()
        ->where('restaurant_id', $restaurant->id)
        ->whereNull('parent_id')
        ->with('translations')
        ->orderBy('sort_order')
        ->get();

    $subcategories = collect();

    if ($categoryId) {

        $subcategories = \App\Models\Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('parent_id', $categoryId)
            ->with('translations')
            ->orderBy('sort_order')
            ->get();
    }

    $carouselSubcategories = \App\Models\Section::query()
        ->where('restaurant_id', $restaurant->id)
        ->whereNotNull('parent_id')
        ->with('translations')
        ->orderBy('sort_order')
        ->get()
        ->groupBy('parent_id')
        ->map(function ($items) use ($tTitle, $defaultLocale) {

            return $items->map(function ($s) use ($tTitle, $defaultLocale) {

                return [
                    'id' => $s->id,
                    'title' => $tTitle($s, $defaultLocale),
                ];

            })->values();

        })
        ->toArray();

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
                    {{ __('menu.carousel') }}:
                    {{ __('menu.bestseller') }}
                </div>

            @endif

            {{-- ADVANCED --}}
            @if($hasAdvanced)

                {{-- SOURCE --}}
                <div id="carouselSourceBlock"
                     class="carousel-source {{ $enabled ? 'active' : '' }}">

                    <label>{{ __('menu.carousel_source') }}</label>

                    <div class="ui-select"
                         data-name="carousel_source">

                        <button type="button"
                                class="ui-select-btn">

                            {{
                                match($source) {
                                    'is_new' => __('menu.new'),
                                    'dish_of_day' => __('menu.dish_of_day'),
                                    'category' => __('menu.category'),
                                    default => __('menu.bestseller'),
                                }
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

                            <div class="ui-select-option {{ $source === 'category' ? 'active' : '' }}"
                                 data-value="category">

                                {{ __('menu.category') }}

                            </div>

                        </div>

                        <input type="hidden"
                               name="carousel_source"
                               value="{{ $source }}">

                    </div>

                </div>

                {{-- CATEGORY CONFIG --}}
                <div id="carouselCategoryBlock"
                     class="carousel-source {{ $source === 'category' ? 'active' : '' }}">

                    {{-- CATEGORY --}}
                    <label>{{ __('menu.category') }}</label>

                    <div class="ui-select"
                         data-name="carousel_category_id">

                        <button type="button"
                                class="ui-select-btn">

                            @php
                                $selectedCategory = $categories->firstWhere('id', $categoryId);
                            @endphp

                            {{
                                $selectedCategory
                                    ? $tTitle($selectedCategory, $defaultLocale)
                                    : __('menu.select_category')
                            }}

                        </button>

                        <div class="ui-select-menu">

                            @foreach($categories as $category)

                                <div class="ui-select-option {{ $categoryId == $category->id ? 'active' : '' }}"
                                     data-value="{{ $category->id }}">

                                    {{ $tTitle($category, $defaultLocale) }}

                                </div>

                            @endforeach

                        </div>

                        <input type="hidden"
                               name="carousel_category_id"
                               value="{{ $categoryId }}">

                    </div>

                    {{-- SUBCATEGORY --}}
                    <div id="carouselSubcategoryWrapper"
                         style="{{ $subcategories->isNotEmpty() ? '' : 'display:none;' }}">

                        <br><br>

                        <label>{{ __('menu.subcategory') }}</label>

                        <div class="ui-select"
                             data-name="carousel_subcategory_id">

                            <button type="button"
                                    class="ui-select-btn"
                                    id="carouselSubcategoryBtn">

                                @php
                                    $selectedSubcategory = $subcategories->firstWhere('id', $subcategoryId);
                                @endphp

                                {{
                                    $selectedSubcategory
                                        ? $tTitle($selectedSubcategory, $defaultLocale)
                                        : __('menu.all')
                                }}

                            </button>

                            <div class="ui-select-menu"
                                 id="carouselSubcategoryMenu">

                                {{-- ALL --}}
                                <div class="ui-select-option {{ !$subcategoryId ? 'active' : '' }}"
                                     data-value="">

                                    {{ __('menu.all') }}

                                </div>

                                {{-- REAL SUBCATEGORIES --}}
                                @foreach($subcategories as $subcategory)

                                    <div class="ui-select-option {{ $subcategoryId == $subcategory->id ? 'active' : '' }}"
                                         data-value="{{ $subcategory->id }}">

                                        {{ $tTitle($subcategory, $defaultLocale) }}

                                    </div>

                                @endforeach

                            </div>

                            <input type="hidden"
                                   name="carousel_subcategory_id"
                                   value="{{ $subcategoryId }}">

                        </div>

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

<script>

window.carouselSubcategories =
    @json($carouselSubcategories);

window.carouselLangAll =
    @json(__('menu.all'));

</script>

@include('admin.restaurants.components.carousel._scripts')
