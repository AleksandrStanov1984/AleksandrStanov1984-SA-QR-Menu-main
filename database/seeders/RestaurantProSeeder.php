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
            // CLEAN OLD DATA
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
                'kalte'     => ['Kalte Vorspeisen', 8],
                'warme'     => ['Warme Vorspeisen', 7],
                'salate'    => ['Salate', 12],
                'fleisch'   => ['Fleischgerichte', 10],
                'fisch'     => ['Fischgerichte', 10],
                'huhn'      => ['Geflügelgerichte', 12],
                'seafood'   => ['Meeresfrüchte', 8],
                'grill'     => ['Grill & Steak', 8],
                'pizza'     => ['Pizza', 15],
                'dessert'   => ['Desserts', 12],
                'beilagen'  => ['Beilagen', 6],
                'cocktails' => ['Cocktails', 20],
                'bar'       => ['Bar & Getränke', 0],
            ];

            $sectionMap = [];
            $sectionSort = 1;

            foreach ($config as $key => [$title, $count]) {

                $section = Section::create([
                    'restaurant_id' => $rid,
                    'parent_id' => null,

                    'key' => $key,

                    'type' => $key === 'bar'
                        ? 'bar'
                        : 'food',

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
            $create = function (
                $section,
                $name,
                $price,
                $meta = []
            ) use ($hasDetails) {

                static $order = 1;

                $item = Item::create([
                    'section_id' => $section->id,

                    'key' => $section->key . '.' . Str::slug($name),

                    'price' => $price,
                    'currency' => 'EUR',

                    'image_path' => null,

                    'meta' => array_merge([
                        'is_new' => rand(0,1),
                        'dish_of_day' => rand(0,10) === 1,
                        'bestseller' => rand(0,5) === 1,
                        'spicy' => rand(0,3),
                        'show_image' => true,
                    ], $meta),

                    'sort_order' => $order++,
                    'is_active' => true,
                ]);

                $payload = [
                    'title' => $name,
                    'description' =>
                        "{$name} – Frisch zubereitet mit hochwertigen Zutaten.",
                ];

                if ($hasDetails) {

                    $payload['details'] =
                        $payload['description']
                        . ' Fein abgestimmt vom Küchenchef.';
                }

                $item->translations()->create([
                        'locale' => 'de',
                    ] + $payload);
            };

            // =============================
            // FOOD DATA
            // =============================
            $foodData = [

                'kalte' => [
                    'Carpaccio vom Rind',
                    'Antipasti Teller',
                    'Vitello Tonnato',
                    'Lachstatar',
                    'Bruschetta Classica',
                    'Mozzarella Caprese',
                    'Avocado Tatar',
                    'Garnelen Cocktail',
                ],

                'warme' => [
                    'Gebackener Camembert',
                    'Knoblauch Garnelen',
                    'Überbackene Champignons',
                    'Calamari Fritti',
                    'Gefüllte Aubergine',
                    'Mini Frühlingsrollen',
                    'Ofenkartoffel mit Kräuterquark',
                ],

                'salate' => [
                    'Caesar Salad',
                    'Griechischer Salat',
                    'Salat mit Ziegenkäse',
                    'Rucola Parmesan Salat',
                    'Thunfisch Salat',
                    'Hähnchen Salat',
                    'Mediterraner Salat',
                    'Avocado Mango Salat',
                    'Quinoa Salat',
                    'Salat mit Garnelen',
                    'Tomaten Mozzarella Salat',
                    'Fitness Salat',
                ],

                'fleisch' => [
                    'Rinderfilet Steak',
                    'Wiener Schnitzel',
                    'Schweinemedaillons',
                    'Lammkoteletts',
                    'Rinderroulade',
                    'BBQ Spareribs',
                    'Kalbssteak',
                    'Rindergulasch',
                    'Rumpsteak',
                    'Hacksteak mit Pfeffersauce',
                ],

                'fisch' => [
                    'Gegrillter Lachs',
                    'Dorade Royal',
                    'Zanderfilet',
                    'Forelle Müllerin',
                    'Seeteufel Medaillons',
                    'Thunfisch Steak',
                    'Gegrillte Garnelen',
                    'Kabeljaufilet',
                    'Fischplatte Deluxe',
                    'Lachsfilet mit Zitronensauce',
                ],

                'huhn' => [
                    'Hähnchenbrust vom Grill',
                    'Chicken Curry',
                    'Hähnchen Teriyaki',
                    'Knuspriges Hähnchen',
                    'Chicken Wings',
                    'Hähnchen mit Gemüse',
                    'Hähnchen Spieße',
                    'Chicken Alfredo',
                    'Gefüllte Hähnchenbrust',
                    'Chicken Burger Deluxe',
                    'Hähnchen in Rahmsauce',
                    'Gegrilltes Maishähnchen',
                ],

                'seafood' => [
                    'Meeresfrüchte Platte',
                    'Gegrillte Jakobsmuscheln',
                    'Black Tiger Garnelen',
                    'Calamari vom Grill',
                    'Miesmuscheln in Weißwein',
                    'Oktopus vom Grill',
                    'Scampi in Knoblauchöl',
                    'Seafood Pasta',
                ],

                'grill' => [
                    'Ribeye Steak',
                    'T Bone Steak',
                    'Tomahawk Steak',
                    'Mixed Grill Teller',
                    'BBQ Burger',
                    'Dry Aged Steak',
                    'Argentinisches Steak',
                    'Grillplatte Spezial',
                ],

                'pizza' => [
                    'Pizza Margherita',
                    'Pizza Salami',
                    'Pizza Prosciutto',
                    'Pizza Hawaii',
                    'Pizza Tonno',
                    'Pizza Quattro Formaggi',
                    'Pizza Diavola',
                    'Pizza Frutti di Mare',
                    'Pizza Vegetaria',
                    'Pizza Carbonara',
                    'Pizza Parma',
                    'Pizza Funghi',
                    'Pizza Capricciosa',
                    'Pizza Calzone',
                    'Pizza Napoli',
                ],

                'dessert' => [
                    'Tiramisu',
                    'Panna Cotta',
                    'Crème Brûlée',
                    'Schokoladen Soufflé',
                    'Cheesecake',
                    'Apfelstrudel',
                    'Vanille Eisbecher',
                    'Mousse au Chocolat',
                    'Waffeln mit Früchten',
                    'Hausgemachtes Sorbet',
                    'Brownie mit Eis',
                    'Dessert Variation',
                ],

                'beilagen' => [
                    'Pommes Frites',
                    'Bratkartoffeln',
                    'Gegrilltes Gemüse',
                    'Reis mit Kräutern',
                    'Kartoffelgratin',
                    'Knoblauchbrot',
                ],

                'cocktails' => [
                    'Mojito',
                    'Caipirinha',
                    'Piña Colada',
                    'Tequila Sunrise',
                    'Sex on the Beach',
                    'Cuba Libre',
                    'Mai Tai',
                    'Long Island Iced Tea',
                    'Aperol Spritz',
                    'Negroni',
                    'Whiskey Sour',
                    'Margarita',
                    'Espresso Martini',
                    'Cosmopolitan',
                    'Gin Tonic',
                    'Blue Lagoon',
                    'Zombie',
                    'Moscow Mule',
                    'Bahama Mama',
                    'Bloody Mary',
                ],
            ];

            // =============================
            // FOOD
            // =============================
            foreach ($sectionMap as $key => [$section, $count]) {

                if ($key === 'bar') {
                    continue;
                }

                if (!isset($foodData[$key])) {
                    continue;
                }

                foreach ($foodData[$key] as $name) {

                    $create(
                        $section,
                        $name,
                        rand(8,32),
                        [
                            'show_image' => true,
                        ]
                    );
                }
            }

            // =============================
            // BAR
            // =============================
            $bar = $sectionMap['bar'][0];

            $barSort = 1;

            // =============================
            // COFFEE
            // =============================
            $coffeeList = [
                'Espresso',
                'Doppio Espresso',
                'Americano',
                'Cappuccino',
                'Latte Macchiato',
                'Flat White',
                'Caffè Latte',
                'Mocha',
                'Macchiato',
                'Iced Coffee',
                'Cold Brew'
            ];

            $coffee = Section::create([
                'restaurant_id' => $rid,
                'parent_id' => $bar->id,

                'key' => 'bar.kaffee',

                'type' => 'bar',
                'sort_order' => $barSort++,

                'is_active' => true,
            ]);

            $coffee->translations()->create([
                'locale' => 'de',
                'title' => 'Kaffee & Heißgetränke'
            ]);

            foreach ($coffeeList as $i => $name) {

                Item::create([
                    'section_id' => $coffee->id,

                    'key' => 'bar.kaffee.' . Str::slug($name),

                    'price' => rand(2,6),
                    'currency' => 'EUR',

                    'image_path' => null,

                    'meta' => [
                        'show_image' => false,
                    ],

                    'sort_order' => $i + 1,
                    'is_active' => true,

                ])->translations()->create([
                    'locale' => 'de',
                    'title' => $name,
                    'description' => 'Frisch zubereitet.',
                ]);
            }

            // =============================
            // WINE
            // =============================
            $wineGroups = [

                'Weißwein' => [
                    'Chardonnay',
                    'Riesling',
                    'Sauvignon Blanc',
                    'Pinot Grigio'
                ],

                'Rotwein' => [
                    'Merlot',
                    'Cabernet Sauvignon',
                    'Chianti',
                    'Barolo'
                ],

                'Rosé' => [
                    'Rosé trocken',
                    'Rosé halbtrocken',
                    'Rosé Frankreich'
                ],

                'Prosecco' => [
                    'Prosecco',
                    'Sekt',
                    'Champagner'
                ],
            ];

            foreach ($wineGroups as $title => $list) {

                $sub = Section::create([
                    'restaurant_id' => $rid,
                    'parent_id' => $bar->id,

                    'key' => "bar." . Str::slug($title),

                    'type' => 'bar',
                    'sort_order' => $barSort++,

                    'is_active' => true,
                ]);

                $sub->translations()->create([
                    'locale' => 'de',
                    'title' => $title
                ]);

                foreach ($list as $i => $name) {

                    Item::create([
                        'section_id' => $sub->id,

                        'key' => $sub->key . '.' . Str::slug($name),

                        'price' => rand(6,25),
                        'currency' => 'EUR',

                        'image_path' => null,

                        'meta' => [
                            'show_image' => false,
                        ],

                        'sort_order' => $i + 1,
                        'is_active' => true,

                    ])->translations()->create([
                        'locale' => 'de',
                        'title' => $name,
                        'description' => 'Ausgewählter Premium Wein.',
                    ]);
                }
            }

            // =============================
            // OTHER BAR
            // =============================
            $barItems = [

                'Softdrinks' => [
                    'Coca Cola',
                    'Fanta',
                    'Sprite',
                    'Mineralwasser',
                    'Apfelschorle',
                    'Eistee'
                ],

                'Bier' => [
                    'Pils',
                    'Weizenbier',
                    'Dunkelbier',
                    'Helles',
                    'Craft Beer',
                    'Alkoholfreies Bier'
                ],

                'Spirituosen' => [
                    'Vodka',
                    'Whisky',
                    'Rum',
                    'Gin',
                    'Tequila',
                    'Cognac'
                ],
            ];

            foreach ($barItems as $title => $list) {

                $sub = Section::create([
                    'restaurant_id' => $rid,
                    'parent_id' => $bar->id,

                    'key' => "bar." . Str::slug($title),

                    'type' => 'bar',
                    'sort_order' => $barSort++,

                    'is_active' => true,
                ]);

                $sub->translations()->create([
                    'locale' => 'de',
                    'title' => $title
                ]);

                foreach ($list as $i => $name) {

                    Item::create([
                        'section_id' => $sub->id,

                        'key' => $sub->key . '.' . Str::slug($name),

                        'price' => rand(3,15),
                        'currency' => 'EUR',

                        'image_path' => null,

                        'meta' => [
                            'show_image' => false,
                        ],

                        'sort_order' => $i + 1,
                        'is_active' => true,

                    ])->translations()->create([
                        'locale' => 'de',
                        'title' => $name,
                        'description' => 'Getränk.',
                    ]);
                }
            }

        });
    }
}
