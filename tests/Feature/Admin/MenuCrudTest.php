<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Tests\Traits\AdminHelpers;
use App\Models\Restaurant;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuCrudTest extends TestCase
{
    use RefreshDatabase, AdminHelpers;

    public function test_create_category()
    {
        $r = Restaurant::factory()->create();
        $u = $this->admin($r, ['sections_manage' => true]);

        $this->actingAs($u);

        $this->post("/admin/restaurants/{$r->id}/sections", [
            'title' => 'Pizza',
        ]);

        $this->assertDatabaseHas('sections', [
            'restaurant_id' => $r->id,
        ]);
    }

    public function test_create_subcategory()
    {
        $r = Restaurant::factory()->create();
        $parent = Section::factory()->create(['restaurant_id' => $r->id]);

        $u = $this->admin($r, ['sections_manage' => true]);
        $this->actingAs($u);

        $this->post("/admin/restaurants/{$r->id}/sections", [
            'parent_id' => $parent->id,
            'title' => 'Sub',
        ]);

        $this->assertDatabaseHas('sections', [
            'parent_id' => $parent->id,
        ]);
    }
}
