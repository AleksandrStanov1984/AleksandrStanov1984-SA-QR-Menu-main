@php
    $u = auth()->user();
    $canLanguages = $u?->is_super_admin || $u?->hasPerm('languages_manage') || $u?->hasPerm('import_manage');
@endphp

@if($canLanguages)
<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.languages.h2') }}</h2>

    <div class="mut" style="font-size:12px; margin-bottom:10px;">
        {{ __('admin.languages.enabled') }}:
        <strong>{{ implode(', ', $restaurant->enabled_locales ?: ['de']) }}</strong>
        • {{ __('admin.languages.default') }}:
        <strong>{{ $restaurant->default_locale ?: 'de' }}</strong>
    </div>

    <div class="grid">
        <div class="col6">
            <h3 class="mut" style="margin:0 0 8px 0;">{{ __('admin.languages.add_h3') }}</h3>

            <form method="POST"
                  action="{{ route('admin.restaurants.languages.import', $restaurant) }}"
                  enctype="multipart/form-data">
                @csrf

                <label>{{ __('admin.languages.locale_label') }}</label>
                <input name="locale" value="{{ old('locale') }}" placeholder="en" required>

                <label>{{ __('admin.languages.file_label') }}</label>
                <input type="file" name="file" accept=".json,.txt" required>

                <label style="margin-top:10px;">
                    <input type="checkbox" name="is_default" value="1" @checked(old('is_default'))>
                    {{ __('admin.languages.set_default_checkbox') }}
                </label>

                <div style="margin-top:12px;">
                    <button class="btn ok">{{ __('admin.languages.upload_import') }}</button>
                </div>
            </form>
        </div>

        <div class="col6">
            <h3 class="mut" style="margin:0 0 8px 0;">{{ __('admin.languages.default_h3') }}</h3>

            <form method="POST" action="{{ route('admin.restaurants.languages.default', $restaurant) }}">
                @csrf

                <label>{{ __('admin.languages.default_select_label') }}</label>
                <select name="default_locale" required>
                    @php($enabled = $restaurant->enabled_locales ?: ['de'])
                    @foreach($enabled as $loc)
                        <option value="{{ $loc }}" @selected(($restaurant->default_locale ?: 'de') === $loc)>
                            {{ strtoupper($loc) }}
                        </option>
                    @endforeach
                </select>

                <div style="margin-top:12px;">
                    <button class="btn ok">{{ __('admin.languages.save_default') }}</button>
                </div>
            </form>

            <div class="mut" style="font-size:12px; margin-top:10px;">
                {{ __('admin.languages.note_de_default') }}
            </div>
        </div>
    </div>
</div>
@else
<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.languages.h2') }}</h2>
    <div class="mut" style="font-size:12px;">
        {{ __('admin.languages.no_access') }}
    </div>
</div>
@endif
