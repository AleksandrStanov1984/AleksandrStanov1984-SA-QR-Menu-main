@php
    use App\Support\AdminContext;

    $u = auth()->user();
    $restaurant = AdminContext::actingRestaurant();

    // BRAND URL
    if (!$u) {
        $brandUrl = route('admin.login');
    } elseif ($u->is_super_admin) {
        $brandUrl = route('admin.restaurants.index');
    } else {
        $brandUrl = route('admin.menu.profile');
    }

    if ($restaurant) {
        $planLocales = $restaurant->feature('locales', ['de']);
        $enabledLocales = $restaurant->enabled_locales;

        $locales = !empty($enabledLocales)
            ? array_values(array_intersect($planLocales, $enabledLocales))
            : $planLocales;

        if (empty($locales)) {
            $locales = ['de']; // fallback
        }
    } else {
        $locales = ['de','en','ru'];
    }
@endphp

@include('admin.layout._styles_topbar')

<div class="topbar">

    <div class="topbar__left">
        @auth
            <button type="button"
                    class="sb-burger"
                    data-sidebar-open
                    aria-label="Open menu">
                ☰
            </button>
        @endauth

        <div>
            <div class="brand">
                <a href="{{ $brandUrl }}">{{ __('admin.brand') }}</a>
            </div>
            <div class="mut">@yield('subtitle')</div>
        </div>
    </div>

    <div class="topbar__right">

        {{-- LANGUAGE --}}
        <form method="POST" action="{{ route('admin.locale.set') }}" id="topbarLocaleForm">
            @csrf
            <select name="locale" onchange="this.form.submit()">
                @foreach($locales as $locale)
                    <option value="{{ $locale }}"
                        @selected(app()->getLocale() === $locale)>
                        {{ strtoupper($locale) }}
                    </option>
                @endforeach
            </select>
        </form>

        @auth

            {{-- PROFILE --}}
            <a class="mut"
               href="{{ ($u->is_super_admin || !$restaurant)
        ? route('admin.profile')
        : route('admin.profile') . '?restaurant=' . $restaurant->id }}">
                {{ $u->name }}
            </a>

            {{-- LOGOUT --}}
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn secondary">
                    {{ __('admin.actions.logout') }}
                </button>
            </form>

        @endauth

    </div>

</div>
