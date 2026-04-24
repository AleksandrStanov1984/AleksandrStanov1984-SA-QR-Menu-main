<?php

namespace App\Http\Controllers\Admin;

use App\DTO\ItemMetaDTO;
use App\Exceptions\TenantAccessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderItemsRequest;
use App\Http\Requests\Admin\StoreItemRequest;
use App\Http\Requests\Admin\UpdateItemRequest;
use App\Models\Item;
use App\Models\ItemTranslation;
use App\Models\Restaurant;
use App\Models\Section;
use App\Services\ImagePipelineService;
use App\Services\ImageService;
use App\Support\Guards\AccessGuardTrait;
use App\Support\Permissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    use AccessGuardTrait;

    private function sanitizeText(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim(strip_tags($value));
    }

    /**
     * @throws TenantAccessException
     * @throws \Throwable
     */
    public function store(StoreItemRequest $request, Restaurant $restaurant, Section $section)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'items_manage');

        if ((int) $section->restaurant_id !== (int) $restaurant->id) {
            abort(404);
        }

        $data = $request->validated();
        $translationsData = $data['translations'] ?? [];

        $lastSort = Item::where('section_id', $section->id)
            ->orderByDesc('sort_order')
            ->value('sort_order');

        $next = $lastSort ? ((int) $lastSort + 1) : 1;

        return DB::transaction(function () use ($request, $restaurant, $section, $data, $translationsData, $next) {
            $meta = [
                'is_new'      => (bool) ($data['is_new'] ?? false),
                'dish_of_day' => (bool) ($data['dish_of_day'] ?? false),
                'show_image'  => (bool) ($data['show_image'] ?? true),
                'spicy'       => (int) ($data['spicy'] ?? 0),
                'style'       => $data['style'] ?? null,
            ];

            if (!empty($data['unit_value']) && !empty($data['unit_type'])) {
                $meta['unit'] = [
                    'value' => (float) $data['unit_value'],
                    'type'  => $data['unit_type'],
                ];
            }

            if (!empty($meta['dish_of_day'])) {
                Item::where('section_id', $section->id)
                    ->whereRaw("JSON_EXTRACT(COALESCE(meta, JSON_OBJECT()), '$.dish_of_day') = true")
                    ->update([
                        'meta' => DB::raw("JSON_SET(COALESCE(meta, JSON_OBJECT()), '$.dish_of_day', false)"),
                    ]);
            }

            $item = Item::create([
                'section_id' => $section->id,
                'sort_order' => $next,
                'price'      => $data['price'] ?? null,
                'currency'   => $data['currency'] ?? 'EUR',
                'meta'       => $meta,
                'is_active'  => (bool) ($data['is_active'] ?? true),
            ]);

            if (!empty($translationsData)) {
                $rows = [];

                foreach ($translationsData as $locale => $t) {
                    $rows[] = [
                        'item_id'     => $item->id,
                        'locale'      => (string) $locale,
                        'title'       => $this->sanitizeText($t['title'] ?? ''),
                        'description' => $this->sanitizeText($t['description'] ?? null),
                        'details'     => $this->sanitizeText($t['details'] ?? null),
                    ];
                }

                ItemTranslation::insert($rows);
            }

            if ($request->file('image') && $request->file('image')->isValid()) {
                try {
                    $path = app(ImagePipelineService::class)
                        ->uploadAndProcess($request->file('image'), $restaurant->id);

                    $item->update(['image_path' => $path]);
                } catch (\Throwable $e) {
                    \Log::error('Image upload failed', [
                        'error' => $e->getMessage(),
                    ]);

                    return back()->with('error', 'Image upload failed');
                }
            }

            return back()->with('status', __('admin.items.created'));
        });
    }

    /**
     * @throws \Throwable
     * @throws TenantAccessException
     */
    public function update(UpdateItemRequest $request, Restaurant $restaurant, Item $item)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'items_manage');

        $item->loadMissing(['section', 'translations']);

        $section = $item->section;
        if (!$section || (int) $section->restaurant_id !== (int) $restaurant->id) {
            abort(404);
        }

        $data = $request->validated();
        $translationsData = $data['translations'] ?? [];

        return DB::transaction(function () use ($request, $restaurant, $item, $data, $translationsData) {
            $item->update([
                'price'     => $data['price'] ?? $item->price,
                'currency'  => $data['currency'] ?? $item->currency,
                'is_active' => (bool) ($data['is_active'] ?? $item->is_active),
            ]);

            if (!empty($translationsData)) {
                $existingTranslations = $item->translations->keyBy(fn ($tr) => (string) $tr->locale);

                foreach ($translationsData as $locale => $t) {
                    $locale = (string) $locale;
                    $tr = $existingTranslations->get($locale);

                    if (!$tr) {
                        $tr = ItemTranslation::create([
                            'item_id' => $item->id,
                            'locale'  => $locale,
                            'title'   => '',
                        ]);

                        $existingTranslations->put($locale, $tr);
                    }

                    $updateData = [
                        'title'       => $this->sanitizeText($t['title'] ?? ''),
                        'description' => $this->sanitizeText($t['description'] ?? null),
                    ];

                    if (array_key_exists('details', $t)) {
                        $updateData['details'] = $this->sanitizeText($t['details']);
                    }

                    $tr->update($updateData);

                }
            }

            if ($request->file('image') && $request->file('image')->isValid()) {
                try {
                    $path = app(ImagePipelineService::class)
                        ->replace($request->file('image'), $restaurant->id, $item->image_path);

                    $item->update(['image_path' => $path]);
                } catch (\Throwable $e) {
                    \Log::error('Image replace failed', [
                        'error' => $e->getMessage(),
                    ]);

                    return back()->with('error', 'Image replace failed');
                }
            }

            return back()->with('status', __('admin.items.updated'));
        });
    }

    /**
     * @throws TenantAccessException
     */
    public function updateMeta(Request $request, Restaurant $restaurant, Item $item)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'items_manage');

        $item->loadMissing('section');

        $data = $request->all();

        // is_active отдельно
        if (array_key_exists('is_active', $data)) {

            $item->is_active = (bool)$data['is_active'];
            $item->save();

            return response()->json([
                'status' => true,
                'is_active' => $item->is_active,
            ]);
        }

        // meta
        $meta = ItemMetaDTO::fromModel($item);
        $meta->apply($data);

        $item->meta = $meta->toArray();
        $item->save();

        return response()->json([
            'status' => true,
            'meta' => $meta->toArray(),
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function updateActive(Request $request, Restaurant $restaurant, Item $item)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'items_manage');

        $item->loadMissing('section');

        $value = (bool) $request->input('is_active', true);

        $item->update([
            'is_active' => $value,
        ]);

        return response()->json([
            'status' => true,
            'is_active' => $item->is_active,
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function destroy(Request $request, Restaurant $restaurant, Item $item)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'items.delete');

        $item->loadMissing('section');


        if ($item->image_path) {
            app(\App\Services\ImageService::class)->delete($item->image_path);
        }

        $item->delete();

        return response()->json([
            'status' => true,
            'deleted_id' => $item->id,
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function reorder(ReorderItemsRequest $request, Restaurant $restaurant, Section $section)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'items_manage');

        $ids = $request->input('item_ids', []);

        foreach ($ids as $index => $id) {
            Item::where('id', $id)
                ->where('section_id', $section->id)
                ->update([
                    'sort_order' => $index + 1
                ]);
        }

        return response()->json([
            'status' => true
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function deleteImage(Request $request, Restaurant $restaurant, Item $item)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'items_manage');

        $imageService = app(ImageService::class);

        if ($item->image_path) {
            $imageService->delete($item->image_path);
        }

        $item->update([
            'image_path' => null,
        ]);

        return response()->json([
            'message' => __('menu.image_deleted'),
        ]);
    }
}
