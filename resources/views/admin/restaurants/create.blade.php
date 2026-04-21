{{-- resources/views/admin/restaurants/create.blade.php --}}

@extends('admin.layout')

@section('title', __('admin.restaurants.create.title'))
@section('subtitle', __('admin.restaurants.create.subtitle'))

@section('breadcrumbs')
    <a href="{{ route('admin.home') }}">{{ __('admin.dashboard.home') }}</a>
    <span class="sep">›</span>
    <a href="{{ route('admin.restaurants.index') }}">{{ __('admin.restaurants.index.h1') }}</a>
    <span class="sep">›</span>
    <span>{{ __('admin.restaurants.create.title') }}</span>
@endsection

@section('content')
    @include('admin.restaurants.components.create._form')
@endsection
