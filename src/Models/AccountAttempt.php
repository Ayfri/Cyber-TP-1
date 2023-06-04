<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;

class AccountAttempt {
	public string $guid;
	public DateTime $time;

	public function __construct(string $guid, string $time) {
		$this->guid = $guid;
		$this->time = date_create_from_format('Y-m-d H:i:s', $time);
	}
}
