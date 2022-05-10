<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of indexController
 *
 * @author walter
 */
class ajustesController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        $this->_view->titulo = NOMBRE;
        $this->_view->renderizar('index');
    }
    
    public function getajustes() {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        
        $modelo = $this->loadModel('ajustes');
        $ajuste = $modelo->get();
        $this->_view->margen = $ajuste->getMargen();
        $this->_view->espacio = $ajuste->getEspacio();
        $this->_view->renderizar('medidas');
        
    }
    
    public function setajustes() {
        $modelo  = $this->loadModel('ajustes');
        $ajuste = $modelo->get();
        $ajuste->setMargen(filter_input(INPUT_POST ,'margen', FILTER_SANITIZE_NUMBER_INT));
        $ajuste->setEspacio(filter_input(INPUT_POST ,'espacio', FILTER_SANITIZE_NUMBER_INT));
        if($modelo->set($ajuste, Session::get('usuario')->idusuario)) {
            echo json_encode(array("mensaje" => "Ajustes guardados con éxito", "color" => "green"));
        } else {
            echo json_encode(array("mensaje" => "Ocurrió un error, intente nuevamente", "color" => "red"));
        }
    }
}