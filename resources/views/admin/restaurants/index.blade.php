@extends('admin.layout')

@section('title', __('admin.restaurants.index.title'))
@section('subtitle', __('admin.restaurants.index.subtitle'))

@section('breadcrumbs')
    <a href="{{ route('admin.home') }}">{{ __('admin.dashboard.home') }}</a>
    <span class="sep">›</span>
    <span>{{ __('admin.restaurants.index.h1') }}</span>
@endsection


@section('content')
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 12px;">
        <h1 style="margin:0; font-size:18px;">
            {{ __('admin.restaurants.index.h1') }}
        </h1>

        <a class="btn" href="{{ route('admin.restaurants.create') }}">
            {{ __('admin.restaurants.index.add') }}
        </a>
    </div>

    <div class="card">
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>{{ __('admin.fields.name') }}</th>
                <th>{{ __('admin.fields.slug') }}</th>
                <th>{{ __('admin.fields.template') }}</th>
                <th>{{ __('admin.fields.languages') }}</th>
                <th>{{ __('admin.fields.status') }}</th>
                <th class="right">{{ __('admin.fields.actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($restaurants as $r)
                <tr>
                    <td>{{ $r->id }}</td>

                    <td>{{ $r->name }}</td>

                    <td class="mut">{{ $r->slug }}</td>

                    <td class="mut">
                        {{ __('admin.templates.'.$r->template_key) }}
                    </td>

                    <td class="mut">
                        {{ implode(', ', $r->enabled_locales ?: ['de']) }}
                        <span class="pill" style="margin-left:6px;">
                            {{ __('admin.languages.default') }}:
                            {{ $r->default_locale ?: 'de' }}
                        </span>
                    </td>

                    <td>
                        <span class="pill {{ $r->is_active ? 'green' : 'red' }}">
                            {{ $r->is_active ? __('admin.status.active') : __('admin.status.inactive') }}
                        </span>
                    </td>

                    <td class="right">
                        <a class="btn small" href="{{ route('admin.restaurants.edit', $r) }}">
                            {{ __('admin.actions.edit') }}
                        </a>

                        <form method="POST"
                              action="{{ route('admin.restaurants.toggle', $r) }}"
                              style="display:inline;">
                            @csrf
                            <button
                                class="btn small {{ $r->is_active ? 'danger' : 'ok' }}"
                                type="submit"
                            >
                                {{ $r->is_active
                                    ? __('admin.actions.deactivate')
                                    : __('admin.actions.activate')
                                }}
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="margin-top: 12px;">
            {{ $restaurants->links() }}
        </div>
    </div>
@endsection
