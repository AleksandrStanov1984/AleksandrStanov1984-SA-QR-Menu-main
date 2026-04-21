{{-- resources/views/admin/security/index.blade.php --}}

@extends('admin.layout')

@section('title', __('admin.security.title'))
@section('subtitle', __('admin.security.subtitle'))

@section('content')

    @php
        $mode = $mode ?? 'admin';

        if ($mode === 'restaurant') {
            $passwordRoute = route('admin.restaurants.credentials.password', $restaurant);
        } else {
            $passwordRoute = route('admin.profile.change_password');
        }
    @endphp

    <div class="card security-card">

        <div class="security-card__header">
            <h2>{{ __('admin.profile.change_password.h2') }}</h2>
        </div>

        <div class="card security-block">

            <form method="POST"
                  action="{{ $passwordRoute }}"
                  class="modal-form js-password-form"
                  autocomplete="off">

                @csrf

                {{-- FAKE поля --}}
                <input type="text" name="fake_user2" autocomplete="username" style="position:absolute; left:-9999px;">
                <input type="password" name="fake_pass2" autocomplete="current-password" style="position:absolute; left:-9999px;">

                {{-- CURRENT PASSWORD --}}
                <div class="modal-form__field">
                    <label>{{ __('admin.profile.change_password.current_password') }}</label>

                    <div class="pw-field">
                        <input name="current_password"
                               type="password"
                               required
                               class="js-password-current"
                               autocomplete="current-password">

                        <button type="button" class="pw-toggle">👁</button>
                    </div>

                    @error('current_password')
                    <div class="input-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- NEW PASSWORD --}}
                <div class="modal-form__field">
                    <label>{{ __('admin.profile.change_password.new_password') }}</label>

                    <div class="pw-field">
                        <input name="new_password"
                               type="password"
                               required
                               class="js-password-new"
                               autocomplete="new-password">

                        <button type="button" class="pw-toggle">👁</button>
                    </div>

                    <div class="password-strength">
                        <span data-rule="upper">A-Z</span>
                        <span data-rule="lower">a-z</span>
                        <span data-rule="number">0-9</span>
                        <span data-rule="symbol">@#!</span>
                        <span data-rule="length">8+</span>
                    </div>

                    @error('new_password')
                    <div class="input-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="modal-form__field">
                    <label>{{ __('admin.profile.change_password.confirm_new_password') }}</label>

                    <div class="pw-field">
                        <input name="new_password_confirm"
                               type="password"
                               required
                               class="js-password-confirm"
                               autocomplete="new-password">

                        <button type="button" class="pw-toggle">👁</button>
                    </div>

                    @error('new_password_confirm')
                    <div class="input-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="modal-form__actions">
                    <button class="btn ok js-submit" type="submit" disabled>
                        {{ __('admin.common.change') }}
                    </button>
                </div>

            </form>

            <div class="mut security-hint">
                {{ __('admin.security.password_hint') }}
            </div>

        </div>

    </div>

    @include('admin.security._scripts')

@endsection
