<?php

declare(strict_types=1);

final class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'role' => $user['role'],
        ];
    }

    public static function logout(): void
    {
        session_unset();
        session_destroy();
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): int
    {
        return (int) ($_SESSION['user']['id'] ?? 0);
    }

    public static function role(): string
    {
        return (string) ($_SESSION['user']['role'] ?? '');
    }

    public static function requireRole(string ...$roles): void
    {
        if (!self::check()) {
            redirect('page=auth&action=login');
        }

        if (!in_array(self::role(), $roles, true)) {
            redirect('page=error&action=403');
        }
    }
}
