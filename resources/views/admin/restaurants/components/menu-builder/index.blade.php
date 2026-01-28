@php
  $locales = $locales ?? ($restaurant->enabled_locales ?: ['de']);

  $tTitle = fn($model, $loc) => optional($model->translations->firstWhere('locale', $loc))->title ?? '';
  $defaultLocale = $restaurant->default_locale ?: 'de';
@endphp

@include('admin.restaurants.components.menu-builder._styles')

<div class="card" style="margin-top:16px;">
  <div class="mb-row">
    <div>
      <h2 style="margin:0;">{{ __('admin.menu_builder.h2') }}</h2>
      <div class="mb-muted">{{ __('admin.menu_builder.hint') }}</div>
    </div>

    <button class="btn ok" type="button" data-mb-open="mbModalCategory">
      + {{ __('admin.menu_builder.add_category') }}
    </button>
  </div>

  {{-- TREE --}}
  @include('admin.restaurants.components.menu-builder._tree', [
    'restaurant' => $restaurant,
    'menuTree' => $menuTree,
    'locales' => $locales,
    'tTitle' => $tTitle,
    'defaultLocale' => $defaultLocale
  ])
</div>

{{-- MODALS --}}
@include('admin.restaurants.components.menu-builder._category-modal', ['restaurant' => $restaurant, 'locales' => $locales])
@include('admin.restaurants.components.menu-builder._subcategory-modal', ['restaurant' => $restaurant, 'locales' => $locales])
@include('admin.restaurants.components.menu-builder._item-modal', ['restaurant' => $restaurant, 'locales' => $locales])

@include('admin.restaurants.components.menu-builder._scripts', ['restaurant' => $restaurant])
