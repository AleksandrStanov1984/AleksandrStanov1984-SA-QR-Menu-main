<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\Restaurants\RestaurantDeletionService;
use App\Support\Guards\AccessGuardTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RestaurantDeletionController extends Controller
{
    use AccessGuardTrait;

    public function destroy(
        Request $request,
        Restaurant $restaurant,
        RestaurantDeletionService $deletionService
    ): RedirectResponse {

        $this->assertSuperAdmin($request);

        if (!$restaurant->canBePurged()) {
            return back()->with(
                'error',
                __('billing.delete.not_allowed')
            );
        }

        $deletionService->delete($restaurant);

        return redirect()
            ->route('admin.restaurants.index')
            ->with(
                'success',
                __('billing.delete.success')
            );
    }
}
