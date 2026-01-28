<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    @include('admin.layout.head')
</head>
<body>

@include('admin.layout.topbar')
@include('admin.layout.breadcrumbs')

<div class="wrap layout-with-sidebar">

    {{-- LEFT SIDEBAR --}}
    @auth
        <aside class="admin-sidebar">
            @include('admin.restaurants.components.sidebar.index')
        </aside>
    @endauth

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        @if(session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="errors">
                <strong>{{ __('admin.common.fix_these') }}</strong>
                <ul>
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>

</div>

@include('admin.layout.footer')

</body>
</html>
