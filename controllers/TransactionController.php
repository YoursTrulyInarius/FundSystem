<?php
// controllers/TransactionController.php
require_once 'core/Database.php';
require_once 'models/Transaction.php';

class TransactionController {
    public function submit_transaction() {
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
            $transaction = new Transaction($db);

            $transaction->project_id = $_POST['project_id'] ?? 0;
            $transaction->type = $_POST['type'] ?? '';
            $transaction->amount = $_POST['amount'] ?? 0;
            $transaction->reference_no = $_POST['reference_no'] ?? '';
            $transaction->remarks = $_POST['remarks'] ?? '';

            if (isset($_FILES['document']) && $_FILES['document']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = 'uploads/transactions/';
                $tmp_name = $_FILES['document']['tmp_name'];
                $name = basename($_FILES['document']['name']);
                $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                
                $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];
                if (!in_array($ext, $allowed)) {
                    echo json_encode(['success' => false, 'message' => "Invalid file format. Please upload PDF, Word, Excel, or Images."]);
                    return;
                }

                $new_name = 'tx_' . time() . '_' . rand(1000,9999) . '.' . $ext;
                $dest_path = $upload_dir . $new_name;

                if (move_uploaded_file($tmp_name, $dest_path)) {
                    $transaction->document_path = $dest_path;
                } else {
                    echo json_encode(['success' => false, 'message' => "Failed to upload document."]);
                    return;
                }
            } else {
                echo json_encode(['success' => false, 'message' => "Document is required."]);
                return;
            }

            if ($transaction->create()) {
                // Auto-mark Authorization milestone for this project
                require_once 'core/MilestoneHelper.php';
                MilestoneHelper::mark($db, $transaction->project_id, 'authorization', 'Transaction submitted — project authorized for disbursement.');

                echo json_encode(['success' => true, 'message' => 'Transaction submitted successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to record transaction in database.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }
}
?>
