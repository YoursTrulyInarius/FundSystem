<?php
// controllers/PublicController.php
require_once 'core/Database.php';

class PublicController {

    public static $barangay_list = [
        'Bagong Opon', 'Bambong Daku', 'Bambong Diut', 'Bobongan',
        'Campo IV', 'Campo V', 'Caniangan', 'Dipalusan',
        'Eastern Bobongan', 'Esperanza', 'Gapasan', 'Katipunan',
        'Kauswagan', 'Lower Sambulawan', 'Mabini', 'Magsaysay',
        'Malating', 'Paradise', 'Pasingkalan', 'Poblacion',
        'San Fernando', 'Santo Rosario', 'Sapa Anding', 'Sinaguing',
        'Switch', 'Upper Laperian', 'Wakat'
    ];

    public function index() {
        $database = new Database();
        $db = $database->connect();

        // 1. Fetch all ongoing and completed projects with barangay & owner info
        $query = "SELECT p.*, u.barangay_name, u.full_name AS project_owner 
                  FROM projects p 
                  LEFT JOIN users u ON p.user_id = u.id 
                  WHERE p.status IN ('ongoing', 'completed') 
                  ORDER BY p.created_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch project milestones
        $project_ids = array_column($projects, 'id');
        $milestones_by_project = [];
        if (!empty($project_ids)) {
            $inIds = implode(',', array_map('intval', $project_ids));
            $mStmt = $db->query("SELECT * FROM project_milestones WHERE project_id IN ($inIds) ORDER BY FIELD(stage, 'planning', 'authorization', 'implementation', 'monitoring')");
            while ($m = $mStmt->fetch(PDO::FETCH_ASSOC)) {
                $milestones_by_project[$m['project_id']][] = $m;
            }
        }

        // 2. Fetch ALL transactions for public transparency
        $txQuery = "SELECT t.*, p.title as project_title, u.barangay_name 
                    FROM transactions t 
                    LEFT JOIN projects p ON t.project_id = p.id 
                    LEFT JOIN users u ON p.user_id = u.id 
                    ORDER BY t.created_at DESC";
        $txStmt = $db->prepare($txQuery);
        $txStmt->execute();
        $transactions = $txStmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Fetch live summary statistics for the hero section
        $countQuery = "SELECT
                (SELECT COUNT(*) FROM projects WHERE status IN ('ongoing', 'completed')) AS total_projects,
                (SELECT COUNT(*) FROM transactions WHERE status = 'recorded') AS recorded_transactions";
        $countStmt = $db->prepare($countQuery);
        $countStmt->execute();
        $liveStats = $countStmt->fetch(PDO::FETCH_ASSOC);
        $total_projects = (int) ($liveStats['total_projects'] ?? 0);
        $recorded_transactions = (int) ($liveStats['recorded_transactions'] ?? 0);
        $registeredBarangayStmt = $db->query("SELECT COUNT(DISTINCT TRIM(REPLACE(barangay_name, 'Barangay ', ''))) FROM users WHERE role = 'sk_admin' AND barangay_name IS NOT NULL AND barangay_name != ''");
        $barangay_count = (int) $registeredBarangayStmt->fetchColumn();

        // 4. Fetch barangay profiles (uploaded cover images, taglines)
        $bpStmt = $db->query("SELECT * FROM barangay_profiles");
        $profiles_by_bname = [];
        while ($bp = $bpStmt->fetch(PDO::FETCH_ASSOC)) {
            $bClean = str_replace('Barangay ', '', $bp['barangay_name']);
            $profiles_by_bname[$bClean] = $bp;
        }

        // 5. Fetch SK Officials by barangay
        $skStmt = $db->query("SELECT id, full_name, email, barangay_name FROM users WHERE role = 'sk_admin'");
        $officials_by_bname = [];
        while ($sk = $skStmt->fetch(PDO::FETCH_ASSOC)) {
            $bClean = str_replace('Barangay ', '', $sk['barangay_name']);
            $officials_by_bname[$bClean] = $sk;
        }

        // 6. Fetch comments / feedback grouped by barangay
        $fbStmt = $db->query("SELECT f.*, COALESCE(f.barangay_name, u.barangay_name) as effective_barangay 
                             FROM feedback f 
                             LEFT JOIN projects p ON f.project_id = p.id 
                             LEFT JOIN users u ON p.user_id = u.id 
                             ORDER BY f.created_at DESC");
        $feedback_by_bname = [];
        while ($fb = $fbStmt->fetch(PDO::FETCH_ASSOC)) {
            $eff = str_replace('Barangay ', '', $fb['effective_barangay'] ?? '');
            if ($eff !== '') {
                $feedback_by_bname[$eff][] = $fb;
            }
        }

        // 7. Group projects and financial stats by barangay
        $projects_by_bname = [];
        $budget_by_bname = [];
        $recorded_by_bname = [];

        foreach ($projects as $p) {
            $bClean = str_replace('Barangay ', '', $p['barangay_name'] ?? '');
            if ($bClean !== '') {
                $p['milestones'] = $milestones_by_project[$p['id']] ?? [];
                $projects_by_bname[$bClean][] = $p;
                $budget_by_bname[$bClean] = ($budget_by_bname[$bClean] ?? 0) + (float)$p['budget'];
            }
        }

        foreach ($transactions as $t) {
            if ($t['status'] === 'recorded') {
                $bClean = str_replace('Barangay ', '', $t['barangay_name'] ?? '');
                if ($bClean !== '') {
                    $recorded_by_bname[$bClean] = ($recorded_by_bname[$bClean] ?? 0) + (float)$t['amount'];
                }
            }
        }

        // 8. Build comprehensive dataset for all 27 barangays
        $barangayCards = [];
        foreach (self::$barangay_list as $bname) {
            $bOfficial = $officials_by_bname[$bname] ?? null;
            if (!$bOfficial) {
                continue;
            }

            $bProjects = $projects_by_bname[$bname] ?? [];
            $bProfile  = $profiles_by_bname[$bname] ?? null;
            $bFeedback = $feedback_by_bname[$bname] ?? [];

            $totBudget = $budget_by_bname[$bname] ?? 0;
            $recAmount = $recorded_by_bname[$bname] ?? 0;
            $utilRate  = $totBudget > 0 ? round(($recAmount / $totBudget) * 100, 1) : 0;

            $customBenefits = json_decode($bProfile['community_benefits'] ?? '', true);
            $benefits = $this->generateBenefits(is_array($customBenefits) ? $customBenefits : []);

            $barangayCards[] = [
                'name' => $bname,
                'display_name' => 'Barangay ' . $bname,
                'image_path' => !empty($bProfile['image_path']) ? $bProfile['image_path'] : null,
                'tagline' => $bProfile['tagline'] ?? 'Dedicated to youth development & transparent community service.',
                'description' => $bProfile['description'] ?? '',
                'chairperson' => $bOfficial['full_name'] ?? 'SK Chairperson',
                'chairperson_email' => $bOfficial['email'] ?? null,
                'project_count' => count($bProjects),
                'total_budget' => $totBudget,
                'recorded_amount' => $recAmount,
                'utilization_rate' => $utilRate,
                'projects' => $bProjects,
                'comments' => $bFeedback,
                'comments_count' => count($bFeedback),
                'benefits' => $benefits
            ];
        }

        require 'views/public/home.php';
    }

    public function barangay() {
        $raw_name = trim($_GET['name'] ?? '');
        if (empty($raw_name)) {
            header('Location: /FundSystem/');
            exit;
        }

        $clean_name = str_replace('Barangay ', '', $raw_name);

        $database = new Database();
        $db = $database->connect();

        // 1. Fetch Profile
        $pStmt = $db->prepare("SELECT * FROM barangay_profiles WHERE barangay_name = :name LIMIT 1");
        $pStmt->execute([':name' => $clean_name]);
        $profile = $pStmt->fetch(PDO::FETCH_ASSOC);

        // 2. Fetch SK Chairperson
        $uStmt = $db->prepare("SELECT id, full_name, email, barangay_name FROM users WHERE role = 'sk_admin' AND (barangay_name = :name OR barangay_name = :prefixed) LIMIT 1");
        $uStmt->execute([
            ':name' => $clean_name,
            ':prefixed' => 'Barangay ' . $clean_name
        ]);
        $sk_official = $uStmt->fetch(PDO::FETCH_ASSOC);

        // 3. Fetch all projects
        $projStmt = $db->prepare("SELECT p.*, u.full_name as owner_name FROM projects p 
                                  JOIN users u ON p.user_id = u.id 
                                  WHERE u.barangay_name = :name OR u.barangay_name = :prefixed 
                                  ORDER BY p.created_at DESC");
        $projStmt->execute([
            ':name' => $clean_name,
            ':prefixed' => 'Barangay ' . $clean_name
        ]);
        $projects = $projStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch milestones
        $project_ids = array_column($projects, 'id');
        $milestones_by_project = [];
        if (!empty($project_ids)) {
            $inIds = implode(',', array_map('intval', $project_ids));
            $mStmt = $db->query("SELECT * FROM project_milestones WHERE project_id IN ($inIds) ORDER BY FIELD(stage, 'planning', 'authorization', 'implementation', 'monitoring')");
            while ($m = $mStmt->fetch(PDO::FETCH_ASSOC)) {
                $milestones_by_project[$m['project_id']][] = $m;
            }
        }
        foreach ($projects as &$p) {
            $p['milestones'] = $milestones_by_project[$p['id']] ?? [];
        }
        unset($p);

        // 4. Fetch all transactions
        $transactions = [];
        if (!empty($project_ids)) {
            $inIds = implode(',', array_map('intval', $project_ids));
            $tStmt = $db->query("SELECT t.*, p.title as project_title FROM transactions t JOIN projects p ON t.project_id = p.id WHERE t.project_id IN ($inIds) ORDER BY t.created_at DESC");
            $transactions = $tStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // 5. Fetch feedback / comments
        $fSql = "SELECT * FROM feedback WHERE (barangay_name = :name OR barangay_name = :prefixed";
        if (!empty($project_ids)) {
            $fSql .= " OR project_id IN (" . implode(',', array_map('intval', $project_ids)) . ")";
        }
        $fSql .= ") ORDER BY created_at DESC";
        $fStmt = $db->prepare($fSql);
        $fStmt->execute([
            ':name' => $clean_name,
            ':prefixed' => 'Barangay ' . $clean_name
        ]);
        $comments = $fStmt->fetchAll(PDO::FETCH_ASSOC);

        // 6. Aggregate stats
        $total_budget = array_sum(array_column($projects, 'budget'));
        $recorded_amount = 0;
        foreach ($transactions as $t) {
            if ($t['status'] === 'recorded') {
                $recorded_amount += $t['amount'];
            }
        }
        $utilization_rate = $total_budget > 0 ? round(($recorded_amount / $total_budget) * 100, 1) : 0;
        $customBenefits = json_decode($profile['community_benefits'] ?? '', true);
        $benefits = $this->generateBenefits(is_array($customBenefits) ? $customBenefits : []);

        require 'views/public/barangay_view.php';
    }

    public function submitFeedback() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $project_id    = isset($_POST['project_id']) && (int)$_POST['project_id'] > 0 ? (int)$_POST['project_id'] : null;
        $barangay_name = trim($_POST['barangay_name'] ?? '');
        $user_name     = trim($_POST['user_name'] ?? '');
        $contact_info  = trim($_POST['contact_info'] ?? '');
        $message       = trim($_POST['message'] ?? '');

        if (!$project_id && empty($barangay_name)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please select a barangay or project to leave feedback.']);
            return;
        }

        if (empty($message)) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Please enter your message or comment.']);
            return;
        }

        if ($user_name === '') {
            $user_name = 'Anonymous Citizen';
        }

        if (!empty($barangay_name)) {
            $barangay_name = str_replace('Barangay ', '', $barangay_name);
        }

        $database = new Database();
        $db = $database->connect();

        $insert = "INSERT INTO feedback (project_id, barangay_name, user_name, contact_info, message) 
                   VALUES (:project_id, :barangay_name, :user_name, :contact_info, :message)";
        $stmt = $db->prepare($insert);
        $stmt->bindParam(':project_id', $project_id, $project_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindParam(':barangay_name', $barangay_name, $barangay_name ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':user_name', $user_name);
        $stmt->bindParam(':contact_info', $contact_info);
        $stmt->bindParam(':message', $message);
        $success = $stmt->execute();
        $newId = $db->lastInsertId();

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Thank you! Your feedback has been submitted successfully.',
                'feedback' => [
                    'id' => $newId,
                    'user_name' => htmlspecialchars($user_name),
                    'contact_info' => htmlspecialchars($contact_info),
                    'message' => htmlspecialchars($message),
                    'created_at' => date('M d, Y \a\t h:i A')
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Unable to submit feedback at this time. Please try again.']);
        }
    }

    public function updateProfile() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access. Only SK Administrators can update photos.']);
            return;
        }

        $database = new Database();
        $db = $database->connect();

        $uStmt = $db->prepare("SELECT barangay_name FROM users WHERE id = :id");
        $uStmt->execute([':id' => $_SESSION['user_id']]);
        $user = $uStmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || empty($user['barangay_name'])) {
            echo json_encode(['success' => false, 'message' => 'No barangay assigned to your account.']);
            return;
        }

        $clean_barangay = str_replace('Barangay ', '', $user['barangay_name']);
        $tagline = trim($_POST['tagline'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $hasCommunityBenefits = array_key_exists('community_benefits', $_POST);
        $communityBenefits = $hasCommunityBenefits ? $_POST['community_benefits'] : [];
        if ($hasCommunityBenefits && !is_array($communityBenefits)) {
            echo json_encode(['success' => false, 'message' => 'Invalid community benefits data.']);
            return;
        }
        $benefitKeys = ['education', 'sports', 'environment', 'livelihood'];
        $communityBenefits = $hasCommunityBenefits
            ? array_intersect_key($communityBenefits, array_flip($benefitKeys))
            : [];
        foreach ($communityBenefits as $key => $benefit) {
            if (!is_string($benefit)) {
                echo json_encode(['success' => false, 'message' => 'Each community benefit must be plain text.']);
                return;
            }
            $communityBenefits[$key] = trim($benefit);
            if (strlen($communityBenefits[$key]) > 500) {
                echo json_encode(['success' => false, 'message' => 'Each community benefit must be 500 characters or fewer.']);
                return;
            }
        }
        $communityBenefitsJson = $hasCommunityBenefits ? json_encode($communityBenefits) : null;

        $image_path = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file format. Please upload JPG, PNG, or WebP.']);
                return;
            }

            if ($file['size'] > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => 'File too large. Maximum size is 5MB.']);
                return;
            }

            $uploadDir = 'uploads/barangays/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = 'brgy_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($clean_barangay)) . '_' . time() . '.' . $ext;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $image_path = $targetPath;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save uploaded photo.']);
                return;
            }
        }

        // Upsert into barangay_profiles
        $checkStmt = $db->prepare("SELECT id, image_path, tagline, description, community_benefits FROM barangay_profiles WHERE barangay_name = :bname");
        $checkStmt->execute([':bname' => $clean_barangay]);
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $finalImage = $image_path ?? $existing['image_path'];
            $finalTagline = !empty($tagline) ? $tagline : ($existing['tagline'] ?? null);
            $finalDesc = !empty($description) ? $description : ($existing['description'] ?? null);
            $finalCommunityBenefits = $hasCommunityBenefits
                ? $communityBenefitsJson
                : ($existing['community_benefits'] ?? null);

            $upStmt = $db->prepare("UPDATE barangay_profiles SET image_path = :img, tagline = :tag, description = :desc, community_benefits = :benefits WHERE id = :id");
            $upStmt->execute([
                ':img' => $finalImage,
                ':tag' => $finalTagline,
                ':desc' => $finalDesc,
                ':benefits' => $finalCommunityBenefits,
                ':id' => $existing['id']
            ]);
        } else {
            $insStmt = $db->prepare("INSERT INTO barangay_profiles (barangay_name, image_path, tagline, description, community_benefits) VALUES (:bname, :img, :tag, :desc, :benefits)");
            $insStmt->execute([
                ':bname' => $clean_barangay,
                ':img' => $image_path,
                ':tag' => $tagline,
                ':desc' => $description,
                ':benefits' => $communityBenefitsJson
            ]);
        }

        echo json_encode([
            'success' => true,
            'message' => 'Barangay cover photo and profile details updated successfully!',
            'image_path' => $image_path ?? ($existing['image_path'] ?? null)
        ]);
    }

    public function project() {
        $project_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($project_id <= 0) {
            header('Location: /FundSystem/');
            exit;
        }

        $database = new Database();
        $db = $database->connect();

        $query = "SELECT p.*, u.barangay_name, u.full_name AS project_owner
                  FROM projects p
                  LEFT JOIN users u ON p.user_id = u.id
                  WHERE p.id = :id AND p.status IN ('ongoing', 'completed')";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $project_id, PDO::PARAM_INT);
        $stmt->execute();
        $project = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$project) {
            header('Location: /FundSystem/');
            exit;
        }

        $txQuery = "SELECT t.*, p.title AS project_title
                    FROM transactions t
                    LEFT JOIN projects p ON t.project_id = p.id
                    WHERE t.project_id = :id
                    ORDER BY t.created_at DESC";
        $txStmt = $db->prepare($txQuery);
        $txStmt->bindParam(':id', $project_id, PDO::PARAM_INT);
        $txStmt->execute();
        $transactions = $txStmt->fetchAll(PDO::FETCH_ASSOC);

        $milestoneQuery = $db->prepare("SELECT * FROM project_milestones WHERE project_id = :id ORDER BY FIELD(stage, 'planning', 'authorization', 'implementation', 'monitoring')");
        $milestoneQuery->bindParam(':id', $project_id, PDO::PARAM_INT);
        $milestoneQuery->execute();
        $milestones = $milestoneQuery->fetchAll(PDO::FETCH_ASSOC);

        require 'views/public/project_view.php';
    }

    private function generateBenefits($customBenefits = []) {
        $benefitDefinitions = [
            [
                'key' => 'education',
                'icon' => 'scholar',
                'title' => 'Education & Youth Capability',
            ],
            [
                'key' => 'sports',
                'icon' => 'sports',
                'title' => 'Sports & Physical Wellness',
            ],
            [
                'key' => 'environment',
                'icon' => 'eco',
                'title' => 'Environmental Protection',
            ],
            [
                'key' => 'livelihood',
                'icon' => 'livelihood',
                'title' => 'Livelihood & Empowerment',
            ]
        ];

        $benefits = [];
        foreach ($benefitDefinitions as $definition) {
            $value = $customBenefits[$definition['key']] ?? '';
            $description = is_string($value) ? trim($value) : '';
            if ($description !== '') {
                $benefits[] = [
                    'icon' => $definition['icon'],
                    'title' => $definition['title'],
                    'description' => $description,
                    'active' => true
                ];
            }
        }

        return $benefits;
    }
}
