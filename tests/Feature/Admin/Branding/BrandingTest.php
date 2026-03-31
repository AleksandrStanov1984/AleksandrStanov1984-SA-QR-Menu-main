<?php

namespace Admin\Branding;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_logo()
    {
        Storage::fake('public');

        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create([
            'restaurant_id' => $restaurant->id,
            'meta' => [
                'permissions' => [
                    'branding.logo.upload' => true,
                ],
            ],
        ]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->image('logo.jpg');

        $response = $this->post(
            route('admin.restaurants.logo.update', $restaurant),
            [
                'logo' => $file,
            ]
        );

        $response->assertRedirect();
    }

    public function test_change_theme_mode()
    {
        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create([
            'restaurant_id' => $restaurant->id,
        ]);

        $this->actingAs($user);

        $this->post("/admin/restaurants/{$restaurant->id}/branding", [
            'theme_mode' => 'dark',
        ]);

        $this->assertDatabaseHas('restaurants', [
            'id' => $restaurant->id,
        ]);
    }
}
