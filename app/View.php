<?php



class View
{
    private $_controlador;

    private $_js;

    private $_arbolMenu;


    public function __construct(Request $peticion) {
        $this->_controlador = $peticion->getControlador();
    }

    public function renderizar($vista, $l=true, $item = false)
    {

        $js = array();

        if($this->_js && count($this->_js)) {
            $js = $this->_js;
        }


        $_layoutParams = array(
            'ruta_css' => BASE_URL . 'views/layout/' . DEFAULT_LAYOUT . '/css/',
            'ruta_img' => BASE_URL . 'views/layout/' . DEFAULT_LAYOUT . '/img/',
            'ruta_js' => BASE_URL . 'views/layout/' . DEFAULT_LAYOUT . '/js/',
            'menu' => $this->_arbolMenu,
            'js'   => $js
        );

        $rutaView = ROOT . 'views' . DS . $this->_controlador . DS . $vista . '.phtml';

        if(is_readable($rutaView)){
            if($l) {
              include_once ROOT . 'views'. DS . 'layout' . DS . DEFAULT_LAYOUT . DS . 'header.php';
              include_once $rutaView;
              include_once ROOT . 'views'. DS . 'layout' . DS . DEFAULT_LAYOUT . DS . 'footer.php';
            } else {
              include_once $rutaView;
            }
        }
        else {
            throw new Exception('Error de vista');
        }
    }

    public function setJs(array $js) {
        if(is_array($js) && count($js)) {
            for($i=0; $i < count($js);$i++) {
                $this->_js[] = BASE_URL . '/views/' . $this->_controlador . '/js/' . $js[$i] . '.js';
            }
        }
    }

    public function setArbolMenu($arbol) {
      $this->_arbolMenu = $arbol;
    }


}
