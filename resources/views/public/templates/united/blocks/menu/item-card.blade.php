@php
    $short = $item['description'] ?? '';
    $long  = $item['details'] ?? '';

    $hasImage = !empty($item['has_image']);

    $isNew  = !empty($item['ui']['is_new']);
    $isDish = !empty($item['ui']['dish_of_day']);

    $spicyLevel = (int)($item['ui']['spicy'] ?? 0);

    $priceFormatted = !empty($item['price'])
        ? number_format((float)$item['price'], 2) . ' ' . ($item['currency'] ?? '€')
        : '';

    $dataImage = $item['image'] ?? '';

    $classes = ['menu-item'];

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

    {{-- IMAGE (независимый блок) --}}
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

        {{-- 🔥 BADGES (НЕЗАВИСИМЫЕ ОТ IMAGE) --}}
        @if($isNew || $isDish)
            <div class="menu-item-badges">
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
