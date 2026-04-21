{{-- resources/views/admin/restaurants/components/edit/_menu.blade.php --}}

@include('admin.restaurants.components.menu-builder.index', [
  'restaurant' => $restaurant,
  'menuTree' => $menuTree,
  'locales' => $locales
])
