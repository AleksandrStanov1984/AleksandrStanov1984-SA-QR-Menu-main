@extends('admin.layout')

@section('title', __('admin.security.title'))
@section('subtitle', __('admin.security.subtitle'))

@section('content')

    @php
        $mode = $mode ?? 'admin';

        if ($mode === 'restaurant') {
            $emailRoute = route('admin.restaurants.credentials.email', $restaurant);
            $passwordRoute = route('admin.restaurants.credentials.password', $restaurant);
        } else {
            $emailRoute = route('admin.profile.change_email');
            $passwordRoute = route('admin.profile.change_password');
        }
    @endphp

    <div class="card">
        <h2 style="margin-top:0;">{{ __('admin.security.h2') }}</h2>

        {{-- 🔥 КОГО РЕДАКТИРУЕМ --}}
        @if($mode === 'restaurant')
            <div class="mut" style="margin-bottom:10px;">
                {{ __('admin.security.user_object') }}:
                <strong>{{ $user->email }}</strong>
            </div>
        @endif

        {{-- Смена email --}}
        <div class="card" style="margin-top:14px;">
            <h3 style="margin-top:0;">{{ __('admin.profile.change_email.h2') }}</h3>

            <form method="POST" action="{{ $emailRoute }}" autocomplete="off">
                @csrf

                <input type="text" name="fake_user" autocomplete="username" style="position:absolute; left:-9999px;">
                <input type="password" name="fake_pass" autocomplete="current-password" style="position:absolute; left:-9999px;">

                <label>{{ __('admin.profile.change_email.current_email') }}</label>
                <input name="current_email" type="email" required>

                <label>{{ __('admin.profile.change_email.current_password') }}</label>
                <div class="pw-field">
                    <input name="current_password" type="password" required>
                    <button type="button" class="pw-toggle">👁</button>
                </div>

                <label>{{ __('admin.profile.change_email.new_email') }}</label>
                <input name="new_email" type="email" required>

                <div style="margin-top:14px; display:flex; justify-content:flex-end;">
                    <button class="btn ok" type="submit">
                        {{ __('admin.common.change') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Смена пароля --}}
        <div class="card" style="margin-top:14px;">
            <h3 style="margin-top:0;">{{ __('admin.profile.change_password.h2') }}</h3>

            <form method="POST" action="{{ $passwordRoute }}" autocomplete="off">
                @csrf

                <input type="text" name="fake_user2" autocomplete="username" style="position:absolute; left:-9999px;">
                <input type="password" name="fake_pass2" autocomplete="current-password" style="position:absolute; left:-9999px;">

                <label>{{ __('admin.profile.change_password.current_email') }}</label>
                <input name="current_email" type="email" required>

                <label>{{ __('admin.profile.change_password.current_password') }}</label>
                <div class="pw-field">
                    <input name="current_password" type="password" required>
                    <button type="button" class="pw-toggle">👁</button>
                </div>

                <label>{{ __('admin.profile.change_password.new_password') }}</label>
                <div class="pw-field">
                    <input name="new_password" type="password" required>
                    <button type="button" class="pw-toggle">👁</button>
                </div>

                <label>{{ __('admin.profile.change_password.confirm_new_password') }}</label>
                <div class="pw-field">
                    <input name="new_password_confirm" type="password" required>
                    <button type="button" class="pw-toggle">👁</button>
                </div>

                <div style="margin-top:14px; display:flex; justify-content:flex-end;">
                    <button class="btn ok" type="submit">
                        {{ __('admin.common.change') }}
                    </button>
                </div>
            </form>

            <div class="mut" style="margin-top:10px; font-size:12px;">
                {{ __('admin.security.password_hint') }}
            </div>
        </div>
    </div>

@endsection
