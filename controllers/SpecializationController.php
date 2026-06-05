<?php

declare(strict_types=1);

class SpecializationController
{
    public function index(): void
    {
        Auth::requireRole('admin');
        view('specializations/index', [
            'pageTitle' => 'Specializations',
            'specializations' => (new SpecializationModel())->getAll(),
        ]);
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        $this->validatePost('page=specializations');

        $name = sanitize($_POST['name'] ?? '');
        if ($name === '') {
            flash('danger', 'Specialization name is required.');
            redirect('page=specializations');
        }

        try {
            (new SpecializationModel())->create($name);
            flash('success', 'Specialization added.');
        } catch (RuntimeException $e) {
            flash('danger', 'Could not add specialization. It may already exist.');
        }

        redirect('page=specializations');
    }

    public function delete(): void
    {
        Auth::requireRole('admin');
        $this->validatePost('page=specializations');

        $id = (int) ($_POST['id'] ?? 0);
        $model = new SpecializationModel();
        if (!$model->isSafeToDelete($id)) {
            flash('warning', 'This specialization is assigned to doctors and cannot be deleted.');
            redirect('page=specializations');
        }

        $model->delete($id);
        flash('success', 'Specialization deleted.');
        redirect('page=specializations');
    }

    private function validatePost(string $back): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token. Please try again.');
            redirect($back);
        }
    }
}
