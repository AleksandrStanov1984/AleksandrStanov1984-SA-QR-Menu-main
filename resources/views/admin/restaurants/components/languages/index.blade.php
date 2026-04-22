{{-- resources/views/admin/restaurants/components/languages/index.blade.php --}}

@php
    $allLocales = config('locales.all', ['de']);
    $enabledLocales = $restaurant->enabled_locales ?? [];
    $limit = $restaurant->feature('locales_limit', 1);

    $enabledLocales = collect($enabledLocales)
        ->push($restaurant->default_locale)
        ->unique()
        ->values()
        ->all();

    $defaultOptions = !empty($enabledLocales)
        ? $enabledLocales
        : [$restaurant->default_locale ?? 'de'];
@endphp

<div class="card lang-card">

    <div class="lang-card__header">
        <h2>{{ __('profile.languages.title') }}</h2>

        @if($limit === null)
            <div class="lang-card__limit">Unlimited</div>
        @else
            <div class="lang-card__limit">
                {{ count($enabledLocales) }} / {{ $limit }}
            </div>
        @endif
    </div>

    <form method="POST"
          action="{{ route('admin.restaurants.languages.update', $restaurant) }}">
        @csrf

        <div class="lang-block">
            <label class="lang-label">
                {{ __('profile.languages.default') }}
            </label>

            <div class="ui-select ui-select--button lang-select" data-name="default_locale">

                <button type="button" class="ui-select-btn">
                    {{ strtoupper($restaurant->default_locale ?? 'de') }}
                </button>

                <div class="ui-select-menu">
                    @foreach($defaultOptions as $locale)
                        <div class="ui-select-option {{ $restaurant->default_locale === $locale ? 'active' : '' }}"
                             data-value="{{ $locale }}">
                            {{ strtoupper($locale) }}
                        </div>
                    @endforeach
                </div>

                <input type="hidden" name="default_locale" value="{{ $restaurant->default_locale }}">
            </div>
        </div>

        @if($limit !== 1)

            <div class="lang-list">

                @foreach($allLocales as $locale)

                    @php
                        $isEnabled = in_array($locale, $enabledLocales);
                        $isDefault = $restaurant->default_locale === $locale;
                    @endphp

                    <div class="lang-row">

                        <div class="lang-row__left">
                            <div class="lang-name">
                                {{ strtoupper($locale) }}
                            </div>

                            @if($isDefault)
                                <div class="lang-badge">
                                    default
                                </div>
                            @endif
                        </div>

                        <div class="lang-row__right">

                            <label class="lang-switch">

                                <input
                                    type="checkbox"
                                    value="{{ $locale }}"
                                    @checked($isEnabled)
                                    @disabled($isDefault)
                                >

                                <span class="lang-switch__ui"></span>

                            </label>

                        </div>

                    </div>

                @endforeach

            </div>

        @endif

    </form>

</div>

@include('admin.restaurants.components.languages._scripts')
