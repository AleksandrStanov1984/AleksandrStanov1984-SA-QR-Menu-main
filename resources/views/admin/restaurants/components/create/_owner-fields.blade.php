<div class="col12" style="margin-top:12px">
    <h3 class="mut">{{ __('admin.restaurants.create.sections.user') }}</h3>
</div>

{{-- ПРОФИЛЬ --}}
<div class="col6">
    <label>{{ __('admin.fields.user_name') }}</label>
    <input
        name="user_name"
        value=""
        required
    >
</div>

<div class="col6">
    <label>{{ __('admin.fields.user_email') }}</label>
    <input
        type="email"
        name="user_email"
        value=""
        required
    >
</div>

{{-- БЕЗОПАСНОСТЬ --}}
<div class="col12" style="margin-top:8px">
    <span class="mut" style="font-size:12px">
        {{ __('admin.security.password_hint') }}
    </span>
</div>

<div class="col6">
    <label>{{ __('admin.fields.password') }}</label>
    <input
        type="password"
        name="password"
        autocomplete="new-password"
    >
</div>

<div class="col6">
    <label>{{ __('admin.fields.password_confirm') }}</label>
    <input
        type="password"
        name="password_confirmation"
        autocomplete="new-password"
    >
</div>
