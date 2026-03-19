@php
    $short = $item['description'] ?? '';
    $long  = $item['details'] ?? '';

    // 🔥 теперь берем из ViewModel
    $hasImage = !empty($item['has_image']);

    $isNew  = !empty($item['display']['is_new']);
    $isDish = !empty($item['display']['dish_of_day']);

    $spicyLevel = (int)($item['meta']['spicy'] ?? 0);

    $classes = ['menu-item'];

    if ($isNew)  $classes[] = 'is-new';
    if ($isDish) $classes[] = 'is-dish';
    if (!$hasImage) $classes[] = 'is-no-image';
@endphp


<div
    class="{{ implode(' ', $classes) }}"

    {{-- 🔥 модалка только если есть изображение --}}
    @if($hasImage)
        data-open-modal="item"
    data-title="{{ $item['title'] ?? '' }}"
    data-description="{{ $short }}"
    data-details="{{ $long }}"
    data-price="@if(!empty($item['price'])){{ number_format((float)$item['price'],2) }} {{ $item['currency'] ?? '€' }}@endif"
    data-image="{{ $item['image'] }}"
    data-is-new="{{ $isNew ? 1 : 0 }}"
    data-is-dish="{{ $isDish ? 1 : 0 }}"
    data-spicy="{{ $spicyLevel }}"
    @endif
>

    {{-- BADGES --}}
    @if($isNew || $isDish)
        <div class="menu-item-badges" aria-hidden="true">

            @if($isNew)
                <span class="menu-item-badge menu-item-badge--new">NEW</span>
            @endif

            @if($isDish)
                <span class="menu-item-badge menu-item-badge--dish">
                    {{ __('menu.dish_of_day') }}
                </span>
            @endif

        </div>
    @endif


    {{-- IMAGE --}}
    @if($hasImage)
        <div class="menu-item-media">
            <img
                class="menu-item-image"
                src="{{ $item['image'] }}"
                alt="{{ $item['title'] ?? '' }}"
                loading="lazy"
                decoding="async"
            >
        </div>
    @endif


    <div class="menu-item-content">

        <div class="menu-item-top">
            <h3 class="menu-item-title">
                {{ $item['title'] ?? '' }}
            </h3>
        </div>

        @if(!empty($short))
            <p class="menu-item-description">
                {{ $short }}
            </p>
        @endif


        {{-- SPICY --}}
        <div class="menu-item-meta">
            @if($spicyLevel > 0)
                <div class="menu-item-spicy" aria-label="{{ __('menu.spicy') }}: {{ $spicyLevel }}/5">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="{{ $i <= $spicyLevel ? 'is-on' : '' }}">🌶</i>
                    @endfor
                </div>
            @endif
        </div>


        @if(!empty($item['price']))
            <div class="menu-item-price">
                {{ number_format((float)$item['price'],2) }}
                {{ $item['currency'] ?? '€' }}
            </div>
        @endif

    </div>

</div>
