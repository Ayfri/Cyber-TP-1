<?php
declare(strict_types=1);

namespace App\services;

use App\repositories\UserRepository;
use JetBrains\PhpStorm\NoReturn;

class AuthService extends Service {
	private UserRepository $userRepository;

	public function __construct() {
		parent::__construct();
		$this->userRepository = new UserRepository();
	}

	#[NoReturn]
	protected function handleRequest(): void {
		$email = $this->getRequiredParam('email');
		$password = $this->getRequiredParam('password');
		$confirm_password = $this->getRequiredParam('confirmPassword');

		if (!$this->isHashed($password) || !$this->isHashed($confirm_password)) {
			$this->sendError('Passwords must be hashed.');
		}

		if ($password !== $confirm_password) {
			$this->sendError('Passwords do not match.');
		}

		$this->userRepository->createUser($email, $password);
		$this->sendSuccess();
	}

	private function isHashed(string $password): bool {
		return preg_match('/^[0-9a-f]{128}$/', $password) === 1;
	}
}
