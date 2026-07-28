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

            $document_paths = [];
            if (isset($_FILES['document'])) {
                $file_array = $_FILES['document'];
                $is_multiple = is_array($file_array['name']);
                $file_count = $is_multiple ? count($file_array['name']) : 1;
                
                $upload_dir = 'uploads/transactions/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx'];
                $has_invalid_format = false;

                for ($i = 0; $i < $file_count; $i++) {
                    $error = $is_multiple ? $file_array['error'][$i] : $file_array['error'];
                    if ($error === UPLOAD_ERR_OK) {
                        $tmp_name = $is_multiple ? $file_array['tmp_name'][$i] : $file_array['tmp_name'];
                        $name = $is_multiple ? basename($file_array['name'][$i]) : basename($file_array['name']);
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        
                        if (!in_array($ext, $allowed)) {
                            $has_invalid_format = true;
                            continue;
                        }

                        $new_name = 'tx_' . time() . '_' . rand(1000,9999) . '_' . $i . '.' . $ext;
                        $dest_path = $upload_dir . $new_name;

                        if (move_uploaded_file($tmp_name, $dest_path)) {
                            $document_paths[] = $dest_path;
                        }
                    }
                }

                if ($has_invalid_format && empty($document_paths)) {
                    echo json_encode(['success' => false, 'message' => "Invalid file format. Please upload PDF, Word, Excel, or Images."]);
                    return;
                }
            }

            if (empty($document_paths)) {
                echo json_encode(['success' => false, 'message' => "At least one valid document is required."]);
                return;
            }

            $transaction->document_path = json_encode($document_paths);

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
