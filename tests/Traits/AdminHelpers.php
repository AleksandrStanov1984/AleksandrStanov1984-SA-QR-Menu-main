<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\Restaurant;

trait AdminHelpers
{
    protected function admin(Restaurant $restaurant, array $permissions = []): User
    {
        return User::factory()->create([
            'restaurant_id' => $restaurant->id,
            'is_super_admin' => false,
            'meta' => [
                'permissions' => $permissions,
            ],
        ]);
    }

    protected function superAdmin(): User
    {
        return User::factory()->create([
            'is_super_admin' => true,
        ]);
    }
}
