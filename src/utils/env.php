<?php
declare(strict_types=1);

namespace App\utils;

use Dotenv\Dotenv;

function load_env(): void {
	$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
	$dotenv->load();
}

function env(string $key, string $default = ''): string {
	$value = getenv($key);
	if ($value === false) {
		return $default;
	}

	return $value;
}
