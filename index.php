<?php
declare(strict_types=1);

require 'vendor/autoload.php';
require_once __DIR__ . '/src/Utils/env.php';
require_once __DIR__ . '/src/Utils/hash.php';
require_once __DIR__ . '/src/Utils/logs.php';

use App\Services\AuthService;
use App\Services\ContentService;
use App\Services\ManageAccountService;
use App\Services\OTPService;
use function App\Utils\load_env;

load_env();
date_default_timezone_set('Europe/Paris');

session_start();

$auth_service = new AuthService();
if (AuthService::isHandledRoute()) {
	$auth_service->handle();
}

$content_service = new ContentService();
if (ContentService::isHandledRoute()) {
	$content_service->handle();
}

$manage_account_service = new ManageAccountService();
if (ManageAccountService::isHandledRoute()) {
	$manage_account_service->handle();
}

$otp_service = new OTPService();
if (OTPService::isHandledRoute()) {
	$otp_service->handle();
}

require_once __DIR__ . '/src/views/404.php';
