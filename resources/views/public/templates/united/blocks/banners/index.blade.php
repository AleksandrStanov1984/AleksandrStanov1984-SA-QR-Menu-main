{{-- resources/views/public/templates/united/blocks/banners/index.blade.php --}}


@if(!empty($vm->promoBanners))

    <div class="menu-banners" data-banner-carousel>

        <div class="banner-carousel">

            <div class="banner-viewport">
                <div class="banner-track">

                    @foreach($vm->promoBanners as $index => $banner)
                        <div class="banner-item" data-index="{{ $index }}">
                            <img
                                src="{{ $banner['image'] }}"
                                alt="banner"

                                width="1200"
                                height="600"

                                loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}"
                                decoding="async"
                            >
                        </div>
                    @endforeach

                </div>
            </div>

            {{-- DOTS --}}
            <div class="banner-dots">
                @foreach($vm->promoBanners as $index => $banner)
                    <span class="banner-dot {{ $index === 0 ? 'active' : '' }}"
                          data-dot="{{ $index }}"></span>
                @endforeach
            </div>

        </div>

    </div>

@endif
