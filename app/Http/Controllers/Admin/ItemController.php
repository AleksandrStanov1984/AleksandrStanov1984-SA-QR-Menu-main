<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderItemsRequest;
use App\Http\Requests\Admin\StoreItemRequest;
use App\Http\Requests\Admin\UpdateItemRequest;

use App\Models\Item;
use App\Models\ItemTranslation;
use App\Models\Restaurant;
use App\Models\Section;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Support\Permissions;
use App\Services\ImagePipelineService;

class ItemController extends Controller
{
    private function assertRestaurantAccess(Request $request, Restaurant $restaurant, ?string $perm = null): void
    {
        $user = $request->user();

        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }

        if ($perm && !$user->is_super_admin && !$user->hasPerm($perm)) {
            abort(403);
        }
    }

    private function sanitizeText(?string $value): ?string
    {
        if ($value === null) return null;
        return trim(strip_tags($value));
    }

    public function store(StoreItemRequest $request, Restaurant $restaurant, Section $section)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'items_manage');

        if ((int)$section->restaurant_id !== (int)$restaurant->id) abort(404);

        $data = $request->validated();

        $next = (int) Item::where('section_id', $section->id)->max('sort_order');
        $next = $next ? $next + 1 : 1;

        return DB::transaction(function () use ($request, $restaurant, $section, $data, $next) {

            $meta = [
                'is_new'       => (bool)($data['is_new'] ?? false),
                'dish_of_day'  => (bool)($data['dish_of_day'] ?? false),
                'show_image'   => (bool)($data['show_image'] ?? true),
                'spicy'        => (int)($data['spicy'] ?? 0),
                'style'        => $data['style'] ?? null,
            ];

            if (!empty($data['unit_value']) && !empty($data['unit_type'])) {
                $meta['unit'] = [
                    'value' => (float)$data['unit_value'],
                    'type'  => $data['unit_type'],
                ];
            }

            if (!empty($meta['dish_of_day'])) {
                Item::where('section_id', $section->id)->update([
                    'meta' => DB::raw("JSON_SET(COALESCE(meta, JSON_OBJECT()), '$.dish_of_day', false)")
                ]);
            }

            $item = Item::create([
                'section_id' => $section->id,
                'sort_order' => $next,
                'price'      => $data['price'] ?? null,
                'currency'   => $data['currency'] ?? 'EUR',
                'meta'       => $meta,
                'is_active'  => (bool)($data['is_active'] ?? true),
            ]);

            foreach (($data['translations'] ?? []) as $locale => $t) {
                ItemTranslation::create([
                    'item_id'     => $item->id,
                    'locale'      => (string)$locale,
                    'title'       => $this->sanitizeText($t['title'] ?? ''),
                    'description' => $this->sanitizeText($t['description'] ?? null),
                    'details'     => $this->sanitizeText($t['details'] ?? null),
                ]);
            }

            // ✅ IMAGE
            if ($request->file('image') && $request->file('image')->isValid()) {

                try {
                    $path = app(ImagePipelineService::class)
                        ->uploadAndProcess($request->file('image'), $restaurant->id);

                    $item->update([
                        'image_path' => $path
                    ]);

                } catch (\Throwable $e) {
                    \Log::error('Image upload failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);

                    return back()->with('error', 'Image upload failed: ' . $e->getMessage());
                }
            }

            return back()->with('success', __('admin.items.created'));
        });
    }

    public function update(UpdateItemRequest $request, Restaurant $restaurant, Item $item)
    {

        $this->assertRestaurantAccess($request, $restaurant, 'items_manage');

        $section = $item->section;
        if (!$section || (int)$section->restaurant_id !== (int)$restaurant->id) abort(404);

        $data = $request->validated();

        return DB::transaction(function () use ($request, $restaurant, $item, $section, $data) {

            $meta = $item->meta ?? [];

            $meta['is_new']      = (bool)($data['is_new'] ?? ($meta['is_new'] ?? false));
            $meta['dish_of_day'] = (bool)($data['dish_of_day'] ?? ($meta['dish_of_day'] ?? false));
            $meta['show_image']  = (bool)($data['show_image'] ?? ($meta['show_image'] ?? true));
            $meta['spicy']       = (int)($data['spicy'] ?? ($meta['spicy'] ?? 0));
            $meta['style']       = $data['style'] ?? ($meta['style'] ?? null);

            if (!empty($data['unit_value']) && !empty($data['unit_type'])) {
                $meta['unit'] = [
                    'value' => (float)$data['unit_value'],
                    'type'  => $data['unit_type'],
                ];
            }

            if (!empty($meta['dish_of_day'])) {
                Item::where('section_id', $section->id)
                    ->where('id', '!=', $item->id)
                    ->update([
                        'meta' => DB::raw("JSON_SET(COALESCE(meta, JSON_OBJECT()), '$.dish_of_day', false)")
                    ]);
            }

            $item->update([
                'price'     => $data['price'] ?? $item->price,
                'currency'  => $data['currency'] ?? $item->currency,
                'meta'      => $meta,
                'is_active' => (bool)($data['is_active'] ?? $item->is_active),
            ]);

            foreach (($data['translations'] ?? []) as $locale => $t) {
                $tr = $item->translations()->where('locale', (string)$locale)->first();

                if (!$tr) {
                    $tr = ItemTranslation::create([
                        'item_id' => $item->id,
                        'locale'  => (string)$locale,
                        'title'   => '',
                    ]);
                }

                $tr->update([
                    'title'       => $this->sanitizeText($t['title'] ?? ''),
                    'description' => $this->sanitizeText($t['description'] ?? null),
                    'details'     => $this->sanitizeText($t['details'] ?? null),
                ]);
            }

            if ($request->file('image') && $request->file('image')->isValid()) {

                try {

                    $path = app(ImagePipelineService::class)
                        ->replace($request->file('image'), $restaurant->id, $item->image_path);

                    $item->update([
                        'image_path' => $path
                    ]);

                } catch (\Throwable $e) {

                    \Log::error('Image replace failed', [
                        'error' => $e->getMessage()
                    ]);

                    return back()->with('error', 'Image replace failed');
                }
            }

            return back()->with('success', __('admin.items.updated'));
        });
    }

    public function reorder(ReorderItemsRequest $request, Restaurant $restaurant, Section $section)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'items_manage');

        if ((int)$section->restaurant_id !== (int)$restaurant->id) abort(404);

        $ids = $request->validated()['item_ids'];

        $count = Item::where('section_id', $section->id)->whereIn('id', $ids)->count();
        if ($count !== count($ids)) abort(422);

        DB::transaction(function () use ($section, $ids) {
            foreach ($ids as $i => $id) {
                Item::where('id', $id)
                    ->where('section_id', $section->id)
                    ->update(['sort_order' => $i + 1]);
            }
        });

        return response()->json(['ok' => true]);
    }

    public function toggleActive(Request $request, Restaurant $restaurant, Item $item)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'items_manage');

        $section = $item->section;
        if (!$section || (int)$section->restaurant_id !== (int)$restaurant->id) abort(404);

        $item->is_active = !$item->is_active;
        $item->save();

        return back();
    }

    public function destroy(Request $request, Restaurant $restaurant, Item $item)
    {
        $user = $request->user();

        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }

        Permissions::abortUnless($user, 'items.delete');

        $item->loadMissing('section');
        abort_unless((int)$item->section->restaurant_id === (int)$restaurant->id, 404);

        $item->deleted_by_user_id = $user->id ?? null;
        $item->save();

        $item->delete();

        return back()->with('success', __('admin.items.deleted') ?? 'Deleted');
    }
}
