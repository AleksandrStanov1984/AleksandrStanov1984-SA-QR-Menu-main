<?php

namespace App\Support;

use App\Models\User;

class Permissions
{
    /**
     * IMPORTANT (MVP MODE)
     *
     * Permissions system is temporarily DISABLED.
     *
     * Access control is based ONLY on:
     * - tenant
     * - plan
     *
     * All permission checks return TRUE intentionally.
     *
     * Do NOT re-enable partially.
     * Full reactivation will be done in future phase.
     */

    /**
     * DEV режим (оставлен для будущего, сейчас не используется)
     */

    /**
     * ROLE IN MVP:
     *
     * This class DOES NOT control access.
     * It ONLY exists as:
     * - permission registry (for UI)
     * - future RBAC layer
     *
     * Actual access control is handled by:
     * - tenant isolation
     * - plan features
     * - AccessGuardTrait
     *
     * Do NOT rely on this class for security.
     */
    public const DEV_ALLOW_ALL = true;

    protected static function devAllowAll(): bool
    {
        return true; // hard override
    }

    /**
     * Реестр прав (сохраняем для будущего)
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
     * Группировка для UI (оставляем, UI может использовать)
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
     * Нормализация входящих данных (оставляем)
     */
    public static function normalize(array $incoming): array
    {
        $out = [];
        $registry = self::registry();

        foreach ($incoming as $k => $v) {
            if (!is_string($k) || !array_key_exists($k, $registry)) {
                continue;
            }

            $out[$k] = !empty($v) && (string) $v !== '0';
        }

        return $out;
    }

    /**
     * Проверка одного права (MVP: ВСЕГДА TRUE)
     */
    public static function can(?User $user, string $key): bool
    {
        return true;
    }

    /**
     * Проверка: есть хотя бы одно право (MVP: ВСЕГДА TRUE)
     */
    public static function canAny(?User $user, array $keys): bool
    {
        return true;
    }

    /**
     * Abort (MVP: отключено)
     */
    public static function abortUnless(?User $user, string $key): void
    {
        // intentionally disabled
    }

    /**
     * Import validation (MVP: отключено)
     */
    public static function requireOrFail(
        ?User $user,
        string $key,
        string $path,
        array &$errors,
        ?string $label = null
    ): void {
        // intentionally disabled
    }

    /**
     * Redirect on deny (MVP: всегда null)
     */
    public static function denyRedirect(
        ?User $user,
        string $key,
        ?string $message = null
    ) {
        return null;
    }
}
