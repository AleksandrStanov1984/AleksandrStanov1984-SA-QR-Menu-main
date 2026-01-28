<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SectionController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        // Верхний уровень (категории)
        $sections = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->with(['children' => function ($q) use ($restaurant) {
                $q->where('restaurant_id', $restaurant->id)
                  ->orderBy('sort_order')
                  ->with('children'); // достаточно для 2 уровней; ниже отрисуем рекурсивно
            }])
            ->get();

        // Для селекта parent при создании подкатегории
        $allParents = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->orderBy('sort_order')
            ->get();

        return view('admin.sections.index', compact('restaurant', 'sections', 'allParents'));
    }

    public function store(Request $request, Restaurant $restaurant)
    {
        $data = $request->validate([
            'parent_id'   => ['nullable', 'integer', 'exists:sections,id'],
            'key'         => ['nullable', 'string', 'max:128'],
            'type'        => ['nullable', 'string', 'max:50'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        // parent должен принадлежать этому же ресторану
        if (!empty($data['parent_id'])) {
            $parent = Section::query()->where('id', $data['parent_id'])
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail();
        }

        // Если key не передали — делаем из title
        $key = $data['key'] ?? Str::slug($data['title']);
        $key = $key ?: Str::random(8);

        $section = new Section();
        $section->restaurant_id = $restaurant->id;
        $section->parent_id = $data['parent_id'] ?? null;
        $section->key = $key;
        $section->type = $data['type'] ?? 'default';
        $section->sort_order = $data['sort_order'] ?? 0;
        $section->is_active = true; // у тебя может называться иначе
        $section->save();

        // Переводы/тексты — если у тебя уже есть таблица translations:
        // Здесь я намеренно не лезу глубже, чтобы не сломать текущую модель.
        // Сохраним title/description в полях Section, если они есть.
        if (property_exists($section, 'title')) {
            $section->title = $data['title'];
        }
        if (property_exists($section, 'description')) {
            $section->description = $data['description'] ?? null;
        }
        $section->save();

        return redirect()
            ->route('admin.restaurants.sections.index', $restaurant)
            ->with('success', 'Section created');
    }

    public function edit(Restaurant $restaurant, Section $section)
    {
        abort_unless($section->restaurant_id === $restaurant->id, 404);

        $parents = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('id', '!=', $section->id)
            ->orderBy('sort_order')
            ->get();

        return view('admin.sections.edit', compact('restaurant', 'section', 'parents'));
    }

    public function update(Request $request, Restaurant $restaurant, Section $section)
    {
        abort_unless($section->restaurant_id === $restaurant->id, 404);

        $data = $request->validate([
            'parent_id'   => ['nullable', 'integer', 'exists:sections,id'],
            'key'         => ['nullable', 'string', 'max:128'],
            'type'        => ['nullable', 'string', 'max:50'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        if (!empty($data['parent_id'])) {
            // parent должен быть в этом ресторане и не быть самим собой
            abort_if((int)$data['parent_id'] === (int)$section->id, 422);
            Section::query()
                ->where('id', $data['parent_id'])
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail();
        }

        $section->parent_id = $data['parent_id'] ?? null;
        if (!empty($data['key'])) {
            $section->key = $data['key'];
        }
        $section->type = $data['type'] ?? $section->type;
        $section->sort_order = $data['sort_order'] ?? $section->sort_order;

        if (property_exists($section, 'title')) {
            $section->title = $data['title'];
        }
        if (property_exists($section, 'description')) {
            $section->description = $data['description'] ?? null;
        }

        $section->save();

        return redirect()
            ->route('admin.restaurants.sections.index', $restaurant)
            ->with('success', 'Section updated');
    }

    public function toggleActive(Restaurant $restaurant, Section $section)
    {
        abort_unless($section->restaurant_id === $restaurant->id, 404);

        // is_active у тебя может называться active/status — подправишь под свою схему.
        $section->is_active = !$section->is_active;
        $section->save();

        return back()->with('success', __('admin.sections.toggled'));
    }

    public function destroy(Restaurant $restaurant, Section $section)
    {
        abort_unless($section->restaurant_id === $restaurant->id, 404);

        // delete children and items (safe MVP)
        $childIds = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('parent_id', $section->id)
            ->pluck('id')
            ->all();

        if (!empty($childIds)) {
            \App\Models\Item::query()->whereIn('section_id', $childIds)->delete();
            Section::query()->whereIn('id', $childIds)->delete();
        }

        \App\Models\Item::query()->where('section_id', $section->id)->delete();
        $section->delete();

        return back()->with('success', __('admin.sections.deleted'));
    }


}
