<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ajaxModel
 *
 * @author walter
 */
class ajaxModel extends Model{
    
    public function __construct() {
        parent::__construct();
    }
    
    public function guardarSocio(Socio $soc) {
        if($soc.getId()==0) {
            $dato = array(
                'id'        => $soc->getId(),
                'nombre'    => $soc->getNombre(),
                'apellido'  => $soc->getApellido(),
                'domicilio' => $soc->getDomicilio(),
                'telefono'  => $soc->getTelefono(),
                'fecha_ingreso' => $soc->getFechaIngreso(),
                'fecha_nacimiento' => $soc->getFechaNacimiento(),
                'estado'            => $soc->getEstado(),
                
            );
            $this->_db->query("INSERT INTO  socios ");
        } else {
            
        }
        
    }
    
    
    public function buscar() {
        
    }
}
