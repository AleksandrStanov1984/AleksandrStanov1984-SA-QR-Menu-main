<header class="site-header">

    <div class="header-inner">

        <div class="header-logo">
            {{ $vm->merchant->name }}
        </div>

        <div class="header-actions">

            <div class="lang-dropdown">

                <button class="lang-toggle" id="langToggle">
                    {{ strtoupper(app()->getLocale()) }}
                    <span class="lang-arrow">▾</span>
                </button>

                <div class="lang-menu" id="langMenu">

                    <a href="?lang=de">DE</a>
                    <a href="?lang=en">EN</a>
                    <a href="?lang=ru">RU</a>

                </div>

            </div>

            <button id="drawerOpen" class="drawer-btn">
                <i class="ri-menu-line"></i>
            </button>

        </div>

    </div>

</header>
