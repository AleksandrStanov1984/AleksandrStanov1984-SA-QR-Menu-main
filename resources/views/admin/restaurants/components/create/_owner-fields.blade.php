<div class="col12" style="margin-top:12px">
    <h3 class="mut">{{ __('admin.restaurants.create.sections.user') }}</h3>
</div>

<div class="col6">
    <label>{{ __('admin.fields.user_name') }}</label>
    <input name="user_name" value="{{ old('user_name') }}" required>
</div>

<div class="col6">
    <label>{{ __('admin.fields.user_email') }}</label>
    <input type="email" name="user_email" value="{{ old('user_email') }}" required>
</div>

<div class="col6">
    <label>{{ __('admin.fields.password') }}</label>
    <input type="password" name="password" required>
</div>
