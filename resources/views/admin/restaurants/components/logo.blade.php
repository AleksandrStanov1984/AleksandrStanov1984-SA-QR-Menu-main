@php
    use App\Support\Permissions;

    $user = auth()->user();
@endphp

@if($user && Permissions::can($user, 'branding.logo.upload'))
<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.restaurants.brand.h2') }}</h2>

    @if(!empty($restaurant->logo_path))
        <div style="margin:10px 0;">
            <img
                src="{{ \Illuminate\Support\Facades\Storage::url($restaurant->logo_path) }}"
                alt="logo"
                style="max-height:64px; width:auto; display:block;">
        </div>
    @endif

    <form method="POST"
          action="{{ route('admin.restaurants.logo.update', $restaurant) }}"
          enctype="multipart/form-data">
        @csrf

        <label>{{ __('admin.restaurants.brand.logo_label') }}</label>
        <input type="file" name="logo" accept=".png,.jpg,.jpeg,.webp" required>

        <div style="margin-top:14px; display:flex; gap:10px; justify-content:flex-end;">
            <button class="btn ok" type="submit">
                {{ __('admin.common.save') }}
            </button>
        </div>
    </form>
</div>
@endif
