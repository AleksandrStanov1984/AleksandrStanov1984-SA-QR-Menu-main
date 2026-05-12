<?php

namespace Tests\Feature\Billing;

use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BillingExpireCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function makeRestaurant(array $data = []): Restaurant
    {
        return Restaurant::factory()->create(array_merge([
            'name' => 'Expire Test',
            'slug' => uniqid(),

            'template_key' => 'united',
            'plan_key' => 'pro',

            'default_locale' => 'de',
            'enabled_locales' => ['de'],
        ], $data));
    }

    #[Test]
    public function future_paid_restaurant_is_not_expired(): void
    {
        $restaurant = $this->makeRestaurant([
            'is_active' => true,
            'paid_until' => now()->addDays(30),
        ]);

        $this->artisan('billing:check-expired')
            ->assertSuccessful();

        $restaurant->refresh();

        $this->assertTrue($restaurant->is_active);
    }

    #[Test]
    public function inactive_restaurant_is_not_processed_again(): void
    {
        $restaurant = $this->makeRestaurant([
            'is_active' => false,
            'paid_until' => now()->subDays(30),
        ]);

        $this->artisan('billing:check-expired')
            ->assertSuccessful();

        $restaurant->refresh();

        $this->assertFalse($restaurant->is_active);
    }
}
