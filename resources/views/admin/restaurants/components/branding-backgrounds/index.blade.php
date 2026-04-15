{{-- resources/views/admin/restaurants/components/branding-backgrounds/index.blade.php --}}


@php
    $meta = is_array($restaurant->meta ?? null) ? $restaurant->meta : [];

    $canBgUpload  = $restaurant->feature('custom_background');
    $canThemeMode = true;

    $themeMode = $meta['theme_mode'] ?? 'light';
@endphp

@include('admin.restaurants.components.branding-backgrounds._styles')

@if($canBgUpload || $canThemeMode)
    <div class="card branding-card" style="margin-top:16px;">
        <h2>{{ __('admin.branding.title') }}</h2>

        @include('admin.restaurants.components.logo', ['restaurant' => $restaurant])

        <form method="POST"
              action="{{ route('admin.restaurants.branding.backgrounds.update', $restaurant) }}"
              enctype="multipart/form-data"
              data-branding-form>
            @csrf

            @if($canThemeMode)
                <div class="block branding-theme-block">
                    <div class="block-title">
                        {{ __('admin.branding.mode_title') }}
                    </div>

                    <div class="radio-row branding-theme-row">
                        <label class="branding-theme-option">
                            <input type="radio" name="theme_mode" value="auto" @checked($themeMode === 'auto')>
                            <span>{{ __('admin.branding.mode_auto') }}</span>
                        </label>

                        <label class="branding-theme-option">
                            <input type="radio" name="theme_mode" value="light" @checked($themeMode === 'light')>
                            <span>{{ __('admin.branding.mode_light') }}</span>
                        </label>

                        <label class="branding-theme-option">
                            <input type="radio" name="theme_mode" value="dark" @checked($themeMode === 'dark')>
                            <span>{{ __('admin.branding.mode_dark') }}</span>
                        </label>
                    </div>
                </div>
            @endif

            @if($canBgUpload)
                <div class="branding-grid">
                    <div class="branding-col">
                        <label>{{ __('admin.branding.bg_light') }}</label>

                        <div class="branding-preview-wrap">
                            @if(!empty($meta['bg_light']))
                                <img
                                    src="{{ asset('assets/'.$meta['bg_light']) }}"
                                    alt="bg light"
                                    class="branding-preview"
                                >
                            @else
                                <div class="branding-preview branding-preview--empty">
                                    {{ __('admin.branding.no_image') ?? 'No image' }}
                                </div>
                            @endif
                        </div>

                        <input type="file" name="bg_light" accept="image/*">
                    </div>

                    <div class="branding-col">
                        <label>{{ __('admin.branding.bg_dark') }}</label>

                        <div class="branding-preview-wrap">
                            @if(!empty($meta['bg_dark']))
                                <img
                                    src="{{ asset('assets/'.$meta['bg_dark']) }}"
                                    alt="bg dark"
                                    class="branding-preview"
                                >
                            @else
                                <div class="branding-preview branding-preview--empty">
                                    {{ __('admin.branding.no_image') ?? 'No image' }}
                                </div>
                            @endif
                        </div>

                        <input type="file" name="bg_dark" accept="image/*">
                    </div>
                </div>
            @else
                <div class="mut" style="margin-top:12px;">
                    {{ __('admin.plan.pro_required_backgrounds') ?? 'Custom backgrounds available in PRO plan' }}
                </div>
            @endif

            <br>
            <div class="actions">
                <button class="btn ok" type="submit">
                    {{ __('admin.branding.save_bg') }}
                </button>
            </div>
        </form>
    </div>
@endif

@include('admin.restaurants.components.branding-backgrounds.og.index')

@include('admin.restaurants.components.branding-backgrounds._scripts')
