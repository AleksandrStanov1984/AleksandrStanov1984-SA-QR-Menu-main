{{-- resources/views/admin/layout/head.blade.php --}}


<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title', __('admin.common.admin')) — {{ __('admin.brand') }}</title>

@vite([
    'resources/css/admin.css',
    'resources/js/admin.js'
])
