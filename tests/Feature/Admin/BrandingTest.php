<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BrandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_logo()
    {
        Storage::fake('public');

        $restaurant = Restaurant::factory()->create();

        $user = User::factory()->create([
            'restaurant_id' => $restaurant->id,
        ]);

        $this->actingAs($user);

        $file = UploadedFile::fake()->image('logo.jpg');

        $this->post("/admin/restaurants/{$restaurant->id}/branding", [
            'logo' => $file,
        ]);

        Storage::disk('public')->assertExists("restaurants/{$restaurant->id}");
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
