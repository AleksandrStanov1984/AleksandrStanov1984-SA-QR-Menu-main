<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.profile.permissions.h2') }}</h2>

    @if (!empty($user) && !empty($user->is_super_admin))
        <div class="pill green">{{ __('admin.profile.permissions.super_admin') }}</div>
    @endif

    @if (!empty($permissions) && count($permissions))
        <div style="margin-top:12px; display:flex; flex-wrap:wrap; gap:8px;">
            @foreach ($permissions as $p)
                <span class="pill">{{ $p }}</span>
            @endforeach
        </div>
    @else
        <div class="mut" style="font-size:13px; margin-top:8px;">
            {{ __('admin.profile.permissions.no_permissions') }}
        </div>
    @endif
</div>
