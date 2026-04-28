<?php

namespace App\Observers;

use App\Models\Item;
use App\Support\Cache\CacheHelper;

class ItemObserver
{
    public function created(Item $item): void
    {
        $this->flush($item);
    }

    public function updated(Item $item): void
    {
        $this->flush($item);
    }

    public function deleted(Item $item): void
    {
        $this->flush($item);
    }

    public function restored(Item $item): void
    {
        $this->flush($item);
    }

    public function forceDeleted(Item $item): void
    {
        $this->flush($item);
    }

    private function flush(Item $item): void
    {
        if (!$item->relationLoaded('section')) {
            $item->load('section');
        }

        $restaurantId = $item->section?->restaurant_id;

        if ($restaurantId) {
            CacheHelper::flushRestaurant($restaurantId);
        }
    }
}
