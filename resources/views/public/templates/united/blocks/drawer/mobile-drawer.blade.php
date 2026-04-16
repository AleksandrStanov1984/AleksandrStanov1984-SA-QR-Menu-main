{{-- resources/views/public/templates/united/blocks/drawer/mobile-drawer.blade.php --}}
{{-- public/templates/united/blocks/drawer/mobile-drawer --}}
<div id="mobileDrawer" class="mobile-drawer">

    <div id="drawerClose" class="drawer-close">
        ✕
    </div>

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
