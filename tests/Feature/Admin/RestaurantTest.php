<?php

namespace Tests\Feature\Admin;

use App\Models\MenuPlan;
use App\Models\MenuTemplate;
use Tests\TestCase;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\AdminHelpers;

class RestaurantTest extends TestCase
{
    use RefreshDatabase, AdminHelpers;

    public function test_create_restaurant()
    {
        $this->actingAs($this->superAdmin());

        $template = MenuTemplate::factory()->create();

        $plan = MenuPlan::factory()->create();

        $this->post('/admin/restaurants', [
            'name' => 'Test Rest',
            'template_key' => $template->key,
            'plan_key' => $plan->key,

            'user_name' => 'Test User',
            'user_email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertDatabaseHas('restaurants', [
            'name' => 'Test Rest',
        ]);
    }

    public function test_delete_restaurant()
    {
        $r = Restaurant::factory()->create();

        $this->actingAs($this->superAdmin());

        $this->delete("/admin/restaurants/{$r->id}");

        $this->assertDatabaseHas('restaurants', [
            'id' => $r->id,
            'is_active' => false,
        ]);
    }
}
