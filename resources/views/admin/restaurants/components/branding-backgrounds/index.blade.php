@php
    $u = auth()->user();
    $p = $u?->permissions ?? [];
    $canBranding = $u?->is_super_admin || ($p['branding_manage'] ?? false);
    $meta = is_array($restaurant->meta ?? null) ? $restaurant->meta : [];
@endphp

@if($canBranding)
<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.permissions.branding') ?? 'Брендирование (фоны)' }}</h2>

    <form method="POST"
          action="{{ route('admin.restaurants.branding.backgrounds.update', $restaurant) }}"
          enctype="multipart/form-data">
        @csrf

        <div class="grid">
            <div class="col6">
                <label>Фон (светлая тема)</label>

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
                <label>Фон (тёмная тема)</label>

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

        <div style="margin-top:14px;">
            <button class="btn ok" type="submit">Сохранить фон</button>
        </div>
    </form>
</div>
@endif
