<?php
declare(strict_types=1);

namespace App;

use PDO;
use function App\Utils\env;

class DatabaseConnection {
	private static ?PDO $database = null;

	public static function getConnection(): PDO {
		if (static::$database === null) {
			$host = env('DB_HOST');
			$password = env('DB_PASSWORD');
			$username = env('DB_USERNAME');
			$port = env('DB_PORT');
			$database = env('DB_NAME');

			$dsn = "mysql:host=$host;port=$port;dbname=$database";
			static::$database = new PDO(
				$dsn, $username, $password,
				[
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::ATTR_EMULATE_PREPARES => true,
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
				]
			);
		}

		return static::$database;
	}
}
