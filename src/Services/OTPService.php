<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\AccountOTPRepository;
use App\Repositories\AccountRepository;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class OTPService extends Service {
	private AccountOTPRepository $accountOTPRepository;
	private AccountRepository $accountRepository;

	public function __construct() {
		parent::__construct();
		$this->accountOTPRepository = new AccountOTPRepository();
		$this->accountRepository = new AccountRepository();
	}

	public static function routes(): array {
		return ['/otp', '/get-otp'];
	}

	/**
	 * @throws Exception
	 */
	#[NoReturn]
	protected function handleRoutes(): void {
		/** @var User $user */
		$user = $_SESSION['user'];
		$this->renderOnGet('/otp', 'otp', ['otp' => $this->getOTPForUser($user)]);

		if (static::onRouteGet('/get-otp')) {
			$otp = $this->getOTPForUser($user);
			$this->sendResponse($otp);
		}
	}

	/**
	 * @throws Exception
	 */
	public function getOTPForUser(User $user): string {
		$account = $this->accountRepository->getAccountByGUID($user->guid);
		if ($account === null) {
			$this->sendError('Account not found.', 404);
		}

		$otp = $this->accountOTPRepository->getOTPByGUID($account->guid);
		if ($otp === null) {
			return $this->generateOTPForUser($account->guid);
		}

		if ($otp->isValid()) {
			return (string)$otp->otp;
		}

		$this->accountOTPRepository->deleteOTP($account->guid);
		return $this->generateOTPForUser($account->guid);
	}

	/**
	 * @throws Exception
	 */
	private function generateOTPForUser(string $guid): string {
		$random_otp = static::generateOTP();
		$next_minute = date_create_immutable()->modify('+1 minute');
		$this->accountOTPRepository->createOTP($guid, $random_otp, $next_minute->format('Y-m-d H:i:s'));
		return (string)$random_otp;
	}

	/**
	 * @throws Exception
	 */
	private static function generateOTP(): int {
		return random_int(100000, 999999);
	}
}
