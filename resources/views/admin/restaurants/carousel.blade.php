{{-- resources/views/admin/restaurants/carousel.blade.php --}}

@extends('admin.layout')

@section('content')

    @include('admin.restaurants.components.carousel.index', [
        'restaurant' => $restaurant
    ])

@endsection
