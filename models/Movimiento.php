<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Movimiento
 *
 * @author walter
 */
class Movimiento {
    
    private $_id;
    
    private $_socio;
    
    private $_importe;
    
    private $_mes;
    
    private $_anio;
    
    private $_fecha;
    
    public function __construct($id = 0, Socio $socio = null, $mes=0, $anio=0, $importe=0) {
        $this->_id = $id;
        $this->_socio = $socio;
        $this->_importe = $importe;
        
    }
    
    public function setFecha($fecha) {
        $this->_fecha = $fecha;
    }
    
    public function getFecha() {
        return $this->_fecha;
    }
    
    public function setId($id) {
        $this->_id = $id;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function setSocio(Socio $socio) {
        $this->_socio = $socio;
    }
    
    public function getSocio() {
        return $this->_socio;
    }
    
    public function setImporte($importe) {
        $this->_importe = $importe;
    }
    
    public function getImporte() {
        return $this->_importe;
    }
    
    public function setMes($mes) {
        $this->_mes = $mes;
    }
    
    public function getMes() {
        return $this->_mes;
    }
    
    public function setAnio($anio) {
        $this->_anio = $anio;
    }
    
    public function getAnio() {
        return $this->_anio;
    }
    
    public function __toString() {
        return $this->_socio . " " . $this->_mes ."/" . $this->_anio; 
    }
    
    
}
