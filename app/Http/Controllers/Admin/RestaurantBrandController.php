<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Support\Permissions;
use App\Services\ImagePipelineService;

class RestaurantBrandController extends Controller
{
    private function assertRestaurantScope(Request $request, Restaurant $restaurant): void
    {
        $user = $request->user();

        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantScope($request, $restaurant);

        $user = $request->user();
        Permissions::abortUnless($user, 'branding.logo.upload');

        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:3000'],
        ]);

        try {
            $pipeline = app(ImagePipelineService::class);

            $segment = 'branding/logo';

            $path = $restaurant->logo_path
                ? $pipeline->replace(
                    $request->file('logo'),
                    $restaurant->id,
                    $restaurant->logo_path,
                    $segment
                )
                : $pipeline->uploadAndProcess(
                    $request->file('logo'),
                    $restaurant->id,
                    $segment
                );

            $restaurant->update([
                'logo_path' => $path,
            ]);

            return back()->with('success', __('admin.restaurants.brand.logo_saved'));

        } catch (\Throwable $e) {

            \Log::error('Restaurant logo upload failed', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Logo upload failed');
        }
    }

    public function updateBackgrounds(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantScope($request, $restaurant);

        $user = $request->user();

        $canBg   = Permissions::can($user, 'branding.backgrounds.upload');
        $canMode = Permissions::can($user, 'branding.theme_mode.edit');

        abort_unless($canBg || $canMode, 403);

        $request->validate([
            'theme_mode' => ['nullable', 'in:auto,light,dark'],
            'bg_light'   => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'bg_dark'    => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
        ]);

        $meta = is_array($restaurant->meta) ? $restaurant->meta : [];
        $meta['theme_mode'] = $meta['theme_mode'] ?? 'light';

        if ($canMode && $request->filled('theme_mode')) {
            $meta['theme_mode'] = $request->input('theme_mode');
        }

        try {
            $pipeline = app(ImagePipelineService::class);

            $segment = 'branding/backgrounds';

            if ($canBg && $request->hasFile('bg_light') && $request->file('bg_light')->isValid()) {
                $meta['bg_light'] = !empty($meta['bg_light'])
                    ? $pipeline->replace(
                        $request->file('bg_light'),
                        $restaurant->id,
                        $meta['bg_light'],
                        $segment
                    )
                    : $pipeline->uploadAndProcess(
                        $request->file('bg_light'),
                        $restaurant->id,
                        $segment
                    );
            }

            if ($canBg && $request->hasFile('bg_dark') && $request->file('bg_dark')->isValid()) {
                $meta['bg_dark'] = !empty($meta['bg_dark'])
                    ? $pipeline->replace(
                        $request->file('bg_dark'),
                        $restaurant->id,
                        $meta['bg_dark'],
                        $segment
                    )
                    : $pipeline->uploadAndProcess(
                        $request->file('bg_dark'),
                        $restaurant->id,
                        $segment
                    );
            }

            $restaurant->meta = $meta;
            $restaurant->save();

            return back()->with('status', 'Фон и тема обновлены');

        } catch (\Throwable $e) {

            \Log::error('Restaurant branding background upload failed', [
                'restaurant_id' => $restaurant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Background upload failed');
        }
    }

    public function edit(Restaurant $restaurant)
    {
        return view('admin.restaurants.branding', [
            'restaurant' => $restaurant,
        ]);
    }
}
