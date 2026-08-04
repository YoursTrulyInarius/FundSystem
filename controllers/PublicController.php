<?php
// controllers/PublicController.php
require_once 'core/Database.php';

class PublicController {
    public function index() {
        $database = new Database();
        $db = $database->connect();

        // Fetch ongoing and completed projects
        $query = "SELECT p.*, u.barangay_name, u.full_name AS project_owner FROM projects p 
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

        // Fetch live summary statistics for the hero section
        $countQuery = "SELECT
                (SELECT COUNT(*) FROM projects WHERE status IN ('ongoing', 'completed')) AS total_projects,
                (SELECT COUNT(*) FROM transactions WHERE status = 'recorded') AS recorded_transactions,
                (SELECT COUNT(DISTINCT barangay_name) FROM (
                    SELECT u.barangay_name
                    FROM projects p
                    LEFT JOIN users u ON p.user_id = u.id
                    WHERE p.status IN ('ongoing', 'completed') AND u.barangay_name IS NOT NULL
                    UNION
                    SELECT u.barangay_name
                    FROM transactions t
                    LEFT JOIN projects p ON t.project_id = p.id
                    LEFT JOIN users u ON p.user_id = u.id
                    WHERE u.barangay_name IS NOT NULL
                ) AS unioned) AS barangay_count";
        $countStmt = $db->prepare($countQuery);
        $countStmt->execute();
        $liveStats = $countStmt->fetch(PDO::FETCH_ASSOC);
        $total_projects = (int) ($liveStats['total_projects'] ?? 0);
        $recorded_transactions = (int) ($liveStats['recorded_transactions'] ?? 0);
        $barangay_count = (int) ($liveStats['barangay_count'] ?? 0);

        require 'views/public/home.php';
    }

    public function project() {
        $project_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($project_id <= 0) {
            header('Location: /FundSystem/');
            exit;
        }

        $database = new Database();
        $db = $database->connect();

        $query = "SELECT p.*, u.barangay_name, u.full_name AS project_owner
                  FROM projects p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.id = :id AND p.status IN ('ongoing', 'completed')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $project_id, PDO::PARAM_INT);
        $stmt->execute();
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            header('Location: /FundSystem/');
            exit;
        }

        $txQuery = "SELECT t.*, p.title AS project_title
                    FROM transactions t
                    LEFT JOIN projects p ON t.project_id = p.id
                    WHERE t.project_id = :id
                    ORDER BY t.created_at DESC";
        $txStmt = $db->prepare($txQuery);
        $txStmt->bindParam(':id', $project_id, PDO::PARAM_INT);
        $txStmt->execute();
        $transactions = $txStmt->fetchAll(PDO::FETCH_ASSOC);

        $milestoneQuery = $db->prepare("SELECT * FROM project_milestones WHERE project_id = :id ORDER BY FIELD(stage, 'planning', 'authorization', 'implementation', 'monitoring')");
        $milestoneQuery->bindParam(':id', $project_id, PDO::PARAM_INT);
        $milestoneQuery->execute();
        $milestones = $milestoneQuery->fetchAll(PDO::FETCH_ASSOC);

        require 'views/public/project_view.php';
    }
}
?>
