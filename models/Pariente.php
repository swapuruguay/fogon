<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Socio
 *
 * @author walter
 */

class Pariente {

    private $_id;
    private $_idSocio;
    private $_nombre;
    private $_apellido;
    private $_documento;
    private $_fechaNacimiento;
    private $_sexo;
    private $_parentezco;

    public function __construct($id, $nombre, $apellido) {
        $this->_id = $id;
        $this->_apellido = $apellido;
        $this->_nombre = $nombre;
    }

    public function getId(){
        return $this->_id;
    }

    public function setId($id) {
        $this->_id = $id;
    }

    public function getIdSocio(){
        return $this->_idSocio;
    }

    public function setIdSocio($idSocio) {
        $this->_idSocio = $idSocio;
    }

    public function getNombre() {
        return $this->_nombre;
    }

    public function setNombre($nombre) {
        $this->_nombre = $nombre;
    }

    public function getApellido() {
        return $this->_apellido;
    }

    public function setApellido($apellido) {
        $this->_apellido = $apellido;
    }

    public function getDocumento() {
        return $this->_documento;
    }

    public function setDocumento($documento) {
        $this->_documento = $documento;
    }

    public  function getSexo() {
        return $this->_sexo;
    }

    public function setSexo($sexo) {
        $this->_sexo = $sexo;
    }

    public function getParentezco() {
        return $this->_parentezco;
    }

    public function setParentezco($parentezco) {
        $this->_parentezco = $parentezco;
    }

    public function getFechaNacimiento() {
        return $this->_fechaNacimiento;
    }

    public function setFechaNacimiento($fechaNacimiento) {
        $this->_fechaNacimiento = $fechaNacimiento;
    }

    public function __toString() {
        return $this->_nombre . ' ' . $this->_apellido;
    }
}
