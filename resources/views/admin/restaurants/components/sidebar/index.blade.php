{{-- resources/views/admin/restaurants/components/sidebar/index.blade.php --}}

<aside class="admin-sidebar" id="adminSidebar" aria-hidden="false">
    <button type="button" class="sb-mobile-close" data-sidebar-close aria-label="Close menu">✕</button>

    @include('admin.restaurants.components.sidebar._user')
    @include('admin.restaurants.components.sidebar._nav')
</aside>

<div class="sb-backdrop" data-sidebar-backdrop></div>
