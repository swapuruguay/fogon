<?php

if (!function_exists('e')) {
    function e(string $value): string {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        return App\Core\Session::csrfToken();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path): void {
        header('Location: ' . url($path));
        exit;
    }
}

if (!function_exists('session_get')) {
    function session_get(string $key): mixed {
        return App\Core\Session::get($key);
    }
}

if (!function_exists('session_set')) {
    function session_set(string $key, mixed $value): void {
        App\Core\Session::set($key, $value);
    }
}

if (!function_exists('session_has')) {
    function session_has(string $key): bool {
        return App\Core\Session::has($key);
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed {
        return App\Core\Session::get('_old.' . $key, $default);
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }
        return match (strtolower($value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => $value,
        };
    }
}

if (!function_exists('sanitize')) {
    function sanitize(string $value): string {
        return trim(strip_tags($value));
    }
}

if (!function_exists('sanitize_int')) {
    function sanitize_int(mixed $value): int {
        return filter_var($value, FILTER_VALIDATE_INT) ?: 0;
    }
}

if (!function_exists('sanitize_float')) {
    function sanitize_float(mixed $value): float {
        return filter_var($value, FILTER_VALIDATE_FLOAT) ?: 0.0;
    }
}

if (!function_exists('to_mysql_date')) {
    function to_mysql_date(string $date): string {
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $date)) {
            [$dia, $mes, $ano] = explode('/', $date);
            return "$ano-$mes-$dia";
        }
        return $date;
    }
}

if (!function_exists('to_view_date')) {
    function to_view_date(string $date): string {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            [$anio, $mes, $dia] = explode('-', $date);
            return "$dia/$mes/$anio";
        }
        return $date;
    }
}

if (!function_exists('auth')) {
    function auth(): bool {
        return App\Core\Session::get('autenticado') === true;
    }
}

if (!function_exists('auth_user')) {
    function auth_user(): mixed {
        return App\Core\Session::get('usuario');
    }
}

if (!function_exists('require_auth')) {
    function require_auth(): void {
        if (!auth()) {
            redirect('login');
        }
    }
}
