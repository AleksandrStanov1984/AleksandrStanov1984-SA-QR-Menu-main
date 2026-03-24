@extends('admin.layout')

@section('title', __('admin.restaurants.edit.title'))
@section('subtitle', $restaurant->name)

@section('content')

@include('admin.restaurants.components.edit._menu', [
    'restaurant' => $restaurant,
    'menuTree' => $menuTree,
    'locales' => $locales,
])

@include('admin.restaurants.components.social-links.index', [
  'restaurant' => $restaurant,
  'socialLinks' => $socialLinks,
])


@include('admin.restaurants.components.edit._permissions', [
    'restaurant' => $restaurant,
    'restaurantUser' => $restaurantUser ?? null,
])




@endsection
