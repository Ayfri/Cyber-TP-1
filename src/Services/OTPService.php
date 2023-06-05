<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Repositories\AccountOTPRepository;
use App\Repositories\AccountRepository;
use Exception;
use JetBrains\PhpStorm\NoReturn;

class OTPService extends Service {
	private static array $otpCache = [];
	private AccountOTPRepository $accountOTPRepository;
	private AccountRepository $accountRepository;

	public function __construct() {
		parent::__construct(false);
		$this->accountOTPRepository = new AccountOTPRepository();
		$this->accountRepository = new AccountRepository();
	}

	public static function routes(): array {
		return ['/otp', '/get-otp', '/otp-verify'];
	}

	/**
	 * @throws Exception
	 */
	#[NoReturn] public function askForOTP(string $guid, string $auth, callable $on_valid): void {
		static::addOTPToCache($guid, $auth, $on_valid);
		$_SESSION['guid'] = $guid;
		$this->redirect('/otp-verify');
	}

	private static function addOTPToCache(string $guid, string $auth, callable $on_valid): void {
		static::$otpCache[$guid] = compact('auth', 'on_valid');
	}

	/**
	 * @throws Exception
	 */
	#[NoReturn]
	protected function handleRoutes(): void {
		/** @var User $user */
		$user_guid = $_SESSION['user']->guid ?? $_SESSION['guid'];
		$is_temporary = !isset($_SESSION['user']);

		if (static::onRouteGet('/otp')) {
			$otp = $this->getOTPForUser($user_guid, $is_temporary);
			$this->render('otp', compact('otp'));
		}

		$this->renderOnGet('/otp-verify', 'otp-verify');

		if (static::onRouteGet('/get-otp')) {
			$otp = $this->getOTPForUser($user_guid);
			$this->sendResponse($otp);
		}

		if (static::onRoutePost('/otp-verify')) {
			$otp = $this->getRequiredParam('otp');
			$guid = $_SESSION['guid'];

			if (!static::hasOTPInCache($guid)) {
				$this->sendError('OTP not found.', 404);
			}

			if (!static::isValidOTPInCache($guid, $otp)) {
				$this->sendError('OTP does not match.', 403);
			}

			static::callOnValidOTPInCache($guid);
			static::removeOTPFromCache($guid);
			$this->accountOTPRepository->deleteOTP($guid);
			$this->sendSuccess();
		}
	}

	/**
	 * @throws Exception
	 */
	public function getOTPForUser(string $guid, bool $temp = false): string {
		$account = $temp ? $this->accountRepository->getTempAccountByGUID($guid) : $this->accountRepository->getAccountByGUID($guid);
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

	private static function hasOTPInCache(string $guid): bool {
		return isset(static::$otpCache[$guid]);
	}

	private static function isValidOTPInCache(string $guid, string $auth): bool {
		return static::hasOTPInCache($guid) && static::$otpCache[$guid]['auth'] === $auth;
	}

	private static function callOnValidOTPInCache(string $guid): void {
		if (static::hasOTPInCache($guid)) {
			$on_valid = static::$otpCache[$guid]['on_valid'];
			$on_valid();
		}
	}

	private static function removeOTPFromCache(string $guid): void {
		unset(static::$otpCache[$guid]);
	}
}
