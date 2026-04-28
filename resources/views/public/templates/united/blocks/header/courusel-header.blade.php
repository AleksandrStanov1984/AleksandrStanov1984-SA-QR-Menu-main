{{-- resources/views/public/templates/united/blocks/header/courusel-header.blade.php --}}

@php
    $img = app(\App\Services\ImageService::class);

    $source = $carouselSource;
    $sourceConfig = config("carousel_sources.$carouselSource");

    $banners = $vm->promoBanners ?? [];
    $items = $items ?? collect();
@endphp

{{-- =========================
   MARKETING BLOCK
========================= --}}

@if(!empty($banners))
    <section class="restaurant-marketing">

        {{-- BANNERS --}}
        @if(!empty($banners))
            @include('public.templates.united.blocks.banners.index')
        @endif
@endif
        {{-- CAROUSEL --}}
        @if($items->isNotEmpty())
            <div class="header-carousel" data-header-carousel>

                <div class="header-carousel__viewport">
                    <div class="header-carousel__track">

                        @foreach($items as $index => $it)
                            @php
                                $image = $img->url($it['image']);
                            @endphp

                            <article
                                class="header-carousel__item"
                                data-open-modal="item"
                                data-title="{{ $it['title'] ?? '' }}"
                                data-description="{{ $it['description'] ?? '' }}"
                                data-details="{{ $it['details'] ?? '' }}"
                                data-price="@if(!empty($it['price'])){{ number_format((float)$it['price'], 2) }} {{ $it['currency'] ?? '€' }}@endif"
                                data-image="{{ $image }}"
                                data-is-new="{{ !empty($it['meta']['is_new']) ? 1 : 0 }}"
                                data-is-dish="{{ !empty($it['meta']['dish_of_day']) ? 1 : 0 }}"
                                data-spicy="{{ $it['meta']['spicy'] ?? 0 }}"
                            >
                                <img
                                    src="{{ $image }}"
                                    alt="{{ $it['title'] ?? '' }}"
                                    loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                    fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}"
                                    decoding="async"
                                    draggable="false"
                                >

                                <div class="header-carousel__overlay">
                                    <div class="header-carousel__title">
                                        {{ $it['title'] ?? '' }}
                                    </div>

                                    @if($sourceConfig)
                                        <div class="header-carousel__badge {{ $sourceConfig['badge_class'] ?? '' }}">
                                            {{ __($sourceConfig['label']) }}
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach

                    </div>
                </div>

            </div>
        @endif

    </section>
