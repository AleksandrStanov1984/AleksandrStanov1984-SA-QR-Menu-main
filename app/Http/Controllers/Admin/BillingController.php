<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\TenantAccessException;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Services\BillingService\BillingService;
use App\Support\Guards\AccessGuardTrait;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    use AccessGuardTrait;

    /**
     * @throws TenantAccessException
     */
    public function index(
        Request $request,
        Restaurant $restaurant
    ) {
        $this->assertRestaurantAccess($request, $restaurant);

        $records = $restaurant
            ->billingRecords()
            ->paginate(20);

        return view(
            'admin.restaurants.billing',
            [
                'restaurant' => $restaurant,
                'records' => $records,
            ]
        );
    }

    /**
     * @throws TenantAccessException
     */
    public function startTrial(
        Request $request,
        Restaurant $restaurant,
        BillingService $billing
    ) {
        $this->assertRestaurantAccess($request, $restaurant);

        if (!$request->user()?->is_super_admin) {
            return back()->with(
                'error',
                __('billing.errors.not_allowed')
            );
        }

        $billing->startTrial(
            restaurant: $restaurant,
            days: 14,
            confirmedBy: $request->user()
        );

        return back()->with(
            'success',
            __('billing.messages.trial_started')
        );
    }

    /**
     * @throws TenantAccessException
     */
    public function confirmPayment(
        Request $request,
        Restaurant $restaurant,
        BillingService $billing
    ) {
        $this->assertRestaurantAccess($request, $restaurant);

        if (!$request->user()?->is_super_admin) {
            return back()->with(
                'error',
                __('billing.errors.not_allowed')
            );
        }

        $billing->confirmPayment(
            restaurant: $restaurant,
            confirmedBy: $request->user(),
            amount: $restaurant->monthlyPrice()
        );

        return back()->with(
            'success',
            __('billing.messages.payment_confirmed')
        );
    }

    /**
     * @throws TenantAccessException
     */
    public function extendTrial(
        Request $request,
        Restaurant $restaurant,
        BillingService $billing
    ) {
        $this->assertRestaurantAccess($request, $restaurant);

        if (!$request->user()?->is_super_admin) {
            return back()->with(
                'error',
                __('billing.errors.not_allowed')
            );
        }

        $data = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:90'],
        ]);

        $billing->extendTrial(
            restaurant: $restaurant,
            days: (int) $data['days'],
            confirmedBy: $request->user()
        );

        return back()->with(
            'success',
            __('billing.messages.trial_extended')
        );
    }

    /**
     * @throws TenantAccessException
     */
    public function deactivate(
        Request $request,
        Restaurant $restaurant,
        BillingService $billing
    ) {
        $this->assertRestaurantAccess($request, $restaurant);

        $billing->deactivate(
            restaurant: $restaurant,
            confirmedBy: $request->user()
        );

        return back()->with(
            'success',
            __('billing.messages.deactivated')
        );
    }

    /**
     * @throws TenantAccessException
     */
    public function toggleKeepData(
        Request $request,
        Restaurant $restaurant
    ) {
        $this->assertRestaurantAccess($request, $restaurant);

        $restaurant->update([
            'keep_data' => $request->boolean('keep_data'),
        ]);

        return back()->with(
            'success',
            __('billing.messages.keep_data_updated')
        );
    }

    /**
     * @throws TenantAccessException
     */
    public function resume(
        Request $request,
        Restaurant $restaurant,
        BillingService $billing
    ) {
        $this->assertRestaurantAccess($request, $restaurant);

        if (!$request->user()?->is_super_admin) {
            return back()->with(
                'error',
                __('billing.errors.not_allowed')
            );
        }

        $billing->resume(
            restaurant: $restaurant,
            confirmedBy: $request->user()
        );

        return back()->with(
            'success',
            __('billing.messages.resumed')
        );
    }
}
