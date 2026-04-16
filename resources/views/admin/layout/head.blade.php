{{-- resources/views/admin/layout/head.blade.php --}}
{{-- admin/layout/head --}}


<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>@yield('title', __('admin.common.admin')) — {{ __('admin.brand') }}</title>

<link rel="stylesheet" href="{{ asset('admin/css/admin.css') }}">

@vite(['resources/js/admin.js'])
