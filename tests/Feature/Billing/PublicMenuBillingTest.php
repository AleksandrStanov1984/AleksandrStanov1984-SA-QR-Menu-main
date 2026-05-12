<?php

namespace Tests\Feature\Billing;

use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PublicMenuBillingTest extends TestCase
{
    use RefreshDatabase;

    protected function makeRestaurant(array $data = []): Restaurant
    {
        return Restaurant::factory()->create(array_merge([
            'name' => 'Test Restaurant',
            'slug' => 'test-restaurant',

            'template_key' => 'united',
            'plan_key' => 'pro',

            'default_locale' => 'de',
            'enabled_locales' => ['de'],

            'is_active' => true,
        ], $data));
    }

    #[Test]
    public function active_restaurant_menu_is_accessible(): void
    {
        $restaurant = $this->makeRestaurant([
            'is_active' => true,
        ]);

        $this->get(route('restaurant.show', $restaurant))
            ->assertOk();
    }

    #[Test]
    public function inactive_restaurant_menu_returns_404(): void
    {
        $restaurant = $this->makeRestaurant([
            'is_active' => false,
        ]);

        $this->get(route('restaurant.show', $restaurant))
            ->assertNotFound();
    }

    #[Test]
    public function inactive_restaurant_impressum_returns_404(): void
    {
        $restaurant = $this->makeRestaurant([
            'is_active' => false,
        ]);

        $this->get(route('legal.impressum', $restaurant))
            ->assertNotFound();
    }

    #[Test]
    public function inactive_restaurant_datenschutz_returns_404(): void
    {
        $restaurant = $this->makeRestaurant([
            'is_active' => false,
        ]);

        $this->get(route('legal.datenschutz', $restaurant))
            ->assertNotFound();
    }
}
