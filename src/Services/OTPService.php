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
		$_SESSION['guid'] = $guid;
		$_SESSION['type'] = $auth;
		$_SESSION['callback'] = $callback;
		Service::redirect('/otp-verify');
	}

	public static function routes(): array {
		return ['/otp', '/get-otp', '/otp-verify'];
	}

	/**
	 * @throws Exception
	 */
	protected function handleRoutes(): never {
		$is_temporary = isset($_SESSION['type']) && $_SESSION['type'] !== 'delete-account';

		if (isset($_SESSION['guid']) || isset($_SESSION['user'])) {
			$user_guid = $is_temporary ? $_SESSION['guid'] : $_SESSION['user']->guid;
		} else {
			$user_guid = null;
		}

		if (Service::onRouteGet('/otp')) {
			if (!isset($_SESSION['type']) || $user_guid === null) {
				$otp = 'No otp request found.';
			} else {
				// TODO : Fix this being called when no otp is requested and user is logged in but should not be.
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
			if (!isset($_SESSION['guid'], $_SESSION['type'], $_SESSION['callback'])) {
				Service::sendError('OTP not found.', 404);
			}

			$callback = $_SESSION['callback'];
			$cancelled = $this->data['cancelled'] ?? false;

			if ($cancelled) {
				$this->accountOTPRepository->deleteOTP($user_guid);
				unset($_SESSION['type'], $_SESSION['callback'], $_SESSION['guid']);
				$callback($_SESSION['type'], $_SESSION['guid'], true);
				Service::sendSuccess();
			}

			$otp = (int)$this->getRequiredParam('otp');
			$guid = $_SESSION['guid'];
			$valid_otp = $this->accountOTPRepository->getOTPByGUID($guid);
			if ($valid_otp?->otp !== $otp) {
				Service::sendError('OTP does not match.', 403);
			}

			$callback($guid, $_SESSION['type'], false);
			unset($_SESSION['type'], $_SESSION['callback'], $_SESSION['guid']);
			$this->accountOTPRepository->deleteOTP($guid);
			Service::sendSuccess();
		}

		Service::sendError('Route not found.', 404);
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
