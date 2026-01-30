@php
    use App\Support\Permissions;

    $u = auth()->user();
    $meta = is_array($restaurant->meta ?? null) ? $restaurant->meta : [];

    // права
    $canBgUpload  = Permissions::can($u, 'branding.backgrounds.upload');
    $canThemeMode = Permissions::can($u, 'branding.theme_mode.edit');

    // дефолт
    $themeMode = $meta['theme_mode'] ?? 'light';
@endphp

@if($canBgUpload || $canThemeMode)
<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.branding.title') }}</h2>

    <form method="POST"
          action="{{ route('admin.restaurants.branding.backgrounds.update', $restaurant) }}"
          enctype="multipart/form-data">
        @csrf

        {{-- Режим темы --}}
        @if($canThemeMode)
            <div style="margin:10px 0 16px; padding:10px; border:1px solid var(--line); border-radius:10px;">
                <div style="font-weight:600; margin-bottom:8px;">
                    {{ __('admin.branding.mode_title') }}
                </div>

                <div style="display:flex; gap:18px; align-items:center; flex-wrap:wrap;">
                    <label style="display:flex; gap:8px; align-items:center; cursor:pointer; margin:0;">
                        <input type="radio" name="theme_mode" value="auto" @checked($themeMode === 'auto')>
                        <span>{{ __('admin.branding.mode_auto') }}</span>
                    </label>

                    <label style="display:flex; gap:8px; align-items:center; cursor:pointer; margin:0;">
                        <input type="radio" name="theme_mode" value="light" @checked($themeMode === 'light')>
                        <span>{{ __('admin.branding.mode_light') }}</span>
                    </label>

                    <label style="display:flex; gap:8px; align-items:center; cursor:pointer; margin:0;">
                        <input type="radio" name="theme_mode" value="dark" @checked($themeMode === 'dark')>
                        <span>{{ __('admin.branding.mode_dark') }}</span>
                    </label>
                </div>
            </div>
        @endif

        {{-- Фоны --}}
        @if($canBgUpload)
        <div class="grid">
            <div class="col6">
                <label>{{ __('admin.branding.bg_light') }}</label>

                @if(!empty($meta['bg_light']))
                    <img
                        src="{{ asset('storage/'.$meta['bg_light']) }}"
                        alt="bg light"
                        style="width:100%; max-width:100%; border-radius:10px; border:1px solid var(--line); margin-bottom:8px;"
                    >
                @endif

                <input type="file" name="bg_light" accept="image/*">
            </div>

            <div class="col6">
                <label>{{ __('admin.branding.bg_dark') }}</label>

                @if(!empty($meta['bg_dark']))
                    <img
                        src="{{ asset('storage/'.$meta['bg_dark']) }}"
                        alt="bg dark"
                        style="width:100%; max-width:100%; border-radius:10px; border:1px solid var(--line); margin-bottom:8px;"
                    >
                @endif

                <input type="file" name="bg_dark" accept="image/*">
            </div>
        </div>
        @endif

        <div style="margin-top:14px;">
            <button class="btn ok" type="submit">{{ __('admin.branding.save_bg') }}</button>
        </div>
    </form>
</div>
@endif
