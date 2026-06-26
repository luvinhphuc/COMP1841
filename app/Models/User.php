<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class User
{
    private $db;
    private $table = "users";

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAllUsers()
    {
        $nameColumn = $this->nameColumn();
        $query = "SELECT id, $nameColumn AS name, username, email, avatar, role, created_at FROM " . $this->table . " ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function usernameExists($username)
    {
        $stmt = $this->db->prepare("SELECT id FROM " . $this->table . " WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);

        return (bool) $stmt->fetch();
    }

    public function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT id FROM " . $this->table . " WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);

        return (bool) $stmt->fetch();
    }

    public function findByEmail($email): ?array
    {
        $nameColumn = $this->nameColumn();
        $stmt = $this->db->prepare(
            "SELECT id, $nameColumn AS full_name, username, email, password, avatar, role, created_at
             FROM " . $this->table . "
             WHERE email = :email
             LIMIT 1"
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function create($data)
    {
        $nameColumn = $this->nameColumn();
        $sql = "INSERT INTO " . $this->table . " ($nameColumn, username, email, password, avatar, role)
                VALUES (:full_name, :username, :email, :password, :avatar, :role)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => $data['password'],
            'avatar' => $data['avatar'],
            'role' => $data['role'] ?? 'student',
        ]);
    }

    private function nameColumn()
    {
        $stmt = $this->db->prepare("SHOW COLUMNS FROM " . $this->table . " LIKE 'full_name'");
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC) ? 'full_name' : 'name';
    }
}
