<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Media;
use PDO;

/**
 * Persists attachment metadata and enforces its single-owner relationship.
 */
class MediaRepository
{
    private PDO $db;
    private string $table = 'media';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Create
    public function create(array $mediaData)
    {
        $postId = (int)($mediaData['post_id'] ?? 0);
        $replyId = (int)($mediaData['reply_id'] ?? 0);
        $type = $this->normaliseType((string)($mediaData['type'] ?? 'document'));
        $path = trim((string)($mediaData['path'] ?? ''));

        if (!$this->hasSingleOwner($postId, $replyId) || $path === '') {
            return false;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (post_id, reply_id, type, path, original_name, mime_type, file_size)
             VALUES (:post_id, :reply_id, :type, :path, :original_name, :mime_type, :file_size)"
        );

        return $stmt->execute([
            'post_id' => $postId > 0 ? $postId : null,
            'reply_id' => $replyId > 0 ? $replyId : null,
            'type' => $type,
            'path' => $path,
            'original_name' => $mediaData['original_name'] ?? null,
            'mime_type' => $mediaData['mime_type'] ?? null,
            'file_size' => (int)($mediaData['file_size'] ?? 0),
        ]);
    }

    // Read
    public function findAll()
    {
        $stmt = $this->db->query(
            "SELECT id, post_id, reply_id, type, path, original_name, mime_type, file_size, created_at
             FROM {$this->table}
             ORDER BY created_at DESC, id DESC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            "SELECT id, post_id, reply_id, type, path, original_name, mime_type, file_size, created_at
             FROM {$this->table}
             WHERE id = :id
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $mediaRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        return $mediaRecord ? new Media($mediaRecord) : null;
    }

    public function findByPostId(int $postId)
    {
        if ($postId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT id, post_id, reply_id, type, path, original_name, mime_type, file_size, created_at
             FROM {$this->table}
             WHERE post_id = :post_id
             ORDER BY created_at ASC, id ASC"
        );
        $stmt->execute(['post_id' => $postId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByReplyId(int $replyId)
    {
        if ($replyId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT id, post_id, reply_id, type, path, original_name, mime_type, file_size, created_at
             FROM {$this->table}
             WHERE reply_id = :reply_id
             ORDER BY created_at ASC, id ASC"
        );
        $stmt->execute(['reply_id' => $replyId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findReplyMediaByPostId(int $postId)
    {
        if ($postId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT m.id, m.post_id, m.reply_id, m.type, m.path,
                m.original_name, m.mime_type, m.file_size, m.created_at
             FROM {$this->table} m
             INNER JOIN replies r ON r.id = m.reply_id AND r.deleted_at IS NULL
             WHERE r.post_id = :post_id
             ORDER BY m.created_at ASC, m.id ASC"
        );
        $stmt->execute(['post_id' => $postId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllByPostId(int $postId)
    {
        if ($postId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            "SELECT m.id, m.post_id, m.reply_id, m.type, m.path,
                m.original_name, m.mime_type, m.file_size, m.created_at
             FROM {$this->table} m
             LEFT JOIN replies r ON r.id = m.reply_id
             WHERE m.post_id = :direct_post_id OR r.post_id = :reply_post_id
             ORDER BY m.created_at ASC, m.id ASC"
        );
        $stmt->execute([
            'direct_post_id' => $postId,
            'reply_post_id' => $postId,
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update
    public function update(int $id, array $mediaData)
    {
        $mediaEntity = $this->findById($id);

        if ($mediaEntity === null) {
            return false;
        }

        $postId = array_key_exists('post_id', $mediaData)
            ? (int)$mediaData['post_id']
            : (int)($mediaEntity->post_id ?? 0);
        $replyId = array_key_exists('reply_id', $mediaData)
            ? (int)$mediaData['reply_id']
            : (int)($mediaEntity->reply_id ?? 0);
        $type = $this->normaliseType((string)($mediaData['type'] ?? $mediaEntity->type ?? 'document'));
        $path = trim((string)($mediaData['path'] ?? $mediaEntity->path ?? ''));

        if (!$this->hasSingleOwner($postId, $replyId) || $path === '') {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET post_id = :post_id,
                reply_id = :reply_id,
                type = :type,
                path = :path,
                original_name = :original_name,
                mime_type = :mime_type,
                file_size = :file_size
             WHERE id = :id"
        );

        return $stmt->execute([
            'id' => $id,
            'post_id' => $postId > 0 ? $postId : null,
            'reply_id' => $replyId > 0 ? $replyId : null,
            'type' => $type,
            'path' => $path,
            'original_name' => $mediaData['original_name'] ?? $mediaEntity->original_name ?? null,
            'mime_type' => $mediaData['mime_type'] ?? $mediaEntity->mime_type ?? null,
            'file_size' => (int)($mediaData['file_size'] ?? $mediaEntity->file_size ?? 0),
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

    private function hasSingleOwner(int $postId, int $replyId)
    {
        return ($postId > 0 && $replyId <= 0) || ($replyId > 0 && $postId <= 0);
    }

    private function normaliseType(string $type)
    {
        return in_array($type, ['image', 'video', 'document'], true) ? $type : 'document';
    }

}
