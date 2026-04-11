<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Section;
use App\Models\SectionTranslation;
use App\Http\Requests\Admin\StoreSubcategoryRequest;
use App\Support\Guards\AccessGuardTrait;
use Illuminate\Support\Arr;
use App\Support\Permissions;

class SubcategoryController extends Controller
{
    use AccessGuardTrait;

    /**
     * @throws TenantAccessException
     */
    public function store(StoreSubcategoryRequest $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $locales = $restaurant->enabled_locales ?: ['de'];
        $defaultLocale = $restaurant->default_locale ?: 'de';

        if (!in_array($defaultLocale, $locales, true)) {
            $defaultLocale = $locales[0] ?? 'de';
        }

        $data = $request->validated();

        $parent = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereKey((int)$data['parent_id'])
            ->first();

        if (!$parent) {
            throw new TenantAccessException(__('admin.errors.not_found'));
        }

        if (!$parent->isCategory()) {
            throw new TenantAccessException(__('admin.validation.parent_must_be_category'));
        }

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

        $fallbackTitle = trim((string) Arr::get($data, "title.$defaultLocale", ''));

        foreach ($locales as $loc) {
            $title = trim((string) Arr::get($data, "title.$loc", ''));

            if ($title === '') {
                $title = $fallbackTitle;
            }

            SectionTranslation::create([
                'section_id' => $section->id,
                'locale'     => $loc,
                'title'      => $title,
            ]);
        }

        return back()->with('status', __('admin.sections.subcategories.created'));
    }
}
