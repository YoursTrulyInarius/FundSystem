<?php
$email = $_GET['email'] ?? '';
$otp = $_GET['otp'] ?? '';
$isValid = false;

if (!empty($email) && !empty($otp)) {
    require_once 'core/Database.php';
    $database = new Database();
    $db = $database->connect();

    // Check if OTP is valid and not expired
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email AND reset_token = :otp AND reset_token_expires > NOW() LIMIT 1");
    $stmt->execute([':email' => $email, ':otp' => $otp]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $isValid = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Watch SK Fund</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            margin: 0;
        }

        .input-field {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            transition: all 0.2s ease;
        }

        .input-field:focus {
            background-color: #ffffff;
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
    </style>
</head>

<body class="text-slate-800 antialiased bg-slate-50 flex items-center justify-center min-h-screen p-6">

    <div class="w-full max-w-md bg-white border border-slate-200 rounded-2xl shadow-xl p-8 sm:p-10 card-container opacity-0 transform translate-y-8">
        
        <?php if ($isValid): ?>
            <!-- Header -->
            <div class="mb-8">
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Reset Password</h2>
                <p class="text-slate-500 text-sm">Choose a strong new password for your account.</p>
            </div>

            <form id="resetForm" class="space-y-5">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <input type="hidden" name="otp" value="<?= htmlspecialchars($otp) ?>">
                
                <div class="form-group">
                    <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">New Password</label>
                    <input id="password" name="password" type="password" required minlength="6"
                        class="input-field w-full px-4 py-3.5 rounded-xl font-medium"
                        placeholder="••••••••">
                </div>

                <div class="form-group">
                    <label for="confirm_password" class="block text-sm font-bold text-slate-700 mb-1.5">Confirm New Password</label>
                    <input id="confirm_password" name="confirm_password" type="password" required minlength="6"
                        class="input-field w-full px-4 py-3.5 rounded-xl font-medium"
                        placeholder="••••••••">
                </div>

                <div class="form-group pt-2">
                    <button type="submit" id="submitBtn"
                        class="w-full flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-black hover:bg-zinc-800 transition shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                        <span id="btnText">Save Password</span>
                        <svg id="btnLoader" class="hidden animate-spin ml-3 h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </button>
                </div>
            </form>
        <?php else: ?>
            <!-- Invalid / Expired State -->
            <div class="text-center py-4">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-50 text-red-500 mb-4 border border-red-100">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Session Expired or Invalid</h3>
                <p class="text-slate-500 text-sm mb-6 max-w-xs mx-auto">Your verification code has expired or is invalid. Please request a new one.</p>
                <a href="forgot-password" class="inline-flex items-center justify-center px-6 py-2.5 bg-black hover:bg-zinc-800 text-white font-bold text-sm rounded-xl transition shadow-md">
                    Request New Code
                </a>
            </div>
        <?php endif; ?>

        <!-- Back to Login -->
        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <a href="login" class="text-sm font-bold text-blue-600 hover:text-blue-700 hover:underline">
                Back to Login
            </a>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Animating main card container in
            anime({
                targets: '.card-container',
                opacity: [0, 1],
                translateY: [30, 0],
                duration: 800,
                easing: 'easeOutQuart'
            });

            const form = document.getElementById('resetForm');
            if (form) {
                const submitBtn = document.getElementById('submitBtn');
                const btnText = document.getElementById('btnText');
                const btnLoader = document.getElementById('btnLoader');

                form.addEventListener('submit', (e) => {
                    e.preventDefault();

                    // Check passwords match
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;

                    if (password !== confirmPassword) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Passwords do not match.'
                        });
                        return;
                    }

                    // Show loading sweetalert
                    Swal.fire({
                        title: 'Please wait...',
                        text: 'Wait for a second while we reset your password.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Start loader on button
                    submitBtn.disabled = true;
                    btnText.textContent = 'Saving...';
                    btnLoader.classList.remove('hidden');

                    const formData = new FormData(form);

                    fetch('api/reset-password', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        submitBtn.disabled = false;
                        btnText.textContent = 'Save Password';
                        btnLoader.classList.add('hidden');
                        Swal.close();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            form.reset();
                            
                            // Redirect to login after 2 seconds
                            setTimeout(() => {
                                window.location.href = 'login';
                            }, 2000);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Reset Failed',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        submitBtn.disabled = false;
                        btnText.textContent = 'Save Password';
                        btnLoader.classList.add('hidden');
                        Swal.close();

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An unexpected error occurred. Please try again.'
                        });
                    });
                });
            }
        });
    </script>
</body>

</html>
