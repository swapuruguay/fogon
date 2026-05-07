<?php

namespace App\Core;

class View
{
    private string $controlador;
    private array $js = [];
    private array $css = [];
    private mixed $arbolMenu = null;
    public array $data = [];

    public function __construct(string $controlador)
    {
        $this->controlador = $controlador;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function render(string $vista, bool $layout = true, mixed $item = false): void
    {
        $js = $this->js;
        $css = $this->css;

        $layoutParams = [
            'ruta_css' => BASE_URL . 'views/layout/' . DEFAULT_LAYOUT . '/css/',
            'ruta_img' => BASE_URL . 'views/layout/' . DEFAULT_LAYOUT . '/img/',
            'ruta_js' => BASE_URL . 'views/layout/' . DEFAULT_LAYOUT . '/js/',
            'menu' => $this->arbolMenu,
            'js' => $js,
            'css' => $css,
        ];

        $rutaView = ROOT . 'views' . DS . $this->controlador . DS . $vista . '.phtml';

        if (is_readable($rutaView)) {
            if ($layout) {
                extract($layoutParams);
                extract($this->data);
                include_once ROOT . 'views' . DS . 'layout' . DS . DEFAULT_LAYOUT . DS . 'header.php';
                include_once $rutaView;
                include_once ROOT . 'views' . DS . 'layout' . DS . DEFAULT_LAYOUT . DS . 'footer.php';
            } else {
                extract($this->data);
                include_once $rutaView;
            }
        } else {
            throw new \Exception("Error: Vista '$vista' no encontrada");
        }
    }

    public function renderizar(string $vista, bool $layout = true, mixed $item = false): void
    {
        $this->render($vista, $layout, $item);
    }

    public function setJs(array $js): void
    {
        foreach ($js as $file) {
            $this->js[] = BASE_URL . 'views/' . $this->controlador . '/js/' . $file . '.js';
        }
    }

    public function setCss(array $css): void
    {
        foreach ($css as $file) {
            $this->css[] = BASE_URL . 'views/' . $this->controlador . '/css/' . $file . '.css';
        }
    }

    public function setArbolMenu(mixed $arbol): void
    {
        $this->arbolMenu = $arbol;
    }
}
