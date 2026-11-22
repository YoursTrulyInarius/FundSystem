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

    private function isValidDate($value) {
        if ($value === '') {
            return true;
        }
        if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return false;
        }
        $parts = explode('-', $value);
        return checkdate((int)$parts[1], (int)$parts[2], (int)$parts[0]);
    }

    private function isValidMarText($value) {
        return is_string($value) && preg_match("~^[\\p{L}\\p{M}\\p{N}\\s.,;:'\"()/&+#%-]*$~u", $value) === 1;
    }

    private function normalizeFormData() {
        $sessions = $_POST['sessions'] ?? [];
        $activities = $_POST['activities'] ?? [];
        $resolutions = $_POST['resolutions'] ?? [];
        $signatories = $_POST['signatories'] ?? [];
        $otherDetails = $_POST['other_details'] ?? '';

        if (!is_array($sessions) || !is_array($activities) || !is_array($resolutions) ||
            !is_array($signatories) || !is_string($otherDetails)) {
            return false;
        }
        if (count($activities) > 25 || count($resolutions) > 25) {
            return false;
        }

        $normalizedSessions = [];
        foreach (['regular', 'special'] as $type) {
            $row = $sessions[$type] ?? [];
            if (!is_array($row)) {
                return false;
            }
            $normalizedSessions[$type] = [];
            foreach (['number', 'present_male', 'present_female', 'absent_male', 'absent_female'] as $field) {
                $raw = $row[$field] ?? '0';
                $value = filter_var($raw, FILTER_VALIDATE_INT);
                if ($value === false || $value < 0 || $value > 9999) {
                    return false;
                }
                $normalizedSessions[$type][$field] = $value;
            }
            $date = $row['date'] ?? '';
            if (!is_string($date) || !$this->isValidDate($date)) {
                return false;
            }
            if ($normalizedSessions[$type]['number'] > 0 && $date === '') {
                return false;
            }
            $normalizedSessions[$type]['date'] = $date;
        }

        $normalizedActivities = [];
        foreach ($activities as $row) {
            if (!is_array($row)) {
                return false;
            }
            foreach (['description', 'center', 'amount', 'started', 'completed'] as $field) {
                if (isset($row[$field]) && !is_string($row[$field])) {
                    return false;
                }
            }
            $description = trim($row['description'] ?? '');
            $center = trim($row['center'] ?? '');
            $amount = trim($row['amount'] ?? '');
            $started = $row['started'] ?? '';
            $completed = $row['completed'] ?? '';
            if ($description === '' && $center === '' && $amount === '' && $started === '' && $completed === '') {
                continue;
            }
            if ($description === '' || $center === '' || !$this->isValidMarText($description) ||
                !$this->isValidMarText($center) || !preg_match('/^\d{1,12}(\.\d{1,2})?$/', $amount) ||
                !$this->isValidDate($started) || !$this->isValidDate($completed) ||
                ($started !== '' && $completed !== '' && $completed < $started)) {
                return false;
            }
            $normalizedActivities[] = [
                'description' => substr($description, 0, 2000),
                'center' => substr($center, 0, 255),
                'amount' => $amount,
                'started' => $started,
                'completed' => $completed
            ];
        }

        $normalizedResolutions = [];
        foreach ($resolutions as $row) {
            if (!is_array($row)) {
                return false;
            }
            if (isset($row['description']) && !is_string($row['description'])) {
                return false;
            }
            $description = trim($row['description'] ?? '');
            if ($description !== '') {
                if (!$this->isValidMarText($description)) {
                    return false;
                }
                $normalizedResolutions[] = substr($description, 0, 2000);
            }
        }

        $normalizedSignatories = [];
        foreach (['secretary', 'treasurer', 'chairperson'] as $field) {
            if (isset($signatories[$field]) && !is_string($signatories[$field])) {
                return false;
            }
            $value = trim($signatories[$field] ?? '');
            if (!$this->isValidMarText($value)) {
                return false;
            }
            $normalizedSignatories[$field] = substr($value, 0, 150);
        }
        $kagawads = $signatories['kagawads'] ?? [];
        if (!is_array($kagawads) || count($kagawads) > 6) {
            return false;
        }
        $normalizedSignatories['kagawads'] = [];
        for ($i = 0; $i < 6; $i++) {
            if (isset($kagawads[$i]) && !is_string($kagawads[$i])) {
                return false;
            }
            $value = trim($kagawads[$i] ?? '');
            if (!$this->isValidMarText($value)) {
                return false;
            }
            $normalizedSignatories['kagawads'][] = substr($value, 0, 150);
        }
        $otherDetails = trim($otherDetails);
        if (!$this->isValidMarText($otherDetails)) {
            return false;
        }

        return [
            'sessions' => $normalizedSessions,
            'activities' => $normalizedActivities,
            'resolutions' => $normalizedResolutions,
            'signatories' => $normalizedSignatories,
            'other_details' => substr($otherDetails, 0, 1000)
        ];
    }

    private function saveUploadedDocument($field, &$newFiles) {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        $file = $_FILES[$field];
        if ($file['error'] !== UPLOAD_ERR_OK || !is_string($file['name']) || !is_string($file['tmp_name'])) {
            return false;
        }

        $extension = strtolower(pathinfo(basename($file['name']), PATHINFO_EXTENSION));
        if (!in_array($extension, ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'], true)) {
            return false;
        }
        $uploadDirectory = __DIR__ . '/../uploads/mars';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
            return false;
        }

        $filename = $field . '_' . bin2hex(random_bytes(16)) . '.' . $extension;
        $absolutePath = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;
        if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
            return false;
        }
        $relativePath = 'uploads/mars/' . $filename;
        $newFiles[$field] = $relativePath;
        return $relativePath;
    }

    private function normalizeStoredOtherImages($formData) {
        $images = is_array($formData) ? ($formData['other_images'] ?? []) : [];
        if (!is_array($images)) {
            return [];
        }

        return array_values(array_filter($images, function($path) {
            return is_string($path) && preg_match('~^uploads/mars/other_image_[a-f0-9]{32}\.(?:jpe?g|png|gif|webp)$~i', $path);
        }));
    }

    private function saveUploadedImages($field, &$newFiles) {
        if (!isset($_FILES[$field])) {
            return [];
        }

        $file = $_FILES[$field];
        foreach (['name', 'tmp_name', 'error'] as $key) {
            if (!isset($file[$key]) || !is_array($file[$key])) {
                return false;
            }
        }
        if (count($file['name']) > 5) {
            return false;
        }

        $allowedImageMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        $uploadDirectory = __DIR__ . '/../uploads/mars';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0755, true)) {
            return false;
        }

        $storedPaths = [];
        foreach ($file['name'] as $index => $originalName) {
            $error = $file['error'][$index] ?? UPLOAD_ERR_NO_FILE;
            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            if ($error !== UPLOAD_ERR_OK || !is_string($originalName) || !is_string($file['tmp_name'][$index] ?? null)) {
                return false;
            }

            $extension = strtolower(pathinfo(basename($originalName), PATHINFO_EXTENSION));
            $temporaryPath = $file['tmp_name'][$index];
            $imageInfo = @getimagesize($temporaryPath);
            if (!isset($allowedImageMimes[$extension]) || $imageInfo === false ||
                ($imageInfo['mime'] ?? '') !== $allowedImageMimes[$extension]) {
                return false;
            }

            $filename = 'other_image_' . bin2hex(random_bytes(16)) . '.' . $extension;
            $absolutePath = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;
            if (!move_uploaded_file($temporaryPath, $absolutePath)) {
                return false;
            }

            $relativePath = 'uploads/mars/' . $filename;
            $newFiles[] = $relativePath;
            $storedPaths[] = $relativePath;
        }

        return $storedPaths;
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
            $this->requireSkAdmin();

            $database = new Database();
            $db = $database->connect();
            $report = new Report($db);

            $report->user_id = $_SESSION['user_id'];
            $report->month = filter_var($_POST['month'] ?? null, FILTER_VALIDATE_INT);
            $report->year = filter_var($_POST['year'] ?? null, FILTER_VALIDATE_INT);
            $formData = $this->normalizeFormData();
            if (!$report->month || $report->month < 1 || $report->month > 12 ||
                !$report->year || $report->year < 1901 || $report->year > 2155 || $formData === false) {
                $this->respond(['success' => false, 'message' => 'Check the reporting period and remove invalid values or unsupported characters from the MAR form.']);
            }

            $newFiles = [];
            $otherImagePaths = $this->saveUploadedImages('other_images', $newFiles);
            if ($otherImagePaths === false) {
                foreach ($newFiles as $uploadedPath) {
                    $this->removeStoredDocument($uploadedPath);
                }
                $this->respond(['success' => false, 'message' => 'Upload up to five valid JPG, PNG, GIF, or WEBP images for OTHER.']);
            }
            $formData['other_images'] = $otherImagePaths;
            $report->form_data = json_encode($formData);
            $fileFields = [
                'session_minutes' => 'session_minutes_path',
                'attendance_records' => 'attendance_records_path',
                'post_activity_reports' => 'post_activity_reports_path',
                'financial_reports' => 'financial_reports_path'
            ];
            foreach ($fileFields as $field => $property) {
                $path = $this->saveUploadedDocument($field, $newFiles);
                if ($path === null || $path === false) {
                    foreach ($newFiles as $uploadedPath) {
                        $this->removeStoredDocument($uploadedPath);
                    }
                    $this->respond(['success' => false, 'message' => $path === false
                        ? "Invalid or failed upload for $field."
                        : "Missing required document: $field."]);
                }
                $report->$property = $path;
            }

            if ($report->create()) {
                $this->respond(['success' => true, 'message' => 'MAR submitted successfully.']);
            }
            foreach ($newFiles as $uploadedPath) {
                $this->removeStoredDocument($uploadedPath);
            }
            $this->respond(['success' => false, 'message' => 'Failed to record submission in database.']);
        }

        $this->respond(['success' => false, 'message' => 'Invalid request method.']);
    }

    public function resubmit_mar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['success' => false, 'message' => 'Invalid request method.']);
        }
        $this->requireSkAdmin();

        $rawReportId = $_POST['report_id'] ?? null;
        $reportId = is_scalar($rawReportId) ? filter_var($rawReportId, FILTER_VALIDATE_INT) : false;
        $month = filter_var($_POST['month'] ?? null, FILTER_VALIDATE_INT);
        $year = filter_var($_POST['year'] ?? null, FILTER_VALIDATE_INT);
        $formData = $this->normalizeFormData();
        if (!$reportId || !$month || $month < 1 || $month > 12 || !$year || $year < 1901 || $year > 2155 || $formData === false) {
            $this->respond(['success' => false, 'message' => 'Check the reporting period and remove invalid values or unsupported characters from the MAR form.']);
        }

        $database = new Database();
        $db = $database->connect();
        $report = $this->getOwnedReport($db, $reportId);
        if (!$report || $report['status'] !== 'returned') {
            $this->respond(['success' => false, 'message' => 'Only a returned MAR can be corrected and resubmitted.']);
        }

        $columns = [
            'session_minutes' => 'session_minutes_path',
            'attendance_records' => 'attendance_records_path',
            'post_activity_reports' => 'post_activity_reports_path',
            'financial_reports' => 'financial_reports_path'
        ];
        $paths = [];
        $newFiles = [];
        $previousFormData = json_decode($report['form_data'] ?? '', true);
        $existingOtherImages = $this->normalizeStoredOtherImages($previousFormData);
        $newOtherImages = $this->saveUploadedImages('other_images', $newFiles);
        if ($newOtherImages === false || count($existingOtherImages) + count($newOtherImages) > 5) {
            foreach ($newFiles as $path) {
                $this->removeStoredDocument($path);
            }
            $this->respond(['success' => false, 'message' => $newOtherImages === false
                ? 'Upload valid JPG, PNG, GIF, or WEBP images for OTHER.'
                : 'OTHER can include a maximum of five images in total.']);
        }
        $formData['other_images'] = array_merge($existingOtherImages, $newOtherImages);

        foreach ($columns as $field => $column) {
            $uploadedPath = $this->saveUploadedDocument($field, $newFiles);
            if ($uploadedPath === false) {
                foreach ($newFiles as $path) {
                    $this->removeStoredDocument($path);
                }
                $this->respond(['success' => false, 'message' => "Invalid or failed upload for $field."]);
            }
            $paths[$column] = $uploadedPath !== null ? $uploadedPath : $report[$column];
            if (empty($paths[$column])) {
                foreach ($newFiles as $path) {
                    $this->removeStoredDocument($path);
                }
                $this->respond(['success' => false, 'message' => "Upload the missing required document: $field."]);
            }
        }

        $stmt = $db->prepare("UPDATE reports
            SET month = :month, year = :year, form_data = :form_data,
                session_minutes_path = :session_minutes_path,
                attendance_records_path = :attendance_records_path,
                post_activity_reports_path = :post_activity_reports_path,
                financial_reports_path = :financial_reports_path,
                status = 'pending', remarks = NULL
            WHERE id = :id AND user_id = :user_id AND status = 'returned'");
        $updated = $stmt->execute([
            ':month' => $month,
            ':year' => $year,
            ':form_data' => json_encode($formData),
            ':session_minutes_path' => $paths['session_minutes_path'],
            ':attendance_records_path' => $paths['attendance_records_path'],
            ':post_activity_reports_path' => $paths['post_activity_reports_path'],
            ':financial_reports_path' => $paths['financial_reports_path'],
            ':id' => $reportId,
            ':user_id' => $_SESSION['user_id']
        ]);
        if (!$updated || $stmt->rowCount() !== 1) {
            foreach ($newFiles as $path) {
                $this->removeStoredDocument($path);
            }
            $this->respond(['success' => false, 'message' => 'MAR could not be resubmitted; it may no longer be returned.']);
        }

        foreach ($columns as $field => $column) {
            if (isset($newFiles[$field])) {
                $this->removeStoredDocument($report[$column]);
            }
        }
        $this->respond(['success' => true, 'message' => 'Corrected MAR resubmitted for LYDO review.']);
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
        if (!$report || $report['status'] !== 'returned') {
            $this->respond(['success' => false, 'message' => 'Only returned MAR documents can be edited.']);
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
        $stmt = $db->prepare("UPDATE reports SET $column = :path WHERE id = :id AND user_id = :user_id AND status = 'returned'");
        if (!$stmt->execute([
            ':path' => $relativePath,
            ':id' => $reportId,
            ':user_id' => $_SESSION['user_id']
        ]) || $stmt->rowCount() !== 1) {
            unlink($uploadDirectory . DIRECTORY_SEPARATOR . $filename);
            $this->respond(['success' => false, 'message' => 'Failed to update document.']);
        }

        $cleanupSucceeded = $this->removeStoredDocument($report[$column]);
        $this->respond([
            'success' => true,
            'message' => $cleanupSucceeded
                ? 'Document replaced. The MAR remains returned until you resubmit it.'
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
        if (!$report || $report['status'] !== 'returned') {
            $this->respond(['success' => false, 'message' => 'Only returned MAR documents can be edited.']);
        }

        $column = $this->documentFields[$field];
        if (empty($report[$column])) {
            $this->respond(['success' => false, 'message' => 'This document has already been removed.']);
        }

        $stmt = $db->prepare("UPDATE reports SET $column = NULL WHERE id = :id AND user_id = :user_id AND status = 'returned'");
        if (!$stmt->execute([
            ':id' => $reportId,
            ':user_id' => $_SESSION['user_id']
        ]) || $stmt->rowCount() !== 1) {
            $this->respond(['success' => false, 'message' => 'Failed to remove document.']);
        }

        $cleanupSucceeded = $this->removeStoredDocument($report[$column]);
        $this->respond([
            'success' => true,
            'message' => $cleanupSucceeded
                ? 'Document removed. The MAR remains returned until you resubmit it.'
                : 'Document reference removed, but the stored file could not be deleted.'
        ]);
    }
}
?>
