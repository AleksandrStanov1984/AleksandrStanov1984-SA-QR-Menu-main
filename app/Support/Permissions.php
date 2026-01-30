<?php

namespace App\Support;

use App\Models\User;

class Permissions
{
    /**
     * Сейчас: всем всё (true).
     * Позже: читаем назначения из файла/БД и включаем реальный контроль.
     */
    public static function can(?User $user, string $key): bool
    {
        return true;
    }

    public static function abortUnless(?User $user, string $key): void
    {
        abort_unless(self::can($user, $key), 403);
    }
}
