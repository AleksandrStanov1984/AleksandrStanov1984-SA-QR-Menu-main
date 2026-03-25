<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            Restaurant10Seeder::class,
            RestaurantHoursSeeder::class,
            RestaurantUsersSeeder::class,
            MenuPlanSeeder::class,
            MenuTemplateSeeder::class,
        ]);
    }
}
