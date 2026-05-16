<?php

namespace App\Services\BillingService;

use App\Models\BillingRecord;
use App\Models\Restaurant;
use App\Models\User;

class BillingService
{
    // =========================
    // START TRIAL
    // =========================
    public function startTrial(
        Restaurant $restaurant,
        int $days = 30,
        ?User $confirmedBy = null
    ): void {

        $from = now();

        $to = now()
            ->copy()
            ->addDays($days);

        $restaurant->update([
            'is_active' => true,
            'trial_ends_at' => $to,

            // IMPORTANT:
            'trial_used_at' => $restaurant->trial_used_at ?: now(),
        ]);

        BillingRecord::create([
            'restaurant_id' => $restaurant->id,
            'confirmed_by' => $confirmedBy?->id,
            'type' => BillingRecord::TYPE_TRIAL,
            'status' => BillingRecord::STATUS_CONFIRMED,
            'plan_key' => $restaurant->plan_key,
            'amount' => 0,
            'currency' => 'EUR',
            'period_from' => $from,
            'period_to' => $to,
            'confirmed_at' => now(),
            'notes' => 'Trial started',
        ]);
    }

    // =========================
    // CONFIRM PAYMENT
    // =========================
    public function confirmPayment(
        Restaurant $restaurant,
        User $confirmedBy,
        ?float $amount = null
    ): void {

        // =========================
        // EXTEND FROM CURRENT PERIOD
        // =========================
        $base = null;

        if (
            $restaurant->paid_until &&
            $restaurant->paid_until->isFuture()
        ) {

            $base = $restaurant->paid_until->copy();

        } elseif (
            $restaurant->trial_ends_at &&
            $restaurant->trial_ends_at->isFuture()
        ) {

            $base = $restaurant->trial_ends_at->copy();

        } else {

            $base = now();
        }

        $from = $base->copy();

        $to = $base->copy()->addMonth();

        $restaurant->update([
            'paid_until' => $to,
        ]);

        BillingRecord::create([
            'restaurant_id' => $restaurant->id,
            'confirmed_by' => $confirmedBy->id,
            'type' => BillingRecord::TYPE_PAYMENT,
            'status' => BillingRecord::STATUS_CONFIRMED,
            'plan_key' => $restaurant->plan_key,
            'amount' => $amount,
            'currency' => 'EUR',
            'period_from' => $from,
            'period_to' => $to,
            'confirmed_at' => now(),
            'notes' => 'Manual payment confirmed',
        ]);
    }

    // =========================
    // DEACTIVATE
    // =========================
    public function deactivate(
        Restaurant $restaurant,
        ?User $confirmedBy = null
    ): void {

        // =========================
        // BURN TRIAL
        // =========================
        if ( $restaurant->trial_ends_at && !$restaurant->paid_until)
        {
            $restaurant->trial_ends_at = now();
        }

        $restaurant->is_active = false;

        $restaurant->save();

        BillingRecord::create([
            'restaurant_id' => $restaurant->id,
            'confirmed_by' => $confirmedBy?->id,
            'type' => BillingRecord::TYPE_DEACTIVATION,
            'status' => BillingRecord::STATUS_CONFIRMED,
            'plan_key' => $restaurant->plan_key,
            'period_from' => now(),
            'period_to' => now(),
            'confirmed_at' => now(),
            'notes' => 'Restaurant manually deactivated',
        ]);
    }

    // =========================
    // RESUME
    // =========================
    public function resume(
        Restaurant $restaurant,
        ?User $confirmedBy = null
    ): void {

        $restaurant->update([
            'is_active' => true,
        ]);

        BillingRecord::create([
            'restaurant_id' => $restaurant->id,
            'confirmed_by' => $confirmedBy?->id,
            'type' => BillingRecord::TYPE_RESUME,
            'status' => BillingRecord::STATUS_CONFIRMED,
            'plan_key' => $restaurant->plan_key,
            'period_from' => now(),
            'period_to' => now(),
            'confirmed_at' => now(),
            'notes' => 'Restaurant manually resumed',
        ]);
    }

    // =========================
    // EXPIRE
    // =========================
    public function expire(Restaurant $restaurant): void
    {
        $restaurant->update([
            'is_active' => false,
        ]);

        BillingRecord::create([
            'restaurant_id' => $restaurant->id,
            'type' => BillingRecord::TYPE_EXPIRATION,
            'status' => BillingRecord::STATUS_SYSTEM,
            'plan_key' => $restaurant->plan_key,
            'period_from' => $restaurant->paid_until
                ?: $restaurant->trial_ends_at,
            'period_to' => $restaurant->paid_until
                ?: $restaurant->trial_ends_at,
            'confirmed_at' => now(),
            'notes' => 'Subscription expired automatically',
        ]);
    }

    // =========================
    // EXTEND TRIAL
    // =========================
    public function extendTrial(
        Restaurant $restaurant,
        int $days,
        User $confirmedBy,
        ?string $note = null
    ): void {

        $base = $restaurant->trial_ends_at &&
                $restaurant->trial_ends_at->isFuture()
            ? $restaurant->trial_ends_at->copy()
            : now();

        $to = $base->copy()->addDays($days);

        $restaurant->update([
            'is_active' => true,
            'trial_ends_at' => $to,
        ]);

        BillingRecord::create([
            'restaurant_id' => $restaurant->id,
            'confirmed_by' => $confirmedBy->id,
            'type' => BillingRecord::TYPE_MANUAL_EXTENSION,
            'status' => BillingRecord::STATUS_CONFIRMED,
            'plan_key' => $restaurant->plan_key,
            'amount' => 0,
            'currency' => 'EUR',
            'period_from' => $base,
            'period_to' => $to,
            'confirmed_at' => now(),
            'notes' => $note ?: 'Trial manually extended',
        ]);
    }
}
