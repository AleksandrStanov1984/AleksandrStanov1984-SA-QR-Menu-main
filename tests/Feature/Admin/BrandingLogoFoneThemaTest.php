<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Tests\Traits\AdminHelpers;
use App\Models\Restaurant;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BrandingLogoFoneThemaTest extends TestCase
{
    use RefreshDatabase, AdminHelpers;

    public function test_upload_logo()
    {
        Storage::fake('public');

        $r = Restaurant::factory()->create();
        $u = $this->admin($r, ['branding.logo.upload' => true]);

        $this->actingAs($u);

        $file = UploadedFile::fake()->image('logo.jpg');

        $this->post("/admin/restaurants/{$r->id}/branding", [
            'logo' => $file,
        ]);

        Storage::disk('public')->assertExists("restaurants/{$r->id}");
    }

    public function test_change_theme()
    {
        $r = Restaurant::factory()->create();
        $u = $this->admin($r);

        $this->actingAs($u);

        $this->post("/admin/restaurants/{$r->id}/branding", [
            'theme_mode' => 'dark',
        ]);

        $this->assertDatabaseHas('restaurants', [
            'id' => $r->id,
        ]);
    }
}
