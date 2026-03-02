<?php

namespace FleetLog\Core;

use FleetLog\App\Repositories\UserRepository;

class Auth
{
    public static function login(string $email, string $password): bool
    {
        $userRepo = new UserRepository();
        $user = $userRepo->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['active']) {
                return false;
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['tenant_id'] = $user['tenant_id'];
            $_SESSION['role'] = $user['role'];
            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        session_destroy();
    }

    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        $userRepo = new UserRepository();
        return $userRepo->find($_SESSION['user_id']);
    }

    public static function tenantId(): ?int
    {
        if (self::isImpersonating()) {
            return $_SESSION['impersonate_tenant_id'];
        }
        return $_SESSION['tenant_id'] ?? null;
    }

    public static function role(): ?string
    {
        return $_SESSION['role'] ?? null;
    }

    public static function impersonate(int $tenantId): void
    {
        if (RBAC::isSuperAdmin()) {
            $_SESSION['impersonate_tenant_id'] = $tenantId;
        }
    }

    public static function stopImpersonating(): void
    {
        unset($_SESSION['impersonate_tenant_id']);
    }

    public static function isImpersonating(): bool
    {
        return isset($_SESSION['impersonate_tenant_id']);
    }
}
