{{-- resources/views/admin/restaurants/components/edit/_menu.blade.php --}}
{{-- admin/restaurants/components/edit/_menu --}}

@include('admin.restaurants.components.menu-builder.index', [
  'restaurant' => $restaurant,
  'menuTree' => $menuTree,
  'locales' => $locales
])
