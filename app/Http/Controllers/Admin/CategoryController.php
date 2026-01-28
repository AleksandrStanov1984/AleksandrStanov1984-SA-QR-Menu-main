<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Section;
use App\Models\SectionTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CategoryController extends Controller
{
    public function store(Request $request, Restaurant $restaurant)
    {
        $user = $request->user();

        // безопасность: user может работать только со своим рестораном
                $user = $request->user();
                if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
                    abort(403);
                }

        if (!$user->is_super_admin && !$user->hasPerm('sections_manage')) {
            abort(403);
        }

        // какие языки активны у ресторана
        $locales = $restaurant->enabled_locales ?: ['de'];
        $defaultLocale = $restaurant->default_locale ?: 'de';
        if (!in_array($defaultLocale, $locales, true)) {
            $defaultLocale = $locales[0] ?? 'de';
        }

        // валидация: title max 50
        $rules = [
            'title_font'  => ['nullable', 'string', 'max:50'],
            'title_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'is_active'   => ['nullable', 'boolean'],
        ];

        foreach ($locales as $loc) {
            $rules["title.$loc"] = ['required', 'string', 'max:50'];
        }

        $data = $request->validate($rules);

        // sort_order следующий
        $nextSort = (int) Section::where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->max('sort_order');
        $nextSort = $nextSort ? $nextSort + 1 : 1;

        $section = Section::create([
            'restaurant_id' => $restaurant->id,
            'parent_id'     => null,
            'type'          => 'category',
            'sort_order'    => $nextSort,
            'is_active'     => (bool)($data['is_active'] ?? true),
            'title_font'    => $data['title_font'] ?? null,
            'title_color'   => $data['title_color'] ?? null,
            // key можно генерить позже отдельной логикой, если нужно
        ]);

        foreach ($locales as $loc) {
            SectionTranslation::create([
                'section_id' => $section->id,
                'locale'     => $loc,
                'title'      => Arr::get($data, "title.$loc"),
            ]);
        }

        return back()->with('success', __('admin.sections.categories.created'));
    }
}
