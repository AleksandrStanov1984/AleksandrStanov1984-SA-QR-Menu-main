<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;
use App\Http\Controllers\Controller;

use App\Models\Restaurant;
use App\Models\Section;
use App\Models\Item;

use App\Support\Guards\AccessGuardTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Support\Permissions;

class SectionController extends Controller
{
    use AccessGuardTrait;

    /**
     * @throws TenantAccessException
     */
    public function store(Request $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        if ($resp = Permissions::denyRedirect(auth()->user(), 'sections_manage')) {
            return $resp;
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:sections,id'],
        ]);

        $section = Section::create([
            'restaurant_id' => $restaurant->id,
            'parent_id' => $data['parent_id'] ?? null,
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $section->translations()->create([
            'locale' => $restaurant->default_locale ?? 'de',
            'title' => $data['title'],
        ]);

        return back()->with('status', 'Section created');
    }

    public function index(Restaurant $restaurant)
    {
        $sections = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->with([
                'translations',
                'children.translations',
                'children.items.translations',
                'items.translations',
            ])
            ->get();

        $allParents = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('sort_order')
            ->get();

        return view('admin.sections.index', compact('restaurant', 'sections', 'allParents'));
    }

    /**
     * @throws TenantAccessException
     */
    public function update(Request $request, Restaurant $restaurant, Section $section)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $user = $request->user();

        if (is_null($section->parent_id)) {
            if ($resp = Permissions::denyRedirect($user, 'categories.edit')) {
                return $resp;
            }
        } else {
            if ($resp = Permissions::denyRedirect($user, 'subcategories.edit')) {
                return $resp;
            }
        }

        if ((int)$section->restaurant_id !== (int)$restaurant->id) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        $data = $request->validate([
            'is_active'   => ['nullable', 'boolean'],
            'title_font'  => ['nullable', 'string', 'max:50'],
            'title_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'title'       => ['nullable', 'array'],
        ]);

        if (array_key_exists('is_active', $data)) {
            $section->is_active = (bool)$data['is_active'];
        }

        $section->title_font  = $data['title_font'] ?? $section->title_font;
        $section->title_color = $data['title_color'] ?? $section->title_color;
        $section->save();

        if (!empty($data['title']) && is_array($data['title'])) {

            $locales = $restaurant->enabled_locales ?: ['de'];
            $defaultLocale = $restaurant->default_locale ?: 'de';

            if (!in_array($defaultLocale, $locales, true)) {
                $defaultLocale = $locales[0] ?? 'de';
            }

            $fallback = trim((string)($data['title'][$defaultLocale] ?? ''));

            foreach ($locales as $loc) {
                $title = trim((string)($data['title'][$loc] ?? ''));
                if ($title === '') $title = $fallback;

                $section->translations()->updateOrCreate(
                    ['locale' => $loc],
                    ['title' => $title]
                );
            }
        }

        return back()->with('status', __('admin.common.saving'));
    }

    /**
     * @throws TenantAccessException
     */
    public function toggleActive(Request $request, Restaurant $restaurant, Section $section)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        if ((int)$section->restaurant_id !== (int)$restaurant->id) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        $section->update([
            'is_active' => !$section->is_active
        ]);

        return back()->with('status', __('admin.sections.toggled'));
    }

    /**
     * @throws TenantAccessException
     * @throws \Throwable
     */
    public function destroy(Request $request, Restaurant $restaurant, Section $section)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        if ((int)$section->restaurant_id !== (int)$restaurant->id) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        if ($resp = Permissions::denyRedirect(auth()->user(), 'sections.delete')) {
            return $resp;
        }

        DB::transaction(function () use ($section, $restaurant, $request) {

            $userId = $request->user()?->id;

            // CATEGORY
            if (is_null($section->parent_id)) {

                $childIds = Section::query()
                    ->where('restaurant_id', $restaurant->id)
                    ->where('parent_id', $section->id)
                    ->pluck('id');

                $ids = $childIds->push($section->id);

                Item::whereIn('section_id', $ids)
                    ->update(['deleted_by_user_id' => $userId]);

                Item::whereIn('section_id', $ids)->delete();

                Section::whereIn('id', $childIds)
                    ->update(['deleted_by_user_id' => $userId]);

                Section::whereIn('id', $childIds)->delete();

                $section->update(['deleted_by_user_id' => $userId]);
                $section->delete();

                return;
            }

            // SUBCATEGORY
            Item::where('section_id', $section->id)
                ->update(['deleted_by_user_id' => $userId]);

            Item::where('section_id', $section->id)->delete();

            $section->update(['deleted_by_user_id' => $userId]);
            $section->delete();
        });

        return response()->json([
            'deleted_id' => $section->id,
            'message' => __('admin.sections.deleted'),
        ]);
    }
}
