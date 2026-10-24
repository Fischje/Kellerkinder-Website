<?php
// „NAVI“-Design: angelehnt an navi.gg – dunkles Graublau, ruhige abgerundete
// Karten mit feinen Rahmen, große fette Versal-Überschriften (Inter) und eine
// einzige kräftige Akzentfarbe. Statt NAVI-Gelb ein helles Grün.
// Die eigenen Hintergrundbilder liegen unter einer graublauen Fläche mit 80 %
// Deckkraft, sind also zu 20 % sichtbar.
// Wird auf allen Seiten als LETZTE Stilschicht eingebunden (nach
// includes/arena-styles.php) und überschreibt dort Farben, Formen und Schriften.
// Sommer- und Winter-Theme behalten ihre Akzentfarben, übernehmen aber die Formen.
if (!isset($backgroundImages) || !is_array($backgroundImages)) { $backgroundImages = []; }
?>
    <style>
        /* ===== Farben & Schriften ===== */
        :root {
            --font-body: "Inter", -apple-system, "Segoe UI", sans-serif;
            --font-heading: "Inter", -apple-system, "Segoe UI", sans-serif;
            --font-ui: "Inter", -apple-system, "Segoe UI", sans-serif;
            --radius-lg: 18px;
            --radius-md: 12px;
            --radius-sm: 8px;
            /* Schrift auf Akzentflächen */
            --accent-ink: #0b1406;
            /* Graublau der Fläche über dem Hintergrundbild */
            --wash-rgb: 21, 26, 35;
        }

        body[data-theme="default"] {
            --bg: #151a23;
            --bg-soft: #1a2029;
            --panel: #1e2530;
            --panel-strong: #222a36;
            --panel-soft: #29323f;
            --panel-deep: #181e27;
            --panel-rgb: 30, 37, 48;
            --panel-strong-rgb: 34, 42, 54;
            --panel-soft-rgb: 41, 50, 63;
            --panel-deep-rgb: 24, 30, 39;
            --line: rgba(255, 255, 255, .07);
            --line-strong: rgba(255, 255, 255, .14);
            --text: #eef1f6;
            --muted: #8d97a8;
            --accent: #9ef06a;
            --accent-hover: #b9ff8e;
            --accent-rgb: 158, 240, 106;
            --primary: #9ef06a;
            --primary-hover: #b9ff8e;
            --primary-rgb: 158, 240, 106;
            --vacation: #8f7bff;
            --open: #4f5867;
            --band: #10141b;
            --band-strong: #0c0f14;
            --shadow: 0 20px 44px rgba(4, 7, 12, .45);
            --sheen: none;
        }

        body[data-theme="summer"] { --accent-ink: #1a0c03; }
        body[data-theme="winter"] { --accent-ink: #ffffff; }

        body { font-family: var(--font-body); line-height: 1.55; -webkit-font-smoothing: antialiased; }

        /* ===== Hintergrund: Graublau mit 80 % Deckkraft über den eigenen Bildern ===== */
        body[data-theme="default"] { background-color: var(--bg); }
        <?php if (!empty($backgroundImages['default'])): ?>
        body[data-theme="default"] {
            background-image:
                linear-gradient(rgba(var(--wash-rgb), .8), rgba(var(--wash-rgb), .8)),
                url("<?= htmlspecialchars((string) $backgroundImages['default'], ENT_QUOTES, 'UTF-8') ?>");
            background-position: center top;
            background-size: cover;
        }
        <?php endif; ?>

        /* Eigenes Bild der Netzje-Party: gleich abgetönt */
        body[data-theme="default"].netzje-has-bg {
            background-image:
                linear-gradient(rgba(var(--wash-rgb), .8), rgba(var(--wash-rgb), .8)),
                var(--netzje-bg) !important;
        }

        /* Keine Linien-/Gittermuster mehr – nur ein sanfter grüner Schimmer oben */
        body[data-theme="default"]::before {
            inset: 0;
            background: radial-gradient(ellipse 55% 40% at 50% -8%, rgba(var(--accent-rgb), .07), transparent 70%);
        }
        body[data-theme="default"]::after { display: none; }

        /* ===== Keine abgeschnittenen Ecken mehr ===== */
        .primary-button, .secondary-button, .danger-button, .compact-button,
        .tag-chip, .install-app-button, .blog-teaser-label, .section-cta,
        .board, .achievements, .account-strip, .blog-panel, .games-panel, .sidebar-box,
        .post-card, .achievement-card, dialog, .blog-teaser, .info-icon-button,
        .stats-period, .stats-avatar { clip-path: none; }

        /* ===== Infoleiste ===== */
        .topbar {
            background: rgba(12, 15, 20, .85);
            border-bottom: 1px solid var(--line);
        }

        .topbar-tagline.subtitle {
            color: var(--muted);
            font-family: var(--font-ui);
            font-size: .72rem;
            font-weight: 600;
            letter-spacing: .12em;
        }

        .topbar-tagline .shine {
            font-weight: 800;
            background: linear-gradient(100deg, var(--muted) 30%, #fff 45%, var(--accent) 52%, var(--muted) 66%);
            background-size: 250% 100%;
            -webkit-background-clip: text;
            background-clip: text;
        }

        .install-app-button {
            height: 26px;
            padding: 0 12px 0 9px;
            border-radius: 999px;
            color: var(--accent-ink);
            background: var(--accent);
            font-family: var(--font-ui);
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .08em;
        }

        .install-app-button img { filter: brightness(0); }
        .install-app-button:hover { background: var(--accent-hover); }

        /* ===== Kopfband ===== */
        .masthead {
            border-bottom: 1px solid var(--line);
            background: rgba(16, 20, 27, .88);
            box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }

        body[data-theme="summer"] .masthead { background: rgba(6, 16, 30, .88); }
        body[data-theme="winter"] .masthead { background: rgba(17, 18, 20, .88); }

        .masthead::after,
        body[data-theme="summer"] .masthead::after,
        body[data-theme="winter"] .masthead::after { display: none; }

        .masthead-row { min-height: 72px; }

        .brand-logo { width: 42px; height: 42px; filter: none; }

        .brand-name {
            font-family: var(--font-ui);
            font-size: 1.35rem;
            font-weight: 900;
            font-style: normal;
            letter-spacing: -.01em;
        }

        .brand-accent { color: var(--accent); }

        .main-nav { gap: 30px; }

        .nav-link {
            color: #c4cbd6;
            font-family: var(--font-ui);
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .08em;
        }

        .nav-link::after {
            bottom: 0;
            height: 3px;
            border-radius: 3px 3px 0 0;
            background: var(--accent);
        }

        .nav-link:hover { color: #fff; }
        .nav-link.active { color: var(--accent); }

        /* ===== Seitentitel als großes Banner (NAVI-Hero) ===== */
        .page-hero {
            padding: 28px 0 4px;
            background: none;
        }

        .page-hero-inner {
            width: min(1152px, calc(100% - 28px));
            overflow: hidden;
            padding: clamp(32px, 6vw, 64px) clamp(22px, 5vw, 56px);
            border: 1px solid var(--line);
            border-radius: 24px;
            background:
                radial-gradient(ellipse 60% 90% at 100% 0%, rgba(var(--accent-rgb), .16), transparent 65%),
                linear-gradient(110deg, rgba(var(--panel-deep-rgb), .96) 0%, rgba(var(--panel-rgb), .78) 55%, rgba(var(--panel-rgb), .45) 100%);
            box-shadow: var(--shadow);
        }

        /* Großes Logo als Wasserzeichen rechts */
        .page-hero-inner::before {
            content: "";
            left: auto;
            right: clamp(-40px, -2vw, 0px);
            top: 50%;
            bottom: auto;
            width: clamp(180px, 30vw, 340px);
            aspect-ratio: 1;
            transform: translateY(-50%);
            background: url("assets/kellerkinder-logo.svg") center / contain no-repeat;
            opacity: .1;
            pointer-events: none;
        }

        .page-hero-crumb,
        .page-hero-title,
        .page-hero-lead { position: relative; margin-left: 0; }

        .page-hero-crumb {
            display: inline-flex;
            align-items: center;
            margin-bottom: 18px;
            padding: 5px 12px;
            border-radius: 999px;
            color: var(--accent-ink);
            background: var(--accent);
            font-family: var(--font-ui);
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .1em;
        }

        .page-hero-crumb span { color: inherit; opacity: .55; }

        .page-hero-title {
            max-width: 15ch;
            font-family: var(--font-ui);
            font-size: clamp(2.3rem, 6.4vw, 4.6rem);
            font-weight: 900;
            line-height: .98;
            letter-spacing: -.025em;
            text-shadow: none;
        }

        .page-hero-lead {
            max-width: 560px;
            margin-top: 16px;
            color: #b5bdca;
            font-size: 1.02rem;
        }

        .page-shell { padding-top: 22px; }

        /* ===== Abschnittstitel: groß, fett, ohne Linien ===== */
        .section-title,
        .board-heading {
            gap: 10px;
            font-family: var(--font-ui);
            font-size: clamp(1.45rem, 3.4vw, 2.1rem);
            font-weight: 900;
            letter-spacing: -.015em;
            line-height: 1.05;
        }

        .section-title::before,
        .section-title::after { display: none; }
        .section-title .accent { margin-right: 0; color: var(--accent); }

        .card-title,
        .modal-title,
        .post-card h2,
        .achievement-card-head h3 {
            font-family: var(--font-ui);
            font-weight: 800;
            letter-spacing: -.005em;
        }

        .card-title,
        .modal-title,
        .post-card h2 { padding-left: 0; }

        .card-title::before,
        .modal-title::before,
        .post-card h2::before { display: none; }

        .modal-title { font-size: 1.4rem; }

        /* ===== Buttons ===== */
        .primary-button,
        .secondary-button,
        .danger-button,
        .compact-button {
            border-radius: 10px;
            font-family: var(--font-ui);
            font-size: .8rem;
            font-weight: 800;
            letter-spacing: .07em;
        }

        .primary-button,
        body[data-theme="summer"] .primary-button,
        body[data-theme="winter"] .primary-button { color: var(--accent-ink); background: var(--accent); }

        .primary-button:hover,
        body[data-theme="summer"] .primary-button:hover,
        body[data-theme="winter"] .primary-button:hover {
            color: var(--accent-ink);
            background: var(--accent-hover);
            box-shadow: 0 8px 24px rgba(var(--accent-rgb), .25);
        }

        .secondary-button,
        .compact-button {
            color: #fff;
            background: rgba(255, 255, 255, .04);
            box-shadow: inset 0 0 0 1px var(--line-strong);
        }

        .secondary-button:hover,
        .compact-button:hover {
            color: var(--accent);
            background: rgba(var(--accent-rgb), .08);
            box-shadow: inset 0 0 0 1px var(--accent);
        }

        .danger-button {
            color: var(--red);
            background: rgba(255, 91, 109, .08);
            box-shadow: inset 0 0 0 1px rgba(255, 91, 109, .5);
        }

        .danger-button:hover { color: #fff; background: var(--red); }

        .compact-button { font-size: .74rem; }

        /* ===== Flächen (Panels): abgerundet, feiner Rahmen ===== */
        .board, .achievements, .account-strip, .blog-panel, .games-panel, .sidebar-box,
        .server-panel, .netzje-panel {
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: rgba(var(--panel-deep-rgb), .82);
            box-shadow: var(--shadow);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }

        .board { overflow: hidden; }

        /* ===== Karten ===== */
        .achievement-card, .post-card, .server-card, .netzje-box, .stats-tile, .instruction-item,
        .admin-user-card, .discord-note {
            border: 1px solid var(--line);
            border-radius: 14px;
            background: rgba(var(--panel-rgb), .92);
            box-shadow: none;
            transition: border-color .15s ease, background-color .15s ease, transform .15s ease;
        }

        .achievement-card:hover, .post-card:hover, .server-card:hover {
            border-color: rgba(var(--accent-rgb), .45);
            background: rgba(var(--panel-strong-rgb), .95);
            box-shadow: none;
            transform: translateY(-2px);
        }

        .achievement-icon { border-radius: 10px; }
        .achievement-edit-button, .nav-arrow { border-radius: 8px; }

        .nav-arrow { color: var(--accent-ink); background: var(--accent); }
        .nav-arrow:hover { background: var(--accent-hover); }

        .achievements-nav { border-radius: 999px; background: rgba(var(--panel-rgb), .9); }
        .achievements-nav-label { font-family: var(--font-ui); font-size: .72rem; font-weight: 800; }

        .achievement-links li, .achievement-links li a, .d4-milestones li { border-radius: 10px; }
        .achievement-links li a { color: var(--accent); }
        .achievement-links li a:hover { background: rgba(var(--accent-rgb), .07); }
        .mplus-bar-label a, .steam-achievement-meta a, .steam-admin-link { color: var(--accent); }

        .mplus-bar-track, .mplus-bar-fill { border-radius: 999px; }
        .mplus-bar-fill { background: var(--accent); }

        .steam-achievement { border-radius: 10px; box-shadow: none; background: rgba(var(--panel-deep-rgb), .8); }
        .steam-achievement-icon { border-radius: 8px; }
        .steam-achievement-time { color: var(--accent); font-family: var(--font-ui); font-size: .7rem; font-weight: 800; }
        .steam-achievement-text strong,
        .achievement-links-head,
        .steam-missing strong { font-family: var(--font-ui); font-weight: 800; }
        .steam-achievement-text strong { font-size: .86rem; }
        .steam-missing { border-radius: 10px; }

        /* ===== Konto-Leiste ===== */
        .account-strip { padding: 14px 18px; }
        .account-summary strong { font-family: var(--font-ui); font-size: 1rem; font-weight: 800; }

        .avatar, .avatar-placeholder { border-radius: 50%; }
        .avatar-placeholder { color: var(--accent-ink); background: var(--accent); font-family: var(--font-ui); font-weight: 800; }

        .account-badge,
        .player-badge {
            border-radius: 999px;
            color: var(--accent-ink);
            background: var(--accent);
            font-family: var(--font-ui);
            font-size: .62rem;
            font-weight: 800;
        }

        .account-badge.admin,
        .player-badge.admin { color: #1a1204; background: var(--gold); }

        .setup-callout,
        .password-callout,
        .storage-warning {
            border-width: 1px;
            border-radius: var(--radius-md);
        }

        .setup-callout { border-color: rgba(var(--accent-rgb), .35); background: rgba(var(--accent-rgb), .08); }

        /* ===== Blog-Hinweis ===== */
        .blog-teaser {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: rgba(var(--panel-rgb), .9);
        }

        .blog-teaser:hover { border-color: rgba(var(--accent-rgb), .45); background: rgba(var(--panel-strong-rgb), .95); }

        .blog-teaser-label {
            color: var(--accent-ink);
            background: var(--accent);
            font-family: var(--font-ui);
            font-size: .7rem;
            font-weight: 800;
            clip-path: none;
        }

        .blog-teaser-title { font-family: var(--font-ui); font-size: .92rem; font-weight: 800; }
        .blog-teaser-arrow { color: var(--accent); }

        /* ===== Kalender ===== */
        .board { margin-top: 22px; }

        .legend-item { font-family: var(--font-ui); font-size: .74rem; font-weight: 700; letter-spacing: .06em; }
        .legend-icon { border-radius: 50%; transform: none; }

        thead th { background: rgba(var(--panel-strong-rgb), .96); }

        thead th.player-heading,
        .player-heading,
        .date-day,
        .date-value { font-family: var(--font-ui); }

        thead th.player-heading,
        .player-heading { font-size: .72rem; font-weight: 800; }
        .date-day { font-size: .8rem; font-weight: 800; letter-spacing: .06em; }
        .date-value { font-size: .66rem; }

        thead th.is-today { background: rgba(var(--accent-rgb), .12); box-shadow: inset 0 3px 0 var(--accent); }
        thead th.is-today .date-day { color: var(--accent); }
        tbody tr td.is-today { background: rgba(var(--accent-rgb), .04); }

        .today-tag,
        .past-tag { border-radius: 999px; font-family: var(--font-ui); }
        thead th.is-today .today-tag { color: var(--accent-ink); background: var(--accent); }

        .player-button { border-radius: 8px; font-family: var(--font-ui); font-size: .82rem; }
        .player-button:hover { border-color: var(--accent); }

        .status-button { border-radius: 8px; }
        .status-badge-main { font-family: var(--font-ui); font-size: .72rem; font-weight: 800; }

        .status-button.vacation { color: #b3a6ff; border-color: rgba(143, 123, 255, .45); background-color: rgba(143, 123, 255, .12); }

        .date-remove { border-radius: 6px; }
        .empty-state strong { color: var(--accent); font-family: var(--font-ui); font-weight: 800; }

        /* ===== Dialoge & Formulare ===== */
        dialog {
            border: 1px solid var(--line-strong);
            border-radius: 20px;
            background: var(--panel);
            box-shadow: 0 30px 70px rgba(0, 0, 0, .6);
        }

        dialog::backdrop { background: rgba(8, 11, 16, .75); backdrop-filter: blur(6px); }

        label { font-family: var(--font-ui); font-size: .74rem; font-weight: 800; }

        input[type="text"],
        input[type="password"],
        input[type="date"],
        input[type="datetime-local"],
        input[type="file"],
        select,
        textarea {
            border: 0;
            border-radius: 10px;
            background: rgba(var(--panel-deep-rgb), .95);
            box-shadow: inset 0 0 0 1px var(--line-strong);
        }

        input:focus,
        select:focus,
        textarea:focus { box-shadow: inset 0 0 0 1px var(--accent), 0 0 0 3px rgba(var(--accent-rgb), .15); }

        .remember-row input[type="checkbox"],
        .stats-admin-item input { accent-color: var(--accent); }

        .status-choice, .weekday-choice span, .instruction-number, .editing-note,
        .achievement-edit-row input, .achievement-edit-row button, .toast { border-radius: 10px; }

        .status-choice:hover, .status-choice.selected { border-color: var(--accent); }
        .status-choice.selected { background: rgba(var(--accent-rgb), .1); }

        .weekday-choice input:checked + span { color: var(--accent-ink); border-color: var(--accent); background: var(--accent); }

        .instruction-number { color: var(--accent-ink); background: var(--accent); font-family: var(--font-ui); }
        .instruction-item strong { color: var(--accent); }

        .info-icon-button {
            border-radius: 50%;
            color: var(--accent-ink);
            background: var(--accent);
            font-family: var(--font-ui);
            font-weight: 900;
        }

        .info-icon-button:hover { color: var(--accent-ink); background: var(--accent-hover); }

        .section-heading { color: var(--accent); }

        /* ===== Fußzeile: dunkles Band ===== */
        .site-footer {
            background: rgba(12, 15, 20, .94);
            box-shadow: none;
            border-top: 1px solid var(--line);
            font-family: var(--font-ui);
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .1em;
        }

        .site-footer::before {
            font-family: var(--font-ui);
            font-size: 1.6rem;
            font-style: normal;
            font-weight: 900;
            letter-spacing: -.01em;
            border-top: 0;
            padding: 40px 16px 14px;
            color: #fff;
        }

        .site-footer .heart { color: var(--accent); }

        /* ===== Blog ===== */
        .post-meta { color: var(--accent); font-family: var(--font-ui); font-size: .72rem; font-weight: 800; }
        .post-card h2 { font-size: 1.45rem; letter-spacing: -.01em; }
        .post-body a { color: var(--accent); }
        .post-body blockquote { border-left-color: var(--accent); }

        .draft-flag { border-radius: 999px; }

        .tag-chip {
            border-radius: 999px;
            background: rgba(255, 255, 255, .05);
            box-shadow: inset 0 0 0 1px var(--line-strong);
            font-family: var(--font-ui);
            font-size: .68rem;
            font-weight: 800;
        }

        .tag-chip:hover { color: var(--accent); background: rgba(var(--accent-rgb), .08); }
        .tag-chip.active { color: var(--accent-ink); background: var(--accent); box-shadow: none; }

        .editor-toolbar button, .editor-surface { border-radius: 10px; }

        /* ===== Spiele / Statistik ===== */
        .stats-period {
            border-radius: 999px;
            background: rgba(255, 255, 255, .05);
            box-shadow: inset 0 0 0 1px var(--line-strong);
            font-family: var(--font-ui);
            font-size: .72rem;
            font-weight: 800;
        }

        .stats-period:hover, .stats-period:focus-visible { color: var(--accent); background: rgba(var(--accent-rgb), .08); }
        .stats-period.active { color: var(--accent-ink); background: var(--accent); box-shadow: none; }

        .stats-tile strong { font-family: var(--font-ui); font-weight: 900; letter-spacing: -.02em; }
        .stats-tile span { font-family: var(--font-ui); font-size: .68rem; font-weight: 700; }
        .stats-tile { box-shadow: none; }

        .stats-section { padding-left: 0; font-family: var(--font-ui); font-size: .95rem; font-weight: 900; }
        .stats-section::before { display: none; }

        .stats-row { overflow: hidden; border-radius: 12px; background: rgba(var(--panel-rgb), .92); }
        .stats-bar { background: linear-gradient(90deg, rgba(var(--accent-rgb), .22), rgba(var(--accent-rgb), .04)); }
        .stats-rank { color: var(--accent); font-family: var(--font-ui); font-size: .9rem; font-weight: 900; }
        .stats-name, .stats-time { font-family: var(--font-ui); font-size: .9rem; }
        .stats-row.expandable:focus-visible { outline-color: var(--accent); }
        .stats-avatar { border-radius: 50%; }
        .stats-icon { border-radius: 8px; }
        .stats-detail { border-radius: 12px; box-shadow: none; border: 1px solid var(--line); }
        .stats-detail-list li { border-radius: 8px; }
        .stats-admin-item { border-radius: 10px; }
        .stats-admin-item:has(input:checked) { box-shadow: inset 0 0 0 1px rgba(var(--accent-rgb), .5); }

        /* ===== Gameserver ===== */
        .server-card.is-online { border-color: rgba(var(--accent-rgb), .45); }
        .server-logo { border-radius: 12px; }
        .server-title h3 { font-family: var(--font-ui); font-weight: 800; }
        .server-status { border-radius: 999px; }
        .server-mini { border-radius: 8px; }

        /* ===== Netzje ===== */
        .netzje-when { font-family: var(--font-ui); font-weight: 900; letter-spacing: -.01em; }
        .netzje-timeline-wrap, .netzje-stream { border-radius: 14px; }
        body[data-theme="default"] .netzje-block {
            border-color: rgba(var(--accent-rgb), .55);
            background: linear-gradient(135deg, rgba(var(--accent-rgb), .26), rgba(var(--accent-rgb), .08));
        }
        .netzje-countdown { font-family: var(--font-ui); font-size: .7rem; }

        /* ===== Mobil ===== */
        @media (max-width: 680px) {
            .masthead-row { padding-top: 10px; }
            .main-nav { gap: 20px; }
            .nav-link { font-size: .74rem; }
            .nav-link::after { bottom: 0; }
            .page-hero { padding-top: 14px; }
            .page-hero-inner { width: calc(100% - 16px); border-radius: 18px; }
            .page-hero-crumb { margin-bottom: 14px; font-size: .6rem; }
            .page-hero-lead { font-size: .92rem; }
            .board, .achievements, .account-strip, .blog-panel, .games-panel, .sidebar-box,
            .server-panel, .netzje-panel { border-radius: 14px; }
            .status-badge-main { font-size: .72rem; }
            .primary-button, .secondary-button, .danger-button { font-size: .76rem; }
        }
    </style>
