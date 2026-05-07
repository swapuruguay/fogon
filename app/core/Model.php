<?php

namespace App\Core;

class Model
{
    protected Database $db;
    protected $_db;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->_db = $this->db;
    }

    protected function dbSelect(string $sql, array $params = []): \PDOStatement
    {
        return $this->db->select($sql, $params);
    }

    protected function dbSelectOne(string $sql, array $params = []): ?object
    {
        return $this->db->selectOne($sql, $params);
    }

    protected function dbSelectAll(string $sql, array $params = []): array
    {
        return $this->db->selectAll($sql, $params);
    }

    protected function dbInsert(string $table, array $data): bool
    {
        return $this->db->insert($table, $data);
    }

    protected function dbUpdate(string $table, array $data, string $where, array $whereParams = []): bool
    {
        return $this->db->update($table, $data, $where, $whereParams);
    }

    protected function dbDelete(string $table, string $where, array $params = []): bool
    {
        return $this->db->delete($table, $where, $params);
    }

    protected function dbCount(string $table, string $where = '1=1', array $params = []): int
    {
        return $this->db->count($table, $where, $params);
    }

    public function getNroNuevo(string $tabla): int
    {
        $sql = "SELECT AUTO_INCREMENT as nro FROM information_schema.tables 
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";
        $result = $this->dbSelectOne($sql, [DB_NAME, $tabla]);
        return (int) ($result->nro ?? 1);
    }
}
