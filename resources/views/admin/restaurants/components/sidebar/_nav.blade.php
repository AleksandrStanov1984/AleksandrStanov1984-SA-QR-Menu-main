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
            <a href="{{ route('admin.home') }}">
                🍽 {{ __('admin.sidebar.my_menu') }}
            </a>
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
