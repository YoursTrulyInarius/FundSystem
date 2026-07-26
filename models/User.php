<?php
// models/User.php
class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $password;
    public $role;
    public $barangay_name;
    public $full_name;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE username = :username LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        $row = $stmt->fetch();
        if ($row) {
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->role = $row['role'];
                $this->barangay_name = $row['barangay_name'];
                $this->full_name = $row['full_name'];
                return true;
            }
        }
        return false;
    }
}
?>
