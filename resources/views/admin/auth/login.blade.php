@extends('admin.layout')

@section('title', __('admin.auth.login.title'))
@section('subtitle', __('admin.auth.login.subtitle'))

@section('content')
    <div class="row" style="justify-content:center;">
        <div class="card" style="width: 420px; max-width: 100%;">
            <h2>{{ __('admin.auth.login.h2') }}</h2>

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <label>{{ __('admin.fields.email') }}</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >

                <label>{{ __('admin.fields.password') }}</label>
                <input
                    type="password"
                    name="password"
                    required
                >

                <div style="margin-top: 14px; display:flex; justify-content:flex-end;">
                    <button class="btn" type="submit">
                        {{ __('admin.auth.login.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
