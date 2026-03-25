<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MenuPlan;

class MenuPlanFactory extends Factory
{
    protected $model = MenuPlan::class;

    public function definition(): array
    {
        return [
            'key' => 'starter',
            'name' => 'Starter',
            'features' => [
                'images' => false,
                'item_modal' => false,
                'spicy' => false,
                'is_new' => false,
                'dish_of_day' => false,
                'long_description' => false,
            ],
        ];
    }
}
