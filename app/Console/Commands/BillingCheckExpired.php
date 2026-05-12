<?php

namespace App\Console\Commands;

use App\Models\Restaurant;
use App\Services\BillingService\BillingService;
use Illuminate\Console\Command;

class BillingCheckExpired extends Command
{
    protected $signature = 'billing:check-expired';

    protected $description =
        'Deactivate expired restaurants automatically';

    public function handle(
        BillingService $billing
    ): int {

        $restaurants = Restaurant::query()

            ->where('is_active', true)

            ->where(function ($q) {

                // =========================
                // EXPIRED TRIAL
                // =========================
                $q->where(function ($trial) {

                    $trial
                        ->whereNotNull('trial_ends_at')
                        ->whereNull('paid_until')
                        ->where('trial_ends_at', '<', now());
                })

                // =========================
                // EXPIRED PAID PERIOD
                // =========================
                ->orWhere(function ($paid) {

                    $paid
                        ->whereNotNull('paid_until')
                        ->where('paid_until', '<', now())
                        ->where('is_active', true);
                });

            })

            ->get();

        if ($restaurants->isEmpty()) {

            $this->info('No expired restaurants found.');

            return self::SUCCESS;
        }

        $count = 0;

        foreach ($restaurants as $restaurant) {

            $billing->expire($restaurant);

            $count++;

            $this->line(
                "Expired: #{$restaurant->id} {$restaurant->name}"
            );
        }

        $this->info(
            "Expired restaurants processed: {$count}"
        );

        return self::SUCCESS;
    }
}
