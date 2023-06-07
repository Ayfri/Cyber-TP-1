<?php
declare(strict_types=1);

namespace App\Repositories;

class AccountAuthorizationRepository {
	use Repository;

	public function createAccountAuthorization(string $guid, string $web_service): void {
		$statement = $this->db->prepare(
			<<<SQL
			INSERT INTO accounts_authorization VALUES (:guid, :web_service)
		SQL,
		);
		$statement->execute(compact('guid', 'web_service'));
	}

	public function isPublicAuthorization(string $guid): bool {
		$statement = $this->db->prepare(
			<<<SQL
			SELECT COUNT(*) AS count
			FROM accounts_authorization
			INNER JOIN public_authorizations
			ON accounts_authorization.web_service = public_authorizations.web_service
			WHERE accounts_authorization.guid = :guid
		SQL,
		);
		$statement->execute(compact('guid'));
		return (int)$statement->fetch()->count > 0;
	}
}
