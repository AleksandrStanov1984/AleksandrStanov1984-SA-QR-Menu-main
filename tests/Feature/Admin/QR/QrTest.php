<?php

namespace Tests\Feature\Admin\QR;

use Illuminate\Support\Facades\File;
use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\RestaurantQr;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\AdminHelpers;

class QrTest extends TestCase
{
    use RefreshDatabase, AdminHelpers;

    protected Restaurant $restaurant;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();

        $this->user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);

        $this->actingAs($this->user);
    }

    /** @test */
    public function qr_page_loads()
    {
        $response = $this->get(route('admin.restaurants.qr', $this->restaurant));
        $response->assertStatus(200);
    }

    /** @test */
    public function fallback_is_used_if_qr_not_exists()
    {
        $response = $this->get(route('admin.restaurants.qr', $this->restaurant));

        $response->assertStatus(200);
        $response->assertSee('fallback.webp');
    }

    /** @test */
    public function qr_can_be_generated()
    {
        // нужен user с permission → не трогаем setUp user
        $user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'meta' => [
                'permissions' => [
                    'restaurants.edit' => true,
                ],
            ],
        ]);

        $this->actingAs($user);

        $response = $this->post(route('admin.restaurants.qr.generate', $this->restaurant));

        $this->restaurant->refresh();

        $this->assertNotNull($this->restaurant->qr);
        $this->assertNotNull($this->restaurant->qr->qr_path);
    }

    /** @test */
    public function qr_regenerate_creates_new_path()
    {
        $user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'meta' => [
                'permissions' => [
                    'restaurants.edit' => true,
                ],
            ],
        ]);

        $this->actingAs($user);

        $this->post(route('admin.restaurants.qr.generate', $this->restaurant));

        $qr = $this->restaurant->qr()->first();
        $oldPath = $qr->qr_path;

        $this->post(route('admin.restaurants.qr.generate', $this->restaurant));

        $qr->refresh();

        $this->assertNotEquals($oldPath, $qr->qr_path);
    }

    /** @test */
    public function qr_svg_can_be_downloaded()
    {
        $qrPath = "restaurants/{$this->restaurant->id}/qr/final/test.svg";

        $fullPath = public_path('assets/' . $qrPath);

        File::ensureDirectoryExists(dirname($fullPath));
        File::put($fullPath, '<svg></svg>');

        $this->restaurant->qr()->create([
            'qr_path' => $qrPath,
        ]);

        $user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'meta' => [
                'permissions' => [
                    'restaurants.edit' => true,
                ],
            ],
        ]);

        $this->actingAs($user);

        $response = $this->get(
            route('admin.restaurants.qr.download', [$this->restaurant, 'svg'])
        );

        $response->assertStatus(200);
    }

    /** @test */
    public function qr_pdf_can_be_downloaded()
    {
        $qrPath = "restaurants/{$this->restaurant->id}/qr/final/test.svg";

        $fullPath = public_path('assets/' . $qrPath);

        File::ensureDirectoryExists(dirname($fullPath));
        File::put($fullPath, '<svg></svg>');

        $this->restaurant->qr()->create([
            'qr_path' => $qrPath,
        ]);

        $user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'meta' => [
                'permissions' => [
                    'restaurants.edit' => true,
                ],
            ],
        ]);

        $this->actingAs($user);

        $response = $this->get(
            route('admin.restaurants.qr.download', [$this->restaurant, 'pdf'])
        );

        $response->assertStatus(200);
    }

    /** @test */
    public function qr_download_returns_404_if_missing()
    {
        $this->restaurant->qr()->create([
            'qr_path' => 'restaurants/999/qr/final/missing.svg',
        ]);

        $user = User::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'meta' => [
                'permissions' => [
                    'restaurants.edit' => true,
                ],
            ],
        ]);

        $this->actingAs($user);

        $response = $this->get(
            route('admin.restaurants.qr.download', [$this->restaurant, 'svg'])
        );

        $response->assertStatus(404);
    }

    /** @test */
    public function qr_page_is_accessible_for_super_admin(): void
    {
        $user = $this->superAdmin();

        $this->actingAs($user)
            ->get("/admin/restaurants/{$this->restaurant->id}/qr")
            ->assertOk();
    }

    /** @test */
    public function qr_download_without_qr_returns_404(): void
    {
        $user = $this->superAdmin();

        $this->actingAs($user)
            ->get("/admin/restaurants/{$this->restaurant->id}/qr/download/svg")
            ->assertNotFound();
    }

    /** @test */
    public function qr_download_invalid_format_returns_404(): void
    {
        RestaurantQr::factory()->create([
            'restaurant_id' => $this->restaurant->id,
            'qr_path' => 'restaurants/' . $this->restaurant->id . '/qr/raw/test.svg',
        ]);

        $user = $this->superAdmin();

        $this->actingAs($user)
            ->get("/admin/restaurants/{$this->restaurant->id}/qr/download/png")
            ->assertNotFound();
    }
}
