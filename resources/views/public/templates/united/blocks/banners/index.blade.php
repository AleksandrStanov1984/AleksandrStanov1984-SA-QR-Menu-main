{{-- resources/views/public/templates/united/blocks/banners/index.blade.php --}}

@if(!empty($vm->promoBanners))

    @php($img = app(\App\Services\ImageService::class))

    <div class="menu-banners" data-banner-carousel>
        <div class="banner-carousel">

            <div class="banner-viewport">
                <div class="banner-track">

                    @foreach($vm->promoBanners as $index => $banner)

                        <div class="banner-item" data-index="{{ $index }}">
                            <img
                                src="{{ $img->banner($banner['image'], 800) }}"

                                srcset="
                                    {{ $img->banner($banner['image'], 400) }} 400w,
                                    {{ $img->banner($banner['image'], 800) }} 800w,
                                    {{ $img->banner($banner['image'], 1200) }} 1200w
                                "

                                sizes="(max-width: 768px) 100vw, (max-width: 1024px) 50vw, 33vw"

                                alt="banner {{ $index + 1 }}"
                                width="1200"
                                height="600"

                                fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}"
                                loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                decoding="async"

                                draggable="false" {{-- 🔥 фикс дерганья drag --}}
                            >
                        </div>

                    @endforeach

                </div>
            </div>

            {{-- DOTS --}}
            <div class="banner-dots">
                @foreach($vm->promoBanners as $index => $banner)
                    <span
                        class="banner-dot {{ $index === 0 ? 'active' : '' }}"
                        data-dot="{{ $index }}"
                        role="button"
                        aria-label="Go to banner {{ $index + 1 }}"
                    ></span>
                @endforeach
            </div>

        </div>
    </div>

@endif
