<?php
declare(strict_types=1);

require 'vendor/autoload.php';
require_once __DIR__ . '/src/Utils/env.php';
require_once __DIR__ . '/src/Utils/hash.php';

use function App\Utils\load_env;

load_env();

$auth_service = new App\Services\AuthService();

if ($auth_service::isHandledRoute()) {
	$auth_service->handle();
}
