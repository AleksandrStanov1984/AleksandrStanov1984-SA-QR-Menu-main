
@include('admin.restaurants.components.menu-builder.index', [
  'restaurant' => $restaurant,
  'menuTree' => $menuTree,
  'locales' => $locales
])
