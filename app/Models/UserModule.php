<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use Throwable;

class UserModule
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function hasSelectedModules(int $userId)
    {
        if ($userId <= 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            'SELECT 1
             FROM user_modules
             WHERE user_id = :user_id
             LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId]);

        return (bool) $stmt->fetchColumn();
    }

    public function getSelectedModuleIds(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT um.module_id
             FROM user_modules um
             INNER JOIN modules m ON m.id = um.module_id
             WHERE um.user_id = :user_id
             ORDER BY m.module_code'
        );
        $stmt->execute(['user_id' => $userId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    public function getModulesByUserId(int $userId): array
    {
        if ($userId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT
                m.id,
                m.module_code AS code,
                m.module_name AS name,
                m.description,
                COUNT(p.id) AS discussion_count
             FROM user_modules um
             INNER JOIN modules m ON m.id = um.module_id
             LEFT JOIN posts p ON p.module_id = m.id AND p.deleted_at IS NULL
             WHERE um.user_id = :user_id
             GROUP BY m.id, m.module_code, m.module_name, m.description
             ORDER BY m.module_code'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function validateModuleIds($submittedModuleIds): array
    {
        if (!is_array($submittedModuleIds)) {
            return [[], ['module_ids' => 'Select at least one module.']];
        }

        $moduleIds = [];

        foreach ($submittedModuleIds as $submittedModuleId) {
            if (is_array($submittedModuleId)) {
                return [$moduleIds, ['module_ids' => 'One or more selected modules are invalid.']];
            }

            $moduleId = filter_var($submittedModuleId, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);

            if ($moduleId === false) {
                return [$moduleIds, ['module_ids' => 'One or more selected modules are invalid.']];
            }

            $moduleIds[] = (int) $moduleId;
        }

        $moduleIds = array_values(array_unique($moduleIds));

        if (empty($moduleIds)) {
            return [[], ['module_ids' => 'Select at least one module.']];
        }

        $placeholders = implode(',', array_fill(0, count($moduleIds), '?'));
        $stmt = $this->db->prepare(
            'SELECT id FROM modules WHERE id IN (' . $placeholders . ')'
        );

        foreach ($moduleIds as $index => $moduleId) {
            $stmt->bindValue($index + 1, $moduleId, PDO::PARAM_INT);
        }

        $stmt->execute();
        $existingIds = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

        if (count($existingIds) !== count($moduleIds)) {
            return [$moduleIds, ['module_ids' => 'One or more selected modules do not exist.']];
        }

        return [$moduleIds, []];
    }

    public function replaceUserModules(int $userId, array $moduleIds)
    {
        if ($userId <= 0 || empty($moduleIds)) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            $delete = $this->db->prepare(
                'DELETE FROM user_modules WHERE user_id = :user_id'
            );
            $delete->execute(['user_id' => $userId]);

            $insert = $this->db->prepare(
                'INSERT INTO user_modules (user_id, module_id)
                 VALUES (:user_id, :module_id)'
            );

            foreach ($moduleIds as $moduleId) {
                $insert->execute([
                    'user_id' => $userId,
                    'module_id' => $moduleId,
                ]);
            }

            $this->db->commit();

            return true;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }
}
