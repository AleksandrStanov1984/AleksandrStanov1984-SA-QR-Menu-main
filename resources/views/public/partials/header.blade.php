<header id="menu-header" class="std-header">
    <div class="std-header__panel">

        {{-- Brand --}}
        <div class="std-header__brand">
            <div class="std-header__logo" aria-hidden="true">
                @if(!empty($vm->branding['logo']))
                    <img src="{{ asset('storage/'.$vm->branding['logo']) }}"
                         alt="{{ $vm->merchant->name }}">
                @endif
            </div>

            <div class="std-header__titles">
                <div class="std-header__title">
                    {{ $vm->merchant->name }}
                </div>
            </div>
        </div>

        {{-- Controls --}}
        <div class="std-header__controls">

            {{-- Language Switch --}}
            <div class="std-header__controlsTop">
                <div class="std-lang" role="tablist" aria-label="Language">
                    @foreach(['de','en','ru'] as $lng)
                        <a
                            class="std-lang__btn {{ $vm->locale === $lng ? 'is-active' : '' }}"
                            href="?lang={{ $lng }}"
                            role="tab"
                            aria-selected="{{ $vm->locale === $lng ? 'true' : 'false' }}"
                        >
                            {{ strtoupper($lng) }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Theme + Burger --}}
            <div class="std-header__controlsBottom">

                <button
                    class="std-iconbtn"
                    type="button"
                    data-action="toggle-theme"
                >
                    <span class="std-icon" data-theme-icon>☀️</span>
                </button>

                <button
                    class="std-iconbtn"
                    type="button"
                    data-action="toggle-sidebar"
                >
                    <span class="std-icon">≡</span>
                </button>

            </div>

        </div>

    </div>
</header>
