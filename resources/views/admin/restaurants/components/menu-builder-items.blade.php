{{-- resources/views/admin/restaurants/components/menu-builder-items.blade.php --}}
{{-- admin/restaurants/components/menu-builder-items --}}
@include('admin.restaurants.components.menu-builder._items-list', [
  'restaurant' => $restaurant,
  'section' => $section,
  'defaultLocale' => $defaultLocale ?? ($restaurant->default_locale ?: 'de')
])
