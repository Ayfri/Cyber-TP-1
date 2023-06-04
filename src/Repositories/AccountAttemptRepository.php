<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\AccountAttempt;
use PDO;

class AccountAttemptRepository {
	use Repository;

	public function createAccountAttempt(string $guid): void {
		$this->db->prepare(<<<SQL
			INSERT INTO accounts_attempt (guid)
			VALUES (:guid)
		SQL
		)->execute(compact('guid'));
	}

	public function deleteAccountAttempts(string $guid): void {
		$this->db->prepare(<<<SQL
			DELETE FROM accounts_attempt
			WHERE guid = :guid
		SQL
		)->execute(compact('guid'));
	}

	public function getAccountAttempts(string $guid, int $interval): array {
		$stmt = $this->db->prepare(<<<SQL
			SELECT time
			FROM accounts_attempt
			WHERE guid = :guid AND time > DATE_SUB(NOW(), INTERVAL :interval MINUTE)
		SQL
		);
		$stmt->execute(compact('guid', 'interval'));
		$attempts = [];
		while ($attempt = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$attempts[] = new AccountAttempt($guid, $attempt['time']);
		}
		return $attempts;
	}
}
