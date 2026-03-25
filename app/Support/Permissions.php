<?php

namespace App\Support;

use App\Models\User;

class Permissions
{
    /**
     * DEV режим: по умолчанию true,
     * но управляется через env, чтобы можно было тестировать реальные права.
     *
     * .env:
     * DEV_ALLOW_ALL_PERMISSIONS=true|false
     */
    public const DEV_ALLOW_ALL = true;

    /**
     * Проверяем, разрешён ли DEV режим через env.
     */
    protected static function devAllowAll(): bool
    {
        // если env не задан — ведём себя как сейчас
        return (bool) env('DEV_ALLOW_ALL_PERMISSIONS', self::DEV_ALLOW_ALL);
    }

    /**
     * ЕДИНЫЙ реестр прав.
     * Сейчас: config/permissions.php
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
     * Группировка для UI
     * group => [permKey => label]
     */
    public static function groupedRegistry(): array
    {
        $grouped = [];

        foreach (self::registry() as $key => $def) {

            if (!is_string($key) || trim($key) === '') continue;
            if (!is_array($def)) continue;

            $group = $def['group'] ?? 'other';

            if (!is_string($group) || trim($group) === '') {
                $group = 'other';
            }

            $labelKey = $def['label'] ?? null;

            if (!is_string($labelKey) || trim($labelKey) === '') {
                continue;
            }

            $label = __('permissions.' . $labelKey);

            if ($label === $labelKey) {
                $label = ucfirst(str_replace('_', ' ', last(explode('.', $labelKey))));
            }

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

        foreach (self::keys() as $k) {
            $v = $incoming[$k] ?? null;

            $out[$k] = !empty($v) && (string)$v !== '0';
        }

        return $out;
    }

    /**
     * Проверка одного права.
     */
    public static function can(?User $user, string $key): bool
    {
        if (!$user) return false;

        if (!empty($user->is_super_admin)) return true;

        if (self::devAllowAll()) return true;

        $meta = $user->meta ?? [];
        $p = $meta['permissions'] ?? [];

        return is_array($p) && !empty($p[$key]);
    }

    /**
     * Проверка: есть ХОТЯ БЫ ОДНО из прав.
     */
    public static function canAny(?User $user, array $keys): bool
    {
        foreach ($keys as $k) {
            if (self::can($user, $k)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Abort 403, если нет права.
     */
    public static function abortUnless(?User $user, string $key): void
    {
        abort_unless(self::can($user, $key), 403);
    }

    /**
     * Строгая проверка для импорта:
     * если нет права — добавляем ошибку.
     */
    public static function requireOrFail(
        ?User $user,
        string $key,
        string $path,
        array &$errors,
        ?string $label = null
    ): void {
        if (self::can($user, $key)) {
            return;
        }

        $errors[] = [
            'path'       => $path,
            'permission' => $key,
            'message'    => $label
                ? "Нет прав: {$label}"
                : "Нет прав для действия ({$key})",
        ];
    }
}
