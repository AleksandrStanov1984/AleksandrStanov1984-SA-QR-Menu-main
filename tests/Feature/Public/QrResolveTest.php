<?php

namespace Tests\Feature\Public;

use Tests\TestCase;
use App\Models\Restaurant;
use App\Models\RestaurantToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QrResolveTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_token_redirects_to_restaurant_page(): void
    {
        $restaurant = Restaurant::factory()->create([
            'slug' => 'test-rest',
        ]);

        RestaurantToken::create([
            'restaurant_id' => $restaurant->id,
            'token' => 'abc123token',
        ]);

        $this->get('/q/abc123token')
            ->assertRedirect('/r/test-rest');
    }

    public function test_invalid_qr_token_returns_404(): void
    {
        $this->get('/q/not-existing-token')
            ->assertNotFound();
    }
}
