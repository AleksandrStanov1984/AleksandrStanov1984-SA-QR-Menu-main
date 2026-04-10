@php
    $short = $item['description'] ?? '';
    $long  = $item['details'] ?? '';

    $hasImage = !empty($item['has_image']);

    $isBestseller = !empty($item['ui']['bestseller']);
    $isNew  = !empty($item['ui']['is_new']);
    $isDish = !empty($item['ui']['dish_of_day']);
    $spicyLevel = (int)($item['ui']['spicy'] ?? 0);

    $priceFormatted = !empty($item['price'])
        ? number_format((float)$item['price'], 2) . ' ' . ($item['currency'] ?? '€')
        : '';

    $dataImage = $item['image'] ?? '';

    $classes = ['menu-item'];

    if ($isBestseller) $classes[] = 'is-bestseller';
    if ($isNew) $classes[] = 'is-new';
    if ($isDish) $classes[] = 'is-dish';
@endphp

<div
    class="{{ implode(' ', $classes) }}"

    @if($vm->showItemModal)
        data-open-modal="item"
    data-title="{{ e($item['title'] ?? '') }}"
    data-description="{{ e($short) }}"
    data-details="{{ e($vm->showLongDescription ? $long : '') }}"
    data-price="{{ $priceFormatted }}"
    data-image="{{ $dataImage ?: '' }}"
    data-is-new="{{ $isNew ? 1 : 0 }}"
    data-is-dish="{{ $isDish ? 1 : 0 }}"
    data-spicy="{{ $spicyLevel }}"
    @endif
>

    @if($item['show_image_block'])
        <div class="menu-item-media">
            <img
                src="{{ $item['image'] }}"
                alt="{{ $item['title'] ?? '' }}"
                class="menu-item-image {{ !$hasImage ? 'is-fallback' : '' }}"
                loading="lazy"
                decoding="async"
            >
        </div>
    @endif

    <div class="menu-item-content">

        @if($isBestseller || $isNew || $isDish)
            <div class="menu-item-badges">
                @if($isBestseller)
                    <span class="menu-item-badge menu-item-badge--bestseller"
                          title="{{ __('menu.bestseller') }}">

        <svg class="badge-icon" viewBox="0 0 24 24" aria-hidden="true">
    <!-- внешняя форма -->
    <path
        d="M12 2C9.5 6 6 8 6 12a6 6 0 0 0 12 0c0-3-2-5.5-6-10Z"
        fill="currentColor"
    />
            <!-- внутренняя «жилка» -->
    <path
        d="M12 9c-1.5 2-2.5 3-2.5 4.5a2.5 2.5 0 0 0 5 0C14.5 12 13.5 11 12 9Z"
        fill="#fff"
        opacity="0.35"
    />
</svg>

    </span>
                @endif

                @if($isNew)
                    <span class="menu-item-badge menu-item-badge--new">
        {{ __('menu.badge_new') }}
    </span>
                @endif

                @if($isDish)
                    <span class="menu-item-badge menu-item-badge--dish"
                          title="{{ __('menu.dish_of_day') }}">

        <svg class="badge-icon" viewBox="0 0 24 24" aria-hidden="true">
    <path
        d="M5 14.25C5 14.25 6.1 9.5 12 9.5C17.9 9.5 19 14.25 19 14.25"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
        stroke-linejoin="round"
    />
    <path
        d="M4 14.25H20"
        fill="none"
        stroke="currentColor"
        stroke-width="1.8"
        stroke-linecap="round"
    />
    <circle cx="12" cy="7.1" r="1.35" fill="currentColor"/>
</svg>

    </span>
                @endif
            </div>
        @endif

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

        <div class="menu-item-meta">
            @if($spicyLevel > 0)
                <div class="menu-item-spicy" aria-label="{{ __('menu.spicy') }}: {{ $spicyLevel }}/5">
                    @for($i = 1; $i <= $spicyLevel; $i++)
                        <i class="is-on">🌶</i>
                    @endfor
                </div>
            @endif
        </div>

        @if(!empty($item['price']))
            <div class="menu-item-price">
                {{ number_format((float)$item['price'], 2) }}
                {{ $item['currency'] ?? '€' }}
            </div>
        @endif

    </div>

</div>
