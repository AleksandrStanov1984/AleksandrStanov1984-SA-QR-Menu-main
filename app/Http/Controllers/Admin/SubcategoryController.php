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

        // scope: user только свой ресторан
        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }

        // permissions
        if (!$user->is_super_admin && !$user->hasPerm('sections_manage')) {
            abort(403);
        }

        $locales = $restaurant->enabled_locales ?: ['de'];
        $defaultLocale = $restaurant->default_locale ?: 'de';
        if (!in_array($defaultLocale, $locales, true)) {
            $defaultLocale = $locales[0] ?? 'de';
        }

        $rules = [
            'parent_id' => ['required', 'integer'],
            'title_font'  => ['nullable', 'string', 'max:50'],
            'title_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'is_active'   => ['nullable', 'boolean'],
        ];

        // required только defaultLocale, остальные nullable
        $rules["title.$defaultLocale"] = ['required', 'string', 'max:50'];
        foreach ($locales as $loc) {
            if ($loc === $defaultLocale) continue;
            $rules["title.$loc"] = ['nullable', 'string', 'max:50'];
        }

        $data = $request->validate($rules);

        // parent должен быть категорией этого ресторана (top-level)
        $parent = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->where('type', 'category')
            ->findOrFail((int)$data['parent_id']);

        // sort_order следующий внутри parent
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

        $fallbackTitle = trim((string)\Illuminate\Support\Arr::get($data, "title.$defaultLocale", ''));

        foreach ($locales as $loc) {
            $title = trim((string)\Illuminate\Support\Arr::get($data, "title.$loc", ''));
            if ($title === '') $title = $fallbackTitle;

            SectionTranslation::create([
                'section_id' => $section->id,
                'locale'     => $loc,
                'title'      => $title,
            ]);
        }

        return back()->with('success', __('admin.sections.subcategories.created'));
    }

}
