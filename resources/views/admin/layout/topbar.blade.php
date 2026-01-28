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
        <div class="brand"><a href="{{ $brandUrl }}">{{ __('admin.brand') }}</a></div>
        <div class="mut">@yield('subtitle')</div>
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
