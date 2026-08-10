<?php

namespace App\Models;

/**
 * Represents an application account and its public profile attributes.
 */
class User
{
    public ?int $id = null;
    public string $first_name = '';
    public string $last_name = '';
    public string $username = '';
    public string $email = '';
    public ?string $password = null;
    public ?string $avatar = null;
    public string $role = 'student';
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?string $deleted_at = null;

    /** Hydrates an account from a database row or form-compatible data. */
    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int)$data['id'] : null;
        $this->first_name = (string)($data['first_name'] ?? '');
        $this->last_name = (string)($data['last_name'] ?? '');
        $this->username = (string)($data['username'] ?? '');
        $this->email = (string)($data['email'] ?? '');
        $this->password = isset($data['password']) ? (string)$data['password'] : null;
        $this->avatar = isset($data['avatar']) ? (string)$data['avatar'] : null;
        $this->role = (string)($data['role'] ?? 'student');
        $this->created_at = isset($data['created_at']) ? (string)$data['created_at'] : null;
        $this->updated_at = isset($data['updated_at']) ? (string)$data['updated_at'] : null;
        $this->deleted_at = isset($data['deleted_at']) ? (string)$data['deleted_at'] : null;
    }

    /** Returns the session-safe account snapshot; the password hash is intentionally omitted. */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => trim($this->first_name . ' ' . $this->last_name),
            'username' => $this->username,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'role' => $this->role,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
        ];
    }
}
