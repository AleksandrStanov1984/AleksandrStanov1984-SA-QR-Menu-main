<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

use App\Models\Restaurant;
use App\Models\Section;
use App\Models\Item;
use App\Models\RestaurantSocialLink;

class Restaurant10Seeder extends Seeder
{
    public function run(): void
    {
        $rid = 10;

        // есть ли details в item_translations (миграции не трогаем!)
        $hasDetailsColumn = Schema::hasColumn('item_translations', 'details');

        // =============================
        // CLEAN OLD DATA (restaurant 10)
        // =============================
        Item::whereHas('section', fn($q) => $q->where('restaurant_id', $rid))->delete();
        Section::where('restaurant_id', $rid)->delete();

        // =============================
        // RESTAURANT
        // =============================
        $restaurant = Restaurant::updateOrCreate(
            ['id' => $rid],
            [
                'name'         => 'Panino & Pizza Test',
                'slug'         => 'panino-pizza-test',
                'is_active'    => true,
                'template_key' => 'united',

                'phone'        => '+49 000 123456',
                'city'         => 'Rottweil',
                'street'       => 'Teststrasse',
                'house_number' => '17',
                'postal_code'  => '78628',
            ]
        );

        // =============================
        // Категории
        // =============================
        $categories = [
            ['key' => 'salads',   'type' => 'food', 'title_de' => 'Salate',            'title_en' => 'Salads'],
            ['key' => 'cold',     'type' => 'food', 'title_de' => 'Kalte Vorspeisen',  'title_en' => 'Cold Starters'],
            ['key' => 'meat',     'type' => 'food', 'title_de' => 'Fleischgerichte',   'title_en' => 'Meat Dishes'],
            ['key' => 'fish',     'type' => 'food', 'title_de' => 'Fischgerichte',     'title_en' => 'Fish Dishes'],
            ['key' => 'desserts', 'type' => 'food', 'title_de' => 'Desserts',          'title_en' => 'Desserts'],
            ['key' => 'cocktails','type' => 'food', 'title_de' => 'Cocktails',         'title_en' => 'Cocktails'],
        ];

        $sectionMap = [];
        $sortOrder = 1;

        foreach ($categories as $cat) {
            $section = Section::create([
                'restaurant_id' => $rid,
                'parent_id'     => null,
                'key'           => $cat['key'],
                'type'          => $cat['type'],
                'sort_order'    => $sortOrder++,
                'is_active'     => true,
            ]);

            $sectionMap[$cat['key']] = $section;

            $section->translations()->updateOrCreate(['locale'=>'de'], ['title'=>$cat['title_de']]);
            $section->translations()->updateOrCreate(['locale'=>'en'], ['title'=>$cat['title_en']]);
        }

        // =============================
        // ITEMS: правила
        // - на категорию: 1 NEW (первый) + 1 DISH (второй)
        // - у всех: spicy_level 0..5
        // - у всех food: image_path (фейковый путь ок)
        // - у бара: image_path = null (тест "без картинок" + не кликается)
        // =============================

        $foodItems = [
            'salads' => [
                // NEW (1-й)
                [
                    'price' => 9.50,
                    'image_path' => 'resources/resources/assets/images/salads/greek.jpg',
                    'meta' => ['is_new'=>true, 'dish_of_day'=>false, 'spicy_level'=>0],
                    'de' => [
                        'title' => 'Griechischer Salat',
                        'description' => 'Tomaten, Gurken, Paprika, Oliven und Feta.',
                        'details' => 'Ein frischer, klassischer griechischer Salat: sonnengereifte Tomaten, knackige Gurken, Paprika und Oliven, dazu Feta. Abgerundet mit Olivenöl, Oregano und einem Hauch Zitrone. Perfekt als leichter Start oder Beilage.'
                    ],
                    'en' => [
                        'title' => 'Greek Salad',
                        'description' => 'Tomatoes, cucumbers, peppers, olives and feta.',
                        'details' => 'A fresh classic: ripe tomatoes, crunchy cucumbers, peppers and olives, finished with feta. Dressed with olive oil, oregano and a hint of lemon. Great as a light starter or side.'
                    ],
                ],
                // DISH OF DAY (2-й)
                [
                    'price' => 11.90,
                    'image_path' => 'resources/resources/assets/images/salads/caesar.jpg',
                    'meta' => ['is_new'=>false, 'dish_of_day'=>true, 'spicy_level'=>1],
                    'de' => [
                        'title' => 'Caesar Salat mit Hähnchen',
                        'description' => 'Römersalat mit Hähnchen, Parmesan und Croutons.',
                        'details' => 'Knackiger Römersalat mit saftig gegrilltem Hähnchen, frisch gehobeltem Parmesan und goldenen Croutons. Dazu ein cremiges Caesar-Dressing mit feiner Knoblauch-Note. Herzhaft, sättigend und perfekt als Hauptgericht.'
                    ],
                    'en' => [
                        'title' => 'Caesar Salad with Chicken',
                        'description' => 'Romaine lettuce with chicken, parmesan and croutons.',
                        'details' => 'Crisp romaine topped with juicy grilled chicken, shaved parmesan and golden croutons. Finished with a creamy Caesar dressing with a subtle garlic note. Hearty and satisfying.'
                    ],
                ],
                // остальные
                [
                    'price' => 10.90,
                    'image_path' => 'resources/resources/assets/images/salads/tuna.jpg',
                    'meta' => ['is_new'=>false, 'dish_of_day'=>false, 'spicy_level'=>2],
                    'de' => [
                        'title' => 'Thunfischsalat',
                        'description' => 'Gemischter Salat mit Thunfisch und Mais.',
                        'details' => 'Gemischte Blattsalate mit Thunfisch, Mais, Tomaten und Gurken. Angenehm würzig, mit leichter Schärfe. Ein unkomplizierter, proteinreicher Klassiker.'
                    ],
                    'en' => [
                        'title' => 'Tuna Salad',
                        'description' => 'Mixed salad with tuna and corn.',
                        'details' => 'Mixed greens with tuna, corn, tomatoes and cucumbers. Mildly spicy and protein-rich – a simple classic.'
                    ],
                ],
            ],

            'cold' => [
                // NEW
                [
                    'price' => 6.90,
                    'image_path' => 'resources/resources/assets/images/cold/bruschetta.jpg',
                    'meta' => ['is_new'=>true, 'dish_of_day'=>false, 'spicy_level'=>0],
                    'de' => [
                        'title' => 'Bruschetta',
                        'description' => 'Geröstetes Brot mit Tomaten und Basilikum.',
                        'details' => 'Geröstetes Brot mit würzigen Tomatenwürfeln, frischem Basilikum und bestem Olivenöl. Leicht knusprig, duftend und perfekt als Vorspeise.'
                    ],
                    'en' => [
                        'title' => 'Bruschetta',
                        'description' => 'Toasted bread with tomatoes and basil.',
                        'details' => 'Toasted bread topped with seasoned diced tomatoes, fresh basil and olive oil. Crisp, aromatic and perfect as a starter.'
                    ],
                ],
                // DISH
                [
                    'price' => 12.90,
                    'image_path' => 'resources/resources/assets/images/cold/carpaccio.jpg',
                    'meta' => ['is_new'=>false, 'dish_of_day'=>true, 'spicy_level'=>1],
                    'de' => [
                        'title' => 'Rinder-Carpaccio',
                        'description' => 'Dünn geschnittenes Rindfleisch mit Rucola und Parmesan.',
                        'details' => 'Sehr dünn geschnittenes Rindfleisch, mariniert mit Olivenöl und Zitrone, dazu Rucola und Parmesan. Elegant, leicht und aromatisch.'
                    ],
                    'en' => [
                        'title' => 'Beef Carpaccio',
                        'description' => 'Thinly sliced beef with arugula and parmesan.',
                        'details' => 'Paper-thin beef slices dressed with olive oil and lemon, served with arugula and parmesan. Light, elegant and aromatic.'
                    ],
                ],
            ],

            'meat' => [
                // NEW
                [
                    'price' => 12.90,
                    'image_path' => 'resources/resources/assets/images/meat/burger.jpg',
                    'meta' => ['is_new'=>true, 'dish_of_day'=>false, 'spicy_level'=>3],
                    'de' => [
                        'title' => 'Beef Burger',
                        'description' => 'Rindfleisch-Patty, Salat, Tomate, Sauce.',
                        'details' => 'Saftiges Rindfleisch-Patty im weichen Bun, knackiger Salat, Tomate, Zwiebel und eine würzige Haussauce. Leicht scharf – auf Wunsch extra scharf möglich.'
                    ],
                    'en' => [
                        'title' => 'Beef Burger',
                        'description' => 'Beef patty, salad, tomato, sauce.',
                        'details' => 'Juicy beef patty in a soft bun with crisp lettuce, tomato, onion and a tangy house sauce. Mildly spicy, can be made hotter.'
                    ],
                ],
                // DISH
                [
                    'price' => 19.90,
                    'image_path' => 'resources/resources/assets/images/meat/grill_mix.jpg',
                    'meta' => ['is_new'=>false, 'dish_of_day'=>true, 'spicy_level'=>2],
                    'de' => [
                        'title' => 'Grillteller',
                        'description' => 'Schwein, Hähnchen und Rind – gemischt.',
                        'details' => 'Gemischter Grillteller mit zartem Hähnchen, saftigem Schwein und aromatischem Rind. Serviert mit Beilage und einer würzigen Sauce. Perfekt, wenn man von allem etwas möchte.'
                    ],
                    'en' => [
                        'title' => 'Mixed Grill Plate',
                        'description' => 'Pork, chicken and beef selection.',
                        'details' => 'A mixed grill with tender chicken, juicy pork and flavorful beef. Served with a side and a savory sauce. Ideal if you want a bit of everything.'
                    ],
                ],
            ],

            'fish' => [
                // NEW
                [
                    'price' => 18.90,
                    'image_path' => 'resources/resources/assets/images/fish/grilled_salmon.jpg',
                    'meta' => ['is_new'=>true, 'dish_of_day'=>false, 'spicy_level'=>1],
                    'de' => [
                        'title' => 'Gegrillter Lachs',
                        'description' => 'Lachsfilet mit Zitronenbutter.',
                        'details' => 'Saftiges Lachsfilet vom Grill, abgeschmeckt mit Zitronenbutter und Kräutern. Leicht, aromatisch und perfekt, wenn man etwas Feines möchte.'
                    ],
                    'en' => [
                        'title' => 'Grilled Salmon',
                        'description' => 'Salmon fillet with lemon butter.',
                        'details' => 'Juicy grilled salmon finished with lemon butter and herbs. Light, aromatic and refined.'
                    ],
                ],
                // DISH
                [
                    'price' => 13.90,
                    'image_path' => 'resources/resources/assets/images/fish/calamari.jpg',
                    'meta' => ['is_new'=>false, 'dish_of_day'=>true, 'spicy_level'=>2],
                    'de' => [
                        'title' => 'Calamari',
                        'description' => 'Knusprig frittierte Calamari mit Aioli.',
                        'details' => 'Knusprig frittierte Calamari, serviert mit Aioli und Zitrone. Außen kross, innen zart. Leichte Schärfe durch Gewürze.'
                    ],
                    'en' => [
                        'title' => 'Calamari',
                        'description' => 'Crispy fried calamari with aioli.',
                        'details' => 'Crispy fried calamari served with aioli and lemon. Crunchy outside, tender inside. Mildly spicy seasoning.'
                    ],
                ],
            ],

            'desserts' => [
                // NEW
                [
                    'price' => 7.20,
                    'image_path' => 'resources/resources/assets/images/desserts/tiramisu.jpg',
                    'meta' => ['is_new'=>true, 'dish_of_day'=>false, 'spicy_level'=>0],
                    'de' => [
                        'title' => 'Tiramisu',
                        'description' => 'Mascarpone, Espresso, Kakao.',
                        'details' => 'Klassisches Tiramisu: luftige Mascarpone-Creme, in Espresso getränkte Löffelbiskuits und Kakao. Cremig, intensiv und einfach perfekt nach dem Essen.'
                    ],
                    'en' => [
                        'title' => 'Tiramisu',
                        'description' => 'Mascarpone, espresso, cocoa.',
                        'details' => 'Classic tiramisu: airy mascarpone cream, espresso-soaked ladyfingers and cocoa. Creamy and intense – a perfect finish.'
                    ],
                ],
                // DISH
                [
                    'price' => 6.90,
                    'image_path' => 'resources/resources/assets/images/desserts/cheesecake.jpg',
                    'meta' => ['is_new'=>false, 'dish_of_day'=>true, 'spicy_level'=>0],
                    'de' => [
                        'title' => 'New York Cheesecake',
                        'description' => 'Cremig mit Vanille und Beeren.',
                        'details' => 'Cremiger Cheesecake im New-York-Stil mit feiner Vanille-Note. Dazu fruchtige Beeren – weich, reichhaltig und sehr beliebt.'
                    ],
                    'en' => [
                        'title' => 'New York Cheesecake',
                        'description' => 'Creamy with vanilla and berries.',
                        'details' => 'Rich New York-style cheesecake with a delicate vanilla note, served with berries. Smooth, indulgent and very popular.'
                    ],
                ],
            ],

            'cocktails' => [
                // NEW
                [
                    'price' => 7.90,
                    'image_path' => 'resources/resources/assets/images/cocktails/aperol_spritz.jpg',
                    'meta' => ['is_new'=>true, 'dish_of_day'=>false, 'spicy_level'=>0],
                    'de' => [
                        'title' => 'Aperol Spritz',
                        'description' => 'Aperol, Prosecco, Soda – erfrischend.',
                        'details' => 'Ein leichter Aperitif aus Aperol, Prosecco und Soda. Fruchtig-bitter, sehr erfrischend – perfekt für den Start in den Abend.'
                    ],
                    'en' => [
                        'title' => 'Aperol Spritz',
                        'description' => 'Aperol, prosecco, soda – refreshing.',
                        'details' => 'A light aperitif with Aperol, prosecco and soda. Fruity-bitter and refreshing – perfect to start the evening.'
                    ],
                ],
                // DISH (как “drink of day”, чтобы тестить)
                [
                    'price' => 8.50,
                    'image_path' => 'resources/resources/assets/images/cocktails/mojito.jpg',
                    'meta' => ['is_new'=>false, 'dish_of_day'=>true, 'spicy_level'=>0],
                    'de' => [
                        'title' => 'Mojito',
                        'description' => 'Minze, Limette, Rum – frisch.',
                        'details' => 'Erfrischender Mojito mit Minze und Limette, leicht süß und sehr kühl serviert. Ein Klassiker, der immer funktioniert.'
                    ],
                    'en' => [
                        'title' => 'Mojito',
                        'description' => 'Mint, lime, rum – fresh.',
                        'details' => 'A refreshing mojito with mint and lime, lightly sweet and served chilled. A classic that always works.'
                    ],
                ],
            ],
        ];

        // Создаём items по категориям
        foreach ($foodItems as $catKey => $items) {
            $section = $sectionMap[$catKey] ?? null;
            if (!$section) continue;

            $itemSort = 1;

            $dishAssigned = false;
            $spicyLevel = 1;

            foreach ($items as $row) {

                $meta = $row['meta'] ?? [];

                // 🌶 разная перченость
                $meta['spicy_level'] = $spicyLevel;

                $spicyLevel++;
                if ($spicyLevel > 5) {
                    $spicyLevel = 1;
                }

                // ⭐ только одно блюдо дня на категорию
                if (!$dishAssigned) {
                    $meta['dish_of_day'] = true;
                    $dishAssigned = true;
                } else {
                    $meta['dish_of_day'] = false;
                }

                // NEW можно случайно
                if (!isset($meta['is_new'])) {
                    $meta['is_new'] = rand(0,1);
                }

                $item = Item::create([
                    'section_id'  => $section->id,
                    'price'       => $row['price'] ?? null,
                    'currency'    => 'EUR',
                    'image_path'  => $row['image_path'] ?? null, // фейковый путь ок
                    'meta'        => $row['meta'] ?? [],
                    'sort_order'  => $itemSort++,
                    'is_active'   => true,
                ]);

                // DE
                $payloadDe = [
                    'title'       => $row['de']['title'] ?? 'Item',
                    'description' => $row['de']['description'] ?? '',
                ];
                if ($hasDetailsColumn) {
                    $payloadDe['details'] = $row['de']['details'] ?? $payloadDe['description'];
                }
                $item->translations()->updateOrCreate(['locale' => 'de'], $payloadDe);

                // EN
                $payloadEn = [
                    'title'       => $row['en']['title'] ?? ($row['de']['title'] ?? 'Item'),
                    'description' => $row['en']['description'] ?? ($row['de']['description'] ?? ''),
                ];
                if ($hasDetailsColumn) {
                    $payloadEn['details'] = $row['en']['details'] ?? ($row['de']['details'] ?? $payloadEn['description']);
                }
                $item->translations()->updateOrCreate(['locale' => 'en'], $payloadEn);
            }
        }

        // =============================
        // BAR: без картинок (и без клика)
        // =============================
        $bar = Section::create([
            'restaurant_id' => $rid,
            'parent_id'     => null,
            'key'           => 'bar',
            'type'          => 'bar',
            'sort_order'    => 999,
            'is_active'     => true,
        ]);

        $bar->translations()->updateOrCreate(['locale'=>'de'], ['title'=>'Bar & Getränke']);
        $bar->translations()->updateOrCreate(['locale'=>'en'], ['title'=>'Bar & Drinks']);

        $barGroups = [
            ['key'=>'softdrinks','de'=>'Softdrinks','en'=>'Soft Drinks', 'items'=>[
                ['de'=>'Coca-Cola 0,33 l', 'en'=>'Coca-Cola 0,33 l', 'price'=>3.20],
                ['de'=>'Fanta 0,33 l',     'en'=>'Fanta 0,33 l',     'price'=>3.20],
                ['de'=>'Sprite 0,33 l',    'en'=>'Sprite 0,33 l',    'price'=>3.20],
            ]],
            ['key'=>'beer','de'=>'Bier','en'=>'Beer', 'items'=>[
                ['de'=>'Helles Lager 0,5 l', 'en'=>'Lager 0,5 l', 'price'=>4.50],
                ['de'=>'Weißbier 0,5 l',     'en'=>'Wheat Beer 0,5 l', 'price'=>4.80],
            ]],
        ];

        $subSort = 1;
        foreach ($barGroups as $g) {
            $sub = Section::create([
                'restaurant_id' => $rid,
                'parent_id'     => $bar->id,
                'key'           => 'bar.'.$g['key'],
                'type'          => 'bar',
                'sort_order'    => $subSort++,
                'is_active'     => true,
            ]);

            $sub->translations()->updateOrCreate(['locale'=>'de'], ['title'=>$g['de']]);
            $sub->translations()->updateOrCreate(['locale'=>'en'], ['title'=>$g['en']]);

            $iSort = 1;
            foreach ($g['items'] as $row) {
                $item = Item::create([
                    'section_id' => $sub->id,
                    'price'      => $row['price'],
                    'currency'   => 'EUR',
                    'image_path' => null, // НЕТ картинок => нет модалки (по item-card)
                    'meta'       => ['spicy_level'=>0, 'is_new'=>false, 'dish_of_day'=>false],
                    'sort_order' => $iSort++,
                    'is_active'  => true,
                ]);

                $payloadDe = [
                    'title' => $row['de'],
                    'description' => 'Getränk, gekühlt serviert.',
                ];
                if ($hasDetailsColumn) $payloadDe['details'] = $payloadDe['description'];
                $item->translations()->updateOrCreate(['locale'=>'de'], $payloadDe);

                $payloadEn = [
                    'title' => $row['en'],
                    'description' => 'Drink, served chilled.',
                ];
                if ($hasDetailsColumn) $payloadEn['details'] = $payloadEn['description'];
                $item->translations()->updateOrCreate(['locale'=>'en'], $payloadEn);
            }
        }

        // =============================
        // FOOTER LINKS
        // =============================
        RestaurantSocialLink::updateOrCreate(
            ['restaurant_id' => $rid, 'sort_order' => 1],
            ['title'=>'Instagram','url'=>'https://instagram.com/test','icon_path'=>null,'is_active'=>true,'deleted_by_user_id'=>null]
        );

        RestaurantSocialLink::updateOrCreate(
            ['restaurant_id' => $rid, 'sort_order' => 2],
            ['title'=>'WhatsApp','url'=>'https://wa.me/49123456789','icon_path'=>null,'is_active'=>true,'deleted_by_user_id'=>null]
        );
    }
}
