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
    
    public function __construct(){
        parent::__construct();
    }
    
    public function index()
    {
        Session::set('autenticado', true);
        Session::set('level', 'especial');
        
        Session::set('var1', 'var1');
        Session::set('var2', 'var2');
        
        $this->redireccionar('login/mostrar');
    }
    
    public function mostrar()
    {
        echo 'Level: '. Session::get('level') . '<br>';
        echo 'Var1: '. Session::get('var1') . '<br>';
        echo 'Var2: '. Session::get('var2') . '<br>';
    }
    
    public function cerrar()
    {
        Session::destroy();
        $this->redireccionar('login/mostrar');
    }
}
