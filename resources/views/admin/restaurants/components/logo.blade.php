{{-- resources/views/admin/restaurants/components/logo.blade.php --}}
{{-- admin/restaurants/components/logo --}}
@include('admin.restaurants.components._styles-logo')

@php
    use App\Support\Permissions;

    $user = auth()->user();

    $logoUrl = !empty($restaurant->logo_path)
        ? app(\App\Services\ImageService::class)->url($restaurant->logo_path)
        : null;
@endphp

@if($user && Permissions::can($user, 'branding.logo.upload'))

    <form method="POST"
          action="{{ route('admin.restaurants.logo.update', $restaurant) }}"
          enctype="multipart/form-data"
          class="branding-logo-form">
        @csrf

        <div class="branding-logo-item">

            <div class="branding-item__title">
                {{ __('admin.restaurants.brand.logo_label') }}
            </div>

            {{-- PREVIEW + DELETE --}}
            <div class="branding-preview-wrapper" style="position:relative;">

                <img
                    src="{{ $logoUrl ?? app(\App\Services\ImageService::class)->logo(null) }}"
                    class="branding-preview branding-logo-preview"
                    data-preview="logo"
                    data-fallback="{{ app(\App\Services\ImageService::class)->logo(null) }}"
                >

                @if($logoUrl)
                    <button
                        type="button"
                        class="branding-delete-btn"
                        data-logo-delete
                        data-logo-url="{{ route('admin.restaurants.logo.delete', $restaurant) }}"
                    >
                        ✕
                    </button>
                @endif

            </div>

            {{-- FILE --}}
            <label class="branding-file-btn">
                {{ __('admin.common.choose_file') ?? 'Datei wählen' }}

                <input
                    type="file"
                    name="logo"
                    class="branding-file-input"
                    data-input="logo"
                    accept=".jpg,.jpeg,.png,.webp"
                >
            </label>

            {{-- SAVE --}}
            <button class="btn ok" type="submit">
                {{ __('admin.common.save') }}
            </button>

        </div>

    </form>

    <br>

@endif

@include('admin.restaurants.components._scripts-logo')


