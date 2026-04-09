<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Restaurant;
use App\Models\RestaurantSocialLink;

class RestaurantSocialSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $restaurants = Restaurant::all();

            foreach ($restaurants as $restaurant) {

                // =============================
                // LIMIT BY PLAN
                // =============================
                $limit = match ($restaurant->plan_key) {
                    'starter' => 1,
                    'basic'   => 3,
                    'pro'     => 5,
                    default   => 1,
                };

                // =============================
                // CLEAN OLD
                // =============================
                RestaurantSocialLink::where('restaurant_id', $restaurant->id)->delete();

                // =============================
                // AVAILABLE SOCIALS
                // =============================
                $socials = [
                    ['instagram', 'https://instagram.com/demo'],
                    ['facebook', 'https://facebook.com/demo'],
                    ['tiktok', 'https://tiktok.com/@demo'],
                    ['whatsapp', 'https://wa.me/49123456789'],
                    ['website', 'https://example.com'],
                ];

                // =============================
                // APPLY LIMIT
                // =============================
                $selected = array_slice($socials, 0, $limit);

                // =============================
                // CREATE
                // =============================
                foreach ($selected as $index => [$title, $url]) {

                    RestaurantSocialLink::create([
                        'restaurant_id' => $restaurant->id,

                        // ✅ важно: lowercase → совпадает с fallback icons
                        'title' => $title,

                        'url' => $url,

                        // null → ImageService fallback → system/icons/*.svg
                        'icon_path' => null,

                        'sort_order' => $index + 1,

                        'is_active' => true,

                        'deleted_by_user_id' => null,
                    ]);
                }
            }

        });
    }
}
