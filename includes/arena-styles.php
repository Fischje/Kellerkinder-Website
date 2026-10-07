<?php
// „Arena“-Design: Mischung aus den Gaming-Vorlagen Overworld (dunkles Violett,
// abgeschnittene Ecken, Rajdhani-Überschriften, Titel mit Akzentbalken) und
// Squadforce (rote Akzentfarbe, durchgehendes Kopfband, Abschnittstitel mit
// Linien, kräftige Montserrat-Beschriftungen).
// Wird auf allen Seiten NACH includes/styles.php (und ggf. Blog/Spiele-Styles)
// eingebunden und überschreibt dort gezielt Darstellung und Farben.
// Sommer- und Winter-Theme behalten ihre eigenen Farben, übernehmen aber die
// neuen Formen und Schriften.
?>
    <style>
        /* ===== Farben & Schriften ===== */
        :root {
            --font-body: "Open Sans", -apple-system, "Segoe UI", sans-serif;
            --font-heading: "Rajdhani", "Montserrat", -apple-system, "Segoe UI", sans-serif;
            --font-ui: "Montserrat", -apple-system, "Segoe UI", sans-serif;
            --cut: 12px;
            --cut-lg: 20px;
            --accent: #e21b40;
            --accent-hover: #ff3355;
            --accent-rgb: 226, 27, 64;
            --primary-rgb: 107, 84, 182;
            --band: #100a15;
            --band-strong: #0c0710;
        }

        body[data-theme="default"] {
            --bg: #140b1b;
            --bg-soft: #1b0f23;
            --panel: #22152c;
            --panel-strong: #261832;
            --panel-soft: #2f2140;
            --panel-deep: #1a1022;
            --panel-rgb: 34, 21, 44;
            --panel-strong-rgb: 38, 24, 50;
            --panel-soft-rgb: 47, 33, 64;
            --panel-deep-rgb: 26, 16, 34;
            --line: rgba(160, 132, 220, .13);
            --line-strong: rgba(160, 132, 220, .3);
            --text: #ece8f2;
            --muted: #a59cb3;
            --primary: #6b54b6;
            --primary-hover: #8a72dc;
            --vacation: #8a6cff;
            --open: #5b5068;
            --shadow: 0 18px 40px rgba(6, 2, 10, .55);
            --sheen: none;
        }

        body[data-theme="summer"] { --accent: #ff7a45; --accent-hover: #ff9a6b; --accent-rgb: 255, 122, 69; --primary-rgb: 255, 157, 63; --band: #06101e; --band-strong: #040b16; }
        body[data-theme="winter"] { --accent: #ff4058; --accent-hover: #ff6a7e; --accent-rgb: 255, 64, 88; --primary-rgb: 92, 150, 255; --band: #111214; --band-strong: #0c0d0f; }

        body { font-family: var(--font-body); line-height: 1.55; }

        /* Hintergrund: diagonale Linien statt Gitter (Overworld-Optik) */
        body[data-theme="default"]::before {
            background:
                radial-gradient(ellipse 60% 40% at 15% 0%, rgba(var(--primary-rgb), .22), transparent 70%),
                radial-gradient(ellipse 45% 35% at 95% 10%, rgba(var(--accent-rgb), .12), transparent 70%);
            inset: 0;
        }

        body[data-theme="default"]::after {
            background-image:
                repeating-linear-gradient(115deg, transparent 0 180px, rgba(160, 132, 220, .05) 180px 181px, transparent 181px 420px),
                repeating-linear-gradient(65deg, transparent 0 260px, rgba(226, 27, 64, .035) 260px 261px, transparent 261px 640px);
            background-size: auto;
            mask-image: linear-gradient(180deg, #000 0%, rgba(0, 0, 0, .55) 60%, transparent 100%);
            -webkit-mask-image: linear-gradient(180deg, #000 0%, rgba(0, 0, 0, .55) 60%, transparent 100%);
        }

        /* Abgeschnittene Ecken oben rechts und unten links */
        .primary-button, .secondary-button, .danger-button, .compact-button,
        .tag-chip, .install-app-button, .blog-teaser-label, .section-cta {
            clip-path: polygon(0 0, calc(100% - var(--cut)) 0, 100% var(--cut), 100% 100%, var(--cut) 100%, 0 calc(100% - var(--cut)));
        }

        .board, .achievements, .account-strip, .blog-panel, .games-panel, .sidebar-box,
        .post-card, .achievement-card, dialog, .blog-teaser {
            clip-path: polygon(0 0, calc(100% - var(--cut-lg)) 0, 100% var(--cut-lg), 100% 100%, var(--cut-lg) 100%, 0 calc(100% - var(--cut-lg)));
        }

        /* ===== Infoleiste & Kopfband (Squadforce-Aufbau, Overworld-Navigation) ===== */
        .topbar {
            position: relative;
            z-index: 3;
            background: var(--band-strong);
            border-bottom: 1px solid rgba(255, 255, 255, .05);
        }

        .topbar-inner,
        .masthead-row,
        .page-hero-inner {
            width: min(1180px, 100%);
            margin: 0 auto;
            padding-left: calc(14px + env(safe-area-inset-left));
            padding-right: calc(14px + env(safe-area-inset-right));
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 38px;
            padding-top: env(safe-area-inset-top);
        }

        .topbar-tagline.subtitle {
            color: var(--muted);
            font-family: var(--font-heading);
            font-size: .82rem;
            font-weight: 600;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .topbar-tagline .shine {
            font-style: normal;
            font-weight: 700;
            background: linear-gradient(100deg, var(--muted) 30%, #fff 45%, var(--accent) 52%, var(--muted) 66%);
            background-size: 250% 100%;
            -webkit-background-clip: text;
            background-clip: text;
        }

        .install-app-button {
            width: auto;
            height: 28px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0 14px 0 10px;
            border: 0;
            border-radius: 0;
            color: #fff;
            background: var(--primary);
            font-family: var(--font-heading);
            font-size: .82rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            --cut: 8px;
        }

        .install-app-button img { width: 16px; height: 16px; }
        .install-app-button:hover { transform: none; background: var(--accent); }

        .masthead {
            position: sticky;
            top: 0;
            z-index: 20;
            overflow: visible;
            padding: 0;
            border: 0;
            border-bottom: 1px solid rgba(255, 255, 255, .06);
            border-radius: 0;
            background: rgba(16, 10, 21, .9);
            box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        body[data-theme="summer"] .masthead { background: rgba(6, 16, 30, .9); }
        body[data-theme="winter"] .masthead { background: rgba(17, 18, 20, .9); }

        .masthead::after {
            inset: auto 0 -1px 0;
            height: 2px;
            border: 0;
            border-radius: 0;
            background: linear-gradient(90deg, transparent, var(--primary) 25%, var(--accent) 75%, transparent);
            opacity: .65;
        }

        body[data-theme="summer"] .masthead::after,
        body[data-theme="winter"] .masthead::after {
            background: linear-gradient(90deg, transparent, var(--primary) 25%, var(--accent) 75%, transparent);
        }

        .masthead-row {
            min-height: 78px;
            gap: 24px;
        }

        .brand { gap: 12px; }

        .brand-logo { width: 46px; height: 46px; filter: drop-shadow(0 0 12px rgba(var(--accent-rgb), .35)); }

        .brand-name {
            font-family: var(--font-ui);
            font-size: 1.55rem;
            font-weight: 900;
            font-style: italic;
            letter-spacing: .01em;
            color: #fff;
        }

        .brand-accent { color: var(--accent); }

        .main-nav {
            justify-content: flex-end;
            gap: 34px;
            margin: 0 0 0 auto;
            align-self: stretch;
            align-items: stretch;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0;
            color: #fff;
            font-family: var(--font-heading);
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .nav-link::after {
            left: 0;
            right: 0;
            bottom: 18px;
            height: 3px;
            border-radius: 0;
            background: var(--primary);
        }

        .nav-link:hover { color: var(--accent-hover); }
        .nav-link.active { color: #fff; }
        .nav-link.disabled { color: var(--muted); opacity: .45; }
        .nav-link small { margin-left: 4px; letter-spacing: .04em; text-transform: none; }

        /* ===== Seitentitel (Overworld) ===== */
        .page-hero {
            position: relative;
            z-index: 1;
            padding: 46px 0 34px;
            background: linear-gradient(180deg, rgba(12, 7, 16, .78), rgba(12, 7, 16, .35) 70%, transparent);
        }

        .page-hero-inner { position: relative; }

        .page-hero-inner::before {
            content: "";
            position: absolute;
            left: calc(14px + env(safe-area-inset-left));
            top: 4px;
            bottom: 6px;
            width: 4px;
            background: linear-gradient(180deg, var(--primary), var(--accent));
        }

        .page-hero-crumb,
        .page-hero-title,
        .page-hero-lead { margin-left: 30px; }

        .page-hero-crumb {
            margin-top: 0;
            margin-bottom: 8px;
            color: #fff;
            font-family: var(--font-heading);
            font-size: .85rem;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
        }

        .page-hero-crumb span { color: var(--accent); margin: 0 4px; }

        .page-hero-title {
            margin-top: 0;
            margin-bottom: 0;
            color: #fff;
            font-family: var(--font-heading);
            font-size: clamp(2.3rem, 7vw, 4.4rem);
            font-weight: 700;
            line-height: .95;
            letter-spacing: -.01em;
            text-transform: uppercase;
            text-shadow: 0 4px 30px rgba(0, 0, 0, .5);
        }

        .page-hero-lead {
            max-width: 640px;
            margin-top: 12px;
            margin-bottom: 0;
            color: var(--muted);
            font-size: 1rem;
        }

        .page-shell { padding-top: 6px; }

        /* ===== Abschnittstitel mit Linien (Squadforce) ===== */
        .section-title,
        .board-heading {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 0 0 18px;
            color: #fff;
            font-family: var(--font-ui);
            font-size: clamp(1.25rem, 3.6vw, 1.7rem);
            font-weight: 800;
            font-style: normal;
            letter-spacing: .01em;
            line-height: 1.1;
            text-transform: uppercase;
        }

        .section-title::before,
        .section-title::after {
            content: "";
            height: 3px;
            background: var(--line-strong);
        }

        .section-title::before { flex: 0 0 28px; }
        .section-title::after { flex: 1 1 auto; min-width: 20px; }
        .section-title .accent { color: var(--accent); margin-right: -6px; }

        /* Kartentitel mit Akzentbalken (Overworld) */
        .card-title,
        .modal-title,
        .post-card h2,
        .achievement-card-head h3 {
            font-family: var(--font-heading);
            font-weight: 700;
            letter-spacing: .02em;
            text-transform: uppercase;
            color: #fff;
        }

        .card-title,
        .modal-title,
        .post-card h2 {
            position: relative;
            padding-left: 16px;
        }

        .card-title::before,
        .modal-title::before,
        .post-card h2::before {
            content: "";
            position: absolute;
            left: 0;
            top: .12em;
            bottom: .16em;
            width: 3px;
            background: var(--primary);
        }

        .modal-title { font-size: 1.55rem; line-height: 1.05; }

        /* ===== Buttons ===== */
        .primary-button,
        .secondary-button,
        .danger-button,
        .compact-button {
            min-height: 44px;
            padding: 10px 22px;
            border: 0;
            border-radius: 0;
            font-family: var(--font-heading);
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            transition: background-color .15s ease, color .15s ease, box-shadow .15s ease;
        }

        .primary-button { color: #fff; background: var(--primary); }
        .primary-button:hover { color: #fff; background: var(--accent); }

        body[data-theme="summer"] .primary-button,
        body[data-theme="winter"] .primary-button { color: #0a0b0e; }
        body[data-theme="summer"] .primary-button:hover,
        body[data-theme="winter"] .primary-button:hover { color: #fff; }

        .secondary-button,
        .compact-button {
            color: #fff;
            background: rgba(var(--panel-soft-rgb), .95);
            box-shadow: inset 0 0 0 1px var(--line-strong);
        }

        .secondary-button:hover,
        .compact-button:hover {
            color: #fff;
            background: rgba(var(--primary-rgb), .3);
            box-shadow: inset 0 0 0 1px var(--primary);
        }

        .danger-button {
            color: #fff;
            background: rgba(var(--accent-rgb), .16);
            box-shadow: inset 0 0 0 1px rgba(var(--accent-rgb), .6);
        }

        .danger-button:hover { background: var(--accent); }

        .primary-button:hover, .secondary-button:hover, .danger-button:hover { transform: none; }
        .primary-button:active, .secondary-button:active, .danger-button:active { transform: translateY(1px); }

        .compact-button { min-height: 40px; padding: 8px 16px; font-size: .92rem; }

        /* ===== Konto-Leiste ===== */
        .account-strip {
            margin-top: 0;
            padding: 14px 18px;
            border: 0;
            border-radius: 0;
            background: rgba(var(--panel-rgb), .92);
            box-shadow: inset 3px 0 0 var(--accent);
            backdrop-filter: none;
        }

        .account-summary strong {
            font-family: var(--font-heading);
            font-size: 1.15rem;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .avatar, .avatar-placeholder { border-radius: 2px; }
        .avatar-placeholder { color: #fff; background: var(--primary); border-color: transparent; font-family: var(--font-heading); font-size: .95rem; }

        .account-badge,
        .player-badge {
            border-radius: 0;
            border-color: transparent;
            color: #fff;
            background: var(--primary);
            font-family: var(--font-heading);
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .account-badge.admin,
        .player-badge.admin { border-color: transparent; color: #fff; background: var(--accent); }

        .setup-callout,
        .password-callout,
        .storage-warning {
            border-radius: 0;
            border-width: 0 0 0 3px;
        }

        /* ===== Blog-Hinweis: hervorgehobener Eintrag (Squadforce „Latest News“) ===== */
        .blog-teaser {
            margin: 16px 0;
            padding: 0;
            gap: 0;
            border: 0;
            border-radius: 0;
            background: rgba(var(--panel-rgb), .92);
            box-shadow: none;
        }

        .blog-teaser:hover { transform: none; background: rgba(var(--panel-soft-rgb), .95); }

        .blog-teaser-label {
            align-self: stretch;
            display: flex;
            align-items: center;
            padding: 14px 22px 14px 18px;
            border: 0;
            border-radius: 0;
            color: #fff;
            background: var(--accent);
            font-family: var(--font-heading);
            font-size: .9rem;
            font-weight: 700;
            letter-spacing: .12em;
            clip-path: polygon(0 0, 100% 0, calc(100% - 12px) 100%, 0 100%);
        }

        .blog-teaser-title {
            padding-left: 18px;
            font-family: var(--font-ui);
            font-size: .98rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .01em;
        }

        .blog-teaser-meta { padding-left: 14px; color: var(--muted); }
        .blog-teaser-arrow { padding: 0 18px 0 12px; color: var(--accent); font-size: 1.2rem; }

        /* ===== Kalender ===== */
        .board {
            margin-top: 22px;
            border: 0;
            border-radius: 0;
            background: rgba(var(--panel-deep-rgb), .82);
            box-shadow: inset 0 -3px 0 var(--accent);
        }

        .board::before { display: none; }

        .board-toolbar { padding: 24px 24px 18px; }

        .legend-item {
            font-family: var(--font-heading);
            font-size: .95rem;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .legend-icon { width: 10px; height: 10px; border-radius: 0; transform: rotate(45deg); }

        th, td { border-color: rgba(255, 255, 255, .05); }

        thead th { background: rgba(var(--panel-strong-rgb), .96); }

        thead th.player-heading,
        .player-heading {
            color: var(--muted);
            font-family: var(--font-heading);
            font-size: .95rem;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .date-day {
            font-family: var(--font-heading);
            font-size: 1.05rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .date-value { font-family: var(--font-heading); font-weight: 600; letter-spacing: .04em; }

        thead th.is-today { background: rgba(var(--accent-rgb), .16); box-shadow: inset 0 3px 0 var(--accent); }
        tbody tr td.is-today { background: rgba(var(--accent-rgb), .05); }

        .today-tag,
        .past-tag {
            border-radius: 0;
            font-family: var(--font-heading);
            letter-spacing: .1em;
        }

        thead th.is-today .today-tag { color: #fff; background: var(--accent); border-color: var(--accent); }

        tbody th { background: rgba(var(--panel-rgb), .9); }
        tbody tr:nth-child(even) th { background: rgba(var(--panel-strong-rgb), .9); }

        .player-button {
            border-radius: 0;
            font-family: var(--font-heading);
            font-size: 1.02rem;
            letter-spacing: .01em;
        }

        .player-name { font-weight: 700; }

        .status-button { border-radius: 2px; }
        .status-badge-main { font-family: var(--font-heading); font-size: .98rem; letter-spacing: .03em; text-transform: uppercase; }

        .date-remove { border-radius: 0; }

        .empty-state strong { font-family: var(--font-heading); text-transform: uppercase; letter-spacing: .04em; }

        /* ===== Erfolge ===== */
        .achievements {
            margin-top: 34px;
            padding: 26px 24px;
            border: 0;
            border-radius: 0;
            background: rgba(var(--panel-deep-rgb), .82);
        }

        .achievements-head {
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
        }

        .achievements-head .section-title { flex: 1 1 320px; margin: 0; }

        .achievements-nav {
            padding: 6px 10px;
            border: 0;
            border-radius: 0;
            background: rgba(var(--panel-soft-rgb), .9);
        }

        .nav-arrow { border: 0; border-radius: 0; background: var(--primary); color: #fff; }
        .nav-arrow:hover { background: var(--accent); transform: none; }

        .achievements-nav-label { font-family: var(--font-heading); font-size: .92rem; letter-spacing: .1em; text-transform: uppercase; color: #fff; }

        .achievement-card {
            padding: 18px;
            border: 0;
            border-radius: 0;
            background: var(--panel);
            box-shadow: inset 0 3px 0 var(--primary);
        }

        .achievement-card:hover { transform: none; background: var(--panel-soft); box-shadow: inset 0 3px 0 var(--accent); }

        .achievement-icon { border-radius: 0; }
        .achievement-card-head h3 { font-size: 1.25rem; }
        .achievement-edit-button { border-radius: 0; }

        .mplus-bar-track, .mplus-bar-fill { border-radius: 0; }
        .mplus-bar-fill { background: linear-gradient(90deg, var(--primary), var(--accent)); }
        .d4-milestones li, .achievement-links li a { border-radius: 0; }

        /* Erfolge-Widgets: kompakt, zwei Karten nebeneinander */
        .achievement-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; align-items: start; }
        .achievement-card { padding: 14px 14px 12px; min-width: 0; }
        .achievement-card-head { margin-bottom: 10px; gap: 10px; }
        .achievement-card-head h3 { font-size: 1.1rem; }
        .achievement-source { font-size: .76rem; }
        .achievement-icon.steam { color: #fff; background: linear-gradient(135deg, #1b2838, #2a475e 60%, #66c0f4); }

        .steam-achievements { display: grid; gap: 6px; margin: 0; padding: 0; list-style: none; }
        .steam-achievements .widget-empty { margin: 8px 0; }

        .steam-achievement {
            display: grid;
            grid-template-columns: 38px minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            padding: 7px 10px;
            background: rgba(var(--panel-deep-rgb), .85);
            box-shadow: inset 3px 0 0 var(--accent);
        }

        .steam-achievement-icon { width: 38px; height: 38px; object-fit: cover; background: rgba(var(--primary-rgb), .35); }
        .steam-achievement-icon.placeholder { display: grid; place-items: center; font-size: 1.1rem; }

        .steam-achievement-text { min-width: 0; display: grid; gap: 0; }
        .steam-achievement-text strong {
            overflow: hidden;
            color: #fff;
            font-family: var(--font-heading);
            font-size: .98rem;
            line-height: 1.2;
            letter-spacing: .02em;
            text-overflow: ellipsis;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .steam-achievement-meta { overflow: hidden; color: var(--muted); font-size: .78rem; text-overflow: ellipsis; white-space: nowrap; }
        .steam-achievement-meta a { color: var(--accent-hover); font-weight: 700; text-decoration: none; }
        .steam-achievement-meta a:hover { text-decoration: underline; }
        .steam-achievement-text small { display: none; }

        .steam-achievement-time {
            color: var(--accent);
            font-family: var(--font-heading);
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .steam-admin-tools { display: flex; gap: 14px; margin-top: 6px; }
        .steam-admin-link {
            padding: 0;
            border: 0;
            color: var(--accent-hover);
            background: none;
            cursor: pointer;
            font: inherit;
            font-size: .8rem;
            font-weight: 700;
            text-decoration: underline;
        }

        .steam-admin-link:disabled { opacity: .6; cursor: default; }

        .achievement-links-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 12px 0 6px;
            color: #fff;
            font-family: var(--font-heading);
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .achievement-links-head .achievement-edit-button { width: 28px; height: 28px; }

        .steam-missing {
            display: grid;
            gap: 2px;
            padding: 8px 10px;
            color: var(--muted);
            font-size: .78rem;
            background: rgba(var(--panel-deep-rgb), .5);
        }

        .steam-missing strong { color: #fff; font-family: var(--font-heading); letter-spacing: .06em; text-transform: uppercase; }

        /* ===== Dialoge & Formulare ===== */
        dialog {
            border: 0;
            border-radius: 0;
            background: var(--panel);
            box-shadow: inset 0 3px 0 var(--accent), 0 30px 70px rgba(0, 0, 0, .6);
        }

        dialog::backdrop { background: rgba(10, 4, 14, .78); }

        .modal-content { padding: 26px; }
        .modal-subtitle { color: var(--muted); }

        label {
            color: #fff;
            font-family: var(--font-heading);
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .remember-row,
        .remember-row span { font-family: var(--font-body); font-size: .92rem; letter-spacing: 0; text-transform: none; font-weight: 600; }

        input[type="text"],
        input[type="password"],
        input[type="date"],
        select,
        textarea {
            border: 0;
            border-radius: 0;
            background: rgba(var(--panel-deep-rgb), .95);
            box-shadow: inset 0 0 0 1px var(--line);
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: transparent;
            box-shadow: inset 0 0 0 1px var(--primary), inset 0 -2px 0 var(--accent);
        }

        .status-choice, .weekday-choice span, .instruction-item, .instruction-number,
        .admin-user-card, .discord-note, .editing-note, .achievement-edit-row input,
        .achievement-edit-row button, .info-icon-button, .toast { border-radius: 0; }

        input[type="file"] { border-radius: 0; background: rgba(var(--panel-deep-rgb), .95); }

        .instruction-number { background: var(--primary); border-color: transparent; font-family: var(--font-heading); }

        .info-icon-button {
            width: 44px;
            height: 44px;
            border: 0;
            color: #fff;
            background: var(--primary);
            font-family: var(--font-heading);
            font-size: 1.2rem;
            clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 10px, 100% 100%, 10px 100%, 0 calc(100% - 10px));
        }

        .info-icon-button:hover { color: #fff; background: var(--accent); transform: none; }

        /* ===== Fußzeile (Overworld: zweistufiges dunkles Band) ===== */
        .site-footer {
            margin: 40px calc(50% - 50vw) 0;
            padding: 0;
            color: var(--muted);
            background: var(--band);
            font-family: var(--font-heading);
            font-size: .95rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .site-footer::before {
            content: "Kellerkinder";
            display: block;
            padding: 28px 16px 22px;
            color: #fff;
            font-family: var(--font-ui);
            font-size: 1.3rem;
            font-style: italic;
            font-weight: 900;
            letter-spacing: .02em;
            border-top: 2px solid rgba(var(--accent-rgb), .7);
        }

        .site-footer {
            padding-bottom: calc(18px + env(safe-area-inset-bottom));
            box-shadow: inset 0 -64px 0 var(--band-strong);
        }

        .page-shell { padding-bottom: 0; }

        /* ===== Blog ===== */
        .blog-layout { gap: 22px; margin-top: 0; }

        .blog-panel,
        .sidebar-box,
        .games-panel {
            border: 0;
            border-radius: 0;
            background: rgba(var(--panel-deep-rgb), .82);
        }

        .blog-head { align-items: center; }
        .blog-head .section-title { flex: 1 1 320px; margin: 0; }

        .post-card {
            padding: 22px;
            border: 0;
            border-radius: 0;
            background: var(--panel);
            box-shadow: inset 0 3px 0 var(--primary);
        }

        .post-card h2 { font-size: 1.6rem; line-height: 1.1; }

        .post-meta {
            color: var(--accent);
            font-family: var(--font-heading);
            font-size: .88rem;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .draft-flag { border-radius: 0; }

        .tag-chip {
            border: 0;
            border-radius: 0;
            color: #fff;
            background: rgba(var(--panel-soft-rgb), .95);
            font-family: var(--font-heading);
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            --cut: 7px;
        }

        .tag-chip:hover { color: #fff; background: rgba(var(--primary-rgb), .45); }
        .tag-chip.active { color: #fff; background: var(--accent); border-color: transparent; }

        .sidebar-box h2.card-title { margin: 0 0 14px; font-size: 1.2rem; }

        .editor-toolbar button, .editor-surface { border-radius: 0; }

        /* ===== Spiele-Seite ===== */
        .games-panel { margin-top: 0; padding: 26px 24px; }
        .games-head .section-title { margin-bottom: 20px; }

        /* Profilbild des Spielers (Discord) */
        .stats-row.with-avatar { grid-template-columns: 2.2em 40px minmax(0, 1fr) auto; }
        .stats-row.expandable.with-avatar { grid-template-columns: 2.2em 40px minmax(0, 1fr) auto 1.2em; }

        .stats-avatar {
            position: relative;
            display: block;
            width: 40px;
            height: 40px;
            object-fit: cover;
            background: rgba(var(--primary-rgb), .45);
            clip-path: polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px));
        }

        .stats-avatar.placeholder {
            display: grid;
            place-items: center;
            color: #fff;
            font-family: var(--font-heading);
            font-size: 1.15rem;
            font-weight: 700;
        }

        .stats-detail-list li.with-avatar { grid-template-columns: 2em 28px minmax(0, 1fr) auto; }
        .stats-detail-list .stats-avatar { width: 28px; height: 28px; font-size: .9rem; clip-path: none; }

        .stats-admin-item { grid-template-columns: auto 28px minmax(0, 1fr) auto; }
        .stats-admin-item .stats-avatar { width: 28px; height: 28px; font-size: .9rem; clip-path: none; }

        /* Spiele-Symbol in der Rangliste */
        .stats-row.with-icon { grid-template-columns: 2.2em 64px minmax(0, 1fr) auto; }
        .stats-row.expandable.with-icon { grid-template-columns: 2.2em 64px minmax(0, 1fr) auto 1.2em; }

        .stats-icon {
            position: relative;
            display: block;
            width: 64px;
            height: 38px;
            object-fit: cover;
            background: rgba(var(--primary-rgb), .35);
        }

        .stats-icon.placeholder {
            display: grid;
            place-items: center;
            font-size: 1.15rem;
            background: linear-gradient(135deg, rgba(var(--primary-rgb), .55), rgba(var(--accent-rgb), .35));
        }

        /* ===== Statistik ===== */
        .stats-period {
            border: 0;
            border-radius: 0;
            color: #fff;
            background: rgba(var(--panel-soft-rgb), .95);
            font-family: var(--font-heading);
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            clip-path: polygon(0 0, calc(100% - 8px) 0, 100% 8px, 100% 100%, 8px 100%, 0 calc(100% - 8px));
        }

        .stats-period:hover, .stats-period:focus-visible { background: rgba(var(--primary-rgb), .45); }
        .stats-period.active { color: #fff; background: var(--accent); border-color: transparent; }

        .stats-tile {
            border: 0;
            border-radius: 0;
            background: var(--panel);
            box-shadow: inset 3px 0 0 var(--primary);
        }

        .stats-tile strong { font-family: var(--font-heading); font-size: 1.9rem; font-weight: 700; }
        .stats-tile span { color: var(--muted); opacity: 1; font-family: var(--font-heading); font-weight: 600; letter-spacing: .08em; text-transform: uppercase; }

        .stats-section { position: relative; padding-left: 14px; font-size: 1.15rem; font-weight: 700; letter-spacing: .06em; }
        .stats-section::before { content: ""; position: absolute; left: 0; top: .15em; bottom: .15em; width: 3px; background: var(--primary); }

        .stats-row { border: 0; border-radius: 0; background: var(--panel); }
        .stats-bar { background: linear-gradient(90deg, rgba(var(--primary-rgb), .45), rgba(var(--accent-rgb), .25)); }
        .stats-rank { color: var(--accent); opacity: 1; font-family: var(--font-heading); font-size: 1.1rem; }
        .stats-name { font-family: var(--font-heading); font-size: 1.08rem; letter-spacing: .02em; }
        .stats-time { font-family: var(--font-heading); font-size: 1.05rem; }

        /* Aufklappbare Zeilen (Top-Spieler je Spiel, Top-5-Spiele je Spieler) */
        .stats-item { list-style: none; }
        .stats-row.expandable {
            width: 100%;
            border: 0;
            color: inherit;
            font: inherit;
            text-align: left;
            cursor: pointer;
            grid-template-columns: 2.2em minmax(0, 1fr) auto 1.2em;
        }
        .stats-row.expandable:hover { background: var(--panel-soft); }
        .stats-row.expandable:focus-visible { outline: 2px solid var(--accent); outline-offset: 2px; }
        .stats-caret { position: relative; color: var(--accent); transition: transform .15s ease; }
        .stats-item.open > .stats-row .stats-caret { transform: rotate(180deg); }

        .stats-detail {
            margin: 2px 0 6px 14px;
            padding: 10px 12px 12px;
            background: rgba(var(--panel-deep-rgb), .85);
            box-shadow: inset 3px 0 0 var(--primary);
        }
        .stats-detail[hidden] { display: none; }
        .stats-detail-title {
            margin: 0 0 8px;
            color: var(--muted);
            font-family: var(--font-heading);
            font-size: .85rem;
            letter-spacing: .1em;
            text-transform: uppercase;
        }
        .stats-detail-list { display: grid; gap: 4px; margin: 0; padding: 0; list-style: none; }
        .stats-detail-list li {
            position: relative;
            display: grid;
            grid-template-columns: 2em minmax(0, 1fr) auto;
            align-items: center;
            gap: 8px;
            padding: 5px 8px;
            overflow: hidden;
            background: var(--panel);
        }
        .stats-detail-list .stats-name { font-size: .95rem; }
        .stats-detail-list .stats-time { font-size: .9rem; }

        .stats-admin { margin-top: 24px; }
        .stats-admin code { color: #fff; }

        .stats-admin-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 220px), 1fr));
            gap: 8px;
            margin: 14px 0 18px;
        }

        .stats-admin-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            margin: 0;
            padding: 10px 12px;
            background: var(--panel);
            cursor: pointer;
            font-family: var(--font-body);
            font-size: .95rem;
            letter-spacing: 0;
            text-transform: none;
        }

        .stats-admin-item input { width: 18px; height: 18px; accent-color: var(--accent); }
        .stats-admin-item span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .stats-admin-item small { color: var(--muted); }
        .stats-admin-item:has(input:checked) { box-shadow: inset 3px 0 0 var(--accent); opacity: .7; }
        .stats-admin-item:has(input:checked) span { text-decoration: line-through; }
        .stats-admin-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 14px; }
        .stats-admin-actions .stats-note { margin: 0; }

        /* ===== Mobil ===== */
        @media (max-width: 680px) {
            .topbar-tagline.subtitle { font-size: .7rem; letter-spacing: .1em; }
            .masthead { padding: 0; border-radius: 0; }
            .masthead-row { min-height: 0; flex-wrap: wrap; gap: 6px 14px; padding-top: 10px; padding-bottom: 0; }
            .brand-logo { width: 34px; height: 34px; }
            .brand-name { font-size: 1.2rem; }
            .main-nav { order: 3; flex: 1 1 100%; justify-content: flex-start; gap: 22px; margin: 0; min-height: 40px; }
            .nav-link { font-size: .95rem; }
            .nav-link::after { bottom: 6px; }
            .page-hero { padding: 26px 0 20px; }
            .page-hero-crumb, .page-hero-title, .page-hero-lead { margin-left: 20px; }
            .page-hero-lead { font-size: .92rem; }
            .section-title::before { flex-basis: 14px; }
            .board-toolbar { padding: 18px 14px 14px; }
            .achievements { padding: 20px 14px; }
            .achievement-grid { grid-template-columns: 1fr; }
            .blog-teaser-label { padding: 10px 18px 10px 12px; font-size: .78rem; }
            .blog-teaser-title { padding: 0 14px; }
            .blog-teaser-meta { padding: 0 0 10px 14px; }
            .blog-teaser-arrow { padding-bottom: 10px; }
            .games-panel { padding: 20px 14px; }
            .stats-summary { grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; margin-bottom: 14px; }
            .stats-tile { padding: 8px 10px; }
            .stats-tile strong { overflow: hidden; font-size: 1rem; line-height: 1.2; text-overflow: ellipsis; white-space: nowrap; }
            .stats-tile span { font-size: .7rem; letter-spacing: .04em; }
            .stats-row.with-icon { grid-template-columns: 2em 48px minmax(0, 1fr) auto; gap: 8px; }
            .stats-row.expandable.with-icon { grid-template-columns: 2em 48px minmax(0, 1fr) auto 1em; }
            .stats-icon { width: 48px; height: 30px; }
            .stats-row.with-avatar { grid-template-columns: 2em 36px minmax(0, 1fr) auto; gap: 8px; }
            .stats-row.expandable.with-avatar { grid-template-columns: 2em 36px minmax(0, 1fr) auto 1em; }
            .stats-avatar { width: 36px; height: 36px; }
            .achievements-head .section-title,
            .blog-head .section-title { flex: none; width: 100%; }
            .status-badge-main { font-size: .84rem; letter-spacing: 0; text-transform: none; }
            .player-button { font-size: .92rem; }
            .status-badge-game { letter-spacing: 0; }
            .primary-button, .secondary-button, .danger-button { padding: 10px 16px; font-size: .95rem; }
        }
    </style>
