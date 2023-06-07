<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\AccountAttemptRepository;
use App\Repositories\AccountRepository;
use App\Repositories\UserRepository;
use Exception;
use function App\Utils\is_hashed;
use function App\Utils\random_salt;
use function App\Utils\uuid;
use function session_destroy;

class AuthService extends Service {
	private AccountAttemptRepository $accountAttemptRepository;
	private AccountRepository $accountRepository;

	public function __construct() {
		parent::__construct(false);
		$this->accountRepository = new AccountRepository();
		$this->accountAttemptRepository = new AccountAttemptRepository();
		$this->userRepository = new UserRepository();
	}

	public static function onOTPValidationCallback(string $guid, string $type, bool $cancelled): void {
		if ($type === 'register') {
			$account_repository = new AccountRepository();
			$account = $account_repository->getTempAccountByGUID($guid);
			if ($account === null) {
				Service::sendError('Account not found.', 404);
			}

			if ($cancelled) {
				$user_repository = new UserRepository();
				$account_repository->deleteTempAccount($guid);
				$user_repository->deleteUser($guid);
				Service::sendSuccess(204);
			}

			$account_repository->transferTempAccountToAccount($guid);
			$account_repository->deleteTempAccount($guid);
			Service::sendSuccess(204);
		}
	}

	public static function routes(): array {
		return ['/login', '/register', '/logout'];
	}

	/**
	 * @throws Exception
	 */
	protected function handleRoutes(): never {
		$this->renderOnGet('/register');
		$this->renderOnGet('/login');

		if (static::onRoutePost('/register')) {
			$email = $this->getRequiredParam('email');
			$password = $this->getRequiredParam('password');
			$confirm_password = $this->getRequiredParam('confirm-password');

			if (!is_hashed($password) || !is_hashed($confirm_password)) {
				Service::sendError('Passwords must be hashed.');
			}

			if ($password !== $confirm_password) {
				Service::sendError('Passwords do not match.');
			}

			$current_user = $this->userRepository->getUserByEmail($email);
			if ($current_user !== null) {
				Service::sendError('User already exists.', 409);
			}

			$guid = uuid();
			$salt = random_salt();

			$hashed_password = hash('sha512', $password . $salt);

			$this->userRepository->createUser($guid, $email);
			$this->accountRepository->createTempAccount($guid, $hashed_password, $salt);

			OTPService::askForOTP($guid, 'register', 'App\Services\AuthService::onOTPValidationCallback');
		}

		if (static::onRoutePost('/login')) {
			$email = $this->getRequiredParam('email');
			$password = $this->getRequiredParam('password');

			if (!is_hashed($password)) {
				Service::sendError('Password must be hashed.');
			}

			$user = $this->userRepository->getUserByEmail($email);
			if ($user === null) {
				Service::sendError('User does not exist.', 404);
			}

			$attempts = $this->accountAttemptRepository->getAccountAttempts($user->guid, 15);
			if (count($attempts) >= 3) {
				Service::sendError('Too many login attempts, please wait.', 429);
			}

			$account = $this->accountRepository->getAccountByGUID($user->guid);
			if ($account === null) {
				Service::sendError('Account does not exist.', 404);
			}

			$hashed_password = hash('sha512', $password . $account->salt);
			if ($account->password !== $hashed_password) {
				$this->accountAttemptRepository->createAccountAttempt($account->guid);
				Service::sendError('Incorrect password.', 401);
			}

			$this->accountAttemptRepository->deleteAccountAttempts($account->guid);

			$_SESSION['user'] = $user;
			Service::sendSuccess();
		}

		if (static::onRouteGet('/logout')) {
			unset($_SESSION);
			session_destroy();
			Service::sendSuccess();
		}

		Service::sendError('Route not found.', 404);
	}
}
