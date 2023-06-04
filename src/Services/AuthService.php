<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\AccountAttemptRepository;
use App\Repositories\AccountRepository;
use App\Repositories\UserRepository;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use function App\Utils\is_hashed;
use function App\Utils\random_salt;
use function App\Utils\uuid;

class AuthService extends Service {
	private AccountAttemptRepository $accountAttemptRepository;
	private AccountRepository $accountRepository;
	private UserRepository $userRepository;

	public function __construct() {
		parent::__construct(false);
		$this->accountRepository = new AccountRepository();
		$this->accountAttemptRepository = new AccountAttemptRepository();
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

			if (!is_hashed($password) || !is_hashed($confirm_password)) {
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
			$this->accountRepository->createTempAccount($guid, $hashed_password, $salt);
			$this->redirect('/verify-otp');
		}

		if (static::onRoutePost('/login')) {
			$email = $this->getRequiredParam('email');
			$password = $this->getRequiredParam('password');

			if (!is_hashed($password)) {
				$this->sendError('Password must be hashed.');
			}

			$user = $this->userRepository->getUserByEmail($email);
			if ($user === null) {
				$this->sendError('User does not exist.', 404);
			}

			$attempts = $this->accountAttemptRepository->getAccountAttempts($user->guid, 15);
			if (count($attempts) >= 3) {
				$this->sendError('Too many login attempts, please wait.', 429);
			}

			$account = $this->accountRepository->getAccountByGUID($user->guid);
			if ($account === null) {
				$this->sendError('Account does not exist.', 404);
			}

			$hashed_password = hash('sha512', $password . $account->salt);
			if ($account->password !== $hashed_password) {
				$this->accountAttemptRepository->createAccountAttempt($account->guid);
				$this->sendError('Incorrect password.', 401);
			}

			$this->accountAttemptRepository->deleteAccountAttempts($account->guid);

			$_SESSION['user'] = $user;
			$this->sendSuccess();
		}

		if (static::onRoutePost('/logout')) {
			unset($_SESSION['user']);
			$this->sendSuccess();
		}
	}
}
