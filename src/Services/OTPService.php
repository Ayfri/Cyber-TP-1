<?php
declare(strict_types=1);

namespace App\Services;

use App\Repositories\AccountOTPRepository;
use App\Repositories\AccountRepository;
use Exception;

class OTPService extends Service {
	private AccountOTPRepository $accountOTPRepository;
	private AccountRepository $accountRepository;

	public function __construct() {
		parent::__construct(false);
		$this->accountOTPRepository = new AccountOTPRepository();
		$this->accountRepository = new AccountRepository();
	}

	/**
	 * @throws Exception
	 */
	public static function askForOTP(string $guid, string $auth, string $callback): never {
		$_SESSION['temp_guid'] = $guid;
		$_SESSION['type'] = $auth;
		$_SESSION['callback'] = $callback;
		Service::redirect('/otp-verify');
	}

	/**
	 * @throws Exception
	 */
	private static function generateOTP(): int {
		return random_int(100000, 999999);
	}

	/**
	 * @throws Exception
	 */
	public function getOTPForUser(string $guid, bool $temp = false): string {
		$account = $temp
			? $this->accountRepository->getTempAccountByGUID($guid)
			: $this->accountRepository->getAccountByGUID($guid);

		if ($account === null) {
			Service::sendError('Account not found.', 404);
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
	protected function handleRoutes(): never {
		$is_temporary = isset($_SESSION['type']) && $_SESSION['type'] !== 'delete-account';

		if (isset($_SESSION['temp_guid']) || isset($_SESSION['guid'])) {
			$user_guid = $is_temporary ? $_SESSION['temp_guid'] : $_SESSION['guid'];
		} else {
			$user_guid = null;
		}

		if (Service::onRouteGet('/otp')) {
			if ($user_guid === null) {
				Service::redirect('/login');
			}

			if (!isset($_SESSION['type'])) {
				$otp = 'No otp request found.';
			} else {
				$otp = $this->getOTPForUser($user_guid, $is_temporary);
			}

			$this->render('otp', compact('otp'));
		}

		if (Service::onRouteGet('/otp-verify')) {
			if ($user_guid === null) {
				Service::redirect('/login');
			}
			$this->render('otp-verify');
		}

		if (Service::onRouteGet('/get-otp')) {
			if (!isset($_SESSION['type']) || $user_guid === null) {
				Service::sendError('No otp request found.', 404);
			}
			$otp = $this->getOTPForUser($user_guid, $is_temporary);
			Service::sendResponse($otp);
		}

		if (Service::onRoutePost('/otp-verify')) {
			if ($user_guid === null) {
				Service::sendError('User not found.', 404);
			}

			if (!isset($_SESSION['type'])) {
				Service::sendError('No otp request found.', 404);
			}

			$callback = $_SESSION['callback'];
			$cancelled = $this->data['cancelled'] ?? false;

			if ($cancelled) {
				$this->accountOTPRepository->deleteOTP($user_guid);
				unset($_SESSION['type'], $_SESSION['callback'], $_SESSION['temp_guid']);
				$callback($_SESSION['type'], $_SESSION['temp_guid'], true);
				Service::sendSuccess();
			}

			$otp = (int)$this->getRequiredParam('otp');
			$guid = $_SESSION['temp_guid'];
			$valid_otp = $this->accountOTPRepository->getOTPByGUID($guid);
			if ($valid_otp?->otp !== $otp) {
				Service::sendError('OTP does not match.', 403);
			}

			$callback($guid, $_SESSION['type'], false);
			unset($_SESSION['type'], $_SESSION['callback'], $_SESSION['temp_guid']);
			$this->accountOTPRepository->deleteOTP($guid);
			Service::sendSuccess();
		}

		Service::sendError('Route not found.', 404);
	}

	public static function routes(): array {
		return ['/otp', '/get-otp', '/otp-verify'];
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
}
