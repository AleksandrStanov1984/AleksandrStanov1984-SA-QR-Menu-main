<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Restaurant;
use App\Models\Section;
use App\Models\Item;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Support\Permissions;

class SectionController extends Controller
{

    public function store(Request $request, Restaurant $restaurant)
    {
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

    public function update(Request $request, Restaurant $restaurant, Section $section)
    {
        // tenant + базовый доступ
        $this->assertRestaurantAccess($request, $restaurant, 'sections_manage');

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

        abort_unless((int)$section->restaurant_id === (int)$restaurant->id, 404);

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

        return back()->with('success', __('admin.common.saved') ?? 'Saved');
    }

    public function toggleActive(Request $request, Restaurant $restaurant, Section $section)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'sections_manage');
        abort_unless((int)$section->restaurant_id === (int)$restaurant->id, 404);

        $section->update([
            'is_active' => !$section->is_active
        ]);

        return back()->with('success', __('admin.sections.toggled'));
    }

    public function destroy(Request $request, Restaurant $restaurant, Section $section)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'sections_manage');
        abort_unless((int)$section->restaurant_id === (int)$restaurant->id, 404);

        $user = $request->user();

        if ($resp = Permissions::denyRedirect(auth()->user(), 'sections.delete')) {
            return $resp;
        }

        DB::transaction(function () use ($section, $restaurant, $user) {

            $userId = $user->id ?? null;

            // CATEGORY
            if (is_null($section->parent_id)) {

                $childIds = Section::query()
                    ->where('restaurant_id', $restaurant->id)
                    ->where('parent_id', $section->id)
                    ->pluck('id');

                Item::whereIn('section_id', $childIds->push($section->id))
                    ->update(['deleted_by_user_id' => $userId]);

                Item::whereIn('section_id', $childIds->push($section->id))
                    ->delete();

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

        return back()->with('success', __('admin.sections.deleted') ?? 'Deleted');
    }
}
