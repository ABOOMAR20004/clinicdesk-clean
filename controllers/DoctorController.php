<?php

declare(strict_types=1);

class DoctorController
{
    public function index(): void
    {
        Auth::requireRole('admin', 'doctor');

        if (Auth::role() === 'doctor') {
            redirect('page=doctors&action=edit');
        }

        $page = currentPage();
        $model = new DoctorModel();
        view('doctors/list', [
            'pageTitle' => 'Doctors',
            'doctors' => $model->getAllPaginated($page),
            'paginator' => new Paginator($model->countAll(), ITEMS_PER_PAGE, $page),
        ]);
    }

    public function create(): void
    {
        Auth::requireRole('admin');
        redirect('page=users&action=create');
    }

    public function edit(): void
    {
        Auth::requireRole('admin', 'doctor');
        $model = new DoctorModel();
        $doctor = Auth::role() === 'admin'
            ? $model->findById((int) ($_GET['id'] ?? 0))
            : $model->findByUserId(Auth::id());

        if (!$doctor) {
            view('errors/404', ['pageTitle' => 'Not Found']);
            return;
        }

        view('doctors/edit', [
            'pageTitle' => 'Edit Doctor',
            'doctor' => $doctor,
            'specializations' => (new SpecializationModel())->getAll(),
        ]);
    }

    public function update(): void
    {
        Auth::requireRole('admin', 'doctor');
        $this->validatePost('page=doctors');

        $doctorModel = new DoctorModel();
        $doctor = Auth::role() === 'admin'
            ? $doctorModel->findById((int) ($_POST['id'] ?? 0))
            : $doctorModel->findByUserId(Auth::id());

        if (!$doctor) {
            view('errors/404', ['pageTitle' => 'Not Found']);
            return;
        }

        $days = array_values(array_intersect(dayOptions(), $_POST['available_days'] ?? []));
        try {
            $photo = uploadImage('doctor_photo', 'doctor_photos');
            $doctorModel->update((int) $doctor['id'], [
                'specialization_id' => (int) ($_POST['specialization_id'] ?? $doctor['specialization_id']),
                'bio' => sanitize($_POST['bio'] ?? ''),
                'consultation_fee' => (float) ($_POST['consultation_fee'] ?? 0),
                'available_days' => implode(',', $days ?: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu']),
            ]);

            if ($photo) {
                (new UserModel())->updateAvatar((int) $doctor['user_id'], $photo);
            }
        } catch (RuntimeException $e) {
            flash('danger', $e->getMessage());
            redirect('page=doctors&action=edit&id=' . (int) $doctor['id']);
        }

        flash('success', 'Doctor profile updated.');
        redirect(Auth::role() === 'admin' ? 'page=doctors' : 'page=dashboard');
    }

    private function validatePost(string $back): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token. Please try again.');
            redirect($back);
        }
    }
}
