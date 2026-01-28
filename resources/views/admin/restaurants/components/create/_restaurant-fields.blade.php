<div class="col12">
    <h3 class="mut">{{ __('admin.restaurants.create.sections.restaurant') }}</h3>
</div>

<div class="col6">
    <label>{{ __('admin.fields.name') }}</label>
    <input name="name"
           value="{{ old('name') }}"
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
    @php($tpl = old('template_key', 'classic'))
    <select name="template_key" required>
        <option value="classic"  @selected($tpl==='classic')>{{ __('admin.templates.classic') }}</option>
        <option value="fastfood" @selected($tpl==='fastfood')>{{ __('admin.templates.fastfood') }}</option>
        <option value="bar"      @selected($tpl==='bar')>{{ __('admin.templates.bar') }}</option>
        <option value="services" @selected($tpl==='services')>{{ __('admin.templates.services') }}</option>
    </select>
</div>

<div class="col6">
    <label>{{ __('admin.fields.phone') }}</label>
    <input name="phone"
           value="{{ old('phone') }}"
           placeholder="+49123456789"
           maxlength="16"
           inputmode="tel"
           autocomplete="off"
           data-phone-e164="1">
</div>

<div class="col6">
    <label>{{ __('admin.fields.city') }}</label>
    <input name="city"
           value="{{ old('city') }}"
           maxlength="50"
           pattern="^[^<>]*$"
           autocomplete="off"
           data-capitalize="first">
</div>

<div class="col6">
    <label>{{ __('admin.fields.street') }}</label>
    <input name="street"
           value="{{ old('street') }}"
           maxlength="50"
           pattern="^[^<>]*$"
           autocomplete="off"
           data-capitalize="first">
</div>

<div class="col6">
    <label>{{ __('admin.fields.house_number') }}</label>
    <input name="house_number"
           value="{{ old('house_number') }}"
           maxlength="4"
           pattern="^\d{1,3}[A-Za-z]?$"
           placeholder="12A"
           autocomplete="off">
</div>

<div class="col6">
    <label>{{ __('admin.fields.postal_code') }}</label>
    <input name="postal_code"
           value="{{ old('postal_code') }}"
           maxlength="5"
           pattern="^\d{5}$"
           inputmode="numeric"
           autocomplete="off">
</div>

