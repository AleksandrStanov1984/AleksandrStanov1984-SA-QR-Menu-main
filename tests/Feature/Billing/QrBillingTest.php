<?php

namespace Tests\Feature\Billing;

use App\Models\Restaurant;
use App\Models\RestaurantToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class QrBillingTest extends TestCase
{
    use RefreshDatabase;

    protected function makeRestaurant(array $data = []): Restaurant
    {
        return Restaurant::factory()->create(array_merge([
            'name' => 'QR Restaurant',
            'slug' => 'qr-restaurant',

            'template_key' => 'united',
            'plan_key' => 'pro',

            'default_locale' => 'de',
            'enabled_locales' => ['de'],

            'is_active' => true,
        ], $data));
    }

    #[Test]
    public function active_restaurant_qr_redirects_to_menu(): void
    {
        $restaurant = $this->makeRestaurant();

        $token = RestaurantToken::query()->create([
            'restaurant_id' => $restaurant->id,
            'token' => 'test-token',
        ]);

        $this->get(route('qr.resolve', $token->token))
            ->assertRedirect(route('restaurant.show', $restaurant, false));
    }

    #[Test]
    public function inactive_restaurant_qr_returns_404(): void
    {
        $restaurant = $this->makeRestaurant([
            'is_active' => false,
        ]);

        $token = RestaurantToken::query()->create([
            'restaurant_id' => $restaurant->id,
            'token' => 'inactive-token',
        ]);

        $this->get(route('qr.resolve', $token->token))
            ->assertNotFound();
    }
}
