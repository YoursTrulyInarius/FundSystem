<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Watch SK Fund | Public Transparency Portal</title>
    <meta name="description" content="Track SK-funded projects, inspect public transactions, and stay informed with a transparent view of how youth development programs are managed in Ramon Magsaysay.">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@700;800;900&display=swap" rel="stylesheet">

    <style>
        /*
         * ────────────────────────────────────────────────────────
         *  COLOR SYSTEM (White, Blue, Black Theme)
         *  Primary:       #000000  (Solid Black)
         *  Secondary:     #FFFFFF  (Pure White)
         *  Complementary: #2563EB  (Vibrant Blue)
         * ────────────────────────────────────────────────────────
         */
        :root {
            --c-primary:      #000000;
            --c-secondary:    #FFFFFF;
            --c-accent:       #2563EB;

            --c-bg:           #F8FAFC; /* Light slate background */
            --c-border:       #E2E8F0; /* Soft gray border */
            --c-text-dark:    #0F172A; /* Charcoal dark text */
            --c-text-muted:   #64748B; /* Muted gray text */
            --c-accent-light: #EFF6FF; /* Translucent blue */
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            margin: 0;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--c-secondary);
            color: var(--c-text-dark);
            overflow-x: hidden;
            overflow-y: hidden;
        }

        /* ─── Snap scroll ─── */
        .snap-container {
            height: 100dvh;
            overflow-y: auto;
            scroll-snap-type: y mandatory;
            scroll-behavior: smooth;
            overscroll-behavior-y: contain;
        }
        .snap-container::-webkit-scrollbar { width: 6px; }
        .snap-container::-webkit-scrollbar-track { background: var(--c-secondary); }
        .snap-container::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
        .snap-container::-webkit-scrollbar-thumb:hover { background: var(--c-accent); }

        .inner-scroll {
            min-height: 0;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .inner-scroll::-webkit-scrollbar { display: none; }

        /* ─── Navbar ─── */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 64px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--c-border);
            z-index: 50;
            display: flex;
            align-items: center;
        }
        .navbar-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 1.05rem;
            color: var(--c-primary);
            text-decoration: none;
        }
        .brand-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: var(--c-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
        }
        .nav-links a {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--c-text-muted);
            text-decoration: none;
            transition: color 0.15s;
        }
        .nav-links a:hover { color: var(--c-primary); }
        
        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.48rem 1.1rem;
            border-radius: 8px;
            background: var(--c-primary);
            color: var(--c-secondary);
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
        }
        .btn-primary:hover { background: #1E293B; transform: translateY(-1px); }

        /* ─── Hero ─── */
        .hero-section {
            background: linear-gradient(180deg, rgba(241, 245, 249, 0.95) 0%, rgba(248, 250, 252, 0.97) 45%, #ffffff 100%);
            border-bottom: 1px solid var(--c-border);
        }
        .hero-inner {
            max-width: 1040px;
            margin: 0 auto;
            padding: 0 1.5rem 2rem;
            text-align: center;
            position: relative;
        }
        .hero-inner::before {
            content: '';
            position: absolute;
            inset: 0;
            margin: auto;
            width: min(96%, 1040px);
            height: min(76%, 540px);
            border-radius: 2rem;
            background: radial-gradient(circle at top, rgba(59, 130, 246, 0.08), transparent 45%);
            pointer-events: none;
            z-index: 0;
        }
        .hero-inner > * { position: relative; z-index: 1; }
        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1rem;
            border-radius: 999px;
            background: rgba(59, 130, 246, 0.12);
            color: #1d4ed8;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .live-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: #2563eb;
            animation: blink 1.8s ease-in-out infinite;
        }
        @keyframes blink { 0%,100%{opacity:1;} 50%{opacity:0.25;} }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(3rem, 6vw, 5.2rem);
            font-weight: 900;
            line-height: 1.02;
            letter-spacing: -0.04em;
            color: #0f172a;
            margin-top: 1.6rem;
            margin-bottom: 1.2rem;
            max-width: 760px;
            margin-left: auto;
            margin-right: auto;
        }
        .hero-title span { color: #2563eb; }
        .hero-sub {
            font-size: clamp(1rem, 1.8vw, 1.15rem);
            color: #475569;
            line-height: 1.9;
            max-width: 680px;
            margin: 0 auto 2.5rem;
        }

        /* ─── Stat cards ─── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 1rem;
            max-width: 860px;
            margin: 0 auto;
        }
        @media (max-width: 820px) { .stat-grid { grid-template-columns: repeat(2, minmax(160px, 1fr)); } }
        @media (max-width: 560px) { .stat-grid { grid-template-columns: 1fr; } }
        .stat-card {
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 28px;
            padding: 1.6rem 1.5rem;
            text-align: left;
            position: relative;
            box-shadow: 0 22px 60px rgba(15, 23, 42, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-6px);
            border-color: rgba(37, 99, 235, 0.32);
            box-shadow: 0 28px 72px rgba(15, 23, 42, 0.08);
        }
        .stat-card-top {
            width: 44px; height: 44px;
            background: rgba(59, 130, 246, 0.14);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.15rem;
            box-shadow: inset 0 0 0 1px rgba(59, 130, 246, 0.1);
        }
        .stat-value {
            font-family: 'Outfit', sans-serif;
            font-size: 2.2rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1;
            letter-spacing: -0.02em;
        }
        .stat-label {
            font-size: 0.82rem;
            color: #475569;
            margin-top: 0.55rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 18px; left: 0;
            width: 5px; height: calc(100% - 36px);
            background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 999px;
            opacity: 0.95;
        }

        /* ─── Scroll hint ─── */
        .scroll-hint {
            position: absolute;
            bottom: 1.9rem; left: 50%;
            transform: translateX(-50%);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.35rem;
            opacity: 0;
            animation: fadeUp 1s 1.8s ease forwards;
        }
        .scroll-hint span { font-size: 0.7rem; letter-spacing: 0.16em; text-transform: uppercase; color: #94a3b8; }
        .scroll-mouse {
            width: 22px; height: 34px;
            border: 1.8px solid rgba(148, 163, 184, 0.7);
            border-radius: 14px;
            display: flex;
            justify-content: center;
            padding-top: 6px;
        }
        .scroll-mouse::after {
            content: '';
            width: 3px; height: 6px;
            background: #2563eb;
            border-radius: 2px;
            animation: wheel 1.8s ease-in-out infinite;
        }
        @keyframes wheel  { 0%,100%{transform:translateY(0);opacity:1;} 55%{transform:translateY(6px);opacity:0;} }
        @keyframes fadeUp { from{opacity:0;transform:translate(-50%,8px);} to{opacity:1;transform:translate(-50%,0);} }

        /* ─── Section shared ─── */
        .section-wrap {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
            width: 100%;
            height: 100%;
            min-height: 0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .section-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.8rem;
            border-radius: 99px;
            border: 1px solid var(--c-border);
            background: var(--c-accent-light);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--c-accent);
        }
        .section-pill-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--c-accent);
        }
        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--c-primary);
            letter-spacing: -0.01em;
            margin-top: 0.6rem;
            margin-bottom: 0.4rem;
        }
        .section-sub {
            font-size: 0.875rem;
            color: var(--c-text-muted);
            line-height: 1.6;
            max-width: 520px;
        }
        .section-header {
            display: flex;
            flex-direction: column;
        }
        @media (min-width: 1024px) {
            .section-header-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 1.5rem; }
        }

        /* ─── Filter controls ─── */
        .filter-control {
            background: var(--c-secondary);
            border: 1px solid var(--c-border);
            border-radius: 8px;
            color: var(--c-text-dark);
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            padding: 0.45rem 2rem 0.45rem 0.75rem;
            appearance: none;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2364748B' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.55rem center;
            background-size: 13px;
            transition: border-color 0.15s;
        }
        .filter-control:focus { outline: none; border-color: var(--c-accent); }
        .filter-control option { background: var(--c-secondary); color: var(--c-text-dark); }

        .search-wrap { position: relative; display: inline-flex; align-items: center; }
        .search-icon { position: absolute; left: 0.65rem; pointer-events: none; color: var(--c-text-muted); }
        .search-input {
            background: var(--c-secondary);
            border: 1px solid var(--c-border);
            border-radius: 8px;
            color: var(--c-text-dark);
            font-family: 'Inter', sans-serif;
            font-size: 0.85rem;
            padding: 0.45rem 0.75rem 0.45rem 2.1rem;
            width: 200px;
            transition: border-color 0.15s, width 0.2s;
        }
        .search-input::placeholder { color: var(--c-text-muted); }
        .search-input:focus { outline: none; border-color: var(--c-primary); width: 230px; }

        /* ─── Project cards ─── */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.1rem;
        }
        @media (max-width: 1024px) { .projects-grid { grid-template-columns: repeat(2,1fr); } }
        @media (max-width: 640px)  { .projects-grid { grid-template-columns: 1fr; } }

        .project-card {
            background: var(--c-secondary);
            border: 1px solid var(--c-border);
            border-radius: 12px;
            padding: 1.35rem;
            display: flex;
            flex-direction: column;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
            transition: border-color 0.2s, transform 0.2s, box-shadow 0.2s;
        }
        .project-card:hover {
            border-color: var(--c-accent);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .project-card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.9rem;
        }
        .project-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--c-primary);
            line-height: 1.35;
            margin-bottom: 0.45rem;
        }
        .project-desc {
            font-size: 0.82rem;
            color: var(--c-text-muted);
            line-height: 1.6;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .project-footer {
            border-top: 1px solid var(--c-border);
            padding-top: 0.9rem;
            margin-top: 0.9rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .project-footer-label { font-size: 0.75rem; color: var(--c-text-muted); font-weight: 600; }
        .project-footer-amount { font-size: 0.875rem; font-weight: 800; color: var(--c-accent); }

        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.62);
            backdrop-filter: blur(10px);
            z-index: 60;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .modal-backdrop.active {
            display: flex;
        }
        .project-modal {
            width: min(100%, 980px);
            max-height: calc(100vh - 3rem);
            overflow: hidden;
            border-radius: 32px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            box-shadow: 0 42px 110px rgba(15, 23, 42, 0.2);
            border: 1px solid rgba(148, 163, 184, 0.22);
            position: relative;
        }
        .project-modal-inner {
            max-height: calc(100vh - 5rem);
            overflow-y: auto;
            padding: 2rem;
            display: grid;
            gap: 1.5rem;
        }
        .project-modal-close {
            position: absolute;
            top: 1.25rem;
            right: 1.25rem;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 1px solid rgba(148, 163, 184, 0.22);
            background: rgba(255, 255, 255, 0.95);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }
        .project-modal-close:hover {
            transform: scale(1.04);
            background: rgba(248, 250, 252, 0.95);
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.08);
        }
        .project-modal-header {
            display: grid;
            gap: 1.5rem;
            margin-bottom: 0;
            grid-template-columns: 1.45fr 1fr;
            align-items: start;
        }
        @media (max-width: 860px) {
            .project-modal-header {
                grid-template-columns: 1fr;
            }
        }
        .project-modal-header-left,
        .project-modal-header-right {
            display: grid;
            gap: 1rem;
        }
        .project-modal-badge {
            width: fit-content;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.7rem 1rem;
            border-radius: 999px;
            background: rgba(59, 130, 246, 0.12);
            color: #1d4ed8;
            font-size: 0.80rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }
        .project-modal-header h2 {
            font-size: clamp(2.2rem, 3vw, 3.2rem);
            margin: 0;
            line-height: 1.02;
            color: #0f172a;
        }
        .project-modal-header .subline {
            color: #475569;
            font-size: 1rem;
            max-width: 700px;
            line-height: 1.9;
            margin: 0;
        }
        .project-modal-budget-label {
            text-transform: uppercase;
            font-size: 0.78rem;
            letter-spacing: 0.16em;
            color: #64748b;
            font-weight: 700;
        }
        .project-modal-budget-value {
            font-size: clamp(2.6rem, 4vw, 3.8rem);
            color: #0f172a;
            line-height: 1;
            font-weight: 800;
        }
        .project-modal-wide-tags {
            display: grid;
            gap: 0.85rem;
        }
        .project-modal-pill {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            min-width: 190px;
            padding: 0.95rem 1.15rem;
            background: #eef6ff;
            border: 1px solid rgba(37, 99, 235, 0.18);
            border-radius: 999px;
            color: #0f172a;
            font-size: 0.88rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        .project-modal-pill strong {
            font-size: 0.84rem;
            font-weight: 700;
            text-transform: none;
            color: #1f2937;
        }
        .project-modal .modal-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }
        @media (max-width: 840px) {
            .project-modal .modal-meta {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 620px) {
            .project-modal .modal-meta {
                grid-template-columns: 1fr;
            }
        }
        .project-modal .meta-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 1.3rem 1.4rem;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }
        .project-modal .meta-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
        }
        .project-modal .meta-card strong {
            display: block;
            margin-bottom: 0.6rem;
            color: #475569;
            font-size: 0.72rem;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }
        .project-modal .meta-card span {
            display: block;
            font-size: 1rem;
            color: #0f172a;
            font-weight: 700;
            line-height: 1.4;
        }
        .project-modal .section-block {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 28px;
            padding: 1.75rem;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.05);
        }
        .project-modal .section-block h3 {
            margin-bottom: 1rem;
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
        }
        .project-modal .section-block p,
        .project-modal .section-block li {
            color: #475569;
            line-height: 1.8;
            font-size: 0.96rem;
        }
        .project-modal .section-block ul {
            padding-left: 1.25rem;
            margin: 0;
            list-style-type: disc;
        }
        .project-modal .section-block li {
            margin-bottom: 0.75rem;
        }
        .project-modal .project-meta-group {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        @media (max-width: 840px) {
            .project-modal .project-meta-group {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 560px) {
            .project-modal .project-meta-group {
                grid-template-columns: 1fr;
            }
        }

        /* ─── Status badges ─── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.65rem;
            border-radius: 99px;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: capitalize;
            border: 1px solid var(--c-border);
            background: var(--c-bg);
            color: var(--c-text-dark);
        }
        .badge-dot { width: 5px; height: 5px; border-radius: 50%; }

        .badge-ongoing   { background: #FFFBEB; color: #D97706; border-color: #FCD34D; }
        .badge-completed { background: #ECFDF5; color: #065F46; border-color: #6EE7B7; }
        .badge-pending   { background: #EFF6FF; color: #1E40AF; border-color: #93C5FD; }
        .badge-recorded  { background: #ECFDF5; color: #065F46; border-color: #6EE7B7; }
        .badge-reviewed  { background: #EFF6FF; color: #1E40AF; border-color: #93C5FD; }
        .badge-returned  { background: #FEE2E2; color: #991B1B; border-color: #FCA5A5; }

        .dot-ongoing   { background: #D97706; }
        .dot-completed { background: #059669; }
        .dot-pending   { background: #2563EB; }
        .dot-recorded  { background: #059669; }
        .dot-reviewed  { background: #2563EB; }
        .dot-returned  { background: #DC2626; }

        /* ─── Registry table ─── */
        .registry-wrap {
            background: var(--c-secondary);
            border: 1px solid var(--c-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }
        .registry-table { width: 100%; border-collapse: collapse; }
        .registry-table thead th {
            background: var(--c-bg);
            border-bottom: 1px solid var(--c-border);
            padding: 0.85rem 1.4rem;
            text-align: left;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--c-text-muted);
            white-space: nowrap;
        }
        .registry-table tbody td {
            padding: 0.95rem 1.4rem;
            font-size: 0.875rem;
            border-bottom: 1px solid var(--c-border);
            color: var(--c-text-muted);
            vertical-align: middle;
        }
        .registry-table tbody tr:last-child td { border-bottom: none; }
        .registry-table tbody tr { transition: background 0.1s; }
        .registry-table tbody tr:hover { background: var(--c-bg); }
        .td-primary { color: var(--c-primary) !important; font-weight: 600; }
        .td-meta    { font-size: 0.75rem; color: var(--c-text-muted); margin-top: 0.15rem; }

        /* ─── Empty state ─── */
        .empty-state {
            padding: 4rem 1.5rem;
            text-align: center;
        }
        .empty-icon {
            width: 48px; height: 48px;
            margin: 0 auto 1rem;
            color: var(--c-primary);
        }
        .empty-text { font-size: 0.875rem; color: var(--c-text-muted); font-weight: 500; }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 0;
            align-content: center;
        }
        .summary-section-wrap {
            justify-content: center;
            height: auto;
            overflow: visible;
        }
        @media (max-width: 980px) { .summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 640px) { .summary-grid { grid-template-columns: 1fr; } }
        .summary-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 1.35rem 1.5rem;
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.06);
        }
        .summary-card strong {
            display: block;
            margin-bottom: 0.65rem;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #64748b;
        }
        .summary-card .summary-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
        }
        .summary-card .summary-meta {
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.75;
            margin-top: 0.55rem;
        }
        .feedback-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.05);
            padding: 2rem;
            margin-top: 1.5rem;
        }
        .feedback-card h3 {
            margin-bottom: 0.6rem;
            font-size: 1.5rem;
            color: #0f172a;
        }
        .feedback-card p {
            margin: 0;
            color: #475569;
            line-height: 1.8;
        }
        .feedback-grid {
            display: grid;
            gap: 1.5rem;
            grid-template-columns: 1.2fr 0.8fr;
        }
        @media (max-width: 900px) { .feedback-grid { grid-template-columns: 1fr; } }
        .feedback-field {
            display: grid;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        .feedback-field label {
            font-weight: 700;
            color: #334155;
            font-size: 0.88rem;
        }
        .feedback-field input,
        .feedback-field select,
        .feedback-field textarea {
            width: 100%;
            border-radius: 16px;
            border: 1px solid #cbd5e1;
            background: #f8fafc;
            padding: 0.95rem 1rem;
            font-size: 0.95rem;
            color: #0f172a;
            outline: none;
            transition: border-color 0.2s ease;
        }
        .feedback-field input:focus,
        .feedback-field select:focus,
        .feedback-field textarea:focus {
            border-color: #2563eb;
        }
        .feedback-field textarea {
            min-height: 160px;
            resize: vertical;
        }
        .feedback-footer {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-top: 1rem;
        }
        .feedback-footer .feedback-message {
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.7;
            flex: 1;
        }
        .feedback-footer .btn-primary {
            min-width: 180px;
        }

        /* ─── Footer ─── */
        .site-footer {
            padding: 1.2rem 0 0;
        }
        .site-footer-card {
            width: min(100%, 980px);
            margin: 0 auto;
            border: 1px solid var(--c-border);
            border-radius: 1.1rem;
            background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            padding: 1.1rem 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            position: relative;
            overflow: hidden;
        }
        .site-footer-card::before {
            content: '';
            position: absolute;
            inset: 0 auto 0 0;
            width: 3px;
            background: linear-gradient(180deg, var(--c-accent), #93c5fd);
        }
        .site-footer-brand,
        .site-footer-meta {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }
        .site-footer-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            width: max-content;
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            background: rgba(37, 99, 235, 0.1);
            color: var(--c-accent);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .site-footer-card p {
            margin: 0;
            font-size: 0.82rem;
            color: var(--c-text-muted);
            line-height: 1.5;
        }
        .site-footer-title {
            color: var(--c-primary);
            font-weight: 700;
            font-size: 0.95rem;
        }
        @media (min-width: 640px) {
            .site-footer-card {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
                padding: 1.15rem 1.4rem;
            }
        }
    </style>
</head>

<body>

    <!-- ════════════════════ NAVBAR ════════════════════ -->
    <nav class="navbar">
        <div class="navbar-inner">
            <a href="#" class="brand nav-brand opacity-0">
                <div class="brand-icon">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#FFFFFF" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                Watch SK Fund
            </a>

            <ul class="nav-links">
                <li><a href="#hero"     class="nav-link opacity-0">Home</a></li>
                <li><a href="#projects" class="nav-link opacity-0">Active Projects</a></li>
                <li><a href="#registry" class="nav-link opacity-0">Registry</a></li>
                <li>
                    <a href="login" class="btn-primary nav-btn opacity-0">
                        <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Login
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- ════════════════════ SNAP CONTAINER ════════════════════ -->
    <main class="snap-container" id="main-scroll">

        <!-- ─── HERO ─── -->
        <section id="hero" class="snap-start h-screen hero-section flex flex-col justify-center items-center relative pt-16">
            <div class="hero-inner">

                <div class="live-badge hero-badge opacity-0">
                    <span class="live-dot"></span>
                    Open · Verified · Citizen-Friendly
                </div>

                <h1 class="hero-title hero-title-el opacity-0">
                    Public <span>Transparency</span><br>Portal
                </h1>

                <p class="hero-sub hero-sub-el opacity-0">
                    Follow SK-funded projects, inspect public transactions, and stay informed with a clean view of how youth development programs are managed across the municipality.
                </p>

                <!-- Stats -->
                <div class="stat-grid hero-stats opacity-0">

                    <!-- Tracked projects -->
                    <div class="stat-card">
                        <div class="stat-card-top">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <p class="stat-value"><?= $total_projects ?? 0 ?></p>
                        <p class="stat-label">Tracked projects</p>
                    </div>

                    <!-- Transactions -->
                    <div class="stat-card">
                        <div class="stat-card-top">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <p class="stat-value"><?= $recorded_transactions ?? 0 ?></p>
                        <p class="stat-label">Recorded transactions</p>
                    </div>

                    <!-- Barangays -->
                    <div class="stat-card">
                        <div class="stat-card-top">
                            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <p class="stat-value"><?= $barangay_count ?? 0 ?></p>
                        <p class="stat-label">Barangays covered</p>
                    </div>

                </div>
            </div>

            <!-- Scroll indicator -->
            <div class="scroll-hint">
                <div class="scroll-mouse"></div>
                <span>Scroll</span>
            </div>
        </section>


        <!-- ─── ACTIVE PROJECTS ─── -->
        <section id="projects" class="snap-start h-screen pt-20 pb-4" style="background: var(--c-bg);">
            <div class="section-wrap">

                <!-- Header row -->
                <div class="section-header mb-6 shrink-0 section-hdr opacity-0">
                    <div class="section-header-row">
                        <div>
                            <div class="section-pill">
                                <span class="section-pill-dot"></span>
                                Active Projects
                            </div>
                            <h2 class="section-title">SK-Funded Projects</h2>
                            <p class="section-sub">A clear view of youth programs and community initiatives currently being delivered across the municipality.</p>
                        </div>
                        <div class="shrink-0 mt-4 lg:mt-0">
                            <select id="projectBarangayFilter" class="filter-control">
                                <option value="">All Barangays</option>
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
                </div>

                <!-- Cards -->
                <div class="inner-scroll flex-1 overflow-y-auto pb-4 pr-1">
                    <div class="projects-grid">
                        <?php if (empty($projects)): ?>
                            <div class="col-span-full empty-state">
                                <svg class="empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                                <p class="empty-text">No active projects yet.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($projects as $proj):
                                $s = $proj['status'] ?? 'pending';
                            ?>
                                <div class="project-card opacity-0"
                                     data-barangay="<?= htmlspecialchars($proj['barangay_name'] ?? '') ?>">

                                    <div class="project-card-top">
                                        <span class="badge badge-<?= $s ?>">
                                            <span class="badge-dot dot-<?= $s ?>"></span>
                                            <?= htmlspecialchars($s) ?>
                                        </span>
                                        <span style="font-size:0.72rem;color:var(--c-text-muted);font-weight:500;">
                                            <?= htmlspecialchars($proj['barangay_name'] ?? 'N/A') ?>
                                        </span>
                                    </div>

                                    <p class="project-title"><?= htmlspecialchars($proj['title']) ?></p>
                                    <p class="project-desc"><?= htmlspecialchars($proj['description'] ?? 'No description provided.') ?></p>

                                    <div class="project-footer">
                                        <span class="project-footer-label">Approved Budget</span>
                                        <span class="project-footer-amount">₱<?= number_format($proj['budget'], 2) ?></span>
                                    </div>
                                    <div class="mt-4 text-right">
                                        <?php
                                            $projectData = json_encode([
                                                'id' => $proj['id'],
                                                'title' => $proj['title'],
                                                'description' => $proj['description'] ?? 'No description provided.',
                                                'budget' => number_format($proj['budget'], 2),
                                                'status' => $proj['status'],
                                                'barangay' => $proj['barangay_name'] ?? 'N/A',
                                                'owner' => $proj['project_owner'] ?? 'SK Barangay Representative',
                                                'abyip_code' => $proj['abyip_code'] ?? 'N/A',
                                                'budget_category' => $proj['budget_category'] ?? 'N/A',
                                                'created_at' => date('M d, Y', strtotime($proj['created_at']))
                                            ], JSON_HEX_APOS | JSON_HEX_QUOT);
                                        ?>
                                        <button type="button" class="view-details-btn inline-flex items-center justify-center px-4 py-2 rounded-full bg-slate-900 text-white text-sm font-semibold hover:bg-slate-800 transition" data-project='<?= htmlspecialchars($projectData, ENT_QUOTES, 'UTF-8') ?>'>View details</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Empty filter state -->
                    <div id="noProjectsRow" style="display:none;" class="empty-state">
                        <svg class="empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <p class="empty-text">No projects found for this barangay.</p>
                    </div>
                </div>

            </div>
        </section>

        <!-- ─── Barangay Summary ─── -->
        <section id="overview" class="snap-start py-8 md:py-10" style="background: var(--c-bg);">
            <div class="section-wrap summary-section-wrap">
                <div class="summary-grid opacity-0">
                    <?php if (!empty($barangaySummaries)): ?>
                        <?php foreach ($barangaySummaries as $summary): ?>
                            <div class="summary-card">
                                <strong><?= htmlspecialchars($summary['barangay_name']) ?></strong>
                                <div class="summary-value"><?= (int)$summary['project_count'] ?> projects</div>
                                <div class="summary-meta">
                                    Total budget approved: ₱<?= number_format($summary['total_budget'], 2) ?><br>
                                    Recorded utilization: ₱<?= number_format($summary['recorded_amount'], 2) ?><br>
                                    Pending utilization: ₱<?= number_format($summary['pending_amount'], 2) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="summary-card">
                            <strong>No barangay summary found</strong>
                            <div class="summary-meta">There are currently no registered projects or fund utilization records available.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- ─── Project Details Modal ─── -->
        <div id="projectDetailModal" class="modal-backdrop" aria-hidden="true">
            <div class="project-modal">
                <button id="projectModalClose" type="button" class="project-modal-close" aria-label="Close project details">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
                        <div class="project-modal-inner">
                    <div class="project-modal-header">
                        <div class="project-modal-header-left">
                            <span class="project-modal-badge" id="modalProjectStatus">Status</span>
                            <h2 id="modalProjectTitle">Project Title</h2>
                            <p class="subline" id="modalProjectSubtitle">Project overview in a short, easy-to-read summary.</p>
                        </div>
                        <div class="project-modal-header-right">
                            <div class="project-modal-budget-label">Approved budget</div>
                            <div class="project-modal-budget-value" id="modalProjectBudget">₱0.00</div>
                            <div class="project-modal-wide-tags">
                                <span class="project-modal-pill">ABYIP <strong id="modalProjectAbyip">N/A</strong></span>
                                <span class="project-modal-pill">CATEGORY <strong id="modalProjectBudgetCategory">N/A</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-meta project-meta-group">
                        <div class="meta-card">
                            <strong>Barangay</strong>
                            <span id="modalProjectBarangay">N/A</span>
                        </div>
                        <div class="meta-card">
                            <strong>SK Owner</strong>
                            <span id="modalProjectOwner">N/A</span>
                        </div>
                        <div class="meta-card">
                            <strong>Created on</strong>
                            <span id="modalProjectCreatedAt">N/A</span>
                        </div>
                    </div>

                    <div class="section-block">
                        <h3>Project Overview</h3>
                        <p id="modalProjectDescription">Project description goes here.</p>
                    </div>

                    <div class="section-block">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <h3>Share feedback</h3>
                            <button type="button" id="toggleFeedbackFormBtn" class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-3 py-1.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition">
                                Add feedback
                            </button>
                        </div>
                        <div id="projectFeedbackPanel" class="hidden rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-sm text-slate-600 mb-3">Your feedback will be submitted anonymously so the community can speak freely.</p>
                            <form id="projectFeedbackForm" class="space-y-3">
                                <input type="hidden" name="project_id" id="feedbackProjectId">
                                <input type="hidden" name="user_name" value="Anonymous">
                                <div class="feedback-field">
                                    <label for="feedbackMessageModal">Message</label>
                                    <textarea id="feedbackMessageModal" name="message" placeholder="Share your concern or suggestion about this project" required></textarea>
                                </div>
                                <div class="feedback-footer">
                                    <div id="projectFeedbackStatus" class="feedback-message"></div>
                                    <button type="submit" class="btn-primary">Submit anonymous feedback</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ─── OFFICIAL REGISTRY ─── -->
        <section id="registry" class="snap-start h-screen pt-20 pb-4" style="background: var(--c-bg);">
            <div class="section-wrap">

                <!-- Header row -->
                <div class="mb-5 shrink-0 section-hdr-2 opacity-0">
                    <div class="section-header-row">
                        <div>
                            <div class="section-pill">
                                <span class="section-pill-dot"></span>
                                Public Registry
                            </div>
                            <h2 class="section-title">Watch SK Fund Registry</h2>
                            <p class="section-sub">A structured public ledger of financial transactions, organized by barangay and reference number.</p>
                        </div>
                        <div class="flex items-center gap-3 shrink-0 mt-4 lg:mt-0">
                            <div class="search-wrap">
                                <svg class="search-icon" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input type="text" id="txSearch" placeholder="Search transactions…" class="search-input">
                            </div>
                            <select id="barangayFilter" class="filter-control">
                                <option value="">All Barangays</option>
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
                </div>

                <!-- Table -->
                <div class="inner-scroll flex-1 overflow-y-auto pr-1 registry-table-el opacity-0">
                    <div class="registry-wrap">
                        <table class="registry-table">
                            <thead>
                                <tr>
                                    <th>Barangay</th>
                                    <th>Project / Transaction</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($transactions)): ?>
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-state">
                                                <svg class="empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                <p class="empty-text">No transactions available yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($transactions as $tx):
                                        $ts = $tx['status'] ?? 'pending';
                                    ?>
                                        <tr class="registry-row"
                                            data-barangay="<?= htmlspecialchars($tx['barangay_name']) ?>">

                                            <td class="td-primary"><?= htmlspecialchars($tx['barangay_name']) ?></td>

                                            <td>
                                                <div class="td-primary"><?= htmlspecialchars($tx['project_title']) ?></div>
                                                <div class="td-meta capitalize">
                                                    <?= htmlspecialchars($tx['type']) ?> &nbsp;·&nbsp; Ref: <?= htmlspecialchars($tx['reference_no']) ?>
                                                </div>
                                            </td>

                                            <td class="td-primary">₱<?= number_format($tx['amount'], 2) ?></td>

                                            <td>
                                                <span class="badge badge-<?= $ts ?>">
                                                    <span class="badge-dot dot-<?= $ts ?>"></span>
                                                    <?= htmlspecialchars($ts) ?>
                                                </span>
                                            </td>

                                            <td><?= date('M d, Y', strtotime($tx['created_at'])) ?></td>
                                        </tr>
                                    <?php endforeach; ?>

                                    <tr id="noResultsRow" style="display:none;">
                                        <td colspan="5">
                                            <div class="empty-state">
                                                <svg class="empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                                </svg>
                                                <p class="empty-text">No transactions match your search.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="site-footer registry-footer opacity-0">
                    <div class="site-footer-card">
                        <div class="site-footer-brand">
                            <span class="site-footer-chip">Public Accountability</span>
                            <p>© 2026 Municipality of Ramon Magsaysay</p>
                        </div>
                        <div class="site-footer-meta">
                            <p class="site-footer-title">Watch SK Fund System</p>
                            <p>All data is publicly available for citizen accountability.</p>
                        </div>
                    </div>
                </footer>

            </div>
        </section>

    </main>


    <!-- ════════════════════ JAVASCRIPT ════════════════════ -->
    <script>
        // ── Registry filter ──────────────────────────────
        function filterRegistry() {
            const searchVal   = document.getElementById('txSearch').value.toLowerCase();
            const barangayVal = document.getElementById('barangayFilter').value.toLowerCase();
            const rows        = document.querySelectorAll('.registry-row');
            let visible       = 0;

            rows.forEach(row => {
                const brgy   = row.getAttribute('data-barangay').toLowerCase();
                const text   = row.innerText.toLowerCase();
                const match  = (!barangayVal || brgy.includes(barangayVal))
                            && (!searchVal   || text.includes(searchVal));
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            const noResults = document.getElementById('noResultsRow');
            if (noResults) noResults.style.display = visible === 0 ? '' : 'none';
        }

        document.getElementById('barangayFilter').addEventListener('change', filterRegistry);
        document.getElementById('txSearch').addEventListener('input', filterRegistry);

        // ── Projects filter ──────────────────────────────
        function filterProjects() {
            const barangayVal = document.getElementById('projectBarangayFilter').value.toLowerCase();
            const cards       = document.querySelectorAll('.project-card');
            let visible       = 0;

            cards.forEach(card => {
                const brgy  = (card.getAttribute('data-barangay') || '').toLowerCase();
                const match = !barangayVal || brgy.includes(barangayVal);
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            const noProjects = document.getElementById('noProjectsRow');
            if (noProjects) noProjects.style.display = visible === 0 ? 'block' : 'none';
        }

        const projFilter = document.getElementById('projectBarangayFilter');
        if (projFilter) projFilter.addEventListener('change', filterProjects);

        // ── Anime.js animations ──────────────────────────
        document.addEventListener('DOMContentLoaded', () => {

            // Navbar entrance
            anime.timeline({ easing: 'easeOutQuart' })
                .add({ targets: '.nav-brand', opacity:[0,1], translateX:[-16,0], duration:600 })
                .add({ targets: '.nav-link',  opacity:[0,1], translateY:[-6,0], delay:anime.stagger(70), duration:450 }, '-=400')
                .add({ targets: '.nav-btn',   opacity:[0,1], translateY:[6,0],  duration:450 }, '-=250');

            // Hero entrance
            anime.timeline({ easing: 'easeOutExpo' })
                .add({ targets: '.hero-badge',   opacity:[0,1], translateY:[12,0], duration:600, delay:150 })
                .add({ targets: '.hero-title-el',opacity:[0,1], translateY:[36,0], duration:800, delay:50  }, '-=350')
                .add({ targets: '.hero-sub-el',  opacity:[0,1], translateY:[24,0], duration:700              }, '-=600')
                .add({ targets: '.hero-stats',   opacity:[0,1], translateY:[20,0], duration:700              }, '-=550');

            // Scroll-snap section observer
            // IMPORTANT: The scrolling element is .snap-container (not the viewport),
            // so we must set it as the IntersectionObserver root.
            const scrollContainer = document.getElementById('main-scroll');
            const scrollKey = 'public-transparency-scroll';
            let scrollSaveScheduled = false;

            const restoreScroll = () => {
                const saved = localStorage.getItem(scrollKey);
                if (!saved) return;
                const value = parseInt(saved, 10);
                if (!Number.isNaN(value)) {
                    window.requestAnimationFrame(() => {
                        scrollContainer.scrollTop = value;
                    });
                }
            };
            const saveScroll = () => {
                localStorage.setItem(scrollKey, String(scrollContainer.scrollTop));
                scrollSaveScheduled = false;
            };
            const scheduleSaveScroll = () => {
                if (scrollSaveScheduled) return;
                scrollSaveScheduled = true;
                window.requestAnimationFrame(saveScroll);
            };

            restoreScroll();
            window.addEventListener('load', restoreScroll);
            scrollContainer.addEventListener('scroll', scheduleSaveScroll);

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        if (entry.target.id === 'projects') {
                            anime({ targets: '.section-hdr',   opacity:[0,1], translateX:[-32,0], duration:650, easing:'easeOutQuart' });
                            anime({ targets: '.project-card',  opacity:[0,1], translateY:[24,0],  delay:anime.stagger(70), duration:600, easing:'easeOutQuart' });
                            observer.unobserve(entry.target);
                        }

                        if (entry.target.id === 'registry') {
                            anime({ targets: '.section-hdr-2',     opacity:[0,1], translateX:[-32,0], duration:650, easing:'easeOutQuart' });
                            anime({ targets: '.registry-table-el', opacity:[0,1], translateY:[24,0],  duration:650, delay:100, easing:'easeOutQuart' });
                            anime({ targets: '.registry-footer',   opacity:[0,1], duration:700,        delay:450,    easing:'linear' });
                            observer.unobserve(entry.target);
                        }
                    }
                });
            }, {
                root: scrollContainer,
                threshold: 0.05
            });

            document.querySelectorAll('section').forEach(s => observer.observe(s));

            // ── Project details modal ─────────────────────
            const modalBackdrop = document.getElementById('projectDetailModal');
            const modalClose = document.getElementById('projectModalClose');
            const modalTitle = document.getElementById('modalProjectTitle');
            const modalSubtitle = document.getElementById('modalProjectSubtitle');
            const modalDescription = document.getElementById('modalProjectDescription');
            const modalOwner = document.getElementById('modalProjectOwner');
            const modalBarangay = document.getElementById('modalProjectBarangay');
            const modalBudget = document.getElementById('modalProjectBudget');
            const modalStatus = document.getElementById('modalProjectStatus');
            const modalCode = document.getElementById('modalProjectAbyip');
            const modalCategory = document.getElementById('modalProjectBudgetCategory');
            const modalCreated = document.getElementById('modalProjectCreatedAt');

            const summarize = (text, limit = 120) => {
                if (!text) { return 'No summary available.'; }
                return text.length > limit ? text.slice(0, limit).trim() + '...' : text;
            };

            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const projectData = JSON.parse(button.getAttribute('data-project'));
                    modalTitle.textContent = projectData.title;
                    modalSubtitle.textContent = summarize(projectData.description);
                    modalDescription.textContent = projectData.description;
                    modalOwner.textContent = projectData.owner;
                    modalBarangay.textContent = projectData.barangay;
                    modalBudget.textContent = '₱' + projectData.budget;
                    modalStatus.textContent = projectData.status;
                    modalCode.textContent = projectData.abyip_code;
                    modalCategory.textContent = projectData.budget_category;
                    modalCreated.textContent = projectData.created_at;
                    if (feedbackProjectId) {
                        feedbackProjectId.value = projectData.id;
                    }
                    if (projectFeedbackStatus) {
                        projectFeedbackStatus.textContent = '';
                    }
                    if (feedbackPanel) {
                        feedbackPanel.classList.add('hidden');
                    }
                    modalBackdrop.classList.add('active');
                });
            });

            function closeProjectModal() {
                modalBackdrop.classList.remove('active');
            }
            modalClose.addEventListener('click', closeProjectModal);
            modalBackdrop.addEventListener('click', (event) => {
                if (event.target === modalBackdrop) {
                    closeProjectModal();
                }
            });

            const feedbackToggle = document.getElementById('toggleFeedbackFormBtn');
            const feedbackPanel = document.getElementById('projectFeedbackPanel');
            const feedbackProjectId = document.getElementById('feedbackProjectId');
            const projectFeedbackForm = document.getElementById('projectFeedbackForm');
            const projectFeedbackStatus = document.getElementById('projectFeedbackStatus');

            if (feedbackToggle && feedbackPanel) {
                feedbackToggle.addEventListener('click', () => {
                    feedbackPanel.classList.toggle('hidden');
                });
            }

            if (projectFeedbackForm && projectFeedbackStatus) {
                projectFeedbackForm.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    projectFeedbackStatus.textContent = 'Submitting feedback...';
                    projectFeedbackStatus.style.color = '#2563eb';

                    const formData = new FormData(projectFeedbackForm);
                    try {
                        const response = await fetch('api/feedback', {
                            method: 'POST',
                            body: formData
                        });
                        const result = await response.json();

                        if (result.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thank you!',
                                text: 'Your feedback has been submitted successfully.',
                                confirmButtonColor: '#2563eb',
                                confirmButtonText: 'Got it'
                            });
                            const messageField = document.getElementById('feedbackMessageModal');
                            if (messageField) {
                                messageField.value = '';
                            }
                            projectFeedbackStatus.textContent = '';
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: result.message || 'Unable to submit feedback. Please try again.',
                                confirmButtonColor: '#2563eb',
                                confirmButtonText: 'Try again'
                            });
                            projectFeedbackStatus.textContent = result.message || 'Unable to submit feedback.';
                            projectFeedbackStatus.style.color = '#991b1b';
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Connection Error',
                            text: 'Unable to submit feedback. Please check your connection and try again.',
                            confirmButtonColor: '#2563eb',
                            confirmButtonText: 'Okay'
                        });
                        projectFeedbackStatus.textContent = 'Unable to submit feedback. Please try again later.';
                        projectFeedbackStatus.style.color = '#991b1b';
                    }
                });
            }
        });
    </script>

</body>
</html>
