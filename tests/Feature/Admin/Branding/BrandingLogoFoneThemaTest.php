<?php

namespace Admin\Branding;

use App\Models\Restaurant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\AdminHelpers;

class BrandingLogoFoneThemaTest extends TestCase
{
    use RefreshDatabase, AdminHelpers;

    protected Restaurant $restaurant;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->restaurant = Restaurant::factory()->create();
        $this->user = $this->admin($this->restaurant);

        $this->actingAs($this->user);
    }

//    public function test_upload_logo()
//    {
//        Storage::fake('public');
//
//        $r = Restaurant::factory()->create();
//        $u = $this->admin($r, ['branding.logo.upload' => true]);
//
//        $this->actingAs($u);
//
//        $file = UploadedFile::fake()->image('logo.jpg');
//
//        $this->post("/admin/restaurants/{$r->id}/branding", [
//            'logo' => $file,
//        ]);
//    }

    public function test_change_theme()
    {
        $this->post("/admin/restaurants/{$this->restaurant->id}/branding", [
            'theme_mode' => 'dark',
        ]);

        $this->assertDatabaseHas('restaurants', [
            'id' => $this->restaurant->id,
        ]);
    }
}
