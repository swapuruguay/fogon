<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sociosModel
 *
 * @author walter
 */


class usuariosModel extends Model{

    public function __construct() {
        parent::__construct();

    }

    public function cambiarPass($id, $password) {
      $sql = "UPDATE usuarios SET password = '" . md5($password) ."' WHERE idusuario=" . $id;
      //echo $sql;
      return $this->_db->query($sql);
    }
}
