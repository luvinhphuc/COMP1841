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
                role = :role
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

    private function nameSelectSql()
    {
        return "TRIM(CONCAT_WS(' ', first_name, last_name))";
    }

    private function firstNameSelectSql()
    {
        return 'first_name';
    }
}
