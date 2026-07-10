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
            'SELECT id, module_code AS code, module_name AS name, description
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
            'SELECT id, module_code AS code, module_name AS name, description, created_at, updated_at
             FROM modules
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $module = $stmt->fetch(PDO::FETCH_ASSOC);

        return $module ?: null;
    }

    public function create(array $data)
    {
        $code = trim((string) ($data['code'] ?? $data['module_code'] ?? ''));
        $name = trim((string) ($data['name'] ?? $data['module_name'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));

        if ($code === '' || $name === '') {
            return 0;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO modules (module_code, module_name, description)
             VALUES (:module_code, :module_name, :description)'
        );
        $stmt->execute([
            'module_code' => $code,
            'module_name' => $name,
            'description' => $description !== '' ? $description : null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data)
    {
        $code = trim((string) ($data['code'] ?? $data['module_code'] ?? ''));
        $name = trim((string) ($data['name'] ?? $data['module_name'] ?? ''));
        $description = trim((string) ($data['description'] ?? ''));

        if ($id <= 0 || $code === '' || $name === '') {
            return false;
        }

        $stmt = $this->db->prepare(
            'UPDATE modules
             SET module_code = :module_code,
                module_name = :module_name,
                description = :description,
                updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'module_code' => $code,
            'module_name' => $name,
            'description' => $description !== '' ? $description : null,
        ]);
    }

    public function delete(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            'DELETE FROM modules
             WHERE id = :id
                AND NOT EXISTS (
                    SELECT 1 FROM posts WHERE module_id = :post_module_id
                )'
        );
        $stmt->execute([
            'id' => $id,
            'post_module_id' => $id,
        ]);

        return $stmt->rowCount() > 0;
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
