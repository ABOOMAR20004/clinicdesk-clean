<?php

declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $query = ''): string
{
    return BASE_URL . 'index.php' . ($query !== '' ? '?' . ltrim($query, '?') : '');
}

function asset(string $path): string
{
    return BASE_URL . 'public/' . ltrim($path, '/');
}

function upload_url(?string $path): string
{
    if (!$path) {
        return asset('assets/adminlte/dist/img/avatar.png');
    }

    return asset('uploads/' . ltrim($path, '/'));
}

function redirect(string $target = ''): void
{
    if (str_starts_with($target, 'http')) {
        header('Location: ' . $target);
    } else {
        header('Location: ' . url($target));
    }
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function sanitize(?string $value): string
{
    return trim(strip_tags((string) $value));
}

function formatDate(?string $date): string
{
    return $date ? date('M d, Y', strtotime($date)) : '';
}

function formatTime(?string $time): string
{
    return $time ? date('H:i', strtotime($time)) : '';
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(CSRF::generateToken()) . '">';
}

function selected(mixed $current, mixed $expected): string
{
    return (string) $current === (string) $expected ? 'selected' : '';
}

function checked(bool $condition): string
{
    return $condition ? 'checked' : '';
}

function statusOptions(): array
{
    return ['pending', 'confirmed', 'completed', 'cancelled'];
}

function statusBadge(string $status): string
{
    return match ($status) {
        'confirmed' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
        default => 'warning',
    };
}

function roleBadge(string $role): string
{
    return match ($role) {
        'admin' => 'danger',
        'doctor' => 'primary',
        default => 'success',
    };
}

function dayOptions(): array
{
    return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
}

function timeSlots(): array
{
    $slots = [];
    $start = strtotime('09:00');
    $end = strtotime('16:00');

    for ($time = $start; $time <= $end; $time += 30 * 60) {
        $slots[] = date('H:i', $time);
    }

    return $slots;
}

function currentPage(): int
{
    return max(1, (int) ($_GET['p'] ?? 1));
}

function activeMenu(string $page, ?string $action = null): string
{
    $currentPage = $_GET['page'] ?? 'dashboard';
    $currentAction = $_GET['action'] ?? 'index';

    if ($currentPage !== $page) {
        return '';
    }

    if ($action !== null && $currentAction !== $action) {
        return '';
    }

    return 'active';
}

function view(string $path, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require dirname(__DIR__) . '/views/' . $path . '.php';
}

function uploadImage(string $field, string $folder, int $maxBytes = MAX_IMAGE_UPLOAD): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }

    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Image upload failed.');
    }

    if ($file['size'] > $maxBytes) {
        throw new RuntimeException('Image size must be 1 MB or less.');
    }

    $info = getimagesize($file['tmp_name']);
    if ($info === false || !in_array($info['mime'], ['image/jpeg', 'image/png'], true)) {
        throw new RuntimeException('Only valid JPEG and PNG images are allowed.');
    }

    $extension = $info['mime'] === 'image/png' ? 'png' : 'jpg';
    $safeName = bin2hex(random_bytes(16)) . '.' . $extension;
    $targetDir = UPLOAD_ROOT . '/' . trim($folder, '/');
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $target = $targetDir . '/' . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Could not save uploaded image.');
    }

    return trim($folder, '/') . '/' . $safeName;
}

function uploadPdf(string $field, int $appointmentId, int $maxBytes = MAX_PDF_UPLOAD): ?string
{
    if (empty($_FILES[$field]['name'])) {
        return null;
    }

    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('PDF upload failed.');
    }

    if ($file['size'] > $maxBytes) {
        throw new RuntimeException('Prescription PDF must be 3 MB or less.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if ($finfo->file($file['tmp_name']) !== 'application/pdf') {
        throw new RuntimeException('Only valid PDF files are allowed.');
    }

    $targetDir = UPLOAD_ROOT . '/prescriptions';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $safeName = 'prescription_' . $appointmentId . '_' . time() . '.pdf';
    $target = $targetDir . '/' . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Could not save prescription PDF.');
    }

    return 'prescriptions/' . $safeName;
}
