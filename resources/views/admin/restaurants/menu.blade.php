
@extends('admin.layout')

@section('title', 'Меню')
@section('subtitle', $restaurant?->name)

@section('content')
    @include('admin.restaurants.components.edit._menu', [
        'restaurant' => $restaurant,
        'menuTree' => $menuTree,
        'locales' => $locales,
    ])
@endsection
