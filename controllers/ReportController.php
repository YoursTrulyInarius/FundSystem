<?php
// controllers/ReportController.php
require_once 'core/Database.php';
require_once 'models/Report.php';

class ReportController {
    private $documentFields = [
        'session_minutes' => 'session_minutes_path',
        'attendance_records' => 'attendance_records_path',
        'post_activity_reports' => 'post_activity_reports_path',
        'financial_reports' => 'financial_reports_path'
    ];

    private function respond($payload) {
        if (ob_get_length() > 0) {
            ob_clean();
        }
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    private function requireSkAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'sk_admin') {
            $this->respond(['success' => false, 'message' => 'Unauthorized access.']);
        }
    }

    private function getOwnedReport($db, $reportId) {
        $stmt = $db->prepare('SELECT * FROM reports WHERE id = :id AND user_id = :user_id LIMIT 1');
        $stmt->execute([
            ':id' => $reportId,
            ':user_id' => $_SESSION['user_id']
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function removeStoredDocument($relativePath) {
        if (!$relativePath || strpos($relativePath, 'uploads/mars/') !== 0) {
            return true;
        }

        $uploadDirectory = realpath(__DIR__ . '/../uploads/mars');
        $filePath = realpath(__DIR__ . '/../' . $relativePath);
        if ($filePath === false) {
            return true;
        }
        if ($uploadDirectory === false || strpos($filePath, $uploadDirectory . DIRECTORY_SEPARATOR) !== 0) {
            return false;
        }
        return unlink($filePath);
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

    public function update_document() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['success' => false, 'message' => 'Invalid request method.']);
        }
        $this->requireSkAdmin();

        $rawReportId = $_POST['report_id'] ?? null;
        $field = $_POST['field'] ?? '';
        $reportId = is_scalar($rawReportId) ? filter_var($rawReportId, FILTER_VALIDATE_INT) : false;
        if (!$reportId || !is_string($field) || !isset($this->documentFields[$field])) {
            $this->respond(['success' => false, 'message' => 'Invalid report or document.']);
        }
        $file = $_FILES['document'] ?? null;
        if (!is_array($file) || !isset($file['error'], $file['name'], $file['tmp_name']) ||
            !is_string($file['name']) || !is_string($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            $this->respond(['success' => false, 'message' => 'Choose a document to upload.']);
        }

        $extension = strtolower(pathinfo(basename($file['name']), PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        if (!in_array($extension, $allowedExtensions, true)) {
            $this->respond(['success' => false, 'message' => 'Invalid file format. Allowed: PDF, DOC/DOCX, JPG, JPEG, PNG.']);
        }

        $database = new Database();
        $db = $database->connect();
        $report = $this->getOwnedReport($db, $reportId);
        if (!$report) {
            $this->respond(['success' => false, 'message' => 'Report not found or you do not have permission to edit it.']);
        }

        $uploadDirectory = __DIR__ . '/../uploads/mars';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
            $this->respond(['success' => false, 'message' => 'Unable to create upload directory for MAR documents.']);
        }

        $filename = $field . '_' . bin2hex(random_bytes(16)) . '.' . $extension;
        $relativePath = 'uploads/mars/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $uploadDirectory . DIRECTORY_SEPARATOR . $filename)) {
            $this->respond(['success' => false, 'message' => 'Failed to upload document.']);
        }

        $column = $this->documentFields[$field];
        $stmt = $db->prepare("UPDATE reports SET $column = :path, status = 'pending', remarks = NULL WHERE id = :id AND user_id = :user_id");
        if (!$stmt->execute([
            ':path' => $relativePath,
            ':id' => $reportId,
            ':user_id' => $_SESSION['user_id']
        ])) {
            unlink($uploadDirectory . DIRECTORY_SEPARATOR . $filename);
            $this->respond(['success' => false, 'message' => 'Failed to update document.']);
        }

        $cleanupSucceeded = $this->removeStoredDocument($report[$column]);
        $this->respond([
            'success' => true,
            'message' => $cleanupSucceeded
                ? 'Document replaced. The MAR has been returned to pending review.'
                : 'Document replaced, but the previous file could not be removed.'
        ]);
    }

    public function delete_document() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['success' => false, 'message' => 'Invalid request method.']);
        }
        $this->requireSkAdmin();

        $rawReportId = $_POST['report_id'] ?? null;
        $field = $_POST['field'] ?? '';
        $reportId = is_scalar($rawReportId) ? filter_var($rawReportId, FILTER_VALIDATE_INT) : false;
        if (!$reportId || !is_string($field) || !isset($this->documentFields[$field])) {
            $this->respond(['success' => false, 'message' => 'Invalid report or document.']);
        }

        $database = new Database();
        $db = $database->connect();
        $report = $this->getOwnedReport($db, $reportId);
        if (!$report) {
            $this->respond(['success' => false, 'message' => 'Report not found or you do not have permission to edit it.']);
        }

        $column = $this->documentFields[$field];
        if (empty($report[$column])) {
            $this->respond(['success' => false, 'message' => 'This document has already been removed.']);
        }

        $stmt = $db->prepare("UPDATE reports SET $column = NULL, status = 'pending', remarks = NULL WHERE id = :id AND user_id = :user_id");
        if (!$stmt->execute([
            ':id' => $reportId,
            ':user_id' => $_SESSION['user_id']
        ])) {
            $this->respond(['success' => false, 'message' => 'Failed to remove document.']);
        }

        $cleanupSucceeded = $this->removeStoredDocument($report[$column]);
        $this->respond([
            'success' => true,
            'message' => $cleanupSucceeded
                ? 'Document removed. The MAR has been returned to pending review.'
                : 'Document reference removed, but the stored file could not be deleted.'
        ]);
    }
}
?>
