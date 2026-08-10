<?php

namespace App\Models;

/**
 * Represents a reply, including nesting and accepted-solution state.
 */
class Reply
{
    public ?int $id = null;
    public ?int $post_id = null;
    public ?int $parent_reply_id = null;
    public ?int $user_id = null;
    public string $content = '';
    public int $is_accepted = 0;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $deleted_at = null;

    /** Hydrates a nested reply from a repository result. */
    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int) $data['id'] : null;
        $this->post_id = isset($data['post_id']) ? (int) $data['post_id'] : null;
        $this->parent_reply_id = isset($data['parent_reply_id']) ? (int) $data['parent_reply_id'] : null;
        $this->user_id = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $this->content = (string) ($data['content'] ?? '');
        $this->is_accepted = (int) ($data['is_accepted'] ?? 0);
        $this->created_at = isset($data['created_at']) ? (string) $data['created_at'] : null;
        $this->updated_at = isset($data['updated_at']) ? (string) $data['updated_at'] : null;
        $this->deleted_at = isset($data['deleted_at']) ? (string) $data['deleted_at'] : null;
    }
}
