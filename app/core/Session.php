<?php

namespace App\Core;

class Session
{
    private static bool $initialized = false;

    public static function init(): void
    {
        if (!self::$initialized) {
            if (session_status() === PHP_SESSION_NONE) {
                $lifetime = (int) env('SESSION_LIFETIME', 120) * 60;
                ini_set('session.cookie_lifetime', (string) $lifetime);
                ini_set('session.gc_maxlifetime', (string) $lifetime);
                session_start();
            }
            self::$initialized = true;

            if (!isset($_SESSION['_csrf_token'])) {
                $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
            }
        }
    }

    public static function set(string $key, mixed $value): void
    {
        if (!empty($key)) {
            $_SESSION[$key] = $value;
        }
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
        if (is_array($key)) {
            foreach ($key as $k) {
                unset($_SESSION[$k]);
            }
        } else {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy(?string $key = null): void
    {
        if ($key !== null) {
            self::remove($key);
        } else {
            session_unset();
            session_destroy();
            self::$initialized = false;
        }
    }

    public static function flash(string $key, ?string $message = null): ?string
    {
        if ($message !== null) {
            $_SESSION['_flash'][$key] = $message;
            return null;
        }

        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function flashHas(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }

    public static function csrfToken(): string
    {
        return $_SESSION['_csrf_token'] ?? '';
    }

    public static function csrfVerify(string $token): bool
    {
        return hash_equals(self::csrfToken(), $token);
    }

    public static function csrfRegenerate(): string
    {
        $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        return self::csrfToken();
    }

    public static function acceso(string $level): void
    {
        if (!self::get('autenticado')) {
            header('Location: ' . BASE_URL . 'error/access/5050');
            exit;
        }

        if (self::getLevel($level) > self::getLevel(self::get('level'))) {
            header('Location: ' . BASE_URL . 'error/access/5050');
            exit;
        }
    }

    public static function accesoView(string $level): bool
    {
        if (!self::get('autenticado')) {
            return false;
        }

        return self::getLevel($level) <= self::getLevel(self::get('level'));
    }

    public static function getLevel(string $level): int
    {
        return match (strtolower($level)) {
            'admin' => 3,
            'especial' => 2,
            'usuario' => 1,
            default => throw new \Exception('Error de acceso'),
        };
    }

    public static function old(string $key, mixed $default = ''): mixed
    {
        return self::get('_old.' . $key, $default);
    }

    public static function setOld(array $data): void
    {
        self::set('_old', $data);
    }

    public static function clearOld(): void
    {
        self::remove('_old');
    }
}
