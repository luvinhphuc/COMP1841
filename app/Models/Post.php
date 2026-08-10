<?php

namespace App\Models;

/**
 * Represents a discussion post and its lifecycle metadata.
 */
class Post
{
    public ?int $id = null;
    public string $title = '';
    public ?string $slug = null;
    public string $content = '';
    public string $status = 'open';
    public int $view_count = 0;
    public ?int $user_id = null;
    public ?int $module_id = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $deleted_at = null;

    /** Hydrates a discussion from a repository result. */
    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int) $data['id'] : null;
        $this->title = (string) ($data['title'] ?? '');
        $this->slug = isset($data['slug']) ? (string) $data['slug'] : null;
        $this->content = (string) ($data['content'] ?? '');
        $this->status = (string) ($data['status'] ?? 'open');
        $this->view_count = (int) ($data['view_count'] ?? 0);
        $this->user_id = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $this->module_id = isset($data['module_id']) ? (int) $data['module_id'] : null;
        $this->created_at = isset($data['created_at']) ? (string) $data['created_at'] : null;
        $this->updated_at = isset($data['updated_at']) ? (string) $data['updated_at'] : null;
        $this->deleted_at = isset($data['deleted_at']) ? (string) $data['deleted_at'] : null;
    }

    /** Exposes the persistence fields used by controllers and views. */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'status' => $this->status,
            'view_count' => $this->view_count,
            'user_id' => $this->user_id,
            'module_id' => $this->module_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
