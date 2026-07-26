<?php
// models/Project.php
class Project {
    private $conn;
    private $table = 'projects';

    public $id;
    public $user_id;
    public $title;
    public $description;
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

    // Create a new project
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' (user_id, title, description, budget) VALUES (:user_id, :title, :description, :budget)';
        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->budget = htmlspecialchars(strip_tags($this->budget));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':budget', $this->budget);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
