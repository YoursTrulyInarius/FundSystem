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

                // If approved, create the Certification of Review Completeness
                if ($action === 'approve') {
                    $cert_query = "INSERT INTO certifications (transaction_id, type, cert_date, issued_by, purpose) 
                                   VALUES (:tx_id, 'review', CURDATE(), :user_id, 'Certification of Review Completeness')";
                    $cert_stmt = $db->prepare($cert_query);
                    $cert_stmt->execute([
                        ':tx_id' => $transaction_id,
                        ':user_id' => $_SESSION['user_id']
                    ]);
                }

                $db->commit();

                if ($action === 'approve') {
                    // Auto-mark Implementation milestone
                    require_once 'core/MilestoneHelper.php';
                    // Get project_id from the transaction
                    $proj_stmt = $db->prepare("SELECT project_id FROM transactions WHERE id = :id");
                    $proj_stmt->execute([':id' => $transaction_id]);
                    $proj_row = $proj_stmt->fetch(PDO::FETCH_ASSOC);
                    if ($proj_row) {
                        MilestoneHelper::mark($db, $proj_row['project_id'], 'implementation', 'Transaction reviewed and approved by LYDO.');
                    }
                }

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

            $report_id = $_POST['report_id'] ?? 0;
            $action = $_POST['action'] ?? ''; // 'approve' or 'return'

            $remarks = $_POST['remarks'] ?? '';

            if (!$report_id || !in_array($action, ['approve', 'return'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
                return;
            }

            $database = new Database();
            $db = $database->connect();

            $new_status = ($action === 'approve') ? 'reviewed' : 'returned';

            $query = "UPDATE reports SET status = :status, remarks = :remarks WHERE id = :id";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([':status' => $new_status, ':remarks' => $remarks, ':id' => $report_id])) {
                echo json_encode(['success' => true, 'message' => 'MAR ' . $new_status . ' successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update MAR status.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }
}
?>
