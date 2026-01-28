@php
    $u = auth()->user();
    $brandUrl = route('admin.home');
    if ($u && $u->is_super_admin) {
        $brandUrl = route('admin.restaurants.index');
    } elseif ($u && $u->restaurant_id) {
        $brandUrl = route('admin.restaurants.edit', $u->restaurant_id);
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
                <button class="btn secondary">{{ __('admin.actions.logout') }}</button>
            </form>
        @endauth
    </div>
</div>
