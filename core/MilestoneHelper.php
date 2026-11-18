<?php
// core/MilestoneHelper.php
// A shared helper to auto-mark project milestones at key workflow events.
require_once 'core/Database.php';

class MilestoneHelper {
    /**
     * Record an automatically achieved workflow milestone.
     */
    public static function mark($db, $project_id, $stage, $description = '') {
        $valid_stages = ['planning', 'lydo_approved', 'sk_fed_approved', 'final'];
        if (!in_array($stage, $valid_stages, true)) {
            throw new InvalidArgumentException('Invalid project milestone stage.');
        }

        $stmt = $db->prepare(
            "INSERT INTO project_milestones (project_id, stage, description, date_achieved)
             VALUES (:pid, :stage, :desc, CURDATE())
             ON DUPLICATE KEY UPDATE id = id"
        );
        $stmt->execute([
            ':pid'   => $project_id,
            ':stage' => $stage,
            ':desc'  => $description
        ]);

        // Auto-update project overall status based on milestone progress
        $new_status = null;
        if ($stage === 'lydo_approved') {
            $new_status = 'ongoing';
        } elseif ($stage === 'final') {
            $new_status = 'completed';
        }

        if ($new_status) {
            $status_stmt = $db->prepare("UPDATE projects SET status = :status WHERE id = :id");
            $status_stmt->execute([':status' => $new_status, ':id' => $project_id]);
        }
    }
}
?>
