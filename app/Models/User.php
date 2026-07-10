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

    public function getAllUsers()
    {
        $nameSelect = $this->nameSelectSql();
        $query = 'SELECT id, ' . $nameSelect . ' AS name, username, email, avatar, role, created_at
            FROM ' . $this->table . '
            ORDER BY id DESC';
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function usernameExists($username)
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM ' . $this->table . ' WHERE username = :username LIMIT 1'
        );
        $stmt->execute(['username' => $username]);

        return $stmt->fetch();
    }

    public function emailExists($email)
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM ' . $this->table . ' WHERE email = :email LIMIT 1'
        );
        $stmt->execute(['email' => $email]);

        return $stmt->fetch();
    }

    public function findByUsername($username)
    {
        $nameSelect = $this->nameSelectSql();
        $firstNameSelect = $this->firstNameSelectSql();
        $stmt = $this->db->prepare(
            'SELECT id, ' . $firstNameSelect . ' AS first_name, ' . $nameSelect . ' AS full_name,
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
        $firstNameSelect = $this->firstNameSelectSql();
        $stmt = $this->db->prepare(
            'SELECT id, ' . $firstNameSelect . ' AS first_name, ' . $nameSelect . ' AS full_name,
                username, email, password, avatar, role, created_at, updated_at
             FROM ' . $this->table . '
             WHERE id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function create($data)
    {
        $sql = 'INSERT INTO ' . $this->table . ' (first_name, last_name, username, email, password, avatar, role)
                VALUES (:first_name, :last_name, :username, :email, :password, :avatar, :role)';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'avatar' => $data['avatar'],
            'role' => $data['role'] ?? 'student',
        ]);
    }

    public function update($data, $id)
    {
        $sql = 'UPDATE ' . $this->table . '
            SET first_name = :first_name,
                last_name = :last_name,
                username = :username,
                email = :email,
                password = :password,
                avatar = :avatar,
                role = :role,
                updated_at = NOW()
            WHERE id = :id';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'avatar' => $data['avatar'],
            'role' => $data['role'] ?? 'student',
            'id' => $id,
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

    private function firstNameSelectSql()
    {
        return 'first_name';
    }
}
