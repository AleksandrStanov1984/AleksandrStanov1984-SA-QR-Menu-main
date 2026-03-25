<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Tests\Traits\AdminHelpers;
use App\Models\Restaurant;
use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ValidationTest extends TestCase
{
    use RefreshDatabase, AdminHelpers;

    public function test_item_requires_title()
    {
        $r = Restaurant::factory()->create();
        $section = Section::factory()->create(['restaurant_id' => $r->id]);

        $u = $this->admin($r, ['items_manage' => true]);
        $this->actingAs($u);

        $this->post("/admin/restaurants/{$r->id}/sections/{$section->id}/items", [
            'translations' => [
                'de' => ['title' => ''],
            ],
        ])->assertSessionHasErrors();
    }
}
