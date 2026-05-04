{{-- resources/views/admin/restaurants/components/sidebar/_nav.blade.php --}}

@php
    use App\Support\Permissions;

    $user = auth()->user();
    $isSuper = (bool)($user?->is_super_admin);

    $ctxRestaurant = ctxRestaurant();
@endphp

<nav class="sb-nav sb-scroll">
    <ul>

        {{-- ================= PROFILE ================= --}}
        <li class="sb-group-title">👤 {{ __('admin.sidebar.profile_group') }}</li>

        <div class="navbar-divider"></div>
        @if($ctxRestaurant)
            <li>
                <a href="{{ route('admin.restaurants.profile', $ctxRestaurant) }}"
                   class="{{ request()->routeIs('admin.restaurants.profile') ? 'is-active' : '' }}">
                    👤 {{ __('admin.sidebar.profile') }}
                </a>
            </li>

            <li>
                <a href="{{ route('admin.restaurants.languages', $ctxRestaurant) }}"
                   class="{{ request()->routeIs('admin.restaurants.languages*') ? 'is-active' : '' }}">
                    🌐 {{ __('admin.sidebar.languages') }}
                </a>
            </li>
        @endif


        {{-- ================= RESTAURANT ================= --}}
        <li class="sb-group-title">🏪 {{ __('admin.sidebar.restaurant_group') }}</li>

        <div class="navbar-divider"></div>

        @if($ctxRestaurant)
            <li>
                <a href="{{ route('admin.restaurants.hours', $ctxRestaurant) }}"
                   class="{{ request()->routeIs('admin.restaurants.hours*') ? 'is-active' : '' }}">
                    🕒 {{ __('admin.sidebar.hours') }}
                </a>
            </li>
        @endif


        {{-- ================= MENU ================= --}}
        <li class="sb-group-title">📋 {{ __('admin.sidebar.menu_group') }}</li>

        <div class="navbar-divider"></div>

        <li>
            <a href="{{ $ctxRestaurant
                ? route('admin.restaurants.menu', $ctxRestaurant)
                : route('admin.home') }}"
               class="{{ request()->routeIs('admin.restaurants.menu*') ? 'is-active' : '' }}">
                🧱 {{ __('admin.sidebar.menu') }}
            </a>
        </li>

        {{-- IMPORT --}}
        @if($ctxRestaurant && Permissions::can(auth()->user(), 'import.menu_json'))
            <li>
                <a href="{{ route('admin.restaurants.import', $ctxRestaurant) }}"
                   class="{{ request()->routeIs('admin.restaurants.import*') ? 'is-active' : '' }}">
                    📥 {{ __('admin.sidebar.import') }}
                </a>
            </li>
        @endif


        {{-- ================= CONTENT ================= --}}
        <li class="sb-group-title">🎨 {{ __('admin.sidebar.content_group') }}</li>

        <div class="navbar-divider"></div>

        @if($ctxRestaurant)
            <li>
                <a href="{{ route('admin.restaurants.branding', $ctxRestaurant) }}"
                   class="{{ request()->routeIs('admin.restaurants.branding*') ? 'is-active' : '' }}">
                    🖼 {{ __('admin.sidebar.branding') }}
                </a>
            </li>

            <li>
                <a href="{{ route('admin.restaurants.qr', $ctxRestaurant) }}"
                   class="{{ request()->routeIs('admin.restaurants.qr*') ? 'is-active' : '' }}">
                    📱 {{ __('admin.sidebar.qr') }}
                </a>
            </li>

            <li>
                <a href="{{ route('admin.restaurants.socials', $ctxRestaurant) }}"
                   class="{{ request()->routeIs('admin.restaurants.socials*') ? 'is-active' : '' }}">
                    🔗 {{ __('admin.sidebar.socials') }}
                </a>
            </li>
        @endif


        {{-- ================= MARKETING ================= --}}
        <li class="sb-group-title">📢 {{ __('admin.sidebar.banners_group') }}</li>

        <div class="navbar-divider"></div>

        <li>
            @if($ctxRestaurant && $ctxRestaurant->feature('banners'))
                <a href="{{ route('admin.restaurants.banners.index', $ctxRestaurant) }}"
                   class="{{ request()->routeIs('admin.restaurants.banners.*') ? 'is-active' : '' }}">
                    🖼 {{ __('admin.sidebar.banners') }}
                </a>
            @else
                <div class="sb-locked">
                    <span>🖼 {{ __('admin.sidebar.banners') }}</span>
                    <span class="sb-lock-badge">🔒 {{ __('admin.sidebar.pro') }}</span>
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
                <div class="sb-locked">
                    <span>🎠 {{ __('admin.sidebar.carousel') }}</span>
                    <span class="sb-lock-badge">🔒 {{ __('admin.sidebar.basic_pro') }}</span>
                </div>
            @endif
        </li>


        {{-- ================= SECURITY ================= --}}
        <li class="sb-group-title">🔒 {{ __('admin.sidebar.security') }}</li>

        <div class="navbar-divider"></div>

        @if($ctxRestaurant)

            @php
                $securityOpen = request()->routeIs('admin.restaurants.credentials*');
            @endphp

            <li class="sb-group {{ $securityOpen ? 'is-open is-active' : '' }}">

                <ul class="sb-submenu">

                    <li>
                        <a href="{{ route('admin.restaurants.credentials.login', $ctxRestaurant) }}"
                           class="{{ request()->routeIs('admin.restaurants.credentials.login') ? 'is-active' : '' }}">
                            📧 {{ __('admin.sidebar.login') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.restaurants.credentials', $ctxRestaurant) }}"
                           class="{{ request()->routeIs('admin.restaurants.credentials')
                                   && !request()->routeIs('admin.restaurants.credentials.login')
                                   ? 'is-active' : '' }}">
                            🔑 {{ __('admin.sidebar.password') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.restaurants.permissions', $ctxRestaurant) }}"
                           class="{{ request()->routeIs('admin.restaurants.permissions*') ? 'is-active' : '' }}">
                            🛡 {{ __('admin.sidebar.permissions') }}
                        </a>
                    </li>

                </ul>
            </li>

        @endif


        {{-- ================= SYSTEM ================= --}}
        @if($isSuper)
            <div class="sidebar-divider"></div>

            <li class="sb-group-title">⚙️ {{ __('admin.sidebar.system_group') }}</li>

            <div class="sidebar-divider"></div>


            <li>
                <a href="{{ route('admin.restaurants.index') }}"
                   class="{{ request()->routeIs('admin.restaurants.index') ? 'is-active' : '' }}">
                    🏪 {{ __('admin.sidebar.restaurants_select') }}
                </a>
            </li>

            <li class="sb-group-title">🔒 {{ __('admin.sidebar.security_group') }}</li>

            <div class="navbar-divider"></div>

            @php
                $adminSecurityOpen = request()->routeIs('admin.security.*');
            @endphp

            <li class="sb-group {{ $adminSecurityOpen ? 'is-open is-active' : '' }}">

                <ul class="sb-submenu">
                    <li>
                        <a href="{{ route('admin.security.login') }}"
                           class="{{ request()->routeIs('admin.security.login') ? 'is-active' : '' }}">
                            📧 {{ __('admin.sidebar.login') }}
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.security.password') }}"
                           class="{{ request()->routeIs('admin.security.password') ? 'is-active' : '' }}">
                            🔑 {{ __('admin.sidebar.password') }}
                        </a>
                    </li>
                </ul>

            </li>
        @endif

    </ul>
</nav>
