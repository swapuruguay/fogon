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
require_once 'Categoria.php';

class categoriasModel extends Model{
    
    public function __construct() {
        parent::__construct();
    }
    
    
    public function getAll() {
        $consulta = $this->_db->query("SELECT * FROM categorias");
        $retorno = array();
        $resultado = $consulta->fetchall(PDO::FETCH_OBJ);
        foreach($resultado as $valor) {
            
            $aux = new Categoria($valor->id_categoria, $valor->nombre, $valor->importe);
            $retorno[] = $aux;
            
        }
        return $retorno;
    }
    
    public function getById($id) {
        $consulta = $this->_db->query("SELECT * FROM categorias WHERE id_categoria = ".$id);
        $resultado = $consulta->fetch(PDO::FETCH_OBJ);
        return new Categoria($resultado->id_categoria, $resultado->nombre, $resultado->importe);
    }
}
