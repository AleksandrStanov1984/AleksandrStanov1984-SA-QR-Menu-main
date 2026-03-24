@php
    $user = auth()->user();
    $isSuper = (bool)($user?->is_super_admin);

    $ctxRestaurant = $restaurant ?? request()->route('restaurant');
@endphp

<nav class="sb-nav">
    <ul>

        {{-- ================= PROFILE ================= --}}
        <li class="sb-group-title">👤 {{ __('admin.sidebar.profile_group') }}</li>


        @if($restaurant)
            <li>
                <a href="{{ route('admin.restaurants.profile', $restaurant) }}"
                   class="{{ request()->routeIs('admin.restaurants.profile') ? 'is-active' : '' }}">
                    👤 {{ __('admin.sidebar.profile') }}
                </a>
            </li>
        @endif


        {{-- ================= RESTAURANT ================= --}}
        <li class="sb-group-title">🏪 {{ __('admin.sidebar.restaurant_group') }}</li>

        <li>
            <a href="{{ route('admin.restaurants.edit', $restaurant ?? 1) }}">
                ⚙️ {{ __('admin.sidebar.settings') }}
            </a>
        </li>

        <li>
            @if(isset($restaurant))
                <a href="{{ route('admin.restaurants.hours', $restaurant) }}">
                    🕒 {{ __('admin.sidebar.hours') }}
            @endif
        </li>


        {{-- ================= МЕНЮ ================= --}}
        <li class="sb-group-title">📋 {{ __('admin.sidebar.menu_group') }}</li>

        <a href="{{ $ctxRestaurant
    ? route('admin.restaurants.menu', $ctxRestaurant)
    : route('admin.home') }}">
            🧱 Меню
        </a>


        {{-- ================= CONTENT ================= --}}
        <li class="sb-group-title">🎨 {{ __('admin.sidebar.content_group') }}</li>

        <li>
            <a href="{{ route('admin.restaurants.branding', $restaurant ?? 1) }}">
                🖼 {{ __('admin.sidebar.branding') }}
            </a>
        </li>

        <li>
            <a href="{{ route('admin.restaurants.qr', $restaurant ?? 1) }}">
                📱 QR-код
            </a>
        </li>

        <li>
            <a href="{{ route('admin.restaurants.socials', $restaurant ?? 1) }}">
                🔗 {{ __('admin.sidebar.socials') }}
            </a>
        </li>


        {{-- ================= IMPORT ================= --}}
        <li class="sb-group-title">📥 {{ __('admin.sidebar.import_group') }}</li>

        <li>
            <a href="{{ route('admin.restaurants.import', $restaurant ?? 1) }}">
                📦 Импорт меню
            </a>
        </li>

        <li>
            <a href="{{ route('admin.restaurants.import.images', $restaurant ?? 1) }}">
                🖼 Импорт изображений
            </a>
        </li>


        {{-- ================= SECURITY ================= --}}
        <li class="sb-group-title">🔒 {{ __('admin.sidebar.security_group') }}</li>

        <li>
            <a href="{{ route('admin.security.password') }}">
                🔑 {{ __('admin.sidebar.password') }}
            </a>
        </li>

        @if($user->can('users.permissions'))
            <li>
                <a href="{{ route('admin.permissions') }}">
                    🛡 Права доступа
                </a>
            </li>
        @endif


        {{-- ================= SYSTEM ================= --}}
        @if($isSuper)
            <li class="sb-sep"></li>

            <li class="sb-group-title">⚙️ Система</li>

            <li>
                <a href="{{ route('admin.restaurants.index') }}">
                    🏪 {{ __('admin.sidebar.restaurants_select') }}
                </a>
            </li>

            {{-- ================= SECURITY ================= --}}
            <li class="sb-group-title">🔒 {{ __('admin.sidebar.security_group') }}</li>

            <li>
                <a href="{{ route('admin.security.password') }}">
                    🔑 {{ __('admin.sidebar.password') }}
                </a>
            </li>


        @endif

    </ul>
</nav>
