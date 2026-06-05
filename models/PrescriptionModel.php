<?php

declare(strict_types=1);

class PrescriptionModel extends BaseModel
{
    public function findByAppointmentId(int $apptId): ?array
    {
        return $this->fetchOne('SELECT * FROM prescriptions WHERE appointment_id = ?', 'i', [$apptId]);
    }

    public function create(array $data): int
    {
        $this->execute(
            'INSERT INTO prescriptions (appointment_id, diagnosis, medications, notes, file_path) VALUES (?, ?, ?, ?, ?)',
            'issss',
            [
                (int) $data['appointment_id'],
                $data['diagnosis'],
                $data['medications'],
                $data['notes'] ?? null,
                $data['file_path'] ?? null,
            ]
        );

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $this->execute(
            'UPDATE prescriptions SET diagnosis = ?, medications = ?, notes = ?, file_path = COALESCE(?, file_path) WHERE id = ?',
            'ssssi',
            [
                $data['diagnosis'],
                $data['medications'],
                $data['notes'] ?? null,
                $data['file_path'] ?? null,
                $id,
            ]
        );

        return true;
    }

    public function getByPatient(int $patientId): array
    {
        return $this->fetchAll(
            'SELECT p.*, a.appt_date, a.appt_time, du.name AS doctor_name
             FROM prescriptions p
             INNER JOIN appointments a ON a.id = p.appointment_id
             INNER JOIN doctors d ON d.id = a.doctor_id
             INNER JOIN users du ON du.id = d.user_id
             WHERE a.patient_id = ?
             ORDER BY p.created_at DESC',
            'i',
            [$patientId]
        );
    }

    public function findWithAppointment(int $apptId): ?array
    {
        return $this->fetchOne(
            'SELECT p.*, a.patient_id, a.doctor_id, d.user_id AS doctor_user_id
             FROM prescriptions p
             INNER JOIN appointments a ON a.id = p.appointment_id
             INNER JOIN doctors d ON d.id = a.doctor_id
             WHERE p.appointment_id = ?',
            'i',
            [$apptId]
        );
    }

    public function countByPatient(int $patientId): int
    {
        return $this->countRows(
            'SELECT COUNT(*) AS total
             FROM prescriptions p
             INNER JOIN appointments a ON a.id = p.appointment_id
             WHERE a.patient_id = ?',
            'i',
            [$patientId]
        );
    }
}
