<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class UserRepository {
    private $table = "users";
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    // READ All: Lấy danh sách tất cả User
    public function getAll() {
        $stmt = $this -> db -> query("SELECT * FROM $this->table");
        return $stmt -> fetchAll(PDO:: FETCH_ASSOC);
    }

    // READ: Tìm 1 User theo id truyền vào
    public function find($id) {
        $stmt = $this -> db -> prepare("SELECT * FROM $this->table WHERE id = :id");
        $stmt -> execute(['id' => $id]);
        $data = $stmt -> fetch(PDO:: FETCH_ASSOC);
        return $data ? new User($data) : null;
    }

    // CREATE: Lưu 1 Record User và Database và trả về kết quả thực thi
    public function create($data) {
        $sql = "INSERT INTO $this->table (full_name, username, email, password) VALUES (:full_name, :username,  :email, :password)";
        $stmt = $this->db->prepare($sql);
        return $stmt -> execute([
            'full_name' => $data['full_name'],
            'email' => $data['email']
        ]);
    }

    // UPDATE: Cập nhật dữ liệu User theo id và trả về kết quả thực thi
    public function update($id, $data) {
        $sql = "UPDATE $this->table SET full_name = :full_name, username = :username, email = :email WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt -> execute([
            'id' => $id,
            'full_name' => $data['full_name'],
            'email' => $data['email']
        ]);
    }

    // DELETE: Xóa 1 User theo id
    public function delete($id) {
        $sql = "DELETE FROM $this->table WHERE id = :id";
        $stmt = $this -> db -> prepare($sql);
        return $stmt -> execute(['id' => $id]);
    }
}
