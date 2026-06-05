<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/database.php';

final class Database
{
    private static ?Database $instance = null;
    private mysqli $conn;

    private function __construct()
    {
        mysqli_report(MYSQLI_REPORT_OFF);

        $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conn->connect_errno) {
            throw new RuntimeException('Database connection failed.');
        }

        if (!$this->conn->set_charset(DB_CHARSET)) {
            throw new RuntimeException('Database charset setup failed.');
        }
    }

    private function __clone()
    {
    }

    public function __wakeup(): void
    {
        throw new RuntimeException('Cannot unserialize database connection.');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function query(string $sql, string $types = '', array $params = []): mysqli_result|bool
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new RuntimeException('Database query could not be prepared.');
        }

        if ($types !== '') {
            $refs = [$types];
            foreach ($params as $key => $value) {
                $refs[] = &$params[$key];
            }
            if (!call_user_func_array([$stmt, 'bind_param'], $refs)) {
                throw new RuntimeException('Database query parameters could not be bound.');
            }
        }

        if (!$stmt->execute()) {
            throw new RuntimeException('Database query failed.');
        }

        if (preg_match('/^\s*(SELECT|SHOW|DESCRIBE|EXPLAIN)\b/i', $sql)) {
            return $stmt->get_result();
        }

        return true;
    }

    public function lastInsertId(): int
    {
        return (int) $this->conn->insert_id;
    }
}
