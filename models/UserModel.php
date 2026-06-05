<?php

declare(strict_types=1);

class UserModel extends BaseModel
{
    public function findById(int $id): ?array
    {
        return $this->fetchOne('SELECT * FROM users WHERE id = ?', 'i', [$id]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne('SELECT * FROM users WHERE email = ?', 's', [$email]);
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO users (name, email, password, role, phone, avatar) VALUES (?, ?, ?, ?, ?, ?)',
            'ssssss',
            [
                $data['name'],
                $data['email'],
                $data['password'],
                $data['role'],
                $data['phone'] ?? null,
                $data['avatar'] ?? null,
            ]
        );

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $this->execute(
            'UPDATE users SET name = ?, phone = ?, avatar = COALESCE(?, avatar) WHERE id = ?',
            'sssi',
            [$data['name'], $data['phone'] ?? null, $data['avatar'] ?? null, $id]
        );

        return true;
    }

    public function updatePassword(int $id, string $newHash): bool
    {
        $this->execute('UPDATE users SET password = ? WHERE id = ?', 'si', [$newHash, $id]);

        return true;
    }

    public function getAllPaginated(int $page, string $role = '', string $search = ''): array
    {
        [$where, $types, $params] = $this->buildFilters($role, $search);
        $params[] = ITEMS_PER_PAGE;
        $params[] = max(0, ($page - 1) * ITEMS_PER_PAGE);

        return $this->fetchAll(
            'SELECT * FROM users ' . $where . ' ORDER BY created_at DESC LIMIT ? OFFSET ?',
            $types . 'ii',
            $params
        );
    }

    public function countAll(string $role = '', string $search = ''): int
    {
        [$where, $types, $params] = $this->buildFilters($role, $search);

        return $this->countRows('SELECT COUNT(*) AS total FROM users ' . $where, $types, $params);
    }

    public function toggleActive(int $id): bool
    {
        $this->execute('UPDATE users SET is_active = IF(is_active = 1, 0, 1) WHERE id = ?', 'i', [$id]);

        return true;
    }

    public function updateAvatar(int $id, string $path): bool
    {
        $this->execute('UPDATE users SET avatar = ? WHERE id = ?', 'si', [$path, $id]);

        return true;
    }

    public function countByRole(): array
    {
        return $this->fetchAll('SELECT role, COUNT(*) AS total FROM users GROUP BY role');
    }

    private function buildFilters(string $role, string $search): array
    {
        $conditions = [];
        $types = '';
        $params = [];

        if ($role !== '' && in_array($role, ['admin', 'doctor', 'patient'], true)) {
            $conditions[] = 'role = ?';
            $types .= 's';
            $params[] = $role;
        }

        if ($search !== '') {
            $conditions[] = '(name LIKE ? OR email LIKE ?)';
            $types .= 'ss';
            $needle = '%' . $search . '%';
            $params[] = $needle;
            $params[] = $needle;
        }

        return [$conditions ? 'WHERE ' . implode(' AND ', $conditions) : '', $types, $params];
    }
}
