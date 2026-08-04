<?php
// views/layout.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard' ?> - Watch SK Fund</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0f172a',
                        secondary: '#1e293b',
                        accent: '#3b82f6',
                        background: '#f8fafc',
                        surface: '#ffffff'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }
        /* Bklit UI / Shadcn aesthetic: subtle borders, sharp aesthetics, no gradients */
        .card {
            background-color: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .btn-primary {
            background-color: #0f172a;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn-primary:hover {
            background-color: #1e293b;
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-slate-800">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col h-full">
        <div class="p-6 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Watch SK Fund</h1>
            <p class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($_SESSION['role'] ?? 'Public') ?> Portal</p>
        </div>
        
        <?php
        $current_route = $route ?? '';
        $active_class = "bg-slate-100 text-slate-900";
        $inactive_class = "text-slate-600 hover:bg-slate-50 hover:text-slate-900";
        ?>
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <a href="dashboard" class="flex items-center px-3 py-2 text-sm font-medium rounded-md <?= ($current_route === 'dashboard') ? $active_class : $inactive_class ?>">
                <svg class="mr-3 h-5 w-5 <?= ($current_route === 'dashboard') ? 'text-slate-500' : 'text-slate-400' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'sk_admin'): ?>
            <a href="projects" class="flex items-center px-3 py-2 text-sm font-medium rounded-md <?= ($current_route === 'projects' || $current_route === 'project-view') ? $active_class : $inactive_class ?> mt-1">
                <svg class="mr-3 h-5 w-5 <?= ($current_route === 'projects' || $current_route === 'project-view') ? 'text-slate-500' : 'text-slate-400' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Projects & ABYIP
            </a>
            <?php endif; ?>
            <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['sk_admin', 'lydo', 'sk_fed', 'verification'])): ?>
            <a href="transactions" class="flex items-center px-3 py-2 text-sm font-medium rounded-md <?= ($current_route === 'transactions') ? $active_class : $inactive_class ?> mt-1">
                <svg class="mr-3 h-5 w-5 <?= ($current_route === 'transactions') ? 'text-slate-500' : 'text-slate-400' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Financial Transactions
            </a>
            <a href="reports" class="flex items-center px-3 py-2 text-sm font-medium rounded-md <?= ($current_route === 'reports') ? $active_class : $inactive_class ?> mt-1">
                <svg class="mr-3 h-5 w-5 <?= ($current_route === 'reports') ? 'text-slate-500' : 'text-slate-400' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                MAR & Compliance Reports
            </a>
            <?php endif; ?>
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'sk_admin'): ?>
            <a href="register" class="flex items-center px-3 py-2 text-sm font-medium rounded-md <?= ($current_route === 'register') ? $active_class : $inactive_class ?> mt-1">
                <svg class="mr-3 h-5 w-5 <?= ($current_route === 'register') ? 'text-slate-500' : 'text-slate-400' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Register User
            </a>
            <?php endif; ?>
        </nav>

        <div class="p-4 border-t border-slate-200">
            <div class="flex items-center mb-4">
                <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-sm">
                    <?= substr($_SESSION['full_name'] ?? 'U', 0, 1) ?>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-slate-700"><?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?></p>
                </div>
            </div>
            <a href="logout" class="block w-full text-center px-3 py-2 border border-slate-300 shadow-sm text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50">
                Log Out
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden bg-slate-50">
        <header class="bg-white border-b border-slate-200 h-16 flex items-center px-8 justify-between">
            <h2 class="text-lg font-semibold text-slate-800"><?= $page_title ?? 'Overview' ?></h2>
            <?php if(isset($_SESSION['barangay_name'])): ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                    <?= htmlspecialchars($_SESSION['barangay_name']) ?>
                </span>
            <?php endif; ?>
        </header>
        
        <div class="flex-1 overflow-y-auto p-8">
            <?= $content ?? '' ?>
        </div>
    </main>
</body>
</html>
