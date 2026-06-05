<?php

declare(strict_types=1);

class SpecializationModel extends BaseModel
{
    public function getAll(): array
    {
        return $this->fetchAll('SELECT * FROM specializations ORDER BY name ASC');
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM specializations WHERE id = ?', 'i', [$id]);
    }

    public function create(string $name): int
    {
        $this->execute('INSERT INTO specializations (name) VALUES (?)', 's', [$name]);

        return $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $this->execute('DELETE FROM specializations WHERE id = ?', 'i', [$id]);

        return true;
    }

    public function isSafeToDelete(int $id): bool
    {
        return $this->countRows('SELECT COUNT(*) AS total FROM doctors WHERE specialization_id = ?', 'i', [$id]) === 0;
    }
}
