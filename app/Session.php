<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Session
 *
 * @author walter
 */
class Session {


    public static function init() {
        session_start();
    }

    public static function destroy($clave = false) {
        if($clave) {
            if(is_array($clave)) {
                if(isset($_SESSION[$clave][$i])) {
                    unset($_SESSION[$clave][$i]);
                }
            } else {
                unset($_SESSION[$clave]);
            }
        } else {
            session_destroy();
        }
    }

    public static function set($clave, $valor) {
        if(!empty($_SESSION[$clave]))
            $_SESSION[$clave] = $valor;
    }

    /**
     * Obtiene el valor de la variable de Sesion $clave
     * @param $clave nombre de la clave
     * return mixed
     */ 
    public static function get($clave) {
        if(isset($_SESSION[$clave]))
            return $_SESSION[$clave];
    }
    
    public static function acceso($level)
    {
        if(!Session::get('autenticado')){
            header('location:' . BASE_URL . 'error/access/5050');
            exit;
        }
        
        if(Session::getLevel($level) > Session::getLevel(Session::get('level'))){
            header('location:' . BASE_URL . 'error/access/5050');
            exit;
        }
    }
    
    public static function accesoView($level)
    {
        if(!Session::get('autenticado')){
            return false;
        }
        
        if(Session::getLevel($level) > Session::getLevel(Session::get('level'))){
            return false;
        }
        
        return true;
    }
    
    public static function getLevel($level)
    {
        $role['admin'] = 3;
        $role['especial'] = 2;
        $role['usuario'] = 1;
        
        if(!array_key_exists($level, $role)){
            throw new Exception('Error de acceso');
        }
        else{
            return $role[$level];
        }
    }
}
