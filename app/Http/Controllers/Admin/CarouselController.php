<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Section;
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
        $enabled = (bool) $request->boolean(
            'carousel_enabled'
        );

        $source = $request->input(
            'carousel_source'
        );

        $categoryId = $request->input(
            'carousel_category_id'
        );

        $subcategoryId = $request->input(
            'carousel_subcategory_id'
        );

        $categoryId = $categoryId
            ? (int) $categoryId
            : null;

        $subcategoryId = $subcategoryId
            ? (int) $subcategoryId
            : null;

        // =====================
        // FEATURE FLAGS
        // =====================
        $hasCarousel = $restaurant->feature(
            'carousel'
        );

        $hasAdvanced = $restaurant->feature(
            'carousel_advanced'
        );

        // =====================
        // GUARD
        // =====================
        if (!$hasCarousel) {

            return back()->withErrors([
                'carousel' => __('admin.carousel_locked'),
            ]);
        }

        // =====================
        // BASIC / PRO LOGIC
        // =====================
        if (!$hasAdvanced) {
            $source = 'bestseller';
        }

        $allowedSources = [
            'bestseller',
            'is_new',
            'dish_of_day',
        ];

        if ($hasAdvanced) {
            $allowedSources[] = 'category';
        }

        if (!in_array(
            $source,
            $allowedSources,
            true
        )) {
            $source = 'bestseller';
        }

        // =====================
        // VALIDATION (STRICT)
        // =====================
        if ($enabled) {

            $items = $restaurant->sections()
                ->with([
                    'items' => fn ($q) => $q
                        ->where('is_active', true),

                    'items.section',
                ])
                ->get()
                ->flatMap(
                    fn ($section) => $section->items
                );

            $hasItems = match ($source) {

                'is_new' => $items
                    ->filter(fn ($i) => (bool) data_get(
                        $i->meta,
                        'is_new'
                    ) === true)
                    ->isNotEmpty(),

                'dish_of_day' => $items
                    ->filter(fn ($i) => (bool) data_get(
                        $i->meta,
                        'dish_of_day'
                    ) === true)
                    ->isNotEmpty(),

                'category' => $items
                    ->filter(function ($i) use (
                        $categoryId,
                        $subcategoryId
                    ) {

                        $section = $i->section;

                        if (!$section) {
                            return false;
                        }

                        if ($subcategoryId) {

                            return
                                (int) $section->id ===
                                (int) $subcategoryId;
                        }

                        return
                            (int) $section->id ===
                            (int) $categoryId
                            || (int) $section->parent_id ===
                            (int) $categoryId;
                    })
                    ->isNotEmpty(),

                default => $items
                    ->filter(fn ($i) => (bool) data_get(
                        $i->meta,
                        'bestseller'
                    ) === true)
                    ->isNotEmpty(),
            };

            if (!$hasItems) {

                return back()
                    ->withInput()
                    ->with(
                        'warning',
                        __('admin.carousel_no_items_for_source', [
                            'source' => __('menu.' . $source),
                        ])
                    );
            }
        }

        // =====================
        // SAVE
        // =====================
        $meta['carousel'] = is_array(
            $meta['carousel'] ?? null
        )
            ? $meta['carousel']
            : [];

        $meta['carousel']['enabled'] = $enabled;

        $meta['carousel']['source'] = $enabled
            ? $source
            : 'bestseller';

        if ($enabled && $source === 'category') {

            $categoryExists = Section::query()
                ->where('restaurant_id', $restaurant->id)
                ->whereNull('parent_id')
                ->where('id', $categoryId)
                ->exists();

            if (!$categoryExists) {

                return back()->withErrors([
                    'carousel' => __('admin.invalid_category'),
                ]);
            }

            $meta['carousel']['category_id'] =
                $categoryId;

            if ($subcategoryId) {

                $subcategoryExists = Section::query()
                    ->where('restaurant_id', $restaurant->id)
                    ->where('parent_id', $categoryId)
                    ->where('id', $subcategoryId)
                    ->exists();

                if ($subcategoryExists) {

                    $meta['carousel']['subcategory_id'] =
                        $subcategoryId;
                }

            } else {

                unset(
                    $meta['carousel']['subcategory_id']
                );
            }

        } else {

            unset(
                $meta['carousel']['category_id'],
                $meta['carousel']['subcategory_id'],
            );
        }

        $restaurant->update([
            'meta' => $meta,
        ]);

        return back()->with(
            'status',
            __('admin.carousel_saved')
        );
    }
}
