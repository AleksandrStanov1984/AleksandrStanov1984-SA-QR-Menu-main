<aside class="admin-sidebar" id="adminSidebar" aria-hidden="false">
    {{-- mobile close button (показываем только на мобилке CSS-ом) --}}
    <button type="button" class="sb-mobile-close" data-sidebar-close aria-label="Close menu">✕</button>

    @include('admin.restaurants.components.sidebar._user')
    @include('admin.restaurants.components.sidebar._nav')
</aside>

{{-- backdrop для mobile drawer --}}
<div class="sb-backdrop" data-sidebar-backdrop></div>
