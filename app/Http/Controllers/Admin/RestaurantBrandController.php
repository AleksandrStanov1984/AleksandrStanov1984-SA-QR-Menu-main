<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\ImagePipelineService;
use App\Support\Guards\AccessGuardTrait;
use App\Support\Permissions;
use Illuminate\Http\Request;

class RestaurantBrandController extends Controller
{
    use AccessGuardTrait;

    /**
     * @throws TenantAccessException
     */
    public function update(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        if ($resp = Permissions::denyRedirect($request->user(), 'branding.logo.upload')) {
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

        $canBg = Permissions::can($user, 'branding.backgrounds.upload');
        $canMode = Permissions::can($user, 'branding.theme_mode.edit');

        if (!$canBg && !$canMode) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        $request->validate([
            'theme_mode' => ['nullable', 'in:auto,light,dark'],
            'bg_light'   => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'bg_dark'    => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ]);

        $meta = is_array($restaurant->meta) ? $restaurant->meta : [];
        $meta['theme_mode'] = $meta['theme_mode'] ?? 'light';

        if ($canMode && $request->filled('theme_mode')) {
            $meta['theme_mode'] = $request->string('theme_mode')->toString();
        }

        try {
            $pipeline = app(ImagePipelineService::class);

            $bgLight = $request->file('bg_light');
            $bgDark  = $request->file('bg_dark');

            $segmentLight = 'branding/backgrounds/light';
            $segmentDark  = 'branding/backgrounds/dark';

            if ($canBg && $bgLight && $bgLight->isValid()) {
                $meta['bg_light'] = !empty($meta['bg_light'])
                    ? $pipeline->replace($bgLight, $restaurant->id, $meta['bg_light'], $segmentLight)
                    : $pipeline->uploadAndProcess($bgLight, $restaurant->id, $segmentLight);
            }

            if ($canBg && $bgDark && $bgDark->isValid()) {
                $meta['bg_dark'] = !empty($meta['bg_dark'])
                    ? $pipeline->replace($bgDark, $restaurant->id, $meta['bg_dark'], $segmentDark)
                    : $pipeline->uploadAndProcess($bgDark, $restaurant->id, $segmentDark);
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

    /**
     * @throws TenantAccessException
     */
    public function edit(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        return view('admin.restaurants.branding', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function uploadOg(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        if (!$restaurant->feature('og_images')) {
            return back()->with('error', 'Feature not available for your plan');
        }

        $request->validate([
            'image'  => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'locale' => ['required', 'string'],
        ]);

        $locale = $request->input('locale');

        $limit = $restaurant->feature('og_limit');

        $meta = is_array($restaurant->meta) ? $restaurant->meta : [];
        $meta['og'] = $meta['og'] ?? [];

        if ($limit !== null && count($meta['og']) >= $limit && empty($meta['og'][$locale])) {
            return back()->with('error', 'OG limit reached for your plan');
        }

        try {
            $pipeline = app(\App\Services\ImagePipelineService::class);

            $path = $pipeline->uploadAndProcess(
                $request->file('image'),
                $restaurant->id,
                "branding/og/{$locale}"
            );

            $meta['og'][$locale] = $path;

            $restaurant->update([
                'meta' => $meta
            ]);

            return back()->with('status', 'Saved');

        } catch (\Throwable $e) {

            \Log::error('OG upload failed', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Upload failed');
        }
    }

    /**
     * @throws TenantAccessException
     */
    public function deleteOg(Request $request, Restaurant $restaurant, string $locale)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        if (!$restaurant->feature('og_images')) {
            return back()->with('error', 'Feature not available for your plan');
        }

        try {
            $meta = is_array($restaurant->meta) ? $restaurant->meta : [];

            if (!empty($meta['og'][$locale])) {

                app(\App\Services\ImageService::class)->delete($meta['og'][$locale]);

                unset($meta['og'][$locale]);

                $restaurant->update([
                    'meta' => $meta
                ]);
            }

            return back()->with('status', 'Deleted');

        } catch (\Throwable $e) {

            \Log::error('OG delete failed', [
                'restaurant_id' => $restaurant->id,
                'locale' => $locale,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Delete failed');
        }
    }
}
