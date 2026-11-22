<?php
// models/Project.php
class Project {
    private $conn;
    private $table = 'projects';

    public $id;
    public $user_id;
    public $title;
    public $description;
    public $abyip_code;
    public $budget_category;
    public $budget;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all projects (optionally for a specific user)
    public function read($user_id = null) {
        $query = 'SELECT * FROM ' . $this->table;
        if ($user_id) {
            $query .= ' WHERE user_id = :user_id';
        }
        $query .= ' ORDER BY created_at DESC';

        $stmt = $this->conn->prepare($query);
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        $stmt->execute();
        return $stmt;
    }

    public function readWithFilters($barangay = null, $status = null) {
        $query = "SELECT p.*, u.barangay_name
                  FROM " . $this->table . " p
                  JOIN users u ON p.user_id = u.id
                  WHERE u.role = 'sk_admin'";
        $params = [];

        if ($barangay !== null && $barangay !== '') {
            $query .= " AND TRIM(REPLACE(u.barangay_name, 'Barangay ', '')) = :barangay";
            $params[':barangay'] = $barangay;
        }
        if ($status !== null && $status !== '') {
            $query .= " AND p.status = :status";
            $params[':status'] = $status;
        }

        $query .= ' ORDER BY p.created_at DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    // Create a new project
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' (user_id, title, description, abyip_code, budget_category, budget) VALUES (:user_id, :title, :description, :abyip_code, :budget_category, :budget)';
        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->abyip_code = htmlspecialchars(strip_tags($this->abyip_code));
        $this->budget_category = htmlspecialchars(strip_tags($this->budget_category));
        $this->budget = htmlspecialchars(strip_tags($this->budget));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':abyip_code', $this->abyip_code);
        $stmt->bindParam(':budget_category', $this->budget_category);
        $stmt->bindParam(':budget', $this->budget);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
