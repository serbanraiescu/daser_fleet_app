<?php

namespace FleetLog\Core;

class RBAC
{
    public static function hasRole(string $role): bool
    {
        return Auth::role() === $role;
    }

    public static function isSuperAdmin(): bool
    {
        return self::hasRole('super_admin');
    }

    public static function isTenantAdmin(): bool
    {
        return self::hasRole('tenant_admin');
    }

    public static function isDriver(): bool
    {
        return self::hasRole('driver');
    }

    public static function requireRole($roles): void
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        if (!Auth::check()) {
            header('Location: /login');
            exit;
        }

        $currentRole = Auth::role();
        if (!in_array($currentRole, $roles)) {
            http_response_code(403);
            die("Access Denied. You do not have the required permissions to access this page.");
        }
    }

    public static function can(string $permission): bool
    {
        // Simple permission logic based on roles for V1
        $role = Auth::role();
        $permissions = [
            'super_admin' => ['manage_tenants', 'manage_users', 'view_all_reports'],
            'tenant_admin' => ['manage_vehicles', 'manage_drivers', 'view_tenant_reports'],
            'driver' => ['manage_trips', 'report_damage', 'view_own_stats'],
        ];

        return in_array($permission, $permissions[$role] ?? []);
    }
}
