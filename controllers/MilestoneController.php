<?php
// controllers/MilestoneController.php
require_once 'core/Database.php';

class MilestoneController {
    public function mark() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid method.']);
            return;
        }
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            return;
        }

        $project_id  = intval($_POST['project_id'] ?? 0);
        $stage       = $_POST['stage'] ?? '';
        $description = $_POST['description'] ?? '';
        $valid_stages = ['planning', 'authorization', 'implementation', 'monitoring'];

        if (!$project_id || !in_array($stage, $valid_stages)) {
            echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
            return;
        }

        $db = (new Database())->connect();

        // Verify this project belongs to the SK Admin
        $check = $db->prepare("SELECT id FROM projects WHERE id = :id AND user_id = :uid");
        $check->execute([':id' => $project_id, ':uid' => $_SESSION['user_id']]);
        if (!$check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Project not found.']);
            return;
        }

        // Insert milestone (ignore if already exists) and auto-update project status
        require_once 'core/MilestoneHelper.php';
        MilestoneHelper::mark($db, $project_id, $stage, $description);

        echo json_encode(['success' => true, 'message' => ucfirst($stage) . ' stage marked as completed!']);
    }
}
?>
