<?php
// controllers/ReportController.php
require_once 'core/Database.php';
require_once 'models/Report.php';

class ReportController {
    public function submit_mar() {
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
            $report = new Report($db);

            $report->user_id = $_SESSION['user_id'];
            $report->month = (int)($_POST['month'] ?? date('n'));
            $report->year = (int)($_POST['year'] ?? date('Y'));

            // Handle 4 file uploads
            $upload_dir = 'uploads/mars/';
            $uploaded_paths = [];
            
            $file_fields = [
                'session_minutes' => 'session_minutes_path',
                'attendance_records' => 'attendance_records_path',
                'post_activity_reports' => 'post_activity_reports_path',
                'financial_reports' => 'financial_reports_path'
            ];

            foreach ($file_fields as $field => $model_prop) {
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES[$field]['tmp_name'];
                    $name = basename($_FILES[$field]['name']);
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    
                    // Allow pdf, doc, docx, jpg, png
                    $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                    if (!in_array($ext, $allowed)) {
                        echo json_encode(['success' => false, 'message' => "Invalid file format for $field."]);
                        return;
                    }

                    $new_name = $field . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                    $dest_path = $upload_dir . $new_name;

                    if (move_uploaded_file($tmp_name, $dest_path)) {
                        $report->$model_prop = $dest_path;
                    } else {
                        echo json_encode(['success' => false, 'message' => "Failed to upload $field."]);
                        return;
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => "Missing required file: $field."]);
                    return;
                }
            }

            if ($report->create()) {
                echo json_encode(['success' => true, 'message' => 'MAR submitted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to record submission in database.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }
}
?>
