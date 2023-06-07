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
if ($auth_service::isHandledRoute()) {
	$auth_service->handle();
}

$content_service = new ContentService();
if ($content_service::isHandledRoute()) {
	$content_service->handle();
}

$manage_account_service = new ManageAccountService();
if ($manage_account_service::isHandledRoute()) {
	$manage_account_service->handle();
}

$otp_service = new OTPService();
if ($otp_service::isHandledRoute()) {
	$otp_service->handle();
}
