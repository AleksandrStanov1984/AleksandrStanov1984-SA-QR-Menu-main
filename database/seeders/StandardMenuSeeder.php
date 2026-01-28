<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;

use App\Models\Restaurant;
use App\Models\RestaurantToken;
use App\Models\Section;
use App\Models\SectionTranslation;
use App\Models\Item;
use App\Models\ItemTranslation;

class StandardMenuSeeder extends Seeder
{
    public function run(): void
    {
        $zipPath = base_path('database/seeders/seed_assets/SA_QRMenu_Standart.zip');
        if (!file_exists($zipPath)) {
            throw new \RuntimeException("ZIP not found: {$zipPath}");
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException("Cannot open ZIP: {$zipPath}");
        }

        // Helper to read json from zip
        $readJson = function(string $path) use ($zip) {
            $idx = $zip->locateName($path);
            if ($idx === false) return null;
            $raw = $zip->getFromIndex($idx);
            if ($raw === false) return null;
            return json_decode($raw, true);
        };

        // Zip root folder name
        $root = 'SA_QRMenu_Standart';

        $categoriesData = $readJson("{$root}/data/categories/categories.json");
        if (!$categoriesData || empty($categoriesData['categories'])) {
            throw new \RuntimeException("categories.json not found or invalid in ZIP");
        }

        $deCats = $readJson("{$root}/data/lang/de/categories.json") ?? [];
        $enCats = $readJson("{$root}/data/lang/en/categories.json") ?? [];

        DB::transaction(function () use ($readJson, $root, $categoriesData, $deCats, $enCats) {

            // 1) Restaurant
            $restaurant = Restaurant::create([
                'name' => 'Demo Bistro Rottweil (STANDARD)',
                'slug' => 'demo-bistro-standard',
                'template_key' => 'classic',
                'default_locale' => 'de',
                'enabled_locales' => ['de', 'en'],
                'is_active' => true,

                // опционально если у тебя есть поля
                'plan_key' => 'demo',
                'monthly_price' => 0,
                'trial_ends_at' => now()->addDays(30)->toDateString(),
            ]);

            RestaurantToken::create([
                'restaurant_id' => $restaurant->id,
                'token' => Str::random(18),
            ]);

            // 2) Create sections (categories + bar subcategories)
            // From ZIP: salads, cold, meat, fish, desserts, cocktails, bar + barSubcategories
            $sectionIdByKey = [];

            $sort = 1;
            foreach ($categoriesData['categories'] as $cat) {
                $key = $cat['id'];
                $typeRaw = $cat['type'] ?? 'food';

                // Map types to our Section.type values (food|drink|service)
                $type = $typeRaw === 'bar' ? 'drink' : 'food';

                // Create main category section
                $section = Section::create([
                    'restaurant_id' => $restaurant->id,
                    'key' => $key,
                    'sort_order' => $sort++,
                    'type' => $type,
                    'is_active' => true,
                ]);
                $sectionIdByKey[$key] = $section->id;

                // Section translations (DE/EN)
                $deTitle = data_get($deCats, "categories.{$key}.name");
                $enTitle = data_get($enCats, "categories.{$key}.name");

                if ($deTitle) {
                    SectionTranslation::create([
                        'section_id' => $section->id,
                        'locale' => 'de',
                        'title' => $deTitle,
                        'description' => null,
                    ]);
                }
                if ($enTitle) {
                    SectionTranslation::create([
                        'section_id' => $section->id,
                        'locale' => 'en',
                        'title' => $enTitle,
                        'description' => null,
                    ]);
                }

                // Bar subcategories => separate sections: bar_softdrinks, bar_beer, ...
                if ($key === 'bar' && !empty($cat['subcategories'])) {
                    foreach ($cat['subcategories'] as $subKey) {
                        $subSectionKey = "bar_{$subKey}";
                        $subSection = Section::create([
                            'restaurant_id' => $restaurant->id,
                            'key' => $subSectionKey,
                            'sort_order' => $sort++,
                            'type' => 'drink',
                            'is_active' => true,
                        ]);
                        $sectionIdByKey[$subSectionKey] = $subSection->id;

                        $deSubTitle = data_get($deCats, "barSubcategories.{$subKey}");
                        $enSubTitle = data_get($enCats, "barSubcategories.{$subKey}");

                        if ($deSubTitle) {
                            SectionTranslation::create([
                                'section_id' => $subSection->id,
                                'locale' => 'de',
                                'title' => $deSubTitle,
                                'description' => null,
                            ]);
                        }
                        if ($enSubTitle) {
                            SectionTranslation::create([
                                'section_id' => $subSection->id,
                                'locale' => 'en',
                                'title' => $enSubTitle,
                                'description' => null,
                            ]);
                        }
                    }
                }
            }

            // 3) Create items for normal categories (salads/cold/meat/fish/desserts/cocktails)
            $normalCategories = ['salads','cold','meat','fish','desserts','cocktails'];

            foreach ($normalCategories as $catKey) {
                $sectionId = $sectionIdByKey[$catKey] ?? null;
                if (!$sectionId) continue;

                $itemsMeta = ($readJson("{$root}/data/items/{$catKey}.json") ?? [])['items'] ?? [];
                $deText = $readJson("{$root}/data/lang/de/{$catKey}.json") ?? [];
                $enText = $readJson("{$root}/data/lang/en/{$catKey}.json") ?? [];

                $order = 1;
                foreach ($itemsMeta as $meta) {
                    $itemKey = $meta['id'];
                    $labels = $meta['labels'] ?? [];
                    $img = $meta['image'] ?? null;

                    // price comes from lang json, there is "price": "19,90 €"
                    $deEntry = $deText[$itemKey] ?? [];
                    $enEntry = $enText[$itemKey] ?? [];

                    $priceStr = $deEntry['price'] ?? ($enEntry['price'] ?? null);
                    [$price, $currency] = $this->parsePrice($priceStr);

                    $item = Item::create([
                        'section_id' => $sectionId,
                        'sort_order' => $order++,
                        'price' => $price,
                        'currency' => $currency ?? 'EUR',
                        'is_active' => true,

                        // если у тебя есть json/meta поле — можно туда сложить labels
                        // 'meta' => ['labels' => $labels, 'source_image' => $img],
                    ]);

                    // translations
                    $this->saveItemTranslation($item->id, 'de', $deEntry);
                    $this->saveItemTranslation($item->id, 'en', $enEntry);
                }
            }

            // 4) Bar items: grouped by subcategory
            $barGroups = ($readJson("{$root}/data/items/bar.json") ?? [])['groups'] ?? [];
            $deBar = $readJson("{$root}/data/lang/de/bar.json") ?? [];
            $enBar = $readJson("{$root}/data/lang/en/bar.json") ?? [];

            foreach ($barGroups as $group) {
                $sub = $group['id'] ?? null;
                if (!$sub) continue;

                $sectionKey = "bar_{$sub}";
                $sectionId = $sectionIdByKey[$sectionKey] ?? null;
                if (!$sectionId) continue;

                $order = 1;
                foreach (($group['items'] ?? []) as $gi) {
                    $itemKey = $gi['id'] ?? null;
                    if (!$itemKey) continue;

                    $priceStr = $gi['price'] ?? null;
                    [$price, $currency] = $this->parsePrice($priceStr);

                    $item = Item::create([
                        'section_id' => $sectionId,
                        'sort_order' => $order++,
                        'price' => $price,
                        'currency' => $currency ?? 'EUR',
                        'is_active' => true,
                    ]);

                    $this->saveItemTranslation($item->id, 'de', $deBar[$itemKey] ?? ['name' => $itemKey]);
                    $this->saveItemTranslation($item->id, 'en', $enBar[$itemKey] ?? ['name' => $itemKey]);
                }
            }
        });
    }

    private function parsePrice(?string $priceStr): array
    {
        if (!$priceStr) return [null, 'EUR'];

        // Examples: "19,90 €", "3,20 €"
        $s = trim($priceStr);
        $currency = str_contains($s, '€') ? 'EUR' : null;

        // extract numeric with comma
        $num = preg_replace('/[^0-9,\.]/', '', $s);
        $num = str_replace('.', '', $num);      // in case "1.200,00"
        $num = str_replace(',', '.', $num);

        $price = is_numeric($num) ? (float)$num : null;
        return [$price, $currency];
    }

    private function saveItemTranslation(int $itemId, string $locale, array $entry): void
    {
        $title = $entry['name'] ?? null;
        if (!$title) return;

        // pick best description (long preferred)
        $desc = $entry['description'] ?? ($entry['short'] ?? null);

        // optionally append weight/allergens (MVP)
        $weight = $entry['weight'] ?? null;
        $allergens = $entry['allergens'] ?? [];

        $extra = [];
        if ($weight) $extra[] = $weight;
        if (is_array($allergens) && count($allergens) > 0) $extra[] = 'Allergens: ' . implode(', ', $allergens);

        if (count($extra) > 0) {
            $desc = trim((string)$desc);
            $desc = $desc ? ($desc . "\n" . implode(' • ', $extra)) : implode(' • ', $extra);
        }

        ItemTranslation::create([
            'item_id' => $itemId,
            'locale' => $locale,
            'title' => $title,
            'description' => $desc,
        ]);
    }
}
