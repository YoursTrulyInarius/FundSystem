<?php
require_once 'includes/config.php';

// Only SK Admin can register new users
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sk_admin') {
    header("Location: login");
    exit;
}

$base_path = '/FundSystem/';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register New User - Watch SK Fund</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
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

        select.input-field {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 20px;
            padding-right: 40px;
        }
    </style>
</head>

<body class="text-slate-800 antialiased">

    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-2xl card-container opacity-0 transform translate-y-8">

            <!-- Back to Dashboard -->
            <a href="dashboard" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-600 transition mb-6 group">
                <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-xl p-8 sm:p-10">

                <!-- Header -->
                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Register New User</h2>
                    <p class="text-slate-500 text-sm">Create a new account for an SK Barangay Council member or staff. All fields are required.</p>
                </div>

                <form id="registerForm" class="space-y-5">

                    <!-- Row: Full Name & Username -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="form-group opacity-0 transform translate-y-4">
                            <label for="full_name" class="block text-sm font-bold text-slate-700 mb-1.5">Full Name</label>
                            <input id="full_name" name="full_name" type="text" required
                                class="input-field w-full px-4 py-3 rounded-xl font-medium"
                                placeholder="e.g. Juan Dela Cruz">
                        </div>
                        <div class="form-group opacity-0 transform translate-y-4">
                            <label for="username" class="block text-sm font-bold text-slate-700 mb-1.5">Username</label>
                            <input id="username" name="username" type="text" required
                                class="input-field w-full px-4 py-3 rounded-xl font-medium"
                                placeholder="e.g. sk_brgy_poblacion">
                        </div>
                    </div>

                    <!-- Row: Email & Role -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="form-group opacity-0 transform translate-y-4">
                            <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">Email Address</label>
                            <input id="email" name="email" type="email" required
                                class="input-field w-full px-4 py-3 rounded-xl font-medium"
                                placeholder="name@example.com">
                        </div>
                        <div class="form-group opacity-0 transform translate-y-4">
                            <label for="role" class="block text-sm font-bold text-slate-700 mb-1.5">Role / Position</label>
                            <select id="role" name="role" required class="input-field w-full px-4 py-3 rounded-xl font-medium">
                                <option value="" disabled selected>Select a role</option>
                                <option value="sk_admin">SK Admin (Chairperson)</option>
                                <option value="lydo">LYDO Officer</option>
                                <option value="sk_fed">SK Federation</option>
                                <option value="verification">Verification / Accountant</option>
                            </select>
                        </div>
                    </div>

                    <!-- Barangay -->
                    <div class="form-group opacity-0 transform translate-y-4">
                        <label for="barangay_name" class="block text-sm font-bold text-slate-700 mb-1.5">Barangay</label>
                        <select id="barangay_name" name="barangay_name" required class="input-field w-full px-4 py-3 rounded-xl font-medium">
                            <option value="" disabled selected>Select barangay</option>
                            <option value="Bagong Opon">Bagong Opon</option>
                            <option value="Bambong Daku">Bambong Daku</option>
                            <option value="Bambong Diut">Bambong Diut</option>
                            <option value="Bobongan">Bobongan</option>
                            <option value="Campo IV">Campo IV</option>
                            <option value="Campo V">Campo V</option>
                            <option value="Caniangan">Caniangan</option>
                            <option value="Dipalusan">Dipalusan</option>
                            <option value="Eastern Bobongan">Eastern Bobongan</option>
                            <option value="Esperanza">Esperanza</option>
                            <option value="Gapasan">Gapasan</option>
                            <option value="Katipunan">Katipunan</option>
                            <option value="Kauswagan">Kauswagan</option>
                            <option value="Lower Sambulawan">Lower Sambulawan</option>
                            <option value="Mabini">Mabini</option>
                            <option value="Magsaysay">Magsaysay</option>
                            <option value="Malating">Malating</option>
                            <option value="Paradise">Paradise</option>
                            <option value="Pasingkalan">Pasingkalan</option>
                            <option value="Poblacion">Poblacion</option>
                            <option value="San Fernando">San Fernando</option>
                            <option value="Santo Rosario">Santo Rosario</option>
                            <option value="Sapa Anding">Sapa Anding</option>
                            <option value="Sinaguing">Sinaguing</option>
                            <option value="Switch">Switch</option>
                            <option value="Upper Laperian">Upper Laperian</option>
                            <option value="Wakat">Wakat</option>
                        </select>
                    </div>

                    <!-- Row: Password & Confirm Password -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="form-group opacity-0 transform translate-y-4">
                            <label for="password" class="block text-sm font-bold text-slate-700 mb-1.5">Password</label>
                            <input id="password" name="password" type="password" required minlength="6"
                                class="input-field w-full px-4 py-3 rounded-xl font-medium"
                                placeholder="••••••••">
                        </div>
                        <div class="form-group opacity-0 transform translate-y-4">
                            <label for="confirm_password" class="block text-sm font-bold text-slate-700 mb-1.5">Confirm Password</label>
                            <input id="confirm_password" name="confirm_password" type="password" required minlength="6"
                                class="input-field w-full px-4 py-3 rounded-xl font-medium"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group opacity-0 transform translate-y-4 pt-3">
                        <button type="submit" id="submitBtn"
                            class="w-full flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-black hover:bg-zinc-800 transition shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                            <span id="btnText">Create Account</span>
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
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Entrance animations
            anime({
                targets: '.card-container',
                opacity: [0, 1],
                translateY: [30, 0],
                duration: 800,
                easing: 'easeOutQuart'
            });

            anime({
                targets: '.form-group',
                opacity: [0, 1],
                translateY: [20, 0],
                duration: 600,
                delay: anime.stagger(80, { start: 400 }),
                easing: 'easeOutQuart'
            });

            const form = document.getElementById('registerForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');

            form.addEventListener('submit', (e) => {
                e.preventDefault();

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

                Swal.fire({
                    title: 'Please wait...',
                    text: 'Creating the account...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                submitBtn.disabled = true;
                btnText.textContent = 'Creating...';
                btnLoader.classList.remove('hidden');

                const formData = new FormData(form);

                fetch('api/register', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    submitBtn.disabled = false;
                    btnText.textContent = 'Create Account';
                    btnLoader.classList.add('hidden');
                    Swal.close();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Account Created!',
                            text: data.message,
                            confirmButtonColor: '#0f172a'
                        }).then(() => {
                            form.reset();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    btnText.textContent = 'Create Account';
                    btnLoader.classList.add('hidden');
                    Swal.close();

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again.'
                    });
                });
            });
        });
    </script>
</body>

</html>
