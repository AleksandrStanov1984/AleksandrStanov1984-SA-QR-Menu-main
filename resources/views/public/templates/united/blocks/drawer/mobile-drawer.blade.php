{{-- resources/views/public/templates/united/blocks/drawer/mobile-drawer.blade.php --}}

<div id="mobileDrawer" class="mobile-drawer">

    {{-- HEADER --}}
    <div class="drawer-header">

        <button
            id="drawerClose"
            class="drawer-close"
            type="button"
            aria-label="Close menu"
        >
            ✕
        </button>

    </div>

    {{-- NAV --}}
    <nav class="drawer-nav">

        @foreach($vm->categories as $cat)

            <a
                class="drawer-link"
                href="#section-{{ $cat['id'] }}"
                data-drawer-link
            >
                {{ $cat['title'] }}
            </a>

        @endforeach

    </nav>

</div>
