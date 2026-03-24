
<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    @include('admin.layout.head')

    {{-- CSS --}}
    @include('admin._styles')
</head>
<body>

@include('admin.layout.topbar')

@include('admin.layout.breadcrumbs')

<div class="wrap layout-with-sidebar">

    {{-- LEFT SIDEBAR --}}
    @auth
        @include('admin.restaurants.components.sidebar.index')
    @endauth

    {{-- MAIN CONTENT --}}
    <div class="main-content">

        @if(session('status'))
            <div class="flash flash-success">
                {{ __(session('status')) }}
            </div>
        @endif

        @if(session('error'))
            <div class="flash flash-error">
                {{ __(session('error')) }}
            </div>
        @endif

        @if($errors->any())
            <div class="flash flash-error">
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

@include('admin.layout._modals')

{{-- QR Loader --}}
<div id="qrLoader" class="qr-loader" aria-hidden="true">
    <div class="qr-loader__backdrop"></div>
    <div class="qr-loader__spinner"></div>
</div>

{{-- JS --}}
@include('admin._scripts')

</body>
</html>
