<?php

declare(strict_types=1);

class ReportController
{
    public function index(): void
    {
        Auth::requireRole('admin');

        $filters = [
            'start_date' => sanitize($_GET['start_date'] ?? ''),
            'end_date' => sanitize($_GET['end_date'] ?? ''),
            'doctor_id' => (int) ($_GET['doctor_id'] ?? 0),
            'status' => sanitize($_GET['status'] ?? ''),
        ];
        $rows = [];
        $summary = [];

        if ($filters['start_date'] !== '' || $filters['end_date'] !== '') {
            if ($filters['start_date'] === '' || $filters['end_date'] === '' || $filters['start_date'] > $filters['end_date']) {
                flash('danger', 'Start date and end date are required, and start date must be before end date.');
            } else {
                $rows = (new AppointmentModel())->report($filters);
                foreach ($rows as $row) {
                    $summary[$row['status']] = ($summary[$row['status']] ?? 0) + 1;
                }

                if (($_GET['export'] ?? '') === 'csv') {
                    $this->exportCsv($rows);
                }
            }
        }

        view('reports/index', [
            'pageTitle' => 'Reports',
            'filters' => $filters,
            'rows' => $rows,
            'summary' => $summary,
            'doctors' => (new DoctorModel())->getAll(),
        ]);
    }

    private function exportCsv(array $rows): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="clinicdesk_appointments_report.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Patient Name', 'Doctor Name', 'Specialization', 'Date', 'Time', 'Status', 'Reason']);
        foreach ($rows as $row) {
            fputcsv($out, [
                $row['patient_name'],
                $row['doctor_name'],
                $row['specialization'],
                $row['appt_date'],
                $row['appt_time'],
                $row['status'],
                $row['reason'],
            ]);
        }
        fclose($out);
        exit;
    }
}
