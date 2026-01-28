<!doctype html>
<html lang="{{ $locale }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $restaurant->name }}</title>

    {{-- BASE (reset + globals) --}}
    @vite(['resources/css/public/base.css'])

    {{-- UI KIT (shared components: tokens/theme/layout/header/footer/drawer/modal) --}}
    @vite([
        'resources/css/public/ui/tokens.css',
        'resources/css/public/ui/theme.css',
        'resources/css/public/ui/layout.css',
        'resources/css/public/ui/header.css',
        'resources/css/public/ui/footer.css',
        'resources/css/public/ui/drawer.css',
        'resources/css/public/ui/modal.css',
    ])

    {{-- TEMPLATE (classic/bar/fastfood/...) --}}
    @vite(["resources/css/templates/{$template}.css"])

    {{-- RESTAURANT OVERRIDES (storage) --}}
    @if($theme_css_url)
        <link rel="stylesheet" href="{{ $theme_css_url }}">
    @endif
    @if($custom_css_url)
        <link rel="stylesheet" href="{{ $custom_css_url }}">
    @endif
</head>
<body data-template="{{ $template }}" class="sa-page">
  <div class="sa-page__main">
    @yield('content')
  </div>

  {{-- BASE + TEMPLATE JS --}}
  @vite(['resources/js/public/base.js'])
  @vite(["resources/js/templates/{$template}.js"])

  {{-- RESTAURANT OVERRIDE JS --}}
  @if($custom_js_url)
    <script src="{{ $custom_js_url }}" defer></script>
  @endif
</body>
</html>
