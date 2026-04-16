{{-- resources/views/admin/restaurants/permissions.blade.php --}}
{{-- admin/restaurants/permissions --}}
@extends('admin.layout')

@section('content')

    <div class="card" style="margin-top:16px;">
        <div class="card-header">
            <h2>{{ __('admin.permissions.h2') }}</h2>
        </div>

        <div class="card-body">

            @include('admin.restaurants.components.edit._permissions', [
                'restaurant' => $restaurant,
                'restaurantUser' => $restaurantUser ?? null,
            ])

        </div>
    </div>

@endsection
