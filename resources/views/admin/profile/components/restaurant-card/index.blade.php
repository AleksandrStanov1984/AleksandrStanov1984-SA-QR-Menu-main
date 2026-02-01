@php
    use App\Support\Permissions;
    $user = auth()->user();
    $canEditRestaurantProfile = $user && Permissions::can($user, 'restaurant.profile.edit');
@endphp

@if($canEditRestaurantProfile)
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

            <h3 style="margin:14px 0 8px; font-size:14px;">
                {{ __('admin.profile.restaurant.address_h3') }}
            </h3>

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
@endif
