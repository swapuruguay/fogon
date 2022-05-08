<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Ajustes
 *
 * @author walter
 */

class Ajuste {
    private $_id;
    private $_margen;
    private $_espacio;
    
    public function __construct($margen, $espacio) {
        
        $this->_margen = $margen;
        $this->_espacio = $espacio;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function setId($id) {
        $this->_id = $id;
    }
    
    public function getEspacio() {
        return $this->_espacio;
    }
    
    public function setEspacio($espacio) {
        $this->_espacio = $espacio;
    }
    
    public function getMargen() {
        return $this->_margen;
    }
    
    public function setMargen($margen) {
        $this->_margen = $margen;
    }
    
    public function __toString() {
        return $this->_margen . " " . $this->_espacio;
    }
}
