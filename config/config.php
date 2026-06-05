<?php

declare(strict_types=1);

date_default_timezone_set('Asia/Hebron');

define('APP_NAME', 'ClinicDesk');

$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
define('BASE_URL', ($scriptDir === '' || $scriptDir === '.') ? '/' : $scriptDir . '/');

define('ITEMS_PER_PAGE', 10);
define('MAX_IMAGE_UPLOAD', 1024 * 1024);
define('MAX_PDF_UPLOAD', 3 * 1024 * 1024);
define('UPLOAD_ROOT', dirname(__DIR__) . '/public/uploads');

ini_set('display_errors', '0');
ini_set('log_errors', '1');
