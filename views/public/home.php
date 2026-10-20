<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK FUND System | Public Transparency Portal</title>
    <meta name="description" content="Track SK-funded projects, inspect public transactions, and stay informed with a transparent view of how youth development programs are managed in Ramon Magsaysay.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/FundSystem/assets/css/mobile-responsive.css">
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
            background: #ffffff;
            color: var(--c-text-dark);
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* ─── Natural Page Container ─── */
        .snap-container {
            min-height: 100vh;
            overflow-y: visible;
            scroll-behavior: smooth;
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
            height: 68px;
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
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
            gap: 0.75rem;
            text-decoration: none;
        }
        .brand-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
        }
        .nav-links a {
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--c-text-muted);
            text-decoration: none;
            transition: color 0.15s;
        }
        .nav-links a:hover { color: #2563eb; }
        
        .btn-login {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.52rem 1.35rem;
            border-radius: 999px;
            background: #2563eb;
            color: #ffffff;
            font-size: 0.86rem;
            font-weight: 700;
            letter-spacing: 0.01em;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .btn-login:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.4);
            color: #ffffff;
        }
        .nav-links a.btn-login { color: #ffffff; }
        .nav-toggle {
            display: none;
            width: 2.75rem;
            height: 2.75rem;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--c-border);
            border-radius: 0.75rem;
            background: #ffffff;
            color: var(--c-text-dark);
            cursor: pointer;
        }
        @media (max-width: 720px) {
            .navbar { height: 64px; }
            .navbar-inner { padding: 0 1rem; }
            .nav-toggle { display: inline-flex; flex-shrink: 0; }
            .nav-links {
                display: none;
                position: absolute;
                top: calc(100% + 0.5rem);
                right: 1rem;
                width: min(18rem, calc(100vw - 2rem));
                flex-direction: column;
                align-items: stretch;
                gap: 0;
                padding: 0.5rem;
                border: 1px solid var(--c-border);
                border-radius: 1rem;
                background: #ffffff;
                box-shadow: 0 16px 40px rgba(15, 23, 42, 0.14);
            }
            .nav-links.is-open { display: flex; }
            .nav-links li a:not(.btn-login) {
                display: flex;
                min-height: 2.75rem;
                align-items: center;
                padding: 0.65rem 0.85rem;
            }
            .nav-links .btn-login {
                display: flex;
                min-height: 2.75rem;
                justify-content: center;
                margin-top: 0.25rem;
            }
        }

        /* ─── Hero (Modern Linear / Stripe Civic Architecture) ─── */
        .hero-section {
            background: radial-gradient(ellipse 90% 55% at 50% -10%, rgba(37, 99, 235, 0.09), transparent 75%),
                        linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
            border-bottom: 1px solid var(--c-border);
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(148, 163, 184, 0.2) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            mask-image: radial-gradient(ellipse 65% 55% at 50% 35%, #000 60%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 65% 55% at 50% 35%, #000 60%, transparent 100%);
        }
        .hero-inner {
            max-width: 1040px;
            margin: 0 auto;
            padding: 1.25rem 1.5rem 1.5rem;
            text-align: center;
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.4rem 1.1rem;
            border-radius: 999px;
            background: rgba(239, 246, 255, 0.95);
            border: 1px solid rgba(191, 219, 254, 0.9);
            color: #1d4ed8;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            box-shadow: 0 2px 10px rgba(37, 99, 235, 0.06);
            backdrop-filter: blur(8px);
        }
        .live-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse-ring {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.75; }
        }

        .hero-title {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(2.2rem, 4.5vw, 3.8rem);
            font-weight: 900;
            line-height: 1.12;
            letter-spacing: -0.035em;
            color: #0f172a;
            margin-top: 1.25rem;
            margin-bottom: 0.9rem;
            max-width: 820px;
        }
        .hero-title span {
            color: #2563eb;
            background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #38bdf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero-sub {
            font-size: clamp(0.92rem, 1.3vw, 1.05rem);
            color: #475569;
            line-height: 1.7;
            max-width: 660px;
            margin: 0 auto 1.35rem;
            font-weight: 400;
        }

        /* ─── Hero CTAs ─── */
        .hero-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.85rem;
            margin-bottom: 1.75rem;
            flex-wrap: wrap;
        }
        .hero-cta-secondary {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.65rem 1.35rem;
            border-radius: 12px;
            background: #ffffff;
            color: #0f172a;
            font-size: 0.85rem;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .hero-cta-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            color: #2563eb;
        }

        /* ─── Hero Civic Hub Bento Card ─── */
        .hero-dashboard-card {
            width: 100%;
            max-width: 860px;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.95);
            border-radius: 22px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.05), 0 0 0 1px rgba(37, 99, 235, 0.04);
            overflow: hidden;
            text-align: left;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .hero-dashboard-card:hover {
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(37, 99, 235, 0.12);
        }
        .dashboard-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1.35rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.75rem;
        }
        .dashboard-card-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }
        @media (max-width: 720px) {
            .dashboard-card-metrics {
                grid-template-columns: 1fr;
            }
        }
        .metric-block {
            padding: 1.15rem 1.4rem;
            position: relative;
            transition: background 0.2s;
        }
        .metric-block:hover {
            background: #fafcff;
        }
        .metric-block:not(:last-child) {
            border-right: 1px solid #f1f5f9;
        }
        @media (max-width: 720px) {
            .metric-block:not(:last-child) {
                border-right: none;
                border-bottom: 1px solid #f1f5f9;
            }
        }
        .metric-icon-wrap {
            width: 36px; height: 36px;
            border-radius: 10px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.65rem;
            color: #2563eb;
        }
        .metric-val {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 900;
            color: #0f172a;
            line-height: 1;
            letter-spacing: -0.025em;
        }
        .metric-lbl {
            font-size: 0.82rem;
            font-weight: 800;
            color: #1e293b;
            margin-top: 0.4rem;
        }
        .metric-sub {
            font-size: 0.72rem;
            color: #64748b;
            margin-top: 0.15rem;
            line-height: 1.4;
        }

        /* ─── Hero Trust Chips ─── */
        .hero-trust-chips {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }
        .trust-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.28rem 0.8rem;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #334155;
            font-size: 0.74rem;
            font-weight: 600;
            box-shadow: 0 1px 4px rgba(15, 23, 42, 0.03);
        }

        #hero.hero-section {
            min-height: 100vh;
            min-height: 100svh;
            padding: 5.5rem 0 2rem;
        }
        #hero .hero-inner {
            padding: 0.5rem 1.5rem 0.75rem;
        }
        #hero .hero-title {
            font-size: clamp(2rem, 3.6vw, 3.4rem);
            line-height: 1.08;
            margin: 0.7rem 0 0.6rem;
        }
        #hero .hero-sub {
            font-size: clamp(0.88rem, 1.1vw, 1rem);
            line-height: 1.5;
            margin-bottom: 0.9rem;
        }
        #hero .hero-trust-chips {
            gap: 0.55rem;
            margin-bottom: 0.9rem;
        }
        #hero .trust-chip {
            padding: 0.24rem 0.65rem;
            font-size: 0.7rem;
        }
        #hero .hero-actions {
            margin-bottom: 1rem;
        }
        #hero .hero-cta-secondary {
            padding: 0.55rem 1.1rem;
        }
        #hero .dashboard-card-top {
            padding: 0.55rem 1rem;
        }
        #hero .metric-block {
            padding: 0.8rem 1rem;
        }
        #hero .metric-icon-wrap {
            width: 32px;
            height: 32px;
            margin-bottom: 0.4rem;
        }
        #hero .metric-val {
            font-size: 1.7rem;
        }
        #hero .metric-lbl {
            font-size: 0.78rem;
            margin-top: 0.25rem;
        }
        #hero .metric-sub {
            font-size: 0.68rem;
        }
        #hero .dashboard-card-footer {
            padding: 0.55rem 0.9rem;
            gap: 0.5rem;
        }
        #hero .card-footer-item {
            font-size: 0.68rem;
        }

        @media (max-width: 720px) {
            #hero.hero-section {
                padding: 5rem 0 1.5rem;
            }
            #hero .hero-inner {
                padding: 0.5rem 1rem 0.75rem;
            }
            #hero .hero-title {
                font-size: clamp(1.9rem, 8vw, 2.7rem);
                margin: 0.75rem 0 0.6rem;
            }
            #hero .hero-sub {
                font-size: 0.9rem;
                margin-bottom: 0.8rem;
            }
            #hero .hero-trust-chips {
                gap: 0.4rem;
                margin-bottom: 0.8rem;
            }
            #hero .hero-actions {
                margin-bottom: 0.9rem;
            }
            #hero .dashboard-card-top {
                align-items: flex-start;
                gap: 0.4rem;
                padding: 0.55rem 0.7rem;
            }
            #hero .dashboard-card-top > div {
                min-width: 0;
            }
            #hero .dashboard-card-metrics {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
            #hero .metric-block {
                padding: 0.65rem 0.45rem;
            }
            #hero .metric-block:not(:last-child) {
                border-right: 1px solid #f1f5f9;
                border-bottom: none;
            }
            #hero .metric-icon-wrap {
                width: 28px;
                height: 28px;
                margin-bottom: 0.35rem;
            }
            #hero .metric-val {
                font-size: 1.5rem;
            }
            #hero .metric-lbl {
                font-size: 0.68rem;
                line-height: 1.25;
            }
            #hero .metric-sub {
                display: none;
            }
            #hero .dashboard-card-footer {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0.4rem;
                padding: 0.5rem 0.6rem;
            }
            #hero .card-footer-item {
                align-items: flex-start;
                gap: 0.25rem;
                font-size: 0.62rem;
                line-height: 1.3;
            }
            #hero .card-footer-item svg {
                width: 12px;
                height: 12px;
            }
        }

        @media (max-width: 420px) {
            #hero .dashboard-card-footer {
                display: none;
            }
            #hero .dashboard-card-top {
                font-size: 0.65rem;
            }
        }

        @media (max-width: 420px) and (max-height: 720px) {
            #hero .hero-trust-chips {
                display: none;
            }
        }

        /* ─── Dashboard Card Verification Footer ─── */
        .dashboard-card-footer {
            display: flex;
            align-items: center;
            justify-content: space-around;
            padding: 0.8rem 1.25rem;
            background: #fafcff;
            border-top: 1px solid #f1f5f9;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .card-footer-item {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.73rem;
            font-weight: 600;
            color: #475569;
        }
        .card-footer-item svg {
            color: #10b981;
            flex-shrink: 0;
        }

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

        /* ─── Barangay Carousel ─── */
        /* ─── Barangay Carousel ─── */
        .brgy-section {
            background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
            border-bottom: 1px solid var(--c-border);
            overflow: hidden;
            position: relative;
        }
        .brgy-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 80% 20%, rgba(37, 99, 235, 0.04), transparent 50%),
                        radial-gradient(circle at 10% 80%, rgba(37, 99, 235, 0.03), transparent 45%);
            pointer-events: none;
        }
        .brgy-section .section-pill {
            background: rgba(37, 99, 235, 0.1);
            border-color: rgba(37, 99, 235, 0.25);
            color: #2563eb;
        }
        .brgy-section .section-title {
            color: #0f172a;
        }
        .brgy-section .section-title span {
            color: #2563eb;
        }
        .brgy-section .section-sub {
            color: #64748b;
        }

        .brgy-carousel-viewport {
            overflow: hidden;
            position: relative;
            flex: 1;
            min-height: 0;
            cursor: grab;
            user-select: none;
        }
        .brgy-carousel-viewport.is-dragging { cursor: grabbing; }
        .brgy-carousel-track {
            display: flex;
            gap: 1.25rem;
            height: 100%;
            transition: transform 0.45s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            will-change: transform;
            padding: 0.5rem 0.2rem 1.2rem;
        }
        .brgy-carousel-track.no-transition { transition: none; }

        .brgy-card {
            flex: 0 0 calc(25% - 0.95rem);
            min-width: 230px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .brgy-card:hover {
            transform: translateY(-8px);
            border-color: rgba(37, 99, 235, 0.4);
            box-shadow: 0 24px 50px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(37, 99, 235, 0.15);
        }
        @media (max-width: 1280px) { .brgy-card { flex: 0 0 calc(33.333% - 0.85rem); } }
        @media (max-width: 900px)  { .brgy-card { flex: 0 0 calc(50% - 0.65rem); } }
        @media (max-width: 600px)  { .brgy-card { flex: 0 0 85%; min-width: 260px; } }

        .brgy-card-img {
            width: 100%;
            aspect-ratio: 16/10;
            object-fit: cover;
            display: block;
            background: #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }
        .brgy-card-img-placeholder {
            width: 100%;
            aspect-ratio: 16/10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f0f7ff 0%, #e0effe 100%);
            border-bottom: 1px solid #e2e8f0;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }
        .brgy-card-img-placeholder::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 40% 40%, rgba(37, 99, 235, 0.12), transparent 60%);
        }
        .brgy-card-img-placeholder svg { color: #2563eb; position: relative; z-index: 1; }
        .brgy-placeholder-label {
            font-size: 0.72rem;
            color: #2563eb;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            position: relative;
            z-index: 1;
        }

        .brgy-card-body {
            padding: 1.15rem 1.25rem 1.35rem;
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
            flex: 1;
            background: #ffffff;
        }
        .brgy-card-name {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            letter-spacing: -0.01em;
        }
        .brgy-card-tagline {
            font-size: 0.8rem;
            color: #64748b;
            line-height: 1.55;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .brgy-card-stats {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.2rem;
        }
        .brgy-card-stat {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.72rem;
            font-weight: 700;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.25rem 0.6rem;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .brgy-card-stat strong { color: #0f172a; font-weight: 800; }
        .brgy-card-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            width: 100%;
            padding: 0.65rem 1rem;
            border-radius: 12px;
            background: #2563eb;
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
            margin-top: 0.45rem;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        }
        .brgy-card-btn:hover {
            background: #1d4ed8;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 8px 22px rgba(37, 99, 235, 0.38);
        }

        .brgy-carousel-controls {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            flex-shrink: 0;
        }
        .brgy-nav-btn {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
            transition: background 0.2s, border-color 0.2s, color 0.2s, transform 0.15s, box-shadow 0.2s;
            flex-shrink: 0;
        }
        .brgy-nav-btn:hover {
            background: #f8fafc;
            border-color: #2563eb;
            color: #2563eb;
            transform: scale(1.06);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.15);
        }
        .brgy-nav-btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
            transform: none;
            border-color: #e2e8f0;
            color: #94a3b8;
            box-shadow: none;
        }
        .brgy-dots {
            display: flex;
            gap: 0.4rem;
            align-items: center;
            flex-wrap: wrap;
            max-width: 160px;
        }
        .brgy-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: #cbd5e1;
            cursor: pointer;
            transition: background 0.2s, transform 0.2s;
            flex-shrink: 0;
        }
        .brgy-dot.active { background: #2563eb; transform: scale(1.4); }
        .brgy-counter {
            font-size: 0.78rem;
            color: #64748b;
            font-weight: 700;
            white-space: nowrap;
        }

        /* ─── About Us Section ─── */
        .about-section {
            background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 100%);
            border-bottom: 1px solid var(--c-border);
            position: relative;
            overflow: hidden;
        }
        .about-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 15% 15%, rgba(37, 99, 235, 0.04), transparent 50%),
                        radial-gradient(circle at 85% 85%, rgba(37, 99, 235, 0.03), transparent 50%);
            pointer-events: none;
        }
        .about-section .section-title span {
            color: #2563eb;
        }
        .about-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 1100px) { .about-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 640px)  { .about-grid { grid-template-columns: 1fr; } }

        .about-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            padding: 1.6rem 1.5rem;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
            position: relative;
            overflow: hidden;
        }
        .about-card:hover {
            transform: translateY(-5px);
            border-color: rgba(37, 99, 235, 0.35);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.07);
        }
        .about-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .about-card-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.25;
            letter-spacing: -0.01em;
        }
        .about-card-desc {
            font-size: 0.84rem;
            color: #64748b;
            line-height: 1.65;
            flex: 1;
        }
        .about-card-tag {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .about-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border-radius: 24px;
            padding: 1.75rem 2rem;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
            box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 860px) {
            .about-banner { flex-direction: column; align-items: flex-start; gap: 1.25rem; padding: 1.5rem; }
        }
        .about-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 90% 10%, rgba(37, 99, 235, 0.25), transparent 60%);
            pointer-events: none;
        }
        .about-banner-content {
            position: relative;
            z-index: 1;
            max-width: 680px;
        }
        .about-banner-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.28rem 0.75rem;
            border-radius: 99px;
            background: rgba(37, 99, 235, 0.3);
            border: 1px solid rgba(59, 130, 246, 0.4);
            color: #93c5fd;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 0.75rem;
        }
        .about-banner-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.25;
            margin-bottom: 0.45rem;
        }
        .about-banner-desc {
            font-size: 0.88rem;
            color: rgba(226, 232, 240, 0.85);
            line-height: 1.6;
        }
        .about-banner-stats {
            position: relative;
            z-index: 1;
            display: flex;
            gap: 1.25rem;
            flex-shrink: 0;
        }
        .about-banner-stat {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 0.9rem 1.2rem;
            text-align: center;
            min-width: 105px;
        }
        .about-banner-stat .num {
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 900;
            color: #60a5fa;
            line-height: 1;
        }
        .about-banner-stat .lbl {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #94a3b8;
            margin-top: 0.35rem;
            letter-spacing: 0.05em;
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
        @media (max-width: 620px) {
            .modal-backdrop {
                align-items: flex-end;
                padding: 0;
            }
            .project-modal {
                width: 100%;
                max-height: 92dvh;
                border-radius: 1.25rem 1.25rem 0 0;
            }
            .project-modal-inner {
                max-height: 92dvh;
                padding: 1rem;
                padding-bottom: max(1.25rem, env(safe-area-inset-bottom));
            }
            .project-modal-close {
                top: 0.75rem;
                right: 0.75rem;
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
                    <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="#FFFFFF" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div style="display:flex; flex-direction:column; line-height:1.15; text-align:left;">
                    <span style="font-weight:900; font-size:1.02rem; color:#0f172a; letter-spacing:-0.02em;">SK FUND System</span>
                    <span style="font-size:0.68rem; font-weight:700; color:#2563eb; text-transform:uppercase; letter-spacing:0.08em;">Ramon Magsaysay</span>
                </div>
            </a>

            <button type="button" class="nav-toggle" id="publicMenuToggle" aria-label="Open navigation menu" aria-controls="publicNavLinks" aria-expanded="false">
                <svg width="21" height="21" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <ul class="nav-links" id="publicNavLinks">
                <li><a href="#hero"      class="nav-link opacity-0">Home</a></li>
                <li><a href="#barangays" class="nav-link opacity-0">Barangays</a></li>
                <li><a href="#about"     class="nav-link opacity-0">About Us</a></li>
                <li>
                    <a href="login" class="btn-login nav-btn opacity-0">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Login
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- ════════════════════ SNAP CONTAINER ════════════════════ -->
    <main class="snap-container" id="main-scroll">

        <!-- ─── HERO (Civic Transparency Hub Architecture) ─── -->
        <section id="hero" class="hero-section flex flex-col justify-center items-center relative pt-24 pb-16 min-h-[calc(100vh-68px)]">
            <div class="hero-inner">

                <!-- Badge Announcement -->
                <div class="hero-badge opacity-0">
                    <span class="live-dot"></span>
                    <span>Official SK Fund Monitoring Portal</span>
                    <span style="color:#93c5fd;">·</span>
                    <span style="color:#475569; font-weight:600;">Republic Act No. 10742</span>
                </div>

                <!-- Main Headline -->
                <h1 class="hero-title hero-title-el opacity-0">
                    Public Transparency & <span>Youth Fund Portal</span>
                </h1>

                <!-- Subheadline -->
                <p class="hero-sub hero-sub-el opacity-0">
                    Official Sangguniang Kabataan digital oversight platform — tracking Annual Barangay Youth Investment Programs (ABYIP), audited public disbursements, and community accomplishments across all 27 constituent barangays of Ramon Magsaysay.
                </p>

                <!-- Trust Chips -->
                <div class="hero-trust-chips hero-trust-el opacity-0">
                    <span class="trust-chip">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Republic Act No. 10742
                    </span>
                    <span class="trust-chip">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        COA & DILG Reporting Standard
                    </span>
                    <span class="trust-chip">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        Open Grassroots Social Audit
                    </span>
                </div>

                <!-- Hero Action -->
                <div class="hero-actions hero-actions-el opacity-0">
                    <a href="#about" class="hero-cta-secondary">
                        <span>Learn About the Platform</span>
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <!-- Integrated Civic Hub Dashboard Card (Linear / Stripe Style) -->
                <div class="hero-dashboard-card hero-stats opacity-0">
                    <div class="dashboard-card-top">
                        <div style="display:flex; align-items:center; gap:0.5rem;">
                            <span style="width:7px; height:7px; border-radius:50%; background:#10b981; box-shadow:0 0 0 2px rgba(16,185,129,0.2);"></span>
                            <span style="font-weight:700; color:#334155; text-transform:uppercase; letter-spacing:0.06em; font-size:0.72rem;">Live Municipal SK Fund Registry</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:0.4rem;">
                            <span style="display:inline-flex; align-items:center; gap:0.3rem; padding:0.2rem 0.65rem; border-radius:999px; background:#eff6ff; color:#1d4ed8; font-weight:700; font-size:0.68rem; border:1px solid #dbeafe;">
                                ABYIP Monitored · <?= date('Y') ?> Fiscal Year
                            </span>
                        </div>
                    </div>

                    <div class="dashboard-card-metrics">
                        <!-- Metric 1: Tracked Projects -->
                        <div class="metric-block">
                            <div class="metric-icon-wrap">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div class="metric-val"><?= $total_projects ?? 0 ?></div>
                            <div class="metric-lbl">Funded Youth Projects</div>
                            <div class="metric-sub">Active & completed ABYIP plans</div>
                        </div>

                        <!-- Metric 2: Recorded Transactions -->
                        <div class="metric-block">
                            <div class="metric-icon-wrap">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <div class="metric-val"><?= $recorded_transactions ?? 0 ?></div>
                            <div class="metric-lbl">Audited Transactions</div>
                            <div class="metric-sub">Verified disbursement vouchers</div>
                        </div>

                        <!-- Metric 3: Barangays Covered -->
                        <div class="metric-block">
                            <div class="metric-icon-wrap">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563EB" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="metric-val"><?= $barangay_count ?? 0 ?></div>
                            <div class="metric-lbl">Barangay Councils</div>
                            <div class="metric-sub">100% municipal-wide coverage</div>
                        </div>
                    </div>

                    <div class="dashboard-card-footer">
                        <div class="card-footer-item">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>COA & DILG Reporting Standard</span>
                        </div>
                        <div class="card-footer-item">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>Annual Barangay Youth Investment Program</span>
                        </div>
                        <div class="card-footer-item">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#10b981" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            <span>100% Public Access</span>
                        </div>
                    </div>
                </div>

            </div>
        </section>


        <!-- ─── Barangay Carousel ─── -->
        <section id="barangays" class="pt-24 pb-16 brgy-section min-h-[calc(100vh-68px)]">
            <div class="section-wrap" style="overflow: hidden;">

                <!-- Header row -->
                <div class="section-header mb-5 shrink-0 brgy-hdr opacity-0">
                    <div class="section-header-row">
                        <div>
                            <div class="section-pill">
                                <span class="section-pill-dot"></span>
                                All Barangays
                            </div>
                            <h2 class="section-title">Municipality of <span>Ramon Magsaysay</span></h2>
                            <p class="section-sub">Explore registered barangays' funded projects, community benefits, and fund utilization. Drag or use the arrows to browse.</p>
                        </div>
                        <div class="brgy-carousel-controls mt-4 lg:mt-0">
                            <button id="brgyPrev" class="brgy-nav-btn" aria-label="Previous barangay" disabled>
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <div class="brgy-dots" id="brgyDots"></div>
                            <span class="brgy-counter" id="brgyCounter">1 / <?= count($barangayCards) ?></span>
                            <button id="brgyNext" class="brgy-nav-btn" aria-label="Next barangay">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Carousel viewport -->
                <div class="brgy-carousel-viewport flex-1 min-h-0 brgy-viewport-el opacity-0" id="brgyViewport">
                    <div class="brgy-carousel-track" id="brgyTrack">
                        <?php foreach ($barangayCards as $bc):
                            $bname = $bc['name'];
                            $burl  = urlencode($bname);
                            $imgPath = $bc['image_path'] ?? null;
                        ?>
                        <div class="brgy-card" data-brgy-name="<?= htmlspecialchars($bname) ?>"
                             onclick="window.location.href='public/barangay?name=<?= $burl ?>'">

                            <?php if ($imgPath && file_exists($imgPath)): ?>
                                <img class="brgy-card-img"
                                     src="<?= htmlspecialchars($imgPath) ?>"
                                     alt="<?= htmlspecialchars($bc['display_name']) ?>"
                                     draggable="false">
                            <?php else: ?>
                                <div class="brgy-card-img-placeholder">
                                    <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="1.4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="brgy-placeholder-label">No photo yet</span>
                                </div>
                            <?php endif; ?>

                            <div class="brgy-card-body">
                                <p class="brgy-card-name"><?= htmlspecialchars($bc['display_name']) ?></p>
                                <p class="brgy-card-tagline"><?= htmlspecialchars($bc['tagline']) ?></p>

                                <div class="brgy-card-stats">
                                    <span class="brgy-card-stat">
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                        <strong><?= (int)$bc['project_count'] ?></strong> projects
                                    </span>
                                    <?php if ($bc['utilization_rate'] > 0): ?>
                                    <span class="brgy-card-stat">
                                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                        <strong><?= $bc['utilization_rate'] ?>%</strong> utilized
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <a href="public/barangay?name=<?= $burl ?>"
                                   class="brgy-card-btn"
                                   onclick="event.stopPropagation()">
                                    See Details
                                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </section>

        <!-- ─── ABOUT US ─── -->
        <section id="about" class="pt-24 pb-12 about-section flex flex-col justify-between min-h-[calc(100vh-68px)]">
            <div class="section-wrap">

                <!-- Header row -->
                <div class="mb-5 shrink-0 about-hdr opacity-0">
                    <div class="section-header-row">
                        <div>
                            <div class="section-pill">
                                <span class="section-pill-dot"></span>
                                About the Platform
                            </div>
                            <h2 class="section-title">Empowering Youth Through <span>Transparency & Integrity</span></h2>
                            <p class="section-sub">A web-based financial review, accomplishment tracking, and grassroots social accountability system for the Sangguniang Kabataan of Ramon Magsaysay.</p>
                        </div>
                    </div>
                </div>

                <!-- Scrollable Body for About Us -->
                <div class="inner-scroll flex-1 overflow-y-auto pr-1 about-cards-el opacity-0">

                    <!-- Featured Municipal Banner -->
                    <div class="about-banner">
                        <div class="about-banner-content">
                            <span class="about-banner-badge">🏛️ Republic Act No. 10742 & Municipal SK Fund Ordinance</span>
                            <h3 class="about-banner-title">Modernizing Sangguniang Kabataan Governance</h3>
                            <p class="about-banner-desc">
                                Institutionalized to digitalize the review, recording, financial reporting, and milestone tracking of youth funds across the 27 constituent barangays of the Municipality of Ramon Magsaysay, Zamboanga del Sur.
                            </p>
                        </div>
                        <div class="about-banner-stats">
                            <div class="about-banner-stat">
                                <div class="num">27</div>
                                <div class="lbl">Constituent Barangays</div>
                            </div>
                            <div class="about-banner-stat">
                                <div class="num">100%</div>
                                <div class="lbl">Digital Records</div>
                            </div>
                            <div class="about-banner-stat">
                                <div class="num">Open</div>
                                <div class="lbl">Citizen Auditing</div>
                            </div>
                        </div>
                    </div>

                    <!-- Core 4 Pillars Grid -->
                    <div class="about-grid">

                        <!-- Card 1: Fund Governance -->
                        <div class="about-card">
                            <div class="about-card-icon">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <h4 class="about-card-title">Digital Fund Oversight</h4>
                            <p class="about-card-desc">
                                Tracks the Annual Barangay Youth Investment Program (ABYIP), budgets, disbursements, and ensures strict compliance with Commission on Audit (COA) and DILG guidelines.
                            </p>
                            <span class="about-card-tag">
                                <span>●</span> Fiscal Governance
                            </span>
                        </div>

                        <!-- Card 2: Milestone Tracking -->
                        <div class="about-card">
                            <div class="about-card-icon">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <h4 class="about-card-title">Milestone & Progress Tracking</h4>
                            <p class="about-card-desc">
                                Replaces vulnerable paper logbooks with traceable milestone stages—from planning and bidding through execution and photo-documented project completion.
                            </p>
                            <span class="about-card-tag">
                                <span>●</span> Project Lifecycle
                            </span>
                        </div>

                        <!-- Card 3: Multi-Level Compliance -->
                        <div class="about-card">
                            <div class="about-card-icon">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <h4 class="about-card-title">LYDO & Federation Review</h4>
                            <p class="about-card-desc">
                                Enables Local Youth Development Officers (LYDO) and the SK Federation to review monthly reports, notify councils of deficiencies, and consolidate municipal compliance.
                            </p>
                            <span class="about-card-tag">
                                <span>●</span> Institutional Review
                            </span>
                        </div>

                        <!-- Card 4: Grassroots Feedback -->
                        <div class="about-card">
                            <div class="about-card-icon">
                                <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <h4 class="about-card-title">Grassroots Social Audit</h4>
                            <p class="about-card-desc">
                                Citizens and youth beneficiaries can directly inspect project expenditures, review progress, and submit anonymous feedback or suggestions to local administrators.
                            </p>
                            <span class="about-card-tag">
                                <span>●</span> Citizen Accountability
                            </span>
                        </div>

                    </div>

                </div>

                <!-- Footer -->
                <footer class="site-footer about-footer opacity-0 shrink-0">
                    <div class="site-footer-card">
                        <div class="site-footer-brand">
                            <span class="site-footer-chip">Public Accountability</span>
                            <p>© 2026 Municipality of Ramon Magsaysay, Zamboanga del Sur</p>
                        </div>
                        <div class="site-footer-meta">
                            <p class="site-footer-title">SK FUND Monitoring & Transparency System</p>
                            <p>An open governance platform dedicated to the youth of Ramon Magsaysay.</p>
                        </div>
                    </div>
                </footer>

            </div>
        </section>

    </main>


    <!-- ════════════════════ JAVASCRIPT ════════════════════ -->
    <script>
        // ── Anime.js animations ──────────────────────────
        const publicMenuToggle = document.getElementById('publicMenuToggle');
        const publicNavLinks = document.getElementById('publicNavLinks');
        publicMenuToggle.addEventListener('click', () => {
            const isOpen = publicNavLinks.classList.toggle('is-open');
            publicMenuToggle.setAttribute('aria-expanded', String(isOpen));
            publicMenuToggle.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
        });
        publicNavLinks.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                publicNavLinks.classList.remove('is-open');
                publicMenuToggle.setAttribute('aria-expanded', 'false');
                publicMenuToggle.setAttribute('aria-label', 'Open navigation menu');
            });
        });

        document.addEventListener('DOMContentLoaded', () => {

            // Navbar entrance
            anime.timeline({ easing: 'easeOutQuart' })
                .add({ targets: '.nav-brand', opacity:[0,1], translateX:[-16,0], duration:600 })
                .add({ targets: '.nav-link',  opacity:[0,1], translateY:[-6,0], delay:anime.stagger(70), duration:450 }, '-=400')
                .add({ targets: '.nav-btn',   opacity:[0,1], translateY:[6,0],  duration:450 }, '-=250');

            // Hero entrance
            anime.timeline({ easing: 'easeOutExpo' })
                .add({ targets: '.hero-badge',       opacity:[0,1], translateY:[12,0], duration:500, delay:100 })
                .add({ targets: '.hero-title-el',    opacity:[0,1], translateY:[24,0], duration:650, delay:40  }, '-=300')
                .add({ targets: '.hero-sub-el',      opacity:[0,1], translateY:[16,0], duration:600            }, '-=450')
                .add({ targets: '.hero-trust-el',    opacity:[0,1], translateY:[12,0], duration:500            }, '-=400')
                .add({ targets: '.hero-actions-el',  opacity:[0,1], translateY:[14,0], duration:550            }, '-=400')
                .add({ targets: '.hero-stats',       opacity:[0,1], translateY:[16,0], duration:650            }, '-=400');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        if (entry.target.id === 'barangays') {
                            anime({ targets: '.brgy-hdr',          opacity:[0,1], translateX:[-24,0], duration:600, easing:'easeOutQuart' });
                            anime({ targets: '.brgy-viewport-el',  opacity:[0,1], translateY:[20,0],  duration:650, delay:120, easing:'easeOutQuart' });
                            observer.unobserve(entry.target);
                        }

                        if (entry.target.id === 'about') {
                            anime({ targets: '.about-hdr',      opacity:[0,1], translateX:[-24,0], duration:600, easing:'easeOutQuart' });
                            anime({ targets: '.about-cards-el', opacity:[0,1], translateY:[20,0],  duration:600, delay:100, easing:'easeOutQuart' });
                            anime({ targets: '.about-footer',   opacity:[0,1], duration:650,        delay:250,    easing:'linear' });
                            observer.unobserve(entry.target);
                        }
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('section').forEach(s => observer.observe(s));

            // ── Barangay Carousel ─────────────────────────
            (function initBrgyCarousel() {
                const viewport  = document.getElementById('brgyViewport');
                const track     = document.getElementById('brgyTrack');
                const prevBtn   = document.getElementById('brgyPrev');
                const nextBtn   = document.getElementById('brgyNext');
                const dotsEl    = document.getElementById('brgyDots');
                const counterEl = document.getElementById('brgyCounter');

                if (!viewport || !track) return;

                const cards = Array.from(track.querySelectorAll('.brgy-card'));
                const total = cards.length;
                let current = 0;

                // Compute how many cards are visible at once
                function visibleCount() {
                    const w = viewport.offsetWidth;
                    if (w < 600)  return 1;
                    if (w < 900)  return 2;
                    if (w < 1280) return 3;
                    return 4;
                }

                // Build dots (one per page, not per card)
                function pageCount() { return Math.ceil(total / visibleCount()); }

                function buildDots() {
                    dotsEl.innerHTML = '';
                    const pages = pageCount();
                    for (let i = 0; i < pages; i++) {
                        const d = document.createElement('button');
                        d.className = 'brgy-dot' + (i === 0 ? ' active' : '');
                        d.setAttribute('aria-label', 'Go to page ' + (i + 1));
                        d.addEventListener('click', () => goToPage(i));
                        dotsEl.appendChild(d);
                    }
                }

                function currentPage() { return Math.round(current / visibleCount()); }

                function updateUI() {
                    const vc = visibleCount();
                    // Compute card width + gap
                    const gap = parseFloat(getComputedStyle(track).gap) || 0;
                    const cardW = (viewport.offsetWidth - gap * (vc - 1)) / vc;
                    const offset = current * (cardW + gap);
                    track.style.transform = `translateX(-${offset}px)`;

                    // dots
                    const pg = currentPage();
                    dotsEl.querySelectorAll('.brgy-dot').forEach((d, i) =>
                        d.classList.toggle('active', i === pg));

                    // counter
                    const displayIdx = Math.min(current + vc, total);
                    counterEl.textContent = (current + 1) + '–' + displayIdx + ' / ' + total;

                    // buttons
                    prevBtn.disabled = current === 0;
                    nextBtn.disabled = current >= total - vc;
                }

                function goToCard(idx) {
                    const vc = visibleCount();
                    current = Math.max(0, Math.min(idx, total - vc));
                    updateUI();
                }
                function goToPage(pg) { goToCard(pg * visibleCount()); }

                prevBtn.addEventListener('click', () => goToCard(current - visibleCount()));
                nextBtn.addEventListener('click', () => goToCard(current + visibleCount()));

                // Keyboard navigation when section is in view
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft')  goToCard(current - 1);
                    if (e.key === 'ArrowRight') goToCard(current + 1);
                });

                // Drag / touch support
                let isDragging = false, startX = 0, scrollStart = 0, movedPx = 0;

                function onDragStart(clientX) {
                    isDragging = true;
                    startX = clientX;
                    scrollStart = current;
                    movedPx = 0;
                    track.classList.add('no-transition');
                    viewport.classList.add('is-dragging');
                }
                function onDragMove(clientX) {
                    if (!isDragging) return;
                    movedPx = clientX - startX;
                    const vc  = visibleCount();
                    const gap = parseFloat(getComputedStyle(track).gap) || 0;
                    const cardW = (viewport.offsetWidth - gap * (vc - 1)) / vc;
                    const dragOffset = scrollStart * (cardW + gap) - movedPx;
                    track.style.transform = `translateX(-${Math.max(0, dragOffset)}px)`;
                }
                function onDragEnd() {
                    if (!isDragging) return;
                    isDragging = false;
                    track.classList.remove('no-transition');
                    viewport.classList.remove('is-dragging');
                    const vc  = visibleCount();
                    const gap = parseFloat(getComputedStyle(track).gap) || 0;
                    const cardW = (viewport.offsetWidth - gap * (vc - 1)) / vc;
                    const threshold = cardW * 0.25;
                    if (movedPx < -threshold) goToCard(scrollStart + 1);
                    else if (movedPx > threshold) goToCard(scrollStart - 1);
                    else goToCard(scrollStart);
                }

                // Mouse
                viewport.addEventListener('mousedown',  e => { e.preventDefault(); onDragStart(e.clientX); });
                window .addEventListener('mousemove',   e => onDragMove(e.clientX));
                window .addEventListener('mouseup',     ()  => onDragEnd());

                // Touch
                viewport.addEventListener('touchstart', e => onDragStart(e.touches[0].clientX), { passive: true });
                viewport.addEventListener('touchmove',  e => onDragMove(e.touches[0].clientX),  { passive: true });
                viewport.addEventListener('touchend',   ()  => onDragEnd());

                // Prevent navigation on drag
                track.addEventListener('click', e => {
                    if (Math.abs(movedPx) > 8) e.stopPropagation();
                }, true);

                // Rebuild on resize
                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => { buildDots(); goToCard(current); }, 120);
                });

                buildDots();
                updateUI();
            })();

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
