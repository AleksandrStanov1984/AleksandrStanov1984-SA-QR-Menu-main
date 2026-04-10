<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class CarouselController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        return view('admin.restaurants.carousel', [
            'restaurant' => $restaurant,
        ]);
    }

    public function update(Request $request, Restaurant $restaurant)
    {
        $meta = is_array($restaurant->meta ?? null)
            ? $restaurant->meta
            : [];

        // =====================
        // INPUT
        // =====================
        $enabled = (bool)$request->boolean('carousel_enabled');
        $source  = $request->input('carousel_source');

        // =====================
        // FEATURE FLAGS
        // =====================
        $hasCarousel = $restaurant->feature('carousel');
        $hasAdvanced = $restaurant->feature('carousel_advanced');

        // =====================
        // GUARD
        // =====================
        if (!$hasCarousel) {
            return back()->withErrors([
                'carousel' => __('admin.carousel_locked')
            ]);
        }

        // =====================
        // BASIC / PRO LOGIC
        // =====================
        if (!$hasAdvanced) {
            $source = 'bestseller';
        }

        if ($hasAdvanced) {
            if (!in_array($source, ['bestseller','is_new','dish_of_day'])) {
                $source = 'bestseller';
            }
        }

        // =====================
        // VALIDATION (STRICT)
        // =====================
        if ($enabled) {

            $items = $restaurant->sections()
                ->with(['items' => fn ($q) => $q->where('is_active', true)])
                ->get()
                ->flatMap(fn ($section) => $section->items);

            $hasItems = match ($source) {
                'is_new' => $items->filter(fn ($i) => (bool)data_get($i->meta, 'is_new') === true)->isNotEmpty(),
                'dish_of_day' => $items->filter(fn ($i) => (bool)data_get($i->meta, 'dish_of_day') === true)->isNotEmpty(),
                default => $items->filter(fn ($i) => (bool)data_get($i->meta, 'bestseller') === true)->isNotEmpty(),
            };

            if (!$hasItems) {
                return back()
                    ->withInput()
                    ->with('warning', __('admin.carousel_no_items_for_source', [
                        'source' => __('menu.' . $source)
                    ]));
            }
        }

        // =====================
        // SAVE
        // =====================
        $meta['carousel_enabled'] = $enabled;
        $meta['carousel_source']  = $enabled ? $source : null;

        $restaurant->update([
            'meta' => $meta,
        ]);

        return back()->with('status', __('admin.carousel_saved'));
    }
}
