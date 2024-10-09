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
require_once 'Categoria.php';
class Socio {

    private $_id;
    private $_nombre;
    private $_apellido;
    private $_documento;
    private $_fechaIngreso;
    private $_fechaNacimiento;
    private $_estado;
    private $_domicilio;
    private $_telefono;
    private $_email;
    private $_categoria;
    private $_saldo;
    private $_exento;
    private $_foto;

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

    public  function getDomicilio() {
        return $this->_domicilio;
    }

    public function setDomicilio($domicilio) {
        $this->_domicilio = $domicilio;
    }

    public function getTelefono() {
        return $this->_telefono;
    }

    public function setTelefono($telefono) {
        $this->_telefono = $telefono;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function setEmail($email) {
        $this->_email = $email;
    }

    public function getFechaNacimiento() {
        return $this->_fechaNacimiento;
    }

    public function setFechaNacimiento($fechaNacimiento) {
        $this->_fechaNacimiento = $fechaNacimiento;
    }

    public function getFechaIngreso() {
        return $this->_fechaIngreso;
    }

    public function setFechaIngreso($fechaIngreso) {
        $this->_fechaIngreso = $fechaIngreso;
    }

    public function getEstado() {
        return $this->_estado;
    }

    public function setEstado($estado) {
        $this->_estado = $estado;
    }

    public function getCategoria() {
        return $this->_categoria;
    }

    public function setCategoria(Categoria $categoria) {
        $this->_categoria = $categoria;
    }

    public function setSaldo($saldo) {
        $this->_saldo = $saldo;
    }

    public function getSaldo() {
        return $this->_saldo;
    }

    public function setExento($exento) {
        $this->_exento = $exento;
    }

    public function getExento() {
        return $this->_exento;
    }

    public function setFoto($foto) {
        $this->_foto = $foto;
    }

    public function getFoto() {
        return $this->_foto;
    }

    public function __toString() {
        return $this->_nombre . ' ' . $this->_apellido;
    }
}
