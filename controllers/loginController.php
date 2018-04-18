<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of loginController
 *
 * @author walter
 */
class loginController extends Controller{

    private $modelo;
    public function __construct(){
        parent::__construct();
    }

    public function index()
    {
      $this->_view->renderizar('index', false);
    }

    public function loguear()
    {
      $modelo  = $this->loadModel('login');
      $username = filter_input(INPUT_POST ,'username', FILTER_SANITIZE_STRING);
      $password = filter_input(INPUT_POST ,'password', FILTER_SANITIZE_STRING);
      $password = md5($password);
      $user = $modelo->getUser($username, $password);
      if($user) {
        Session::set('autenticado', true);
        Session::set('usuario', $user);
        //print_r(Session::get('usuario'));
        $this->redireccionar('index');
      } else {
        $this->redireccionar('login');
      }
    }

    public function cerrar()
    {
        Session::destroy();
        $this->redireccionar('login/mostrar');
    }
}
