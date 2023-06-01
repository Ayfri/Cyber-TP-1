<?php
declare(strict_types=1);

namespace App\models;

class User {
	public string $guid;
	public string $email;

	public function __construct(string $guid, string $email) {
		$this->guid = $guid;
		$this->email = $email;
	}
}
