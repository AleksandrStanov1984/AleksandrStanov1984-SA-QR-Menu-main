@extends('admin.layout')

@section('title', __('admin.profile.title'))
@section('subtitle', __('admin.profile.subtitle'))

@section('breadcrumbs')
    <a href="{{ route('admin.home') }}">{{ __('admin.breadcrumbs.dashboard') }}</a>
    <span class="sep">/</span>

    @if (!empty($restaurant))
        <a href="{{ route('admin.restaurants.edit', ['restaurant' => $restaurant->id]) }}">{{ $restaurant->name }}</a>
        <span class="sep">/</span>
    @endif

    <span>{{ __('admin.profile.subtitle') }}</span>
@endsection

@section('content')

    @include('admin.profile.components.restaurant-card.index', ['restaurant' => $restaurant ?? null])

    @include('admin.profile.components.permissions-card.index', [
        'user' => $user,
        'permissions' => $permissions ?? []
    ])

@endsection
