<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Reply;
use PDO;

/**
 * Persists nested replies and coordinates accepted-solution state with discussions.
 */
class ReplyRepository
{
    private PDO $db;
    private string $table = 'replies';
    private ?array $userColumns = null;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Create
    public function create(array $data)
    {
        $postId = (int) ($data['post_id'] ?? 0);
        $userId = (int) ($data['user_id'] ?? 0);
        $content = trim((string) ($data['content'] ?? ''));
        $parentReplyId = (int) ($data['parent_reply_id'] ?? 0);

        if ($postId <= 0 || $userId <= 0 || $content === '') {
            return 0;
        }

        if ($parentReplyId > 0 && !$this->parentReplyBelongsToPost($parentReplyId, $postId)) {
            $parentReplyId = 0;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (post_id, parent_reply_id, user_id, content)
                    VALUES (:post_id, :parent_reply_id, :user_id, :content)"
        );

        $stmt->execute([
            'post_id' => $postId,
            'parent_reply_id' => $parentReplyId > 0 ? $parentReplyId : null,
            'user_id' => $userId,
            'content' => $content,
        ]);

        $replyId = (int) $this->db->lastInsertId();

        $this->touchPost($postId);

        return $replyId;
    }

    // Read
    public function findByPostId(int $postId)
    {
        if ($postId <= 0) {
            return [];
        }

        $userNameSelect = $this->userNameSelectSql('u');
        $parentNameSelect = $this->userNameSelectSql('parent_u');

        $stmt = $this->db->prepare(
            'SELECT
                r.id,
                r.post_id,
                r.parent_reply_id,
                r.user_id,
                r.content,
                r.is_accepted,
                r.created_at,
                r.updated_at,
                p.user_id AS post_user_id,
                u.username,
                u.role,
                u.avatar,
                ' . $userNameSelect . ' AS full_name,
                parent_u.username AS parent_author_username,
                ' . $parentNameSelect . " AS parent_author_name
            FROM {$this->table} r
            INNER JOIN posts p ON p.id = r.post_id AND p.deleted_at IS NULL
            INNER JOIN users u ON u.id = r.user_id
            LEFT JOIN replies parent_r ON parent_r.id = r.parent_reply_id AND parent_r.deleted_at IS NULL AND parent_r.post_id = r.post_id
            LEFT JOIN users parent_u ON parent_u.id = parent_r.user_id
            WHERE r.post_id = :post_id AND r.deleted_at IS NULL
            ORDER BY r.is_accepted DESC, r.created_at DESC, r.id DESC"
        );

        $stmt->execute(['post_id' => $postId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT id, post_id, parent_reply_id, user_id, content, is_accepted,
                created_at, updated_at, deleted_at
             FROM {$this->table}
             WHERE id = :id AND deleted_at IS NULL
             LIMIT 1"
        );

        $stmt->execute(['id' => $id]);
        $reply = $stmt->fetch(PDO::FETCH_ASSOC);

        return $reply ? new Reply($reply) : null;
    }

    public function findDetailsById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $userNameSelect = $this->userNameSelectSql();
        $stmt = $this->db->prepare(
            'SELECT
                r.id,
                r.post_id,
                r.parent_reply_id,
                r.user_id,
                r.content,
                r.is_accepted,
                r.created_at,
                r.updated_at,
                p.user_id AS post_user_id,
                p.slug AS post_slug,
                p.title AS post_title,
                p.status AS post_status,
                u.username,
                u.role,
                u.avatar,
                ' . $userNameSelect . " AS full_name
             FROM {$this->table} r
             INNER JOIN posts p ON p.id = r.post_id AND p.deleted_at IS NULL
             INNER JOIN users u ON u.id = r.user_id
             WHERE r.id = :id AND r.deleted_at IS NULL
             LIMIT 1"
        );

        $stmt->execute(['id' => $id]);
        $reply = $stmt->fetch(PDO::FETCH_ASSOC);

        return $reply ?: null;
    }

    // Update
    public function markAsSolved(int $replyId)
    {
        if ($replyId <= 0) {
            return false;
        }

        $replyStatement = $this->db->prepare(
            "SELECT post_id
             FROM {$this->table}
             WHERE id = :id AND deleted_at IS NULL
             LIMIT 1"
        );
        $replyStatement->execute(['id' => $replyId]);
        $postId = (int) $replyStatement->fetchColumn();

        if ($postId <= 0) {
            return false;
        }

        try {
            // Lock the parent post so concurrent requests cannot accept two solutions.
            $this->db->beginTransaction();

            $postStatement = $this->db->prepare(
                'SELECT id
                 FROM posts
                 WHERE id = :post_id AND deleted_at IS NULL
                 FOR UPDATE'
            );
            $postStatement->execute(['post_id' => $postId]);

            if ($postStatement->fetchColumn() === false) {
                $this->db->rollBack();
                return false;
            }

            $this->db->prepare(
                "UPDATE {$this->table}
                 SET is_accepted = 0, updated_at = NOW()
                 WHERE post_id = :post_id AND deleted_at IS NULL"
            )->execute(['post_id' => $postId]);

            $targetStatement = $this->db->prepare(
                "UPDATE {$this->table}
                 SET is_accepted = 1, updated_at = NOW()
                 WHERE id = :reply_id AND post_id = :post_id AND deleted_at IS NULL"
            );
            $targetStatement->execute([
                'reply_id' => $replyId,
                'post_id' => $postId,
            ]);

            if ($targetStatement->rowCount() === 0) {
                $this->db->rollBack();
                return false;
            }

            $this->db->prepare(
                'UPDATE posts
                 SET status = "solved", updated_at = NOW()
                 WHERE id = :post_id AND deleted_at IS NULL'
            )->execute(['post_id' => $postId]);

            $this->db->commit();
            (new PostRepository())->updateActivityTimestamp($postId);

            return true;
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    public function unmarkAsSolved(int $replyId)
    {
        if ($replyId <= 0) {
            return false;
        }

        $replyStatement = $this->db->prepare(
            "SELECT post_id
             FROM {$this->table}
             WHERE id = :id AND is_accepted = 1 AND deleted_at IS NULL
             LIMIT 1"
        );
        $replyStatement->execute(['id' => $replyId]);
        $postId = (int) $replyStatement->fetchColumn();

        if ($postId <= 0) {
            return false;
        }

        try {
            // Accepted-reply and post status changes must remain synchronised.
            $this->db->beginTransaction();

            $postStatement = $this->db->prepare(
                'SELECT id
                 FROM posts
                 WHERE id = :post_id AND deleted_at IS NULL
                 FOR UPDATE'
            );
            $postStatement->execute(['post_id' => $postId]);

            if ($postStatement->fetchColumn() === false) {
                $this->db->rollBack();
                return false;
            }

            $this->db->prepare(
                "UPDATE {$this->table}
                 SET is_accepted = 0, updated_at = NOW()
                 WHERE post_id = :post_id AND deleted_at IS NULL"
            )->execute(['post_id' => $postId]);

            $this->db->prepare(
                'UPDATE posts
                 SET status = "open", updated_at = NOW()
                 WHERE id = :post_id AND deleted_at IS NULL'
            )->execute(['post_id' => $postId]);

            $this->db->commit();
            (new PostRepository())->updateActivityTimestamp($postId);

            return true;
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    public function update(int $id, string $content)
    {
        $content = trim($content);

        if ($id <= 0 || $content === '') {
            return false;
        }

        $reply = $this->findById($id);

        if ($reply === null) {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET content = :content, updated_at = NOW()
             WHERE id = :id AND deleted_at IS NULL"
        );

        $updated = $stmt->execute([
            'id' => $id,
            'content' => $content,
        ]);

        if ($updated) {
            $this->touchPost((int) ($reply->post_id ?? 0));
        }

        return $updated;
    }

    // Delete
    public function delete(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        $reply = $this->findById($id);

        if ($reply === null) {
            return false;
        }

        $postId = (int) ($reply->post_id ?? 0);
        $wasAccepted = (int) ($reply->is_accepted ?? 0) === 1;

        try {
            // Deleting an accepted reply also reopens its discussion in the same transaction.
            $this->db->beginTransaction();

            $this->db->prepare('DELETE FROM media WHERE reply_id = :reply_id')
                ->execute(['reply_id' => $id]);

            $stmt = $this->db->prepare(
                "UPDATE {$this->table}
                 SET deleted_at = NOW(), is_accepted = 0, updated_at = NOW()
                 WHERE id = :id AND deleted_at IS NULL"
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
                (new PostRepository())->updateActivityTimestamp($postId);
            }

            return $deleted;
        } catch (\Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $exception;
        }
    }

    private function parentReplyBelongsToPost(int $parentReplyId, int $postId)
    {
        $stmt = $this->db->prepare(
            "SELECT id
             FROM {$this->table}
             WHERE id = :id AND post_id = :post_id AND deleted_at IS NULL
             LIMIT 1"
        );

        $stmt->execute([
            'id' => $parentReplyId,
            'post_id' => $postId,
        ]);

        return $stmt->fetchColumn() !== false;
    }

    private function touchPost(int $postId)
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

    private function userNameSelectSql(string $alias = 'u')
    {
        if ($this->userHasColumn('full_name')) {
            return $alias . '.full_name';
        }

        if ($this->userHasColumn('first_name') && $this->userHasColumn('last_name')) {
            return "TRIM(CONCAT_WS(' ', " . $alias . ".first_name, " . $alias . ".last_name))";
        }

        return $alias . '.username';
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
