<?php

namespace App\Support;

use App\Models\User;

class Permissions
{
    /**
     * DEV режим: пока всем всё разрешено.
     * Потом просто переключим на false (или уберём) — и включится реальный контроль.
     */
    public const DEV_ALLOW_ALL = true;

    /**
     * ЕДИНЫЙ реестр прав.
     * Сейчас: config/permissions.php
     * Потом: заменишь реализацию на чтение из БД — и ВСЁ приложение подхватит.
     *
     * Формат:
     *  'perm.key' => ['group' => 'menu', 'label' => '...']
     */
    public static function registry(): array
    {
        $reg = config('permissions', []);
        return is_array($reg) ? $reg : [];
    }

    /** Список всех ключей */
    public static function keys(): array
    {
        return array_keys(self::registry());
    }

    /**
     * Группировка для UI (кнопки групп, модалки и т.д.)
     * Возвращает: group => [permKey => label]
     */
    public static function groupedRegistry(): array
    {
        $grouped = [];
        foreach (self::registry() as $key => $def) {
            if (!is_string($key) || trim($key) === '') continue;
            if (!is_array($def)) continue;

            $group = $def['group'] ?? 'other';
            $label = $def['label'] ?? null;

            if (!is_string($group) || trim($group) === '') $group = 'other';
            if (!is_string($label) || trim($label) === '') continue;

            $grouped[$group][$key] = $label;
        }

        ksort($grouped);
        return $grouped;
    }

    /**
     * Нормализация входа perm[...] строго по реестру.
     * На выходе всегда полный массив key => bool.
     */
    public static function normalize(array $incoming): array
    {
        $out = [];
        $keys = self::keys();

        foreach ($keys as $k) {
            $v = $incoming[$k] ?? null;

            // чекбокс может прийти как '1' / 1 / true, или отсутствовать, или '0'
            $out[$k] = !empty($v) && (string)$v !== '0';
        }

        return $out;
    }

    /**
     * Проверка права (ТОЛЬКО через этот метод везде).
     */
    public static function can(?User $user, string $key): bool
    {
        if (!$user) return false;

        // super admin всегда ок
        if (!empty($user->is_super_admin)) return true;

        // dev: всем всё
        if (self::DEV_ALLOW_ALL) return true;

        // реальный контроль (когда выключим DEV_ALLOW_ALL)
        $p = $user->permissions ?? [];
        return is_array($p) && !empty($p[$key]);
    }

    public static function abortUnless(?User $user, string $key): void
    {
        abort_unless(self::can($user, $key), 403);
    }
}
