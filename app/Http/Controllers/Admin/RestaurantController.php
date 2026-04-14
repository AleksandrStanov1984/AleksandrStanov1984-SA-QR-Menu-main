<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;
use App\Http\Controllers\Controller;

use App\Models\MenuPlan;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Section;
use App\Models\RestaurantSocialLink;
use App\Models\MenuTemplate;

use App\Support\Guards\AccessGuardTrait;
use App\Support\Permissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class RestaurantController extends Controller
{
    use AccessGuardTrait;

    private function capFirst(?string $v): ?string
    {
        $v = $v !== null ? trim($v) : null;
        if ($v === null || $v === '') return null;

        return mb_strtoupper(mb_substr($v, 0, 1)) . mb_substr($v, 1);
    }

    private function cleanText(?string $v): ?string
    {
        if ($v === null) return null;
        $v = trim(strip_tags($v));
        return $v === '' ? null : $v;
    }

    private function cleanPhone(?string $v): ?string
    {
        $v = $this->cleanText($v);
        if ($v === null) return null;

        $v = preg_replace('/[^\d+]/', '', $v);
        $digits = substr(preg_replace('/\D/', '', $v), 0, 15);

        return $digits ? ('+' . $digits) : null;
    }

    public function index(Request $request): View
    {
        $this->assertSuperAdmin($request);

        $restaurants = Restaurant::orderBy('name')->paginate(25);

        return view('admin.restaurants.index', compact('restaurants'));
    }

    /**
     * @throws TenantAccessException
     */
    public function create(Request $request): View
    {
        $this->assertSuperAdmin($request);

        return view('admin.restaurants.create', [
            'templates' => MenuTemplate::where('is_active', true)->orderBy('sort_order')->get(),
            'plans' => MenuPlan::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->assertSuperAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:20', 'regex:/^[^\d<>]+$/u'],
            'template_key' => ['required', 'exists:menu_templates,key'],
            'plan_key' => ['required', 'exists:menu_plans,key'],

            'phone' => ['nullable', 'string', 'regex:/^\+\d{1,15}$/'],
            'city' => ['nullable', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'street' => ['nullable', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'house_number' => ['nullable', 'string', 'regex:/^\d{1,3}[A-Za-z]?$/'],
            'postal_code' => ['nullable', 'string', 'regex:/^\d{5}$/'],

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

        $baseSlug = Str::slug($data['name']) ?: ('restaurant-' . time());
        $existing = Restaurant::where('slug', 'like', "{$baseSlug}%")->pluck('slug');

        $slug = $baseSlug;
        $i = 2;

        while ($existing->contains($slug)) {
            $slug = "{$baseSlug}-{$i}";
            $i++;
        }

        $restaurant = Restaurant::create([
            'name' => $data['name'],
            'slug' => $slug,
            'template_key' => $data['template_key'],
            'plan_key' => $data['plan_key'],
            'default_locale' => 'de',
            'enabled_locales' => ['de'],
            'is_active' => true,

            'phone' => $data['phone'],
            'city' => $data['city'],
            'street' => $data['street'],
            'house_number' => $data['house_number'],
            'postal_code' => $data['postal_code'],
        ]);

        $restaurant->token()->firstOrCreate(
            ['restaurant_id' => $restaurant->id],
            ['token' => Str::random(10)]
        );

        $restaurant->qr()->firstOrCreate(
            ['restaurant_id' => $restaurant->id],
            [
                'qr_path' => null,
                'logo_path' => null,
                'background_path' => null,
                'settings' => [],
            ]
        );

        User::create([
            'name' => $this->cleanText($data['user_name']),
            'email' => $data['user_email'],
            'password' => Hash::make($data['password']),
            'restaurant_id' => $restaurant->id,
            'is_super_admin' => false,
        ]);

        $request->session()->put('admin.restaurant_id', $restaurant->id);

        return redirect()
            ->route('admin.restaurants.edit', $restaurant)
            ->with('status', 'Restaurant created.');
    }

    /**
     * @throws TenantAccessException
     */
    public function edit(Request $request, Restaurant $restaurant): View
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $user = $request->user();
        $isSuper = $user->is_super_admin;

        $socialLinksQuery = $restaurant->socialLinks()->orderBy('sort_order');

        $socialLinks = $isSuper
            ? $socialLinksQuery->withTrashed()->get()
            : $socialLinksQuery->whereNull('deleted_at')->get();

        $restaurantUser = User::where('restaurant_id', $restaurant->id)
            ->where('is_super_admin', false)
            ->first();

        $categories = Section::where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->when($isSuper, fn($q) => $q->withTrashed())
            ->with([
                'translations:id,section_id,locale,title',
                'children.translations:id,section_id,locale,title',
                'children.items.translations:id,item_id,locale,title,description',
                'items.translations:id,item_id,locale,title,description',
            ])
            ->get();

        return view('admin.restaurants.edit', [
            'restaurant' => $restaurant,
            'restaurantUser' => $restaurantUser,
            'menuTree' => $this->buildMenuTree($categories),
            'locales' => $restaurant->enabled_locales ?: ['de'],
            'adminLocale' => session('admin_locale', app()->getLocale()),
            'socialLinks' => $socialLinks,
            'templates' => MenuTemplate::where('is_active', true)->orderBy('sort_order')->get(),
            'plans' => MenuPlan::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }

    private function buildMenuTree($categories)
    {
        return $categories->map(function ($cat) {

            $cat->items = $cat->items
                ->sortByDesc(fn($i) => !empty($i->meta['is_new']))
                ->sortByDesc(fn($i) => !empty($i->meta['dish_of_day']))
                ->sortBy('sort_order')
                ->values();

            $cat->children = $cat->children->map(function ($sub) {

                $sub->items = $sub->items
                    ->sortByDesc(fn($i) => !empty($i->meta['is_new']))
                    ->sortByDesc(fn($i) => !empty($i->meta['dish_of_day']))
                    ->sortBy('sort_order')
                    ->values();

                return $sub;
            });

            return $cat;
        });
    }

    /**
     * @throws TenantAccessException
     */
    public function updateUserPermissions(Request $request, Restaurant $restaurant)
    {
        $this->assertSuperAdmin($request);

        $restaurantUser = User::where('restaurant_id', $restaurant->id)->firstOrFail();

        $incoming = Permissions::normalize($request->input('perm', []));

        $meta = $restaurantUser->meta ?? [];
        $existing = $meta['permissions'] ?? [];

        $meta['permissions'] = array_replace($existing, $incoming);

        $restaurantUser->meta = $meta;
        $restaurantUser->save();

        return back()->with('status', __('admin.permissions.saved') ?? 'Saved');
    }

    /**
     * @throws TenantAccessException
     */
    public function menu(Request $request, Restaurant $restaurant): View
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $user = $request->user();
        $isSuper = (bool) ($user->is_super_admin ?? false);

        $adminLocale = session('admin_locale', app()->getLocale());
        $locales = $restaurant->enabled_locales ?: ['de'];

        $catQuery = Section::query()
            ->where('restaurant_id', $restaurant->id)
            ->whereNull('parent_id')
            ->orderBy('sort_order');

        if ($isSuper) {
            $catQuery->withTrashed();
        }

        $categories = $catQuery
            ->with([
                'translations:id,section_id,locale,title',

                'children' => function ($q) use ($isSuper) {
                    if ($isSuper) $q->withTrashed();

                    $q->orderBy('sort_order')
                        ->with([
                            'translations:id,section_id,locale,title',

                            'items' => function ($qi) use ($isSuper) {
                                if ($isSuper) $qi->withTrashed();

                                $qi->orderBy('sort_order')
                                    ->with([
                                        'translations:id,item_id,locale,title,description,details'
                                    ]);
                            },
                        ]);
                },

                'items' => function ($qi) use ($isSuper) {
                    if ($isSuper) $qi->withTrashed();

                    $qi->orderBy('sort_order')
                        ->with([
                            'translations:id,item_id,locale,title,description,details'
                        ]);
                },
            ])
            ->get();

        $menuTree = $this->buildMenuTree($categories);

        return view('admin.restaurants.menu', [
            'restaurant' => $restaurant,
            'menuTree' => $menuTree,
            'locales' => $locales,
            'adminLocale' => $adminLocale,
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function profile(Request $request, Restaurant $restaurant): View
    {
        $this->assertRestaurantAccess($request, $restaurant);

        $authUser = $request->user();

        if ($authUser->is_super_admin) {
            $user = $restaurant->users()->first();

        } else {
            $user = $authUser;
        }

        $templates = MenuTemplate::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $plans = MenuPlan::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('admin.restaurants.profile', [
            'restaurant' => $restaurant,
            'user' => $user,
            'templates' => $templates,
            'plans' => $plans,
            'profileMode' => 'restaurant',
        ]);
    }

    public function permissions(Request $request, Restaurant $restaurant): View
    {
        $user = $request->user();

        //abort_unless($user?->is_super_admin, 403);

        $restaurantUser = User::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('is_super_admin', false)
            ->firstOrFail();

        return view('admin.restaurants.permissions', [
            'restaurant' => $restaurant,
            'restaurantUser' => $restaurantUser,
            'groupedPermissions' => Permissions::groupedRegistry(),
            'currentPermissions' => $restaurantUser->meta['permissions'] ?? [],
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function destroy(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $this->assertSuperAdmin($request);

        $ctx = \App\Support\AdminContext::actingRestaurant();

        if (!$ctx || $ctx->id !== $restaurant->id) {
            abort(404);
        }

        $restaurant->update([
            'is_active' => false,
        ]);

        return redirect()
            ->route('admin.restaurants.index')
            ->with('status', 'Restaurant deactivated.');
    }

    /**
     * @throws TenantAccessException
     */
    public function toggleActive(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $this->assertSuperAdmin($request);

        $ctx = \App\Support\AdminContext::actingRestaurant();

        if (!$ctx || $ctx->id !== $restaurant->id) {
            abort(404);
        }

        $restaurant->update([
            'is_active' => !$restaurant->is_active,
        ]);

        return back()->with(
            'status',
            $restaurant->is_active
                ? 'Restaurant activated.'
                : 'Restaurant deactivated.'
        );
    }

    /**
     * @throws TenantAccessException
     */
    public function update(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $user = $request->user();

        Permissions::abortUnless($user, 'restaurants.edit');

        $this->assertRestaurantAccess($request, $restaurant);

        $isSuper = (bool) ($user?->is_super_admin ?? false);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:20', 'regex:/^[^\d<>]+$/u'],

            'template_key' => [
                $isSuper ? 'required' : 'nullable',
                'exists:menu_templates,key'
            ],

            'plan_key' => [
                $isSuper ? 'required' : 'nullable',
                'exists:menu_plans,key'
            ],

            'phone' => ['nullable', 'string', 'regex:/^\+\d{1,15}$/'],
            'city' => ['nullable', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'street' => ['nullable', 'string', 'max:50', 'regex:/^[^<>]*$/u'],
            'house_number' => ['nullable', 'string', 'regex:/^\d{1,3}[A-Za-z]?$/'],
            'postal_code' => ['nullable', 'string', 'regex:/^\d{5}$/'],
        ]);

        // Tenant users не могут менять plan/template
        if (!$isSuper) {
            unset($data['template_key'], $data['plan_key']);
        }

        $data['name'] = $this->capFirst($this->cleanText($data['name']));
        $data['city'] = $this->capFirst($this->cleanText($data['city'] ?? null));
        $data['street'] = $this->capFirst($this->cleanText($data['street'] ?? null));
        $data['house_number'] = $this->cleanText($data['house_number'] ?? null);
        $data['postal_code'] = $this->cleanText($data['postal_code'] ?? null);
        $data['phone'] = $this->cleanPhone($data['phone'] ?? null);

        $restaurant->update($data);

        return back()->with('status', 'Saved.');
    }

}
