@extends('admin.layout')

@section('title', __('admin.security.title'))
@section('subtitle', __('admin.security.subtitle'))

@section('content')

    @php
        $mode = $mode ?? 'admin';

        if ($mode === 'restaurant') {
            $emailRoute = route('admin.restaurants.credentials.email', $restaurant);
        } else {
            $emailRoute = route('admin.profile.change_email');
        }
    @endphp

    @include('admin.security._styles')

    <div class="card security-card">

        <div class="security-card__header">
            <h2>{{ __('admin.profile.change_email.h2') }}</h2>
        </div>

        <div class="card security-block">

            <form method="POST"
                  action="{{ $emailRoute }}"
                  class="modal-form js-email-form"
                  autocomplete="off">

                @csrf

                {{-- FAKE (анти autofill) --}}
                <input type="text" name="fake_user" autocomplete="username" style="position:absolute; left:-9999px;">
                <input type="password" name="fake_pass" autocomplete="current-password" style="position:absolute; left:-9999px;">

                {{-- CURRENT EMAIL --}}
                <div class="modal-form__field">
                    <label>{{ __('admin.profile.change_email.current_email') }}</label>

                    <div class="input-readonly">
                        {{ $user->email }}
                    </div>
                </div>

                {{-- NEW EMAIL --}}
                <div class="modal-form__field">
                    <label>{{ __('admin.profile.change_email.new_email') }}</label>

                    <input name="new_email"
                           type="email"
                           required
                           class="js-email-new"
                           autocomplete="off">

                    @error('new_email')
                    <div class="input-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- CURRENT PASSWORD --}}
                <div class="modal-form__field">
                    <label>{{ __('admin.profile.change_email.current_password') }}</label>

                    <div class="pw-field">
                        <input name="current_password"
                               type="password"
                               required
                               autocomplete="current-password">

                        <button type="button" class="pw-toggle">👁</button>
                    </div>

                    @error('current_password')
                    <div class="input-error">{{ $message }}</div>
                    @enderror
                </div><br>

                <div class="modal-form__actions">
                    <button class="btn ok js-submit" type="submit" disabled>
                        {{ __('admin.common.change') }}
                    </button>
                </div>

            </form>

        </div>

    </div>

    @include('admin.security._scripts')
@endsection



