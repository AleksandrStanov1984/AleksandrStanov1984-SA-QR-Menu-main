<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Section;
use App\Models\SectionTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SubcategoryController extends Controller
{
    public function store(Request $request, Restaurant $restaurant)
    {
        $user = $request->user();
        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }

        $locales = $restaurant->enabled_locales ?: ['de'];

        $rules = [
            'parent_id'   => ['required', 'integer', 'exists:sections,id'],
            'title_font'  => ['nullable', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'title_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'is_active'   => ['nullable', 'boolean'],
        ];

        foreach ($locales as $loc) {
            $rules["title.$loc"] = ['required', 'string', 'max:50', 'regex:/^[^<>]*$/u'];
        }

        $data = $request->validate($rules);

        // parent must belong to same restaurant AND be top-level category
        $parent = Section::query()
            ->where('id', $data['parent_id'])
            ->where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->firstOrFail();

        // next sort order within this parent
        $nextSort = (int) Section::where('restaurant_id', $restaurant->id)
            ->where('parent_id', $parent->id)
            ->max('sort_order');
        $nextSort = $nextSort ? $nextSort + 1 : 1;

        $section = Section::create([
            'restaurant_id' => $restaurant->id,
            'parent_id'     => $parent->id,
            'type'          => 'subcategory',
            'sort_order'    => $nextSort,
            'is_active'     => (bool)($data['is_active'] ?? true),
            'title_font'    => $data['title_font'] ?? null,
            'title_color'   => $data['title_color'] ?? null,
        ]);

        foreach ($locales as $loc) {
            SectionTranslation::create([
                'section_id' => $section->id,
                'locale'     => $loc,
                'title'      => strip_tags(Arr::get($data, "title.$loc")),
            ]);
        }

        return back()->with('success', __('admin.sections.subcategories.created'));
    }
}
