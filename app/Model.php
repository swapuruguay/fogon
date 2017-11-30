<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class Model {
    
    protected $_db;
    
    public function __construct() {
        $this->_db = new Database();
    }
    
    protected function preparaInsert(array $datos) {
        $campos = '';
        $valores = '';
        $retorno = '';
        if(is_array($datos)) {
            foreach($datos as $campo => $valor) {
                $campos .= $campo . ',';
                $valores .= "'".$valor."',";
            }
            $retorno = '('.substr($campos, 0, strlen($campos)-1) . ') VALUES(' . substr($valores, 0, strlen($valores)-1) . ')';
            
        }
        return $retorno;
    }
    
    protected function preparaUpdate(array $datos) {
        
        $retorno = '';
        if(is_array($datos)) {
            foreach($datos as $campo => $valor) {
                $retorno .= $campo ."='" . $valor . "',";
            }
            $retorno = substr($retorno, 0, strlen($retorno)-1);
            
        }
        return $retorno;
    }
    
    public function getNroNuevo($tabla) {
        $sql = "Select AUTO_INCREMENT as nro FROM information_schema.tables WHERE TABLE_SCHEMA = 'swapdev1_fogon' And TABLE_NAME = '" . $tabla . "'";
        $cons = $this->_db->query($sql);
        $result = $cons->fetch(PDO::FETCH_OBJ);
        $nro = $result->nro;
       return $nro;
    }
}
