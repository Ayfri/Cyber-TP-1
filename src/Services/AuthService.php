<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\AccountRepository;
use App\Repositories\UserRepository;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use function App\Utils\random_salt;
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
		$this->renderOnGet('/login');

		if (static::onRoutePost('/register')) {
			$email = $this->getRequiredParam('email');
			$password = $this->getRequiredParam('password');
			$confirm_password = $this->getRequiredParam('confirm-password');

			if (!self::isHashed($password) || !self::isHashed($confirm_password)) {
				$this->sendError('Passwords must be hashed.');
			}

			if ($password !== $confirm_password) {
				$this->sendError('Passwords do not match.');
			}

			$current_user = $this->userRepository->getUserByEmail($email);
			if ($current_user !== null) {
				$this->sendError('User already exists.', 409);
			}

			$guid = uuid();
			$salt = random_salt();

			$hashed_password = hash('sha512', $password . $salt);

			$this->userRepository->createUser($guid, $email);
			$this->accountRepository->createAccount($guid, $hashed_password, $salt);
			$this->sendSuccess(201);
		}

		if (static::onRoutePost('/login')) {
			$email = $this->getRequiredParam('email');
			$password = $this->getRequiredParam('password');

			if (!self::isHashed($password)) {
				$this->sendError('Password must be hashed.');
			}

			$user = $this->userRepository->getUserByEmail($email);
			if ($user === null) {
				$this->sendError('User does not exist.', 404);
			}

			$account = $this->accountRepository->getAccountByGUID($user->guid);
			if ($account === null) {
				$this->sendError('Account does not exist.', 404);
			}

			$hashed_password = hash('sha512', $password . $account->salt);
			if ($account->password !== $hashed_password) {
				$this->sendError('Incorrect password.', 401);
			}

			$_SESSION['user'] = $user;
			$this->sendSuccess();
		}

		if (static::onRoutePost('/logout')) {
			unset($_SESSION['user']);
			$this->sendSuccess();
		}
	}

	private static function isHashed(string $password): bool {
		return preg_match('/^[0-9a-f]{128}$/', $password) === 1;
	}
}
