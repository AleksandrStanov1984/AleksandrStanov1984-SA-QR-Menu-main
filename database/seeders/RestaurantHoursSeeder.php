<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\RestaurantHour;

class RestaurantHoursSeeder extends Seeder
{
    public function run(): void
    {
        $restaurantIds = Restaurant::pluck('id')->toArray();

        foreach ($restaurantIds as $restaurantId) {

            // базовые часы (рандом)
            $baseOpen  = rand(9, 11);
            $baseClose = rand(21, 23);

            for ($day = 0; $day <= 6; $day++) {

                // логика Германии: воскресенье чаще закрыт
                $isClosed = ($day === 0)
                    ? rand(0, 100) < 40 // воскресенье 40% закрыт
                    : rand(0, 100) < 10;

                if ($isClosed) {
                    RestaurantHour::updateOrCreate(
                        [
                            'restaurant_id' => $restaurantId,
                            'day_of_week' => $day
                        ],
                        [
                            'open_time' => null,
                            'close_time' => null,
                            'is_closed' => true
                        ]
                    );
                    continue;
                }

                // пятница/суббота дольше работают
                if ($day >= 5) {
                    $open  = $baseOpen;
                    $close = $baseClose + rand(1, 2);
                } else {
                    $open  = $baseOpen + rand(-1, 1);
                    $close = $baseClose + rand(-1, 1);
                }

                // ограничения
                $open  = max(8, min(12, $open));
                $close = max(20, min(24, $close));

                RestaurantHour::updateOrCreate(
                    [
                        'restaurant_id' => $restaurantId,
                        'day_of_week' => $day
                    ],
                    [
                        'open_time' => sprintf('%02d:00', $open),
                        'close_time' => sprintf('%02d:00', $close),
                        'is_closed' => false
                    ]
                );
            }
        }
    }
}
