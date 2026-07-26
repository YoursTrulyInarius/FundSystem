<?php
// core/MilestoneHelper.php
// A shared helper to auto-mark project milestones at key workflow events.
require_once 'core/Database.php';

class MilestoneHelper {
    /**
     * Auto-mark a milestone stage as achieved for a given project.
     * Uses INSERT IGNORE so it won't error or duplicate if already marked.
     */
    public static function mark($db, $project_id, $stage, $description = '') {
        $stmt = $db->prepare(
            "INSERT IGNORE INTO project_milestones (project_id, stage, description, date_achieved)
             VALUES (:pid, :stage, :desc, CURDATE())"
        );
        $stmt->execute([
            ':pid'   => $project_id,
            ':stage' => $stage,
            ':desc'  => $description
        ]);

        // Auto-update project overall status based on milestone progress
        $new_status = null;
        if ($stage === 'implementation') {
            $new_status = 'ongoing';
        } elseif ($stage === 'monitoring') {
            $new_status = 'completed';
        }

        if ($new_status) {
            $status_stmt = $db->prepare("UPDATE projects SET status = :status WHERE id = :id");
            $status_stmt->execute([':status' => $new_status, ':id' => $project_id]);
        }
    }
}
?>
