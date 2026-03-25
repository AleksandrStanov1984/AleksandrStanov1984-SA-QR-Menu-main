<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\Restaurant;

trait AdminTestHelpers
{
    protected function createAdminUser(Restaurant $restaurant): User
    {
        return User::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_super_admin' => false,
            'permissions' => [
                'items_manage' => true,
                'sections_manage' => true,
                'branding.logo.upload' => true,
                'theme_manage' => true,
            ],
        ]);
    }
}
