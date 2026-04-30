{{-- resources/views/public/templates/united/blocks/header/header.blade.php --}}

<header class="site-header">

    <div class="header-inner">

        <div class="header-logo">
            {{ $vm->merchant->name }}
        </div>

        <div class="header-actions">

            <div class="lang-dropdown">

                <button class="lang-toggle" id="langToggle">
                    {{ strtoupper($vm->locale) }}
                    <span class="lang-arrow">▾</span>
                </button>

                <div class="lang-menu" id="langMenu">

                    @foreach($vm->locales() as $locale)

                        @php
                            $isActive = $locale === $vm->locale;

                            $url = request()->fullUrlWithQuery([
                                'lang' => $locale
                            ]);
                        @endphp

                        <a href="{{ $url }}"
                           class="lang-item {{ $isActive ? 'is-active' : '' }}">
                            {{ strtoupper($locale) }}
                        </a>

                    @endforeach

                </div>

            </div>

            @if(empty($isLegalPage))
                <button id="drawerOpen" class="drawer-btn">
                    <i class="ri-menu-line"></i>
                </button>
            @endif

        </div>

    </div>

</header>
