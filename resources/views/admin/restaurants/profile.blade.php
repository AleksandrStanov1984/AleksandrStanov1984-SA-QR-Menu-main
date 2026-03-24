@extends('admin.layout')

@section('title', __('admin.profile.restaurant.h2'))
@section('subtitle', $restaurant->name)

@section('content')

    @include('admin.profile.components.restaurant-card.index', [
        'restaurant' => $restaurant
    ])

@endsection
