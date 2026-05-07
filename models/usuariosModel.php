<?php

class usuariosModel extends Model {

    public function __construct() {
        parent::__construct();
    }

    public function cambiarPass(int $id, string $password): bool {
        $sql = "UPDATE usuarios SET password = ? WHERE idusuario = ?";
        return $this->_db->select($sql, [md5($password), $id])->rowCount() > 0;
    }
}
