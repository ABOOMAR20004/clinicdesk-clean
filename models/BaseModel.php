<?php

declare(strict_types=1);

abstract class BaseModel
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function execute(string $sql, string $types = '', array $params = []): mysqli_result|bool
    {
        return $this->db->query($sql, $types, $params);
    }

    protected function fetchOne(string $sql, string $types = '', array $params = []): ?array
    {
        $result = $this->execute($sql, $types, $params);
        $row = $result instanceof mysqli_result ? $result->fetch_assoc() : null;

        return $row ?: null;
    }

    protected function fetchAll(string $sql, string $types = '', array $params = []): array
    {
        $result = $this->execute($sql, $types, $params);

        return $result instanceof mysqli_result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    protected function countRows(string $sql, string $types = '', array $params = []): int
    {
        $row = $this->fetchOne($sql, $types, $params);

        return (int) ($row['total'] ?? 0);
    }
}
