<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Module;
use PDO;

/**
 * Provides module catalogue queries and administrative mutations.
 */
class ModuleRepository
{
    private PDO $db;
    private string $table = 'modules';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Create
    public function create(array $data)
    {
        $code = trim((string)($data['code'] ?? $data['module_code'] ?? ''));
        $name = trim((string)($data['name'] ?? $data['module_name'] ?? ''));
        $description = trim((string)($data['description'] ?? ''));

        if ($code === '' || $name === '') {
            return 0;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (module_code, module_name, description)
             VALUES (:module_code, :module_name, :description)"
        );
        $stmt->execute([
            'module_code' => $code,
            'module_name' => $name,
            'description' => $description !== '' ? $description : null,
        ]);

        return (int)$this->db->lastInsertId();
    }

    // Read
    public function findAll()
    {
        $stmt = $this->db->query(
            "SELECT id, module_code AS code, module_name AS name, description
             FROM {$this->table}
             ORDER BY module_code"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findPaginated(int $limit, int $offset)
    {
        $stmt = $this->db->prepare(
            "SELECT m.id, m.module_code AS code, m.module_name AS name, m.description,
                COUNT(p.id) AS discussion_count
             FROM {$this->table} m
             LEFT JOIN posts p ON p.module_id = m.id AND p.deleted_at IS NULL
             GROUP BY m.id, m.module_code, m.module_name, m.description
             ORDER BY m.module_code
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll()
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    public function existsByCodeExceptModule(string $code, int $moduleId = 0)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE module_code = :code";
        $params = ['code' => $code];

        if ($moduleId > 0) {
            $sql .= ' AND id <> :id';
            $params['id'] = $moduleId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

    public function findById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT id, module_code, module_name, description, created_at, updated_at
             FROM {$this->table}
             WHERE id = :id
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $module = $stmt->fetch(PDO::FETCH_ASSOC);

        return $module ? new Module($module) : null;
    }

    public function existsById(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*)
             FROM {$this->table}
             WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetchColumn() > 0;
    }

    public function findTrending(int $limit = 5)
    {
        $stmt = $this->db->prepare(
            "SELECT
                m.id,
                m.module_code AS code,
                m.module_name AS name,
                COUNT(p.id) AS post_count
             FROM {$this->table} m
             INNER JOIN posts p ON p.module_id = m.id AND p.deleted_at IS NULL
             GROUP BY m.id, m.module_code, m.module_name
             ORDER BY post_count DESC, m.module_code ASC
             LIMIT :limit"
        );

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update
    public function update(int $id, array $data)
    {
        $code = trim((string)($data['code'] ?? $data['module_code'] ?? ''));
        $name = trim((string)($data['name'] ?? $data['module_name'] ?? ''));
        $description = trim((string)($data['description'] ?? ''));

        if ($id <= 0 || $code === '' || $name === '') {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET module_code = :module_code,
                module_name = :module_name,
                description = :description,
                updated_at = NOW()
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $id,
            'module_code' => $code,
            'module_name' => $name,
            'description' => $description !== '' ? $description : null,
        ]);
    }

    // Delete
    public function delete(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            "DELETE FROM {$this->table}
             WHERE id = :id
                AND NOT EXISTS (
                    SELECT 1 FROM posts WHERE module_id = :post_module_id
                )"
        );
        $stmt->execute([
            'id' => $id,
            'post_module_id' => $id,
        ]);

        return $stmt->rowCount() > 0;
    }

}
