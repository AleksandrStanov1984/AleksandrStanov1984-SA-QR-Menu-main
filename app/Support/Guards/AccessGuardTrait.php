<?php

namespace App\Support\Guards;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Exceptions\TenantAccessException;

trait AccessGuardTrait
{
    /**
     * ACCESS = TENANT + PLAN ONLY (MVP MODE)
     * @throws TenantAccessException
     */
    protected function assertRestaurantAccess(
        Request $request,
        Restaurant $restaurant,
        ?string $perm = null
    ): void {
        $user = $request->user();

        if (!$user) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        // TENANT CHECK
        if (
            !$user->is_super_admin &&
            (int)$user->restaurant_id !== (int)$restaurant->id
        ) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        $route = $request->route();
        $action = $route?->getAction() ?? [];
        $defaults = $action['defaults'] ?? [];

        // PLAN CHECK
        $plan = $defaults['plan'] ?? null;

        if (
            !$user->is_super_admin &&
            $plan &&
            $restaurant->plan_key !== $plan
        ) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        // PERMISSIONS DISABLED (intentionally ignored)
    }

    /**
     * Только super admin
     * @throws TenantAccessException
     */
    protected function assertSuperAdmin(Request $request): void
    {
        $user = $request->user();

        if (!$user || !$user->is_super_admin) {
            throw new TenantAccessException(__('permissions.no_access'));
        }
    }

    /**
     * Проверка: модель принадлежит ресторану
     * @throws TenantAccessException
     */
    protected function assertBelongs(
        Request $request,
        Restaurant $restaurant,
        object $model,
        string $foreignKey = 'restaurant_id'
    ): void {
        $user = $request->user();

        if (
            !$user ||
            (
                !$user->is_super_admin &&
                (int)$user->restaurant_id !== (int)$restaurant->id
            ) ||
            !isset($model->{$foreignKey}) ||
            (int)$model->{$foreignKey} !== (int)$restaurant->id
        ) {
            throw new TenantAccessException(__('permissions.no_access'));
        }
    }

    /**
     * MVP: permissions отключены → всегда пропускаем
     */
    protected function assertAnyPermission(
        Request $request,
        array $permissions
    ): void {
        // intentionally disabled
    }

    /**
     * PRO feature = TENANT + PLAN ONLY
     * @throws TenantAccessException
     */
    protected function assertProFeature(
        Request $request,
        Restaurant $restaurant,
        ?string $permission = null
    ): void {
        $user = $request->user();

        if (!$user) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        // TENANT
        if (
            !$user->is_super_admin &&
            (int)$user->restaurant_id !== (int)$restaurant->id
        ) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        $route = $request->route();
        $action = $route?->getAction() ?? [];
        $defaults = $action['defaults'] ?? [];

        // PLAN (по умолчанию pro)
        $plan = $defaults['plan'] ?? 'pro';

        if (
            !$user->is_super_admin &&
            $restaurant->plan_key !== $plan
        ) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        // PERMISSIONS DISABLED
    }

    /**
     * LEGACY (оставлено для будущего, сейчас не используется)
     */
    protected function resolvePermissionFromRoute(?string $routeName): ?string
    {
        return null;
    }
}
