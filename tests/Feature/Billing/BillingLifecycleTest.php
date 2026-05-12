<?php

namespace Tests\Feature\Billing;

use App\Models\BillingRecord;
use App\Models\MenuPlan;
use App\Models\Restaurant;
use App\Models\User;
use App\Services\BillingService\BillingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BillingLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected BillingService $billing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->billing = app(BillingService::class);
    }

    protected function makeRestaurant(array $data = []): Restaurant
    {
        MenuPlan::query()->create([
            'key' => 'pro',
            'name' => 'Pro',
            'price' => 29.99,
            'is_active' => true,
            'is_public' => true,
        ]);

        return Restaurant::query()->create(array_merge([
            'name' => 'Test Restaurant',
            'slug' => 'test-restaurant',

            'template_key' => 'united',
            'plan_key' => 'pro',

            'default_locale' => 'de',
            'enabled_locales' => ['de'],

            'is_active' => false,
        ], $data));
    }

    protected function makeAdmin(): User
    {
        return User::factory()->create([
            'is_super_admin' => true,
        ]);
    }

    // =========================
    // START TRIAL
    // =========================
    #[Test]
    public function trial_can_be_started(): void
    {
        $restaurant = $this->makeRestaurant();

        $admin = $this->makeAdmin();

        $this->billing->startTrial(
            restaurant: $restaurant,
            days: 14,
            confirmedBy: $admin
        );

        $restaurant->refresh();

        $this->assertTrue($restaurant->is_active);

        $this->assertNotNull($restaurant->trial_ends_at);

        $this->assertDatabaseHas('billing_records', [
            'restaurant_id' => $restaurant->id,
            'type' => BillingRecord::TYPE_TRIAL,
        ]);
    }

    // =========================
    // CONFIRM PAYMENT
    // =========================
    #[Test]
    public function payment_extends_subscription(): void
    {
        $restaurant = $this->makeRestaurant([
            'is_active' => true,
            'paid_until' => now()->addDays(10),
        ]);

        $admin = $this->makeAdmin();

        $oldDate = $restaurant->paid_until->copy();

        $this->billing->confirmPayment(
            restaurant: $restaurant,
            confirmedBy: $admin,
            amount: 29.99
        );

        $restaurant->refresh();

        $this->assertTrue(
            $restaurant->paid_until->greaterThan($oldDate)
        );

        $this->assertDatabaseHas('billing_records', [
            'restaurant_id' => $restaurant->id,
            'type' => BillingRecord::TYPE_PAYMENT,
        ]);
    }

    // =========================
    // DEACTIVATE
    // =========================
    #[Test]
    public function restaurant_can_be_deactivated(): void
    {
        $restaurant = $this->makeRestaurant([
            'is_active' => true,
            'trial_ends_at' => now()->addDays(14),
        ]);

        $admin = $this->makeAdmin();

        $this->billing->deactivate(
            restaurant: $restaurant,
            confirmedBy: $admin
        );

        $restaurant->refresh();

        $this->assertFalse($restaurant->is_active);

        $this->assertTrue(
            $restaurant->trial_ends_at->isPast()
        );

        $this->assertDatabaseHas('billing_records', [
            'restaurant_id' => $restaurant->id,
            'type' => BillingRecord::TYPE_DEACTIVATION,
        ]);
    }

    // =========================
    // RESUME
    // =========================
    #[Test]
    public function restaurant_can_be_resumed(): void
    {
        $restaurant = $this->makeRestaurant([
            'is_active' => false,
        ]);

        $admin = $this->makeAdmin();

        $this->billing->resume(
            restaurant: $restaurant,
            confirmedBy: $admin
        );

        $restaurant->refresh();

        $this->assertTrue($restaurant->is_active);

        $this->assertDatabaseHas('billing_records', [
            'restaurant_id' => $restaurant->id,
            'type' => BillingRecord::TYPE_RESUME,
        ]);
    }

    // =========================
    // KEEP DATA
    // =========================
    #[Test]
    public function keep_data_can_be_enabled(): void
    {
        $restaurant = $this->makeRestaurant();

        $restaurant->update([
            'keep_data' => true,
        ]);

        $restaurant->refresh();

        $this->assertTrue($restaurant->keep_data);
    }

    // =========================
    // EXPIRE
    // =========================
    #[Test]
    public function expired_restaurant_becomes_inactive(): void
    {
        $restaurant = $this->makeRestaurant([
            'is_active' => true,
            'paid_until' => now()->subDay(),
        ]);

        $this->artisan('billing:check-expired')
            ->assertSuccessful();

        $restaurant->refresh();

        $this->assertFalse($restaurant->is_active);
    }

    #[Test]
    public function second_trial_does_not_overwrite_trial_used_at(): void
    {
        $restaurant = $this->makeRestaurant();

        $admin = $this->makeAdmin();

        $this->billing->startTrial(
            restaurant: $restaurant,
            days: 14,
            confirmedBy: $admin
        );

        $restaurant->refresh();

        $firstTrialUsedAt = $restaurant->trial_used_at->copy();

        sleep(1);

        $this->billing->startTrial(
            restaurant: $restaurant,
            days: 14,
            confirmedBy: $admin
        );

        $restaurant->refresh();

        $this->assertTrue(
            $restaurant->trial_used_at->equalTo($firstTrialUsedAt)
        );
    }

    #[Test]
    public function payment_extends_from_trial_end_date(): void
    {
        $restaurant = $this->makeRestaurant([
            'is_active' => true,
            'trial_ends_at' => now()->addDays(5),
        ]);

        $admin = $this->makeAdmin();

        $trialEnd = $restaurant->trial_ends_at->copy();

        $this->billing->confirmPayment(
            restaurant: $restaurant,
            confirmedBy: $admin,
            amount: 29.99
        );

        $restaurant->refresh();

        $this->assertTrue(
            $restaurant->paid_until->greaterThan($trialEnd)
        );
    }
}
