<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;

/**
 * Encapsulates account lookup, profile statistics, updates, and anonymised deletion.
 */
class UserRepository
{
    private PDO $db;
    private string $table = 'users';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Account creation -----------------------------------------------------
    public function create(array $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO ' . $this->table . '
                (first_name, last_name, username, email, password, avatar, role)
             VALUES (:first_name, :last_name, :username, :email, :password, :avatar, :role)'
        );

        return $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'avatar' => $data['avatar'] ?? null,
            'role' => $data['role'] ?? 'student',
        ]);
    }

    // Account and profile queries -----------------------------------------
    public function findPaginated(int $limit, int $offset)
    {
        $fullNameSelect = $this->fullNameSelectSql();
        $stmt = $this->db->prepare(
            'SELECT id, first_name, last_name, ' . $fullNameSelect . ' AS full_name,
                username, email, avatar, role, created_at
             FROM ' . $this->table . '
             WHERE deleted_at IS NULL
             ORDER BY id DESC
             LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll()
    {
        return (int) $this->db->query(
            'SELECT COUNT(*) FROM ' . $this->table . ' WHERE deleted_at IS NULL'
        )->fetchColumn();
    }

    public function countAdmins()
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM {$this->table} WHERE role = 'admin' AND deleted_at IS NULL"
        )->fetchColumn();
    }

    public function findProfileStatistics(int $userId): ?array
    {
        if ($userId <= 0) {
            return null;
        }

        $userRecord = $this->getUserMemberSince($userId);

        if (!$userRecord) {
            return null;
        }

        $postStats = $this->getPostStatistics($userId);
        $replyStats = $this->getReplyStatistics($userId);

        return [
            'questions_asked' => (int) ($postStats['questions_asked'] ?? 0),
            'replies_posted' => (int) ($replyStats['replies_posted'] ?? 0),
            'solved_questions' => (int) ($postStats['solved_questions'] ?? 0),
            'total_post_views' => (int) ($postStats['total_post_views'] ?? 0),
            'member_since' => (string) $userRecord['member_since'],
        ];
    }

    public function existsByEmailExceptUser(string $email, int $userId)
    {
        $sql = 'SELECT id FROM ' . $this->table . ' WHERE email = :email';
        $params = ['email' => $email];

        if ($userId > 0) {
            $sql .= ' AND id <> :user_id';
            $params['user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);

        return (bool) $stmt->fetch();
    }

    public function existsByUsernameExceptUser(string $username, int $userId)
    {
        $sql = 'SELECT id FROM ' . $this->table . ' WHERE username = :username';
        $params = ['username' => $username];

        if ($userId > 0) {
            $sql .= ' AND id <> :user_id';
            $params['user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);

        return (bool) $stmt->fetch();
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare(
            'SELECT id, first_name, last_name, username, email, password, avatar, role,
                created_at, updated_at, deleted_at
             FROM ' . $this->table . '
             WHERE username = :username AND deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute(['username' => $username]);
        $userRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userRecord ? new User($userRecord) : null;
    }

    public function findById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT id, first_name, last_name, username, email, password, avatar, role,
                created_at, updated_at, deleted_at
             FROM ' . $this->table . '
             WHERE id = :id AND deleted_at IS NULL
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $userRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userRecord ? new User($userRecord) : null;
    }

    // Profile and administration mutations -------------------------------
    public function updateProfile(int $userId, array $data)
    {
        $stmt = $this->db->prepare(
            'UPDATE ' . $this->table . '
             SET first_name = :first_name,
                 last_name = :last_name,
                 username = :username,
                 email = :email,
                 updated_at = NOW()
             WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'id' => $userId,
        ]);
    }

    public function updateAvatar(int $userId, string $avatarPath)
    {
        $stmt = $this->db->prepare(
            'UPDATE ' . $this->table . '
             SET avatar = :avatar, updated_at = NOW()
             WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute([
            'avatar' => $avatarPath,
            'id' => $userId,
        ]);
    }

    public function updatePassword(int $userId, string $passwordHash)
    {
        $stmt = $this->db->prepare(
            'UPDATE ' . $this->table . '
             SET password = :password, updated_at = NOW()
             WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute([
            'password' => $passwordHash,
            'id' => $userId,
        ]);
    }

    public function updateFromAdmin(int $id, array $data)
    {
        $stmt = $this->db->prepare(
            'UPDATE ' . $this->table . '
             SET first_name = :first_name,
                 last_name = :last_name,
                 username = :username,
                 email = :email,
                 role = :role,
                 updated_at = NOW()
             WHERE id = :id AND deleted_at IS NULL'
        );

        return $stmt->execute([
            'id' => $id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);
    }

    // Privacy-preserving account removal ---------------------------------
    public function softDeleteAndAnonymize(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        $suffix = str_pad((string) $id, 2, '0', STR_PAD_LEFT);

        try {
            // Preserve authored content while removing credentials and personal identifiers.
            $this->db->beginTransaction();

            $stmt = $this->db->prepare(
                'UPDATE ' . $this->table . '
                 SET first_name = :first_name,
                     last_name = :last_name,
                     username = :username,
                     email = :email,
                     password = NULL,
                     avatar = NULL,
                     role = :role,
                     updated_at = NOW(),
                     deleted_at = NOW()
                 WHERE id = :id AND deleted_at IS NULL'
            );
            $stmt->execute([
                'id' => $id,
                'first_name' => 'Deleted',
                'last_name' => 'User ' . $suffix,
                'username' => 'deleted_user_' . $suffix,
                'email' => 'deleted_user_' . $suffix . '@deleted.invalid',
                'role' => 'student',
            ]);

            if ($stmt->rowCount() !== 1) {
                $this->db->rollBack();

                return false;
            }

            $this->db->commit();

            return true;
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    private function getUserMemberSince(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT created_at AS member_since
         FROM {$this->table}
         WHERE id = :user_id AND deleted_at IS NULL
         LIMIT 1"
        );

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function getPostStatistics(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT
            COUNT(*) AS questions_asked,
            SUM(CASE WHEN status = 'solved' THEN 1 ELSE 0 END) AS solved_questions,
            COALESCE(SUM(view_count), 0) AS total_post_views
         FROM posts
         WHERE user_id = :user_id
           AND deleted_at IS NULL"
        );

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function getReplyStatistics(int $userId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) AS replies_posted
         FROM replies
         WHERE user_id = :user_id
           AND deleted_at IS NULL'
        );

        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function fullNameSelectSql()
    {
        return "TRIM(CONCAT_WS(' ', first_name, last_name))";
    }

}
