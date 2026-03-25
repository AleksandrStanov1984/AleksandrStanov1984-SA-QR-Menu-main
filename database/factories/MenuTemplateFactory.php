<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MenuTemplateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => Str::slug($this->faker->unique()->word()),
            'name' => $this->faker->words(2, true),
            'is_active' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
        ];
    }
}
