<?php
declare(strict_types=1);

namespace App\Utils;

function log_message(...$args): void {
	$stdout = fopen('php://stdout', 'wb');
	fwrite($stdout, implode(' ', $args) . "\n");
}

function log_error(...$args): void {
	$stderr = fopen('php://stderr', 'wb');
	fwrite($stderr, implode(' ', $args) . "\n");
}
