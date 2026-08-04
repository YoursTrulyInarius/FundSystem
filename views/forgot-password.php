<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Watch SK Fund</title>
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
        
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight mb-2">Forgot Password?</h2>
            <p class="text-slate-500 text-sm">Enter your registered email address below, and we will send you a secure link to reset your password.</p>
        </div>

        <form id="forgotForm" class="space-y-6">
            <div class="form-group">
                <label for="email" class="block text-sm font-bold text-slate-700 mb-1.5">Email Address</label>
                <input id="email" name="email" type="text" required
                    class="input-field w-full px-4 py-3.5 rounded-xl font-medium"
                    placeholder="name@example.com or digits">
            </div>

            <div class="form-group pt-2">
                <button type="submit" id="submitBtn"
                    class="w-full flex justify-center items-center py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-black hover:bg-zinc-800 transition shadow-md hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black">
                    <span id="btnText">Send Reset Link</span>
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

            const form = document.getElementById('forgotForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');

            form.addEventListener('submit', (e) => {
                e.preventDefault();

                // Show loading SweetAlert
                Swal.fire({
                    title: 'Please wait...',
                    text: 'Wait for a second while we process your request.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Start loading on button
                submitBtn.disabled = true;
                btnText.textContent = 'Sending...';
                btnLoader.classList.remove('hidden');

                const formData = new FormData(form);

                fetch('api/forgot-password', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    submitBtn.disabled = false;
                    btnText.textContent = 'Send Reset Link';
                    btnLoader.classList.add('hidden');
                    Swal.close();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'OTP Code Sent!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        const emailVal = document.getElementById('email').value;
                        setTimeout(() => {
                            window.location.href = 'verify-otp?email=' + encodeURIComponent(emailVal);
                        }, 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    submitBtn.disabled = false;
                    btnText.textContent = 'Send Reset Link';
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
