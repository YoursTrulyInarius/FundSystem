<?php
// controllers/AuthController.php
require_once 'core/Database.php';
require_once 'models/User.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    public function forgot_password() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        if (empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Please provide your email address.']);
            return;
        }

        $database = new Database();
        $db = $database->connect();

        // Check if email exists
        $query = "SELECT id, full_name, email FROM users WHERE email = :email LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'No account found with that email address.']);
            return;
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Save OTP to DB (reuse reset_token and reset_token_expires columns)
        $updateQuery = "UPDATE users SET reset_token = :otp, reset_token_expires = :expires WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute([
            ':otp' => $otp,
            ':expires' => $expires,
            ':id' => $user['id']
        ]);

        // Write OTP to log for development / backup convenience
        $logDir = 'uploads';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        $logFile = $logDir . '/reset_links.log';
        $logContent = "[" . date('Y-m-d H:i:s') . "] OTP for " . $email . " (" . $user['full_name'] . "): " . $otp . "\n";
        file_put_contents($logFile, $logContent, FILE_APPEND);

        // Attempt sending email with OTP
        require_once 'vendor/autoload.php';

        $mail = new PHPMailer(true);
        $emailSent = false;

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = !empty(SMTP_USER);
            if (!empty(SMTP_USER)) {
                $mail->Username   = SMTP_USER;
                $mail->Password   = SMTP_PASS;
            }
            $mail->Port       = SMTP_PORT;
            if (!empty(SMTP_SECURE)) {
                $mail->SMTPSecure = SMTP_SECURE;
            }

            // Recipients
            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($email, $user['full_name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your Password Reset Code - Watch SK Fund';
            $mail->Body    = "
                <div style='font-family: sans-serif; max-width: 500px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px;'>
                    <h2 style='color: #0f172a;'>Password Reset Code</h2>
                    <p style='color: #475569;'>Hello " . htmlspecialchars($user['full_name']) . ",</p>
                    <p style='color: #475569;'>We received a request to reset your password for the Watch SK Fund portal. Use the verification code below to proceed. This code is valid for <strong>10 minutes</strong>.</p>
                    <div style='margin: 30px 0; text-align: center;'>
                        <div style='background-color: #f1f5f9; border: 2px dashed #cbd5e1; border-radius: 12px; padding: 20px; display: inline-block;'>
                            <span style='font-family: monospace; font-size: 36px; font-weight: bold; letter-spacing: 8px; color: #0f172a;'>" . $otp . "</span>
                        </div>
                    </div>
                    <p style='color: #64748b; font-size: 12px;'>If you did not request a password reset, please ignore this email. Your password will remain unchanged.</p>
                    <hr style='border: none; border-top: 1px solid #f1f5f9; margin-top: 20px;'>
                    <p style='color: #94a3b8; font-size: 11px;'>This code will expire at " . date('g:i A', strtotime($expires)) . ".</p>
                </div>";

            $mail->send();
            $emailSent = true;
        } catch (Exception $e) {
            // Log PHPMailer error internally
            $errorContent = "[" . date('Y-m-d H:i:s') . "] Mailer Error for " . $email . ": " . $mail->ErrorInfo . "\n";
            file_put_contents('uploads/reset_errors.log', $errorContent, FILE_APPEND);
        }

        if ($emailSent) {
            echo json_encode(['success' => true, 'message' => 'A 6-digit verification code has been sent to your email.']);
        } else {
            echo json_encode([
                'success' => true, 
                'message' => 'Verification code generated. (Note: Email delivery failed. Check uploads/reset_links.log for the OTP code.)'
            ]);
        }
    }

    public function verify_otp() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $otp = trim($_POST['otp'] ?? '');

        if (empty($email) || empty($otp)) {
            echo json_encode(['success' => false, 'message' => 'Please provide both email and OTP code.']);
            return;
        }

        if (!preg_match('/^\d{6}$/', $otp)) {
            echo json_encode(['success' => false, 'message' => 'Invalid code format. Please enter a 6-digit number.']);
            return;
        }

        $database = new Database();
        $db = $database->connect();

        // Verify OTP matches and is not expired
        $query = "SELECT id, email FROM users WHERE email = :email AND reset_token = :otp AND reset_token_expires > NOW() LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([':email' => $email, ':otp' => $otp]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired verification code. Please try again or request a new code.']);
            return;
        }

        // OTP is valid — pass both email and otp to the reset page
        echo json_encode([
            'success' => true, 
            'message' => 'Code verified successfully! Redirecting...',
            'email' => $user['email'],
            'otp' => $otp
        ]);
    }

    public function reset_password() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $otp = trim($_POST['otp'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($email) || empty($otp) || empty($password) || empty($confirmPassword)) {
            echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
            return;
        }

        if ($password !== $confirmPassword) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
            return;
        }

        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
            return;
        }

        $database = new Database();
        $db = $database->connect();

        // Verify OTP is still valid for this email
        $query = "SELECT id, email FROM users WHERE email = :email AND reset_token = :otp AND reset_token_expires > NOW() LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute([':email' => $email, ':otp' => $otp]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired verification code. Please request a new code.']);
            return;
        }

        // Hash new password and clear OTP fields
        $newPasswordHash = password_hash($password, PASSWORD_BCRYPT);
        
        $updateQuery = "UPDATE users SET password = :password, reset_token = NULL, reset_token_expires = NULL WHERE id = :id";
        $updateStmt = $db->prepare($updateQuery);
        $success = $updateStmt->execute([
            ':password' => $newPasswordHash,
            ':id' => $user['id']
        ]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Password reset successfully! Redirecting you to login...']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to reset password. Please try again.']);
        }
    }

    public function register() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
            return;
        }

        // Only SK Admin can register users
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized. Only SK Admin can register new users.']);
            return;
        }

        $full_name = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $barangay_name = trim($_POST['barangay_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate required fields
        if (empty($full_name) || empty($username) || empty($email) || empty($role) || empty($barangay_name) || empty($password) || empty($confirm_password)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            return;
        }

        // Validate role
        $valid_roles = ['sk_admin', 'lydo', 'sk_fed', 'verification'];
        if (!in_array($role, $valid_roles)) {
            echo json_encode(['success' => false, 'message' => 'Invalid role selected.']);
            return;
        }

        // Validate barangay
        $valid_barangays = [
            'Bagong Opon', 'Bambong Daku', 'Bambong Diut', 'Bobongan',
            'Campo IV', 'Campo V', 'Caniangan', 'Dipalusan',
            'Eastern Bobongan', 'Esperanza', 'Gapasan', 'Katipunan',
            'Kauswagan', 'Lower Sambulawan', 'Mabini', 'Magsaysay',
            'Malating', 'Paradise', 'Pasingkalan', 'Poblacion',
            'San Fernando', 'Santo Rosario', 'Sapa Anding', 'Sinaguing',
            'Switch', 'Upper Laperian', 'Wakat'
        ];
        if (!in_array($barangay_name, $valid_barangays)) {
            echo json_encode(['success' => false, 'message' => 'Invalid barangay selected.']);
            return;
        }

        // Validate passwords match
        if ($password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
            return;
        }

        if (strlen($password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters.']);
            return;
        }

        $database = new Database();
        $db = $database->connect();

        // Check if username already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username already taken. Please choose a different one.']);
            return;
        }

        // Check if email already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email address is already registered.']);
            return;
        }

        // Hash password and insert
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $insertQuery = "INSERT INTO users (username, password, role, full_name, barangay_name, email) VALUES (:username, :password, :role, :full_name, :barangay_name, :email)";
        $insertStmt = $db->prepare($insertQuery);
        $success = $insertStmt->execute([
            ':username' => $username,
            ':password' => $hashedPassword,
            ':role' => $role,
            ':full_name' => $full_name,
            ':barangay_name' => $barangay_name,
            ':email' => $email
        ]);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Account for "' . htmlspecialchars($full_name) . '" created successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create account. Please try again.']);
        }
    }
}
?>
