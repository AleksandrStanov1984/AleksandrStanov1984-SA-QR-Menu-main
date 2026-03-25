<?php

namespace Tests\Feature\Public;

use Tests\TestCase;
use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_loads(): void
    {
        $restaurant = Restaurant::factory()->create([
            'slug' => 'test',
        ]);

        $this->get('/r/test')
            ->assertStatus(200)
            ->assertSee($restaurant->name);
    }
}
