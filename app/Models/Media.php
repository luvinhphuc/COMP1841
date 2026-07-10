<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Media
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create(array $data)
    {
        $postId = (int) ($data['post_id'] ?? 0);
        $replyId = (int) ($data['reply_id'] ?? 0);
        $type = $this->normaliseType((string) ($data['type'] ?? 'document'));
        $path = trim((string) ($data['path'] ?? ''));

        if (!$this->hasSingleOwner($postId, $replyId) || $path === '') {
            return false;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO media (post_id, reply_id, type, path, original_name, mime_type, file_size)
             VALUES (:post_id, :reply_id, :type, :path, :original_name, :mime_type, :file_size)'
        );

        return $stmt->execute([
            'post_id' => $postId > 0 ? $postId : null,
            'reply_id' => $replyId > 0 ? $replyId : null,
            'type' => $type,
            'path' => $path,
            'original_name' => $data['original_name'] ?? null,
            'mime_type' => $data['mime_type'] ?? null,
            'file_size' => (int) ($data['file_size'] ?? 0),
        ]);
    }

    public function getAll()
    {
        $stmt = $this->db->query(
            'SELECT id, post_id, reply_id, type, path, original_name, mime_type, file_size, created_at
             FROM media
             ORDER BY created_at DESC, id DESC'
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $stmt = $this->db->prepare(
            'SELECT id, post_id, reply_id, type, path, original_name, mime_type, file_size, created_at
             FROM media
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $media = $stmt->fetch(PDO::FETCH_ASSOC);

        return $media ?: null;
    }

    public function getByPostId(int $postId)
    {
        if ($postId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT id, post_id, reply_id, type, path, original_name, mime_type, file_size, created_at
             FROM media
             WHERE post_id = :post_id
             ORDER BY created_at ASC, id ASC'
        );
        $stmt->execute(['post_id' => $postId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByReplyId(int $replyId)
    {
        if ($replyId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT id, post_id, reply_id, type, path, original_name, mime_type, file_size, created_at
             FROM media
             WHERE reply_id = :reply_id
             ORDER BY created_at ASC, id ASC'
        );
        $stmt->execute(['reply_id' => $replyId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReplyMediaByPostId(int $postId)
    {
        if ($postId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT m.id, m.post_id, m.reply_id, m.type, m.path,
                m.original_name, m.mime_type, m.file_size, m.created_at
             FROM media m
             INNER JOIN replies r ON r.id = m.reply_id AND r.deleted_at IS NULL
             WHERE r.post_id = :post_id
             ORDER BY m.created_at ASC, m.id ASC'
        );
        $stmt->execute(['post_id' => $postId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(int $id, array $data)
    {
        $media = $this->find($id);

        if ($media === null) {
            return false;
        }

        $postId = array_key_exists('post_id', $data)
            ? (int) $data['post_id']
            : (int) ($media['post_id'] ?? 0);
        $replyId = array_key_exists('reply_id', $data)
            ? (int) $data['reply_id']
            : (int) ($media['reply_id'] ?? 0);
        $type = $this->normaliseType((string) ($data['type'] ?? $media['type'] ?? 'document'));
        $path = trim((string) ($data['path'] ?? $media['path'] ?? ''));

        if (!$this->hasSingleOwner($postId, $replyId) || $path === '') {
            return false;
        }

        $stmt = $this->db->prepare(
            'UPDATE media
             SET post_id = :post_id,
                reply_id = :reply_id,
                type = :type,
                path = :path,
                original_name = :original_name,
                mime_type = :mime_type,
                file_size = :file_size
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'post_id' => $postId > 0 ? $postId : null,
            'reply_id' => $replyId > 0 ? $replyId : null,
            'type' => $type,
            'path' => $path,
            'original_name' => $data['original_name'] ?? $media['original_name'] ?? null,
            'mime_type' => $data['mime_type'] ?? $media['mime_type'] ?? null,
            'file_size' => (int) ($data['file_size'] ?? $media['file_size'] ?? 0),
        ]);
    }

    public function delete(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        $stmt = $this->db->prepare('DELETE FROM media WHERE id = :id');
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
