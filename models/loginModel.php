<?php

class LoginModel extends Model {
  public function __construct() {
    parent::__construct();
  }

  public function getUser($username, $password) {

    $result = $this->_db->query("SELECT * FROM usuarios WHERE username = '$username' AND password='$password'" );
    $listado = $result->fetch(PDO::FETCH_OBJ);
    if($listado) {
      return $listado;
    } else {
      return false;
    }

  }


}
