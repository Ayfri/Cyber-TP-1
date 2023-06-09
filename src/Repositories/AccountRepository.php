<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\Account;
use PDO;

class AccountRepository {
	use Repository;

	public function createTempAccount(string $guid, string $password, string $salt): void {
		$statement = $this->db->prepare(
			<<<SQL
			INSERT INTO accounts_tmp VALUES (:guid, :password, :salt)
		SQL,
		);
		$statement->execute(compact('guid', 'password', 'salt'));
	}

	public function deleteAccount(string $guid): void {
		$statement = $this->db->prepare(
			<<<SQL
			DELETE FROM accounts
			WHERE guid = :guid
		SQL,
		);
		$statement->execute(compact('guid'));
	}

	public function deleteTempAccount(string $guid): void {
		$statement = $this->db->prepare(
			<<<SQL
			DELETE FROM accounts_tmp
			WHERE guid = :guid
		SQL,
		);
		$statement->execute(compact('guid'));
	}

	public function getAccountByGUID(string $guid): ?Account {
		$statement = $this->db->prepare(
			<<<SQL
			SELECT guid, password, salt
			FROM accounts
			WHERE guid = :guid
		SQL,
		);
		$statement->execute(compact('guid'));
		$statement->setFetchMode(PDO::FETCH_CLASS, Account::class);
		return $statement->fetch() ?: null;
	}

	public function getTempAccountByGUID(string $guid): ?Account {
		$statement = $this->db->prepare(
			<<<SQL
			SELECT guid, password, salt
			FROM accounts_tmp
			WHERE guid = :guid
		SQL,
		);
		$statement->execute(compact('guid'));
		$statement->setFetchMode(PDO::FETCH_CLASS, Account::class);
		return $statement->fetch() ?: null;
	}

	public function transferAccountToTemp(string $guid): void {
		$statement = $this->db->prepare(
			<<<SQL
			INSERT INTO accounts_tmp
			SELECT *
			FROM accounts
			WHERE guid = :guid
		SQL,
		);
		$statement->execute(compact('guid'));
	}

	public function transferTempAccountToAccount(string $guid): void {
		$statement = $this->db->prepare(
			<<<SQL
			INSERT INTO accounts
			SELECT *
			FROM accounts_tmp
			WHERE guid = :guid
		SQL,
		);
		$statement->execute(compact('guid'));
	}

	public function updateTempPassword(string $guid, string $hashed_new_password): void {
		$statement = $this->db->prepare(
			<<<SQL
			UPDATE accounts_tmp
			SET password = :hashed_new_password
			WHERE guid = :guid
		SQL,
		);
		$statement->execute(compact('guid', 'hashed_new_password'));
	}
}
