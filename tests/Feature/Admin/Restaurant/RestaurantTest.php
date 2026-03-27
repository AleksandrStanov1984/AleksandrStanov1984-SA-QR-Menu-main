<?php

namespace Admin\Restaurant;

use App\Models\MenuPlan;
use App\Models\MenuTemplate;
use App\Models\Restaurant;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AdminHelpers;

class RestaurantTest extends TestCase
{
    use RefreshDatabase, AdminHelpers;

    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();
    }

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
        $this->actingAs($this->superAdmin());

        $this->delete("/admin/restaurants/{$this->restaurant->id}");

        $this->assertDatabaseHas('restaurants', [
            'id' => $this->restaurant->id,
            'is_active' => false,
        ]);
    }

    public function test_item_requires_title()
    {
        $section = Section::factory()->create([
            'restaurant_id' => $this->restaurant->id
        ]);

        $user = $this->admin($this->restaurant, [
            'items_manage' => true,
        ]);

        $this->actingAs($user);

        $this->post("/admin/restaurants/{$this->restaurant->id}/sections/{$section->id}/items", [
            'translations' => [
                'de' => ['title' => ''],
            ],
        ])->assertSessionHasErrors();
    }
}
