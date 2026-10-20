<?php
// views/layout.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard' ?> - SK FUND System</title>
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
    <link rel="stylesheet" href="/FundSystem/assets/css/mobile-responsive.css">
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Inter', sans-serif;
        }
        @media (max-width: 1023px) {
            body.dashboard-shell { min-width: 0; }
            #appSidebar {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 50;
                width: min(18rem, 85vw);
                height: 100dvh;
                transform: translateX(-100%);
                transition: transform 0.2s ease;
                box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
            }
            #appSidebar.is-open { transform: translateX(0); }
            #sidebarBackdrop {
                position: fixed;
                inset: 0;
                z-index: 40;
                border: 0;
                background: rgba(15, 23, 42, 0.45);
            }
            #dashboardMain { width: 100%; min-width: 0; }
            #dashboardHeader {
                display: grid;
                grid-template-columns: auto minmax(0, 1fr);
                gap: 0.35rem 0.75rem;
                height: auto;
                min-height: 4rem;
                padding: 0.75rem 1rem;
            }
            #dashboardMenuButton {
                display: inline-flex;
                grid-column: 1;
                grid-row: 1 / span 2;
            }
            #dashboardHeader h2 {
                grid-column: 2;
                min-width: 0;
                overflow-wrap: anywhere;
                font-size: 1rem;
                line-height: 1.25;
            }
            #dashboardHeader > span {
                grid-column: 2;
                justify-self: start;
                max-width: 100%;
                overflow-wrap: anywhere;
            }
            #dashboardContent {
                min-width: 0;
                padding: 1rem;
                overflow-x: hidden;
            }
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
<body class="dashboard-shell flex h-screen overflow-hidden text-slate-800">

    <!-- Sidebar -->
    <aside id="appSidebar" class="w-64 bg-white border-r border-slate-200 flex flex-col h-full" aria-label="Dashboard navigation">
        <div class="p-6 border-b border-slate-200">
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">SK FUND System</h1>
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
            <?php if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['sk_admin', 'lydo', 'sk_fed'])): ?>
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
            
            <!-- SK Admin Only Section -->
            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'sk_admin'): ?>
            <a href="feedback" class="flex items-center px-3 py-2 text-sm font-medium rounded-md <?= ($current_route === 'feedback') ? $active_class : $inactive_class ?> mt-1">
                <div class="relative">
                    <svg class="mr-3 h-5 w-5 <?= ($current_route === 'feedback') ? 'text-slate-500' : 'text-slate-400' ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    <?php 
                        if (session_status() === PHP_SESSION_NONE) session_start();
                        if (isset($_SESSION['user_id'])) {
                            require_once 'core/Database.php';
                            $db_inst = new Database();
                            $conn = $db_inst->connect();
                            $countStmt = $conn->prepare("SELECT COUNT(*) as cnt FROM feedback f LEFT JOIN projects p ON f.project_id = p.id WHERE p.user_id = :uid AND f.read_at IS NULL");
                            $countStmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
                            $countStmt->execute();
                            $unread = $countStmt->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0;
                            if ($unread > 0):
                    ?>
                    <span class="absolute -top-1 -right-2 inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"><?= $unread ?></span>
                    <?php endif; } ?>
                </div>
                Community Feedback
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
    <button id="sidebarBackdrop" class="hidden" type="button" aria-label="Close navigation menu"></button>

    <!-- Main Content -->
    <main id="dashboardMain" class="flex-1 flex flex-col overflow-hidden bg-slate-50">
        <header id="dashboardHeader" class="bg-white border-b border-slate-200 h-16 flex items-center px-8 justify-between">
            <button id="dashboardMenuButton" type="button" class="hidden items-center justify-center rounded-lg border border-slate-200 bg-white p-2 text-slate-700 hover:bg-slate-50" aria-label="Open navigation menu" aria-controls="appSidebar" aria-expanded="false">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <h2 class="text-lg font-semibold text-slate-800"><?= $page_title ?? 'Overview' ?></h2>
            <?php if(isset($_SESSION['barangay_name'])): ?>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200">
                    <?= htmlspecialchars($_SESSION['barangay_name']) ?>
                </span>
            <?php endif; ?>
        </header>
        
        <div id="dashboardContent" class="flex-1 overflow-y-auto p-8">
            <?= $content ?? '' ?>
        </div>
    </main>
    <script>
        const appSidebar = document.getElementById('appSidebar');
        const sidebarBackdrop = document.getElementById('sidebarBackdrop');
        const dashboardMenuButton = document.getElementById('dashboardMenuButton');

        function closeDashboardMenu() {
            appSidebar.classList.remove('is-open');
            sidebarBackdrop.classList.add('hidden');
            dashboardMenuButton.setAttribute('aria-expanded', 'false');
        }

        dashboardMenuButton.addEventListener('click', () => {
            const isOpen = appSidebar.classList.toggle('is-open');
            sidebarBackdrop.classList.toggle('hidden', !isOpen);
            dashboardMenuButton.setAttribute('aria-expanded', String(isOpen));
        });
        sidebarBackdrop.addEventListener('click', closeDashboardMenu);
        appSidebar.querySelectorAll('nav a').forEach((link) => {
            link.addEventListener('click', closeDashboardMenu);
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeDashboardMenu();
        });
    </script>
</body>
</html>
