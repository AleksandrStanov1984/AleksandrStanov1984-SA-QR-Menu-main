<!DOCTYPE html>
<html lang="{{ $vm->locale }}">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $vm->merchant->name }}</title>

@include('public.templates.united.layout.styles')

</head>

<body class="theme-light">

@include('public.templates.united.blocks.header.header')

@include('public.templates.united.blocks.drawer.mobile-drawer')

@include('public.templates.united.blocks.header.restaurant-info')

@include('public.templates.united.blocks.categories.category-nav')

<main id="menuContainer">

@include('public.templates.united.blocks.menu.menu-section')

</main>

@include('public.templates.united.blocks.footer.footer')

@include('public.templates.united.blocks.modal.item-modal')
@include('public.templates.united.blocks.modal.hours-modal')

@include('public.templates.united.layout.scripts')

</body>
</html>
