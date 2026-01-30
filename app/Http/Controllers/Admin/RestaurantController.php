<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Section;

class RestaurantController extends Controller
{
    private function capFirst(?string $v): ?string
    {
        $v = $v !== null ? trim($v) : null;
        if ($v === null || $v === '') return null;

        $first = mb_strtoupper(mb_substr($v, 0, 1));
        $rest  = mb_substr($v, 1);
        return $first.$rest;
    }

    private function cleanText(?string $v): ?string
    {
        if ($v === null) return null;
        $v = strip_tags($v);
        $v = trim($v);
        return $v === '' ? null : $v;
    }

    private function cleanPhone(?string $v): ?string
    {
        $v = $this->cleanText($v);
        if ($v === null) return null;

        $v = preg_replace('/[^\d+]/', '', $v);

        $digits = preg_replace('/\D/', '', $v);
        $digits = substr($digits, 0, 15);

        return $digits ? ('+'.$digits) : null;
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        abort_unless($user?->is_super_admin, 403);

        $restaurants = Restaurant::query()->orderBy('name')->paginate(25);
        return view('admin.restaurants.index', compact('restaurants'));
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        abort_unless($user?->is_super_admin, 403);

        return view('admin.restaurants.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->is_super_admin, 403);

        $data = $request->validate([
            // restaurant
            'name' => ['required', 'string', 'max:20', 'regex:/^[^\d<>]+$/u'],
            'template_key' => ['required', 'in:classic,fastfood,bar,services'],

            'phone' => ['nullable', 'string', 'regex:/^\+\d{1,15}$/'],
            'city' => ['nullable', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'street' => ['nullable', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'house_number' => ['nullable', 'string', 'regex:/^\d{1,3}[A-Za-z]?$/'],
            'postal_code' => ['nullable', 'string', 'regex:/^\d{5}$/'],

            // user (created вместе с рестораном)
            'user_name' => ['required', 'string', 'max:255', 'regex:/^[^<>]*$/u'],
            'user_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'max:255'],
        ]);

        // normalize
        $data['name'] = $this->capFirst($this->cleanText($data['name']));
        $data['city'] = $this->capFirst($this->cleanText($data['city'] ?? null));
        $data['street'] = $this->capFirst($this->cleanText($data['street'] ?? null));
        $data['house_number'] = $this->cleanText($data['house_number'] ?? null);
        $data['postal_code'] = $this->cleanText($data['postal_code'] ?? null);
        $data['phone'] = $this->cleanPhone($data['phone'] ?? null);

        $baseSlug = Str::slug($data['name']) ?: ('restaurant-'.time());
        $slug = $baseSlug;
        $i = 2;
        while (Restaurant::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i;
            $i++;
        }

        $restaurant = Restaurant::create([
            'name' => $data['name'],
            'slug' => $slug,
            'template_key' => $data['template_key'],
            'default_locale' => 'de',
            'enabled_locales' => ['de'],
            'is_active' => true,

            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'street' => $data['street'] ?? null,
            'house_number' => $data['house_number'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
        ]);

        // create restaurant user
        User::create([
            'name' => $this->cleanText($data['user_name']),
            'email' => $data['user_email'],
            'password' => Hash::make($data['password']),
            'restaurant_id' => $restaurant->id,
            'is_super_admin' => false,
        ]);

        // auto-select restaurant for superadmin session
        $request->session()->put('admin.restaurant_id', $restaurant->id);

        return redirect()
            ->route('admin.restaurants.edit', $restaurant)
            ->with('status', 'Restaurant created.');
    }

    public function edit(Request $request, Restaurant $restaurant): View
    {
        $user = $request->user();
        if (!$user?->is_super_admin) {
            abort_unless((int) $user->restaurant_id === (int) $restaurant->id, 403);
        }

        $isSuper = (bool)($user->is_super_admin ?? false);

        $restaurantUser = \App\Models\User::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('is_super_admin', false)
            ->orderBy('id')
            ->first();

        $adminLocale = session('admin_locale', app()->getLocale());
        $locales = $restaurant->enabled_locales ?: ['de'];

        // --- categories query (withTrashed for super admin) ---
        $catQuery = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order');

        if ($isSuper) {
            $catQuery->withTrashed();
        }

        $categories = $catQuery
            ->with([
                'translations',

                // children (subcategories)
                'children' => function ($q) use ($isSuper) {
                    if ($isSuper) $q->withTrashed();

                    $q->orderBy('sort_order')
                      ->with([
                          'translations',

                          // items of subcategory
                          'items' => function ($qi) use ($isSuper) {
                              if ($isSuper) $qi->withTrashed();
                              $qi->orderBy('sort_order')->with('translations');
                          },
                      ]);
                },

                // items of category
                'items' => function ($qi) use ($isSuper) {
                    if ($isSuper) $qi->withTrashed();
                    $qi->orderBy('sort_order')->with('translations');
                },
            ])
            ->get();

        // --- sort items внутри category/subcategory, учитывая meta flags ---
        $menuTree = $categories->map(function ($cat) {
            $cat->items = ($cat->items ?? collect())->sort(function ($a, $b) {
                $am = $a->meta ?? [];
                $bm = $b->meta ?? [];

                $an = (int)!empty($am['is_new']);
                $bn = (int)!empty($bm['is_new']);
                if ($an !== $bn) return $bn <=> $an;

                $ad = (int)!empty($am['dish_of_day']);
                $bd = (int)!empty($bm['dish_of_day']);
                if ($ad !== $bd) return $bd <=> $ad;

                return ((int)($a->sort_order ?? 0)) <=> ((int)($b->sort_order ?? 0));
            })->values();

            $cat->children = ($cat->children ?? collect())->map(function ($sub) {
                $sub->items = ($sub->items ?? collect())->sort(function ($a, $b) {
                    $am = $a->meta ?? [];
                    $bm = $b->meta ?? [];

                    $an = (int)!empty($am['is_new']);
                    $bn = (int)!empty($bm['is_new']);
                    if ($an !== $bn) return $bn <=> $an;

                    $ad = (int)!empty($am['dish_of_day']);
                    $bd = (int)!empty($bm['dish_of_day']);
                    if ($ad !== $bd) return $bd <=> $ad;

                    return ((int)($a->sort_order ?? 0)) <=> ((int)($b->sort_order ?? 0));
                })->values();

                return $sub;
            });

            return $cat;
        });

        return view('admin.restaurants.edit', [
            'restaurant' => $restaurant,
            'restaurantUser' => $restaurantUser,

            'menuTree' => $menuTree,
            'locales'  => $locales,
            'adminLocale' => $adminLocale,
        ]);
    }


    public function update(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $user = $request->user();
        if (!$user?->is_super_admin) {
            abort_unless((int) $user->restaurant_id === (int) $restaurant->id, 403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:20', 'regex:/^[^\d<>]+$/u'],
            'template_key' => ['required', 'in:classic,fastfood,bar,services'],

            'phone' => ['nullable', 'string', 'regex:/^\+\d{1,15}$/'],
            'city' => ['nullable', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'street' => ['nullable', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'house_number' => ['nullable', 'string', 'regex:/^\d{1,3}[A-Za-z]?$/'],
            'postal_code' => ['nullable', 'string', 'regex:/^\d{5}$/'],
        ]);

        // normalize
        $data['name'] = $this->capFirst($this->cleanText($data['name']));
        $data['city'] = $this->capFirst($this->cleanText($data['city'] ?? null));
        $data['street'] = $this->capFirst($this->cleanText($data['street'] ?? null));
        $data['house_number'] = $this->cleanText($data['house_number'] ?? null);
        $data['postal_code'] = $this->cleanText($data['postal_code'] ?? null);
        $data['phone'] = $this->cleanPhone($data['phone'] ?? null);

        $restaurant->fill($data)->save();

        return back()->with('status', 'Saved.');
    }

    public function destroy(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->is_super_admin, 403);

        $restaurant->is_active = false;
        $restaurant->save();

        return redirect()->route('admin.restaurants.index')->with('status', 'Restaurant deactivated.');
    }

    public function toggleActive(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->is_super_admin, 403);

        $restaurant->is_active = !$restaurant->is_active;
        $restaurant->save();

        return back()->with('status', $restaurant->is_active ? 'Restaurant activated.' : 'Restaurant deactivated.');
    }

    public function updateUserPermissions(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->is_super_admin, 403);

        $restaurantUser = \App\Models\User::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('is_super_admin', false)
            ->orderBy('id')
            ->firstOrFail();

        $keys = [
            'languages_manage',
            'sections_manage',
            'items_manage',
            'banners_manage',
            'socials_manage',
            'theme_manage',
            'branding_manage',
            'import_manage'
        ];

        $perms = $restaurantUser->permissions ?? [];

        foreach ($keys as $k) {
            $perms[$k] = (bool) $request->input("perm.$k", false);
        }

        $restaurantUser->permissions = $perms;
        $restaurantUser->save();

        return back()->with('status', 'User permissions saved.');
    }
}
