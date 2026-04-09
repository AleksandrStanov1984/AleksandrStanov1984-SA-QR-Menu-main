@inject('img', 'App\Services\ImageService')

@php
    $banners = $vm->promoBanners ?? [];
    $items = $items ?? collect();
@endphp

{{-- =========================
   MARKETING BLOCK
========================= --}}

@if(!empty($banners) || $items->isNotEmpty())
    <section class="restaurant-marketing">

        {{-- BANNERS --}}
        @if(!empty($banners))
            @include('public.templates.united.blocks.banners.index')
        @endif

        {{-- CAROUSEL --}}
        @if($items->isNotEmpty())
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
                                data-spicy="{{ $it['meta']['spicy'] ?? 0 }}"
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

                                    {{-- BADGE PRIORITY --}}
                                    @if(!empty($it['meta']['bestseller']))
                                        <div class="header-carousel__badge">
                                            {{ __('menu.bestseller') }}
                                        </div>
                                    @elseif(!empty($it['meta']['dish_of_day']))
                                        <div class="header-carousel__badge">
                                            {{ __('menu.dish_of_day') }}
                                        </div>
                                    @elseif(!empty($it['meta']['is_new']))
                                        <div class="header-carousel__badge">
                                            {{ __('menu.new') }}
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
@endif
