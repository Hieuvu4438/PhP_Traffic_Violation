<?php
namespace App\Core;

/**
 * Session Manager — wrapper cho $_SESSION
 */
class Session
{
    public static function start(): void
    {
        $config = require __DIR__ . '/../../config/config.php';

        if (session_status() === PHP_SESSION_NONE) {
            session_name($config['session_name']);
            session_set_cookie_params($config['session_lifetime']);
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    // Flash messages (hiển thị 1 lần rồi xóa)
    public static function setFlash(string $key, string $message): void
    {
        $_SESSION['flash_' . $key] = $message;
    }

    public static function getFlash(string $key): ?string
    {
        $flashKey = 'flash_' . $key;
        if (isset($_SESSION[$flashKey])) {
            $message = $_SESSION[$flashKey];
            unset($_SESSION[$flashKey]);
            return $message;
        }
        return null;
    }

    public static function hasFlash(string $key): bool
    {
        return isset($_SESSION['flash_' . $key]);
    }

    // Auth helpers
    public static function isLoggedIn(): bool
    {
        return self::has('user_id');
    }

    public static function isAdmin(): bool
    {
        return self::get('role') === 'admin';
    }

    public static function login(array $user): void
    {
        self::set('user_id', $user['id']);
        self::set('user_name', $user['fullname']);
        self::set('user_email', $user['email']);
        self::set('role', $user['role']);
        session_regenerate_id(true);
    }

    public static function logout(): void
    {
        self::destroy();
    }

    // CSRF Token
    public static function csrfToken(): string
    {
        if (!self::has('csrf_token')) {
            self::set('csrf_token', bin2hex(random_bytes(32)));
        }
        return self::get('csrf_token');
    }

    public static function validateCsrf(string $token): bool
    {
        if (!self::has('csrf_token')) {
            return false;
        }
        return hash_equals(self::get('csrf_token'), $token);
    }
}
