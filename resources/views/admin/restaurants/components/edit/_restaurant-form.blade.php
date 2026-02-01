@php
    use App\Support\Permissions;
    $user = auth()->user();
@endphp

@if($user && Permissions::can($user, 'restaurants.edit'))
<div class="card">
    <h2>{{ __('admin.restaurants.edit.h2') }}</h2>

    <form method="POST" action="{{ route('admin.restaurants.update', $restaurant) }}">
        @csrf
        @method('PUT')

        <div class="grid">
            <div class="col6">
                <label>{{ __('admin.fields.name') }}</label>
                <input name="name"
                       value="{{ old('name', $restaurant->name) }}"
                       maxlength="20"
                       pattern="^[^\d<>]+$"
                       inputmode="text"
                       autocomplete="off"
                       required
                       data-capitalize="first"
                       data-no-digits="1">
            </div>

            <div class="col6">
                <label>{{ __('admin.fields.template') }}</label>
                <select name="template_key" required>
                    @foreach (['classic','fastfood','bar','services'] as $tpl)
                        <option value="{{ $tpl }}" @selected($restaurant->template_key === $tpl)>
                            {{ __('admin.templates.'.$tpl) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col6">
                <label>{{ __('admin.fields.phone') }}</label>
                <input name="phone"
                       value="{{ old('phone', $restaurant->phone) }}"
                       placeholder="+49123456789"
                       maxlength="16"
                       inputmode="tel"
                       autocomplete="off"
                       data-phone-e164="1">
            </div>

            <div class="col6">
                <label>{{ __('admin.fields.city') }}</label>
                <input name="city"
                       value="{{ old('city', $restaurant->city) }}"
                       maxlength="50"
                       pattern="^[^<>]*$"
                       autocomplete="off"
                       data-capitalize="first">
            </div>

            <div class="col6">
                <label>{{ __('admin.fields.street') }}</label>
                <input name="street"
                       value="{{ old('street', $restaurant->street) }}"
                       maxlength="50"
                       pattern="^[^<>]*$"
                       autocomplete="off"
                       data-capitalize="first">
            </div>

            <div class="col6">
                <label>{{ __('admin.fields.house_number') }}</label>
                <input name="house_number"
                       value="{{ old('house_number', $restaurant->house_number) }}"
                       maxlength="4"
                       pattern="^\d{1,3}[A-Za-z]?$"
                       placeholder="12A"
                       autocomplete="off">
            </div>

            <div class="col6">
                <label>{{ __('admin.fields.postal_code') }}</label>
                <input name="postal_code"
                       value="{{ old('postal_code', $restaurant->postal_code) }}"
                       maxlength="5"
                       pattern="^\d{5}$"
                       inputmode="numeric"
                       autocomplete="off">
            </div>
        </div>

        <div style="margin-top:16px">
            <button class="btn ok">{{ __('admin.actions.save') }}</button>
        </div>
    </form>
</div>
@endif
