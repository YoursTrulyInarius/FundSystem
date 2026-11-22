<?php
// controllers/CertificationController.php
require_once 'core/Database.php';

class CertificationController {
    public function view() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $isPublicRequest = ($_GET['public'] ?? '') === '1';
        if (!$isPublicRequest && !isset($_SESSION['user_id'])) {
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
                    c.amount as certificate_amount,
                    t.amount,
                    t.type as tx_type,
                    t.reference_no,
                    t.document_path,
                    t.created_at as tx_date,
                    t.status as transaction_status,
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

        if ($isPublicRequest) {
            $isPublicReviewCertificate = $cert['type'] === 'review'
                && in_array($cert['review_status'], ['complete', 'minor_clarification'], true)
                && in_array($cert['transaction_status'], ['reviewed', 'recorded'], true);
            $isPublicRecordingCertificate = $cert['type'] === 'recording'
                && $cert['transaction_status'] === 'recorded';
            if (!$isPublicReviewCertificate && !$isPublicRecordingCertificate) {
                http_response_code(404);
                echo "This certificate is not available in the public ledger.";
                exit;
            }
        } else {
            $viewerRole = $_SESSION['role'] ?? '';
            $isProjectOwner = $viewerRole === 'sk_admin' && (int)$cert['sk_user_id'] === (int)$_SESSION['user_id'];
            $isReviewOfficer = in_array($viewerRole, ['lydo', 'sk_fed'], true);
            if (!$isProjectOwner && !$isReviewOfficer) {
                http_response_code(403);
                echo "You do not have permission to view this certificate.";
                exit;
            }
        }

        if ($cert['type'] === 'recording' && !empty($cert['registry_control_no'])) {
            $pdf = $this->buildAnnex3Pdf($cert);
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="SK-Federation-Annex-3-' . (int) $cert['id'] . '.pdf"');
            header('Content-Length: ' . strlen($pdf));
            echo $pdf;
            return;
        }

        if (!empty($cert['review_status'])) {
            $pdf = $this->buildAnnex2Pdf($cert);
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="LYDO-Annex-2-' . (int) $cert['id'] . '.pdf"');
            header('Content-Length: ' . strlen($pdf));
            echo $pdf;
            return;
        }

        require 'views/certifications/view.php';
    }

    private function buildAnnex2Pdf(array $cert) {
        require_once dirname(__DIR__) . '/vendor/setasign/fpdf/fpdf.php';

        $assets = dirname(__DIR__) . '/assets/img/';
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetMargins(18, 18, 18);
        $pdf->SetAutoPageBreak(true, 30);
        $pdf->AddPage();

        $pdf->Image($assets . 'annex2-municipal-seal.png', 17, 12, 23, 23);
        $pdf->Image($assets . 'annex2-lydo-seal.png', 171, 8, 27, 28);
        $pdf->SetTextColor(25, 25, 25);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetXY(43, 14);
        $pdf->Cell(124, 5, 'MUNICIPALITY OF RAMON MAGSAYSAY', 0, 1, 'C');
        $pdf->SetX(43);
        $pdf->Cell(124, 4, 'Municipal Hall, Quezon St., Poblacion, Ramon Magsaysay', 0, 1, 'C');
        $pdf->SetX(43);
        $pdf->Cell(124, 4, 'Zamboanga del Sur, 7024 Philippines', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->SetX(43);
        $pdf->Cell(124, 8, 'LOCAL YOUTH DEVELOPMENT OFFICE', 0, 1, 'C');

        $pdf->SetY(43);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetX(92);
        $pdf->Cell(26, 7, 'Annex 2', 1, 1, 'C');
        $pdf->SetY(52);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'CERTIFICATION OF COMPLETENESS AND REVIEW', 0, 1, 'C');
        $pdf->Ln(7);

        $barangay = preg_replace('/^Barangay\s+/i', '', (string) ($cert['review_barangay_name'] ?? ''));
        $pdf->SetFont('Arial', '', 11);
        $pdf->Write(6, $this->pdfText('This is to certify that the financial transaction documents and supporting records submitted by the Sangguniang Kabataan of Barangay '));
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(120, 53, 15);
        $pdf->Write(6, $this->pdfText($barangay));
        $pdf->SetFont('Arial', '', 11);
        $pdf->SetTextColor(25, 25, 25);
        $pdf->Write(6, $this->pdfText(' have been reviewed by the Office of the Local Youth Development Officer pursuant to the requirements of the LYOFOST SK Fund Program.'));
        $pdf->Ln(6);
        $pdf->Ln(3);
        $pdf->Cell(0, 6, 'Upon review, it is hereby certified that the submitted documents are:', 0, 1, 'L');
        $pdf->Ln(1);

        $reviewOptions = [
            'complete' => 'Complete and in order',
            'minor_clarification' => 'Complete but subject to minor clarification',
            'incomplete' => 'Incomplete and requiring submission of additional documents'
        ];
        foreach ($reviewOptions as $status => $label) {
            $x = 24;
            $y = $pdf->GetY() + 1;
            $pdf->Rect($x, $y, 4, 4);
            if ($cert['review_status'] === $status) {
                $pdf->SetLineWidth(0.5);
                $pdf->Line($x + 0.8, $y + 2.1, $x + 1.7, $y + 3.1);
                $pdf->Line($x + 1.7, $y + 3.1, $x + 3.4, $y + 0.8);
                $pdf->SetLineWidth(0.2);
            }
            $pdf->SetXY(32, $y - 1);
            $pdf->MultiCell(155, 6, $this->pdfText($label), 0, 'L');
        }
        $pdf->Ln(2);
        $pdf->Cell(0, 6, 'The records reviewed pertain to the following:', 0, 1, 'L');
        $pdf->Ln(1);

        $amount = $cert['certificate_amount'] ?? $cert['amount'];
        $this->writeAnnex2PdfField($pdf, 'Reference Document No.:', $cert['reference_document_no'] ?? '');
        $this->writeAnnex2PdfField($pdf, 'Project/Program/Activity:', $cert['project_activity'] ?? '');
        $this->writeAnnex2PdfField($pdf, 'Amount Involved (Php):', number_format((float) $amount, 2));
        $this->writeAnnex2PdfField($pdf, 'Date of Transaction:', date('F d, Y', strtotime($cert['date_of_transaction'])));
        $this->writeAnnex2PdfField($pdf, 'Date Reviewed:', date('F d, Y', strtotime($cert['date_reviewed'])));

        $pdf->Ln(5);
        $statement = 'This certification is issued to attest that the submitted records have undergone document review and compliance verification for purposes of registration and monitoring under the LYOFOST SK Fund Program.';
        $pdf->MultiCell(0, 6, $this->pdfText($statement), 0, 'J');
        $issueDate = strtotime($cert['cert_date'] ?: $cert['created_at']);
        $issued = 'Issued this ' . date('jS', $issueDate) . ' day of ' . date('F', $issueDate) . ', ' .
            date('Y', $issueDate) . ' at Ramon Magsaysay, Zamboanga del Sur.';
        $pdf->Ln(4);
        $pdf->MultiCell(0, 6, $this->pdfText($issued), 0, 'L');

        $pdf->Ln(7);
        $pdf->Cell(0, 6, 'Signed:', 0, 1, 'L');
        $signatureY = $pdf->GetY() + 18;
        $pdf->Line(18, $signatureY, 102, $signatureY);
        $pdf->SetXY(18, $signatureY + 2);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(84, 5, $this->pdfText($cert['issuer_name']), 0, 1, 'C');
        $pdf->SetX(18);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(84, 5, 'LOCAL YOUTH DEVELOPMENT OFFICER', 0, 1, 'C');

        $pdf->SetAutoPageBreak(false);
        $this->writeAnnex2PdfFooter($pdf, $assets);
        return $pdf->Output('S');
    }

    private function buildAnnex3Pdf(array $cert) {
        require_once dirname(__DIR__) . '/vendor/setasign/fpdf/fpdf.php';

        $assets = dirname(__DIR__) . '/assets/img/';
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->SetMargins(18, 18, 18);
        $pdf->SetAutoPageBreak(true, 30);
        $pdf->AddPage();

        $pdf->Image($assets . 'annex2-municipal-seal.png', 17, 12, 23, 23);
        $pdf->Image($assets . 'annex2-lydo-seal.png', 171, 8, 27, 28);
        $pdf->SetTextColor(25, 25, 25);
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetXY(43, 14);
        $pdf->Cell(124, 5, 'MUNICIPALITY OF RAMON MAGSAYSAY', 0, 1, 'C');
        $pdf->SetX(43);
        $pdf->Cell(124, 4, 'Municipal Hall, Quezon St., Poblacion, Ramon Magsaysay', 0, 1, 'C');
        $pdf->SetX(43);
        $pdf->Cell(124, 4, 'Zamboanga del Sur, 7024 Philippines', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->SetX(43);
        $pdf->Cell(124, 8, 'LOCAL YOUTH DEVELOPMENT OFFICE', 0, 1, 'C');

        $pdf->SetY(43);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetX(92);
        $pdf->Cell(26, 7, 'Annex 3', 1, 1, 'C');
        $pdf->SetY(52);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, 'LIARGAONON YOUTH OFFICE FISCAL OVERSIGHT AND TRANSPARENCY (LYOFOST)', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 6, 'SK FUND PROGRAM', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 8, 'CERTIFICATION OF RECORDING', 0, 1, 'C');
        $pdf->Ln(5);

        $barangay = preg_replace('/^Barangay\s+/i', '', (string) ($cert['recording_barangay_name'] ?? $cert['barangay_name'] ?? ''));
        $pdf->SetFont('Arial', '', 10);
        $pdf->Write(6, $this->pdfText('This is to certify that the financial transaction record of the Sangguniang Kabataan of Barangay '));
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(120, 53, 15);
        $pdf->Write(6, $this->pdfText($barangay));
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(25, 25, 25);
        $pdf->Write(6, $this->pdfText(' has been duly entered and recorded in the Official SK Financial Transaction Registry maintained under the LYOFOST SK Fund Program.'));
        $pdf->Ln(7);
        $pdf->Cell(0, 6, 'The following record has been officially registered:', 0, 1, 'L');
        $pdf->Ln(1);

        $this->writeAnnex2PdfField($pdf, 'Registry Control No.:', $cert['registry_control_no'] ?? '');
        $this->writeAnnex2PdfField($pdf, 'Entry No.:', $cert['entry_no'] ?? '');
        $this->writeAnnex2PdfField($pdf, 'Reference Document No.:', $cert['reference_document_no'] ?? '');
        $this->writeAnnex2PdfField($pdf, 'Project/Program/Activity:', $cert['project_activity'] ?? '');
        $this->writeAnnex2PdfField($pdf, 'Amount Involved (Php):', number_format((float) ($cert['certificate_amount'] ?? $cert['amount']), 2));
        $this->writeAnnex2PdfField($pdf, 'Date of Transaction:', date('F d, Y', strtotime($cert['date_of_transaction'])));
        $this->writeAnnex2PdfField($pdf, 'Date Recorded:', date('F d, Y', strtotime($cert['date_recorded'])));
        $this->writeAnnex2PdfField($pdf, 'Compliance Status:', $cert['compliance_status'] ?? '');

        $pdf->Ln(3);
        $pdf->SetFont('Arial', '', 10);
        $disclaimer = 'This certification confirms only the recording of the transaction in the official registry and does not, by itself, constitute approval, audit clearance, or financial certification of the expenditure.';
        $pdf->MultiCell(0, 5, $this->pdfText($disclaimer), 0, 'J');
        $issueDate = strtotime($cert['cert_date'] ?: $cert['created_at']);
        $issued = 'Issued this ' . date('jS', $issueDate) . ' day of ' . date('F', $issueDate) . ', ' .
            date('Y', $issueDate) . ' at Ramon Magsaysay, Zamboanga del Sur.';
        $pdf->Ln(3);
        $pdf->MultiCell(0, 6, $this->pdfText($issued), 0, 'L');

        $pdf->Ln(6);
        $pdf->Cell(0, 6, 'Signed:', 0, 1, 'L');
        $signatureY = $pdf->GetY() + 16;
        $pdf->Line(18, $signatureY, 102, $signatureY);
        $pdf->SetXY(18, $signatureY + 2);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(84, 5, 'MUNICIPAL SK FEDERATION', 0, 1, 'C');

        $pdf->SetAutoPageBreak(false);
        $this->writeAnnex2PdfFooter($pdf, $assets);
        return $pdf->Output('S');
    }

    private function writeAnnex2PdfField($pdf, $label, $value) {
        $x = 18;
        $labelWidth = 49;
        $valueWidth = 125;
        $y = $pdf->GetY();

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetXY($x, $y);
        $pdf->Cell($labelWidth, 7, $this->pdfText($label), 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY($x + $labelWidth, $y);
        $pdf->MultiCell($valueWidth, 6, $this->pdfText((string) $value), 0, 'L');
        $endY = max($y + 7, $pdf->GetY());
        $pdf->Line($x + $labelWidth, $endY - 0.5, $x + $labelWidth + $valueWidth, $endY - 0.5);
        $pdf->SetY($endY + 1);
    }

    private function writeAnnex2PdfFooter($pdf, $assets) {
        $pdf->SetDrawColor(120, 120, 120);
        $pdf->Line(14, 270, 196, 270);
        $pdf->SetTextColor(55, 55, 55);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->SetXY(14, 273);
        $pdf->Cell(126, 4, $this->pdfText('Hapsay, Han-ay, Husay, Hamugaway nga Ramon Magsaysay'), 0, 1, 'C');
        $pdf->SetX(14);
        $pdf->Cell(126, 4, $this->pdfText('For the Filipino Youth, For the Liargaonon Youth'), 0, 1, 'C');
        $pdf->SetFont('Arial', '', 7);
        $pdf->SetX(14);
        $pdf->Cell(126, 4, 'www.ramonmagsaysay.gov.ph   |   skliargao@gmail.com   |   SK LYDO Ramon Magsaysay', 0, 0, 'C');
        $pdf->Image($assets . 'annex2-dpo-seal.jpeg', 144, 272, 11, 17);
        $pdf->Image($assets . 'annex2-registry-qr.png', 158, 273, 15, 15);
        $pdf->Image($assets . 'annex2-bagong-pilipinas.png', 176, 274, 17, 13);
    }

    private function pdfText($value) {
        $text = iconv('UTF-8', 'windows-1252//TRANSLIT', (string) $value);
        if ($text === false) {
            throw new RuntimeException('Certificate text could not be encoded for PDF output.');
        }
        return $text;
    }
}
?>
