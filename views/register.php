<?php
require_once 'includes/config.php';

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

    <div class="min-h-screen w-full flex flex-col lg:flex-row">

        <!-- Left Side: Branding / Text -->
        <div class="hidden lg:flex w-full lg:w-1/2 relative overflow-hidden flex-col justify-center px-16 xl:px-24">
            <div class="absolute inset-0 bg-cover bg-center z-0" style="background-image: url('assets/img/bg.png');"></div>
            <div class="absolute inset-0 bg-black/75 z-0"></div>

            <div class="relative z-10 w-full max-w-lg left-content opacity-0 transform translate-y-8">
                <a href="/FundSystem/"
                    class="inline-flex items-center text-sm font-medium text-slate-400 hover:text-blue-400 transition mb-12 group">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Public Portal
                </a>

                <h1 class="text-4xl xl:text-5xl font-extrabold text-white tracking-tight mb-6 leading-tight">
                    Watch SK Fund<br>Management System
                </h1>

                <p class="text-lg text-slate-300 font-medium mb-12 leading-relaxed">
                    A centralized platform for monitoring youth development programs, ensuring transparency,
                    accountability, and fiscal responsibility across all barangays.
                </p>

                <div class="flex items-center space-x-4 text-slate-400 text-sm font-medium">
                    <div class="flex items-center"><svg class="w-5 h-5 mr-2 text-slate-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg> SK Profiling</div>
                    <div class="flex items-center"><svg class="w-5 h-5 mr-2 text-slate-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg> MAR Submissions</div>
                    <div class="flex items-center"><svg class="w-5 h-5 mr-2 text-slate-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg> Official Registry</div>
                </div>
            </div>
        </div>

        <!-- Right Side: Register Form -->
        <div class="w-full lg:w-1/2 bg-white flex items-center justify-center p-8 sm:p-16">
            <div class="w-full max-w-md right-content">

                <a href="login"
                    class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-600 transition mb-8 group">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Login
                </a>

                <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Create an Account</h2>
                <p class="text-slate-500 mb-8">Register your SK account so you can access the portal.</p>

                <div id="alert" class="hidden mb-6 p-4 rounded-lg text-sm bg-red-50 text-red-600 border border-red-200">
                </div>

                <form id="registerForm" class="space-y-5">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="form-group opacity-0 transform translate-y-4">
                            <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                            <input id="full_name" name="full_name" type="text" required
                                class="input-field w-full px-4 py-3 rounded-lg font-medium"
                                placeholder="e.g. Juan Dela Cruz">
                        </div>
                        <div class="form-group opacity-0 transform translate-y-4">
                            <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">Username</label>
                            <input id="username" name="username" type="text" required
                                class="input-field w-full px-4 py-3 rounded-lg font-medium"
                                placeholder="Enter your username">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="form-group opacity-0 transform translate-y-4">
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                            <input id="email" name="email" type="email" required
                                class="input-field w-full px-4 py-3 rounded-lg font-medium"
                                placeholder="name@example.com">
                        </div>
                        <div class="form-group opacity-0 transform translate-y-4">
                            <label for="barangay_name" class="block text-sm font-medium text-slate-700 mb-1.5">Barangay</label>
                            <select id="barangay_name" name="barangay_name" required class="input-field w-full px-4 py-3 rounded-lg font-medium">
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
                    </div>

                    <div class="form-group opacity-0 transform translate-y-4">
                        <label for="role" class="block text-sm font-medium text-slate-700 mb-1.5">Role / Position</label>
                        <select id="role" name="role" required class="input-field w-full px-4 py-3 rounded-lg font-medium">
                            <option value="" disabled selected>Select a role</option>
                            <option value="sk_admin">SK Admin</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="form-group opacity-0 transform translate-y-4">
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" required minlength="6"
                                    class="input-field w-full px-4 py-3 rounded-lg font-medium pr-12"
                                    placeholder="••••••••">
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none">
                                    <svg id="passwordEyeIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="form-group opacity-0 transform translate-y-4">
                            <label for="confirm_password" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password</label>
                            <div class="relative">
                                <input id="confirm_password" name="confirm_password" type="password" required minlength="6"
                                    class="input-field w-full px-4 py-3 rounded-lg font-medium pr-12"
                                    placeholder="••••••••">
                                <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none">
                                    <svg id="confirmPasswordEyeIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group opacity-0 transform translate-y-4 pt-2">
                        <button type="submit" id="submitBtn"
                            class="w-full flex justify-center items-center py-3.5 px-4 rounded-lg text-sm font-bold text-white bg-black hover:bg-zinc-800 transition shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
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

                    <div class="text-center text-sm text-slate-500 mt-4">
                        Already have an account? <a href="login" class="font-semibold text-blue-600 hover:text-blue-700">Sign in</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Entrance animations
            anime.timeline({
                easing: 'easeOutQuart'
            })
                .add({
                    targets: '.left-content',
                    opacity: [0, 1],
                    translateY: [40, 0],
                    duration: 1200,
                    delay: 200
                })
                .add({
                    targets: '.right-content',
                    opacity: [0, 1],
                    translateX: [-40, 0],
                    duration: 1000
                }, '-=1000')
                .add({
                    targets: '.form-group',
                    opacity: [0, 1],
                    translateY: [20, 0],
                    duration: 800,
                    delay: anime.stagger(100)
                }, '-=600');

            const form = document.getElementById('registerForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');
            const togglePassword = document.getElementById('togglePassword');
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const passwordEyeIcon = document.getElementById('passwordEyeIcon');
            const confirmPasswordEyeIcon = document.getElementById('confirmPasswordEyeIcon');

            const updateEyeIcon = (icon, visible) => {
                icon.innerHTML = visible
                    ? `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                        />`
                    : `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`
            };

            togglePassword?.addEventListener('click', () => {
                const visible = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', visible ? 'text' : 'password');
                updateEyeIcon(passwordEyeIcon, visible);
            });

            toggleConfirmPassword?.addEventListener('click', () => {
                const visible = confirmPasswordInput.getAttribute('type') === 'password';
                confirmPasswordInput.setAttribute('type', visible ? 'text' : 'password');
                updateEyeIcon(confirmPasswordEyeIcon, visible);
            });

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
