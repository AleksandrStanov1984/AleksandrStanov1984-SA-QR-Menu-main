{{-- resources/views/admin/restaurants/edit.blade.php --}}
{{-- admin/restaurants/edit --}}
@extends('admin.layout')

@section('title', $restaurant->name)
@section('subtitle', $restaurant->name)

@section('content')

@include('admin.restaurants.components.edit._menu', [
    'restaurant' => $restaurant,
    'menuTree' => $menuTree,
    'locales' => $locales,
])


@include('admin.restaurants.components.edit._permissions', [
    'restaurant' => $restaurant,
    'restaurantUser' => $restaurantUser ?? null,
])

@endsection
