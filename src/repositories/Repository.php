<?php

namespace App\repositories;

use App\DatabaseConnection;
use PDO;

trait Repository {
    public PDO $db;

    public function __construct() {
        $this->db = (new DatabaseConnection())->getConnection();
    }
}
