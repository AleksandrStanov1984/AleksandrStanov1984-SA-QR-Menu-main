@php
    $u = auth()->user();

    if (!$u) {
        $brandUrl = route('admin.login');
    } elseif ($u->is_super_admin) {
        // super admin всегда может зайти на список ресторанов
        $brandUrl = route('admin.restaurants.index');
    } else {
        // обычный пользователь всегда заходит через "умный вход" в меню
        $brandUrl = route('admin.menu.profile');
    }
@endphp

<div class="topbar">
    <div class="topbar__left">
        {{-- burger (виден только на мобилке) --}}
        @auth
            <button type="button"
                    class="sb-burger"
                    data-sidebar-open
                    aria-label="Open menu">
                ☰
            </button>
        @endauth

        <div>
            <div class="brand"><a href="{{ $brandUrl }}">{{ __('admin.brand') }}</a></div>
            <div class="mut">@yield('subtitle')</div>
        </div>
    </div>

    <div class="topbar__right">
        <form method="POST" action="{{ route('admin.locale.set') }}">
            @csrf
            <select name="locale" onchange="this.form.submit()">
                <option value="de" @selected(app()->getLocale()==='de')>DE</option>
                <option value="en" @selected(app()->getLocale()==='en')>EN</option>
                <option value="ru" @selected(app()->getLocale()==='ru')>RU</option>
            </select>
        </form>

        @auth
            <a class="mut" href="{{ route('admin.profile') }}">{{ $u->name }}</a>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn secondary">
                    {{ __('admin.actions.logout') }}
                </button>
            </form>
        @endauth
    </div>
</div>

<style>
    .topbar__right select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;

        display: inline-flex;
        align-items: center;

        height: 36px;
        padding: 0 34px 0 10px;

        border-radius: 10px;
        border: 1px solid var(--line);

        background: var(--card);
        background-color: var(--card);
        color: var(--text-primary);

        font-size: 13px;
        font-weight: 500;

        cursor: pointer;

        /* стрелка */
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 20 20' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M6 8l4 4 4-4' stroke='%23ffffff' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 12px;

        box-shadow: 0 6px 16px rgba(0,0,0,.25);

        transition: all .15s ease;
    }

    .topbar__right select:hover {
        border-color: rgba(255,255,255,0.2);
        background-color: color-mix(in srgb, var(--card) 90%, white 10%);
    }

    .topbar__right select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow:
            0 0 0 2px rgba(37,99,235,.25),
            0 6px 16px rgba(0,0,0,.25);
    }

    .topbar__right select:active {
        transform: scale(0.98);
    }
</style>
