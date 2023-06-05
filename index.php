<?php
declare(strict_types=1);

require 'vendor/autoload.php';
require_once __DIR__ . '/src/Utils/env.php';
require_once __DIR__ . '/src/Utils/hash.php';
require_once __DIR__ . '/src/Utils/logs.php';

use App\Services\AuthService;
use function App\Utils\load_env;

load_env();

session_start();

global $otp_service;
$otp_service = new App\Services\OTPService();
if ($otp_service::isHandledRoute()) {
	$otp_service->handle();
}

$auth_service = new App\Services\AuthService();
AuthService::setOtpService($otp_service);
if ($auth_service::isHandledRoute()) {
	$auth_service->handle();
}

$content_service = new App\Services\ContentService();
if ($content_service::isHandledRoute()) {
	$content_service->handle();
}


$manage_account_service = new App\Services\ManageAccountService();
if ($manage_account_service::isHandledRoute()) {
	$manage_account_service->handle();
}
