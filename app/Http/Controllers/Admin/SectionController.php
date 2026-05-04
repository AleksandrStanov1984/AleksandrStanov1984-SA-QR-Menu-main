<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;

use App\Http\Controllers\Controller;

use App\Models\Restaurant;
use App\Models\Section;
use App\Models\Item;

use App\Services\SectionPositionService\SectionPositionService;

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
            'title'         => ['required', 'string', 'max:255'],
            'parent_id'     => ['nullable', 'integer'],
            'position_mode' => ['nullable', 'in:keep,start,end,before,after'],
            'target_id'     => ['nullable', 'integer'],
        ]);

        $parentId = $data['parent_id'] ?? null;

        if ($parentId) {
            $parent = Section::query()
                ->where('restaurant_id', $restaurant->id)
                ->whereKey($parentId)
                ->first();

            if (!$parent) {
                throw new TenantAccessException(__('admin.errors.not_found'));
            }
        }

        $section = Section::create([
            'restaurant_id' => $restaurant->id,
            'parent_id'     => $parentId,
            'sort_order'    => 9999,
            'is_active'     => true,
        ]);

        $section->translations()->create([
            'locale' => $restaurant->default_locale ?? 'de',
            'title'  => $data['title'],
        ]);

        // =========================
        // POSITION
        // =========================
        $mode = $data['position_mode'] ?? 'end';
        $targetId = $data['target_id'] ?? null;

        if (in_array($mode, ['before', 'after'], true) && !$targetId) {
            return back()->withErrors([
                'position' => __('admin.validation.target_required')
            ]);
        }

        if ($targetId) {
            $target = Section::query()
                ->where('restaurant_id', $restaurant->id)
                ->where('parent_id', $parentId)
                ->whereKey($targetId)
                ->first();

            if (!$target) {
                throw new TenantAccessException(__('admin.errors.not_found'));
            }
        }

        app(SectionPositionService::class)->apply(
            section: $section,
            mode: $mode,
            targetId: $targetId
        );

        return redirect()
            ->back()
            ->with('status', 'Section created')
            ->with('scroll_to_section', $section->id);
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
            'is_active'     => ['nullable', 'boolean'],
            'position_mode' => ['nullable', 'in:keep,start,end,before,after'],
            'target_id'     => ['nullable', 'integer'],
            'title_font'    => ['nullable', 'string', 'max:50'],
            'title_color'   => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'title'         => ['nullable', 'array'],
        ]);

        // =========================
        // SIMPLE FIELDS
        // =========================
        if (array_key_exists('is_active', $data)) {
            $section->is_active = (bool)$data['is_active'];
        }

        if (array_key_exists('title_font', $data)) {
            $section->title_font = $data['title_font'];
        }

        if (array_key_exists('title_color', $data)) {
            $section->title_color = $data['title_color'];
        }

        $section->save();

        // =========================
        // TRANSLATIONS
        // =========================
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

        // =========================
        // POSITION SAFE
        // =========================
        $mode = $data['position_mode'] ?? null;

        if ($mode && $mode !== 'keep') {

            $targetId = $data['target_id'] ?? null;

            if (in_array($mode, ['before', 'after'], true) && !$targetId) {
                return back()->withErrors([
                    'position' => __('admin.validation.target_required')
                ]);
            }

            if ($targetId) {
                $target = Section::query()
                    ->where('restaurant_id', $restaurant->id)
                    ->where('parent_id', $section->parent_id)
                    ->whereKey($targetId)
                    ->first();

                if (!$target) {
                    throw new TenantAccessException(__('admin.errors.not_found'));
                }
            }

            app(SectionPositionService::class)->apply(
                section: $section,
                mode: $mode,
                targetId: $targetId
            );
        }

        return back()
            ->with('status', __('admin.common.saving'))
            ->with('scroll_to_section', $section->id);
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

        $section->forceDelete();

        return response()->json([
            'deleted_id' => $section->id,
            'message' => __('admin.sections.deleted'),
        ]);
    }
}
