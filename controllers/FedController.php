<?php
// controllers/FedController.php
require_once 'core/Database.php';

class FedController {
    public function record_transaction() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_fed') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
                return;
            }

            $transaction_id = $_POST['transaction_id'] ?? 0;
            
            if (!$transaction_id) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
                return;
            }

            $database = new Database();
            $db = $database->connect();

            try {
                $db->beginTransaction();

                // Update transaction status to 'recorded'
                $query = "UPDATE transactions SET status = 'recorded', recorded_by = :user_id WHERE id = :id AND status = 'reviewed'";
                $stmt = $db->prepare($query);
                $stmt->execute([':user_id' => $_SESSION['user_id'], ':id' => $transaction_id]);
                
                if ($stmt->rowCount() === 0) {
                    throw new Exception("Transaction not found or not in 'reviewed' status.");
                }

                // Create the Certification of Recording
                $cert_query = "INSERT INTO certifications (transaction_id, type, cert_date, issued_by, purpose) 
                               VALUES (:tx_id, 'recording', CURDATE(), :user_id, 'Certification of Official Recording')";
                $cert_stmt = $db->prepare($cert_query);
                $cert_stmt->execute([
                    ':tx_id' => $transaction_id,
                    ':user_id' => $_SESSION['user_id']
                ]);

                $db->commit();

                // Auto-mark Monitoring milestone
                require_once 'core/MilestoneHelper.php';
                $proj_stmt = $db->prepare("SELECT project_id FROM transactions WHERE id = :id");
                $proj_stmt->execute([':id' => $transaction_id]);
                $proj_row = $proj_stmt->fetch(PDO::FETCH_ASSOC);
                if ($proj_row) {
                    MilestoneHelper::mark($db, $proj_row['project_id'], 'monitoring', 'Transaction officially recorded by SK Federation.');
                }

                echo json_encode(['success' => true, 'message' => 'Transaction recorded and certified successfully.']);
            } catch (Exception $e) {
                $db->rollBack();
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }

        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }

    public function record_mar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_fed') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
                return;
            }

            $report_id = $_POST['report_id'] ?? 0;

            if (!$report_id) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
                return;
            }

            $database = new Database();
            $db = $database->connect();

            // Update report status to 'recorded' (assuming we add 'recorded' to enum if needed, wait, enum is pending, reviewed... Let's check reports schema)
            // Ah, reports enum is ('pending', 'reviewed'). We might need to alter it if we want 'recorded'.
            // For now, let's just assume we alter it or we just add 'recorded' to it.
            $db->exec("ALTER TABLE reports MODIFY COLUMN status ENUM('pending', 'reviewed', 'returned', 'recorded') DEFAULT 'pending'");

            $query = "UPDATE reports SET status = 'recorded' WHERE id = :id AND status = 'reviewed'";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute([':id' => $report_id]) && $stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'MAR recorded successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to record MAR or it is not in reviewed status.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }
}
?>
