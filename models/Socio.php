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
        return $this->fixEncoding($this->_nombre);
    }

    private function fixEncoding(?string $str): string {
        if ($str === null) {
            return '';
        }
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

    public function setNombre($nombre) {
        $this->_nombre = $nombre;
    }

    public function getApellido() {
        return $this->fixEncoding($this->_apellido);
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
        return $this->fixEncoding($this->_domicilio);
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

    public function isInHouse(): bool {
        $domicilio = strtolower(trim($this->_domicilio ?? ''));
        return $domicilio === 'el fogon' || $domicilio === 'el fogón' || $domicilio === 'fogon';
    }

    public function getCollectionType(): string {
        return $this->isInHouse() ? 'in_house' : 'street';
    }

    public function getCollectionTypeLabel(): string {
        return $this->isInHouse() ? 'En el Club' : 'En Calle';
    }
}
