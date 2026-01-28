<header class="std-header">
    <div class="std-header__panel">
        <div class="std-header__brand">
            <div class="std-header__logo" aria-hidden="true">
                {{-- поставь сюда свой logo.png --}}
                <img src="{{ $logoUrl ?? '' }}" alt="">
            </div>

            <div class="std-header__titles">
                <div class="std-header__title">{{ $restaurant->name }}</div>
            </div>
        </div>

        <div class="std-header__controls">
            {{-- TOP: Language switch --}}
            <div class="std-header__controlsTop">
                <div class="std-lang" role="tablist" aria-label="Language">
                    @foreach($enabled as $lng)
                        <a
                            class="std-lang__btn {{ $lng===$locale ? 'is-active' : '' }}"
                            href="{{ route('restaurant.show', $restaurant->slug) }}?lang={{ $lng }}"
                            role="tab"
                            aria-selected="{{ $lng===$locale ? 'true' : 'false' }}"
                        >
                            {{ strtoupper($lng) }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- BOTTOM: theme + burger --}}
            <div class="std-header__controlsBottom">
                <button class="std-iconbtn" type="button" data-action="toggle-theme" aria-label="Toggle theme">
                    <span class="std-icon" data-theme-icon>☀️</span>
                </button>

                <button class="std-iconbtn" type="button" data-action="open-menu" aria-label="Open menu">
                    <span class="std-icon">≡</span>
                </button>
            </div>
        </div>
    </div>
</header>
