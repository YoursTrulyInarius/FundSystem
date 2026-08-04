<?php
require_once 'includes/config.php';

// If user is already logged in, redirect them to their dashboard
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
    header("Location: dashboard");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Watch SK Fund</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            margin: 0;
        }

        /* Custom Input Styles */
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

<body class="text-slate-800 antialiased">

    <div class="min-h-screen w-full flex flex-col lg:flex-row">

        <!-- Left Side: Branding / Text -->
        <div class="hidden lg:flex w-full lg:w-1/2 relative overflow-hidden flex-col justify-center px-16 xl:px-24">
            <!-- New Background Image -->
            <div class="absolute inset-0 bg-cover bg-center z-0" style="background-image: url('assets/img/bg.png');">
            </div>
            <!-- Dark Overlay for Readability -->
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

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 bg-white flex items-center justify-center p-8 sm:p-16">
            <div class="w-full max-w-md right-content opacity-0 transform -translate-x-8">

                <!-- Mobile Back Button (Only visible on small screens) -->
                <a href="/FundSystem/"
                    class="lg:hidden inline-flex items-center text-sm font-medium text-slate-500 hover:text-blue-600 transition mb-8 group">
                    <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Public Portal
                </a>

                <h2 class="text-3xl font-bold text-slate-900 tracking-tight mb-2">Welcome Back</h2>
                <p class="text-slate-500 mb-8">Sign in to access your administrative dashboard.</p>

                <div id="alert" class="hidden mb-6 p-4 rounded-lg text-sm bg-red-50 text-red-600 border border-red-200">
                </div>

                <form id="loginForm" class="space-y-5">
                    <div class="form-group opacity-0 transform translate-y-4">
                        <label for="username" class="block text-sm font-medium text-slate-700 mb-1.5">Username</label>
                        <input id="username" name="username" type="text" required
                            class="input-field w-full px-4 py-3 rounded-lg font-medium"
                            placeholder="Enter your username">
                    </div>

                    <div class="form-group opacity-0 transform translate-y-4">
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                            <a href="forgot-password" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">Forgot password?</a>
                        </div>
                        <div class="relative">
                            <input id="password" name="password" type="password" required
                                class="input-field w-full px-4 py-3 rounded-lg font-medium pr-12"
                                placeholder="••••••••">
                            <button type="button" id="togglePassword"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none transition">
                                <svg id="eyeIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group opacity-0 transform translate-y-4 pt-2">
                        <button type="submit" id="loginBtn"
                            class="w-full flex justify-center items-center py-3.5 px-4 rounded-lg text-sm font-bold text-white bg-black hover:bg-zinc-800 transition shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                            <span id="btnText">Sign In</span>
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

    <script src="assets/js/login.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Entrance Animations
            anime.timeline({
                easing: 'easeOutQuart'
            })
                // Left Side Content Animation (slides up)
                .add({
                    targets: '.left-content',
                    opacity: [0, 1],
                    translateY: [40, 0],
                    duration: 1200,
                    delay: 200
                })
                // Right Side Form Container (slides in from left)
                .add({
                    targets: '.right-content',
                    opacity: [0, 1],
                    translateX: [-40, 0],
                    duration: 1000
                }, '-=1000')
                // Staggered Form Inputs (slide up slightly)
                .add({
                    targets: '.form-group',
                    opacity: [0, 1],
                    translateY: [20, 0],
                    duration: 800,
                    delay: anime.stagger(100)
                }, '-=600');
        });
    </script>
</body>

</html>