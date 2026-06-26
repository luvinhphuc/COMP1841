<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Module
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAll()
    {
        $stmt = $this->db->query(
            'SELECT id, module_code AS code, module_name AS name
             FROM modules
             ORDER BY module_code'
        );

        return $stmt->fetchAll();
    }
}
