<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Restaurant;
use App\Services\Restaurants\RestaurantDeletionService;

class PurgeExpiredRestaurants extends Command
{
    protected $signature = 'restaurants:purge-expired';

    protected $description = 'Delete expired inactive restaurants';

    public function handle(
        RestaurantDeletionService $deletion
    ): int {

        $restaurants = Restaurant::query()
            ->where('is_active', false)
            ->get();

        $count = 0;

        foreach ($restaurants as $restaurant) {

            if (!$restaurant->canBePurged()) {
                continue;
            }

            $this->info(
                'Deleting restaurant #' . $restaurant->id
            );

            $deletion->delete($restaurant);

            $count++;
        }

        $this->info(
            'Deleted: ' . $count
        );

        return self::SUCCESS;
    }
}
