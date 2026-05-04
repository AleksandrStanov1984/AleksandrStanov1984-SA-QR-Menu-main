{{-- resources/views/admin/restaurants/import.blade.php --}}

@extends('admin.layout')

@section('content')

    <div class="container">
        @include('admin.restaurants.components.import.index', [
            'restaurant' => $restaurant
        ])
    </div>

@endsection
