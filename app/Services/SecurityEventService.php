<?php

namespace App\Services;

use App\Models\SecurityEvent;
use App\Models\User;
use Illuminate\Http\Request;

class SecurityEventService
{
    public function log(
        string $event,
        User $actor,
        User $target,
        ?int $restaurantId,
        array $meta = [],
        ?Request $request = null
    ): void {
        SecurityEvent::create([
            'actor_id' => $actor->id,
            'target_user_id' => $target->id,
            'restaurant_id' => $restaurantId,
            'event' => $event,
            'meta' => $meta,
            'ip' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
