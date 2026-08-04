<?php
// controllers/FeedbackController.php
require_once 'core/Database.php';

class FeedbackController {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /FundSystem/login');
            exit;
        }
        
        // Only SK Admin can view feedback
        if ($_SESSION['role'] !== 'sk_admin') {
            header('Location: /FundSystem/dashboard');
            exit;
        }

        $database = new Database();
        $db = $database->connect();

        // Fetch all feedback with project and user info
        $query = "SELECT 
                f.id,
                f.project_id,
                f.user_name,
                f.contact_info,
                f.message,
                f.created_at,
                p.title as project_title,
                p.budget,
                p.status,
                u.barangay_name,
                u.full_name as project_owner
            FROM feedback f
            LEFT JOIN projects p ON f.project_id = p.id
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.user_id = :user_id OR :user_id = (SELECT id FROM users WHERE role = 'sk_admin' AND user_id = :user_id LIMIT 1)
            ORDER BY f.created_at DESC";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
        $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark all unread feedback as read when admin visits this page
        $markReadQuery = "UPDATE feedback f
                         SET f.read_at = NOW()
                         WHERE f.read_at IS NULL
                         AND f.project_id IN (
                             SELECT id FROM projects WHERE user_id = :user_id
                         )";
        $markReadStmt = $db->prepare($markReadQuery);
        $markReadStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $markReadStmt->execute();

        // Count unread feedback (feedback with read_at = NULL)
        $countQuery = "SELECT COUNT(*) as unread_count FROM feedback f
                      LEFT JOIN projects p ON f.project_id = p.id
                      WHERE p.user_id = :user_id
                      AND f.read_at IS NULL";
        $countStmt = $db->prepare($countQuery);
        $countStmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $countStmt->execute();
        $unreadCount = $countStmt->fetch(PDO::FETCH_ASSOC)['unread_count'] ?? 0;

        $page_title = 'Community Feedback';
        ob_start();
        include 'views/dashboards/feedback.php';
        $content = ob_get_clean();
        require 'views/layout.php';
    }

    public function delete() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $feedback_id = isset($_POST['feedback_id']) ? (int) $_POST['feedback_id'] : 0;
        if ($feedback_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid feedback ID']);
            return;
        }

        $database = new Database();
        $db = $database->connect();

        // Verify ownership before deleting
        $query = "SELECT f.id FROM feedback f
                 LEFT JOIN projects p ON f.project_id = p.id
                 WHERE f.id = :feedback_id AND p.user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':feedback_id', $feedback_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            echo json_encode(['success' => false, 'message' => 'Feedback not found or unauthorized']);
            return;
        }

        $deleteQuery = "DELETE FROM feedback WHERE id = :feedback_id";
        $deleteStmt = $db->prepare($deleteQuery);
        $deleteStmt->bindParam(':feedback_id', $feedback_id, PDO::PARAM_INT);

        if ($deleteStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Feedback deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete feedback']);
        }
    }
}
?>
