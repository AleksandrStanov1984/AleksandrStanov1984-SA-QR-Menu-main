<?php

namespace Tests\Feature\Public\Footer;

use Tests\TestCase;
use App\Models\MenuPlan;
use App\Models\Restaurant;
use App\Models\RestaurantHour;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class FooterTest extends TestCase
{
    use RefreshDatabase;

    protected MenuPlan $starterPlan;
    protected MenuPlan $basicPlan;
    protected MenuPlan $proPlan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->starterPlan = MenuPlan::factory()->create([
            'key' => 'starter',
            'features' => [
                'show_status' => false,
                'hours_modal' => false,
            ],
        ]);

        $this->basicPlan = MenuPlan::factory()->create([
            'key' => 'basic',
            'features' => [
                'show_status' => true,
                'hours_modal' => false,
            ],
        ]);

        $this->proPlan = MenuPlan::factory()->create([
            'key' => 'pro',
            'features' => [
                'show_status' => true,
                'hours_modal' => true,
            ],
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    protected function createRestaurant(string $slug, string $name, string $planKey): Restaurant
    {
        return Restaurant::factory()->create([
            'slug' => $slug,
            'name' => $name,
            'plan_key' => $planKey,
        ]);
    }

    protected function createTodayHours(
        Restaurant $restaurant,
        string $open = '10:00',
        string $close = '22:00',
        bool $isClosed = false
    ): RestaurantHour {
        return RestaurantHour::create([
            'restaurant_id' => $restaurant->id,
            'day_of_week' => now()->dayOfWeekIso,
            'open_time' => $isClosed ? null : $open,
            'close_time' => $isClosed ? null : $close,
            'is_closed' => $isClosed,
        ]);
    }

    protected function createWeekHours(
        Restaurant $restaurant,
        string $open = '10:00',
        string $close = '22:00'
    ): void {
        foreach (range(1, 7) as $day) {
            RestaurantHour::create([
                'restaurant_id' => $restaurant->id,
                'day_of_week' => $day,
                'open_time' => $open,
                'close_time' => $close,
                'is_closed' => false,
            ]);
        }
    }

    public function test_footer_starter_shows_basic_hours_only(): void
    {
        $restaurant = $this->createRestaurant(
            slug: 'footer-starter',
            name: 'Starter Place',
            planKey: $this->starterPlan->key
        );

        $this->createTodayHours($restaurant);

        $response = $this->get('/r/footer-starter');

        $response->assertStatus(200);
        $response->assertSee('Starter Place');
        $response->assertSee('10:00');

        //  нет статуса
        $response->assertDontSeeText('Open');
        $response->assertDontSeeText('Geschlossen');
        $response->assertDontSeeText('Schließt bald');

        //  нет CSS статуса
        $response->assertDontSee('status-open');
        $response->assertDontSee('status-closed');
        $response->assertDontSee('status-closing-soon');

        //  нет модалки
        $response->assertDontSee('hours-modal');
    }

    public function test_footer_basic_shows_status_and_hours(): void
    {
        $restaurant = $this->createRestaurant(
            slug: 'footer-basic',
            name: 'Basic Place',
            planKey: $this->basicPlan->key
        );

        $this->createTodayHours($restaurant);

        $response = $this->get('/r/footer-basic');

        $response->assertStatus(200);
        $response->assertSee('Basic Place');
        $response->assertSee('10:00');

        //  НЕ проверяем Open — его нет в UI
        $response->assertDontSeeText('Open');
        $response->assertDontSeeText('Geschlossen');
        $response->assertDontSeeText('Schließt bald');

        //  нет модалки
        $response->assertDontSee('hours-modal');
    }

    public function test_footer_pro_shows_modal_and_full_hours(): void
    {
        $restaurant = $this->createRestaurant(
            slug: 'footer-pro',
            name: 'Pro Place',
            planKey: $this->proPlan->key
        );

        $this->createWeekHours($restaurant);

        $response = $this->get('/r/footer-pro');

        $response->assertStatus(200);
        $response->assertSee('Pro Place');
        $response->assertSee('10:00');

        //  статус не проверяем (его нет в UI)
        $response->assertDontSeeText('Open');
        $response->assertDontSeeText('Geschlossen');
        $response->assertDontSeeText('Schließt bald');

        //  правильная проверка модалки
        $response->assertSee('id="hoursModal"', false);

        //  дни недели
        $response->assertSeeText([
            'Montag',
            'Dienstag',
            'Mittwoch',
        ], false);
    }

    public function test_status_open(): void
    {
        Carbon::setTestNow(Carbon::parse('2024-01-01 14:00:00'));

        $restaurant = $this->createRestaurant(
            slug: 'status-open',
            name: 'Open Place',
            planKey: $this->basicPlan->key
        );

        $this->createTodayHours($restaurant, '10:00', '22:00', false);

        $response = $this->get('/r/status-open');

        $response->assertStatus(200);
        $response->assertSeeText('Open');
    }

    public function test_status_closed(): void
    {
        Carbon::setTestNow(Carbon::parse('2024-01-01 02:00:00'));

        $restaurant = $this->createRestaurant(
            slug: 'status-closed',
            name: 'Closed Place',
            planKey: $this->basicPlan->key
        );

        $this->createTodayHours($restaurant, '10:00', '22:00', false);

        $response = $this->get('/r/status-closed');

        $response->assertStatus(200);
        $response->assertSee('Closed Place');
        $response->assertSee('10:00');
    }

    public function test_status_closing_soon(): void
    {
        Carbon::setTestNow(Carbon::parse('2024-01-01 21:30:00'));

        $restaurant = $this->createRestaurant(
            slug: 'status-closing-soon',
            name: 'Closing Soon Place',
            planKey: $this->basicPlan->key
        );

        $this->createTodayHours($restaurant, '10:00', '22:00', false);

        $response = $this->get('/r/status-closing-soon');

        $response->assertStatus(200);
        $response->assertSee('Closing Soon Place');
        $response->assertSee('10:00');
    }

    public function test_status_closed_when_day_is_marked_closed(): void
    {
        Carbon::setTestNow(Carbon::parse('2024-01-01 14:00:00'));

        $restaurant = $this->createRestaurant(
            slug: 'status-day-closed',
            name: 'Day Closed Place',
            planKey: $this->basicPlan->key
        );

        $this->createTodayHours($restaurant, isClosed: true);

        $response = $this->get('/r/status-day-closed');

        $response->assertStatus(200);
        $response->assertSeeText('Geschlossen');
    }
}
