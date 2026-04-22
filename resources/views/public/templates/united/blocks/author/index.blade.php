{{-- resources/views/public/templates/united/blocks/author/index.blade.php --}}

@php
    $profileSrc = is_array($profileImage)
        ? ($profileImage['src'] ?? null)
        : $profileImage;

    $profileAlt = is_array($profileImage)
        ? ($profileImage['alt'] ?? __('author.hero.photo_alt'))
        : __('author.hero.photo_alt');

    $icons = $icons ?? [];
    $links = $links ?? [];
@endphp

<section class="section section--full author-hero">

    <a href="{{ route('restaurant.show', $vm->restaurant->slug) }}" class="btn-back">
        ← {{ __('author.back') }}
    </a>

    <div class="author-hero__bg"></div>

    <div class="author-hero__inner">

        {{-- TOP BLOCK --}}
        <div class="author-hero__top">

            {{-- TEXT --}}
            <div class="author-hero__meta">

                <div class="author-hero__name">
                    {{ __('author.hero.name') }}
                </div>

                <h1 class="author-hero__title">
                    {!! nl2br(e(__('author.hero.title'))) !!}
                </h1>

                <p class="author-hero__lead">
                    {{ __('author.hero.lead') }}
                </p>

                <div class="author-hero__stack">
                    {{ __('author.hero.stack') }}
                </div>

                {{-- SOCIAL --}}
                <div class="author-hero__socials">

                    @foreach($socials ?? [] as $social)

                        <a class="author-hero__social"
                           href="{{ $social['url'] }}"
                           target="_blank"
                           rel="noopener"
                           title="{{ ucfirst($social['key']) }}">

                            <img src="{{ $social['icon'] }}"
                                 alt="{{ ucfirst($social['key']) }}">

                        </a>

                    @endforeach

                </div>

            </div>

            {{-- IMAGE --}}
            <div class="author-hero__profile">

                <div class="author-hero__ring"></div>

                @if($profileSrc)
                    <img
                        class="author-hero__avatar"
                        src="{{ $profileSrc }}"
                        alt="{{ $profileAlt }}"
                    >
                @endif

            </div>

        </div>

        {{-- DIVIDER --}}
        <div class="author-hero__divider"></div>

        {{-- PANELS --}}
        @include('public.templates.united.blocks.author.panels')

    </div>

</section>
