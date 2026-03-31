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
          enctype="multipart/form-data">
        @csrf

        <label>{{ __('admin.restaurants.brand.logo_label') }}</label>

        <div style=" gap:10px; align-items:center; margin-top:10px;">

            <input type="file"
                   name="logo"
                   accept=".png,.jpg,.jpeg,.webp"
                   required
                   style="flex:1;">

            <button class="btn ok" type="submit">
                {{ __('admin.common.save') }}
            </button>
        </div>
    </form>
    <br>

@endif
