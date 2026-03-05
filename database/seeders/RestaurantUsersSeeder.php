<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RestaurantUsersSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Restaurant #10 user
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(

            ['email' => 'restaurant10@example.com'],

            [
                'name' => 'Restaurant 10 Manager',

                'password' => Hash::make('password123'),

                'restaurant_id' => 10,

                'is_super_admin' => false
            ]

        );

        /*
        |--------------------------------------------------------------------------
        | Additional staff user
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(

            ['email' => 'restaurant10.staff@example.com'],

            [
                'name' => 'Restaurant 10 Staff',

                'password' => Hash::make('password123'),

                'restaurant_id' => 10,

                'is_super_admin' => false
            ]

        );
    }
}
