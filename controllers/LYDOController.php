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

            $transaction_id = $_POST['transaction_id'] ?? 0;
            $action = $_POST['action'] ?? ''; // 'approve' or 'return'
            $remarks = $_POST['remarks'] ?? '';

            if (!$transaction_id || !in_array($action, ['approve', 'return'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
                return;
            }

            $database = new Database();
            $db = $database->connect();

            try {
                $db->beginTransaction();

                $new_status = ($action === 'approve') ? 'reviewed' : 'returned';

                // Update transaction status
                $query = "UPDATE transactions SET status = :status, remarks = :remarks WHERE id = :id";
                $stmt = $db->prepare($query);
                $stmt->execute([':status' => $new_status, ':remarks' => $remarks, ':id' => $transaction_id]);

                // If approved, issue a certificate for the SK Admin.
                if ($action === 'approve') {
                    $cert_query = "INSERT INTO certifications (transaction_id, type, cert_date, issued_by, purpose) 
                                   VALUES (:tx_id, 'review', CURDATE(), :user_id, 'LYDO Approval and Authorization to Proceed')";
                    $cert_stmt = $db->prepare($cert_query);
                    $cert_stmt->execute([
                        ':tx_id' => $transaction_id,
                        ':user_id' => $_SESSION['user_id']
                    ]);

                    require_once 'core/MilestoneHelper.php';
                    $proj_stmt = $db->prepare("SELECT project_id FROM transactions WHERE id = :id");
                    $proj_stmt->execute([':id' => $transaction_id]);
                    $proj_row = $proj_stmt->fetch(PDO::FETCH_ASSOC);
                    if ($proj_row) {
                        MilestoneHelper::mark($db, $proj_row['project_id'], 'lydo_approved', 'Transaction approved by LYDO.');
                    }
                }

                $db->commit();

                echo json_encode(['success' => true, 'message' => 'Transaction ' . $new_status . ' successfully.']);
            } catch (Exception $e) {
                $db->rollBack();
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
            $raw_remarks = $_POST['remarks'] ?? '';
            $remarks = is_string($raw_remarks) ? trim($raw_remarks) : null;

            if (!$report_id || !in_array($action, ['approve', 'return'], true) || $remarks === null) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
                return;
            }
            if ($action === 'return' && $remarks === '') {
                echo json_encode(['success' => false, 'message' => 'Add correction remarks before returning the MAR.']);
                return;
            }

            $database = new Database();
            $db = $database->connect();

            $documentsStmt = $db->prepare("SELECT session_minutes_path, attendance_records_path, post_activity_reports_path, financial_reports_path FROM reports WHERE id = :id AND status = 'pending' LIMIT 1");
            $documentsStmt->execute([':id' => $report_id]);
            $documents = $documentsStmt->fetch(PDO::FETCH_ASSOC);
            if (!$documents) {
                echo json_encode(['success' => false, 'message' => 'MAR not found or it is no longer pending review.']);
                return;
            }

            if ($action === 'approve') {
                if (in_array('', array_map('strval', $documents), true) || in_array(null, $documents, true)) {
                    echo json_encode(['success' => false, 'message' => 'Cannot approve this MAR until all four required documents are uploaded.']);
                    return;
                }
            }

            $new_status = ($action === 'approve') ? 'reviewed' : 'returned';

            $query = "UPDATE reports SET status = :status, remarks = :remarks WHERE id = :id AND status = 'pending'";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([
                ':status' => $new_status,
                ':remarks' => $remarks !== '' ? $remarks : null,
                ':id' => $report_id
            ]) && $stmt->rowCount() === 1) {
                $message = $action === 'approve'
                    ? 'MAR approved and forwarded to SK Federation.'
                    : 'MAR rejected and returned to the SK Admin with correction remarks.';
                echo json_encode(['success' => true, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'message' => 'MAR could not be updated; it may no longer be pending review.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }
}
?>
