<?php

namespace App\Core;

use PDO;
use PDOException;

class Database extends PDO
{
    private static ?Database $instance = null;

    public function __construct()
    {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_NAME
        );

        parent::__construct(
            $dsn,
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function select(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function selectOne(string $sql, array $params = []): ?object
    {
        $result = $this->select($sql, $params)->fetch();
        return $result ?: null;
    }

    public function selectAll(string $sql, array $params = []): array
    {
        return $this->select($sql, $params)->fetchAll() ?: [];
    }

    public function insert(string $table, array $data): bool
    {
        $columns = implode(', ', array_map(fn($col) => "`$col`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO `$table` ($columns) VALUES ($placeholders)";

        $stmt = $this->prepare($sql);
        return $stmt->execute(array_values($data));
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): bool
    {
        $set = implode(', ', array_map(fn($col) => "`$col` = ?", array_keys($data)));
        $sql = "UPDATE `$table` SET $set WHERE $where";

        $params = array_merge(array_values($data), $whereParams);
        $stmt = $this->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(string $table, string $where, array $params = []): bool
    {
        $sql = "DELETE FROM `$table` WHERE $where";
        $stmt = $this->prepare($sql);
        return $stmt->execute($params);
    }

    public function count(string $table, string $where = '1=1', array $params = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM `$table` WHERE $where";
        $result = $this->selectOne($sql, $params);
        return (int) ($result->total ?? 0);
    }

    public function lastInsertId(?string $name = null): string|false
    {
        return parent::lastInsertId($name);
    }
}
