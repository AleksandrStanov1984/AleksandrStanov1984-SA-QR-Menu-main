<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Support\Guards\AccessGuardTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use AccessGuardTrait;

    public function index(Request $request): View
    {
        $user = $request->user();
        $currentRestaurant = $request->attributes->get('admin_restaurant');

        $restaurants = collect();

        if ($user->is_super_admin) {
            $restaurants = Restaurant::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
        }

        return view('admin.dashboard.index', [
            'user' => $user,
            'currentRestaurant' => $currentRestaurant,
            'restaurants' => $restaurants,
        ]);
    }

    /**
     * @throws TenantAccessException
     */
    public function selectRestaurant(Request $request): RedirectResponse
    {
        $this->assertSuperAdmin($request);

        $user = $request->user();

        $data = $request->validate([
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
        ]);

        $request->session()->put('admin.restaurant_id', (int) $data['restaurant_id']);

        return redirect()->route('admin.restaurants.edit', $data['restaurant_id']);
    }
}
