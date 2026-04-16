{{-- resources/views/public/author/sections/hero.blade.php --}}
{{-- public/author/sections/hero --}}
@php
    $tPrefix = 'author';
    $profileImage = $profileImage ?? null;
    $icons = $icons ?? [];
    $links = $links ?? [];

    $profileSrc = is_array($profileImage) ? ($profileImage['src'] ?? null) : $profileImage;
    $profileSrcset = is_array($profileImage) ? ($profileImage['srcset'] ?? null) : null;

    $profileAlt = is_array($profileImage)
        ? ($profileImage['alt'] ?? __($tPrefix.'.hero.photo_alt'))
        : __($tPrefix.'.hero.photo_alt');

    $w = is_array($profileImage) ? (int)($profileImage['width'] ?? 520) : 520;
    $h = is_array($profileImage) ? (int)($profileImage['height'] ?? 520) : 520;
@endphp

<section class="section section--full author-hero" aria-labelledby="author-title">
    <div class="author-hero__bg" aria-hidden="true"></div>

    <div class="author-hero__inner">
        <div class="author-hero__top">
            <div class="author-hero__meta">
                <div class="author-hero__name">{{ __($tPrefix.'.hero.name') }}</div>

                <h1 id="author-title" class="author-hero__title">
                    {!! nl2br(e(__($tPrefix.'.hero.title'))) !!}
                </h1>

                <p class="author-hero__lead">
                    {{ __($tPrefix.'.hero.lead') }}
                </p>

                <div class="author-hero__stack">
                    {{ __($tPrefix.'.hero.stack') }}
                </div>

                <div class="author-hero__socials" aria-label="Social links">
                    @if(!empty($links['telegram']) && !empty($icons['telegram']))
                        <a class="author-hero__social"
                           href="{{ $links['telegram'] }}"
                           target="_blank"
                           rel="noopener"
                           aria-label="Telegram">
                            <img src="{{ $icons['telegram'] }}" alt="Telegram" loading="lazy" decoding="async">
                        </a>
                    @endif

                    @if(!empty($links['whatsapp']) && !empty($icons['whatsapp']))
                        <a class="author-hero__social"
                           href="{{ $links['whatsapp'] }}"
                           target="_blank"
                           rel="noopener"
                           aria-label="WhatsApp">
                            <img src="{{ $icons['whatsapp'] }}" alt="WhatsApp" loading="lazy" decoding="async">
                        </a>
                    @endif
                </div>
            </div>

            <div class="author-hero__profile">
                <div class="author-hero__ring" aria-hidden="true"></div>

                @if($profileSrc)
                    <img
                        class="author-hero__avatar"
                        src="{{ $profileSrc }}"
                        @if($profileSrcset) srcset="{{ $profileSrcset }}" @endif
                        alt="{{ $profileAlt }}"
                        width="{{ $w }}"
                        height="{{ $h }}"
                        loading="lazy"
                        decoding="async"
                    >
                @endif
            </div>
        </div>

        <div class="author-hero__divider" aria-hidden="true"></div>

        @include('public.author.sections.panels')
    </div>
</section>
