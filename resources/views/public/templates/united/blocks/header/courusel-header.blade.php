@inject('img', 'App\Services\ImageService')

@php
    $showFeaturedItems = $showFeaturedItems ?? false;
    $items = collect($vm->featuredItems ?? [])->filter(fn ($it) => !empty($it['image']))->values();
@endphp

@if(!empty($vm->promoBanners))
    @include('public.templates.united.blocks.banners.index')
@endif

@if($showFeaturedItems && $vm->showDishOfDay && $items->isNotEmpty())
    <section class="restaurant-featured">
        <div class="header-carousel" data-header-carousel>

            <div class="header-carousel__viewport">
                <div class="header-carousel__track">
                    @foreach($items as $it)
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
                            data-spicy="{{ $it['meta']['spicy_level'] ?? 0 }}"
                        >
                            <img
                                src="{{ $image }}"
                                alt="{{ $it['title'] ?? '' }}"
                                loading="lazy"
                                decoding="async"
                                draggable="false"
                            >
                            <div class="header-carousel__overlay">
                                <div class="header-carousel__title">
                                    {{ $it['title'] ?? '' }}
                                </div>

                                @if(!empty($it['meta']['dish_of_day']))
                                    <div class="header-carousel__badge">
                                        {{ __('menu.dish_of_day') }}
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>

        </div>
    </section>
@endif
