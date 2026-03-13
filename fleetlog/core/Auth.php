<?php

namespace FleetLog\Core;

use FleetLog\App\Repositories\UserRepository;

class Auth
{
    private const REMEMBER_ME_COOKIE = 'fleetlog_remember';
    private const REMEMBER_ME_EXPIRY = 2592000; // 30 days

    public static function login(string $email, string $password, bool $remember = false): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userRepo = new UserRepository();
        $user = $userRepo->findByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['active']) {
                return false;
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['tenant_id'] = $user['tenant_id'];
            $_SESSION['role'] = $user['role'];

            if ($remember) {
                self::setRememberMeCookie($user['id']);
            }

            return true;
        }

        return false;
    }

    public static function loginWithPin(string $email, string $pin, bool $remember = false): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $userRepo = new UserRepository();
        $user = $userRepo->findByEmail($email);

        // Verify PIN if it exists and matches
        if ($user && !empty($user['pin']) && password_verify($pin, $user['pin'])) {
            if (!$user['active']) {
                return false;
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['tenant_id'] = $user['tenant_id'];
            $_SESSION['role'] = $user['role'];

            if ($remember) {
                self::setRememberMeCookie($user['id']);
            }

            return true;
        }

        return false;
    }

    public static function logout(): void
    {
        self::clearRememberMeCookie();
        session_destroy();
    }

    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user_id'])) {
            return true;
        }

        return self::checkRememberMeCookie();
    }

    private static function setRememberMeCookie(int $userId): void
    {
        $selector = \bin2hex(\random_bytes(6));
        $validator = \bin2hex(\random_bytes(32));
        $hashedValidator = \hash('sha256', $validator);
        $expires = \date('Y-m-d H:i:s', \time() + self::REMEMBER_ME_EXPIRY);

        DB::query(
            "INSERT INTO user_remember_tokens (user_id, selector, hashed_validator, expires_at) VALUES (?, ?, ?, ?)",
            [$userId, $selector, $hashedValidator, $expires]
        );

        $cookieValue = $selector . ':' . $validator;
        \setcookie(self::REMEMBER_ME_COOKIE, $cookieValue, \time() + self::REMEMBER_ME_EXPIRY, '/', '', false, true);
    }

    private static function checkRememberMeCookie(): bool
    {
        $cookie = $_COOKIE[self::REMEMBER_ME_COOKIE] ?? '';
        if (empty($cookie)) {
            return false;
        }

        $parts = \explode(':', $cookie);
        if (\count($parts) !== 2) {
            return false;
        }

        list($selector, $validator) = $parts;
        $hashedValidator = \hash('sha256', $validator);

        $token = DB::fetch(
            "SELECT * FROM user_remember_tokens WHERE selector = ? AND expires_at > NOW()",
            [$selector]
        );

        if ($token && \hash_equals($token['hashed_validator'], $hashedValidator)) {
            $userRepo = new UserRepository();
            $user = $userRepo->find($token['user_id']);

            if ($user && $user['active']) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['tenant_id'] = $user['tenant_id'];
                $_SESSION['role'] = $user['role'];
                
                // Security: Rotate token on successful persistent login
                self::clearRememberMeCookie(); // Clear old
                self::setRememberMeCookie($user['id']); // Set new

                return true;
            }
        }

        return false;
    }

    private static function clearRememberMeCookie(): void
    {
        $cookie = $_COOKIE[self::REMEMBER_ME_COOKIE] ?? '';
        if ($cookie) {
            $parts = \explode(':', $cookie);
            if (\count($parts) === 2) {
                DB::query("DELETE FROM user_remember_tokens WHERE selector = ?", [$parts[0]]);
            }
        }
        \setcookie(self::REMEMBER_ME_COOKIE, '', \time() - 3600, '/', '', false, true);
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
            return (int)$_SESSION['impersonate_tenant_id'];
        }
        return isset($_SESSION['tenant_id']) ? (int)$_SESSION['tenant_id'] : null;
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
