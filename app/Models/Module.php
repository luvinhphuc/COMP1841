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

    public function exists(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM modules
             WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetchColumn() > 0;
    }

    public function getTrending(int $limit = 5)
    {
        $stmt = $this->db->prepare(
            'SELECT
                m.id,
                m.module_code AS code,
                m.module_name AS name,
                COUNT(p.id) AS post_count
             FROM modules m
             INNER JOIN posts p ON p.module_id = m.id AND p.deleted_at IS NULL
             GROUP BY m.id, m.module_code, m.module_name
             ORDER BY post_count DESC, m.module_code ASC
             LIMIT :limit'
        );

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
