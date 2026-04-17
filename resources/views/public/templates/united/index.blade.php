{{-- resources/views/public/templates/united/index.blade.php --}}

<!DOCTYPE html>
<html lang="{{ $vm->locale }}">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $vm->ogTitle }}">
    <meta property="og:description" content="{{ $vm->ogDescription }}">
    <meta property="og:image" content="{{ url($vm->ogImage) }}">
    <meta property="og:url" content="{{ $vm->ogUrl }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ url($vm->ogImage) }}">

    <title>{{ $vm->merchant->name }}</title>

    @include('public.templates.united.layout.styles')

</head>

<body
    data-theme-mode="{{ $vm->branding['theme_mode'] ?? 'light' }}"
    data-bg-light="{{ $vm->branding['bg_light'] ?? '' }}"
    data-bg-dark="{{ $vm->branding['bg_dark'] ?? '' }}"
    class="theme-{{ $vm->branding['theme_mode'] === 'dark' ? 'dark' : 'light' }}"
>

@include('public.templates.united.blocks.header.header')

<div class="container">
    @include('public.templates.united.blocks.header.restaurant-info', [
        'showFeaturedItems' => true,
    ])

    @php
        $marketingBanners = collect($vm->promoBanners ?? []);
        $marketingItems = collect($vm->carouselItems ?? []);
    @endphp

    @if($marketingBanners->isNotEmpty() || $marketingItems->isNotEmpty())
        @include('public.templates.united.blocks.header.courusel-header', [
            'items' => $marketingItems,
        ])
    @endif
</div>

<main id="menuContainer">
    <div class="container">
        @include('public.templates.united.blocks.menu.menu-section')
    </div>
</main>

@include('public.templates.united.blocks.footer.footer', [
    'showFeaturedItems' => false,
])

@include('public.templates.united.blocks.modal.item-modal')
@include('public.templates.united.blocks.modal.hours-modal')

@include('public.templates.united.blocks.drawer.mobile-drawer')
<div id="drawerOverlay" class="drawer-overlay"></div>

@include('public.templates.united.layout.scripts')

</body>
</html>

<script>
    window.UI_LANG = {
        badge_new: "{{ __('menu.new') }}",
        badge_dish: "{{ __('menu.dish_of_day') }}",
        badge_bestseller: "{{ __('menu.bestseller') }}"
    };
</script>
