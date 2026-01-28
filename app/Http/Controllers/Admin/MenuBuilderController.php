<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Section;
use App\Models\Item;

class MenuBuilderController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        // permission check (пока локально, позже вынесем в gate)
        if (!auth()->user()->can('sections_manage') && !auth()->user()->can('items_manage')) {
            abort(403);
        }

        $categories = Section::query()
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

        return view('admin.restaurants.menu-builder.index', [
            'restaurant' => $restaurant,
            'categories' => $categories,
        ]);
    }
}
