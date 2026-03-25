<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Tests\Traits\AdminHelpers;
use App\Models\Restaurant;
use App\Models\RestaurantQr;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RestaurantQrTest extends TestCase
{
    use RefreshDatabase;
    use AdminHelpers;

    public function test_qr_page_is_accessible_for_super_admin(): void
    {
        $restaurant = Restaurant::factory()->create();

        $user = $this->superAdmin();

        $this->actingAs($user)
            ->get("/admin/restaurants/{$restaurant->id}/qr")
            ->assertOk();
    }

    public function test_qr_download_without_qr_returns_404(): void
    {
        $restaurant = Restaurant::factory()->create();

        $user = $this->superAdmin();

        $this->actingAs($user)
            ->get("/admin/restaurants/{$restaurant->id}/qr/download/svg")
            ->assertNotFound();
    }

    public function test_qr_download_invalid_format_returns_404(): void
    {
        $restaurant = Restaurant::factory()->create();

        RestaurantQr::factory()->create([
            'restaurant_id' => $restaurant->id,
            'qr_path' => 'restaurants/' . $restaurant->id . '/qr/raw/test.svg',
        ]);

        $user = $this->superAdmin();

        $this->actingAs($user)
            ->get("/admin/restaurants/{$restaurant->id}/qr/download/png")
            ->assertNotFound();
    }
}
