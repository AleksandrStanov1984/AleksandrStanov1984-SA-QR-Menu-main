<?php

namespace App\Support;

use Carbon\Carbon;

class Hours
{
    public static function status(?array $todayHours): string
    {
        if (!$todayHours || !empty($todayHours['closed'])) {
            return 'closed';
        }

        $open = $todayHours['open'] ?? null;
        $close = $todayHours['close'] ?? null;

        if (!$open || !$close) {
            return 'closed';
        }

        $now = Carbon::now();

        $openAt = Carbon::today()->setTimeFromTimeString($open);
        $closeAt = Carbon::today()->setTimeFromTimeString($close);

        // если закрытие "меньше или равно" открытию,
        // значит заведение работает через полночь
        if ($closeAt->lte($openAt)) {
            $closeAt->addDay();
        }

        // если сейчас уже после полуночи, но смена началась вчера
        if ($now->lt($openAt) && $closeAt->isNextDay()) {
            $openAt->subDay();
        }

        if ($now->lt($openAt) || $now->gte($closeAt)) {
            return 'closed';
        }

        if ($now->diffInMinutes($closeAt) <= 60) {
            return 'closing_soon';
        }

        return 'open';
    }
}
