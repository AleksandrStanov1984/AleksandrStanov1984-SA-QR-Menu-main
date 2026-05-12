<?php

namespace Tests\Feature\Billing;

use App\Models\MenuPlan;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BillingAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function makeRestaurant(): Restaurant
    {
        MenuPlan::query()->create([
            'key' => 'pro',
            'name' => 'Pro',
            'price' => 29.99,
            'is_active' => true,
            'is_public' => true,
        ]);

        return Restaurant::query()->create([
            'name' => 'Test Restaurant',
            'slug' => 'test-restaurant',

            'template_key' => 'united',
            'plan_key' => 'pro',

            'default_locale' => 'de',
            'enabled_locales' => ['de'],

            'is_active' => true,
        ]);
    }

    #[Test]
    public function super_admin_can_confirm_payment(): void
    {
        $restaurant = $this->makeRestaurant();

        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route(
                'admin.restaurants.billing.confirm',
                $restaurant
            ));

        $response->assertRedirect();
    }

    #[Test]
    public function regular_user_cannot_confirm_payment(): void
    {
        $restaurant = $this->makeRestaurant();

        $user = User::factory()->create([
            'is_super_admin' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route(
                'admin.restaurants.billing.confirm',
                $restaurant
            ));

        $response
            ->assertRedirect();

        $response
            ->assertSessionHas('warning');
    }

    #[Test]
    public function regular_user_cannot_resume_restaurant(): void
    {
        $restaurant = $this->makeRestaurant();

        $user = User::factory()->create([
            'is_super_admin' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route(
                'admin.restaurants.billing.resume',
                $restaurant
            ));

        $response
            ->assertRedirect();

        $response
            ->assertSessionHas('warning');
    }

    #[Test]
    public function super_admin_can_resume_restaurant(): void
    {
        $restaurant = $this->makeRestaurant();

        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route(
                'admin.restaurants.billing.resume',
                $restaurant
            ));

        $response->assertRedirect();
    }
}
