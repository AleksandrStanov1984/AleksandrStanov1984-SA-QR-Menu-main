{{-- resources/views/admin/layout/topbar.blade.php --}}

@php
    use App\Support\AdminContext;

    $u = auth()->user();
    $restaurant = AdminContext::actingRestaurant();

    if (!$u) {
        $brandUrl = route('admin.login');
    } elseif ($u->is_super_admin) {
        $brandUrl = route('admin.restaurants.index');
    } else {
        $brandUrl = route('admin.menu.profile');
    }

    if ($u?->is_super_admin) {

        $locales = config('locales.all', ['de']);

    } elseif ($restaurant) {

        $enabledLocales = $restaurant->enabled_locales ?? [];

        $locales = collect($enabledLocales)
            ->push($restaurant->default_locale)
            ->unique()
            ->sortByDesc(fn ($l) => $l === $restaurant->default_locale)
            ->values()
            ->all();

    } else {
        $locales = ['de'];
    }
@endphp

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

        <form method="POST" action="{{ route('admin.locale.set') }}" id="topbarLocaleForm">
            @csrf

            <div class="ui-select ui-select--button" data-name="locale">

                <button type="button" class="ui-select-btn">
                    {{ strtoupper(app()->getLocale()) }}
                </button>

                <div class="ui-select-menu">
                    @foreach($locales as $locale)
                        <div class="ui-select-option {{ app()->getLocale() === $locale ? 'active' : '' }}"
                             data-value="{{ $locale }}">
                            {{ strtoupper($locale) }}
                        </div>
                    @endforeach
                </div>

                <input type="hidden" name="locale" value="{{ app()->getLocale() }}">
            </div>
        </form>

        @auth

            {{-- PROFILE --}}
            <a class="mut"
               href="{{ ($u->is_super_admin || !$restaurant)
        ? route('admin.profile')
        : route('admin.profile') . '?restaurant=' . $restaurant->id }}">
                {{ $u->name }}
            </a>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn secondary">
                    {{ __('admin.actions.logout') }}
                </button>
            </form>

        @endauth

    </div>

</div>
