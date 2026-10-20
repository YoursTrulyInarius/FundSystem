<?php
// views/public/barangay_view.php
// Rendered by PublicController::barangayView()
// Expected variables: $clean_name, $profile, $projects, $transactions,
//                     $benefits, $sk_official, $comments,
//                     $total_budget, $utilization_rate
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay <?= htmlspecialchars($clean_name) ?> &mdash; SK FUND System</title>
    <meta name="description" content="Explore SK-funded projects, community benefits, and fund utilization for Barangay <?= htmlspecialchars($clean_name) ?> in Ramon Magsaysay.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/FundSystem/assets/css/mobile-responsive.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <style>
        /* ════════ Design Tokens (White, Black, Blue 3-Color Palette) ════════ */
        :root {
            --c-primary:      #000000;
            --c-secondary:    #FFFFFF;
            --c-accent:       #2563EB;
            --c-accent-hover: #1D4ED8;
            --c-accent-light: #EFF6FF;
            --c-accent-border:#DBEAFE;

            --c-bg:           #F8FAFC;
            --c-surface:      #FFFFFF;
            --c-border:       #E2E8F0;
            --c-border-subtle:#F1F5F9;

            --c-text:         #0F172A;
            --c-text-muted:   #64748B;
            --c-text-sub:     #475569;

            --navbar-h:       64px;
            --radius-lg:      22px;
            --radius-md:      14px;
            --radius-sm:      10px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--c-bg);
            color: var(--c-text);
            min-height: 100vh;
            overflow-x: clip;
            -webkit-font-smoothing: antialiased;
        }

        /* ════════ Navbar ════════ */
        .navbar {
            position: sticky; top: 0; z-index: 50;
            height: var(--navbar-h);
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--c-border);
            display: flex; align-items: center;
        }
        .navbar-inner {
            max-width: 1280px; width: 100%; margin: 0 auto;
            padding: 0 clamp(1rem, 3vw, 1.5rem);
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        }
        .back-link {
            display: inline-flex; align-items: center; gap: 0.5rem;
            font-size: 0.85rem; font-weight: 700; color: var(--c-text-sub);
            text-decoration: none; transition: color 0.15s, transform 0.15s;
        }
        .back-link:hover { color: var(--c-accent); transform: translateX(-2px); }
        .back-link svg { flex-shrink: 0; }
        .brand {
            font-family: 'Outfit', sans-serif; font-weight: 800;
            font-size: 1.02rem; color: var(--c-text);
            display: flex; align-items: center; gap: 0.65rem; text-decoration: none;
        }
        .brand-icon {
            width: 32px; height: 32px; border-radius: 8px;
            background: #000000; display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        @media (max-width: 480px) {
            .navbar-inner { gap: 0.5rem; }
            .back-link { gap: 0.3rem; font-size: 0.72rem; }
            .brand { gap: 0.4rem; font-size: 0.85rem; }
            .brand-icon { width: 29px; height: 29px; }
        }

        /* ════════ Hero ════════ */
        .hero {
            position: relative; width: 100%; height: clamp(280px, 32vw, 360px); overflow: hidden;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        @media (max-width: 768px) { .hero { height: 260px; } }
        .hero-img {
            width: 100%; height: 100%; object-fit: cover; display: block;
        }
        .hero-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0.25) 0%, rgba(15, 23, 42, 0.88) 100%);
        }
        .hero-placeholder {
            width: 100%; height: 100%;
            display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.75rem;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            position: relative;
        }
        .hero-placeholder::before {
            content: ''; position: absolute; inset: 0;
            background: radial-gradient(circle at 40% 40%, rgba(37, 99, 235, 0.2), transparent 60%);
        }
        .hero-placeholder svg { color: #60a5fa; position: relative; z-index: 1; }
        .hero-placeholder span {
            font-size: 0.75rem; color: #94a3b8;
            font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; position: relative; z-index: 1;
        }
        .hero-content {
            position: absolute; bottom: 0; left: 0; right: 0;
            padding: 1.5rem clamp(1.25rem, 4vw, 2rem) 2rem;
            max-width: 1280px; margin: 0 auto;
            z-index: 2;
        }
        .hero-chip {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.35rem 0.85rem; border-radius: 99px;
            background: rgba(37, 99, 235, 0.25); border: 1px solid rgba(59, 130, 246, 0.45);
            color: #93c5fd; font-size: 0.7rem; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 0.75rem;
            backdrop-filter: blur(8px);
        }
        .hero-chip-dot { width: 6px; height: 6px; border-radius: 50%; background: #60a5fa; }
        .hero-name {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 900; color: #ffffff; line-height: 1.05;
            letter-spacing: -0.03em;
            text-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }
        .hero-name span { color: #60a5fa; }
        .hero-tagline {
            font-size: 0.95rem; color: rgba(241, 245, 249, 0.88);
            margin-top: 0.5rem; max-width: 620px; line-height: 1.6;
            text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        @media (max-width: 480px) {
            .hero-content { padding: 1rem 1rem 1.5rem; }
            .hero-chip { margin-bottom: 0.5rem; font-size: 0.62rem; }
            .hero-name { font-size: clamp(1.8rem, 9vw, 2.5rem); }
            .hero-tagline { font-size: 0.82rem; }
            .hero-placeholder { justify-content: flex-start; padding-top: 1.25rem; }
            .hero-placeholder svg { width: 36px; height: 36px; }
            .hero-placeholder span { display: none; }
        }

        /* ════════ Page layout ════════ */
        .page {
            width: 100%; max-width: 1280px; margin: 0 auto;
            padding: clamp(1.25rem, 3vw, 2rem) clamp(1rem, 3vw, 1.5rem) 4rem;
            display: flex; flex-direction: column; gap: 2rem;
        }

        /* ════════ Stats row ════════ */
        .stats-row {
            display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: clamp(0.75rem, 2vw, 1.25rem);
        }
        @media (max-width: 640px) { .stats-row { grid-template-columns: 1fr; } }
        .stat-card {
            background: #ffffff;
            border: 1px solid var(--c-border);
            border-radius: var(--radius-lg);
            min-width: 0; padding: clamp(1.15rem, 2.5vw, 1.6rem) clamp(1.15rem, 3vw, 1.75rem);
            position: relative; overflow: hidden;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(37, 99, 235, 0.35);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.07);
        }
        .stat-card::before {
            content: ''; position: absolute;
            top: 20px; left: 0;
            width: 4px; height: calc(100% - 40px);
            background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 999px;
        }
        .stat-card-label {
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.1em; color: var(--c-text-muted); margin-bottom: 0.6rem;
        }
        .stat-card-value {
            font-family: 'Outfit', sans-serif; font-size: clamp(1.65rem, 3vw, 2.2rem);
            font-weight: 900; color: var(--c-text); line-height: 1;
            letter-spacing: -0.02em;
        }
        .stat-card-sub {
            font-size: 0.8rem; color: var(--c-text-muted); margin-top: 0.45rem;
            font-weight: 500;
        }

        /* ════════ Two-column grid ════════ */
        .content-grid {
            display: grid; gap: 1.5rem; min-width: 0;
            grid-template-columns: minmax(0, 1fr) minmax(300px, 360px);
        }
        @media (max-width: 1024px) { .content-grid { grid-template-columns: minmax(0, 1fr); } }
        .col { display: flex; min-width: 0; flex-direction: column; gap: 1.5rem; }

        /* ════════ Cards ════════ */
        .card {
            background: #ffffff;
            border: 1px solid var(--c-border);
            border-radius: var(--radius-lg);
            padding: 1.75rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.03);
        }
        .card-title {
            font-family: 'Outfit', sans-serif; font-size: 1.15rem;
            font-weight: 800; color: var(--c-text); margin-bottom: 1.25rem;
            letter-spacing: -0.01em;
            display: flex; align-items: center; justify-content: space-between;
        }
        @media (max-width: 480px) {
            .page { gap: 1.25rem; }
            .card { padding: 1.2rem; }
            .card-title { gap: 0.75rem; font-size: 1rem; }
        }

        /* ════════ Benefits grid ════════ */
        .benefit-grid {
            display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;
        }
        @media (max-width: 600px) { .benefit-grid { grid-template-columns: 1fr; } }
        .benefit-item {
            background: #f8fafc;
            border: 1px solid var(--c-border);
            border-radius: var(--radius-md); padding: 1.15rem;
            display: flex; gap: 0.85rem; align-items: flex-start;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
        }
        .benefit-item:hover {
            transform: translateY(-2px);
            border-color: rgba(37, 99, 235, 0.35);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
        }
        .benefit-item.active { border-color: rgba(37, 99, 235, 0.35); background: #eff6ff; }
        .benefit-icon {
            width: 40px; height: 40px; flex-shrink: 0; border-radius: 12px;
            background: #dbeafe; color: #2563eb;
            display: flex; align-items: center; justify-content: center;
        }
        .benefit-title { font-weight: 800; font-size: 0.88rem; color: var(--c-text); margin-bottom: 0.25rem; }
        .benefit-desc { font-size: 0.78rem; color: var(--c-text-muted); line-height: 1.55; }

        /* ════════ Project items ════════ */
        .project-item {
            padding: 1.25rem 0; border-bottom: 1px solid var(--c-border-subtle);
            display: flex; flex-direction: column; gap: 0.6rem;
        }
        .project-item:first-child { padding-top: 0; }
        .project-item:last-child { border-bottom: none; padding-bottom: 0; }
        .project-item-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 0.75rem; }
        .project-title { font-weight: 800; color: var(--c-text); font-size: 0.95rem; line-height: 1.35; }
        .project-budget { font-size: 0.92rem; font-weight: 900; color: var(--c-accent); white-space: nowrap; font-family: 'Outfit', sans-serif; }
        .project-desc { font-size: 0.82rem; color: var(--c-text-muted); line-height: 1.6; }

        /* ════════ Milestone pills ════════ */
        .milestone-pills { display: flex; gap: 0.4rem; flex-wrap: wrap; margin-top: 0.3rem; }
        .ms-pill {
            font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;
            padding: 0.22rem 0.6rem; border-radius: 99px; border: 1px solid;
        }
        .ms-pill.done    { background: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
        .ms-pill.pending { background: #f8fafc; color: var(--c-text-muted); border-color: var(--c-border); }

        /* ════════ Badges (Light theme matching home.php) ════════ */
        .badge {
            display: inline-flex; align-items: center; gap: 0.35rem;
            padding: 0.22rem 0.65rem; border-radius: 99px;
            font-size: 0.68rem; font-weight: 700; text-transform: capitalize; border: 1px solid;
        }
        .badge-dot { width: 5px; height: 5px; border-radius: 50%; }
        .badge-ongoing   { background: #fffbeb; color: #d97706; border-color: #fcd34d; }
        .badge-completed { background: #ecfdf5; color: #065f46; border-color: #6ee7b7; }
        .badge-planned   { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
        .badge-pending   { background: #f8fafc; color: #64748b; border-color: #e2e8f0; }
        .badge-recorded  { background: #ecfdf5; color: #065f46; border-color: #6ee7b7; }
        .badge-reviewed  { background: #eff6ff; color: #1e40af; border-color: #93c5fd; }
        .badge-returned  { background: #fef2f2; color: #991b1b; border-color: #fca5a5; }
        .dot-ongoing   { background: #d97706; }
        .dot-completed { background: #059669; }
        .dot-planned   { background: #2563eb; }
        .dot-pending   { background: #94a3b8; }
        .dot-recorded  { background: #059669; }
        .dot-reviewed  { background: #2563eb; }
        .dot-returned  { background: #dc2626; }

        /* ════════ Table ════════ */
        .tx-table { width: 100%; border-collapse: collapse; min-width: 560px; }
        .tx-table th {
            background: #f8fafc; padding: 0.85rem 1rem;
            font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.08em; color: var(--c-text-muted); text-align: left;
            border-bottom: 1px solid var(--c-border);
        }
        .tx-table td {
            padding: 0.95rem 1rem; font-size: 0.85rem; color: var(--c-text-sub);
            border-bottom: 1px solid var(--c-border-subtle); vertical-align: middle;
        }
        .tx-table tbody tr:last-child td { border-bottom: none; }
        .tx-table tbody tr:hover { background: #f8fafc; }
        .td-strong { font-weight: 700; color: var(--c-text); }
        .td-small  { font-size: 0.74rem; color: var(--c-text-muted); margin-top: 0.15rem; }
        .table-wrap { overflow-x: auto; }

        /* ════════ Official row ════════ */
        .official-row { display: flex; align-items: center; gap: 1rem; }
        .official-avatar {
            width: 48px; height: 48px; border-radius: 50%;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .official-name { font-weight: 800; color: var(--c-text); font-size: 1rem; }
        .official-meta { font-size: 0.78rem; color: var(--c-text-muted); margin-top: 0.15rem; }

        /* ════════ Feedback form ════════ */
        .form-field { display: grid; gap: 0.4rem; margin-bottom: 0.95rem; }
        .form-field label { font-size: 0.72rem; font-weight: 700; color: var(--c-text-muted); text-transform: uppercase; letter-spacing: 0.08em; }
        .form-field input,
        .form-field textarea {
            width: 100%; border-radius: var(--radius-sm);
            border: 1px solid var(--c-border);
            background: #f8fafc; padding: 0.75rem 0.9rem;
            font-size: 0.875rem; color: var(--c-text); outline: none;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s; font-family: inherit;
        }
        .form-field input::placeholder,
        .form-field textarea::placeholder { color: #94a3b8; }
        .form-field input:focus,
        .form-field textarea:focus {
            border-color: var(--c-accent);
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .form-field textarea { min-height: 100px; resize: vertical; }
        .btn-submit {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.45rem;
            width: 100%; padding: 0.8rem; border-radius: 12px;
            background: #0f172a; color: #ffffff;
            font-weight: 700; font-size: 0.85rem; cursor: pointer;
            border: none; transition: background 0.2s, transform 0.1s, box-shadow 0.2s; font-family: inherit;
            box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
        }
        .btn-submit:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(37, 99, 235, 0.25);
        }

        /* ════════ Comment list ════════ */
        .comment-item { padding: 0.95rem 0; border-bottom: 1px solid var(--c-border-subtle); }
        .comment-item:last-child { border-bottom: none; padding-bottom: 0; }
        .comment-author { font-weight: 800; font-size: 0.85rem; color: var(--c-text); }
        .comment-date   { font-size: 0.72rem; color: var(--c-text-muted); margin-left: 0.5rem; }
        .comment-msg    { font-size: 0.82rem; color: var(--c-text-sub); margin-top: 0.35rem; line-height: 1.6; }

        /* ════════ Empty state ════════ */
        .empty-box {
            padding: 2.5rem 1rem; text-align: center;
            color: var(--c-text-muted); font-size: 0.85rem;
            background: #f8fafc; border-radius: var(--radius-sm);
        }

        /* ════════ Section pill (matches home.php) ════════ */
        .section-pill {
            display: inline-flex; align-items: center; gap: 0.45rem;
            background: rgba(37, 99, 235, 0.1);
            border: 1px solid rgba(37, 99, 235, 0.25);
            padding: 0.3rem 0.85rem; border-radius: 99px;
            font-size: 0.7rem; font-weight: 700; color: #2563eb;
            text-transform: uppercase; letter-spacing: 0.08em;
            margin-bottom: 0.85rem;
        }
        .section-pill-dot { width: 5px; height: 5px; border-radius: 50%; background: #2563eb; }
    </style>
</head>
<body>

    <!-- ════════ Navbar ════════ -->
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="/FundSystem/" class="back-link">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Public Portal
            </a>
            <a href="/FundSystem/" class="brand">
                <div class="brand-icon">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#ffffff" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                SK FUND System
            </a>
        </div>
    </nav>

    <!-- ════════ Hero ════════ -->
    <div class="hero">
        <?php if (!empty($profile['image_path']) && file_exists($profile['image_path'])): ?>
            <img class="hero-img" src="/FundSystem/<?= htmlspecialchars(ltrim($profile['image_path'], '/'), ENT_QUOTES, 'UTF-8') ?>" alt="Barangay <?= htmlspecialchars($clean_name) ?> cover photo">
            <div class="hero-overlay"></div>
        <?php else: ?>
            <div class="hero-placeholder">
                <svg width="52" height="52" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>No cover photo uploaded yet</span>
            </div>
            <div class="hero-overlay" style="background:linear-gradient(180deg, rgba(15,23,42,0.1) 0%, rgba(15,23,42,0.85) 100%);"></div>
        <?php endif; ?>
        <div class="hero-content">
            <div class="hero-chip">
                <span class="hero-chip-dot"></span>
                Sangguniang Kabataan
            </div>
            <h1 class="hero-name">Barangay <?= htmlspecialchars($clean_name) ?></h1>
            <p class="hero-tagline">
                <?= htmlspecialchars(!empty($profile['tagline']) ? $profile['tagline'] : 'Dedicated to youth development, fund transparency, and community service.') ?>
            </p>
        </div>
    </div>

    <!-- ════════ Page body ════════ -->
    <main class="page">

        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card">
                <p class="stat-card-label">Total Projects</p>
                <p class="stat-card-value"><?= count($projects) ?></p>
                <p class="stat-card-sub">SK-funded initiatives</p>
            </div>
            <div class="stat-card">
                <p class="stat-card-label">Approved Budget</p>
                <p class="stat-card-value">
                    <?php if ($total_budget >= 1000000): ?>
                        &#8369;<?= number_format($total_budget / 1000000, 2) ?>M
                    <?php elseif ($total_budget >= 1000): ?>
                        &#8369;<?= number_format($total_budget / 1000, 0) ?>K
                    <?php else: ?>
                        &#8369;<?= number_format($total_budget, 0) ?>
                    <?php endif; ?>
                </p>
                <p class="stat-card-sub">Total approved allocation</p>
            </div>
            <div class="stat-card">
                <p class="stat-card-label">Utilization Rate</p>
                <p class="stat-card-value"><?= $utilization_rate ?>%</p>
                <p class="stat-card-sub">Fund utilization</p>
            </div>
        </div>

        <!-- Two-column content -->
        <div class="content-grid">

            <!-- Left column -->
            <div class="col">

                <!-- About -->
                <?php if (!empty($profile['description'])): ?>
                <div class="card">
                    <div class="section-pill"><span class="section-pill-dot"></span> About Barangay</div>
                    <p style="font-size:0.92rem;color:var(--c-text-sub);line-height:1.8;"><?= nl2br(htmlspecialchars($profile['description'])) ?></p>
                </div>
                <?php endif; ?>

                <!-- Community Benefits -->
                <?php if (!empty($benefits)): ?>
                <div class="card">
                    <p class="card-title">Community Benefits</p>
                    <div class="benefit-grid">
                        <?php foreach ($benefits as $b): ?>
                        <div class="benefit-item active">
                            <div class="benefit-icon">
                                <?php if ($b['icon'] === 'scholar'): ?>
                                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                <?php elseif ($b['icon'] === 'sports'): ?>
                                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 2c1.7 4 1.7 16 0 20M2 12h20"/></svg>
                                <?php elseif ($b['icon'] === 'eco'): ?>
                                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                                <?php else: ?>
                                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="benefit-title"><?= htmlspecialchars($b['title']) ?></p>
                                <p class="benefit-desc"><?= htmlspecialchars($b['description']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Projects -->
                <div class="card">
                    <p class="card-title">
                        <span>Funded Projects</span>
                        <span style="font-size:0.8rem;font-weight:700;color:var(--c-accent);background:var(--c-accent-light);padding:0.25rem 0.65rem;border-radius:99px;border:1px solid var(--c-accent-border);"><?= count($projects) ?> active</span>
                    </p>
                    <?php if (empty($projects)): ?>
                        <div class="empty-box">No projects registered for this barangay yet.</div>
                    <?php else: ?>
                        <?php foreach ($projects as $proj):
                            $ps = $proj['status'] ?? 'pending';
                        ?>
                        <div class="project-item">
                            <div class="project-item-top">
                                <span class="badge badge-<?= $ps ?>">
                                    <span class="badge-dot dot-<?= $ps ?>"></span>
                                    <?= htmlspecialchars($ps) ?>
                                </span>
                                <span class="project-budget">&#8369;<?= number_format($proj['budget'], 2) ?></span>
                            </div>
                            <p class="project-title"><?= htmlspecialchars($proj['title']) ?></p>
                            <?php if (!empty($proj['description'])): ?>
                                <p class="project-desc"><?= htmlspecialchars(mb_strimwidth($proj['description'], 0, 160, '...')) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($proj['milestones'])): ?>
                                <div class="milestone-pills">
                                    <?php foreach ($proj['milestones'] as $ms): ?>
                                        <span class="ms-pill <?= !empty($ms['date_achieved']) ? 'done' : 'pending' ?>">
                                            <?= htmlspecialchars($ms['stage']) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Transactions -->
                <div class="card">
                    <p class="card-title">
                        <span>Transaction Ledger</span>
                        <span style="font-size:0.8rem;font-weight:700;color:var(--c-text-muted);"><?= count($transactions) ?> records</span>
                    </p>
                    <?php if (empty($transactions)): ?>
                        <div class="empty-box">No transactions recorded yet.</div>
                    <?php else: ?>
                    <div class="table-wrap">
                        <table class="tx-table">
                            <thead>
                                <tr>
                                    <th>Project</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $tx):
                                    $ts = $tx['status'] ?? 'pending';
                                ?>
                                <tr>
                                    <td>
                                        <div class="td-strong"><?= htmlspecialchars($tx['project_title'] ?? 'N/A') ?></div>
                                        <div class="td-small">Ref: <?= htmlspecialchars($tx['reference_no'] ?? '—') ?></div>
                                    </td>
                                    <td style="text-transform:capitalize;"><?= htmlspecialchars($tx['type'] ?? '—') ?></td>
                                    <td class="td-strong" style="color:var(--c-accent);font-family:'Outfit',sans-serif;">&#8369;<?= number_format($tx['amount'], 2) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $ts ?>">
                                            <span class="badge-dot dot-<?= $ts ?>"></span>
                                            <?= htmlspecialchars($ts) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($tx['created_at'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>

            </div><!-- /left col -->

            <!-- Right sidebar -->
            <div class="col">

                <!-- SK Chairperson -->
                <div class="card">
                    <p class="card-title">SK Leadership</p>
                    <?php if ($sk_official): ?>
                    <div class="official-row">
                        <div class="official-avatar">
                            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="official-name"><?= htmlspecialchars($sk_official['full_name']) ?></p>
                            <?php if (!empty($sk_official['email'])): ?>
                                <p class="official-meta"><?= htmlspecialchars($sk_official['email']) ?></p>
                            <?php endif; ?>
                            <p class="official-meta" style="margin-top:0.25rem;color:var(--c-accent);font-weight:600;">SK Chairperson &middot; Brgy. <?= htmlspecialchars($clean_name) ?></p>
                        </div>
                    </div>
                    <?php else: ?>
                        <div class="empty-box" style="padding:1.5rem 0;">No SK Chairperson registered for this barangay.</div>
                    <?php endif; ?>
                </div>

                <!-- Feedback form -->
                <div class="card">
                    <p class="card-title">Citizen Feedback</p>
                    <p style="font-size:0.82rem;color:var(--c-text-muted);margin-bottom:1.15rem;line-height:1.6;">Share your feedback, suggestions, or concerns regarding SK youth programs. Your voice matters.</p>
                    <form id="brgyFeedbackForm">
                        <input type="hidden" name="barangay_name" value="<?= htmlspecialchars($clean_name) ?>">
                        <div class="form-field">
                            <label for="fbName">Your Name (optional)</label>
                            <input type="text" id="fbName" name="user_name" placeholder="Anonymous Citizen">
                        </div>
                        <div class="form-field">
                            <label for="fbMsg">Message</label>
                            <textarea id="fbMsg" name="message" placeholder="Share your suggestion or concern..." required></textarea>
                        </div>
                        <div id="brgyFbStatus" style="font-size:0.8rem;margin-bottom:0.6rem;min-height:1.2em;"></div>
                        <button type="submit" class="btn-submit" id="brgyFbBtn">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Submit Feedback
                        </button>
                    </form>
                </div>

                <!-- Existing comments -->
                <?php if (!empty($comments)): ?>
                <div class="card">
                    <p class="card-title">
                        <span>Community Voice</span>
                        <span style="font-size:0.8rem;font-weight:700;color:var(--c-text-muted);">(<?= count($comments) ?>)</span>
                    </p>
                    <?php foreach (array_slice($comments, 0, 6) as $c): ?>
                    <div class="comment-item">
                        <div>
                            <span class="comment-author"><?= htmlspecialchars($c['user_name'] ?? 'Anonymous Citizen') ?></span>
                            <span class="comment-date"><?= date('M d, Y', strtotime($c['created_at'])) ?></span>
                        </div>
                        <p class="comment-msg"><?= nl2br(htmlspecialchars($c['message'])) ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($comments) > 6): ?>
                        <p style="font-size:0.78rem;color:var(--c-text-muted);margin-top:0.75rem;text-align:center;">
                            +<?= count($comments) - 6 ?> more feedback messages
                        </p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

            </div><!-- /right col -->

        </div><!-- /content-grid -->

    </main>

    <script>
        // ── Feedback submit ─────────────────────────────
        document.getElementById('brgyFeedbackForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const statusEl = document.getElementById('brgyFbStatus');
            const btn      = document.getElementById('brgyFbBtn');
            statusEl.textContent = 'Submitting...';
            statusEl.style.color = '#2563eb';
            btn.disabled = true;

            try {
                const fd  = new FormData(this);
                const res = await fetch('/FundSystem/api/feedback', { method: 'POST', body: fd });
                const data = await res.json();

                if (data.success) {
                    Swal.fire({
                        icon: 'success', title: 'Thank you!',
                        text: 'Your feedback has been submitted to the SK Council.',
                        background: '#ffffff', color: '#0f172a',
                        confirmButtonColor: '#0f172a', confirmButtonText: 'Done'
                    });
                    this.reset();
                    statusEl.textContent = '';
                } else {
                    statusEl.textContent = data.message || 'Unable to submit.';
                    statusEl.style.color = '#dc2626';
                }
            } catch {
                statusEl.textContent = 'Connection error. Please try again.';
                statusEl.style.color = '#dc2626';
            }
            btn.disabled = false;
        });

    </script>

</body>
</html>
