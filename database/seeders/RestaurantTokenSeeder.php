<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Restaurant;
use App\Models\RestaurantToken;

class RestaurantTokenSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $restaurants = Restaurant::all();

            foreach ($restaurants as $restaurant) {

                // =============================
                // BASE TOKEN BY PLAN
                // =============================
                $base = match ($restaurant->plan_key) {
                    'starter' => 'starter-demo',
                    'basic'   => 'basic-demo',
                    'pro'     => 'pro-demo',
                    default   => 'restaurant',
                };

                // 👉 slug + id = всегда уникально и читаемо
                $token = $base . '-' . $restaurant->id;

                // =============================
                // EXTRA SAFETY (на случай будущих изменений)
                // =============================
                $original = $token;
                $i = 1;

                while (
                RestaurantToken::where('token', $token)
                    ->where('restaurant_id', '!=', $restaurant->id)
                    ->exists()
                ) {
                    $token = $original . '-' . $i++;
                }

                // =============================
                // UPSERT
                // =============================
                RestaurantToken::updateOrCreate(
                    ['restaurant_id' => $restaurant->id],
                    [
                        'token' => $token
                    ]
                );
            }

        });
    }
}
