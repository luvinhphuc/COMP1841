<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Reply
{
    private PDO $db;
    private ?array $userColumns = null;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getByPostId(int $postId)
    {
        if ($postId <= 0) {
            return [];
        }

        $userNameSelect = $this->userNameSelectSql();
        $stmt = $this->db->prepare(
            'SELECT
                r.id,
                r.post_id,
                r.user_id,
                r.content,
                r.is_accepted,
                r.created_at,
                r.updated_at,
                p.user_id AS post_user_id,
                u.username,
                u.role,
                u.avatar,
                ' . $userNameSelect . ' AS full_name
             FROM replies r
             INNER JOIN posts p ON p.id = r.post_id AND p.deleted_at IS NULL
             INNER JOIN users u ON u.id = r.user_id
             WHERE r.post_id = :post_id AND r.deleted_at IS NULL
             ORDER BY r.is_accepted DESC, r.created_at ASC'
        );

        $stmt->execute(['post_id' => $postId]);

        return $stmt->fetchAll();
    }

    public function create(array $data)
    {
        $postId = (int) ($data['post_id'] ?? 0);
        $userId = (int) ($data['user_id'] ?? 0);
        $content = trim((string) ($data['content'] ?? ''));

        if ($postId <= 0 || $userId <= 0 || $content === '') {
            return 0;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO replies (post_id, user_id, content)
             VALUES (:post_id, :user_id, :content)'
        );

        $stmt->execute([
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $content,
        ]);

        $replyId = (int) $this->db->lastInsertId();

        $this->touchPost($postId);

        return $replyId;
    }

    public function find(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $userNameSelect = $this->userNameSelectSql();
        $stmt = $this->db->prepare(
            'SELECT
                r.id,
                r.post_id,
                r.user_id,
                r.content,
                r.is_accepted,
                r.created_at,
                r.updated_at,
                p.user_id AS post_user_id,
                p.slug AS post_slug,
                p.title AS post_title,
                u.username,
                u.role,
                u.avatar,
                ' . $userNameSelect . ' AS full_name
             FROM replies r
             INNER JOIN posts p ON p.id = r.post_id AND p.deleted_at IS NULL
             INNER JOIN users u ON u.id = r.user_id
             WHERE r.id = :id AND r.deleted_at IS NULL
             LIMIT 1'
        );

        $stmt->execute(['id' => $id]);
        $reply = $stmt->fetch();

        return $reply ?: null;
    }

    public function update(int $id, string $content)
    {
        $content = trim($content);

        if ($id <= 0 || $content === '') {
            return false;
        }

        $reply = $this->find($id);

        if ($reply === null) {
            return false;
        }

        $stmt = $this->db->prepare(
            'UPDATE replies
             SET content = :content, updated_at = NOW()
             WHERE id = :id AND deleted_at IS NULL'
        );

        $updated = $stmt->execute([
            'id' => $id,
            'content' => $content,
        ]);

        if ($updated) {
            $this->touchPost((int) ($reply['post_id'] ?? 0));
        }

        return $updated;
    }

    public function delete(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        $reply = $this->find($id);

        if ($reply === null) {
            return false;
        }

        $postId = (int) ($reply['post_id'] ?? 0);
        $wasAccepted = (int) ($reply['is_accepted'] ?? 0) === 1;

        try {
            $this->db->beginTransaction();

            $this->db->prepare('DELETE FROM notifications WHERE reply_id = :reply_id')
                ->execute(['reply_id' => $id]);

            $stmt = $this->db->prepare(
                'UPDATE replies
                 SET deleted_at = NOW(), is_accepted = 0, updated_at = NOW()
                 WHERE id = :id AND deleted_at IS NULL'
            );

            $stmt->execute(['id' => $id]);
            $deleted = $stmt->rowCount() > 0;

            if ($deleted && $wasAccepted) {
                $this->db->prepare(
                    'UPDATE posts
                     SET status = "open", updated_at = NOW()
                     WHERE id = :post_id AND deleted_at IS NULL'
                )->execute(['post_id' => $postId]);
            }

            $this->db->commit();

            if ($deleted) {
                (new Post())->refreshActivityTimestamp($postId);
            }

            return $deleted;
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    private function touchPost(int $postId): void
    {
        if ($postId <= 0) {
            return;
        }

        $stmt = $this->db->prepare(
            'UPDATE posts
             SET updated_at = NOW()
             WHERE id = :post_id AND deleted_at IS NULL'
        );

        $stmt->execute(['post_id' => $postId]);
    }

    private function userNameSelectSql()
    {
        if ($this->userHasColumn('full_name')) {
            return 'u.full_name';
        }

        if ($this->userHasColumn('first_name') && $this->userHasColumn('last_name')) {
            return "TRIM(CONCAT_WS(' ', u.first_name, u.last_name))";
        }

        return 'u.username';
    }

    private function userHasColumn(string $column)
    {
        if ($this->userColumns === null) {
            $stmt = $this->db->prepare('SHOW COLUMNS FROM users');
            $stmt->execute();
            $this->userColumns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
        }

        return in_array($column, $this->userColumns, true);
    }
}
