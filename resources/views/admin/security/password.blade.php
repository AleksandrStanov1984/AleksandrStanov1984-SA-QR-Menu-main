@extends('admin.layout')

@section('title', __('admin.profile.title'))
@section('subtitle', __('admin.profile.subtitle'))

@section('breadcrumbs')
    <a href="{{ route('admin.home') }}">{{ __('admin.breadcrumbs.dashboard') }}</a>
    <span class="sep">/</span>
    <a href="{{ route('admin.profile') }}">{{ __('admin.profile.subtitle') }}</a>
    <span class="sep">/</span>
    <span>{{ __('admin.sidebar.password') }}</span>
@endsection

@section('content')

    @include('admin.profile.components.user-card.index', ['user' => $user])

    @include('admin.profile.components.modals.change-email.index')

    @include('admin.profile.components.modals.change-password.index')

    @include('admin.profile.components.errors-open-modal.index')

@endsection
