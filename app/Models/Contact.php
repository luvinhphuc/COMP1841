<?php

namespace App\Models;

/**
 * Represents a contact request persisted for administrative follow-up.
 */
class Contact
{
    public ?int $id = null;
    public ?int $user_id = null;
    public string $name = '';
    public string $email = '';
    public string $subject = '';
    public string $message = '';
    public string $status = 'unread';
    public ?string $created_at = null;
    public ?string $updated_at = null;
    public ?int $account_id = null;
    public ?string $account_username = null;
    public ?string $account_full_name = null;
    public ?string $account_role = null;

    public function __construct(array $data = [])
    {
        $this->id = isset($data['id']) ? (int) $data['id'] : null;
        $this->user_id = isset($data['user_id']) ? (int) $data['user_id'] : null;
        $this->name = (string) ($data['name'] ?? '');
        $this->email = (string) ($data['email'] ?? '');
        $this->subject = (string) ($data['subject'] ?? '');
        $this->message = (string) ($data['message'] ?? '');
        $this->status = (string) ($data['status'] ?? 'unread');
        $this->created_at = isset($data['created_at']) ? (string) $data['created_at'] : null;
        $this->updated_at = isset($data['updated_at']) ? (string) $data['updated_at'] : null;
        $this->account_id = isset($data['account_id']) ? (int) $data['account_id'] : null;
        $this->account_username = isset($data['account_username'])
            ? (string) $data['account_username']
            : null;
        $this->account_full_name = isset($data['account_full_name'])
            ? (string) $data['account_full_name']
            : null;
        $this->account_role = isset($data['account_role']) ? (string) $data['account_role'] : null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'account_id' => $this->account_id,
            'account_username' => $this->account_username,
            'account_full_name' => $this->account_full_name,
            'account_role' => $this->account_role,
        ];
    }
}
