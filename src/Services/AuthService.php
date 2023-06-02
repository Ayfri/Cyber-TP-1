<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;
use JetBrains\PhpStorm\NoReturn;
use function preg_match;

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

		if (!self::isHashed($password) || !self::isHashed($confirm_password)) {
			$this->sendError('Passwords must be hashed.');
		}

		if ($password !== $confirm_password) {
			$this->sendError('Passwords do not match.');
		}

		$this->userRepository->createUser($email, $password);
		$this->sendSuccess();
	}

	private static function isHashed(string $password): bool {
		return preg_match('/^[0-9a-f]{128}$/', $password) === 1;
	}
}
