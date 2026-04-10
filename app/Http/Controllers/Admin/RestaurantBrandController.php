<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Restaurant;

use App\Support\Guards\AccessGuardTrait;
use Illuminate\Http\Request;

use App\Support\Permissions;

use App\Services\ImagePipelineService;

use App\Exceptions\TenantAccessException;

class RestaurantBrandController extends Controller
{
    use AccessGuardTrait;

    /**
     * @throws TenantAccessException
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $user = $request->user();

        if ($resp = Permissions::denyRedirect(auth()->user(), 'branding.logo.upload')) {
            return $resp;
        }

        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:3000'],
        ]);

        try {
            $pipeline = app(ImagePipelineService::class);
            $file = $request->file('logo');

            $segment = 'branding/logo';

            $path = $restaurant->logo_path
                ? $pipeline->replace($file, $restaurant->id, $restaurant->logo_path, $segment)
                : $pipeline->uploadAndProcess($file, $restaurant->id, $segment);

            $restaurant->update([
                'logo_path' => $path,
            ]);

            return back()->with('status', __('admin.restaurants.brand.logo_saved'));

        } catch (\Throwable $e) {

            \Log::error('Restaurant logo upload failed', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', __('admin.restaurants.brand.logo_upload_failed'));
        }
    }

    /**
     * @throws TenantAccessException
     */
    public function updateBackgrounds(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $user = $request->user();

        $canBg   = Permissions::can($user, 'branding.backgrounds.upload');
        $canMode = Permissions::can($user, 'branding.theme_mode.edit');

        abort_unless($canBg || $canMode, 403);

        $request->validate([
            'theme_mode' => ['nullable', 'in:auto,light,dark'],
            'bg_light'   => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'bg_dark'    => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ]);

        $meta = (array) $restaurant->meta;
        $meta['theme_mode'] = $meta['theme_mode'] ?? 'light';

        if ($canMode && $request->filled('theme_mode')) {
            $meta['theme_mode'] = $request->input('theme_mode');
        }

        try {
            $pipeline = app(ImagePipelineService::class);

            $segment = 'branding/backgrounds';

            $bgLight = $request->file('bg_light');
            $bgDark  = $request->file('bg_dark');

            if ($canBg && $bgLight && $bgLight->isValid()) {
                $meta['bg_light'] = !empty($meta['bg_light'])
                    ? $pipeline->replace($bgLight, $restaurant->id, $meta['bg_light'], $segment)
                    : $pipeline->uploadAndProcess($bgLight, $restaurant->id, $segment);
            }

            if ($canBg && $bgDark && $bgDark->isValid()) {
                $meta['bg_dark'] = !empty($meta['bg_dark'])
                    ? $pipeline->replace($bgDark, $restaurant->id, $meta['bg_dark'], $segment)
                    : $pipeline->uploadAndProcess($bgDark, $restaurant->id, $segment);
            }

            $restaurant->update([
                'meta' => $meta,
            ]);

            return back()->with('status', __('admin.restaurants.brand.background_updated'));

        } catch (\Throwable $e) {

            \Log::error('Restaurant branding background upload failed', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', __('admin.restaurants.brand.background_upload_failed'));
        }
    }

    public function edit(Restaurant $restaurant)
    {
        return view('admin.restaurants.branding', [
            'restaurant' => $restaurant,
        ]);
    }
}
