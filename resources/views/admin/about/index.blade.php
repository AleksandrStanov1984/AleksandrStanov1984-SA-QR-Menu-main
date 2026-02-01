@extends('admin.layout')

@section('title', __('admin.sidebar.about'))

@section('content')
    <div class="page">
        <div class="page-head">
            <h1 class="page-title">{{ __('admin.about.title') }}</h1>
            <p class="mut">{{ __('admin.about.subtitle') }}</p>
        </div>

        <div class="card">
            <div class="card-body">
                <p>{{ __('admin.about.p1') }}</p>
                <p>{{ __('admin.about.p2') }}</p>
                <p>{{ __('admin.about.p3') }}</p>

                <hr style="border-color: var(--line); margin: 14px 0;">

                <p><strong>{{ __('admin.about.location') }}:</strong> {{ __('admin.about.location_value') }}</p>
                <p>
                    <strong>{{ __('admin.about.email') }}:</strong>
                    <a href="mailto:{{ __('admin.about.email_value') }}">{{ __('admin.about.email_value') }}</a>
                </p>
                <p><strong>{{ __('admin.about.phone') }}:</strong> {{ __('admin.about.phone_value') }}</p>

                <div style="margin-top: 14px;">
                    <a class="btn" href="mailto:{{ __('admin.about.email_value') }}">
                        {{ __('admin.about.send') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
