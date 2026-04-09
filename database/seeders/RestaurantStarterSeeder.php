<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Models\Restaurant;
use App\Models\Section;
use App\Models\Item;
use App\Models\ItemTranslation;
use App\Models\SectionTranslation;

class RestaurantStarterSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $rid = 100;
            $hasDetails = Schema::hasColumn('item_translations', 'details');

            // =============================
            // CLEAN (ВАЖНО)
            // =============================
            ItemTranslation::whereHas('item.section', fn($q) => $q->where('restaurant_id', $rid))->delete();
            SectionTranslation::whereHas('section', fn($q) => $q->where('restaurant_id', $rid))->delete();

            Item::whereHas('section', fn($q) => $q->where('restaurant_id', $rid))->delete();
            Section::where('restaurant_id', $rid)->delete();

            // =============================
            // RESTAURANT
            // =============================
            $restaurant = Restaurant::updateOrCreate(
                ['id' => $rid],
                [
                    'name' => 'City Döner & Grill',
                    'slug' => 'city-doener-grill',
                    'plan_key' => 'starter',
                    'template_key' => 'united',
                    'is_active' => true,

                    'phone' => '+49 152 12345678',
                    'contact_name' => 'Ali Yilmaz',
                    'contact_email' => 'kontakt@city-doener.de',

                    'city' => 'Rottweil',
                    'street' => 'Hauptstraße',
                    'house_number' => '1',
                    'postal_code' => '78628',
                ]
            );

            // =============================
            // CATEGORIES
            // =============================
            $categories = [
                'doener'     => 'Döner & Kebap',
                'yufka'      => 'Yufka & Wraps',
                'teller'     => 'Tellergerichte',
                'salate'     => 'Salate',
                'pizza'      => 'Pizza',
                'pide'       => 'Pide',
                'snacks'     => 'Snacks & Falafel',
                'grill'      => 'Grill Spezialitäten',
                'getraenke'  => 'Getränke',
                'coffee'     => 'Kaffee',
                'tee'        => 'Tee',
            ];

            $sectionMap = [];
            $sectionSort = 1;

            foreach ($categories as $key => $title) {

                $section = Section::create([
                    'restaurant_id' => $rid,
                    'parent_id'     => null,
                    'key'           => $key,
                    'type'          => 'food',
                    'sort_order'    => $sectionSort++,
                    'is_active'     => true,
                ]);

                $section->translations()->create([
                    'locale' => 'de',
                    'title'  => $title,
                ]);

                $sectionMap[$key] = $section;
            }

            // =============================
            // HELPER (стабильный порядок)
            // =============================
            $create = function ($section, $name, $price, $meta = []) use ($hasDetails) {

                static $order = 1;

                $item = Item::create([
                    'section_id' => $section->id,
                    'price'      => $price,
                    'currency'   => 'EUR',
                    'image_path' => null, // ❗ без фейков
                    'meta'       => array_merge([
                        // Starter: без флагов
                        'is_new'       => false,
                        'dish_of_day'  => false,
                        'spicy_level'  => 0,
                    ], $meta),
                    'sort_order' => $order++,
                    'is_active'  => true,
                ]);

                $payload = [
                    'title'       => $name,
                    'description' => "{$name} – Frisch zubereitet.",
                ];

                if ($hasDetails) {
                    $payload['details'] = $payload['description'] . ' Schnell serviert.';
                }

                $item->translations()->create([
                        'locale' => 'de',
                    ] + $payload);
            };

            // =============================
            // DATA
            // =============================

            // Döner
            foreach ([
                         'Döner im Brot','Dürüm Döner','Döner Box','Döner Teller',
                         'Döner mit Käse','XXL Döner','Falafel Döner','Veggie Döner'
                     ] as $name) {
                $create($sectionMap['doener'], $name, rand(5,10));
            }

            // Yufka
            foreach ([
                         'Yufka Döner','Yufka Hähnchen','Yufka Falafel','Yufka Käse','Yufka Mix'
                     ] as $name) {
                $create($sectionMap['yufka'], $name, rand(6,11));
            }

            // Tellergerichte
            foreach ([
                         'Döner Teller','Hähnchen Teller','Falafel Teller','Köfte Teller',
                         'Cevapcici Teller','Mix Grill Teller','Vegetarischer Teller'
                     ] as $name) {
                $create($sectionMap['teller'], $name, rand(8,15));
            }

            // Salate
            foreach ([
                         'Gemischter Salat','Hähnchen Salat','Thunfisch Salat','Falafel Salat','Döner Salat'
                     ] as $name) {
                $create($sectionMap['salate'], $name, rand(5,9));
            }

            // Pizza
            foreach ([
                         'Pizza Margherita','Pizza Salami','Pizza Prosciutto',
                         'Pizza Funghi','Pizza Döner','Pizza Tonno'
                     ] as $name) {
                $create($sectionMap['pizza'], $name, rand(7,12));
            }

            // Pide
            foreach ([
                         'Pide mit Käse','Pide mit Hackfleisch','Pide mit Spinat',
                         'Pide mit Sucuk','Pide Mix'
                     ] as $name) {
                $create($sectionMap['pide'], $name, rand(7,13));
            }

            // Snacks
            foreach ([
                         'Falafel Teller','Falafel Box','Falafel im Brot',
                         'Chicken Nuggets','Pommes'
                     ] as $name) {
                $create($sectionMap['snacks'], $name, rand(4,8));
            }

            // Grill
            foreach ([
                         'Cevapcici','Köfte','Hähnchenspieß','Lammspieß','Mix Grill'
                     ] as $name) {
                $create($sectionMap['grill'], $name, rand(9,16));
            }

            // Getränke
            foreach ([
                         'Cola','Cola Zero','Fanta','Sprite',
                         'Wasser still','Wasser sprudel','Eistee','Apfelschorle','Ayran'
                     ] as $name) {
                $create($sectionMap['getraenke'], $name, rand(2,4));
            }

            // Kaffee
            foreach ([
                         'Espresso','Kaffee','Cappuccino','Latte Macchiato'
                     ] as $name) {
                $create($sectionMap['coffee'], $name, rand(2,5));
            }

            // Tee
            foreach ([
                         'Schwarzer Tee','Grüner Tee','Pfefferminztee','Früchtetee'
                     ] as $name) {
                $create($sectionMap['tee'], $name, rand(2,4));
            }

        });
    }
}
