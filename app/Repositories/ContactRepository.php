<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Contact;
use PDO;

/**
 * Persists contact messages and builds the filters used by the admin inbox.
 */
class ContactRepository
{
    private PDO $db;
    private string $table = 'contacts';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Create
    public function create(array $data)
    {
        $userId = filter_var($data['user_id'] ?? null, FILTER_VALIDATE_INT);
        $name = trim((string) ($data['name'] ?? ''));
        $email = trim((string) ($data['email'] ?? ''));
        $subject = trim((string) ($data['subject'] ?? ''));
        $message = trim((string) ($data['message'] ?? ''));

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (user_id, name, email, subject, message)
             VALUES (:user_id, :name, :email, :subject, :message)"
        );
        $stmt->bindValue(
            'user_id',
            $userId !== false && $userId > 0 ? $userId : null,
            $userId !== false && $userId > 0 ? PDO::PARAM_INT : PDO::PARAM_NULL
        );
        $stmt->bindValue('name', $name);
        $stmt->bindValue('email', $email);
        $stmt->bindValue('subject', $subject);
        $stmt->bindValue('message', $message);
        $stmt->execute();

        return (int) $this->db->lastInsertId();
    }

    // Read
    public function countAll(array $filters = [])
    {
        $params = [];
        $sql = "SELECT COUNT(*)
                FROM {$this->table} c
                LEFT JOIN users u ON u.id = c.user_id
                WHERE 1 = 1" . $this->contactFilterSql($filters, $params);

        $stmt = $this->db->prepare($sql);
        $this->bindContactParams($stmt, $params);
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function findPaginated(array $filters, int $limit, int $offset)
    {
        $params = [];
        $sql = $this->contactSelectSql()
            . $this->contactFilterSql($filters, $params)
            . ' ORDER BY c.created_at DESC, c.id DESC
                LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        $this->bindContactParams($stmt, $params);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $contactRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn(array $contactRecord): array => (new Contact($contactRecord))->toArray(),
            $contactRecords
        );
    }

    public function findById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare($this->contactSelectSql() . '
            WHERE c.id = :id
            LIMIT 1');
        $stmt->execute(['id' => $id]);
        $contact = $stmt->fetch(PDO::FETCH_ASSOC);

        return $contact ? new Contact($contact) : null;
    }

    // Update
    public function updateReadStatus(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET status = 'read', updated_at = NOW()
             WHERE id = :id AND status = 'unread'"
        );

        return $stmt->execute(['id' => $id]);
    }

    public function updateStatus(int $id, string $status)
    {
        $status = strtolower(trim($status));

        if ($id <= 0 || !in_array($status, ['unread', 'read', 'resolved'], true)) {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET status = :status, updated_at = NOW()
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }

    // Delete
    public function delete(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() > 0;
    }

    private function contactSelectSql()
    {
        return "
            SELECT
                c.id,
                c.user_id,
                c.name,
                c.email,
                c.subject,
                c.message,
                c.status,
                c.created_at,
                c.updated_at,
                u.id AS account_id,
                u.username AS account_username,
                TRIM(CONCAT_WS(' ', u.first_name, u.last_name)) AS account_full_name,
                u.role AS account_role
            FROM {$this->table} c
            LEFT JOIN users u ON u.id = c.user_id
        ";
    }

    private function contactFilterSql(array $filters, array &$params)
    {
        $sql = '';
        $query = trim((string) ($filters['q'] ?? ''));
        $status = trim((string) ($filters['status'] ?? ''));

        if ($query !== '') {
            $sql .= '
                AND (
                    c.name LIKE :query_name
                    OR c.email LIKE :query_email
                    OR c.subject LIKE :query_subject
                )';
            $queryValue = '%' . $query . '%';
            $params['query_name'] = $queryValue;
            $params['query_email'] = $queryValue;
            $params['query_subject'] = $queryValue;
        }

        if (in_array($status, ['unread', 'read', 'resolved'], true)) {
            $sql .= ' AND c.status = :status';
            $params['status'] = $status;
        }

        return $sql;
    }

    private function bindContactParams($stmt, array $params)
    {
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
    }
}
