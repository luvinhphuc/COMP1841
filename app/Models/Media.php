<?php

namespace App\Models;

/**
 * Represents an uploaded file owned by exactly one discussion or reply.
 */
class Media
{
    public ?int $id = null;
    public ?int $post_id = null;
    public ?int $reply_id = null;
    public string $type = 'document';
    public string $path = '';
    public ?string $original_name = null;
    public ?string $mime_type = null;
    public ?int $file_size = null;
    public ?string $created_at = null;

    /** Hydrates attachment metadata from a repository result. */
    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int) $data['id'] : null;
        $this->post_id = isset($data['post_id']) ? (int) $data['post_id'] : null;
        $this->reply_id = isset($data['reply_id']) ? (int) $data['reply_id'] : null;
        $this->type = (string) ($data['type'] ?? 'document');
        $this->path = (string) ($data['path'] ?? '');
        $this->original_name = isset($data['original_name']) ? (string) $data['original_name'] : null;
        $this->mime_type = isset($data['mime_type']) ? (string) $data['mime_type'] : null;
        $this->file_size = isset($data['file_size']) ? (int) $data['file_size'] : null;
        $this->created_at = isset($data['created_at']) ? (string) $data['created_at'] : null;
    }
}
