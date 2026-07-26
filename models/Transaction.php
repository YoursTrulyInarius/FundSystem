<?php
// models/Transaction.php
class Transaction {
    private $conn;
    private $table = 'transactions';

    public $id;
    public $project_id;
    public $type;
    public $amount;
    public $reference_no;
    public $status;
    public $document_path;
    public $remarks;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readByProject($project_id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE project_id = :project_id ORDER BY created_at DESC';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
            (project_id, type, amount, reference_no, document_path, remarks) 
            VALUES (:project_id, :type, :amount, :reference_no, :document_path, :remarks)';
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':project_id', $this->project_id);
        $stmt->bindParam(':type', $this->type);
        $stmt->bindParam(':amount', $this->amount);
        $stmt->bindParam(':reference_no', $this->reference_no);
        $stmt->bindParam(':document_path', $this->document_path);
        $stmt->bindParam(':remarks', $this->remarks);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
