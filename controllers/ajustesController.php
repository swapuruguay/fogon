<?php

use App\Core\Controller;
use App\Core\Session;

class ajustesController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index(): void {
        $this->requireAuth();
        $this->_view->titulo = NOMBRE;
        $this->_view->renderizar('index');
    }

    public function getajustes(): void {
        $this->requireAuth();
        $modelo = $this->loadModel('ajustes');
        $ajuste = $modelo->get();
        $this->_view->margen = $ajuste->getMargen();
        $this->_view->espacio = $ajuste->getEspacio();
        $this->_view->left = $ajuste->getLeft();
        $this->_view->renderizar('medidas');
    }
public function setajustes(): void
    {
        $this->requireAuth();
        $modelo = $this->loadModel('ajustes');
        $ajuste = $modelo->get();
        $ajuste->setMargen(filter_input(INPUT_POST, 'margen', FILTER_VALIDATE_INT) ?: 0);
        $ajuste->setEspacio(filter_input(INPUT_POST, 'espacio', FILTER_VALIDATE_INT) ?: 0);
        $ajuste->setLeft(filter_input(INPUT_POST, 'left', FILTER_VALIDATE_INT) ?: 0);
        if ($modelo->set($ajuste, Session::get('usuario')->idusuario)) {
            echo json_encode(["mensaje" => "Ajustes guardados con éxito", "color" => "green"]);
        } else {
            echo json_encode(["mensaje" => "Ocurrió un error, intente nuevamente", "color" => "red"]);
        }
    }
}
