<?php

declare(strict_types=1);

class AuthController
{
    public function login(): void
    {
        if (Auth::check()) {
            redirect('page=dashboard');
        }

        view('auth/login', ['pageTitle' => 'Login']);
    }

    public function authenticate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid form token. Please try again.');
            redirect('page=auth&action=login');
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = (string) ($_POST['password'] ?? '');
        $user = (new UserModel())->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            flash('danger', 'Invalid credentials.');
            redirect('page=auth&action=login');
        }

        if ((int) $user['is_active'] !== 1) {
            flash('warning', 'Account suspended. Contact admin.');
            redirect('page=auth&action=login');
        }

        Auth::login($user);
        redirect('page=dashboard');
    }

    public function logout(): void
    {
        Auth::requireRole('admin', 'doctor', 'patient');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !CSRF::validateToken($_POST['csrf_token'] ?? '')) {
            flash('danger', 'Invalid logout request.');
            redirect('page=dashboard');
        }

        Auth::logout();
        session_start();
        flash('success', 'You have been logged out.');
        redirect('page=auth&action=login');
    }
}
