<?php

declare(strict_types=1);

class AppointmentController
{
    public function index(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');

        $page = currentPage();
        $filters = [
            'status' => sanitize($_GET['status'] ?? ''),
            'start_date' => sanitize($_GET['start_date'] ?? ''),
            'end_date' => sanitize($_GET['end_date'] ?? ''),
            'doctor_id' => (int) ($_GET['doctor_id'] ?? 0),
            'patient_search' => sanitize($_GET['patient_search'] ?? ''),
        ];

        $model = new AppointmentModel();
        $role = Auth::role();
        $today = [];
        $scopeId = 0;

        if ($role === 'admin') {
            $appointments = $model->getAll($page, $filters);
            $scope = 'admin';
        } elseif ($role === 'doctor') {
            $doctor = $this->currentDoctor();
            $scopeId = (int) $doctor['id'];
            $appointments = $model->getByDoctor($scopeId, $page, $filters);
            $today = $model->getTodayByDoctor($scopeId);
            $scope = 'doctor';
        } else {
            $scopeId = Auth::id();
            $appointments = $model->getByPatient($scopeId, $page, $filters);
            $scope = 'patient';
        }

        view('appointments/list', [
            'pageTitle' => 'Appointments',
            'appointments' => $appointments,
            'today' => $today,
            'role' => $role,
            'filters' => $filters,
            'doctors' => (new DoctorModel())->getAll(),
            'paginator' => new Paginator($model->countFiltered($scope, $scopeId, $filters), ITEMS_PER_PAGE, $page),
        ]);
    }

    public function book(): void
    {
        Auth::requireRole('patient');
        view('appointments/book', [
            'pageTitle' => 'Book Appointment',
            'doctors' => (new DoctorModel())->getAll(),
            'selectedDoctor' => (int) ($_GET['doctor_id'] ?? 0),
        ]);
    }

    public function edit(): void
    {
        Auth::requireRole('admin', 'doctor');
        redirect('page=appointments');
    }

    public function store(): void
    {
        Auth::requireRole('patient');
        $this->validatePost('page=appointments&action=book');

        $doctorId = (int) ($_POST['doctor_id'] ?? 0);
        $date = sanitize($_POST['appt_date'] ?? '');
        $time = sanitize($_POST['appt_time'] ?? '');
        $reason = sanitize($_POST['reason'] ?? '');
        $doctorModel = new DoctorModel();
        $doctor = $doctorModel->findById($doctorId);

        if (!$doctor || $date === '' || $time === '' || !in_array($time, timeSlots(), true)) {
            flash('danger', 'Please choose a valid doctor, date, and time slot.');
            redirect('page=appointments&action=book');
        }

        $timestamp = strtotime($date);
        if ($timestamp === false || $date < date('Y-m-d')) {
            flash('danger', 'Appointment date must not be in the past.');
            redirect('page=appointments&action=book&doctor_id=' . $doctorId);
        }

        $day = date('D', $timestamp);
        if (!in_array($day, $doctorModel->getAvailableDays($doctorId), true)) {
            flash('warning', 'The selected doctor is not available on that day.');
            redirect('page=appointments&action=book&doctor_id=' . $doctorId);
        }

        $model = new AppointmentModel();
        if ($model->hasConflict($doctorId, $date, $time)) {
            flash('warning', 'This slot is already booked, please choose another time.');
            redirect('page=appointments&action=book&doctor_id=' . $doctorId);
        }

        if (!$model->book([
            'patient_id' => Auth::id(),
            'doctor_id' => $doctorId,
            'appt_date' => $date,
            'appt_time' => $time,
            'reason' => $reason,
        ])) {
            flash('danger', 'Could not book appointment. Please choose another slot.');
            redirect('page=appointments&action=book&doctor_id=' . $doctorId);
        }

        flash('success', 'Appointment booked successfully.');
        redirect('page=appointments');
    }

    public function detail(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');
        $appointment = (new AppointmentModel())->findById((int) ($_GET['id'] ?? 0));

        if (!$appointment || !$this->canAccess($appointment)) {
            view('errors/403', ['pageTitle' => 'Forbidden']);
            return;
        }

        view('appointments/detail', [
            'pageTitle' => 'Appointment Detail',
            'appointment' => $appointment,
            'prescription' => (new PrescriptionModel())->findByAppointmentId((int) $appointment['id']),
        ]);
    }

    public function status(): void
    {
        Auth::requireRole('admin', 'doctor');
        $id = (int) ($_POST['id'] ?? 0);
        $this->validatePost('page=appointments&action=detail&id=' . $id);

        $model = new AppointmentModel();
        $appointment = $model->findById($id);
        if (!$appointment || !$this->canAccess($appointment)) {
            view('errors/403', ['pageTitle' => 'Forbidden']);
            return;
        }

        $newStatus = sanitize($_POST['status'] ?? '');
        if (!in_array($newStatus, statusOptions(), true)) {
            flash('danger', 'Invalid appointment status.');
            redirect('page=appointments&action=detail&id=' . $id);
        }

        if (Auth::role() === 'doctor' && !$this->doctorTransitionAllowed($appointment['status'], $newStatus)) {
            flash('warning', 'This status transition is not allowed.');
            redirect('page=appointments&action=detail&id=' . $id);
        }

        $model->updateStatus($id, $newStatus, sanitize($_POST['doctor_notes'] ?? ''));
        flash('success', 'Appointment status updated.');
        redirect('page=appointments&action=detail&id=' . $id);
    }

    public function cancel(): void
    {
        Auth::requireRole('patient');
        $id = (int) ($_POST['id'] ?? 0);
        $this->validatePost('page=appointments');

        $model = new AppointmentModel();
        $appointment = $model->findById($id);
        if (!$appointment || (int) $appointment['patient_id'] !== Auth::id()) {
            view('errors/403', ['pageTitle' => 'Forbidden']);
            return;
        }

        if ($appointment['status'] !== 'pending') {
            flash('warning', 'Only pending appointments can be cancelled by patients.');
            redirect('page=appointments');
        }

        $model->updateStatus($id, 'cancelled');
        flash('success', 'Appointment cancelled.');
        redirect('page=appointments');
    }

    private function currentDoctor(): array
    {
        $doctor = (new DoctorModel())->findByUserId(Auth::id());
        if (!$doctor) {
            flash('warning', 'Doctor profile is missing.');
            redirect('page=dashboard');
        }

        return $doctor;
    }

    private function canAccess(array $appointment): bool
    {
        if (Auth::role() === 'admin') {
            return true;
        }

        if (Auth::role() === 'patient') {
            return (int) $appointment['patient_id'] === Auth::id();
        }

        return (int) $appointment['doctor_user_id'] === Auth::id();
    }

    private function doctorTransitionAllowed(string $old, string $new): bool
    {
        return in_array($old . ':' . $new, [
            'pending:confirmed',
            'pending:cancelled',
            'confirmed:completed',
            'confirmed:cancelled',
            $old . ':' . $old,
        ], true);
    }

    private function validatePost(string $back): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token. Please try again.');
            redirect($back);
        }
    }
}
