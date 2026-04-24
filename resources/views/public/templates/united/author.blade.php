{{-- resources/views/public/templates/united/author.blade.php --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Author</title>

    @include('public.templates.united.layout.styles')
</head>

<body
    data-theme-mode="{{ $vm->branding['theme_mode'] ?? 'light' }}"
    data-bg-light="{{ $vm->branding['bg_light'] ?? '' }}"
    data-bg-dark="{{ $vm->branding['bg_dark'] ?? '' }}"
    class="page-author theme-{{ $vm->branding['theme_mode'] === 'dark' ? 'dark' : 'light' }}"
>

@include('public.templates.united.blocks.header.header')

<main class="container">

    @include('public.templates.united.blocks.author.index')

</main>

@include('public.templates.united.blocks.footer.footer', [
    'showFeaturedItems' => false,
])

@include('public.templates.united.layout.scripts')

</body>
</html>
