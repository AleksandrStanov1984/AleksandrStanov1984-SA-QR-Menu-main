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

class RestaurantBasicSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            $rid = 300;
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
                    'name' => 'Trattoria Bella Italia',
                    'slug' => 'trattoria-bella-italia',
                    'plan_key' => 'basic',
                    'template_key' => 'united',
                    'is_active' => true,

                    'phone' => '+49 741 9876543',
                    'contact_name' => 'Marco Rossi',
                    'contact_email' => 'info@trattoria-italia.de',

                    'city' => 'Villingen-Schwenningen',
                    'street' => 'Hauptstraße',
                    'house_number' => '1',
                    'postal_code' => '78054',
                ]
            );

            // =============================
            // CONFIG
            // =============================
            $config = [
                'kalte' => ['Kalte Vorspeisen', 8],
                'warme' => ['Warme Vorspeisen', 7],
                'salate' => ['Salate', 10],
                'fleisch' => ['Fleischgerichte', 9],
                'fisch' => ['Fischgerichte', 8],
                'huhn' => ['Geflügelgerichte', 9],
                'seafood' => ['Meeresfrüchte', 7],
                'pizza' => ['Pizza', 20],
                'dessert' => ['Desserts', 15],
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
                        'bestseller' => rand(0,5) === 1, // ✅ BASIC использует bestseller
                        'spicy_level' => rand(0,2),
                    ], $meta),
                    'sort_order' => $order++,
                    'is_active' => true,
                ]);

                $payload = [
                    'title' => $name,
                    'description' => "{$name} – Frisch zubereitet nach italienischer Art.",
                ];

                if ($hasDetails) {
                    $payload['details'] = $payload['description'] . ' Mit hochwertigen Zutaten und traditionellem Rezept.';
                }

                $item->translations()->create([
                        'locale' => 'de',
                    ] + $payload);
            };

            // =============================
            // DATA
            // =============================
            $data = [
                'kalte' => ['Bruschetta','Caprese','Carpaccio','Vitello Tonnato','Antipasti Misto','Bresaola','Mozzarella','Olivenplatte'],
                'warme' => ['Arancini','Gebackene Auberginen','Gnocchi Fritti','Frittierte Calamari','Polenta','Lasagne Mini','Zucchini Fritti'],
                'salate' => ['Caesar','Insalata Mista','Rucola','Thunfischsalat','Hähnchensalat','Caprese Deluxe','Pasta Salat','Quinoa Salat','Garnelen Salat','Avocado Salat'],
                'fleisch' => ['Rindersteak','Saltimbocca','Ragù','Osso Buco','Schweinefilet','Kalbsschnitzel','Rinderbraten','Lammkoteletts','Beef Tagliata'],
                'fisch' => ['Lachs','Dorade','Zander','Thunfisch Steak','Calamari','Garnelen','Fischplatte','Seeteufel'],
                'huhn' => ['Hähnchenbrust','Pollo al Limone','Chicken Parmesan','Hähnchen Pasta','Grillhähnchen','Chicken Risotto','Pollo Piccata','Hähnchenspieß','Chicken Alfredo'],
                'seafood' => ['Garnelen','Muscheln','Oktopus','Meeresfrüchte Pasta','Scampi','Calamari','Seafood Mix'],
                'beilagen' => ['Pommes','Kartoffelpüree','Reis','Gemüse','Ofenkartoffeln','Spinat'],
            ];

            foreach ($data as $key => $list) {
                foreach ($list as $name) {
                    $create($sectionMap[$key][0], $name, rand(6,22));
                }
            }

            // =============================
            // PIZZA
            // =============================
            $pizzaNames = [
                'Margherita','Salami','Prosciutto','Funghi','Hawaii',
                'Diavolo','Quattro Formaggi','Tonno','Napoli','Capricciosa',
                'Vegetaria','Calzone','Parma','Spinaci','Siciliana',
                'Frutti di Mare','BBQ Chicken','Carbonara','Romana','Milano'
            ];

            foreach ($pizzaNames as $name) {
                $create($sectionMap['pizza'][0], "Pizza $name", rand(9,16));
            }

            // =============================
            // DESSERTS
            // =============================
            $desserts = [
                'Tiramisu','Panna Cotta','Cheesecake','Gelato','Cannoli',
                'Affogato','Mousse','Crème Brûlée','Brownie','Apfelstrudel',
                'Ricotta Kuchen','Eisbecher','Schokoladenkuchen','Profiteroles','Zabaione'
            ];

            foreach ($desserts as $d) {
                $create($sectionMap['dessert'][0], $d, rand(5,9));
            }

            // =============================
            // COCKTAILS
            // =============================
            $cocktails = [
                'Mojito','Caipirinha','Piña Colada','Tequila Sunrise','Aperol Spritz',
                'Negroni','Martini','Whiskey Sour','Gin Tonic','Bloody Mary',
                'Cosmopolitan','Long Island','Daiquiri','Mai Tai','Margarita',
                'Espresso Martini','Sex on the Beach','Hugo','Spritz','Rum Punch'
            ];

            foreach ($cocktails as $c) {
                $create($sectionMap['cocktails'][0], $c, rand(7,14));
            }

            // =============================
            // BAR
            // =============================
            $bar = $sectionMap['bar'][0];
            $barSort = 1;

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

                for ($i=1;$i<=5;$i++){
                    Item::create([
                        'section_id'=>$sub->id,
                        'price'=>rand(3,10),
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
