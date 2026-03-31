@php
    use App\Support\Permissions;

    $u = auth()->user();
    $meta = is_array($restaurant->meta ?? null) ? $restaurant->meta : [];

    $canBgUpload  = Permissions::can($u, 'branding.backgrounds.upload');
    $canThemeMode = Permissions::can($u, 'branding.theme_mode.edit');

    $themeMode = $meta['theme_mode'] ?? 'light';
@endphp

@if($canBgUpload || $canThemeMode)
    <div class="card" style="margin-top:16px;">
        <h2>{{ __('admin.branding.title') }}</h2>

        @include('admin.restaurants.components.logo', ['restaurant' => $restaurant])

        <form method="POST"
              action="{{ route('admin.restaurants.branding.backgrounds.update', $restaurant) }}"
              enctype="multipart/form-data"
              data-branding-form
        >
            @csrf

            {{-- ===================== --}}
            {{-- THEME MODE --}}
            {{-- ===================== --}}
            @if($canThemeMode)
                <div class="block">
                    <div class="block-title">
                        {{ __('admin.branding.mode_title') }}
                    </div>

                    <div class="radio-row">
                        <label>
                            <input type="radio" name="theme_mode" value="auto" @checked($themeMode === 'auto')>
                            <span>{{ __('admin.branding.mode_auto') }}</span>
                        </label>

                        <label>
                            <input type="radio" name="theme_mode" value="light" @checked($themeMode === 'light')>
                            <span>{{ __('admin.branding.mode_light') }}</span>
                        </label>

                        <label>
                            <input type="radio" name="theme_mode" value="dark" @checked($themeMode === 'dark')>
                            <span>{{ __('admin.branding.mode_dark') }}</span>
                        </label>
                    </div>
                </div>
            @endif

            {{-- ===================== --}}
            {{-- BACKGROUNDS --}}
            {{-- ===================== --}}
            @if($canBgUpload)
                <div class="grid">
                    <div class="col6">
                        <label>{{ __('admin.branding.bg_light') }}</label>

                        @if(!empty($meta['bg_light']))
                            <img
                                src="{{ asset('assets/'.$meta['bg_light']) }}"
                                alt="bg light"
                                class="preview"
                            >
                        @endif

                        <input type="file" name="bg_light" accept="image/*">
                    </div>

                    <div class="col6">
                        <label>{{ __('admin.branding.bg_dark') }}</label>

                        @if(!empty($meta['bg_dark']))
                            <img
                                src="{{ asset('assets/'.$meta['bg_dark']) }}"
                                alt="bg dark"
                                class="preview"
                            >
                        @endif

                        <input type="file" name="bg_dark" accept="image/*">
                    </div>
                </div><br>
            @endif

            <div class="actions">
                <button class="btn ok" type="submit">
                    {{ __('admin.branding.save_bg') }}
                </button>
            </div>
        </form>
    </div>
@endif

@include('admin.restaurants.components.branding-backgrounds._scripts')

