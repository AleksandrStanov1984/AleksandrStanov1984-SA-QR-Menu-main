@if(auth()->user()->is_super_admin)
<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.permissions.h2') }}</h2>

    @if(!$restaurantUser)
        <div class="errors">{{ __('admin.permissions.no_user') }}</div>
    @else
        <div class="perm-userline">
            {{ __('admin.permissions.user') }}:
            <strong>{{ $restaurantUser->name }}</strong> ({{ $restaurantUser->email }})
        </div>

        <form method="POST" action="{{ route('admin.restaurants.user_permissions', $restaurant) }}">
            @csrf

            @php($p = $restaurantUser->permissions ?? [])

            <div class="perm-grid">
                <div class="perm-col">
                    <label class="perm-item">
                        <input type="checkbox" name="perm[languages_manage]" @checked($p['languages_manage'] ?? false)>
                        {{ __('admin.permissions.languages') }}
                    </label>

                    <label class="perm-item">
                        <input type="checkbox" name="perm[sections_manage]" @checked($p['sections_manage'] ?? false)>
                        {{ __('admin.permissions.sections') }}
                    </label>

                    <label class="perm-item">
                        <input type="checkbox" name="perm[items_manage]" @checked($p['items_manage'] ?? false)>
                        {{ __('admin.permissions.items') }}
                    </label>
                </div>

                <div class="perm-col">
                    <label class="perm-item">
                        <input type="checkbox" name="perm[banners_manage]" @checked($p['banners_manage'] ?? false)>
                        {{ __('admin.permissions.banners') }}
                    </label>

                    <label class="perm-item">
                        <input type="checkbox" name="perm[socials_manage]" @checked($p['socials_manage'] ?? false)>
                        {{ __('admin.permissions.socials') }}
                    </label>
                </div>

                <div class="perm-col">
                    <label class="perm-item">
                        <input type="checkbox" name="perm[theme_manage]" @checked($p['theme_manage'] ?? false)>
                        {{ __('admin.permissions.theme') }}
                    </label>

                    <label class="perm-item">
                        <input type="checkbox" name="perm[import_manage]" @checked($p['import_manage'] ?? false)>
                        {{ __('admin.permissions.import') }}
                    </label>
                </div>
            </div>

            <div style="margin-top:14px;">
                <button class="btn ok" type="submit">{{ __('admin.permissions.save') }}</button>
            </div>
        </form>
    @endif
</div>
@endif
