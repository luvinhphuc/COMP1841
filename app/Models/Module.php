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

    public function findById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT id, module_code AS code, module_name AS name
             FROM modules
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $module = $stmt->fetch(PDO::FETCH_ASSOC);

        return $module ?: null;
    }
}
