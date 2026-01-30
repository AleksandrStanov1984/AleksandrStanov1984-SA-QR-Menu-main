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
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

use App\Support\Permissions;

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
        $value = strip_tags($value);
        return trim($value);
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

            // Если делаем dish_of_day=true — снимаем у остальных в этой секции (железно)
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

            // translations: ожидаем формат translations[locale][...]
            foreach (($data['translations'] ?? []) as $locale => $t) {
                ItemTranslation::create([
                    'item_id'     => $item->id,
                    'locale'      => (string)$locale,
                    'title'       => $this->sanitizeText($t['title'] ?? ''),
                    'description' => $this->sanitizeText($t['description'] ?? null),
                    'details'     => $this->sanitizeText($t['details'] ?? null),
                ]);
            }

            // image upload (без svg, только image mime)
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store("restaurants/{$restaurant->id}/items", 'public');
                $item->update(['image_path' => $path]);
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

            if (!empty($meta['dish_of_day'])) {
                // снять у остальных
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

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store("restaurants/{$restaurant->id}/items", 'public');
                $item->update(['image_path' => $path]);
            }

            return back()->with('success', __('admin.items.updated'));
        });
    }

    public function reorder(ReorderItemsRequest $request, Restaurant $restaurant, Section $section)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'items_manage');
        if ((int)$section->restaurant_id !== (int)$restaurant->id) abort(404);

        $ids = $request->validated()['item_ids'];

        // Проверяем, что все id действительно принадлежат этой секции
        $count = Item::where('section_id', $section->id)->whereIn('id', $ids)->count();
        if ($count !== count($ids)) abort(422);

        DB::transaction(function () use ($section, $ids) {
            foreach ($ids as $i => $id) {
                Item::where('id', $id)->where('section_id', $section->id)->update(['sort_order' => $i + 1]);
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

    public function destroy(Request $request, Restaurant $restaurant, \App\Models\Item $item)
    {
        $user = $request->user();

        // scope
        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }

        // permissions
        \App\Support\Permissions::abortUnless($user, 'items.delete');

        // item belongs to restaurant
        $item->loadMissing('section');
        abort_unless((int)$item->section->restaurant_id === (int)$restaurant->id, 404);

        // помечаем кто удалил
        $item->deleted_by_user_id = $user->id ?? null;
        $item->save();

        // SOFT delete
        $item->delete();

        // Для обычного пользователя оно исчезнет из списка (default scope).
        // Для super admin будет видно через withTrashed().
        return back()->with('success', __('admin.items.deleted') ?? 'Deleted');
    }


    public function updateMeta(Request $request, Restaurant $restaurant, Item $item)
    {
        $user = $request->user();

        // scope: user только свой ресторан
        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }

        // item должен принадлежать этому ресторану (через section)
        $item->loadMissing('section');
        abort_unless((int)$item->section->restaurant_id === (int)$restaurant->id, 404);

        // валидация входа (принимаем только перечисленное)
        $data = $request->validate([
            'is_active'   => ['sometimes', 'boolean'],
            'show_image'  => ['sometimes', 'boolean'],
            'is_new'      => ['sometimes', 'boolean'],
            'dish_of_day' => ['sometimes', 'boolean'],
            'spicy'       => ['sometimes', 'integer', 'min:0', 'max:5'],
        ]);

        // --- права на каждый "пук" отдельно ---
        if (array_key_exists('is_active', $data)) {
            Permissions::abortUnless($user, 'items.toggle.active');
        }
        if (array_key_exists('show_image', $data)) {
            Permissions::abortUnless($user, 'items.toggle.show_image');
        }
        if (array_key_exists('is_new', $data)) {
            Permissions::abortUnless($user, 'items.flag.new');
        }
        if (array_key_exists('dish_of_day', $data)) {
            Permissions::abortUnless($user, 'items.flag.dish_of_day');
        }
        if (array_key_exists('spicy', $data)) {
            Permissions::abortUnless($user, 'items.flag.spicy');
        }

        // legacy страховка на переход (если хочешь — можно убрать позже)
        if (!$user->is_super_admin && !$user->hasPerm('items_manage')) {
            abort(403);
        }

        // meta хранится в json
        $meta = is_array($item->meta) ? $item->meta : (json_decode((string)$item->meta, true) ?: []);

        // is_active отдельным полем
        if (array_key_exists('is_active', $data)) {
            $item->is_active = (bool)$data['is_active'];
        }

        // meta flags
        if (array_key_exists('show_image', $data)) {
            $meta['show_image'] = (bool)$data['show_image'];
        }
        if (array_key_exists('is_new', $data)) {
            $meta['is_new'] = (bool)$data['is_new'];
        }
        if (array_key_exists('spicy', $data)) {
            $meta['spicy'] = (int)$data['spicy'];
        }

        // dish_of_day: строго один на секцию (SQLite-safe)
        if (array_key_exists('dish_of_day', $data)) {
            $newVal = (bool)$data['dish_of_day'];

            if ($newVal) {
                $others = Item::query()
                    ->where('section_id', $item->section_id)
                    ->where('id', '!=', $item->id)
                    ->get();

                foreach ($others as $o) {
                    $m2 = is_array($o->meta) ? $o->meta : (json_decode((string)$o->meta, true) ?: []);
                    if (!empty($m2['dish_of_day'])) {
                        $m2['dish_of_day'] = false;
                        $o->meta = $m2;
                        $o->save();
                    }
                }
            }

            $meta['dish_of_day'] = $newVal;
        }

        $item->meta = $meta;
        $item->save();

        return response()->json([
            'ok' => true,
            'item_id' => $item->id,
            'meta' => $item->meta,
            'is_active' => (bool)$item->is_active,
        ]);
    }



}
