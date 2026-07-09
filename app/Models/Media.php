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
        $stmt = $this->db->prepare(
            'INSERT INTO media (post_id, type, path, original_name, mime_type, file_size)
             VALUES (:post_id, :type, :path, :original_name, :mime_type, :file_size)'
        );

        return $stmt->execute([
            'post_id' => (int) ($data['post_id'] ?? 0),
            'type' => $data['type'] ?? 'document',
            'path' => $data['path'] ?? '',
            'original_name' => $data['original_name'] ?? null,
            'mime_type' => $data['mime_type'] ?? null,
            'file_size' => (int) ($data['file_size'] ?? 0),
        ]);
    }

    public function getByPostId(int $postId)
    {
        if ($postId <= 0) {
            return [];
        }

        $stmt = $this->db->prepare(
            'SELECT id, post_id, type, path, original_name, mime_type, file_size, created_at
             FROM media
             WHERE post_id = :post_id
             ORDER BY created_at ASC, id ASC'
        );
        $stmt->execute(['post_id' => $postId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
