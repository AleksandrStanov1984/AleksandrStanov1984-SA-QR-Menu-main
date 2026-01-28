@extends('admin.layout')

@section('title', __('admin.security.title') ?? 'Безопасность')
@section('subtitle', __('admin.security.subtitle') ?? 'Email и пароль')

@section('breadcrumbs')
    <a href="{{ route('admin.home') }}">{{ __('admin.breadcrumbs.dashboard') }}</a>
    <span class="sep">/</span>
    <span>{{ __('admin.security.title') ?? 'Безопасность' }}</span>
@endsection

@section('content')
<div class="card">
    <h2 style="margin-top:0;">{{ __('admin.security.h2') ?? 'Безопасность' }}</h2>

    {{-- Смена e-mail --}}
    <div class="card" style="margin-top:14px;">
        <h3 style="margin-top:0;">{{ __('admin.profile.change_email.h2') }}</h3>

        <form method="POST" action="{{ route('admin.profile.change_email') }}" autocomplete="off">
            @csrf

            {{-- anti-autofill traps --}}
            <input type="text" name="fake_user" autocomplete="username" style="position:absolute; left:-9999px; width:1px; height:1px;">
            <input type="password" name="fake_pass" autocomplete="current-password" style="position:absolute; left:-9999px; width:1px; height:1px;">

            <label>{{ __('admin.profile.change_email.current_email') }}</label>
            <input name="current_email" type="email" autocomplete="off" required>

            <label>{{ __('admin.profile.change_email.current_password') }}</label>
            <div class="pw-field">
                <input name="current_password" type="password" autocomplete="new-password" required>
                <button type="button" class="pw-toggle" aria-label="Show password">👁</button>
            </div>

            <label>{{ __('admin.profile.change_email.new_email') }}</label>
            <input name="new_email" type="email" autocomplete="off" required>

            <div style="margin-top:14px; display:flex; justify-content:flex-end;">
                <button class="btn ok" type="submit">{{ __('admin.common.change') }}</button>
            </div>
        </form>
    </div>

    {{-- Смена пароля --}}
    <div class="card" style="margin-top:14px;">
        <h3 style="margin-top:0;">{{ __('admin.profile.change_password.h2') }}</h3>

        <form method="POST" action="{{ route('admin.profile.change_password') }}" autocomplete="off">
            @csrf

            {{-- anti-autofill traps --}}
            <input type="text" name="fake_user2" autocomplete="username" style="position:absolute; left:-9999px; width:1px; height:1px;">
            <input type="password" name="fake_pass2" autocomplete="current-password" style="position:absolute; left:-9999px; width:1px; height:1px;">

            <label>{{ __('admin.profile.change_password.current_email') }}</label>
            <input name="current_email" type="email" autocomplete="off" required>

            <label>{{ __('admin.profile.change_password.current_password') }}</label>
            <div class="pw-field">
                <input name="current_password" type="password" autocomplete="new-password" required>
                <button type="button" class="pw-toggle" aria-label="Show password">👁</button>
            </div>

            <label>{{ __('admin.profile.change_password.new_password') }}</label>
            <div class="pw-field">
                <input name="new_password" type="password" autocomplete="new-password" required>
                <button type="button" class="pw-toggle" aria-label="Show password">👁</button>
            </div>

            <label>{{ __('admin.profile.change_password.confirm_new_password') }}</label>
            <div class="pw-field">
                <input name="new_password_confirm" type="password" autocomplete="new-password" required>
                <button type="button" class="pw-toggle" aria-label="Show password">👁</button>
            </div>

            <div style="margin-top:14px; display:flex; justify-content:flex-end;">
                <button class="btn ok" type="submit">{{ __('admin.common.change') }}</button>
            </div>
        </form>

        <div class="mut" style="margin-top:10px; font-size:12px;">
            Пароль: минимум 8 символов, 1 заглавная, 1 маленькая, 1 цифра и 1 спецсимвол.
        </div>
    </div>
</div>
@endsection
