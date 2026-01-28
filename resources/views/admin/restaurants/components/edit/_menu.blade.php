@include('admin.restaurants.components.logo', ['restaurant' => $restaurant])

@include('admin.restaurants.components.menu-builder.index', [
  'restaurant' => $restaurant,
  'menuTree' => $menuTree,
  'locales' => $locales
])
