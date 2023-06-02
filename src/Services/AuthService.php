<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\UserRepository;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use function App\Utils\uuid;
use function preg_match;

class AuthService extends Service {
	private AccountRepository $accountRepository;
	private UserRepository $userRepository;

	public function __construct() {
		parent::__construct(false);
		$this->accountRepository = new AccountRepository();
		$this->userRepository = new UserRepository();
	}

	public static function routes(): array {
		return ['/login', '/register', '/logout'];
	}

	/**
	 * @throws Exception
	 */
	#[NoReturn]
	protected function handleRoutes(): void {
		$this->renderOnGet('/register');
		if ($this->onRoutePost('register')) {
			$email = $this->getRequiredParam('email');
			$password = $this->getRequiredParam('password');
			$confirm_password = $this->getRequiredParam('confirmPassword');
			$salt = $this->getRequiredParam('salt');

			if (!self::isHashed($password) || !self::isHashed($confirm_password)) {
				$this->sendError('Passwords must be hashed.');
			}

			if ($password !== $confirm_password) {
				$this->sendError('Passwords do not match.');
			}

			$guid = uuid();

			$this->userRepository->createUser($guid, $email);
			$this->accountRepository->createAccount($guid, $password, $salt);
			$this->sendSuccess();
		}
	}

	private static function isHashed(string $password): bool {
		return preg_match('/^[0-9a-f]{128}$/', $password) === 1;
	}
}
