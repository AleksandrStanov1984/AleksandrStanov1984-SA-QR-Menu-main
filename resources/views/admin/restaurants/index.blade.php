{{-- resources/views/admin/restaurants/index.blade.php --}}

@extends('admin.layout')

@section('title', __('admin.restaurants.index.title'))
@section('subtitle', __('admin.restaurants.index.subtitle'))

@section('breadcrumbs')
    <a href="{{ route('admin.home') }}">{{ __('admin.dashboard.home') }}</a>
    <span class="sep">›</span>
    <span>{{ __('admin.restaurants.index.h1') }}</span>
@endsection

@section('content')

    {{-- HEADER --}}
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px;">
        <h1 style="margin:0; font-size:18px;">
            {{ __('admin.restaurants.index.h1') }}
        </h1>

        <a class="btn" href="{{ route('admin.restaurants.create') }}">
            {{ __('admin.restaurants.index.add') }}
        </a>
    </div>

    {{-- 🔍 SEARCH PANEL --}}
    <div style="margin-bottom: 12px;">
        <input
            type="text"
            id="restaurantSearch"
            class="input"
            placeholder="Search by name, slug or ID..."
            style="width:100%; max-width:360px;"
        >
    </div>

    <div class="card" id="restaurantsTable">

        <div class="table-scroll">
            <table class="table">

                <thead>
                <tr>
                    <th>{{ __('admin.fields.name') }}</th>
                    <th>{{ __('admin.fields.template') }}</th>
                    <th>{{ __('admin.fields.languages') }}</th>
                    <th>{{ __('admin.fields.status') }}</th>
                    <th class="right">{{ __('admin.fields.actions') }}</th>
                </tr>
                </thead>

                <tbody>
                @foreach($restaurants as $r)
                    <tr
                        data-id="{{ $r->id }}"
                        data-name="{{ strtolower(e($r->name)) }}"
                        data-slug="{{ strtolower(e($r->slug)) }}"
                    >

                        {{-- NAME --}}
                        <td data-label="{{ __('admin.fields.name') }}">
                            <div class="restaurant-name js-name">
                                {{ $r->name }}
                                <div class="restaurant-sub js-slug">
                                    #{{ $r->id }} · {{ $r->slug }}
                                </div>
                            </div>
                        </td>

                        {{-- TEMPLATE --}}
                        <td data-label="{{ __('admin.fields.template') }}">
                            <span class="pill">
                                {{ __('admin.templates.'.$r->template_key) }}
                            </span>
                        </td>

                        {{-- LANGUAGES --}}
                        <td data-label="{{ __('admin.fields.languages') }}" class="mut">
                            {{ implode(', ', $r->enabled_locales ?: ['de']) }}
                            <span class="pill small">
                                {{ $r->default_locale ?: 'de' }}
                            </span>
                        </td>

                        {{-- STATUS --}}
                        <td data-label="{{ __('admin.fields.status') }}">
                            <span class="status">
                                <span class="status-dot {{ $r->is_active ? 'on' : 'off' }}"></span>
                                {{ $r->is_active
                                    ? __('admin.status.active')
                                    : __('admin.status.inactive')
                                }}
                            </span>
                        </td>

                        {{-- ACTIONS --}}
                        <td class="right actions-desktop" data-label="{{ __('admin.fields.actions') }}">
                            <div class="actions-inline">

                                <a class="btn small"
                                   href="{{ route('admin.restaurants.edit', $r) }}">
                                    {{ __('admin.actions.edit') }}
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.restaurants.toggle', $r) }}"
                                      class="toggle-form">
                                    @csrf

                                    <label class="switch">
                                        <input
                                            type="checkbox"
                                            {{ $r->is_active ? 'checked' : '' }}
                                            onchange="this.form.submit()"
                                        >
                                        <span class="slider"></span>
                                    </label>

                                </form>

                            </div>
                        </td>

                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>

        <div style="margin-top: 12px;">
            {{ $restaurants->links() }}
        </div>

    </div>

@endsection
