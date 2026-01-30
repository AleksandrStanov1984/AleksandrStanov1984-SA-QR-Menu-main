<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Section;
use App\Models\SectionTranslation;
use App\Http\Requests\Admin\StoreCategoryRequest;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(StoreCategoryRequest $request, Restaurant $restaurant)
    {
        $user = $request->user();

        // безопасность: user может работать только со своим рестораном
        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }

        // permissions (пока can=true, но проверка обязательна)
        Permissions::abortUnless($user, 'categories.create');

        if (!$user->is_super_admin && !$user->hasPerm('sections_manage')) {
            abort(403);
        }

        // какие языки активны у ресторана
        $locales = $restaurant->enabled_locales ?: ['de'];
        $defaultLocale = $restaurant->default_locale ?: 'de';
        if (!in_array($defaultLocale, $locales, true)) {
            $defaultLocale = $locales[0] ?? 'de';
        }

        $data = $request->validated();

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
            $title = trim((string) Arr::get($data, "title.$loc", ''));

            // fallback: если где-то пусто — берём дефолтный
            if ($title === '') {
                $title = trim((string) Arr::get($data, "title.$defaultLocale", ''));
            }

            SectionTranslation::create([
                'section_id' => $section->id,
                'locale'     => $loc,
                'title'      => $title,
            ]);
        }

        return back()->with('success', __('admin.sections.categories.created'));
    }
}
