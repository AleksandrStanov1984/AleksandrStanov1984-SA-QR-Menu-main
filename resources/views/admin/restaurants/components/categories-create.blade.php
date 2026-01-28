<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.sections.categories.h2') }}</h2>
    <p class="muted" style="margin-top:-6px;">{{ __('admin.sections.categories.hint') }}</p>

    <form method="POST" action="{{ route('admin.restaurants.categories.store', $restaurant) }}">
        @csrf

        @php
            $locales = $restaurant->enabled_locales ?: ['de'];
        @endphp

        <div class="grid" style="margin-top:12px;">
            @foreach($locales as $loc)
                <div class="col6">
                    <label>{{ __('admin.sections.categories.title') }} ({{ strtoupper($loc) }})</label>
                    <input name="title[{{ $loc }}]" value="{{ old("title.$loc") }}" maxlength="50" required>
                </div>
            @endforeach
        </div>

        <div class="grid" style="margin-top:12px;">
            <div class="col6">
                <label>{{ __('admin.sections.categories.font') }}</label>
                <select name="title_font">
                    <option value="">{{ __('admin.common.select') ?? '—' }}</option>
                    <option value="inter" @selected(old('title_font')==='inter')>Inter</option>
                    <option value="poppins" @selected(old('title_font')==='poppins')>Poppins</option>
                    <option value="roboto" @selected(old('title_font')==='roboto')>Roboto</option>
                    <option value="playfair" @selected(old('title_font')==='playfair')>Playfair</option>
                </select>
            </div>

            <div class="col6">
                <label>{{ __('admin.sections.categories.color') }}</label>
                <input name="title_color" value="{{ old('title_color') }}" placeholder="#FFFFFF">
            </div>
        </div>

        <div style="margin-top:12px; display:flex; gap:10px; justify-content:flex-end;">
            <button class="btn ok" type="submit">{{ __('admin.sections.categories.create_btn') }}</button>
        </div>
    </form>
</div>
