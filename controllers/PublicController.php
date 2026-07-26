<?php
// controllers/PublicController.php
require_once 'core/Database.php';

class PublicController {
    public function index() {
        $database = new Database();
        $db = $database->connect();

        // Fetch ongoing and completed projects
        $query = "SELECT p.*, u.barangay_name FROM projects p 
                  LEFT JOIN users u ON p.user_id = u.id 
                  WHERE p.status IN ('ongoing', 'completed') 
                  ORDER BY p.created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch ALL transactions for public transparency
        $txQuery = "SELECT t.*, p.title as project_title, u.barangay_name 
                    FROM transactions t 
                    LEFT JOIN projects p ON t.project_id = p.id 
                    LEFT JOIN users u ON p.user_id = u.id 
                    ORDER BY t.created_at DESC";
        $txStmt = $db->prepare($txQuery);
        $txStmt->execute();
        $transactions = $txStmt->fetchAll(PDO::FETCH_ASSOC);

        require 'views/public/home.php';
    }
}
?>
