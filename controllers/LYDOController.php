<?php
// controllers/LYDOController.php
require_once 'core/Database.php';

class LYDOController {
    public function review_transaction() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lydo') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
                return;
            }

            $rawTransactionId = $_POST['transaction_id'] ?? null;
            $transaction_id = is_scalar($rawTransactionId) ? filter_var($rawTransactionId, FILTER_VALIDATE_INT) : false;
            $action = isset($_POST['action']) && is_string($_POST['action']) ? $_POST['action'] : '';
            $rawRemarks = $_POST['remarks'] ?? '';
            $remarks = is_string($rawRemarks) ? trim($rawRemarks) : '';

            if (!$transaction_id || !in_array($action, ['approve', 'return'], true)) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
                return;
            }

            if ($action === 'return' && ($remarks === '' || strlen($remarks) > 2000)) {
                echo json_encode(['success' => false, 'message' => 'A correction note of 1 to 2000 characters is required.']);
                return;
            }

            $reviewData = null;
            if ($action === 'approve') {
                $textLimits = [
                    'reference_document_no' => 100,
                    'project_activity' => 255
                ];
                $reviewData = [];
                foreach ($textLimits as $field => $maxLength) {
                    $value = $_POST[$field] ?? null;
                    if (!is_string($value) || trim($value) === '' || strlen(trim($value)) > $maxLength) {
                        echo json_encode(['success' => false, 'message' => 'Enter all certificate text fields within their allowed lengths.']);
                        return;
                    }
                    $reviewData[$field] = trim($value);
                }
                $reviewData['issue_place'] = 'Ramon Magsaysay, Zamboanga del Sur';

                $rawAmount = $_POST['amount'] ?? null;
                if (!is_string($rawAmount) || !preg_match('/^\d{1,13}(?:\.\d{1,2})?$/D', $rawAmount) || (float) $rawAmount <= 0) {
                    echo json_encode(['success' => false, 'message' => 'Enter a valid positive transaction amount with up to two decimal places.']);
                    return;
                }
                $reviewData['amount'] = number_format((float) $rawAmount, 2, '.', '');

                $dateFields = ['date_of_transaction', 'date_reviewed', 'issue_date'];
                foreach ($dateFields as $field) {
                    $value = $_POST[$field] ?? null;
                    $date = is_string($value) ? DateTime::createFromFormat('!Y-m-d', $value) : false;
                    $dateErrors = DateTime::getLastErrors();
                    if (
                        !$date ||
                        $date->format('Y-m-d') !== $value ||
                        (is_array($dateErrors) && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))
                    ) {
                        echo json_encode(['success' => false, 'message' => 'Enter valid transaction, review, and issue dates.']);
                        return;
                    }
                    $reviewData[$field] = $value;
                }
                if (
                    $reviewData['date_of_transaction'] > $reviewData['date_reviewed'] ||
                    $reviewData['date_reviewed'] > $reviewData['issue_date']
                ) {
                    echo json_encode(['success' => false, 'message' => 'The transaction date must not be after the review date, and the review date must not be after the issue date.']);
                    return;
                }

                $reviewData['review_status'] = isset($_POST['review_status']) && is_string($_POST['review_status'])
                    ? $_POST['review_status']
                    : '';
                if (!in_array($reviewData['review_status'], ['complete', 'minor_clarification', 'incomplete'], true)) {
                    echo json_encode(['success' => false, 'message' => 'Select one review-completeness status.']);
                    return;
                }
                if ($reviewData['review_status'] === 'incomplete' && ($remarks === '' || strlen($remarks) > 2000)) {
                    echo json_encode(['success' => false, 'message' => 'A correction note is required for incomplete documents.']);
                    return;
                }
            }

            $database = new Database();
            $db = $database->connect();

            try {
                $db->beginTransaction();

                $pendingStmt = $db->prepare("SELECT t.project_id, sk.barangay_name
                    FROM transactions t
                    JOIN projects p ON t.project_id = p.id
                    JOIN users sk ON p.user_id = sk.id
                    WHERE t.id = :id AND t.status = 'pending'
                    LIMIT 1 FOR UPDATE");
                $pendingStmt->execute([':id' => $transaction_id]);
                $pendingTransaction = $pendingStmt->fetch(PDO::FETCH_ASSOC);
                if (!$pendingTransaction) {
                    $db->rollBack();
                    echo json_encode(['success' => false, 'message' => 'Transaction not found or no longer pending LYDO review.']);
                    return;
                }

                if ($reviewData !== null) {
                    $barangayName = trim((string) ($pendingTransaction['barangay_name'] ?? ''));
                    if ($barangayName === '' || strlen($barangayName) > 100) {
                        throw new Exception('The SK Council account must have a valid barangay name before issuing a certificate.');
                    }
                    $reviewData['review_barangay_name'] = $barangayName;
                }

                $new_status = ($action === 'return' || $reviewData['review_status'] === 'incomplete')
                    ? 'returned'
                    : 'reviewed';
                if ($new_status === 'returned') {
                    $stmt = $db->prepare("UPDATE transactions SET status = :status, reviewed_by = :reviewed_by, remarks = :remarks WHERE id = :id AND status = 'pending'");
                    $stmt->execute([
                        ':status' => $new_status,
                        ':reviewed_by' => $_SESSION['user_id'],
                        ':remarks' => $remarks,
                        ':id' => $transaction_id
                    ]);
                } else {
                    $stmt = $db->prepare("UPDATE transactions SET status = :status, reviewed_by = :reviewed_by WHERE id = :id AND status = 'pending'");
                    $stmt->execute([
                        ':status' => $new_status,
                        ':reviewed_by' => $_SESSION['user_id'],
                        ':id' => $transaction_id
                    ]);
                }
                if ($stmt->rowCount() !== 1) {
                    throw new Exception('Transaction could not be updated; it may no longer be pending LYDO review.');
                }

                $certificateId = null;
                if ($reviewData !== null) {
                    $cert_query = "INSERT INTO certifications
                        (transaction_id, type, cert_date, issued_by, purpose, review_barangay_name,
                         reference_document_no, project_activity, issue_place, amount, date_of_transaction, date_reviewed, review_status)
                        VALUES
                        (:tx_id, 'review', :cert_date, :user_id, 'LYOFOST Certification of Completeness and Review',
                         :review_barangay_name, :reference_document_no, :project_activity, :issue_place, :amount,
                         :date_of_transaction, :date_reviewed, :review_status)";
                    $cert_stmt = $db->prepare($cert_query);
                    $cert_stmt->execute([
                        ':tx_id' => $transaction_id,
                        ':cert_date' => $reviewData['issue_date'],
                        ':user_id' => $_SESSION['user_id'],
                        ':review_barangay_name' => $reviewData['review_barangay_name'],
                        ':reference_document_no' => $reviewData['reference_document_no'],
                        ':project_activity' => $reviewData['project_activity'],
                        ':issue_place' => $reviewData['issue_place'],
                        ':amount' => $reviewData['amount'],
                        ':date_of_transaction' => $reviewData['date_of_transaction'],
                        ':date_reviewed' => $reviewData['date_reviewed'],
                        ':review_status' => $reviewData['review_status']
                    ]);
                    $certificateId = (int) $db->lastInsertId();
                }

                if ($new_status === 'reviewed') {
                    require_once 'core/MilestoneHelper.php';
                    MilestoneHelper::mark($db, $pendingTransaction['project_id'], 'lydo_approved', 'Transaction documents reviewed and certified by LYDO.');
                }

                $db->commit();

                if ($reviewData === null) {
                    $message = 'Transaction returned to the SK Council for correction.';
                } elseif ($new_status === 'returned') {
                    $message = 'Incomplete review certified and returned to the SK Council for additional documents.';
                } elseif ($reviewData['review_status'] === 'minor_clarification') {
                    $message = 'Review certified subject to minor clarification and forwarded to the SK Federation.';
                } else {
                    $message = 'Review certified as complete and forwarded to the SK Federation.';
                }

                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'certificate_id' => $certificateId,
                    'status' => $new_status
                ]);
            } catch (Exception $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            }

        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }

    public function review_mar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'lydo') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
                return;
            }

            $raw_report_id = $_POST['report_id'] ?? null;
            $report_id = is_scalar($raw_report_id) ? filter_var($raw_report_id, FILTER_VALIDATE_INT) : false;
            $action = $_POST['action'] ?? '';
            if ($action === 'return') {
                $action = 'reject';
            }
            $raw_remarks = $_POST['remarks'] ?? '';
            $remarks = is_string($raw_remarks) ? trim($raw_remarks) : null;

            if (!$report_id || !in_array($action, ['approve', 'reject'], true) || $remarks === null) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
                return;
            }
            if ($action === 'reject' && $remarks === '') {
                echo json_encode(['success' => false, 'message' => 'A reason is required when rejecting the MAR.']);
                return;
            }

            $database = new Database();
            $db = $database->connect();

            $documentsStmt = $db->prepare("SELECT form_data, session_minutes_path, attendance_records_path, post_activity_reports_path, financial_reports_path FROM reports WHERE id = :id AND status = 'pending' LIMIT 1");
            $documentsStmt->execute([':id' => $report_id]);
            $documents = $documentsStmt->fetch(PDO::FETCH_ASSOC);
            if (!$documents) {
                echo json_encode(['success' => false, 'message' => 'MAR not found or it is no longer pending review.']);
                return;
            }

            if ($action === 'approve') {
                $requiredDocuments = ['session_minutes_path', 'attendance_records_path', 'post_activity_reports_path', 'financial_reports_path'];
                $missingDocument = false;
                foreach ($requiredDocuments as $field) {
                    if (!isset($documents[$field]) || trim((string)$documents[$field]) === '') {
                        $missingDocument = true;
                        break;
                    }
                }
                if ($missingDocument) {
                    echo json_encode(['success' => false, 'message' => 'Cannot approve this MAR until all four required documents are uploaded.']);
                    return;
                }
            }

            $new_status = ($action === 'approve') ? 'reviewed' : 'returned';

            $formDataParam = '';
            $params = [
                ':status' => $new_status,
                ':remarks' => $action === 'reject' ? $remarks : null,
                ':id' => $report_id
            ];
            if ($action === 'approve') {
                $formData = json_decode($documents['form_data'] ?? '', true);
                if (is_array($formData) && array_key_exists('lydo_remarks', $formData)) {
                    unset($formData['lydo_remarks']);
                    $encodedFormData = json_encode($formData, JSON_UNESCAPED_UNICODE);
                    if ($encodedFormData === false) {
                        echo json_encode(['success' => false, 'message' => 'Unable to clear LYDO remarks while approving this MAR.']);
                        return;
                    }
                    $formDataParam = ', form_data = :form_data';
                    $params[':form_data'] = $encodedFormData;
                }
            }

            $query = "UPDATE reports SET status = :status, remarks = :remarks$formDataParam WHERE id = :id AND status = 'pending'";
            $stmt = $db->prepare($query);

            if ($stmt->execute($params) && $stmt->rowCount() === 1) {
                $message = $action === 'approve'
                    ? 'MAR approved and forwarded to SK Federation.'
                    : 'MAR rejected and returned to the SK Council with the rejection reason.';
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'message' => 'MAR could not be updated; it may no longer be pending review.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }

    private function normalizeLydoRemarkList($values, $rowCount) {
        if (!is_array($values) || count($values) > $rowCount) {
            return false;
        }

        $normalized = array_fill(0, $rowCount, '');
        foreach ($values as $index => $value) {
            if ((!is_int($index) && (!is_string($index) || !ctype_digit($index))) ||
                !is_string($value)) {
                return false;
            }
            $index = (int)$index;
            $value = trim($value);
            if ($index < 0 || $index >= $rowCount || preg_match('/\A.{0,1000}\z/us', $value) !== 1) {
                return false;
            }
            $normalized[$index] = $value;
        }

        return $normalized;
    }

    public function save_mar_remarks() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'lydo') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            return;
        }

        $rawReportId = $_POST['report_id'] ?? null;
        $reportId = is_scalar($rawReportId) ? filter_var($rawReportId, FILTER_VALIDATE_INT) : false;
        $rawRemarks = $_POST['remarks'] ?? null;
        $remarks = is_string($rawRemarks) ? json_decode($rawRemarks, true) : null;
        if (!$reportId || !is_array($remarks) ||
            array_diff(array_keys($remarks), ['activities', 'resolutions', 'attachments'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid LYDO remarks data.']);
            return;
        }

        $database = new Database();
        $db = $database->connect();
        $db->beginTransaction();
        $reportStmt = $db->prepare("SELECT form_data FROM reports WHERE id = :id AND status = 'pending' LIMIT 1 FOR UPDATE");
        $reportStmt->execute([':id' => $reportId]);
        $report = $reportStmt->fetch(PDO::FETCH_ASSOC);
        if (!$report) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'MAR not found or it is no longer pending LYDO review.']);
            return;
        }

        $formData = json_decode($report['form_data'] ?? '', true);
        if (!is_array($formData)) {
            $formData = [];
        }
        $activities = is_array($formData['activities'] ?? null) ? $formData['activities'] : [];
        $resolutions = is_array($formData['resolutions'] ?? null) ? $formData['resolutions'] : [];
        $activityRemarks = $this->normalizeLydoRemarkList($remarks['activities'] ?? [], count($activities));
        $resolutionRemarks = $this->normalizeLydoRemarkList($remarks['resolutions'] ?? [], count($resolutions));
        $attachmentKeys = ['session_minutes', 'attendance_records', 'post_activity_reports', 'financial_reports', 'other'];
        $attachmentRemarks = $remarks['attachments'] ?? [];
        if ($activityRemarks === false || $resolutionRemarks === false || !is_array($attachmentRemarks) ||
            array_diff(array_keys($attachmentRemarks), $attachmentKeys)) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Invalid LYDO remarks data.']);
            return;
        }

        $normalizedAttachmentRemarks = [];
        foreach ($attachmentKeys as $key) {
            $value = $attachmentRemarks[$key] ?? '';
            if (!is_string($value)) {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => 'Invalid LYDO remarks data.']);
                return;
            }
            $value = trim($value);
            if (preg_match('/\A.{0,1000}\z/us', $value) !== 1) {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => 'LYDO remarks must be 1,000 characters or fewer.']);
                return;
            }
            $normalizedAttachmentRemarks[$key] = $value;
        }

        $formData['lydo_remarks'] = [
            'activities' => $activityRemarks,
            'resolutions' => $resolutionRemarks,
            'attachments' => $normalizedAttachmentRemarks
        ];
        $encodedFormData = json_encode($formData, JSON_UNESCAPED_UNICODE);
        if ($encodedFormData === false) {
            $db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Unable to save LYDO remarks.']);
            return;
        }

        $updateStmt = $db->prepare("UPDATE reports SET form_data = :form_data WHERE id = :id AND status = 'pending'");
        $updateStmt->execute([':form_data' => $encodedFormData, ':id' => $reportId]);
        $db->commit();
        echo json_encode(['success' => true, 'message' => 'LYDO remarks saved.']);
    }
}
?>
