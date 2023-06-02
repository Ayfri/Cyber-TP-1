<?php
declare(strict_types=1);

namespace App;

use PDO;
use function App\Utils\env;

/**
 * Class DatabaseConnection is a class that represents a database connection
 * @package Database
 */
class DatabaseConnection {
	private ?PDO $database = null;

	/**
	 * getConnection is the function that gets the database connection
	 * @return PDO - the database connection
	 */
	public function getConnection(): PDO {
		if ($this->database === null) {
			$host = env('DB_HOST');
			$password = env('DB_PASSWORD');
			$username = env('DB_USERNAME');
			$port = env('DB_PORT');
			$database = env('DB_NAME');

			$this->database = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password,
				[
					PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
				]);
		}

		return $this->database;
	}
}
