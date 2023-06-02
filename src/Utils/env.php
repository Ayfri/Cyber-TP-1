<?php
declare(strict_types=1);

namespace App\Utils;

use Dotenv\Dotenv;

function load_env(): void {
	$dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
	$variables = $dotenv->load();
	$_ENV = [...$_ENV, ...$variables];
}

function env(string $key, string $default = ''): string {
	$value = $_ENV["CYBER_$key"];
	if ($value === false) {
		return $default;
	}

	return $value;
}
