<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Restaurant;

class DevQuickLoginSeeder extends Seeder
{
    public function run(): void
    {
        // =============================
        // USERS FOR RESTAURANTS
        // =============================
        $restaurants = Restaurant::all();

        foreach ($restaurants as $restaurant) {

            $email = match ($restaurant->plan_key) {
                'starter' => 'starter@test.com',
                'basic'   => 'basic@test.com',
                'pro'     => 'pro@test.com',
                default   => "restaurant{$restaurant->id}@test.com",
            };

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => ucfirst($restaurant->plan_key) . ' User',
                    'password' => Hash::make('password123'),
                    'restaurant_id' => $restaurant->id,
                    'is_super_admin' => false,

                    // полный доступ (dev режим)
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
                    ],
                ]
            );
        }
    }
}
