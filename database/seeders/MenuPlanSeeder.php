<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MenuPlan;

class MenuPlanSeeder extends Seeder
{
    public function run(): void
    {
        MenuPlan::updateOrCreate(
            ['key' => 'starter'],
            [
                'name' => 'Starter',
                'price' => 9.99,
                'description' => 'Basic features',
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 1,

                'features' => [
                    'status' => false,
                    'hours_modal' => false,
                    'search' => false,
                    'bestsellers' => false,
                    'images' => false,
                    'item_modal' => false,
                    'spicy' => false,
                    'is_new' => false,
                    'dish_of_day' => false,
                    'long_description' => false,
                    'socials_limit' => 1,
                    'locales_limit' => 1,
                    'banners' => false,
                    'banner_carousel' => false,
                ],
            ]
        );

        MenuPlan::updateOrCreate(
            ['key' => 'basic'],
            [
                'name' => 'Basic',
                'price' => 19.99,
                'description' => 'Standard features',
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 2,

                'features' => [
                    'status' => true,
                    'hours_modal' => false,
                    'search' => true,
                    'bestsellers' => true,
                    'images' => true,
                    'item_modal' => true,
                    'spicy' => true,
                    'is_new' => false,
                    'dish_of_day' => false,
                    'long_description' => false,
                    'socials_limit' => 3,
                    'locales_limit' => 2,
                    'banners' => false,
                    'banner_carousel' => false,
                ],
            ]
        );

        MenuPlan::updateOrCreate(
            ['key' => 'pro'],
            [
                'name' => 'Pro',
                'price' => 29.99,
                'description' => 'Full features',
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 3,

                'features' => [
                    'status' => true,
                    'hours_modal' => true,
                    'search' => true,
                    'bestsellers' => true,
                    'images' => true,
                    'item_modal' => true,
                    'spicy' => true,
                    'is_new' => true,
                    'dish_of_day' => true,
                    'long_description' => true,
                    'socials_limit' => 5,
                    'locales_limit' => 5,
                    'banners' => true,
                    'banner_carousel' => true,
                ],
            ]
        );
    }
}
