<?php
declare(strict_types=1);

namespace App\models;

class Account {
	public string $guid;
	public string $password;
	public string $salt;
}
