<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Section;
use App\Models\SectionTranslation;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Support\Permissions;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(StoreCategoryRequest $request, Restaurant $restaurant)
    {
        $user = $request->user();

        // безопасность: user может работать только со своим
        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }

        if ($resp = Permissions::denyRedirect(auth()->user(), 'categories.create')) {
            return $resp;
        }

        if (!$user->is_super_admin && !$user->hasPerm('sections_manage')) {
            abort(403);
        }

        // языки
        $locales = $restaurant->enabled_locales ?: ['de'];
        $defaultLocale = $restaurant->default_locale ?: 'de';

        if (!in_array($defaultLocale, $locales, true)) {
            $defaultLocale = $locales[0] ?? 'de';
        }

        $data = $request->validated();
        $titles = $data['title'] ?? [];

        $lastSort = Section::where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->orderByDesc('sort_order')
            ->value('sort_order');

        $nextSort = $lastSort ? $lastSort + 1 : 1;

        $section = Section::create([
            'restaurant_id' => $restaurant->id,
            'parent_id'     => null,
            'type'          => 'category',
            'sort_order'    => $nextSort,
            'is_active'     => (bool)($data['is_active'] ?? true),
            'title_font'    => $data['title_font'] ?? null,
            'title_color'   => $data['title_color'] ?? null,
        ]);

        // bulk insert переводов
        $translations = [];

        foreach ($locales as $loc) {
            $title = trim((string) ($titles[$loc] ?? ''));

            if ($title === '') {
                $title = trim((string) ($titles[$defaultLocale] ?? ''));
            }

            $translations[] = [
                'section_id' => $section->id,
                'locale'     => $loc,
                'title'      => $title,
            ];
        }

        SectionTranslation::insert($translations);

        return back()->with('success', __('admin.sections.categories.created'));
    }
}
