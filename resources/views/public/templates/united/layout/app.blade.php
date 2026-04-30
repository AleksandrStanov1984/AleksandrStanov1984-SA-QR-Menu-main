@include('public.templates.united.layout.styles')

@php
    $themeMode = $vm->branding['theme_mode'] ?? 'light';
@endphp

<body
    data-theme-mode="{{ $themeMode }}"
    data-bg-light="{{ $vm->branding['bg_light'] ?? '' }}"
    data-bg-dark="{{ $vm->branding['bg_dark'] ?? '' }}"
    class="theme-{{ $themeMode === 'dark' ? 'dark' : 'light' }}"
>

@include('public.templates.united.blocks.header.header')

<main>
    @yield('content')
</main>

@include('public.templates.united.blocks.footer.footer')

@include('public.templates.united.layout.scripts')

</body>
