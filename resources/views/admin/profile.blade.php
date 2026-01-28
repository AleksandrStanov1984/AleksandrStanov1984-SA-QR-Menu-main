@extends('admin.layout')

@section('title', __('admin.profile.title'))
@section('subtitle', __('admin.profile.subtitle'))

@section('breadcrumbs')
    <a href="{{ route('admin.home') }}">{{ __('admin.breadcrumbs.dashboard') }}</a>
    <span class="sep">/</span>

    @if (!empty($restaurant))
        <a href="{{ route('admin.restaurants.edit', ['restaurant' => $restaurant->id]) }}">{{ $restaurant->name }}</a>
        <span class="sep">/</span>
    @endif

    <span>{{ __('admin.profile.subtitle') }}</span>
@endsection

@section('content')
<div class="card">
    <h2>{{ __('admin.profile.h2') }}</h2>

    <form method="POST" action="{{ route('admin.profile.update') }}">
        @csrf

        <div class="grid">
            <div class="col6">
                <label>{{ __('admin.fields.name') }}</label>
                <input name="name" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="col6">
                <label>{{ __('admin.fields.email') }}</label>
                <input value="{{ $user->email }}" disabled>
            </div>
        </div>

        <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
            <button class="btn ok" type="submit">{{ __('admin.common.save') }}</button>
            <button class="btn secondary" type="button" onclick="openModal('emailModal')">
                {{ __('admin.profile.change_email_btn') }}
            </button>
            <button class="btn secondary" type="button" onclick="openModal('passModal')">
                {{ __('admin.profile.change_password_btn') }}
            </button>
        </div>
    </form>
</div>

{{-- RESTAURANT CARD --}}
<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.profile.restaurant.h2') }}</h2>

    @if (empty($restaurant))
        <div class="mut" style="font-size:13px;">
            {{ __('admin.profile.restaurant.no_restaurant_context') }}
        </div>
    @else
        <form method="POST" action="{{ route('admin.profile.restaurant.update') }}">
            @csrf

            <div class="grid">
                <div class="col6">
                    <label>{{ __('admin.profile.restaurant.restaurant_name') }}</label>
                    <input name="restaurant_name" value="{{ old('restaurant_name', $restaurant->name) }}" required>
                </div>

                <div class="col6">
                    <label>{{ __('admin.profile.restaurant.contact_name') }}</label>
                    <input name="contact_name" value="{{ old('contact_name', $restaurant->contact_name) }}">
                </div>
            </div>

            <div class="grid">
                <div class="col6">
                    <label>{{ __('admin.fields.phone') }}</label>
                    <input name="phone" value="{{ old('phone', $restaurant->phone) }}">
                </div>

                <div class="col6">
                    <label>{{ __('admin.profile.restaurant.email') }}</label>
                    <input name="contact_email" type="email" value="{{ old('contact_email', $restaurant->contact_email) }}">
                </div>
            </div>

            <h3 style="margin:14px 0 8px; font-size:14px;">{{ __('admin.profile.restaurant.address_h3') }}</h3>

            <div class="grid">
                <div class="col6">
                    <label>{{ __('admin.fields.city') }}</label>
                    <input name="city" value="{{ old('city', $restaurant->city) }}">
                </div>

                <div class="col6">
                    <label>{{ __('admin.fields.postal_code') }}</label>
                    <input name="postal_code" value="{{ old('postal_code', $restaurant->postal_code) }}">
                </div>
            </div>

            <div class="grid">
                <div class="col6">
                    <label>{{ __('admin.fields.street') }}</label>
                    <input name="street" value="{{ old('street', $restaurant->street) }}">
                </div>

                <div class="col6">
                    <label>{{ __('admin.fields.house_number') }}</label>
                    <input name="house_number" value="{{ old('house_number', $restaurant->house_number) }}">
                </div>
            </div>

            <div style="margin-top:16px; display:flex; gap:10px; flex-wrap:wrap;">
                <button class="btn ok" type="submit">{{ __('admin.common.save') }}</button>
            </div>
        </form>
    @endif
</div>

{{-- PERMISSIONS CARD (read-only) --}}
<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.profile.permissions.h2') }}</h2>

    @if (!empty($user) && !empty($user->is_super_admin))
        <div class="pill green">{{ __('admin.profile.permissions.super_admin') }}</div>
    @endif

    @if (!empty($permissions) && count($permissions))
        <div style="margin-top:12px; display:flex; flex-wrap:wrap; gap:8px;">
            @foreach ($permissions as $p)
                <span class="pill">{{ $p }}</span>
            @endforeach
        </div>
    @else
        <div class="mut" style="font-size:13px; margin-top:8px;">{{ __('admin.profile.permissions.no_permissions') }}</div>
    @endif
</div>

{{-- MODAL: Change Email --}}
<div id="emailModal" class="modal" aria-hidden="true">
    <div class="modal__backdrop" onclick="closeModal('emailModal')"></div>
    <div class="modal__panel">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
            <h2 style="margin:0;">{{ __('admin.profile.change_email.h2') }}</h2>
            <button class="btn secondary small" type="button" onclick="closeModal('emailModal')">X</button>
        </div>

        <form method="POST"
              action="{{ route('admin.profile.change_email') }}"
              style="margin-top:12px;"
              autocomplete="off">
            @csrf

            <label>{{ __('admin.profile.change_email.current_email') }}</label>
            <input name="current_email" type="email" autocomplete="email" required>

            <label>{{ __('admin.profile.change_email.current_password') }}</label>
            <input name="current_password" type="password" autocomplete="current-password" required>

            <label>{{ __('admin.profile.change_email.new_email') }}</label>
            <input name="new_email" type="email" autocomplete="email" required>

            <div style="margin-top:14px; display:flex; gap:10px; justify-content:flex-end;">
                <button class="btn secondary" type="button" onclick="closeModal('emailModal')">
                    {{ __('admin.common.cancel') }}
                </button>
                <button class="btn ok" type="submit">
                    {{ __('admin.common.change') }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Change Password --}}
<div id="passModal" class="modal" aria-hidden="true">
    <div class="modal__backdrop" onclick="closeModal('passModal')"></div>
    <div class="modal__panel">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:10px;">
            <h2 style="margin:0;">{{ __('admin.profile.change_password.h2') }}</h2>
            <button class="btn secondary small" type="button" onclick="closeModal('passModal')">X</button>
        </div>

        <form method="POST"
              action="{{ route('admin.profile.change_password') }}"
              style="margin-top:12px;"
              autocomplete="off">
            @csrf

            <label>{{ __('admin.profile.change_password.current_email') }}</label>
            <input name="current_email" type="email" autocomplete="email" required>

            <label>{{ __('admin.profile.change_password.current_password') }}</label>
            <input name="current_password" type="password" autocomplete="current-password" required>

            <label>{{ __('admin.profile.change_password.new_password') }}</label>
            <input name="new_password" type="password" autocomplete="new-password" required>

            <label>{{ __('admin.profile.change_password.confirm_new_password') }}</label>
            <input name="new_password_confirm" type="password" autocomplete="new-password" required>

            <div style="margin-top:14px; display:flex; gap:10px; justify-content:flex-end;">
                <button class="btn secondary" type="button" onclick="closeModal('passModal')">
                    {{ __('admin.common.cancel') }}
                </button>
                <button class="btn ok" type="submit">
                    {{ __('admin.common.change') }}
                </button>
            </div>
        </form>
    </div>
</div>

@if ($errors->any())
<script>
    (function () {
        const errorFields = @json(array_keys($errors->toArray()));

        const emailFields = ['current_email', 'new_email'];
        const passwordFields = ['current_password', 'new_password', 'new_password_confirm'];

        if (errorFields.some(f => emailFields.includes(f))) {
            openModal('emailModal');
            return;
        }

        if (errorFields.some(f => passwordFields.includes(f))) {
            openModal('passModal');
            return;
        }
    })();
</script>
@endif
@endsection
