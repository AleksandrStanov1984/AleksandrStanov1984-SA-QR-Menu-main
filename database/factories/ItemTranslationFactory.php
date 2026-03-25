<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ItemTranslation;
use App\Models\Item;

class ItemTranslationFactory extends Factory
{
    protected $model = ItemTranslation::class;

    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'locale' => 'de',
            'title' => fake()->word(),
            'description' => fake()->sentence(),
            'details' => fake()->sentence(),
        ];
    }
}
