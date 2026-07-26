<?php
// controllers/ProjectController.php
require_once 'core/Database.php';
require_once 'models/Project.php';

class ProjectController {
    public function create() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
                return;
            }

            $database = new Database();
            $db = $database->connect();
            $project = new Project($db);

            $project->user_id = $_SESSION['user_id'];
            $project->title = $_POST['title'] ?? '';
            $project->description = $_POST['description'] ?? '';
            $project->budget = $_POST['budget'] ?? 0;

            if (empty($project->title) || empty($project->budget)) {
                echo json_encode(['success' => false, 'message' => 'Title and budget are required.']);
                return;
            }

            if ($project->create()) {
                // Auto-mark Planning milestone
                require_once 'core/MilestoneHelper.php';
                $new_project_id = $db->lastInsertId();
                MilestoneHelper::mark($db, $new_project_id, 'planning', 'Project registered in system.');

                echo json_encode(['success' => true, 'message' => 'Project registered successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to register project.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }

    public function update_status() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
                return;
            }

            $project_id = $_POST['project_id'] ?? 0;
            $status = $_POST['status'] ?? '';
            $valid_statuses = ['planned', 'ongoing', 'completed'];

            if (!$project_id || !in_array($status, $valid_statuses)) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
                return;
            }

            $database = new Database();
            $db = $database->connect();

            // Verify project belongs to user
            $stmt = $db->prepare("UPDATE projects SET status = :status WHERE id = :id AND user_id = :user_id");
            if ($stmt->execute([
                ':status' => $status,
                ':id' => $project_id,
                ':user_id' => $_SESSION['user_id']
            ])) {
                echo json_encode(['success' => true, 'message' => 'Status updated.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }
}
?>
