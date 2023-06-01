<?php
declare(strict_types=1);

namespace App\services;

use JetBrains\PhpStorm\NoReturn;

class LoginService extends Service {

	public function __construct() {
		parent::__construct(false);
	}

	#[NoReturn] protected function handleRequest(): void {
	}
}
