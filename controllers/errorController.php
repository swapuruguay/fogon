<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of errorController
 *
 * @author walter
 */
class errorController extends Controller{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function index() {
        $this->_view->titulo = 'Error';
        $this->_view->mensaje = $this->_getError();
    }
    
    private function _getError($codigo = false) {
        if($codigo) {
            $codigo = $this->filtrarInt($codigo);
        } else {
            $codigo = 'default';
        }
        
        $error['default'] = 'Ha ocurrido un error y la p&aacute;gina no puede mostrarse';
        $error[5050] = 'Acceso restringido';
        
        if(array_key_exists($codigo, $error)) {
            return $error[$codigo];
        } else {
            return $error['default'];
        }
    }
    
    public function access($codigo)
    {
        $this->_view->titulo = 'Error';
        $this->_view->mensaje = $this->_getError($codigo);
        $this->_view->renderizar('access');
    }

//put your code here
}
