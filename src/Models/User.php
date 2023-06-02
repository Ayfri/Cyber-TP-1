<?php
declare(strict_types=1);

namespace App\Models;

class User {
	public string $email;
	public string $guid;

	public function __construct(string $guid, string $email) {
		$this->guid = $guid;
		$this->email = $email;
	}
}
