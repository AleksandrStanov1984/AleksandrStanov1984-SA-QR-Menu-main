{{-- resources/views/public/templates/united/blocks/header/header.blade.php --}}

<header class="site-header">

    <div class="header-inner">

        <div class="header-logo">
            {{ $vm->merchant->name }}
        </div>

        <div class="header-actions">

            @if(count($vm->locales()) > 1)
                <div class="lang-dropdown">

                    <button class="lang-toggle" id="langToggle">
                        {{ strtoupper(app()->getLocale()) }}
                        <span class="lang-arrow">▾</span>
                    </button>

                    <div class="lang-menu" id="langMenu">

                        @foreach($vm->locales() as $locale)
                            <a href="{{ request()->fullUrlWithQuery(['lang' => $locale]) }}"
                               class="{{ app()->getLocale() === $locale ? 'active' : '' }}">
                                {{ strtoupper($locale) }}
                            </a>
                        @endforeach

                    </div>

                </div>
            @endif

            <button id="drawerOpen" class="drawer-btn">
                <i class="ri-menu-line"></i>
            </button>

        </div>

    </div>

</header>
