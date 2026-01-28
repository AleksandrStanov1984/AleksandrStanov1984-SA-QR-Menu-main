@include('admin.restaurants.components.menu-builder._items-list', [
  'restaurant' => $restaurant,
  'section' => $section,
  'defaultLocale' => $defaultLocale ?? ($restaurant->default_locale ?: 'de')
])
