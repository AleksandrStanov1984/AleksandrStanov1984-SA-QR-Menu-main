{{-- resources/views/public/templates/united/blocks/header/courusel-header.blade.php --}}

@php
    $img = app(\App\Services\ImageService::class);
    $sourceConfig = config("carousel_sources.$carouselSource");
@endphp

@if($items->isNotEmpty())
    <section class="header-carousel" data-header-carousel>

        <div class="header-carousel__viewport">
            <div class="header-carousel__track">

                @foreach($items as $index => $it)
                    @php
                        $image = $img->url($it['image']);
                    @endphp

                    <article class="header-carousel__item">

                        <img
                            src="{{ $image }}"
                            alt="{{ $it['title'] ?? '' }}"
                            loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                            fetchpriority="{{ $index === 0 ? 'high' : 'auto' }}"
                            decoding="async"
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

    </section>
@endif
