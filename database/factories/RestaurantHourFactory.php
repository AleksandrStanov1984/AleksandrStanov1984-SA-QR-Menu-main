<?php

namespace Database\Factories;

use App\Models\RestaurantHour;
use App\Models\Restaurant;
use Illuminate\Database\Eloquent\Factories\Factory;

class RestaurantHourFactory extends Factory
{
    protected $model = RestaurantHour::class;

    public function definition(): array
    {
        $isClosed = $this->faker->boolean(20);

        if ($isClosed) {
            return [
                'restaurant_id' => Restaurant::factory(),
                'day_of_week' => $this->faker->numberBetween(1, 7),
                'is_closed' => true,
                'open_time' => null,
                'close_time' => null,
            ];
        }

        $open = $this->faker->numberBetween(6, 12);
        $close = $this->faker->numberBetween($open + 4, 23);

        return [
            'restaurant_id' => Restaurant::factory(),
            'day_of_week' => $this->faker->numberBetween(1, 7),
            'is_closed' => false,
            'open_time' => sprintf('%02d:00', $open),
            'close_time' => sprintf('%02d:00', $close),
        ];
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'is_closed' => true,
            'open_time' => null,
            'close_time' => null,
        ]);
    }
}
