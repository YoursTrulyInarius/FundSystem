<?php
// Setup indexes for performance optimization
require_once 'core/Database.php';

$database = new Database();
$db = $database->connect();

$indexes = [
    // Users table
    "ALTER TABLE users ADD INDEX idx_username (username)",
    "ALTER TABLE users ADD INDEX idx_role (role)",
    
    // Projects table
    "ALTER TABLE projects ADD INDEX idx_user_id (user_id)",
    "ALTER TABLE projects ADD INDEX idx_status (status)",
    "ALTER TABLE projects ADD INDEX idx_created_at (created_at)",
    
    // Project Milestones
    "ALTER TABLE project_milestones ADD INDEX idx_project_id (project_id)",
    "ALTER TABLE project_milestones ADD INDEX idx_stage (stage)",
    
    // Transactions table
    "ALTER TABLE transactions ADD INDEX idx_project_id (project_id)",
    "ALTER TABLE transactions ADD INDEX idx_status (status)",
    "ALTER TABLE transactions ADD INDEX idx_type (type)",
    "ALTER TABLE transactions ADD INDEX idx_created_at (created_at)",
    "ALTER TABLE transactions ADD INDEX idx_reviewed_by (reviewed_by)",
    "ALTER TABLE transactions ADD INDEX idx_recorded_by (recorded_by)",
    
    // Reports table
    "ALTER TABLE reports ADD INDEX idx_user_id (user_id)",
    "ALTER TABLE reports ADD INDEX idx_year_month (year, month)",
    "ALTER TABLE reports ADD INDEX idx_status (status)",
    "ALTER TABLE reports ADD INDEX idx_submitted_at (submitted_at)",
    
    // Certifications table
    "ALTER TABLE certifications ADD INDEX idx_transaction_id (transaction_id)",
    "ALTER TABLE certifications ADD INDEX idx_issued_by (issued_by)",
    "ALTER TABLE certifications ADD INDEX idx_type (type)",
    
    // Feedback table
    "ALTER TABLE feedback ADD INDEX idx_project_id (project_id)",
    "ALTER TABLE feedback ADD INDEX idx_barangay_name (barangay_name)",
    "ALTER TABLE feedback ADD INDEX idx_read_at (read_at)",
    "ALTER TABLE feedback ADD INDEX idx_created_at (created_at)"
];

$success_count = 0;
$error_count = 0;

// Add structured MAR form data to reports created before the form redesign.
try {
    $columnStmt = $db->query("SHOW COLUMNS FROM reports LIKE 'form_data'");
    if (!$columnStmt->fetch(PDO::FETCH_ASSOC)) {
        $db->exec("ALTER TABLE reports ADD COLUMN form_data LONGTEXT NULL AFTER status");
        echo "✓ Added reports.form_data for the redesigned MAR form.<br>";
    }
} catch (PDOException $e) {
    $error_count++;
    echo "✗ Error adding reports.form_data: " . htmlspecialchars($e->getMessage()) . "<br>";
}

$certificationColumns = [
    'review_barangay_name' => 'VARCHAR(100) NULL',
    'reference_document_no' => 'VARCHAR(100) NULL',
    'project_activity' => 'VARCHAR(255) NULL',
    'issue_place' => 'VARCHAR(150) NULL',
    'date_of_transaction' => 'DATE NULL',
    'date_reviewed' => 'DATE NULL',
    'review_status' => 'VARCHAR(32) NULL',
    'recording_barangay_name' => 'VARCHAR(100) NULL',
    'registry_control_no' => 'VARCHAR(100) NULL',
    'entry_no' => 'VARCHAR(100) NULL',
    'date_recorded' => 'DATE NULL',
    'compliance_status' => 'VARCHAR(100) NULL'
];
foreach ($certificationColumns as $column => $definition) {
    try {
        $columnStmt = $db->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'certifications' AND COLUMN_NAME = :column");
        $columnStmt->execute([':column' => $column]);
        if ((int) $columnStmt->fetchColumn() === 0) {
            $db->exec("ALTER TABLE certifications ADD COLUMN `$column` $definition");
            echo "✓ Added certifications.$column.<br>";
        }
    } catch (PDOException $e) {
        $error_count++;
        echo "✗ Error adding certifications.$column: " . htmlspecialchars($e->getMessage()) . "<br>";
    }
}

foreach ($indexes as $sql) {
    try {
        $db->exec($sql);
        $success_count++;
        echo "✓ " . substr($sql, 0, 50) . "...<br>";
    } catch (PDOException $e) {
        $error_count++;
        echo "✗ Error: " . $e->getMessage() . "<br>";
    }
}

echo "<br><strong>Summary: $success_count indexes added successfully, $error_count errors</strong>";
?>
