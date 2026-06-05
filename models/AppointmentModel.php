<?php

declare(strict_types=1);

class AppointmentModel extends BaseModel
{
    private string $baseSelect = 'SELECT a.*, pu.name AS patient_name, pu.phone AS patient_phone,
        du.name AS doctor_name, d.user_id AS doctor_user_id, s.name AS specialization,
        p.id AS prescription_id, p.file_path AS prescription_file
        FROM appointments a
        INNER JOIN users pu ON pu.id = a.patient_id
        INNER JOIN doctors d ON d.id = a.doctor_id
        INNER JOIN users du ON du.id = d.user_id
        INNER JOIN specializations s ON s.id = d.specialization_id
        LEFT JOIN prescriptions p ON p.appointment_id = a.id';

    public function book(array $data): bool
    {
        try {
            $this->execute(
                'INSERT INTO appointments (patient_id, doctor_id, appt_date, appt_time, status, reason) VALUES (?, ?, ?, ?, "pending", ?)',
                'iisss',
                [
                    (int) $data['patient_id'],
                    (int) $data['doctor_id'],
                    $data['appt_date'],
                    $data['appt_time'],
                    $data['reason'] ?? null,
                ]
            );
        } catch (RuntimeException $e) {
            return false;
        }

        return true;
    }

    public function hasConflict(int $doctorId, string $date, string $time): bool
    {
        return $this->countRows(
            'SELECT COUNT(*) AS total FROM appointments WHERE doctor_id = ? AND appt_date = ? AND appt_time = ?',
            'iss',
            [$doctorId, $date, $time]
        ) > 0;
    }

    public function getByPatient(int $patientId, int $page, array $filters): array
    {
        [$where, $types, $params] = $this->buildFilters($filters, 'patient', $patientId);
        $params[] = ITEMS_PER_PAGE;
        $params[] = max(0, ($page - 1) * ITEMS_PER_PAGE);

        return $this->fetchAll(
            $this->baseSelect . ' ' . $where . ' ORDER BY a.appt_date DESC, a.appt_time DESC LIMIT ? OFFSET ?',
            $types . 'ii',
            $params
        );
    }

    public function getByDoctor(int $doctorId, int $page, array $filters): array
    {
        [$where, $types, $params] = $this->buildFilters($filters, 'doctor', $doctorId);
        $params[] = ITEMS_PER_PAGE;
        $params[] = max(0, ($page - 1) * ITEMS_PER_PAGE);

        return $this->fetchAll(
            $this->baseSelect . ' ' . $where . ' ORDER BY a.appt_date DESC, a.appt_time DESC LIMIT ? OFFSET ?',
            $types . 'ii',
            $params
        );
    }

    public function getAll(int $page, array $filters): array
    {
        [$where, $types, $params] = $this->buildFilters($filters, 'admin', 0);
        $params[] = ITEMS_PER_PAGE;
        $params[] = max(0, ($page - 1) * ITEMS_PER_PAGE);

        return $this->fetchAll(
            $this->baseSelect . ' ' . $where . ' ORDER BY a.appt_date DESC, a.appt_time DESC LIMIT ? OFFSET ?',
            $types . 'ii',
            $params
        );
    }

    public function countFiltered(string $scope, int $scopeId, array $filters): int
    {
        [$where, $types, $params] = $this->buildFilters($filters, $scope, $scopeId);

        return $this->countRows(
            'SELECT COUNT(*) AS total
             FROM appointments a
             INNER JOIN users pu ON pu.id = a.patient_id
             INNER JOIN doctors d ON d.id = a.doctor_id
             INNER JOIN users du ON du.id = d.user_id
             INNER JOIN specializations s ON s.id = d.specialization_id ' . $where,
            $types,
            $params
        );
    }

    public function updateStatus(int $id, string $status, string $notes = ''): bool
    {
        $this->execute(
            'UPDATE appointments SET status = ?, doctor_notes = COALESCE(NULLIF(?, ""), doctor_notes) WHERE id = ?',
            'ssi',
            [$status, $notes, $id]
        );

        return true;
    }

    public function findById(int $id): ?array
    {
        return $this->fetchOne($this->baseSelect . ' WHERE a.id = ?', 'i', [$id]);
    }

    public function getTodayByDoctor(int $doctorId): array
    {
        return $this->fetchAll(
            $this->baseSelect . ' WHERE a.doctor_id = ? AND a.appt_date = CURDATE() ORDER BY a.appt_time ASC',
            'i',
            [$doctorId]
        );
    }

    public function getUpcomingByDoctor(int $doctorId, int $limit = 5): array
    {
        return $this->fetchAll(
            $this->baseSelect . ' WHERE a.doctor_id = ? AND a.appt_date >= CURDATE() ORDER BY a.appt_date ASC, a.appt_time ASC LIMIT ?',
            'ii',
            [$doctorId, $limit]
        );
    }

    public function getRecent(int $limit = 5): array
    {
        return $this->fetchAll($this->baseSelect . ' ORDER BY a.created_at DESC LIMIT ?', 'i', [$limit]);
    }

    public function countToday(): int
    {
        return $this->countRows('SELECT COUNT(*) AS total FROM appointments WHERE appt_date = CURDATE()');
    }

    public function countThisWeekByStatus(): array
    {
        return $this->fetchAll(
            'SELECT status, COUNT(*) AS total
             FROM appointments
             WHERE YEARWEEK(appt_date, 1) = YEARWEEK(CURDATE(), 1)
             GROUP BY status'
        );
    }

    public function doctorMonthlyCounts(int $doctorId): array
    {
        return $this->fetchAll(
            'SELECT status, COUNT(*) AS total
             FROM appointments
             WHERE doctor_id = ? AND MONTH(appt_date) = MONTH(CURDATE()) AND YEAR(appt_date) = YEAR(CURDATE())
             GROUP BY status',
            'i',
            [$doctorId]
        );
    }

    public function patientStats(int $patientId): array
    {
        $active = $this->countRows(
            'SELECT COUNT(*) AS total FROM appointments WHERE patient_id = ? AND status IN ("pending", "confirmed")',
            'i',
            [$patientId]
        );
        $completed = $this->countRows(
            'SELECT COUNT(*) AS total FROM appointments WHERE patient_id = ? AND status = "completed"',
            'i',
            [$patientId]
        );
        $next = $this->fetchOne(
            $this->baseSelect . ' WHERE a.patient_id = ? AND a.status IN ("pending", "confirmed") AND a.appt_date >= CURDATE()
             ORDER BY a.appt_date ASC, a.appt_time ASC LIMIT 1',
            'i',
            [$patientId]
        );
        $activeList = $this->fetchAll(
            $this->baseSelect . ' WHERE a.patient_id = ? AND a.status IN ("pending", "confirmed")
             ORDER BY a.appt_date ASC, a.appt_time ASC LIMIT 5',
            'i',
            [$patientId]
        );

        return compact('active', 'completed', 'next', 'activeList');
    }

    public function report(array $filters): array
    {
        [$where, $types, $params] = $this->buildReportFilters($filters);

        return $this->fetchAll(
            $this->baseSelect . ' ' . $where . ' ORDER BY a.appt_date ASC, a.appt_time ASC',
            $types,
            $params
        );
    }

    public function chartLast14Days(): array
    {
        return $this->fetchAll(
            'SELECT appt_date, COUNT(*) AS total
             FROM appointments
             WHERE appt_date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
             GROUP BY appt_date
             ORDER BY appt_date'
        );
    }

    private function buildFilters(array $filters, string $scope, int $scopeId): array
    {
        $conditions = [];
        $types = '';
        $params = [];

        if ($scope === 'patient') {
            $conditions[] = 'a.patient_id = ?';
            $types .= 'i';
            $params[] = $scopeId;
        } elseif ($scope === 'doctor') {
            $conditions[] = 'a.doctor_id = ?';
            $types .= 'i';
            $params[] = $scopeId;
        }

        if (!empty($filters['doctor_id'])) {
            $conditions[] = 'a.doctor_id = ?';
            $types .= 'i';
            $params[] = (int) $filters['doctor_id'];
        }

        if (!empty($filters['patient_search'])) {
            $conditions[] = 'pu.name LIKE ?';
            $types .= 's';
            $params[] = '%' . $filters['patient_search'] . '%';
        }

        if (!empty($filters['status']) && in_array($filters['status'], statusOptions(), true)) {
            $conditions[] = 'a.status = ?';
            $types .= 's';
            $params[] = $filters['status'];
        }

        if (!empty($filters['start_date'])) {
            $conditions[] = 'a.appt_date >= ?';
            $types .= 's';
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $conditions[] = 'a.appt_date <= ?';
            $types .= 's';
            $params[] = $filters['end_date'];
        }

        return [$conditions ? 'WHERE ' . implode(' AND ', $conditions) : '', $types, $params];
    }

    private function buildReportFilters(array $filters): array
    {
        $conditions = ['a.appt_date BETWEEN ? AND ?'];
        $types = 'ss';
        $params = [$filters['start_date'], $filters['end_date']];

        if (!empty($filters['doctor_id'])) {
            $conditions[] = 'a.doctor_id = ?';
            $types .= 'i';
            $params[] = (int) $filters['doctor_id'];
        }

        if (!empty($filters['status']) && in_array($filters['status'], statusOptions(), true)) {
            $conditions[] = 'a.status = ?';
            $types .= 's';
            $params[] = $filters['status'];
        }

        return ['WHERE ' . implode(' AND ', $conditions), $types, $params];
    }
}
