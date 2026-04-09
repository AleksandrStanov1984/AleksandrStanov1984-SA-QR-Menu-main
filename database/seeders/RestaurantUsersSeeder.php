<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Restaurant;

class RestaurantUsersSeeder extends Seeder
{
    public function run(): void
    {
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {

            $rid = $restaurant->id;

            User::updateOrCreate(

                [
                    // ✅ теперь это owner, а не "restaurant"
                    'email' => "owner{$rid}@example.com"
                ],

                [
                    'name' => $restaurant->name . ' Owner',

                    'password' => Hash::make('password123'),

                    'restaurant_id' => $rid,

                    'is_super_admin' => false,

                    // 👉 полный доступ (на будущее, сейчас не влияет)
                    'meta' => [
                        'permissions' => [
                            'menu_manage' => true,
                            'items_manage' => true,
                            'sections_manage' => true,
                            'branding_manage' => true,
                            'hours_manage' => true,
                            'socials_manage' => true,
                            'qr_manage' => true,
                            'import_manage' => true,
                        ]
                    ]
                ]
            );
        }
    }
}
