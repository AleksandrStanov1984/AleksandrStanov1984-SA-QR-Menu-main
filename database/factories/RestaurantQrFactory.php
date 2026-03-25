<?php

namespace Database\Factories;

use App\Models\Restaurant;
use App\Models\RestaurantQr;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantQrFactory extends Factory
{
    protected $model = RestaurantQr::class;

    public function definition(): array
    {
        return [
            'restaurant_id' => Restaurant::factory(),

            // базовый путь как у тебя в проекте
            'qr_path' => 'restaurants/' . $this->faker->numberBetween(1, 1000) . '/qr/raw/test.svg',

            'logo_path' => null,
            'background_path' => null,

            'settings' => [],
        ];
    }
}
