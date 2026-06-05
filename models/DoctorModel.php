<?php

declare(strict_types=1);

class DoctorModel extends BaseModel
{
    private string $selectJoin = 'SELECT d.*, u.name, u.email, u.phone, u.avatar, s.name AS specialization
        FROM doctors d
        INNER JOIN users u ON u.id = d.user_id
        INNER JOIN specializations s ON s.id = d.specialization_id';

    public function findById(int $id): ?array
    {
        return $this->fetchOne($this->selectJoin . ' WHERE d.id = ?', 'i', [$id]);
    }

    public function findByUserId(int $userId): ?array
    {
        return $this->fetchOne($this->selectJoin . ' WHERE d.user_id = ?', 'i', [$userId]);
    }

    public function getAll(): array
    {
        return $this->fetchAll($this->selectJoin . ' WHERE u.is_active = 1 ORDER BY u.name ASC');
    }

    public function getAllPaginated(int $page): array
    {
        return $this->fetchAll(
            $this->selectJoin . ' ORDER BY u.name ASC LIMIT ? OFFSET ?',
            'ii',
            [ITEMS_PER_PAGE, max(0, ($page - 1) * ITEMS_PER_PAGE)]
        );
    }

    public function countAll(): int
    {
        return $this->countRows('SELECT COUNT(*) AS total FROM doctors');
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO doctors (user_id, specialization_id, bio, consultation_fee, available_days) VALUES (?, ?, ?, ?, ?)',
            'iisds',
            [
                (int) $data['user_id'],
                (int) $data['specialization_id'],
                $data['bio'] ?? null,
                (float) ($data['consultation_fee'] ?? 0),
                $data['available_days'] ?? 'Sun,Mon,Tue,Wed,Thu',
            ]
        );

        return $this->db->lastInsertId();
    }

    public function update(int $doctorId, array $data): bool
    {
        $this->execute(
            'UPDATE doctors SET specialization_id = ?, bio = ?, consultation_fee = ?, available_days = ? WHERE id = ?',
            'isdsi',
            [
                (int) $data['specialization_id'],
                $data['bio'] ?? null,
                (float) ($data['consultation_fee'] ?? 0),
                $data['available_days'],
                $doctorId,
            ]
        );

        return true;
    }

    public function getAvailableDays(int $doctorId): array
    {
        $row = $this->fetchOne('SELECT available_days FROM doctors WHERE id = ?', 'i', [$doctorId]);
        if (!$row || empty($row['available_days'])) {
            return [];
        }

        return array_map('trim', explode(',', $row['available_days']));
    }
}
