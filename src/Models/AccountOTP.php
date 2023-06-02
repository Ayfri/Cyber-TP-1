<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;

class AccountOTP {
	public string $guid;
	public int $otp;
	public DateTime $validity;
}