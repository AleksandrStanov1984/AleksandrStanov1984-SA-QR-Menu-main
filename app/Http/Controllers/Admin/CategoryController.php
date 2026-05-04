<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;

use App\Models\Restaurant;
use App\Models\Section;
use App\Models\SectionTranslation;

use App\Services\SectionPositionService\SectionPositionService;

use App\Support\Guards\AccessGuardTrait;
use App\Support\Permissions;

class CategoryController extends Controller
{
    use AccessGuardTrait;

    /**
     * @throws TenantAccessException
     */
    public function store(StoreCategoryRequest $request, Restaurant $restaurant)
    {
        $this->assertRestaurantAccess($request, $restaurant);

        if ($resp = Permissions::denyRedirect($request->user(), 'categories.create')) {
            return $resp;
        }

        $data = $request->validated();

        // =========================
        // LOCALES
        // =========================
        $locales = $restaurant->enabled_locales ?: ['de'];
        $defaultLocale = $restaurant->default_locale ?: 'de';

        if (!in_array($defaultLocale, $locales, true)) {
            $defaultLocale = $locales[0] ?? 'de';
        }

        $titles = $data['title'] ?? [];

        // =========================
        // CREATE
        // =========================
        $section = Section::create([
            'restaurant_id' => $restaurant->id,
            'parent_id'     => null,
            'type'          => 'category',
            'sort_order'    => 9999,
            'is_active'     => (bool)($data['is_active'] ?? true),
            'title_font'    => $data['title_font'] ?? null,
            'title_color'   => $data['title_color'] ?? null,
        ]);

        // =========================
        // TRANSLATIONS
        // =========================
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

        // =========================
        // POSITION LOGIC
        // =========================
        $hasPosition = $request->has('position_mode');

        if ($hasPosition) {

            $mode = $data['position_mode'] ?? 'end';
            $targetId = $data['target_id'] ?? null;

            app(SectionPositionService::class)->apply(
                section: $section,
                mode: $mode,
                targetId: $targetId
            );

        } else {
            app(SectionPositionService::class)->apply(
                section: $section,
                mode: 'end',
                targetId: null
            );
        }

        // =========================
        // RESPONSE
        // =========================
        return redirect()
            ->back()
            ->with('status', __('admin.sections.categories.created'))
            ->with('scroll_to_section', $section->id);
    }
}
