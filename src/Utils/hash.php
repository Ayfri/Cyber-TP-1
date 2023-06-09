<?php
declare(strict_types=1);

namespace App\Utils;

use Exception;
use function preg_match;

/**
 * @throws Exception
 */
function uuid(): string {
	return sprintf(
		'%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
		// 32 bits for "time_low"
		random_int(0, 0xffff),
		random_int(0, 0xffff),

		// 16 bits for "time_mid"
		random_int(0, 0xffff),

		// 16 bits for "time_hi_and_version",
		// four most significant bits holds version number 4
		random_int(0, 0x0fff) | 0x4000,

		// 16 bits, 8 bits for "clk_seq_hi_res",
		// 8 bits for "clk_seq_low",
		// two most significant bits holds zero and one for variant DCE1.1
		random_int(0, 0x3fff) | 0x8000,

		// 48 bits for "node"
		random_int(0, 0xffff),
		random_int(0, 0xffff),
		random_int(0, 0xffff),
	);
}

/**
 * @throws Exception
 */
function random_salt(): string {
	return bin2hex(random_bytes(64));
}

function is_hashed(string $password): bool {
	return preg_match('/^[0-9a-f]{128}$/', $password) === 1;
}
