<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use PDO;

class UserRepository {
	use Repository;

	public function createUser(string $guid, string $email): void {
		$this->db->prepare(
			<<<SQL
            INSERT INTO users (guid, email)
            VALUES (:guid, :email)
        SQL,
		)->execute(compact('guid', 'email'));
	}

	public function deleteUser(string $guid): void {
		$this->db->prepare(
			<<<SQL
			DELETE FROM users
			WHERE guid = :guid
		SQL,
		)->execute(compact('guid'));
	}

	public function getUserByEmail(string $email): ?User {
		$stmt = $this->db->prepare(
			<<<SQL
			SELECT email, guid
			FROM users
			WHERE email = :email
		SQL,
		);
		$stmt->execute(compact('email'));
		$stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
		return $stmt->fetch() ?: null;
	}

	public function getUserByGUID(string $guid): ?User {
		$stmt = $this->db->prepare(
			<<<SQL
			SELECT email, guid
			FROM users
			WHERE guid = :guid
		SQL,
		);
		$stmt->execute(compact('guid'));
		$stmt->setFetchMode(PDO::FETCH_CLASS, User::class);
		return $stmt->fetch() ?: null;
	}
}
