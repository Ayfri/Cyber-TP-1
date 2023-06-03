<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\AccountRepository;
use JetBrains\PhpStorm\NoReturn;
use function App\Utils\is_hashed;

class ManageAccountService extends Service {

	private AccountRepository $accountRepository;

	public function __construct() {
		parent::__construct();
		$this->accountRepository = new AccountRepository();
	}

	public static function routes(): array {
		return ['/change-password', '/delete-account'];
	}

	#[NoReturn]
	protected function handleRoutes(): void {
		$this->renderOnGet('/change-password');
		$this->renderOnGet('/delete-account');

		if (static::onRoutePost('/change-password')) {
			$current_password = $this->getRequiredParam('current-password');
			$new_password = $this->getRequiredParam('new-password');
			$confirm_password = $this->getRequiredParam('confirm-password');

			if (!is_hashed($current_password) || !is_hashed($new_password) || !is_hashed($confirm_password)) {
				$this->sendError('Passwords must be hashed.');
			}

			if ($new_password !== $confirm_password) {
				$this->sendError('Passwords do not match.');
			}

			/** @var User $user */
			$user = $_SESSION['user'];
			$account = $this->accountRepository->getAccountByGUID($user->guid);
			if ($account === null) {
				$this->sendError('Account not found.', 404);
			}

			$hashed_password = hash('sha512', $current_password . $account->salt);
			if ($hashed_password !== $account->password) {
				$this->sendError('Incorrect password.', 401);
			}

			$hashed_new_password = hash('sha512', $new_password . $account->salt);
			$this->accountRepository->updatePassword($account->guid, $hashed_new_password);
			$this->redirect('/');
		}
	}
}
