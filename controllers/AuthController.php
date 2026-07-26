<?php
// controllers/AuthController.php
require_once 'core/Database.php';
require_once 'models/User.php';

class AuthController {
    public function login() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->connect();
            $user = new User($db);

            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Please provide both username and password.']);
                return;
            }

            if ($user->login($username, $password)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['role'] = $user->role;
                $_SESSION['full_name'] = $user->full_name;
                $_SESSION['barangay_name'] = $user->barangay_name;

                echo json_encode([
                    'success' => true,
                    'role' => $user->role,
                    'message' => 'Login successful.'
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        
        $script_name = dirname($_SERVER['SCRIPT_NAME']);
        $script_name = str_replace('\\', '/', $script_name);
        if ($script_name === '/') $script_name = '';
        
        header("Location: " . $script_name . "/login");
        exit;
    }
}
?>
