<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\AccountOTP;
use PDO;

class AccountOTPRepository {
	use Repository;

	public function createOTP(string $guid, int $otp, string $validity): void {
		$this->db->prepare(<<<SQL
			INSERT INTO accounts_otp (guid, otp, validity)
			VALUES (:guid, :otp, :validity)
		SQL
		)->execute(compact('guid', 'otp', 'validity'));
	}

	public function deleteOTP(string $guid): void {
		$this->db->prepare(<<<SQL
			DELETE FROM accounts_otp
			WHERE guid = :guid
		SQL
		)->execute(compact('guid'));
	}

	public function getOTPByGUID(string $guid): ?AccountOTP {
		$stmt = $this->db->prepare(<<<SQL
			SELECT *
			FROM accounts_otp
			WHERE guid = :guid
		SQL
		);
		$stmt->execute(compact('guid'));
		$otp = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($otp === false) {
			return null;
		}

		return new AccountOTP($otp['guid'], $otp['otp'], $otp['validity']);
	}
}
