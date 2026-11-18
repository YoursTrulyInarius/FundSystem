<?php
// controllers/CertificationController.php
require_once 'core/Database.php';

class CertificationController {
    public function view() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header('Location: login');
            exit;
        }

        $cert_id = $_GET['id'] ?? 0;

        if (!$cert_id) {
            echo "Invalid certificate ID.";
            exit;
        }

        $database = new Database();
        $db = $database->connect();

        // Fetch certification data with all related info
        $query = "SELECT 
                    c.*,
                    t.amount,
                    t.type as tx_type,
                    t.reference_no,
                    t.document_path,
                    t.created_at as tx_date,
                    p.title as project_title,
                    p.budget as project_budget,
                    sk.full_name as sk_name,
                    sk.id as sk_user_id,
                    sk.barangay_name,
                    issuer.full_name as issuer_name,
                    issuer.role as issuer_role
                FROM certifications c
                JOIN transactions t ON c.transaction_id = t.id
                JOIN projects p ON t.project_id = p.id
                JOIN users sk ON p.user_id = sk.id
                JOIN users issuer ON c.issued_by = issuer.id
                WHERE c.id = :id";

        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $cert_id]);
        $cert = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cert) {
            http_response_code(404);
            echo "Certificate not found.";
            exit;
        }

        $viewerRole = $_SESSION['role'] ?? '';
        $isProjectOwner = $viewerRole === 'sk_admin' && (int)$cert['sk_user_id'] === (int)$_SESSION['user_id'];
        $isReviewOfficer = in_array($viewerRole, ['lydo', 'sk_fed'], true);
        if (!$isProjectOwner && !$isReviewOfficer) {
            http_response_code(403);
            echo "You do not have permission to view this certificate.";
            exit;
        }

        require 'views/certifications/view.php';
    }
}
?>
