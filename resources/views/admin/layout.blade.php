{{-- resources/views/admin/layout.blade.php --}}


<!doctype html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

@if(!request()->is('admin/*'))
    {{-- OG meta --}}
@endif

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('admin.layout.head')
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

        {{-- WARNING --}}
        @if(session('warning'))
            <div class="flash flash-warning">
                {{ session('warning') }}
            </div>
        @endif

        {{-- SUCCESS --}}
        @if(session('status'))
            <div class="flash flash-success">
                {{ __(session('status')) }}
            </div>
        @endif


        {{-- ERROR --}}
        @if(session('error'))
            <div class="flash flash-error">
                {{ __(session('error')) }}
            </div>
        @endif

        {{-- VALIDATION --}}
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

{{-- AUTO HIDE FLASH + BLUR --}}
<script>
    setTimeout(() => {
        document.querySelectorAll('.flash').forEach(el => {
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 300);
        });
    }, 5000);

    document.addEventListener('click', (e) => {
        const active = document.activeElement;

        if (!active) return;

        const isInput =
            active.tagName === 'INPUT' ||
            active.tagName === 'TEXTAREA';

        if (!isInput) return;

        if (!e.target.closest('input, textarea, .pw-toggle')) {
            active.blur();
        }
    });
</script>

{{-- UI LANG --}}
<script>
    window.UI_LANG = {
        select_file: "{{ __('ui.toast.select_file') }}",

        saved: "{{ __('ui.toast.saved') }}",
        error: "{{ __('ui.toast.error') }}",

        delete_error: "{{ __('ui.toast.delete_error') }}",
        save_error: "{{ __('ui.toast.save_error') }}",

        delete_banner: "{{ __('ui.confirm.delete_banner') }}",
        delete_all: "{{ __('ui.confirm.delete_all_banners') }}",

        email_same: "{{ __('admin.security.errors.email_same') }}",
        email_ok: "{{ __('admin.security.hints.email_ok') }}",

        password_mismatch: "{{ __('admin.security.errors.password_mismatch') }}",
        password_same: "{{ __('admin.security.errors.password_same') }}",
        password_weak: "{{ __('admin.security.errors.password_weak') }}",
        password_ok: "{{ __('admin.security.hints.password_ok') }}"
    };
</script>

{{-- JS --}}
@include('admin._scripts')

{{-- UI SYSTEM --}}
@include('admin.restaurants.components.ui.toast._view')
@include('admin.restaurants.components.ui.confirm._view')

@include('admin.restaurants.components.ui.toast._scripts')
@include('admin.restaurants.components.ui.confirm._scripts')

<div id="appLoader" class="app-loader hidden">
    <div class="spinner"></div>
</div>

</body>
</html>


