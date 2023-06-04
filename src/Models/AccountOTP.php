<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;

class AccountOTP {
	public string $guid;
	public int $otp;
	public DateTime $validity;

	public function __construct(string $guid, int $otp, string $validity) {
		$this->guid = $guid;
		$this->otp = $otp;
		$this->validity = date_create_from_format('Y-m-d H:i:s', $validity);
	}

	public function isValid(): bool {
		return $this->validity > new DateTime();
	}
}
