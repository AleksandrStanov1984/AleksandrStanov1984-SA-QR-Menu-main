<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Section;
use App\Support\Permissions;

class MenuBuilderController extends Controller
{
    public function index(Restaurant $restaurant)
    {
        $user = auth()->user();

        // tenant check
        if (!$user ||
            (
                !$user->is_super_admin &&
                (int)$user->restaurant_id !== (int)$restaurant->id
            )
        ) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        // permission check
        if (
            !Permissions::can($user, 'sections_manage') &&
            !Permissions::can($user, 'items_manage')
        ) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        $categories = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->with([
                'translations:id,section_id,locale,title',

                'children' => function ($q) {
                    $q->orderBy('sort_order')
                        ->select(['id', 'restaurant_id', 'parent_id', 'sort_order', 'is_active']);
                },

                'children.translations:id,section_id,locale,title',

                'children.items' => function ($q) {
                    $q->orderBy('sort_order')
                        ->select(['id', 'section_id', 'price', 'currency', 'is_active']);
                },

                'children.items.translations:id,item_id,locale,title',

                'items' => function ($q) {
                    $q->orderBy('sort_order')
                        ->select(['id', 'section_id', 'price', 'currency', 'is_active']);
                },

                'items.translations:id,item_id,locale,title',
            ])
            ->get([
                'id',
                'restaurant_id',
                'parent_id',
                'sort_order',
                'is_active',
            ]);

        return view('admin.restaurants.components.branding-backgrounds.index', [
            'restaurant' => $restaurant,
            'categories' => $categories,
        ]);
    }
}
