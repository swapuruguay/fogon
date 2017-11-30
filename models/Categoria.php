<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Categoria
 *
 * @author walter
 */

class Categoria {
    private $_id;
    private $_nombre;
    private $_importe;
    
    public function __construct($id, $nombre, $importe) {
        $this->_id = $id;
        $this->_nombre = $nombre;
        $this->_importe = $importe;
    }
    
    public function getId() {
        return $this->_id;
    }
    
    public function setId($id) {
        $this->_id = $id;
    }
    
    public function getNombre() {
        return $this->_nombre;
    }
    
    public function setNombre($nombre) {
        $this->_nombre = $nombre;
    }
    
    public function getImporte() {
        return $this->_importe;
    }
    
    public function setImporte($importe) {
        $this->_importe = $importe;
    }
    
    public function __toString() {
        return $this->_nombre;
    }
}
