<?php
// models/Report.php
class Report {
    private $conn;
    private $table = 'reports';

    public $id;
    public $user_id;
    public $month;
    public $year;
    public $status;
    public $session_minutes_path;
    public $attendance_records_path;
    public $post_activity_reports_path;
    public $financial_reports_path;
    public $submitted_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all reports (optionally by user)
    public function read($user_id = null) {
        $query = 'SELECT r.*, u.barangay_name FROM ' . $this->table . ' r 
                  LEFT JOIN users u ON r.user_id = u.id';
        if ($user_id) {
            $query .= ' WHERE r.user_id = :user_id';
        }
        $query .= ' ORDER BY r.submitted_at DESC';

        $stmt = $this->conn->prepare($query);
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        $stmt->execute();
        return $stmt;
    }

    // Create a new MAR report entry
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
            (user_id, month, year, session_minutes_path, attendance_records_path, post_activity_reports_path, financial_reports_path) 
            VALUES (:user_id, :month, :year, :sm_path, :ar_path, :par_path, :fr_path)';
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':month', $this->month);
        $stmt->bindParam(':year', $this->year);
        $stmt->bindParam(':sm_path', $this->session_minutes_path);
        $stmt->bindParam(':ar_path', $this->attendance_records_path);
        $stmt->bindParam(':par_path', $this->post_activity_reports_path);
        $stmt->bindParam(':fr_path', $this->financial_reports_path);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
