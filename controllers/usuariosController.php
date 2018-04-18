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
class usuariosController extends Controller {

    private $_ajax;

    public function __construct() {
        parent::__construct();
        $this->_ajax  = $this->loadModel('usuarios');
    }

    public function index() {
        if(!Session::get('autenticado')) {
            $this->redireccionar('login');
        }
        $this->_view->titulo = NOMBRE;
        $this->_view->renderizar('index');
    }

    public function cambiar() {
      if(!Session::get('autenticado')) {
          $this->redireccionar('login');
      }
      $this->_view->titulo = NOMBRE;
      $this->_view->renderizar('cambiar');
    }

    public function change() {
      $id = filter_input(INPUT_POST ,'id', FILTER_SANITIZE_NUMBER_INT);
      $password = filter_input(INPUT_POST ,'password', FILTER_SANITIZE_STRING);
      if($this->_ajax->cambiarPass($id, $password)) {
        echo "Password cambiado correctamente";
      } else {
        echo "Ocurrió un error, intente más tarde";
      }
    }

    public function logout() {
      Session::destroy();
      $this->redireccionar('index');
    }
}
