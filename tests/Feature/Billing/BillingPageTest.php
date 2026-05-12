<?php

namespace Tests\Feature\Billing;

use App\Models\MenuPlan;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BillingPageTest extends TestCase
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

    public function test_billing_page_loads(): void
    {
        $restaurant = $this->makeRestaurant();

        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route(
                'admin.restaurants.billing',
                $restaurant
            ));

        $response
            ->assertOk();

        $response
            ->assertSee(__('billing.title'));
    }

    public function test_super_admin_sees_admin_buttons(): void
    {
        $restaurant = $this->makeRestaurant();

        $admin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $response = $this
            ->actingAs($admin)
            ->get(route(
                'admin.restaurants.billing',
                $restaurant
            ));

        $response->assertSee(
            __('billing.actions.confirm_payment')
        );

        $response->assertSee(
            __('billing.actions.start_trial')
        );
    }

    public function test_regular_user_does_not_see_admin_buttons(): void
    {
        $restaurant = $this->makeRestaurant();

        $user = User::factory()->create([
            'is_super_admin' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route(
                'admin.restaurants.billing',
                $restaurant
            ));

        $response->assertDontSee(
            __('billing.actions.confirm_payment')
        );

        $response->assertDontSee(
            __('billing.actions.start_trial')
        );

        $response->assertDontSee(
            __('billing.actions.resume')
        );
    }
}
