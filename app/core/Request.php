<?php

namespace App\Core;

class Request
{
    private string $controlador;
    private string $metodo;
    private array $argumentos = [];

    public function __construct()
    {
        if (isset($_GET['url'])) {
            $url = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
            $url = rtrim((string) $url, '/');
            $parts = array_filter(explode('/', $url));

            $this->controlador = strtolower(array_shift($parts) ?: '');
            $this->metodo = strtolower(array_shift($parts) ?: '');
            $this->argumentos = array_values($parts);
        }

        if (empty($this->controlador)) {
            $this->controlador = DEFAULT_CONTROLLER;
        }

        if (empty($this->metodo)) {
            $this->metodo = 'index';
        }
    }

    public function getControlador(): string
    {
        return $this->controlador;
    }

    public function getMetodo(): string
    {
        return $this->metodo;
    }

    public function getArgs(): array
    {
        return $this->argumentos;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $_REQUEST[$key] ?? $default;
    }

    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    public function has(string $key): bool
    {
        return isset($_REQUEST[$key]);
    }
}
