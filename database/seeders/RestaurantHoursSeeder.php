<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RestaurantHour;

class RestaurantHoursSeeder extends Seeder
{
    public function run(): void
    {

        $restaurantId = 10;

        $hours = [

            0 => ['10:00','21:00'], // Sunday
            1 => ['10:00','22:00'],
            2 => ['10:00','22:00'],
            3 => ['10:00','22:00'],
            4 => ['10:00','22:00'],
            5 => ['10:00','23:00'],
            6 => ['10:00','23:00'],

        ];

        foreach ($hours as $day => $time) {

            RestaurantHour::updateOrCreate(

                [
                    'restaurant_id' => $restaurantId,
                    'day_of_week' => $day
                ],

                [
                    'open_time' => $time[0],
                    'close_time' => $time[1],
                    'is_closed' => false
                ]

            );

        }

    }
}
