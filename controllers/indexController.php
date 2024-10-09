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
class indexController extends Controller {

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
}
