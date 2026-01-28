@php
    $user = auth()->user();
    $isSuper = (bool)($user?->is_super_admin);
@endphp

<nav class="sb-nav">
    <ul>
        <li>
            <a href="#">
                🧾 {{ __('admin.sidebar.about') }}
            </a>
        </li>

        <li>
            <a href="{{ route('admin.profile') }}">
                👤 {{ __('admin.sidebar.profile') }}
            </a>
        </li>

        <li>
            <a href="{{ route('admin.menu.profile') }}">
                🍽 {{ __('admin.sidebar.my_menu') }}
            </a>
        </li>
    @if(auth()->user()->can('sections_manage') || auth()->user()->can('items_manage'))
    <li>
        <a href="{{ route('admin.restaurants.menu', $restaurant) }}"
           class="sidebar-link {{ request()->routeIs('admin.restaurants.menu') ? 'is-active' : '' }}">
            {{ __('admin.menu_builder') }}
        </a>
    </li>
    @endif

        <li class="sb-has-sub">
            <a href="#" class="sb-parent" onclick="event.preventDefault(); this.parentElement.classList.toggle('open');">
                🔒 {{ __('admin.sidebar.security') }}
                <span class="sb-caret">▾</span>
            </a>

            <ul class="sb-sub">
                <li>
                    <a href="{{ route('admin.security.password') }}">
                        🔑 {{ __('admin.sidebar.password') }}
                    </a>

                </li>
            </ul>
       </li>




        @if($isSuper)
            <li class="sb-sep"></li>

            <li>
                <a href="{{ route('admin.restaurants.index') }}">
                    🏪 {{ __('admin.sidebar.restaurants_select') }}
                </a>
            </li>
        @endif
    </ul>
</nav>
