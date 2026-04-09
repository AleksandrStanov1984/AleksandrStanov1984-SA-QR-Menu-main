@php
    $user = auth()->user();
    $isSuper = (bool)($user?->is_super_admin);

    $ctxRestaurant = ctxRestaurant();
@endphp

<nav class="sb-nav">
    <ul>

        {{-- ================= PROFILE ================= --}}
        <li class="sb-group-title">👤 {{ __('admin.sidebar.profile_group') }}</li>

        @if($ctxRestaurant)
            <li>
                <a href="{{ route('admin.restaurants.profile', $ctxRestaurant) }}"
                   class="{{ request()->routeIs('admin.restaurants.profile') ? 'is-active' : '' }}">
                    👤 {{ __('admin.sidebar.profile') }}
                </a>
            </li>
        @endif


        {{-- ================= RESTAURANT ================= --}}
        <li class="sb-group-title">🏪 {{ __('admin.sidebar.restaurant_group') }}</li>

        @if($ctxRestaurant)
            {{--
            <li>
                <a href="{{ route('admin.restaurants.edit', $ctxRestaurant) }}">
                    ⚙️ {{ __('admin.sidebar.settings') }}
                </a>
            </li>
             --}}

            <li>
                <a href="{{ route('admin.restaurants.hours', $ctxRestaurant) }}">
                    🕒 {{ __('admin.sidebar.hours') }}
                </a>
            </li>
        @endif


        {{-- ================= MENU ================= --}}
        <li class="sb-group-title">📋 {{ __('admin.sidebar.menu_group') }}</li>

        <li>
            <a href="{{ $ctxRestaurant
                ? route('admin.restaurants.menu', $ctxRestaurant)
                : route('admin.home') }}">
                🧱 {{ __('admin.sidebar.menu') }}
            </a>
        </li>


        {{-- ================= CONTENT ================= --}}
        <li class="sb-group-title">🎨 {{ __('admin.sidebar.content_group') }}</li>

        @if($ctxRestaurant)
            <li>
                <a href="{{ route('admin.restaurants.branding', $ctxRestaurant) }}">
                    🖼 {{ __('admin.sidebar.branding') }}
                </a>
            </li>

            <li>
                <a href="{{ route('admin.restaurants.qr', $ctxRestaurant) }}">
                    📱 {{ __('admin.sidebar.qr') }}
                </a>
            </li>

            <li>
                <a href="{{ route('admin.restaurants.socials', $ctxRestaurant) }}">
                    🔗 {{ __('admin.sidebar.socials') }}
                </a>
            </li>
        @endif

        <li class="sb-group-title">
            📢 {{ __('admin.sidebar.banners_group') }}
        </li>

        <li>
            @if($ctxRestaurant && $ctxRestaurant->feature('banners'))
                {{-- PRO: активная ссылка --}}
                <a href="{{ route('admin.restaurants.banners.index', $ctxRestaurant) }}"
                   class="{{ request()->routeIs('admin.restaurants.banners.*') ? 'is-active' : '' }}">
                    🖼 {{ __('admin.sidebar.banners') }}
                </a>
            @else
                {{-- НЕ PRO: заблокировано --}}
                <div style="
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:8px 10px;
            opacity:0.6;
            cursor:not-allowed;
        ">
                    <span>🖼 {{ __('admin.sidebar.banners') }}</span>

                    <span style="
                font-size:11px;
                background:rgba(255,255,255,0.08);
                padding:2px 6px;
                border-radius:6px;
            ">
                🔒 PRO
            </span>
                </div>
            @endif
        </li>

        <li>
            @if($ctxRestaurant && $ctxRestaurant->feature('carousel'))
                <a href="{{ route('admin.restaurants.carousel', $ctxRestaurant) }}"
                   class="{{ request()->routeIs('admin.restaurants.carousel*') ? 'is-active' : '' }}">
                    🎠 {{ __('admin.sidebar.carousel') }}
                </a>
            @else
                <div style="
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:8px 10px;
            opacity:0.6;
            cursor:not-allowed;
        ">
                    <span>🎠 {{ __('admin.sidebar.carousel') }}</span>

                    <span style="
                font-size:11px;
                background:rgba(255,255,255,0.08);
                padding:2px 6px;
                border-radius:6px;
            ">
                🔒 BASIC / PRO
            </span>
                </div>
            @endif
        </li>


        {{-- ================= IMPORT ================= --}}
        {{--
        <li class="sb-group-title">📥 {{ __('admin.sidebar.import_group') }}</li>

        @if($ctxRestaurant)
            <li>
                <a href="{{ route('admin.restaurants.import', $ctxRestaurant) }}">
                    📦 {{ __('admin.sidebar.import_menu') }}
                </a>
            </li>

            <li>
                <a href="{{ route('admin.restaurants.import.images', $ctxRestaurant) }}">
                    🖼 {{ __('admin.sidebar.import_images') }}
                </a>
            </li>
        @endif
       --}}

        {{-- ================= SECURITY ================= --}}
        <li class="sb-group-title">🔒 {{ __('admin.sidebar.security') }}</li>

        @if($ctxRestaurant)
            <li>
                <a href="{{ route('admin.restaurants.credentials', $ctxRestaurant) }}">
                    🔑 {{ __('admin.sidebar.password') }}
                </a>
            </li>

            <li>
                <a href="{{ route('admin.restaurants.permissions', $ctxRestaurant) }}">
                    🛡 {{ __('admin.sidebar.permissions') }}
                </a>
            </li>
        @endif


        {{-- ================= SYSTEM ================= --}}
        @if($isSuper)
            <li class="sb-sep"></li>

            <li class="sb-group-title">⚙️ {{ __('admin.sidebar.system_group') }}</li>

            <li>
                <a href="{{ route('admin.restaurants.index') }}">
                    🏪 {{ __('admin.sidebar.restaurants_select') }}
                </a>
            </li>

            <li class="sb-group-title">🔒 {{ __('admin.sidebar.security_group') }}</li>

            <li>
                <a href="{{ route('admin.security.password') }}">
                    🔑 {{ __('admin.sidebar.password') }}
                </a>
            </li>
        @endif

    </ul>
</nav>
