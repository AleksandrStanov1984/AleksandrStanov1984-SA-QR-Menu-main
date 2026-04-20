<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,

            MenuPlanSeeder::class,
            MenuTemplateSeeder::class,

            RestaurantStarterSeeder::class,
            RestaurantBasicSeeder::class,
            RestaurantProSeeder::class,

            RestaurantHoursSeeder::class,
            RestaurantSocialSeeder::class,

          //  RestaurantUsersSeeder::class,

            DevQuickLoginSeeder::class,

            RestaurantTokenSeeder::class,

            SecurityEventSeeder::class,

        ]);
    }
}
