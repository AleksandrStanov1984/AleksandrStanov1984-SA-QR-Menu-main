<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Support\Permissions;

class RestaurantHoursController extends Controller
{
    public function update(Request $request, Restaurant $restaurant)
    {
        Permissions::abortUnless(auth()->user(), 'restaurants.edit');

        $request->validate([
            'hours' => ['required', 'array'],
            'hours.*.open_time' => ['nullable', 'regex:/^(0[0-9]|1[0-9]|2[0-3]):(00|30)$/'],
            'hours.*.close_time' => ['nullable', 'regex:/^(0[0-9]|1[0-9]|2[0-3]):(00|30)$/'],
        ]);

        DB::transaction(function () use ($request, $restaurant) {

            foreach ($request->hours as $day => $row) {

                $isClosed = isset($row['is_closed']);

                RestaurantHour::updateOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'open_time' => $isClosed ? null : $row['open_time'],
                        'close_time' => $isClosed ? null : $row['close_time'],
                        'is_closed' => $isClosed,
                    ]
                );
            }
        });

        return back()->with('success', 'Hours updated');
    }
}
