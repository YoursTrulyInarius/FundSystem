<?php
// controllers/ReportController.php
require_once 'core/Database.php';
require_once 'models/Report.php';

class ReportController {
    private function respond($payload) {
        if (ob_get_length() > 0) {
            ob_clean();
        }
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    public function submit_mar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        ob_start();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_admin') {
                $this->respond(['success' => false, 'message' => 'Unauthorized access.']);
            }

            $database = new Database();
            $db = $database->connect();
            $report = new Report($db);

            $report->user_id = $_SESSION['user_id'];
            $report->month = (int)($_POST['month'] ?? date('n'));
            $report->year = (int)($_POST['year'] ?? date('Y'));

            // Handle 4 file uploads
            $upload_dir = 'uploads/mars/';
            if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
                $this->respond(['success' => false, 'message' => 'Unable to create upload directory for MAR documents.']);
            }
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
                    
                    // Allow pdf, doc, docx, jpg, jpeg, png
                    $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                    if (!in_array($ext, $allowed, true)) {
                        $this->respond(['success' => false, 'message' => "Invalid file format for $field. Allowed: PDF, DOC/DOCX, JPG, JPEG, PNG."]);
                    }

                    $new_name = $field . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                    $dest_path = $upload_dir . $new_name;

                    if (move_uploaded_file($tmp_name, $dest_path)) {
                        $report->$model_prop = $dest_path;
                    } else {
                        $this->respond(['success' => false, 'message' => "Failed to upload $field."]);
                    }
                } else {
                    $this->respond(['success' => false, 'message' => "Missing required file: $field."]);
                }
            }

            if ($report->create()) {
                $this->respond(['success' => true, 'message' => 'MAR submitted successfully.']);
            }
            $this->respond(['success' => false, 'message' => 'Failed to record submission in database.']);
        }

        $this->respond(['success' => false, 'message' => 'Invalid request method.']);
    }
}
?>
