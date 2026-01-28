@extends('public.layouts.menu')

@section('content')
@php
    use App\Support\MenuAsset;

    /*
    |--------------------------------------------------------------------------
    | Asset resolver
    |--------------------------------------------------------------------------
    | 1) restaurant overrides (storage/restaurants/{id}/...)
    | 2) template assets (via Vite::asset)
    */
    $img = fn(string $rel) => MenuAsset::image($restAssetBase, $tplAssetBase, $rel);

    // Backgrounds & fallback
    $bgDay    = $img('bg_day.png');
    $bgNight  = $img('bg_night.png');
    $fallback = $img('image-fallback.png');
    $logoUrl  = $img('logo/logo.png');

    // ----------------------------
    // Locale resolver (public menu)
    // ----------------------------
    $defaultLocale = $restaurant->default_locale ?: 'de';
    $enabledLocales = $restaurant->enabled_locales ?: ['de'];

    // if controller already provides $menuLocale - prefer it
    $menuLocale = $menuLocale ?? null;

    if (!$menuLocale) {
        // allow session override if enabled
        $cand = session('menu_locale');
        if (is_string($cand)) {
            $cand = strtolower(trim($cand));
            if (in_array($cand, $enabledLocales, true)) {
                $menuLocale = $cand;
            }
        }
    }

    $menuLocale = $menuLocale ?: $defaultLocale;

    // helper: translation by locale with fallback
    $pickTr = function ($collection) use ($menuLocale, $defaultLocale) {
        if (!$collection) return null;
        return $collection->firstWhere('locale', $menuLocale)
            ?? $collection->firstWhere('locale', $defaultLocale)
            ?? $collection->first();
    };

    // helper: safe attribute string (no newlines)
    $attr = function ($v) {
        $v = (string) ($v ?? '');
        $v = str_replace(["\r\n", "\n", "\r"], ' ', $v);
        return e($v, false);
    };
@endphp

@include('public.partials.header', ['logoUrl' => $logoUrl])

{{-- Background layers for day/night --}}
<div class="std-bg" aria-hidden="true">
    <div class="std-bg__layer std-bg__day" style="background-image:url('{{ $bgDay }}')"></div>
    <div class="std-bg__layer std-bg__night" style="background-image:url('{{ $bgNight }}')"></div>
    <div class="std-bg__overlay"></div>
</div>

<nav class="std-cats" data-chips>
    @foreach($sections as $section)
        @php($st = $pickTr($section->translations))
        @php($key = $section->key ?? null)

        {{-- Category icons are relative to ".../classic-menu/images" --}}
        @php($iconRel = $key ? "category-menu/{$key}/{$key}.png" : null)

        <a class="std-cat" href="#sec-{{ $section->id }}" data-chip>
            @if($iconRel)
                <img class="std-cat__ico"
                     src="{{ $img($iconRel) }}"
                     alt=""
                     loading="lazy"
                     onerror="this.style.display='none'">
            @endif
            <span class="std-cat__txt">{{ $st?->title ?? '—' }}</span>
        </a>
    @endforeach
</nav>

<main class="std-wrap">
    @foreach($sections as $section)
        @php($st = $pickTr($section->translations))

        <section class="std-sec" id="sec-{{ $section->id }}">
            <h2 class="std-sec__title">{{ $st?->title ?? '—' }}</h2>

            @foreach($section->items as $item)
                @php($it = $pickTr($item->translations))
                @php($itemImg = $item->image_path ? asset('storage/'.$item->image_path) : $fallback)

                @php($priceStr = !is_null($item->price)
                    ? number_format((float)$item->price, 2, ',', '.') . ' €'
                    : ''
                )

                <article class="std-card"
                         data-action="open-modal"
                         data-modal-img="{{ $attr($itemImg) }}"
                         data-modal-title="{{ $attr($it?->title) }}"
                         data-modal-desc="{{ $attr($it?->description) }}"
                         data-modal-price="{{ $attr($priceStr) }}">

                    <div class="std-card__left">
                        <div class="std-thumb">
                            <img src="{{ $itemImg }}"
                                 alt=""
                                 loading="lazy"
                                 onerror="this.src='{{ $fallback }}'">
                        </div>
                    </div>

                    <div class="std-card__right">
                        <div class="std-card__top">
                            <div class="std-title">{{ $it?->title ?? '—' }}</div>
                            @if(!is_null($item->price))
                                <div class="std-price">{{ number_format((float)$item->price, 2, ',', '.') }} €</div>
                            @endif
                        </div>

                        @if($it?->description)
                            <div class="std-desc">{{ $it->description }}</div>
                        @endif

                        <div class="std-meta">
                            <div class="std-icons"></div>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    @endforeach

    @include('public.partials.footer')
</main>

<div class="std-backdrop" data-drawer-backdrop></div>

<aside class="std-drawer" data-drawer>
    <div class="std-drawer__head">
        <div class="std-drawer__title">{{ __('menu.drawer.title') }}</div>
        <button class="std-drawer__close" type="button" data-action="close-menu">×</button>
    </div>

    <div class="std-drawer__list">
        @foreach($sections as $section)
            @php($st = $pickTr($section->translations))
            <a href="#sec-{{ $section->id }}" data-action="close-menu">{{ $st?->title ?? '—' }}</a>
        @endforeach
    </div>
</aside>

<div class="std-modal" data-modal>
    <div class="std-modal__bg" data-action="close-modal"></div>
    <div class="std-modal__card">
        <button class="std-modal__close" type="button" data-action="close-modal">×</button>

        <img class="std-modal__img" data-modal-el="img" alt="">

        <div class="std-modal__body">
            <div class="std-modal__price" data-modal-el="price"></div>
            <h3 class="std-modal__title" data-modal-el="title"></h3>
            <div class="std-modal__desc" data-modal-el="desc"></div>
        </div>
    </div>
</div>
@endsection
