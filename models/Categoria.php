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
    
    private function fixEncoding(string $str): string {
        $fixes = [
            "\xC3\x83\xC2\xB1" => "\xC3\xB1",
            "\xC3\x83\xC2\xA1" => "\xC3\xA1",
            "\xC3\x83\xC2\xA9" => "\xC3\xA9",
            "\xC3\x83\xC2\xAD" => "\xC3\xAD",
            "\xC3\x83\xC2\xB3" => "\xC3\xB3",
            "\xC3\x83\xC2\xBA" => "\xC3\xBA",
        ];
        return str_replace(array_keys($fixes), array_values($fixes), $str);
    }

    public function getNombre() {
        return $this->fixEncoding($this->_nombre);
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
        return $this->fixEncoding($this->_nombre);
    }
}
