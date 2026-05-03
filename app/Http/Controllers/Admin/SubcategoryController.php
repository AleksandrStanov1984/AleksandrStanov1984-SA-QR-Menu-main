<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubcategoryRequest;

use App\Models\Restaurant;
use App\Models\Section;
use App\Models\SectionTranslation;

use App\Services\SectionPositionService\SectionPositionService;

use App\Support\Guards\AccessGuardTrait;

use Illuminate\Support\Arr;

class SubcategoryController extends Controller
{
    use AccessGuardTrait;

    /**
     * @throws TenantAccessException
     */
    public function store(StoreSubcategoryRequest $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $data = $request->validated();

        // =========================
        // LOCALES
        // =========================
        $locales = $restaurant->enabled_locales ?: ['de'];
        $defaultLocale = $restaurant->default_locale ?: 'de';

        if (!in_array($defaultLocale, $locales, true)) {
            $defaultLocale = $locales[0] ?? 'de';
        }

        // =========================
        // PARENT CHECK
        // =========================
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

        // =========================
        // CREATE
        // =========================
        $section = Section::create([
            'restaurant_id' => $restaurant->id,
            'parent_id'     => $parent->id,
            'type'          => 'subcategory',
            'sort_order'    => 9999, // важно для PositionService
            'is_active'     => (bool)($data['is_active'] ?? true),
            'title_font'    => $data['title_font'] ?? null,
            'title_color'   => $data['title_color'] ?? null,
        ]);

        // =========================
        // TRANSLATIONS
        // =========================
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

        // =========================
        // POSITION LOGIC
        // =========================
        $mode = $data['position_mode'] ?? 'end';
        $targetId = $data['target_id'] ?? null;

        app(SectionPositionService::class)->apply(
            section: $section,
            mode: $mode,
            targetId: $targetId
        );

        // =========================
        // RESPONSE
        // =========================
        return redirect()
            ->back()
            ->with('status', __('admin.sections.subcategories.created'))
            ->with('scroll_to_section', $section->id);
    }
}
