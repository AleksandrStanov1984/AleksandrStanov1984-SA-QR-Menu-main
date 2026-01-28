@extends('admin.layout')

@section('title', __('admin.restaurants.edit.title'))
@section('subtitle', $restaurant->name)

@section('breadcrumbs')
    <a href="{{ route('admin.home') }}">{{ __('admin.dashboard.home') }}</a>
    <span class="sep">›</span>
    <a href="{{ route('admin.restaurants.index') }}">{{ __('admin.restaurants.index.h1') }}</a>
    <span class="sep">›</span>
    <span>{{ $restaurant->name }}</span>
@endsection

@section('content')

@include('admin.restaurants.components.logo', ['restaurant' => $restaurant])

@include('admin.restaurants.components.branding-backgrounds.index', ['restaurant' => $restaurant])

@include('admin.restaurants.components.edit._menu', [
    'restaurant' => $restaurant,
    'menuTree' => $menuTree,
    'locales' => $locales,
])

@include('admin.restaurants.components.edit._permissions', [
    'restaurant' => $restaurant,
    'restaurantUser' => $restaurantUser ?? null,
])

@include('admin.restaurants.components.edit._languages', ['restaurant' => $restaurant])

@endsection
