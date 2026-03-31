@include('public.templates.united.blocks.banners._styles')

@if(!empty($vm->promoBanners))

    <div class="menu-banners" data-banner-carousel>

        <div class="banner-carousel">

            <div class="banner-viewport">
                <div class="banner-track">

                    @foreach($vm->promoBanners as $index => $banner)
                        <div class="banner-item" data-index="{{ $index }}">
                            <img src="{{ $banner['image'] }}" alt="banner">
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

@include('public.templates.united.blocks.banners._scripts')
