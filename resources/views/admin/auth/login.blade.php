{{-- resources/views/admin/auth/login.blade.php --}}
@extends('admin.layout')

@section('title', __('admin.auth.login.title'))
@section('subtitle', __('admin.auth.login.subtitle'))

@section('content')
    <div class="auth-layout">

        <div class="card auth-card">
            <h2 class="auth-title">{{ __('admin.auth.login.h2') }}</h2>

            <form method="POST" action="{{ route('admin.login.submit') }}" class="form-center">
                @csrf

                <div class="form-group">
                    <label>{{ __('admin.fields.email') }}</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label>{{ __('admin.fields.password') }}</label>
                    <input
                        type="password"
                        name="password"
                        required
                    >
                </div>

                <div class="form-actions">
                    <button class="btn" type="submit">
                        {{ __('admin.auth.login.submit') }}
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
