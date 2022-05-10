<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of categoriasModelo
 *
 * @author walter
 */

require_once 'Ajuste.php';

class ajustesModel extends Model{
    
    public function __construct() {
        parent::__construct();
    }
    
    
    public function get() {
        $consulta = $this->_db->query("SELECT * FROM ajustes");
        $retorno = array();
        $resultado = $consulta->fetch(PDO::FETCH_OBJ);
        $aux = new Ajuste($resultado->margen, $resultado->espacio);
        $retorno = $aux;
        return $retorno;
    }
    
    public function set(Ajuste $ajuste, $usuario) {
        $datos = array(

            'margen'            => $ajuste->getMargen(),
            'espacio'          => $ajuste->getEspacio()
        );

        $sql = 'UPDATE ajustes SET ' . $this->preparaUpdate($datos);

        return $this->_db->query($sql);
    }
}
