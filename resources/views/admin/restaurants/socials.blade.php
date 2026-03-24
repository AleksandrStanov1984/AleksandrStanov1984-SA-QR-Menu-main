@extends('admin.layout')

@section('content')
    @include('admin.restaurants.components.social-links.index', [
  'restaurant' => $restaurant,
  'linksArr' => $linksArr,
])
@endsection
