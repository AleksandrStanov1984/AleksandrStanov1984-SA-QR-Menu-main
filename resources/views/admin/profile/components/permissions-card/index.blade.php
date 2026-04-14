<div class="card" style="margin-top:16px;">
    <h2>{{ __('admin.profile.permissions.h2') }}</h2>

    @if (!empty($user) && !empty($user->is_super_admin))
        <div class="pill green">{{ __('admin.profile.permissions.super_admin') }}</div>
    @endif

        <div class="card" style="margin-top:16px;">
            <div class="card-header">
                <h2>{{ __('admin.permissions.h2') }}</h2>
            </div>

            <div class="card-body">

                @include('admin.restaurants.components.edit._permissions', [
                    'restaurant' => $restaurant,
                    'restaurantUser' => $restaurantUser ?? null,
                ])

            </div>
        </div>

</div>
