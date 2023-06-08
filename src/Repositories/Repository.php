<?php
declare(strict_types=1);

namespace App\Repositories;

use App\DatabaseConnection;
use PDO;

trait Repository {
	public PDO $db;

	public function __construct() {
		$this->db = DatabaseConnection::getConnection();
	}
}
