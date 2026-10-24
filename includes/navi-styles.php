<?php
// „NAVI“-Design: angelehnt an navi.gg (Seite „Publications“).
// Aufbau als mittige Spalte: schwarze Infoleiste, dunkles Kopfband, darunter ein
// hellgrauer Inhaltsbereich mit weißen, eckigen Karten und dunkler Schrift,
// unten eine dunkle Fußzeile. Neben der Spalte ist der Hintergrund zu sehen:
// dunkles Graublau mit 80 % Deckkraft über den eigenen Bildern (Bild zu 20 % sichtbar).
// Akzentfarbe: helles Grün statt NAVI-Gelb (Flächen, Rahmen, Markierungen);
// für Schrift auf Hell ein dunkleres Grün, damit es lesbar bleibt.
// Wird auf allen Seiten als LETZTE Stilschicht eingebunden (nach
// includes/arena-styles.php). Sommer- und Winter-Theme behalten ihre Akzentfarbe.
if (!isset($backgroundImages) || !is_array($backgroundImages)) { $backgroundImages = []; }
?>
    <style>
        /* ===== Farben & Schriften ===== */
        :root {
            --font-body: "Inter", -apple-system, "Segoe UI", sans-serif;
            --font-heading: "Inter", -apple-system, "Segoe UI", sans-serif;
            --font-ui: "Inter", -apple-system, "Segoe UI", sans-serif;
            --radius-lg: 0;
            --radius-md: 0;
            --radius-sm: 0;
            --column: 1180px;
            --gutter: 24px;
            /* Schrift auf Akzentflächen */
            --accent-ink: #0b1406;
            /* Graublau der Fläche über dem Hintergrundbild */
            --wash-rgb: 27, 31, 39;
            /* Heller Inhaltsbereich */
            --surface: #eceef1;
        }

        body[data-theme="default"] {
            --bg: #1b1f27;
            --accent: #9ef06a;
            --accent-hover: #b6fb8a;
            --accent-rgb: 158, 240, 106;
            --accent-text: #3a8a10;
            --primary: #9ef06a;
            --primary-hover: #b6fb8a;
            --primary-rgb: 158, 240, 106;
            --band: #121419;
            --band-strong: #000;
            --sheen: none;
        }

        body[data-theme="summer"] { --accent-ink: #1a0c03; --accent-text: #c2561c; }
        body[data-theme="winter"] { --accent-ink: #ffffff; --accent-text: #d42a40; }

        body { font-family: var(--font-body); line-height: 1.55; -webkit-font-smoothing: antialiased; }

        /* ===== Hintergrund neben der Spalte: Graublau mit 80 % Deckkraft über den Bildern ===== */
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

        body[data-theme="default"]::before,
        body[data-theme="default"]::after { display: none; }

        /* ===== Heller Inhaltsbereich: Farben für Hell ===== */
        .page-shell, .page-hero, dialog {
            --text: #1a1d23;
            --muted: #6a7280;
            --panel: #ffffff;
            --panel-strong: #f7f8fa;
            --panel-soft: #f1f3f6;
            --panel-deep: #ffffff;
            --panel-rgb: 255, 255, 255;
            --panel-strong-rgb: 247, 248, 250;
            --panel-soft-rgb: 241, 243, 246;
            --panel-deep-rgb: 255, 255, 255;
            --line: #e2e5ea;
            --line-strong: #cfd4dc;
            --online: #17904b;
            --late: #b37300;
            --absent: #cf3449;
            --vacation: #5b45d6;
            --open: #8a929e;
            --red: #cf3449;
            --green: #17904b;
            --gold: #a86b00;
            --shadow: none;
            color: var(--text);
            color-scheme: light;
        }

        /* Fest eingestellte weiße Schrift aus älteren Schichten → dunkel */
        .page-shell :is(.section-title, .board-heading, .card-title, .post-card h2, .post-body h2, .post-body h3, .post-body h4,
            .blog-head h1, .sidebar-box h2, .achievement-card-head h3, .achievements-head h2, .account-summary strong,
            .stats-tile strong, .stats-section, .stats-name, .server-title h3, .server-rows dd, .netzje-when,
            .netzje-box h3, .netzje-list h3, .netzje-day, .netzje-row .game, .netzje-chip.me, .netzje-block,
            .steam-achievement-text strong, .achievement-links-head, .steam-missing strong, .stats-admin code,
            .discord-note h3, .discord-note code, .player-heading, thead th, .date-day, .legend-item,
            .achievements-nav-label, .instruction-item p, .editing-note, .mplus-bar-label),
        dialog :is(.modal-title, label, .admin-user-card strong, .install-steps li, .editing-note, .instruction-item p,
            .discord-note h3, .discord-note p, .discord-note code) { color: var(--text); }

        .page-shell :is(.account-summary, .empty-state, .loading, .discord-note p, .widget-loading, .widget-empty),
        dialog :is(.modal-subtitle, .field-help, .remember-row, .remember-row span) { color: var(--muted); }

        .page-shell .empty-state strong,
        dialog .section-heading { color: var(--accent-text); }

        /* ===== Spalte: Infoleiste, Kopfband, Titel, Inhalt, Fußzeile ===== */
        .topbar,
        .masthead,
        .page-hero,
        .page-shell {
            width: min(var(--column), 100%);
            margin-left: auto;
            margin-right: auto;
        }

        .topbar-inner,
        .masthead-row,
        .page-hero-inner {
            width: 100%;
            padding-left: var(--gutter);
            padding-right: var(--gutter);
        }

        /* ===== Infoleiste (schwarz) ===== */
        .topbar {
            background: #000;
            border-bottom: 0;
        }

        .topbar-inner { min-height: 40px; }

        .topbar-tagline.subtitle {
            color: #9aa1ad;
            font-family: var(--font-ui);
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .14em;
        }

        .topbar-tagline .shine {
            font-weight: 800;
            background: linear-gradient(100deg, #9aa1ad 30%, #fff 45%, var(--accent) 52%, #9aa1ad 66%);
            background-size: 250% 100%;
            -webkit-background-clip: text;
            background-clip: text;
        }

        .install-app-button {
            height: 28px;
            padding: 0 14px 0 10px;
            border-radius: 0;
            clip-path: none;
            color: var(--accent-ink);
            background: var(--accent);
            font-family: var(--font-ui);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .12em;
        }

        .install-app-button img { filter: brightness(0); }
        .install-app-button:hover { background: var(--accent-hover); }

        /* ===== Kopfband (dunkel) ===== */
        .masthead,
        body[data-theme="summer"] .masthead,
        body[data-theme="winter"] .masthead {
            border-bottom: 0;
            background: #1c1f25;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }

        .masthead::after,
        body[data-theme="summer"] .masthead::after,
        body[data-theme="winter"] .masthead::after { display: none; }

        .masthead-row { min-height: 84px; }

        .brand-logo { width: 46px; height: 46px; filter: none; }

        .brand-name {
            font-family: var(--font-ui);
            font-size: 1.35rem;
            font-weight: 900;
            font-style: normal;
            letter-spacing: .01em;
        }

        .brand-accent { color: var(--accent); }

        .main-nav { gap: 34px; }

        .nav-link {
            color: #fff;
            font-family: var(--font-ui);
            font-size: .84rem;
            font-weight: 800;
            letter-spacing: .12em;
        }

        .nav-link::after {
            bottom: 0;
            height: 4px;
            border-radius: 0;
            background: var(--accent);
        }

        .nav-link:hover { color: var(--accent); }
        .nav-link.active { color: #fff; }

        /* ===== Seitentitel (auf Hell, wie „News & Coverages“) ===== */
        .page-hero {
            padding: 46px 0 6px;
            background: var(--surface);
        }

        .page-hero-inner {
            overflow: visible;
            border: 0;
            border-radius: 0;
            background: none;
            box-shadow: none;
        }

        .page-hero-inner::before { display: none; }

        .page-hero-inner::after {
            content: "";
            display: block;
            width: 64px;
            height: 5px;
            margin-top: 22px;
            background: var(--accent);
        }

        .page-hero-crumb,
        .page-hero-title,
        .page-hero-lead { position: relative; margin-left: 0; }

        .page-hero-crumb {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
            padding: 0;
            color: var(--text);
            background: none;
            font-family: var(--font-ui);
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .14em;
        }

        .page-hero-crumb::before {
            content: "";
            width: 22px;
            height: 22px;
            flex: none;
            background: var(--accent) url("assets/kellerkinder-logo.svg") center / 16px no-repeat;
        }

        .page-hero-crumb span { margin: 0 2px; color: var(--muted); opacity: 1; }

        .page-hero-title {
            max-width: none;
            color: var(--text);
            font-family: var(--font-ui);
            font-size: clamp(2rem, 5vw, 3.4rem);
            font-weight: 900;
            line-height: 1.02;
            letter-spacing: -.01em;
            text-shadow: none;
        }

        .page-hero-lead {
            max-width: 640px;
            margin-top: 12px;
            color: var(--muted);
            font-size: 1.02rem;
        }

        .page-shell {
            padding: 26px var(--gutter) 0;
            background: var(--surface);
        }

        /* ===== Abschnittstitel: dunkel, fett, Versalien; Akzentwort mit grünem Marker ===== */
        .section-title,
        .board-heading {
            gap: 12px;
            margin-bottom: 20px;
            font-family: var(--font-ui);
            font-size: clamp(1.3rem, 3vw, 1.75rem);
            font-weight: 900;
            letter-spacing: .01em;
            line-height: 1.1;
        }

        .section-title::before,
        .section-title::after { display: none; }

        .section-title .accent {
            margin-right: 0;
            padding: 0 .1em;
            color: inherit;
            background: linear-gradient(transparent 60%, rgba(var(--accent-rgb), .9) 60%);
        }

        .card-title,
        .modal-title,
        .post-card h2,
        .achievement-card-head h3 {
            font-family: var(--font-ui);
            font-weight: 800;
            letter-spacing: 0;
        }

        .card-title,
        .modal-title,
        .post-card h2 { padding-left: 0; }

        .card-title::before,
        .modal-title::before,
        .post-card h2::before { display: none; }

        .modal-title { font-size: 1.4rem; text-transform: uppercase; }

        /* ===== Eckige Formen überall ===== */
        .primary-button, .secondary-button, .danger-button, .compact-button,
        .tag-chip, .blog-teaser-label, .section-cta, .info-icon-button, .stats-period, .stats-avatar,
        .board, .achievements, .account-strip, .blog-panel, .games-panel, .sidebar-box,
        .post-card, .achievement-card, dialog, .blog-teaser { clip-path: none; border-radius: 0; }

        /* ===== Buttons ===== */
        .primary-button,
        .secondary-button,
        .danger-button,
        .compact-button {
            border-radius: 0;
            font-family: var(--font-ui);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .1em;
        }

        .primary-button,
        body[data-theme="summer"] .primary-button,
        body[data-theme="winter"] .primary-button { color: var(--accent-ink); background: var(--accent); box-shadow: none; }

        .primary-button:hover,
        body[data-theme="summer"] .primary-button:hover,
        body[data-theme="winter"] .primary-button:hover { color: var(--accent-ink); background: var(--accent-hover); }

        .secondary-button,
        .compact-button {
            color: var(--text);
            background: #fff;
            box-shadow: inset 0 0 0 1px var(--line-strong);
        }

        .secondary-button:hover,
        .compact-button:hover {
            color: var(--text);
            background: rgba(var(--accent-rgb), .18);
            box-shadow: inset 0 0 0 2px var(--accent);
        }

        .danger-button {
            color: var(--red);
            background: #fff;
            box-shadow: inset 0 0 0 1px rgba(207, 52, 73, .55);
        }

        .danger-button:hover { color: #fff; background: var(--red); }

        .compact-button { font-size: .72rem; }

        /* ===== Flächen ===== */
        /* Kasten-Flächen: weiß mit feinem Rahmen */
        .board, .account-strip, .sidebar-box, .netzje-panel {
            border: 1px solid var(--line);
            border-radius: 0;
            background: #fff;
            box-shadow: none;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }

        /* Abschnitte ohne Kasten: Inhalt liegt direkt auf dem hellen Grau */
        .achievements, .blog-panel, .games-panel, .server-panel {
            margin-top: 44px;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: none;
            box-shadow: none;
            backdrop-filter: none;
            -webkit-backdrop-filter: none;
        }

        .blog-panel, .games-panel, .server-panel { margin-top: 18px; }

        /* ===== Karten: weiß, eckig, beim Überfahren grün umrandet (wie NAVIs gelber Rahmen) ===== */
        .achievement-card, .post-card, .server-card, .stats-tile, .stats-row, .instruction-item,
        .admin-user-card, .discord-note, .blog-teaser {
            border: 1px solid var(--line);
            border-radius: 0;
            background: #fff;
            box-shadow: none;
            transition: box-shadow .15s ease, background-color .15s ease;
        }

        .achievement-card:hover, .post-card:hover, .server-card:hover, .blog-teaser:hover, .stats-row.expandable:hover {
            transform: none;
            border-color: var(--line);
            background: #fff;
            box-shadow: inset 0 0 0 3px var(--accent);
        }

        .netzje-box { border: 1px solid var(--line); border-radius: 0; background: var(--panel-strong); }

        /* ===== Konto-Leiste ===== */
        .account-strip { padding: 14px 18px; }
        .account-summary strong { font-family: var(--font-ui); font-size: 1rem; font-weight: 800; }

        .avatar, .avatar-placeholder { border-radius: 0; }
        .avatar-placeholder { color: var(--accent-ink); background: var(--accent); font-family: var(--font-ui); font-weight: 800; }

        .account-badge,
        .player-badge {
            border-radius: 0;
            color: var(--accent-ink);
            background: var(--accent);
            font-family: var(--font-ui);
            font-size: .6rem;
            font-weight: 800;
        }

        .account-badge.admin,
        .player-badge.admin { color: #2a1c00; background: #f5c542; }

        .setup-callout,
        .password-callout,
        .storage-warning,
        .forced-password-note {
            border: 1px solid;
            border-radius: 0;
            box-shadow: none;
        }

        .setup-callout { color: var(--text); border-color: var(--accent); background: rgba(var(--accent-rgb), .18); }
        .password-callout,
        .storage-warning,
        .forced-password-note { color: #5a3d00; border-color: #efc46a; background: #fff6e2; }
        .storage-warning strong, .storage-warning code { color: #3d2900; }

        /* ===== Blog-Hinweis ===== */
        .blog-teaser { overflow: hidden; }

        .blog-teaser-label {
            color: var(--accent-ink);
            background: var(--accent);
            font-family: var(--font-ui);
            font-size: .7rem;
            font-weight: 800;
        }

        .blog-teaser-title { color: var(--text); font-family: var(--font-ui); font-size: .95rem; font-weight: 800; }
        .blog-teaser-meta { color: var(--muted); font-family: var(--font-ui); font-size: .7rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; }
        .blog-teaser-arrow { color: var(--accent-text); }

        /* ===== Kalender ===== */
        .board { margin-top: 22px; }

        .legend-item { font-family: var(--font-ui); font-size: .72rem; font-weight: 800; letter-spacing: .1em; }
        .legend-icon { border-radius: 0; transform: none; }

        .page-shell th,
        .page-shell td { border-color: var(--line); }

        .page-shell thead th { background: var(--panel-strong); }
        .page-shell tbody td,
        .page-shell tbody th { background: #fff; }
        .page-shell tbody tr:nth-child(even) td,
        .page-shell tbody tr:nth-child(even) th { background: #fafbfc; }
        .page-shell .player-cell { background: var(--panel-strong) !important; }
        .page-shell tbody tr:hover td { background-color: var(--panel-soft); }

        thead th.player-heading,
        .player-heading,
        .date-day,
        .date-value { font-family: var(--font-ui); }

        thead th.player-heading,
        .player-heading { color: var(--muted); font-size: .7rem; font-weight: 800; letter-spacing: .14em; }
        .date-day { font-size: .8rem; font-weight: 900; letter-spacing: .08em; }
        .date-value { color: var(--muted); font-size: .66rem; }

        .page-shell thead th.is-today { background: rgba(var(--accent-rgb), .3); box-shadow: inset 0 4px 0 var(--accent); }
        .page-shell thead th.is-today .date-day { color: var(--text); }
        .page-shell tbody tr td.is-today { background: rgba(var(--accent-rgb), .1); }

        .today-tag,
        .past-tag { border-radius: 0; font-family: var(--font-ui); }
        thead th.is-today .today-tag { color: var(--accent-ink); background: var(--accent); }
        .past-tag { color: var(--muted); background: var(--panel-soft); }

        .player-button {
            border-radius: 0;
            color: var(--text);
            background: #fff;
            font-family: var(--font-ui);
            font-size: .82rem;
        }

        .player-button:hover { border-color: var(--accent); box-shadow: inset 0 0 0 1px var(--accent); }

        .status-button { border-radius: 0; }
        .status-badge-main { font-family: var(--font-ui); font-size: .72rem; font-weight: 800; letter-spacing: .04em; }
        .status-button.vacation { color: var(--vacation); border-color: rgba(91, 69, 214, .4); background-color: rgba(91, 69, 214, .08); }
        .status-button.open { background-color: #f4f5f7; }
        .status-button:disabled:hover,
        .player-button.readonly:hover { box-shadow: none; }
        .recurring-tag { color: var(--muted); background: #fff; border-color: var(--line-strong); }

        .date-remove { border-radius: 0; }
        .empty-state strong { font-family: var(--font-ui); font-weight: 900; }

        /* ===== Erfolge ===== */
        .achievements-nav { border: 1px solid var(--line); border-radius: 0; background: #fff; }
        .achievements-nav-label { font-family: var(--font-ui); font-size: .72rem; font-weight: 800; }
        .nav-arrow { border-radius: 0; color: var(--accent-ink); background: var(--accent); }
        .nav-arrow:hover { background: var(--accent-hover); }

        .achievement-card-head h3 { font-size: 1.05rem; text-transform: uppercase; }
        .achievement-icon, .achievement-edit-button { border-radius: 0; }
        .achievement-edit-button { color: var(--text); background: #fff; border-color: var(--line-strong); }

        .achievement-links li, .d4-milestones li { border-radius: 0; border-color: var(--line); background: var(--panel-strong); }
        .achievement-links li a { border-radius: 0; color: var(--accent-text); }
        .achievement-links li a:hover { background: rgba(var(--accent-rgb), .15); }
        .mplus-bar-label a, .steam-achievement-meta a, .steam-admin-link { color: var(--accent-text); }
        .mplus-bar-label b { color: var(--muted); background: var(--panel-soft); border-color: var(--line-strong); }

        .mplus-bar-track { border-radius: 0; background: #e3e6eb; }
        .mplus-bar-fill { border-radius: 0; background: var(--accent); }

        .steam-achievement { border-radius: 0; background: var(--panel-strong); box-shadow: inset 3px 0 0 var(--accent); }
        .steam-achievement-icon { border-radius: 0; }
        .steam-achievement-time { color: var(--muted); font-family: var(--font-ui); font-size: .68rem; font-weight: 800; letter-spacing: .1em; }
        .steam-achievement-text strong,
        .achievement-links-head,
        .steam-missing strong { font-family: var(--font-ui); font-weight: 800; }
        .steam-achievement-text strong { font-size: .86rem; }
        .steam-missing { border-radius: 0; background: var(--panel-strong); }

        /* ===== Dialoge & Formulare ===== */
        dialog {
            border: 1px solid var(--line-strong);
            border-radius: 0;
            color: var(--text);
            background: #fff;
            box-shadow: inset 0 4px 0 var(--accent), 0 30px 70px rgba(0, 0, 0, .55);
        }

        dialog::backdrop { background: rgba(10, 12, 16, .75); backdrop-filter: blur(4px); }

        label { font-family: var(--font-ui); font-size: .72rem; font-weight: 800; letter-spacing: .1em; }

        :is(.page-shell, dialog) :is(input[type="text"], input[type="password"], input[type="date"],
            input[type="datetime-local"], input[type="file"], select, textarea) {
            border: 0;
            border-radius: 0;
            color: var(--text);
            background: #f6f7f9;
            box-shadow: inset 0 0 0 1px var(--line-strong);
            color-scheme: light;
        }

        :is(.page-shell, dialog) :is(input, select, textarea):focus { box-shadow: inset 0 0 0 2px var(--accent), 0 0 0 3px rgba(var(--accent-rgb), .25); }

        .remember-row input[type="checkbox"],
        .stats-admin-item input { accent-color: var(--accent-text); }

        .status-choice,
        .weekday-choice span,
        .achievement-edit-row input,
        .achievement-edit-row button,
        .editing-note,
        .instruction-number,
        .toast { border-radius: 0; }

        dialog .status-choice { color: var(--text); background: #fff; border-color: var(--line-strong); }
        dialog .status-choice:hover,
        dialog .status-choice.selected { border-color: var(--accent); }
        dialog .status-choice.selected { background: rgba(var(--accent-rgb), .18); }

        dialog .weekday-choice span { color: var(--text); background: #f6f7f9; border-color: var(--line-strong); }
        dialog .weekday-choice input:checked + span { color: var(--accent-ink); border-color: var(--accent); background: var(--accent); }

        dialog .achievement-edit-row input { color: var(--text); background: #f6f7f9; border-color: var(--line-strong); }
        dialog .admin-user-card { background: var(--panel-strong); }
        dialog .separator { background: var(--line); }

        .instruction-number { color: var(--accent-ink); background: var(--accent); font-family: var(--font-ui); }
        .instruction-item strong { color: var(--accent-text); }
        .editing-note strong { color: var(--accent-text); }
        .install-steps li::before { border-radius: 0; color: var(--accent-ink); border-color: transparent; background: var(--accent); }

        .info-icon-button {
            border-radius: 0;
            color: var(--accent-ink);
            background: var(--accent);
            font-family: var(--font-ui);
            font-weight: 900;
        }

        .info-icon-button:hover { color: var(--accent-ink); background: var(--accent-hover); }

        .toast { border-left: 4px solid var(--accent); }

        /* ===== Fußzeile: dunkles Band über die Breite der Spalte ===== */
        .site-footer {
            margin: 56px calc(-1 * var(--gutter)) 0;
            color: #8e95a1;
            background: #121419;
            box-shadow: none;
            border-top: 0;
            font-family: var(--font-ui);
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .12em;
            padding-left: 16px;
            padding-right: 16px;
        }

        .site-footer::before {
            font-family: var(--font-ui);
            font-size: 1.6rem;
            font-style: normal;
            font-weight: 900;
            letter-spacing: .01em;
            border-top: 5px solid var(--accent);
            margin: 0 -16px;
            padding: 38px 16px 14px;
            color: #fff;
        }

        .site-footer .heart { color: var(--accent); }

        /* ===== Blog ===== */
        .blog-layout { gap: 24px; }

        .post-card { padding: 24px; }
        .post-card h2 { font-size: 1.45rem; text-transform: none; letter-spacing: -.005em; line-height: 1.2; }

        .post-meta {
            color: var(--muted);
            font-family: var(--font-ui);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .12em;
        }

        .post-body a { color: var(--accent-text); }
        .post-body blockquote { border-left-color: var(--accent); background: var(--panel-strong); }
        .post-body pre { background: var(--panel-soft); color: var(--text); }

        .draft-flag { border-radius: 0; }

        .tag-chip {
            border-radius: 0;
            color: var(--text);
            background: #fff;
            box-shadow: inset 0 0 0 1px var(--line);
            font-family: var(--font-ui);
            font-size: .68rem;
            font-weight: 800;
        }

        .tag-chip:hover { color: var(--text); background: rgba(var(--accent-rgb), .18); }
        .tag-chip.active { color: var(--accent-ink); background: var(--accent); box-shadow: none; }

        .sidebar-box { padding: 22px; }
        .sidebar-box h2.card-title { font-size: 1.15rem; text-transform: uppercase; }

        .editor-toolbar { border-radius: 0; background: var(--panel-soft); }
        .editor-toolbar button { border-radius: 0; color: var(--text); }
        .editor-toolbar button:hover { background: rgba(var(--accent-rgb), .2); }
        .editor-surface { border-radius: 0; background: #fff; color: var(--text); }

        /* ===== Spiele / Statistik ===== */
        /* Zeitraum-Auswahl als Kacheln (wie NAVIs Spiele-Filter, „ALL“ in Akzentfarbe) */
        .stats-period {
            border-radius: 0;
            color: var(--text);
            background: #fff;
            box-shadow: inset 0 0 0 1px var(--line);
            font-family: var(--font-ui);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .1em;
        }

        .stats-period:hover, .stats-period:focus-visible { color: var(--text); background: rgba(var(--accent-rgb), .18); }
        .stats-period.active { color: var(--accent-ink); background: var(--accent); box-shadow: none; }

        .stats-tile strong { font-family: var(--font-ui); font-weight: 900; letter-spacing: -.02em; }
        .stats-tile span { color: var(--muted); font-family: var(--font-ui); font-size: .66rem; font-weight: 800; letter-spacing: .12em; }

        .stats-section { padding-left: 0; font-family: var(--font-ui); font-size: .95rem; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
        .stats-section::before { display: none; }

        .stats-row { overflow: hidden; }
        .stats-bar { background: linear-gradient(90deg, rgba(var(--accent-rgb), .4), rgba(var(--accent-rgb), .08)); }
        .stats-rank { color: var(--accent-text); font-family: var(--font-ui); font-size: .9rem; font-weight: 900; }
        .stats-name, .stats-time { font-family: var(--font-ui); font-size: .9rem; }
        .stats-row.expandable:focus-visible { outline-color: var(--accent-text); }
        .stats-caret { color: var(--accent-text); }
        .stats-avatar, .stats-icon { border-radius: 0; }
        .stats-detail { border: 1px solid var(--line); border-radius: 0; background: var(--panel-strong); box-shadow: inset 3px 0 0 var(--accent); }
        .stats-detail-list li { border-radius: 0; background: #fff; }
        .stats-admin-item { border: 1px solid var(--line); border-radius: 0; background: #fff; }
        .stats-admin-item:has(input:checked) { box-shadow: inset 3px 0 0 var(--accent-text); }

        /* ===== Gameserver ===== */
        .server-card.is-online { border-color: var(--line); box-shadow: inset 0 4px 0 var(--accent); }
        .server-card.is-online:hover { box-shadow: inset 0 0 0 3px var(--accent); }
        .server-logo { border-radius: 0; background: var(--panel-soft); }
        .server-title h3 { font-family: var(--font-ui); font-weight: 800; }
        .server-status { border-radius: 0; }
        .server-mini { border-radius: 0; background: var(--panel-soft); }
        .server-mini:hover, .server-mini:focus-visible { background: rgba(var(--accent-rgb), .2); }

        /* ===== Netzje ===== */
        .netzje-panel { margin-top: 22px; padding: 24px; }
        .netzje-when { font-family: var(--font-ui); font-weight: 900; }
        .netzje-countdown, .netzje-chip { border-radius: 0; font-family: var(--font-ui); }
        .netzje-chip { background: var(--panel-soft); }
        .netzje-chip.me { border-color: var(--accent); background: rgba(var(--accent-rgb), .2); }
        .netzje-timeline-wrap, .netzje-stream, .netzje-row, .netzje-block, .netzje-bg-preview { border-radius: 0; }
        .netzje-timeline-wrap { background: #fff; }
        .page-shell .netzje-tick { border-left-color: rgba(0, 0, 0, .12); }
        .page-shell .netzje-gridline { border-left-color: rgba(0, 0, 0, .06); }
        body[data-theme="default"] .netzje-block {
            border-color: var(--accent);
            background: rgba(var(--accent-rgb), .35);
        }
        .netzje-block:hover, .netzje-block:focus-visible { outline-color: var(--text); }
        .netzje-block.full { background: #e3e6eb; }

        /* ===== Mobil ===== */
        @media (max-width: 680px) {
            :root { --gutter: 12px; }
            .topbar-tagline.subtitle { font-size: .64rem; letter-spacing: .1em; }
            .masthead-row { min-height: 0; padding-top: 10px; }
            .main-nav { gap: 20px; }
            .nav-link { font-size: .74rem; }
            .nav-link::after { bottom: 0; height: 3px; }
            .page-hero { padding: 28px 0 2px; }
            .page-hero-inner::after { margin-top: 16px; }
            .page-hero-lead { font-size: .92rem; }
            .page-shell { padding-top: 18px; }
            .achievements { margin-top: 34px; }
            .post-card { padding: 16px; }
            .netzje-panel { padding: 16px 12px; }
            .status-badge-main { font-size: .7rem; letter-spacing: 0; }
            .primary-button, .secondary-button, .danger-button { font-size: .74rem; }
            .site-footer { margin-top: 40px; }
        }
    </style>
