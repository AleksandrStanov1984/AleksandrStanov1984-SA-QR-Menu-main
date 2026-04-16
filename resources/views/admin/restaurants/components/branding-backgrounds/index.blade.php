{{-- admin/restaurants/components/branding-backgrounds/index --}}
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

                    {{-- LIGHT --}}
                    <div class="branding-col">
                        <div class="branding-item">

                            <div class="branding-item__title">
                                {{ __('admin.branding.bg_light') }}
                            </div>

                            <div class="branding-preview-wrapper">

                                <img
                                    src="{{ app(\App\Services\ImageService::class)->url($meta['bg_light'] ?? null) }}"
                                    class="branding-preview"
                                    data-preview="bg_light"
                                >

                                @if(!empty($meta['bg_light']))
                                    <button
                                        type="button"
                                        class="branding-delete-btn"
                                        data-bg-delete="bg_light"
                                        data-bg-url="{{ route('admin.restaurants.branding.backgrounds.delete', [$restaurant, 'bg_light']) }}"
                                    >
                                        ✕
                                    </button>
                                @endif

                            </div>

                            <label class="branding-file-btn">
                                {{ __('admin.common.choose_file') ?? 'Datei wählen' }}

                                <input
                                    type="file"
                                    name="bg_light"
                                    class="branding-file-input"
                                    data-input="bg_light"
                                    accept=".jpg,.jpeg,.png,.webp"
                                >
                            </label>

                        </div>
                    </div>

                    {{-- DARK --}}
                    <div class="branding-col">
                        <div class="branding-item">

                            <div class="branding-item__title">
                                {{ __('admin.branding.bg_dark') }}
                            </div>

                            <div class="branding-preview-wrapper">

                                <img
                                    src="{{ app(\App\Services\ImageService::class)->url($meta['bg_dark'] ?? null) }}"
                                    class="branding-preview"
                                    data-preview="bg_dark"
                                >

                                @if(!empty($meta['bg_dark']))
                                    <button
                                        type="button"
                                        class="branding-delete-btn"
                                        data-bg-delete="bg_dark"
                                        data-bg-url="{{ route('admin.restaurants.branding.backgrounds.delete', [$restaurant, 'bg_dark']) }}"
                                    >
                                        ✕
                                    </button>
                                @endif

                            </div>

                            <label class="branding-file-btn">
                                {{ __('admin.common.choose_file') ?? 'Datei wählen' }}

                                <input
                                    type="file"
                                    name="bg_dark"
                                    class="branding-file-input"
                                    data-input="bg_dark"
                                    accept=".jpg,.jpeg,.png,.webp"
                                >
                            </label>

                        </div>
                    </div>

                </div>
            @else
                <div class="mut" style="margin-top:12px;">
                    {{ __('admin.plan.pro_required_backgrounds') }}
                </div>
            @endif

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
