<?php

declare(strict_types=1);

class PrescriptionController
{
    public function index(): void
    {
        Auth::requireRole('patient');
        view('prescriptions/index', [
            'pageTitle' => 'My Prescriptions',
            'prescriptions' => (new PrescriptionModel())->getByPatient(Auth::id()),
        ]);
    }

    public function create(): void
    {
        Auth::requireRole('doctor');
        $appointment = $this->validDoctorAppointment((int) ($_GET['id'] ?? 0));
        view('prescriptions/create', [
            'pageTitle' => 'Add Prescription',
            'appointment' => $appointment,
        ]);
    }

    public function store(): void
    {
        Auth::requireRole('doctor');
        $appointmentId = (int) ($_POST['appointment_id'] ?? 0);
        $this->validatePost('page=prescriptions&action=create&id=' . $appointmentId);
        $appointment = $this->validDoctorAppointment($appointmentId);

        $diagnosis = sanitize($_POST['diagnosis'] ?? '');
        $medications = sanitize($_POST['medications'] ?? '');
        if ($diagnosis === '' || $medications === '') {
            flash('danger', 'Diagnosis and medications are required.');
            redirect('page=prescriptions&action=create&id=' . $appointmentId);
        }

        try {
            $filePath = uploadPdf('prescription_file', $appointmentId);
            (new PrescriptionModel())->create([
                'appointment_id' => $appointment['id'],
                'diagnosis' => $diagnosis,
                'medications' => $medications,
                'notes' => sanitize($_POST['notes'] ?? ''),
                'file_path' => $filePath,
            ]);
        } catch (RuntimeException $e) {
            flash('danger', $e->getMessage());
            redirect('page=prescriptions&action=create&id=' . $appointmentId);
        }

        flash('success', 'Prescription added.');
        redirect('page=appointments&action=detail&id=' . $appointmentId);
    }

    public function download(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');
        $appointmentId = (int) ($_GET['id'] ?? 0);
        $prescription = (new PrescriptionModel())->findWithAppointment($appointmentId);

        if (!$prescription || !$this->canDownload($prescription)) {
            view('errors/403', ['pageTitle' => 'Forbidden']);
            return;
        }

        if (empty($prescription['file_path'])) {
            flash('warning', 'No PDF file is attached to this prescription.');
            redirect('page=appointments&action=detail&id=' . $appointmentId);
        }

        $fullPath = realpath(UPLOAD_ROOT . '/' . $prescription['file_path']);
        $safeRoot = realpath(UPLOAD_ROOT . '/prescriptions');
        if (!$fullPath || !$safeRoot || !str_starts_with($fullPath, $safeRoot) || !is_file($fullPath)) {
            flash('danger', 'Prescription file is missing.');
            redirect('page=appointments&action=detail&id=' . $appointmentId);
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prescription.pdf"');
        header('Content-Length: ' . filesize($fullPath));
        readfile($fullPath);
        exit;
    }

    private function validDoctorAppointment(int $appointmentId): array
    {
        $appointment = (new AppointmentModel())->findById($appointmentId);
        if (!$appointment || (int) $appointment['doctor_user_id'] !== Auth::id() || $appointment['status'] !== 'completed') {
            view('errors/403', ['pageTitle' => 'Forbidden']);
            exit;
        }

        if ((new PrescriptionModel())->findByAppointmentId($appointmentId)) {
            flash('warning', 'This appointment already has a prescription.');
            redirect('page=appointments&action=detail&id=' . $appointmentId);
        }

        return $appointment;
    }

    private function canDownload(array $prescription): bool
    {
        if (Auth::role() === 'admin') {
            return true;
        }

        if (Auth::role() === 'patient') {
            return (int) $prescription['patient_id'] === Auth::id();
        }

        return (int) $prescription['doctor_user_id'] === Auth::id();
    }

    private function validatePost(string $back): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token. Please try again.');
            redirect($back);
        }
    }
}
