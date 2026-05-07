<?php

class LoginModel extends Model {
    public function __construct() {
        parent::__construct();
    }

    public function getUser(string $username, string $password): object|false {
        $sql = "SELECT * FROM usuarios WHERE username = ? AND password = ?";
        $result = $this->_db->select($sql, [$username, $password]);
        $user = $result->fetch(PDO::FETCH_OBJ);
        return $user ?: false;
    }
}
