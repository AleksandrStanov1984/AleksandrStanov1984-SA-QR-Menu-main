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

class RestaurantProSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $rid = 200;
            $hasDetails = Schema::hasColumn('item_translations', 'details');

            // =============================
            // CLEAN OLD DATA (ВАЖНО)
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
                    'name' => 'Grand Restaurant Premium',
                    'slug' => 'grand-restaurant-premium',
                    'plan_key' => 'pro',
                    'template_key' => 'united',
                    'is_active' => true,

                    'phone' => '+49 711 12345678',
                    'contact_name' => 'Max Mustermann',
                    'contact_email' => 'info@grand-restaurant.de',

                    'city' => 'Stuttgart',
                    'street' => 'Hauptstraße',
                    'house_number' => '1',
                    'postal_code' => '70173',
                ]
            );

            // =============================
            // CONFIG
            // =============================
            $config = [
                'kalte' => ['Kalte Vorspeisen', 8],
                'warme' => ['Warme Vorspeisen', 7],
                'salate' => ['Salate', 12],
                'fleisch' => ['Fleischgerichte', 10],
                'fisch' => ['Fischgerichte', 10],
                'huhn' => ['Geflügelgerichte', 12],
                'seafood' => ['Meeresfrüchte', 8],
                'grill' => ['Grill & Steak', 8],
                'pizza' => ['Pizza', 15],
                'dessert' => ['Desserts', 12],
                'beilagen' => ['Beilagen', 6],
                'cocktails' => ['Cocktails', 20],
                'bar' => ['Bar & Getränke', 0],
            ];

            $sectionMap = [];
            $sectionSort = 1;

            foreach ($config as $key => [$title, $count]) {

                $section = Section::create([
                    'restaurant_id' => $rid,
                    'parent_id' => null,
                    'key' => $key,
                    'type' => $key === 'bar' ? 'bar' : 'food',
                    'sort_order' => $sectionSort++,
                    'is_active' => true,
                ]);

                $section->translations()->create([
                    'locale' => 'de',
                    'title' => $title,
                ]);

                $sectionMap[$key] = [$section, $count];
            }

            // =============================
            // HELPER
            // =============================
            $create = function ($section, $name, $price, $meta = []) use ($hasDetails) {

                static $order = 1;

                $item = Item::create([
                    'section_id' => $section->id,
                    'price' => $price,
                    'currency' => 'EUR',
                    'image_path' => null,
                    'meta' => array_merge([
                        'is_new' => rand(0,1),
                        'dish_of_day' => rand(0,10) === 1,
                        'bestseller' => rand(0,5) === 1,
                        'spicy_level' => rand(0,3),
                    ], $meta),
                    'sort_order' => $order++,
                    'is_active' => true,
                ]);

                $payload = [
                    'title' => $name,
                    'description' => "{$name} – Premium Gericht mit hochwertigen Zutaten.",
                ];

                if ($hasDetails) {
                    $payload['details'] = $payload['description'] . ' Fein abgestimmt vom Küchenchef.';
                }

                $item->translations()->create([
                        'locale' => 'de',
                    ] + $payload);
            };

            // =============================
            // FOOD
            // =============================
            foreach ($sectionMap as $key => [$section, $count]) {

                if ($key === 'bar') continue;

                for ($i = 1; $i <= $count; $i++) {
                    $create($section, $section->key . " Spezial $i", rand(8,32));
                }
            }

            // =============================
            // BAR
            // =============================
            $bar = $sectionMap['bar'][0];

            $barSort = 1;

            // COFFEE
            $coffeeList = [
                'Espresso','Doppio Espresso','Americano','Cappuccino',
                'Latte Macchiato','Flat White','Caffè Latte','Mocha',
                'Macchiato','Iced Coffee','Cold Brew'
            ];

            $coffee = Section::create([
                'restaurant_id'=>$rid,
                'parent_id'=>$bar->id,
                'key'=>'bar.kaffee',
                'type'=>'bar',
                'sort_order'=>$barSort++,
                'is_active'=>true,
            ]);

            $coffee->translations()->create([
                'locale'=>'de',
                'title'=>'Kaffee & Heißgetränke'
            ]);

            foreach ($coffeeList as $i => $name) {
                Item::create([
                    'section_id'=>$coffee->id,
                    'price'=>rand(2,6),
                    'currency'=>'EUR',
                    'image_path'=>null,
                    'meta'=>[],
                    'sort_order'=>$i+1,
                    'is_active'=>true,
                ])->translations()->create([
                    'locale'=>'de',
                    'title'=>$name,
                    'description'=>'Frisch zubereitet.',
                ]);
            }

            // WINE
            $wineGroups = [
                'Weißwein'=>['Chardonnay','Riesling','Sauvignon Blanc','Pinot Grigio'],
                'Rotwein'=>['Merlot','Cabernet Sauvignon','Chianti','Barolo'],
                'Rosé'=>['Rosé trocken','Rosé halbtrocken','Rosé Frankreich'],
                'Prosecco'=>['Prosecco','Sekt','Champagner'],
            ];

            foreach ($wineGroups as $title => $list) {

                $sub = Section::create([
                    'restaurant_id'=>$rid,
                    'parent_id'=>$bar->id,
                    'key'=>"bar.".Str::slug($title),
                    'type'=>'bar',
                    'sort_order'=>$barSort++,
                    'is_active'=>true,
                ]);

                $sub->translations()->create([
                    'locale'=>'de',
                    'title'=>$title
                ]);

                foreach ($list as $i => $name) {
                    Item::create([
                        'section_id'=>$sub->id,
                        'price'=>rand(6,25),
                        'currency'=>'EUR',
                        'image_path'=>null,
                        'meta'=>[],
                        'sort_order'=>$i+1,
                        'is_active'=>true,
                    ])->translations()->create([
                        'locale'=>'de',
                        'title'=>$name,
                        'description'=>'Ausgewählter Premium Wein.',
                    ]);
                }
            }

            // OTHER BAR
            foreach (['Softdrinks','Bier','Spirituosen'] as $title) {

                $sub = Section::create([
                    'restaurant_id'=>$rid,
                    'parent_id'=>$bar->id,
                    'key'=>"bar.".Str::slug($title),
                    'type'=>'bar',
                    'sort_order'=>$barSort++,
                    'is_active'=>true,
                ]);

                $sub->translations()->create([
                    'locale'=>'de',
                    'title'=>$title
                ]);

                for ($i=1;$i<=6;$i++){
                    Item::create([
                        'section_id'=>$sub->id,
                        'price'=>rand(3,15),
                        'currency'=>'EUR',
                        'image_path'=>null,
                        'meta'=>[],
                        'sort_order'=>$i,
                        'is_active'=>true,
                    ])->translations()->create([
                        'locale'=>'de',
                        'title'=>"$title $i",
                        'description'=>'Getränk.',
                    ]);
                }
            }

        });
    }
}
