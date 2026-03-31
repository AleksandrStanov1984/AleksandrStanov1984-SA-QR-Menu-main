<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantBanner;
use App\Support\Guards\AccessGuardTrait;
use App\Services\ImagePipelineService;
use App\Services\ImageService;
use App\Exceptions\TenantAccessException;
use Illuminate\Http\Request;

class PromoBannerController extends Controller
{
    use AccessGuardTrait;

    /**
     * Страница баннеров
     * @throws TenantAccessException
     */
    public function index(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);
        $this->assertProFeature($request, $restaurant);

        $banners = $restaurant->banners()->get();

        return view('admin.restaurants.components.banners.index', compact('restaurant', 'banners'));
    }

    /**
     * SAVE (универсальный: 1 или несколько слотов)
     * @throws TenantAccessException
     */
    public function save(
        Request $request,
        Restaurant $restaurant,
        ImagePipelineService $pipeline
    ) {
        $this->assertRestaurantAccess($request, $restaurant);
        $this->assertProFeature($request, $restaurant);

        $files = $request->file('banners', []);

        if (empty($files)) {
            return response()->json([
                'error' => __('banners.select_file')
            ], 422);
        }

        foreach ($files as $slot => $file) {

            if (!$file) continue;

            $slot = (int) $slot;

            if ($slot < 1 || $slot > 5) continue;

            $banner = RestaurantBanner::where('restaurant_id', $restaurant->id)
                ->where('slot', $slot)
                ->first();

            // удалить старое изображение
            if ($banner && $banner->image_path) {
                app(ImageService::class)->delete($banner->image_path);
            }

            // загрузка нового
            $path = $pipeline->uploadAndProcess(
                $file,
                $restaurant->id,
                'banners'
            );

            if ($banner) {
                $banner->update([
                    'image_path' => $path
                ]);
            } else {
                RestaurantBanner::create([
                    'restaurant_id' => $restaurant->id,
                    'slot' => $slot,
                    'image_path' => $path,
                    'sort_order' => $slot,
                    'is_active' => true,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Удаление одного баннера
     * @throws TenantAccessException
     */
    public function destroy(Request $request, Restaurant $restaurant, int $id)
    {
        $this->assertRestaurantAccess($request, $restaurant);
        $this->assertProFeature($request, $restaurant);

        $banner = RestaurantBanner::where('restaurant_id', $restaurant->id)
            ->findOrFail($id);

        if ($banner->image_path) {
            app(ImageService::class)->delete($banner->image_path);
        }

        $banner->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Удалить все баннеры
     * @throws TenantAccessException
     */
    public function destroyAll(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);
        $this->assertProFeature($request, $restaurant);

        $banners = RestaurantBanner::where('restaurant_id', $restaurant->id)->get();

        foreach ($banners as $banner) {
            if ($banner->image_path) {
                app(ImageService::class)->delete($banner->image_path);
            }
            $banner->delete();
        }

        return response()->json(['success' => true]);
    }

    /**
     * @throws TenantAccessException
     */
    public function reorder(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $ids = $request->input('ids', []);

        foreach ($ids as $index => $id) {
            RestaurantBanner::where('id', $id)
                ->where('restaurant_id', $restaurant->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }
}
