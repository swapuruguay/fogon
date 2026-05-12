<?php

namespace App\Core;

class Bootstrap
{
    public static function run(Request $peticion): void
    {
        $controllerName = $peticion->getControlador() . 'Controller';
        $controllerFile = ROOT . 'controllers' . DS . $controllerName . '.php';
        $metodo = $peticion->getMetodo();
        $args = $peticion->getArgs();

        if (!is_readable($controllerFile)) {
            throw new \Exception("Controlador '$controllerName' no encontrado");
        }

        require_once $controllerFile;

        if (!class_exists($controllerName)) {
            throw new \Exception("Clase '$controllerName' no definida");
        }

        $controller = new $controllerName(
            $peticion->getControlador(),
            $peticion->getMetodo()
        );

        if (!is_callable([$controller, $metodo])) {
            $metodo = 'index';
        }

        if (!empty($args)) {
            call_user_func_array([$controller, $metodo], $args);
        } else {
            $controller->$metodo();
        }
    }
}

/*  */
