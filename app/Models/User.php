<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getPaginated(int $limit, int $offset)
    {
        $nameSelect = $this->nameSelectSql();
        $stmt = $this->db->prepare(
            'SELECT id, first_name, last_name, ' . $nameSelect . ' AS name,
                username, email, avatar, role, created_at
             FROM ' . $this->table . '
             ORDER BY id DESC
             LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAll()
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM ' . $this->table)->fetchColumn();
    }

    public function countAdmins()
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM {$this->table} WHERE role = 'admin'"
        )->fetchColumn();
    }

    public function normaliseAccountData(array $data)
    {
        return [
            'first_name' => trim((string) ($data['first_name'] ?? '')),
            'last_name' => trim((string) ($data['last_name'] ?? '')),
            'username' => trim((string) ($data['username'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'password' => (string) ($data['password'] ?? ''),
            'confirm_password' => (string) ($data['confirm_password'] ?? ''),
        ];
    }

    public function validateAccount(array $data, int $exceptUserId = 0)
    {
        $data = $this->normaliseAccountData($data);
        $errors = [];

        if ($data['first_name'] === '') {
            $errors['first_name'] = 'First name is required.';
        } elseif (mb_strlen($data['first_name']) > 50) {
            $errors['first_name'] = 'First name must be 50 characters or fewer.';
        }

        if ($data['last_name'] === '') {
            $errors['last_name'] = 'Last name is required.';
        } elseif (mb_strlen($data['last_name']) > 50) {
            $errors['last_name'] = 'Last name must be 50 characters or fewer.';
        }

        if ($data['username'] === '') {
            $errors['username'] = 'Username is required.';
        } elseif (mb_strlen($data['username']) > 75) {
            $errors['username'] = 'Username must be 75 characters or fewer.';
        } elseif (!preg_match('/^[A-Za-z0-9_.-]+$/', $data['username'])) {
            $errors['username'] = 'Use only letters, numbers, underscores, dots, or hyphens.';
        } elseif ($this->usernameExistsExceptUser($data['username'], $exceptUserId)) {
            $errors['username'] = 'Username is already in use.';
        }

        if ($data['email'] === '') {
            $errors['email'] = 'Email is required.';
        } elseif (mb_strlen($data['email']) > 150) {
            $errors['email'] = 'Email must be 150 characters or fewer.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif ($this->emailExistsExceptUser($data['email'], $exceptUserId)) {
            $errors['email'] = 'Email is already in use.';
        }

        return $errors;
    }

    public function validatePassword(
        string $password,
        string $confirmation,
        string $passwordField = 'password',
        string $confirmationField = 'confirm_password',
        string $passwordLabel = 'Password'
    ) {
        $errors = [];

        if ($password === '') {
            $errors[$passwordField] = $passwordLabel . ' is required.';
        } elseif (mb_strlen($password) < 8) {
            $errors[$passwordField] = $passwordLabel . ' must be at least 8 characters.';
        } elseif (mb_strlen($password) > 128) {
            $errors[$passwordField] = $passwordLabel . ' must be 128 characters or fewer.';
        }

        if ($confirmation === '') {
            $errors[$confirmationField] = 'Please confirm your password.';
        } elseif (mb_strlen($confirmation) > 128) {
            $errors[$confirmationField] = 'Confirm password must be 128 characters or fewer.';
        } elseif ($password !== $confirmation) {
            $errors[$confirmationField] = 'Passwords do not match.';
        }

        return $errors;
    }

    public function emailExistsExceptUser(string $email, int $userId)
    {
        $sql = 'SELECT id FROM ' . $this->table . ' WHERE email = :email';
        $params = ['email' => $email];

        if ($userId > 0) {
            $sql .= ' AND id <> :user_id';
            $params['user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);

        return (bool) $stmt->fetch();
    }

    public function usernameExistsExceptUser(string $username, int $userId)
    {
        $sql = 'SELECT id FROM ' . $this->table . ' WHERE username = :username';
        $params = ['username' => $username];

        if ($userId > 0) {
            $sql .= ' AND id <> :user_id';
            $params['user_id'] = $userId;
        }

        $stmt = $this->db->prepare($sql . ' LIMIT 1');
        $stmt->execute($params);

        return (bool) $stmt->fetch();
    }

    public function findByUsername($username)
    {
        $nameSelect = $this->nameSelectSql();
        $stmt = $this->db->prepare(
            'SELECT id, first_name, last_name, ' . $nameSelect . ' AS full_name,
                username, email, password, avatar, role, created_at
             FROM ' . $this->table . '
             WHERE username = :username
             LIMIT 1'
        );
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function findById(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        $nameSelect = $this->nameSelectSql();
        $stmt = $this->db->prepare(
            'SELECT id, first_name, last_name, ' . $nameSelect . ' AS full_name,
                username, email, password, avatar, role, created_at, updated_at
             FROM ' . $this->table . '
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function find(int $id)
    {
        return $this->findById($id);
    }

    public function updateProfile(int $userId, array $data)
    {
        $stmt = $this->db->prepare(
            'UPDATE ' . $this->table . '
             SET first_name = :first_name,
                 last_name = :last_name,
                 username = :username,
                 email = :email,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'id' => $userId,
        ]);
    }

    public function updateAvatar(int $userId, string $avatarPath)
    {
        $stmt = $this->db->prepare(
            'UPDATE ' . $this->table . '
             SET avatar = :avatar, updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'avatar' => $avatarPath,
            'id' => $userId,
        ]);
    }

    public function updatePassword(int $userId, string $passwordHash)
    {
        $stmt = $this->db->prepare(
            'UPDATE ' . $this->table . '
             SET password = :password, updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'password' => $passwordHash,
            'id' => $userId,
        ]);
    }

    public function create(array $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO ' . $this->table . '
                (first_name, last_name, username, email, password, avatar, role)
             VALUES (:first_name, :last_name, :username, :email, :password, :avatar, :role)'
        );

        return $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'avatar' => $data['avatar'] ?? null,
            'role' => $data['role'] ?? 'student',
        ]);
    }

    public function updateFromAdmin(int $id, array $data)
    {
        $stmt = $this->db->prepare(
            'UPDATE ' . $this->table . '
             SET first_name = :first_name,
                 last_name = :last_name,
                 username = :username,
                 email = :email,
                 role = :role,
                 updated_at = NOW()
             WHERE id = :id'
        );

        return $stmt->execute([
            'id' => $id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
        ]);
    }

    public function delete(int $id)
    {
        if ($id <= 0) {
            return false;
        }

        $stmt = $this->db->prepare(
            'DELETE FROM ' . $this->table . '
             WHERE id = :id
                AND NOT EXISTS (
                    SELECT 1 FROM posts WHERE user_id = :post_user_id
                )
                AND NOT EXISTS (
                    SELECT 1 FROM replies WHERE user_id = :reply_user_id
                )'
        );
        $stmt->execute([
            'id' => $id,
            'post_user_id' => $id,
            'reply_user_id' => $id,
        ]);

        return $stmt->rowCount() > 0;
    }

    private function nameSelectSql()
    {
        return "TRIM(CONCAT_WS(' ', first_name, last_name))";
    }
}
