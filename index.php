<?php
// index.php
require_once 'includes/config.php';

// Simple Router
$request_uri = $_SERVER['REQUEST_URI'];
$parsed_url = parse_url($request_uri);
$path = $parsed_url['path'];

$script_name = dirname($_SERVER['SCRIPT_NAME']); // e.g., /FundSystem or \FundSystem
$script_name = str_replace('\\', '/', $script_name);
if ($script_name === '/') $script_name = '';
$base_path = $script_name === '' ? '/' : $script_name . '/';

if (strpos(strtolower($path), strtolower($script_name)) === 0) {
    $route = substr($path, strlen($script_name));
} else {
    $route = $path;
}

// Remove leading/trailing slashes
$route = trim($route, '/');

switch ($route) {
    case '':
        require 'controllers/PublicController.php';
        $publicCtrl = new PublicController();
        $publicCtrl->index();
        break;

    case 'public/project':
        require 'controllers/PublicController.php';
        $publicCtrl = new PublicController();
        $publicCtrl->project();
        break;

    case 'login':
        if (isset($_SESSION['user_id'])) {
            header("Location: " . $base_path . "dashboard");
            exit;
        }
        require 'views/login.php';
        break;

    case 'forgot-password':
        if (isset($_SESSION['user_id'])) {
            header("Location: " . $base_path . "dashboard");
            exit;
        }
        require 'views/forgot-password.php';
        break;

    case 'api/forgot-password':
        require 'controllers/AuthController.php';
        $auth = new AuthController();
        $auth->forgot_password();
        break;

    case 'verify-otp':
        if (isset($_SESSION['user_id'])) {
            header("Location: " . $base_path . "dashboard");
            exit;
        }
        require 'views/verify-otp.php';
        break;

    case 'api/verify-otp':
        require 'controllers/AuthController.php';
        $auth = new AuthController();
        $auth->verify_otp();
        break;

    case 'reset-password':
        if (isset($_SESSION['user_id'])) {
            header("Location: " . $base_path . "dashboard");
            exit;
        }
        require 'views/reset-password.php';
        break;

    case 'api/reset-password':
        require 'controllers/AuthController.php';
        $auth = new AuthController();
        $auth->reset_password();
        break;

    case 'api/login':
        require 'controllers/AuthController.php';
        $auth = new AuthController();
        $auth->login();
        break;

    case 'api/feedback':
        require 'controllers/PublicController.php';
        $publicCtrl = new PublicController();
        $publicCtrl->submitFeedback();
        break;
        
    case 'logout':
        require 'controllers/AuthController.php';
        $auth = new AuthController();
        $auth->logout();
        break;

    case 'register':
        if (isset($_SESSION['user_id'])) {
            header("Location: " . $base_path . "dashboard");
            exit;
        }
        require 'views/register.php';
        break;

    case 'api/register':
        require 'controllers/AuthController.php';
        $auth = new AuthController();
        $auth->register();
        break;

    case 'dashboard':
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . $base_path . "login");
            exit;
        }
        
        $role = $_SESSION['role'];
        if ($role === 'sk_admin') {
            require 'views/dashboards/sk.php';
        } elseif ($role === 'lydo') {
            require 'views/dashboards/lydo.php';
        } elseif ($role === 'sk_fed') {
            require 'views/dashboards/fed.php';
        } elseif (in_array($role, ['verification', 'accountant', 'mayor_office'])) {
            require 'views/dashboards/verification.php';
        } else {
            require 'views/dashboards/public.php';
        }
        break;

    case 'transactions':
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . $base_path . "login");
            exit;
        }
        require 'views/transactions.php';
        break;

    case 'projects':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_admin') {
            header("Location: " . $base_path . "login");
            exit;
        }
        require 'views/projects/index.php';
        break;

    case 'project-view':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_admin') {
            header("Location: " . $base_path . "login");
            exit;
        }
        require 'views/projects/view.php';
        break;

    case 'certification':
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . $base_path . "login");
            exit;
        }
        require 'controllers/CertificationController.php';
        $certCtrl = new CertificationController();
        $certCtrl->view();
        break;

    case 'api/projects/create':
        require 'controllers/ProjectController.php';
        $proj = new ProjectController();
        $proj->create();
        break;

    case 'api/transactions/submit':
        require 'controllers/TransactionController.php';
        $tx = new TransactionController();
        $tx->submit_transaction();
        break;

    case 'reports':
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . $base_path . "login");
            exit;
        }
        // SK Admin sees submission form, oversight roles see consolidation view
        if ($_SESSION['role'] === 'sk_admin') {
            require 'views/reports/sk_reports.php';
        } elseif (in_array($_SESSION['role'], ['lydo', 'sk_fed', 'verification', 'accountant', 'mayor_office'])) {
            require 'views/reports/consolidation.php';
        } else {
            header("Location: " . $base_path . "dashboard");
            exit;
        }
        break;

    case 'feedback':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_admin') {
            header("Location: " . $base_path . "dashboard");
            exit;
        }
        require 'controllers/FeedbackController.php';
        $feedbackCtrl = new FeedbackController();
        $feedbackCtrl->index();
        break;

    case 'api/feedback/delete':
        require 'controllers/FeedbackController.php';
        $feedbackCtrl = new FeedbackController();
        $feedbackCtrl->delete();
        break;

    case 'api/reports/submit':
        require 'controllers/ReportController.php';
        $reportCtrl = new ReportController();
        $reportCtrl->submit_mar();
        break;

    case 'api/lydo/approve_tx':
    case 'api/lydo/return_tx':
        require 'controllers/LYDOController.php';
        $lydo = new LYDOController();
        $lydo->review_transaction();
        break;

    case 'api/lydo/approve_mar':
    case 'api/lydo/return_mar':
        require 'controllers/LYDOController.php';
        $lydo = new LYDOController();
        $lydo->review_mar();
        break;

    case 'api/fed/record_tx':
        require 'controllers/FedController.php';
        $fed = new FedController();
        $fed->record_transaction();
        break;

    case 'api/fed/record_mar':
        require 'controllers/FedController.php';
        $fed = new FedController();
        $fed->record_mar();
        break;

    case 'api/milestone/mark':
        require 'controllers/MilestoneController.php';
        $ms = new MilestoneController();
        $ms->mark();
        break;

    case 'api/project/update_status':
        require 'controllers/ProjectController.php';
        $proj = new ProjectController();
        $proj->update_status();
        break;

    default:
        http_response_code(404);
        echo "404 Page Not Found";
        break;
}
?>
