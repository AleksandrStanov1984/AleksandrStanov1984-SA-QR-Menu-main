@extends('admin.layout')

@section('content')

    @include('admin.security.password', [
        'user' => $user,
        'restaurant' => $restaurant,
        'mode' => 'restaurant'
    ])

@endsection
