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

                'features' => config('plan_features.starter'),
            ]
        );

        MenuPlan::updateOrCreate(
            ['key' => 'basic'],
            [
                'name' => 'Basic',
                'price' => 24.99,
                'description' => 'Standard features',
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 2,

                'features' => config('plan_features.basic'),
            ]
        );

        MenuPlan::updateOrCreate(
            ['key' => 'pro'],
            [
                'name' => 'Pro',
                'price' => 34.99,
                'description' => 'Full features',
                'is_active' => true,
                'is_public' => true,
                'sort_order' => 3,

                'features' => config('plan_features.pro'),
            ]
        );
    }
}
