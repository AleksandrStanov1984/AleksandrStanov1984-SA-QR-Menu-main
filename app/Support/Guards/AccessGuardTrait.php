<?php

namespace App\Support\Guards;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Support\Permissions;
use App\Exceptions\TenantAccessException;

trait AccessGuardTrait
{
    /**
     * Tenant + optional permission
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

        if (
            !$user->is_super_admin &&
            (int)$user->restaurant_id !== (int)$restaurant->id
        ) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        if ($perm && !Permissions::can($user, $perm)) {
            throw new TenantAccessException(__('permissions.no_access'));
        }
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
     * (универсально для link, item, section и т.д.)
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
     * Проверка: есть ХОТЯ БЫ ОДНО право
     * @throws TenantAccessException
     */
    protected function assertAnyPermission(
        Request $request,
        array $permissions
    ): void {
        $user = $request->user();

        if (!$user || !Permissions::canAny($user, $permissions)) {
            throw new TenantAccessException(__('permissions.no_access'));
        }
    }

    /**
     * Проверка: доступ к PRO feature (или super admin)
     * @throws TenantAccessException
     */
    protected function assertProFeature(Request $request, Restaurant $restaurant): void
    {
        $user = $request->user();

        if (!$user) {
            throw new TenantAccessException(__('permissions.no_access'));
        }

        if (
            !$user->is_super_admin &&
            $restaurant->plan_key !== 'pro'
        ) {
            throw new TenantAccessException(__('permissions.no_access'));
        }
    }
}
