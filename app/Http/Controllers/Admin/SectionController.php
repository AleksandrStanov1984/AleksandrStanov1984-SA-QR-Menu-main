<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Restaurant;
use App\Models\Section;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Support\Permissions;

class SectionController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        $sections = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->with(['children' => function ($q) use ($restaurant) {
                $q->where('restaurant_id', $restaurant->id)
                  ->orderBy('sort_order');
            }])
            ->get();

        $allParents = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('sort_order')
            ->get();

        return view('admin.sections.index', compact('restaurant', 'sections', 'allParents'));
    }

    public function update(Request $request, Restaurant $restaurant, Section $section)
    {
        // Permissions::abortUnless(auth()->user(), 'categories.edit');

        $this->assertRestaurantAccess($request, $restaurant, 'sections_manage');

        // category or subcategory edit permission (определяем по parent_id)
        if (is_null($section->parent_id)) {
            Permissions::abortUnless($request->user(), 'categories.edit');
        } else {
            Permissions::abortUnless($request->user(), 'subcategories.edit');
        }

        // legacy guard (на время перехода)
        if (!$request->user()->is_super_admin && !$request->user()->hasPerm('sections_manage')) {
            abort(403);
        }

        abort_unless((int)$section->restaurant_id === (int)$restaurant->id, 404);

        // ВАЖНО: здесь мы оставляем твою текущую логику update по sections,
        // но дальше (на этапе edit modal) мы будем обновлять translations так же, как в create.
        // Сейчас — минимум чтобы не падало.
        $data = $request->validate([
            'is_active'   => ['nullable', 'boolean'],
            'title_font'  => ['nullable', 'string', 'max:50'],
            'title_color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
            'title'       => ['nullable', 'array'], // title[locale]
        ]);

        // Обновляем только поля section
        if (array_key_exists('is_active', $data)) {
            $section->is_active = (bool)$data['is_active'];
        }
        $section->title_font  = $data['title_font'] ?? $section->title_font;
        $section->title_color = $data['title_color'] ?? $section->title_color;
        $section->save();

        // Обновляем translations, если пришли
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

                $tr = $section->translations()->firstOrNew(['locale' => $loc]);
                $tr->title = $title;
                $tr->save();
            }
        }

        return back()->with('success', __('admin.common.saved') ?? 'Saved');
    }

    public function toggleActive(Request $request, Restaurant $restaurant, Section $section)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'sections_manage');
        abort_unless((int)$section->restaurant_id === (int)$restaurant->id, 404);

        $section->is_active = !$section->is_active;
        $section->save();

        return back()->with('success', __('admin.sections.toggled'));
    }

    public function destroy(Request $request, Restaurant $restaurant, Section $section)
    {
        $this->assertRestaurantAccess($request, $restaurant, 'sections_manage');
        abort_unless((int)$section->restaurant_id === (int)$restaurant->id, 404);

        $user = $request->user();
        Permissions::abortUnless($user, 'sections.delete'); // ключ права на удаление секций

        DB::transaction(function () use ($section, $restaurant, $user) {

            // Если это категория (top-level) — удалить подкатегории и их items + items категории
            if (is_null($section->parent_id)) {

                // items категории
                $items = \App\Models\Item::query()
                    ->where('section_id', $section->id)
                    ->get();

                foreach ($items as $it) {
                    $it->deleted_by_user_id = $user->id ?? null;
                    $it->save();
                    $it->delete();
                }

                // подкатегории (children)
                $children = Section::query()
                    ->where('restaurant_id', $restaurant->id)
                    ->where('parent_id', $section->id)
                    ->get();

                foreach ($children as $child) {

                    // items подкатегории
                    $childItems = \App\Models\Item::query()
                        ->where('section_id', $child->id)
                        ->get();

                    foreach ($childItems as $it) {
                        $it->deleted_by_user_id = $user->id ?? null;
                        $it->save();
                        $it->delete();
                    }

                    // soft delete подкатегории
                    $child->deleted_by_user_id = $user->id ?? null;
                    $child->save();
                    $child->delete();
                }

                // soft delete категории
                $section->deleted_by_user_id = $user->id ?? null;
                $section->save();
                $section->delete();
                return;
            }

            // Иначе это подкатегория — удалить её items и её саму
            $items = \App\Models\Item::query()
                ->where('section_id', $section->id)
                ->get();

            foreach ($items as $it) {
                $it->deleted_by_user_id = $user->id ?? null;
                $it->save();
                $it->delete();
            }

            $section->deleted_by_user_id = $user->id ?? null;
            $section->save();
            $section->delete();
        });

        return back()->with('success', __('admin.sections.deleted') ?? 'Deleted');
    }

    private function assertRestaurantAccess(Request $request, Restaurant $restaurant, ?string $perm = null): void
    {
        $user = $request->user();

        if (!$user->is_super_admin && (int)$user->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }

        if ($perm && !$user->is_super_admin && !$user->hasPerm($perm)) {
            abort(403);
        }
    }
}
