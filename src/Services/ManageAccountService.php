<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\AccountRepository;
use App\Repositories\UserRepository;
use Exception;
use function App\Utils\is_hashed;
use function session_destroy;

class ManageAccountService extends Service {
	private AccountRepository $accountRepository;

	public function __construct() {
		parent::__construct();
		$this->accountRepository = new AccountRepository();
		$this->userRepository = new UserRepository();
	}

	public static function onOTPValidationCallback(string $guid, string $type, bool $cancelled): void {
		if ($type === 'change-password') {
			$account_repository = new AccountRepository();
			$account = $account_repository->getTempAccountByGUID($guid);
			if ($account === null) {
				Service::sendError('Account not found.', 404);
			}

			if ($cancelled) {
				$account_repository->deleteTempAccount($guid);
				Service::sendSuccess(204);
			}

			$account_repository->deleteAccount($guid);
			$account_repository->transferTempAccountToAccount($guid);
			$account_repository->deleteTempAccount($guid);
			Service::sendSuccess(204);
		}

		if ($type === 'delete-account') {
			if ($cancelled) {
				Service::redirect('/');
			}

			$user_repository = new UserRepository();
			$user_repository->deleteUser($guid);
			session_destroy();
			Service::sendSuccess(204);
		}
	}

	/**
	 * @throws Exception
	 */
	protected function handleRoutes(): never {
		$this->renderOnGet('/change-password');
		$this->renderOnGet('/delete-account');

		if (Service::onRoutePost('/change-password')) {
			$current_password = $this->getRequiredParam('current-password');
			$new_password = $this->getRequiredParam('new-password');
			$confirm_password = $this->getRequiredParam('confirm-password');

			if (!is_hashed($current_password) || !is_hashed($new_password) || !is_hashed($confirm_password)) {
				Service::sendError('Passwords must be hashed.');
			}

			if ($new_password !== $confirm_password) {
				Service::sendError('Passwords do not match.');
			}

			if ($current_password === $new_password) {
				Service::sendError('New password cannot be the same as the current password.');
			}

			/** @var User $user */
			$user = $_SESSION['user'];
			$account = $this->accountRepository->getAccountByGUID($user->guid);
			if ($account === null) {
				Service::sendError('Account not found.', 404);
			}

			$hashed_password = hash('sha512', $current_password . $account->salt);
			if ($hashed_password !== $account->password) {
				Service::sendError('Incorrect password.', 401);
			}

			$hashed_new_password = hash('sha512', $new_password . $account->salt);

			$this->accountRepository->deleteTempAccount($account->guid);
			$this->accountRepository->transferAccountToTemp($account->guid);
			$this->accountRepository->updateTempPassword($account->guid, $hashed_new_password);

			OTPService::askForOTP(
				$account->guid,
				'change-password',
				'App\Services\ManageAccountService::onOTPValidationCallback',
			);
		}

		if (Service::onRoutePost('/delete-account')) {
			$current_password = $this->getRequiredParam('password');
			if (!is_hashed($current_password)) {
				Service::sendError('Passwords must be hashed.');
			}

			/** @var User $user */
			$user = $_SESSION['user'];
			$account = $this->accountRepository->getAccountByGUID($user->guid);
			if ($account === null) {
				Service::sendError('Account not found.', 404);
			}

			$hashed_password = hash('sha512', $current_password . $account->salt);
			if ($hashed_password !== $account->password) {
				Service::sendError('Incorrect password.', 401);
			}

			OTPService::askForOTP(
				$account->guid,
				'delete-account',
				'App\Services\ManageAccountService::onOTPValidationCallback',
			);
		}

		Service::sendError('Not found.', 404);
	}

	public static function routes(): array {
		return ['/change-password', '/delete-account'];
	}
}
