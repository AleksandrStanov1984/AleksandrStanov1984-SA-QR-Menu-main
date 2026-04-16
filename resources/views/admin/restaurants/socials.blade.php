{{-- resources/views/admin/restaurants/socials.blade.php --}}
{{-- admin/restaurants/socials --}}
@extends('admin.layout')

@section('content')
    @include('admin.restaurants.components.social-links.index', [
  'restaurant' => $restaurant,
  'linksArr' => $linksArr,
])
@endsection
