<?php
declare(strict_types=1);

namespace App\Repositories;

class AccountRepository {
	use Repository;

	public function createAccount(string $guid, string $password, string $salt): void {
		$statement = $this->db->prepare(<<<SQL
			INSERT INTO accounts VALUES (guid, password, salt)
		SQL
		);
		$statement->execute(compact('guid', 'password', 'salt'));
	}
}
