<?php

namespace App\Core;

abstract class Controller
{
    protected View $view;
    protected $_view;
    protected string $controller;
    protected string $method;

    public function __construct(?string $controller = null, ?string $method = null)
    {
        if ($controller !== null) {
            $this->controller = $controller;
            $this->method = $method ?? 'index';
        } else {
            $request = new Request();
            $this->controller = $request->getControlador();
            $this->method = $request->getMetodo();
        }
        $this->view = new View($this->controller);
        $this->_view = $this->view;
    }

    abstract public function index(): void;

    protected function loadModel(string $modelo): mixed
    {
        $modeloClass = $modelo . 'Model';

        if (class_exists($modeloClass)) {
            return new $modeloClass();
        }

        $rutaModelo = ROOT . 'models' . DS . $modeloClass . '.php';

        if (is_readable($rutaModelo)) {
            require_once $rutaModelo;
            return new $modeloClass();
        }

        throw new \Exception("Error: Modelo '$modelo' no encontrado");
    }

    protected function getLibrary(string $libreria): void
    {
        $rutaLibreria = ROOT . 'libs' . DS . $libreria . '.php';

        if (is_readable($rutaLibreria)) {
            require_once $rutaLibreria;
        } else {
            throw new \Exception("Error: Libreria '$libreria' no encontrada");
        }
    }

    protected function redirect(string $ruta = ''): void
    {
        $url = $ruta ? BASE_URL . $ruta : BASE_URL;
        header('Location: ' . $url);
        exit;
    }

    protected function redireccionar(string $ruta = ''): void
    {
        $this->redirect($ruta);
    }

    protected function getPost(string $clave): string
    {
        if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
            return sanitize((string) $_POST[$clave]);
        }
        return '';
    }

    protected function getTexto(string $clave): string
    {
        if (isset($_POST[$clave]) && !empty($_POST[$clave])) {
            $_POST[$clave] = htmlspecialchars((string) $_POST[$clave], ENT_QUOTES, 'UTF-8');
            return (string) $_POST[$clave];
        }
        return '';
    }

    protected function getInt(string $clave): int
    {
        if (isset($_POST[$clave])) {
            return sanitize_int($_POST[$clave]);
        }
        return 0;
    }

    protected function getFloat(string $clave): float
    {
        if (isset($_POST[$clave])) {
            return sanitize_float($_POST[$clave]);
        }
        return 0.0;
    }

    protected function filterInt(mixed $int): int
    {
        return (int) $int;
    }

    protected function json(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function requireAuth(): void
    {
        if (!Session::get('autenticado')) {
            $this->redirect('login');
        }
    }

    protected function cambiarfecha_mysql(string $fecha): string
    {
        list($dia, $mes, $ano) = explode('/', $fecha);
        return "$ano-$mes-$dia";
    }

    protected function cambiarfecha_vista(string $fecha): string
    {
        list($anio, $mes, $dia) = explode('-', $fecha);
        return "$dia/$mes/$anio";
    }

    protected function armarMenu(): void {}
}
