<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Account;
use PDO;

class AccountRepository {
	use Repository;

	public function createAccount(string $guid, string $password, string $salt): void {
		$statement = $this->db->prepare(<<<SQL
			INSERT INTO accounts VALUES (:guid, :password, :salt)
		SQL
		);
		$statement->execute(compact('guid', 'password', 'salt'));
	}

	public function getAccountByGUID(string $guid): ?Account {
		$statement = $this->db->prepare(<<<SQL
			SELECT guid, password, salt
			FROM accounts
			WHERE guid = :guid
		SQL
		);
		$statement->execute(compact('guid'));
		$statement->setFetchMode(PDO::FETCH_CLASS, Account::class);
		return $statement->fetch() ?: null;
	}

	public function updatePassword(string $guid, string $hashed_new_password): void {
		$statement = $this->db->prepare(<<<SQL
			UPDATE accounts
			SET password = :hashed_new_password
			WHERE guid = :guid
		SQL
		);
		$statement->execute(compact('guid', 'hashed_new_password'));
	}
}
