<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\{Restaurant, RestaurantToken, Section, Item, SectionTranslation, ItemTranslation};

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $r = Restaurant::create([
            'name' => 'Demo Bistro Rottweil',
            'slug' => 'demo-bistro-rottweil',
            'template_key' => 'classic',
            'default_locale' => 'de',
            'enabled_locales' => ['de','en'],
            'theme_tokens' => [
                'color_primary' => '#111827',
                'color_text' => '#111827',
                'color_accent' => '#16a34a',
                'card_radius' => '18px',
                'font' => 'system-ui, -apple-system, Segoe UI, Roboto, Arial',
            ],
            'trial_ends_at' => now()->addDays(14)->toDateString(),
            'plan_key' => 'small',
            'monthly_price' => 25.00,
            'is_active' => true,
        ]);

        RestaurantToken::create([
            'restaurant_id' => $r->id,
            'token' => Str::random(18),
        ]);

        $sec = Section::create(
        [
            'restaurant_id' => $r->id,
            'sort_order' => 1,
            'type' => 'food',
            'is_active' => true
        ]);

        SectionTranslation::insert([
            [
                'section_id' => $sec->id,
                'locale' => 'de',
                'title' => 'Burgers',
                'description' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'section_id' => $sec->id,
                'locale' => 'en',
                'title' => 'Burgers',
                'description' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        $item1 = Item::create(
        [
            'section_id' => $sec->id,
            'sort_order' => 1,
            'price' => 11.90,
            'currency' => 'EUR',
            'is_active' => true
        ]);

        ItemTranslation::insert([
            [
                'item_id' => $item1->id,
                'locale' => 'de',
                'title' => 'Classic Burger',
                'description' => 'Rind, Salat, Tomate, Haussoße',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'item_id' => $item1->id,
                'locale' => 'en',
                'title' => 'Classic Burger',
                'description' => 'Beef, lettuce, tomato, house sauce',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
