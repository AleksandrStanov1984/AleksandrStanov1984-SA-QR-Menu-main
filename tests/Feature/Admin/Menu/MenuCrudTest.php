<?php

namespace Admin\Menu;

use App\Models\Restaurant;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\AdminHelpers;

class MenuCrudTest extends TestCase
{
    use RefreshDatabase, AdminHelpers;

    protected Restaurant $restaurant;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();

        $this->user = $this->admin($this->restaurant, [
            'sections_manage' => true,
        ]);

        $this->actingAs($this->user);
    }

    public function test_create_category()
    {
        $this->post("/admin/restaurants/{$this->restaurant->id}/sections", [
            'title' => 'Pizza',
        ]);

        $this->assertDatabaseHas('sections', [
            'restaurant_id' => $this->restaurant->id,
        ]);
    }

    public function test_create_subcategory()
    {
        $parent = Section::factory()->create([
            'restaurant_id' => $this->restaurant->id
        ]);

        $this->post("/admin/restaurants/{$this->restaurant->id}/sections", [
            'parent_id' => $parent->id,
            'title' => 'Sub',
        ]);

        $this->assertDatabaseHas('sections', [
            'parent_id' => $parent->id,
        ]);
    }
}
