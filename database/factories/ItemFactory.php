<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\Section;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'section_id' => Section::factory(),
            'price' => 10,
            'currency' => 'EUR',
            'image_path' => null,
            'sort_order' => 1,
            'is_active' => true,
            'meta' => [
                'show_image' => true,
                'spicy' => 0,
                'is_new' => false,
                'dish_of_day' => false,
            ],
        ];
    }
}
