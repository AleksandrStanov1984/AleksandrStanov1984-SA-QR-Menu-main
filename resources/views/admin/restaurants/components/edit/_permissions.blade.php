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
                <label class="perm-item">
                    <span>Языки</span>
                    <input type="checkbox" name="perm[languages_manage]" @checked($p['languages_manage'] ?? false)>
                </label>

                <label class="perm-item">
                    <span>Категории / Разделы</span>
                    <input type="checkbox" name="perm[sections_manage]" @checked($p['sections_manage'] ?? false)>
                </label>

                <label class="perm-item">
                    <span>Блюда / Позиции</span>
                    <input type="checkbox" name="perm[items_manage]" @checked($p['items_manage'] ?? false)>
                </label>

                <label class="perm-item">
                    <span>Баннеры</span>
                    <input type="checkbox" name="perm[banners_manage]" @checked($p['banners_manage'] ?? false)>
                </label>

                <label class="perm-item">
                    <span>Соцсети</span>
                    <input type="checkbox" name="perm[socials_manage]" @checked($p['socials_manage'] ?? false)>
                </label>

                <label class="perm-item">
                    <span>Тема</span>
                    <input type="checkbox" name="perm[theme_manage]" @checked($p['theme_manage'] ?? false)>
                </label>

                <label class="perm-item">
                    <span>Брендинг (фон, оформление)</span>
                    <input type="checkbox" name="perm[branding_manage]" @checked($p['branding_manage'] ?? false)>
                </label>

                <label class="perm-item">
                    <span>Импорт</span>
                    <input type="checkbox" name="perm[import_manage]" @checked($p['import_manage'] ?? false)>
                </label>
            </div>



            <div style="margin-top:14px;">
                <button class="btn ok" type="submit">{{ __('admin.permissions.save') }}</button>
            </div>
        </form>
    @endif
</div>
@endif
