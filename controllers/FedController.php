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

            $rawTransactionId = $_POST['transaction_id'] ?? null;
            $transaction_id = is_scalar($rawTransactionId) ? filter_var($rawTransactionId, FILTER_VALIDATE_INT) : false;
            if (!$transaction_id) {
                echo json_encode(['success' => false, 'message' => 'Invalid transaction ID.']);
                return;
            }

            $textLimits = [
                'registry_control_no' => 100,
                'entry_no' => 100,
                'reference_document_no' => 100,
                'project_activity' => 255,
                'compliance_status' => 100
            ];
            $certificateData = [];
            foreach ($textLimits as $field => $maxLength) {
                $value = $_POST[$field] ?? null;
                if (!is_string($value) || trim($value) === '' || strlen(trim($value)) > $maxLength) {
                    echo json_encode(['success' => false, 'message' => 'Complete all certificate text fields within their allowed lengths.']);
                    return;
                }
                $certificateData[$field] = trim($value);
            }

            $rawAmount = $_POST['amount'] ?? null;
            if (!is_string($rawAmount) || !preg_match('/^\d{1,13}(?:\.\d{1,2})?$/D', $rawAmount) || (float) $rawAmount <= 0) {
                echo json_encode(['success' => false, 'message' => 'Enter a valid positive transaction amount with up to two decimal places.']);
                return;
            }
            $certificateData['amount'] = number_format((float) $rawAmount, 2, '.', '');

            foreach (['date_of_transaction', 'date_recorded', 'issue_date'] as $field) {
                $value = $_POST[$field] ?? null;
                $date = is_string($value) ? DateTime::createFromFormat('!Y-m-d', $value) : false;
                $dateErrors = DateTime::getLastErrors();
                if (
                    !$date ||
                    $date->format('Y-m-d') !== $value ||
                    (is_array($dateErrors) && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))
                ) {
                    echo json_encode(['success' => false, 'message' => 'Enter valid transaction, recording, and issue dates.']);
                    return;
                }
                $certificateData[$field] = $value;
            }
            if (
                $certificateData['date_of_transaction'] > $certificateData['date_recorded'] ||
                $certificateData['date_recorded'] > $certificateData['issue_date']
            ) {
                echo json_encode(['success' => false, 'message' => 'The transaction date must not be after the recording date, and the recording date must not be after the issue date.']);
                return;
            }

            $database = new Database();
            $db = $database->connect();

            try {
                $db->beginTransaction();

                $transactionQuery = $db->prepare("SELECT t.project_id, sk.barangay_name
                    FROM transactions t
                    JOIN projects p ON t.project_id = p.id
                    JOIN users sk ON p.user_id = sk.id
                    WHERE t.id = :id AND t.status = 'reviewed'
                    LIMIT 1 FOR UPDATE");
                $transactionQuery->execute([':id' => $transaction_id]);
                $transaction = $transactionQuery->fetch(PDO::FETCH_ASSOC);
                if (!$transaction || trim((string) ($transaction['barangay_name'] ?? '')) === '') {
                    throw new Exception('Transaction not found for recording or the SK Council account has no barangay name.');
                }

                // Update transaction status to 'recorded'
                $query = "UPDATE transactions SET status = 'recorded', recorded_by = :user_id WHERE id = :id AND status = 'reviewed'";
                $stmt = $db->prepare($query);
                $stmt->execute([':user_id' => $_SESSION['user_id'], ':id' => $transaction_id]);
                
                if ($stmt->rowCount() === 0) {
                    throw new Exception("Transaction not found or not in 'reviewed' status.");
                }

                $cert_query = "INSERT INTO certifications
                    (transaction_id, type, cert_date, issued_by, purpose, recording_barangay_name, registry_control_no, entry_no,
                     reference_document_no, project_activity, amount, date_of_transaction, date_recorded,
                     compliance_status, issue_place)
                    VALUES
                    (:tx_id, 'recording', :cert_date, :user_id, 'LYOFOST Certification of Recording',
                     :recording_barangay_name, :registry_control_no, :entry_no, :reference_document_no, :project_activity, :amount,
                     :date_of_transaction, :date_recorded, :compliance_status, 'Ramon Magsaysay, Zamboanga del Sur')";
                $cert_stmt = $db->prepare($cert_query);
                $cert_stmt->execute([
                    ':tx_id' => $transaction_id,
                    ':cert_date' => $certificateData['issue_date'],
                    ':user_id' => $_SESSION['user_id'],
                    ':recording_barangay_name' => trim($transaction['barangay_name']),
                    ':registry_control_no' => $certificateData['registry_control_no'],
                    ':entry_no' => $certificateData['entry_no'],
                    ':reference_document_no' => $certificateData['reference_document_no'],
                    ':project_activity' => $certificateData['project_activity'],
                    ':amount' => $certificateData['amount'],
                    ':date_of_transaction' => $certificateData['date_of_transaction'],
                    ':date_recorded' => $certificateData['date_recorded'],
                    ':compliance_status' => $certificateData['compliance_status']
                ]);
                $certificateId = (int) $db->lastInsertId();

                require_once 'core/MilestoneHelper.php';
                MilestoneHelper::mark($db, $transaction['project_id'], 'sk_fed_approved', 'Transaction recorded by the SK Federation.');
                MilestoneHelper::mark($db, $transaction['project_id'], 'final', 'Final transaction recording completed by the SK Federation.');

                $db->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Transaction recorded and the SK Federation certificate was issued.',
                    'certificate_id' => $certificateId
                ]);
            } catch (Exception $e) {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
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

            $raw_report_id = $_POST['report_id'] ?? null;
            $report_id = is_scalar($raw_report_id) ? filter_var($raw_report_id, FILTER_VALIDATE_INT) : false;
            $action = $_POST['action'] ?? 'approve';
            $raw_reason = $_POST['reason'] ?? '';
            $reason = is_string($raw_reason) ? trim($raw_reason) : null;

            if (!$report_id || !in_array($action, ['approve', 'reject'], true) || $reason === null) {
                echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
                return;
            }
            if ($action === 'reject' && $reason === '') {
                echo json_encode(['success' => false, 'message' => 'A reason is required when rejecting the MAR.']);
                return;
            }

            $database = new Database();
            $db = $database->connect();

            $query = $action === 'approve'
                ? "UPDATE reports SET status = 'recorded', remarks = NULL WHERE id = :id AND status = 'reviewed'"
                : "UPDATE reports SET status = 'returned', remarks = :reason WHERE id = :id AND status = 'reviewed'";
            $stmt = $db->prepare($query);

            $params = [':id' => $report_id];
            if ($action === 'reject') {
                $params[':reason'] = $reason;
            }
            if ($stmt->execute($params) && $stmt->rowCount() === 1) {
                echo json_encode(['success' => true, 'message' => $action === 'approve'
                    ? 'MAR approved successfully.'
                    : 'MAR rejected and returned to the SK Council with the rejection reason.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'MAR could not be updated; it may no longer be awaiting SK Federation approval.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }
}
?>
