<?php

class Database extends PDO {
    public function __construct() {
        parent::__construct(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    public function select(string $sql, array $params = []): PDOStatement {
        $stmt = $this->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function selectOne(string $sql, array $params = []): ?object {
        $result = $this->select($sql, $params)->fetch();
        return $result ?: null;
    }

    public function selectAll(string $sql, array $params = []): array {
        return $this->select($sql, $params)->fetchAll() ?: [];
    }

    public function insert(string $table, array $data): bool {
        $columns = implode(', ', array_map(fn($col) => "`$col`", array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO `$table` ($columns) VALUES ($placeholders)";
        return $this->prepare($sql)->execute(array_values($data));
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): bool {
        $set = implode(', ', array_map(fn($col) => "`$col` = ?", array_keys($data)));
        $sql = "UPDATE `$table` SET $set WHERE $where";
        return $this->prepare($sql)->execute(array_merge(array_values($data), $whereParams));
    }
}
