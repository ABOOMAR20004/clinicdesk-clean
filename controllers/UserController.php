<?php

declare(strict_types=1);

class UserController
{
    public function index(): void
    {
        Auth::requireRole('admin');
        $page = currentPage();
        $role = sanitize($_GET['role'] ?? '');
        $search = sanitize($_GET['q'] ?? '');
        $model = new UserModel();

        view('users/list', [
            'pageTitle' => 'Users',
            'users' => $model->getAllPaginated($page, $role, $search),
            'paginator' => new Paginator($model->countAll($role, $search), ITEMS_PER_PAGE, $page),
            'role' => $role,
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        Auth::requireRole('admin');
        view('users/create', [
            'pageTitle' => 'Create User',
            'specializations' => (new SpecializationModel())->getAll(),
        ]);
    }

    public function store(): void
    {
        Auth::requireRole('admin');
        $this->validatePost('page=users&action=create');

        $role = sanitize($_POST['role'] ?? 'patient');
        if (!in_array($role, ['admin', 'doctor', 'patient'], true)) {
            flash('danger', 'Invalid role selected.');
            redirect('page=users&action=create');
        }

        $name = sanitize($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = (string) ($_POST['password'] ?? '');

        if ($name === '' || !$email || strlen($password) < 8) {
            flash('danger', 'Name, valid email, and password of at least 8 characters are required.');
            redirect('page=users&action=create');
        }

        try {
            $userId = (new UserModel())->create([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_BCRYPT),
                'role' => $role,
                'phone' => sanitize($_POST['phone'] ?? ''),
            ]);

            if ($role === 'doctor') {
                $days = array_values(array_intersect(dayOptions(), $_POST['available_days'] ?? []));
                (new DoctorModel())->create([
                    'user_id' => $userId,
                    'specialization_id' => (int) ($_POST['specialization_id'] ?? 0),
                    'bio' => sanitize($_POST['bio'] ?? ''),
                    'consultation_fee' => (float) ($_POST['consultation_fee'] ?? 0),
                    'available_days' => implode(',', $days ?: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu']),
                ]);
            }
        } catch (RuntimeException $e) {
            flash('danger', 'Could not create user. Check that the email is unique and doctor fields are valid.');
            redirect('page=users&action=create');
        }

        flash('success', 'User created successfully.');
        redirect('page=users');
    }

    public function edit(): void
    {
        Auth::requireRole('admin');
        $user = (new UserModel())->findById((int) ($_GET['id'] ?? 0));
        if (!$user) {
            view('errors/404', ['pageTitle' => 'Not Found']);
            return;
        }

        view('users/edit', ['pageTitle' => 'Edit User', 'user' => $user]);
    }

    public function update(): void
    {
        Auth::requireRole('admin');
        $id = (int) ($_POST['id'] ?? 0);
        $this->validatePost('page=users&action=edit&id=' . $id);

        $avatar = null;
        try {
            $avatar = uploadImage('avatar', 'avatars');
            (new UserModel())->update($id, [
                'name' => sanitize($_POST['name'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'avatar' => $avatar,
            ]);
        } catch (RuntimeException $e) {
            flash('danger', $e->getMessage());
            redirect('page=users&action=edit&id=' . $id);
        }

        if (!empty($_POST['new_password'])) {
            (new UserModel())->updatePassword($id, password_hash((string) $_POST['new_password'], PASSWORD_BCRYPT));
        }

        flash('success', 'User updated successfully.');
        redirect('page=users');
    }

    public function toggle(): void
    {
        Auth::requireRole('admin');
        $id = (int) ($_POST['id'] ?? 0);
        $this->validatePost('page=users');

        if ($id === Auth::id()) {
            flash('warning', 'You cannot deactivate your own account.');
            redirect('page=users');
        }

        (new UserModel())->toggleActive($id);
        flash('success', 'Account status updated.');
        redirect('page=users');
    }

    public function profile(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');
        view('users/profile', [
            'pageTitle' => 'My Profile',
            'user' => (new UserModel())->findById(Auth::id()),
        ]);
    }

    public function update_profile(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');
        $this->validatePost('page=users&action=profile');

        try {
            $avatar = uploadImage('avatar', 'avatars');
            (new UserModel())->update(Auth::id(), [
                'name' => sanitize($_POST['name'] ?? ''),
                'phone' => sanitize($_POST['phone'] ?? ''),
                'avatar' => $avatar,
            ]);
        } catch (RuntimeException $e) {
            flash('danger', $e->getMessage());
            redirect('page=users&action=profile');
        }

        $_SESSION['user']['name'] = sanitize($_POST['name'] ?? $_SESSION['user']['name']);
        flash('success', 'Profile updated.');
        redirect('page=users&action=profile');
    }

    private function validatePost(string $back): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token. Please try again.');
            redirect($back);
        }
    }
}
