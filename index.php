<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/helpers.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/CSRF.php';
require_once __DIR__ . '/core/Paginator.php';

require_once __DIR__ . '/models/BaseModel.php';
foreach (glob(__DIR__ . '/models/*.php') as $modelFile) {
    if (basename($modelFile) !== 'BaseModel.php') {
        require_once $modelFile;
    }
}

foreach (glob(__DIR__ . '/controllers/*.php') as $controllerFile) {
    require_once $controllerFile;
}

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$publicAuthActions = ['login', 'authenticate'];

if (!Auth::check() && !($page === 'auth' && in_array($action, $publicAuthActions, true))) {
    redirect('page=auth&action=login');
}

$routes = [
    'auth' => AuthController::class,
    'dashboard' => DashboardController::class,
    'users' => UserController::class,
    'doctors' => DoctorController::class,
    'specializations' => SpecializationController::class,
    'appointments' => AppointmentController::class,
    'prescriptions' => PrescriptionController::class,
    'reports' => ReportController::class,
    'error' => ErrorController::class,
];

if (!isset($routes[$page])) {
    view('errors/404', ['pageTitle' => 'Not Found']);
    exit;
}

$controller = new $routes[$page]();
$method = $action;

if (!is_callable([$controller, $method])) {
    view('errors/404', ['pageTitle' => 'Not Found']);
    exit;
}

$controller->$method();
