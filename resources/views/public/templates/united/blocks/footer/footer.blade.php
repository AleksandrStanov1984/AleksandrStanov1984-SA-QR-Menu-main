@inject('img', 'App\Services\ImageService')

<footer class="section section--full site-footer">

    {{-- ========================================
       FEATURED ITEMS
    ======================================== --}}

    @php
        $footerItems = $vm->footer['featured_items'] ?? [];
        $footerHasCarousel = count($footerItems) > 4;
    @endphp

    @if(!empty($footerItems))

        <div class="footer-gallery {{ $footerHasCarousel ? 'is-carousel' : 'is-static' }}" data-footer-gallery>

            <div class="footer-gallery-wrap">

                <div class="footer-gallery__track">

                    @foreach($footerItems as $it)

                        @continue(empty($it['image_path']))

                        @php
                            $image = $img->url($it['image_path']);
                        @endphp

                        <div
                            class="footer-gallery__item"
                            data-open-modal="item"
                            data-title="{{ $it['title'] ?? '' }}"
                            data-description="{{ $it['description'] ?? '' }}"
                            data-details="{{ $it['details'] ?? '' }}"
                            data-price="@if(!empty($it['price'])){{ number_format((float)$it['price'],2) }} {{ $it['currency'] ?? '€' }}@endif"
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
                            >

                        </div>

                    @endforeach

                </div>

            </div>

        </div>

    @endif

    @include('public.templates.united.blocks.header.restaurant-info')

    {{-- ========================================
       SOCIAL ICONS
    ======================================== --}}

    <div class="footer-social-row">

        <div class="footer-social-row__inner">

            <div class="footer-socials">

                @foreach($vm->footer['links'] as $social)

                    @continue(empty($social['url']))

                    @php

                        if (!empty($social['icon'])) {
                            $icon = $img->url($social['icon']);
                        } else {

                            $fallbackIcon = 'assets/system/icons/' . strtolower($social['title']) . '.svg';
                            $icon = asset($fallbackIcon);
                        }
                    @endphp

                    <a href="{{ $social['url'] }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       aria-label="{{ $social['title'] ?? '' }}">

                        <img
                            src="{{ $icon }}"
                            alt="{{ $social['title'] ?? '' }}"
                            loading="lazy"
                            decoding="async"
                        >

                    </a>

                @endforeach

            </div>

        </div>

    </div>


    {{-- ========================================
       FOOTER BOTTOM
    ======================================== --}}

    <div class="footer-bottom">

        <div class="footer-bottom__inner">

            <div class="footer-bottom__left">

                <span>© {{ date('Y') }}</span>

                <span class="dot">•</span>

                <span>{{ __('footer.crafted_by') }}</span>

                <a href="{{ route('author') }}" class="footer-author-badge">
                    {{ __('footer.author') }}
                </a>

            </div>

        </div>

    </div>

</footer>
