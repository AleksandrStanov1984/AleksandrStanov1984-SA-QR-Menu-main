{{-- resources/views/admin/layout/head.blade.php --}}

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title', __('admin.common.admin')) — {{ __('admin.brand') }}</title>

@vite([
    'resources/css/admin.css',
    'resources/js/admin.js'
])

<link rel="icon" href="/assets/system/favicon/favicon.ico" sizes="any">
<link rel="icon" type="image/svg+xml" href="/assets/system/favicon/favicon.svg">
<link rel="icon" type="image/png" sizes="96x96" href="/assets/system/favicon/favicon-96x96.png">
<link rel="apple-touch-icon" sizes="180x180" href="/assets/system/favicon/apple-touch-icon.png">
<link rel="manifest" href="/assets/system/favicon/site.webmanifest">
