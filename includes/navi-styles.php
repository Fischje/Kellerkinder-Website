<?php
// „NAVI“-Design: angelehnt an navi.gg (Seite „Publications“).
// Aufbau als mittige Spalte: schwarze Infoleiste, dunkles Kopfband, darunter ein
// hellgrauer Inhaltsbereich mit weißen, eckigen Karten und dunkler Schrift,
// unten eine dunkle Fußzeile. Neben der Spalte ist der Hintergrund zu sehen:
// dunkles Graublau mit 80 % Deckkraft über den eigenen Bildern (Bild zu 20 % sichtbar).
// Akzentfarbe: Grün #76e36c (Wunsch des Nutzers: #45e636, 10 % heller, 10 % weniger Sättigung) statt NAVI-Gelb (Flächen, Rahmen, Markierungen);
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
            --column: 1250px;
            --gutter: 27px;
            --sidebar: 380px;
            /* Schrift auf Akzentflächen */
            --accent-ink: #0b1406;
            /* Graublau der Fläche über dem Hintergrundbild */
            --wash-rgb: 27, 31, 39;
            /* Heller Inhaltsbereich */
            --surface: #eceef1;
        }

        body[data-theme="default"] {
            --bg: #1b1f27;
            --accent: #76e36c;
            --accent-hover: #63d059;
            --accent-rgb: 118, 227, 108;
            --accent-text: #217317;
            --primary: #76e36c;
            --primary-hover: #63d059;
            --primary-rgb: 118, 227, 108;
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
        .page-shell, .page-hero, .site-sidebar, dialog {
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
        .page-shell :is(.section-title, .board-heading, .post-card h2, .post-body h2, .post-body h3, .post-body h4,
            .blog-head h1, .achievement-card-head h3, .achievements-head h2,
            .stats-tile strong, .stats-section, .stats-name, .server-title h3, .server-rows dd, .netzje-when,
            .netzje-box h3, .netzje-list h3, .netzje-day, .netzje-row .game, .netzje-chip.me, .netzje-block,
            .steam-achievement-text strong, .achievement-links-head, .steam-missing strong, .stats-admin code,
            .discord-note h3, .discord-note code, .player-heading, thead th, .date-day, .legend-item,
            .achievements-nav-label, .instruction-item p, .editing-note, .mplus-bar-label),
        dialog :is(.modal-title, label, .admin-user-card strong, .install-steps li, .editing-note, .instruction-item p,
            .discord-note h3, .discord-note p, .discord-note code) { color: var(--text); }

        .page-shell :is(.empty-state, .loading, .discord-note p, .widget-loading, .widget-empty),
        dialog :is(.modal-subtitle, .field-help, .remember-row, .remember-row span) { color: var(--muted); }

        .page-shell .empty-state strong { color: var(--accent-text); }

        /* ===== Spalte: Infoleiste, Kopfband, Rahmen (Inhalt + Seitenleiste), Fußzeile ===== */
        .topbar,
        .masthead,
        .page-frame,
        .site-footer {
            width: min(var(--column), 100%);
            margin-left: auto;
            margin-right: auto;
        }

        /* Rahmen: links der hellgraue Inhalt, rechts die weiße Seitenleiste über die volle Höhe */
        .page-frame {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: minmax(0, 1fr) var(--sidebar);
            background: var(--surface);
        }

        .page-main { min-width: 0; }

        .page-hero,
        .page-shell {
            width: auto;
            margin: 0;
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

        /* Kopfleiste rechts: App-Knopf (nur Kalender) und Account auf allen Seiten */
        .topbar { position: relative; z-index: 30; }
        .topbar-actions { display: flex; align-items: center; gap: 10px; margin-left: auto; }
        .topbar-account { position: relative; }
        .topbar-actions .install-app-button { order: 0; }
        .topbar-actions .topbar-account { order: 1; }

        .topbar-account-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 28px;
            padding: 0 12px;
            border: 1px solid rgba(255, 255, 255, .28);
            color: #fff;
            background: transparent;
            font-family: var(--font-ui);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-decoration: none;
            text-transform: uppercase;
            cursor: pointer;
            transition: border-color .15s ease, color .15s ease;
        }

        .topbar-account-button:hover,
        .topbar-account-button:focus-visible,
        .topbar-account-button[aria-expanded="true"] { outline: none; border-color: var(--accent); color: var(--accent); }
        .topbar-account-button.is-user { padding-left: 4px; }

        .topbar-avatar { width: 20px; height: 20px; flex: none; object-fit: cover; }
        .topbar-avatar.placeholder { display: grid; place-items: center; color: var(--accent-ink); background: var(--accent); font-size: .66rem; letter-spacing: 0; }
        .topbar-account-name { max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; letter-spacing: .06em; }
        .topbar-caret { font-size: .7rem; letter-spacing: 0; }

        .topbar-menu {
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            z-index: 40;
            min-width: 200px;
            margin: 0;
            padding: 6px 0;
            list-style: none;
            border-top: 3px solid var(--accent);
            background: #fff;
            box-shadow: 0 18px 40px rgba(0, 0, 0, .35);
        }

        .topbar-menu[hidden] { display: none; }

        .topbar-menu-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
            padding: 11px 16px;
            border: 0;
            color: #1a1d23;
            background: none;
            font-family: var(--font-ui);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-align: left;
            text-decoration: none;
            text-transform: uppercase;
            cursor: pointer;
        }

        .topbar-menu-item:hover,
        .topbar-menu-item:focus-visible { outline: none; background: rgba(var(--accent-rgb), .18); }
        .topbar-menu li + li { border-top: 1px solid #eceef1; }

        /* Anmelde-Dialog: Weg zum Registrieren (die Konto-Leiste über dem Kalender gibt es nicht mehr) */
        .login-register-hint { margin: 0; color: var(--muted); font-size: .92rem; }
        .text-button {
            padding: 0;
            border: 0;
            color: var(--accent-text);
            background: none;
            font: inherit;
            font-weight: 800;
            text-decoration: underline;
            text-underline-offset: 3px;
            cursor: pointer;
        }

        .text-button:hover { color: var(--text); }
        .setup-callout-button { display: flex; margin-top: 12px; }
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

        .brand-logo { width: 60px; height: 60px; filter: none; }

        .brand-name {
            font-family: var(--font-ui);
            font-size: 1.35rem;
            font-weight: 900;
            font-style: normal;
            letter-spacing: .01em;
        }

        .brand-accent { color: var(--accent); }
        .brand-tld { margin-left: 2px; color: #9aa1ad; font-size: .42em; font-weight: 700; letter-spacing: .04em; vertical-align: baseline; }

        .main-nav { gap: 34px; }

        /* Hauptmenü scrollbar mit Pfeilen: Hülle trägt die Platzrolle, das Menü scrollt darin */
        .main-nav-wrap { position: relative; display: flex; flex: 1 1 0%; min-width: 0; margin: 0 0 0 auto; align-self: stretch; }
        .main-nav-wrap .main-nav { flex: 1 1 auto; width: 100%; margin: 0; justify-content: flex-start; scrollbar-width: none; -webkit-overflow-scrolling: touch; }
        .main-nav-wrap .main-nav::-webkit-scrollbar { display: none; }
        .main-nav-wrap .main-nav > :first-child { margin-left: auto; }
        .nav-arrow {
            position: absolute;
            top: 0;
            bottom: 0;
            z-index: 2;
            display: grid;
            place-items: center;
            width: 44px;
            padding: 0 0 4px;
            border: 0;
            color: var(--accent);
            font: 900 2rem/1 var(--font-ui);
            cursor: pointer;
        }
        .nav-arrow[hidden] { display: none; }
        .main-nav-wrap .nav-arrow { height: auto; min-height: 0; border-radius: 0; box-shadow: none; text-transform: none; letter-spacing: 0; }
        .main-nav-wrap .nav-arrow-left { left: 0; justify-content: start; padding-left: 4px; background: linear-gradient(90deg, #1c1f25 55%, rgba(28, 31, 37, 0)); }
        .main-nav-wrap .nav-arrow-right { right: 0; justify-content: end; padding-right: 4px; background: linear-gradient(270deg, #1c1f25 55%, rgba(28, 31, 37, 0)); }
        .main-nav-wrap .nav-arrow:hover { color: #fff; }
        .nav-arrow:focus-visible { outline: 2px solid var(--accent); outline-offset: -2px; }

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
            margin-bottom: 6px;
            padding: 0;
            color: var(--text);
            background: none;
            font-family: var(--font-ui);
            font-size: .76rem;
            font-weight: 800;
            letter-spacing: .14em;
        }

        .page-hero-title {
            max-width: none;
            color: var(--text);
            font-family: var(--font-ui);
            font-size: clamp(1.75rem, 4.3vw, 2.9rem);
            font-weight: 900;
            line-height: 1.02;
            letter-spacing: -.01em;
            text-shadow: none;
        }

        .page-hero-lead {
            max-width: 640px;
            margin-top: 6px;
            color: var(--muted);
            font-size: 1.02rem;
            line-height: 1.45;
        }

        .page-shell {
            padding: 26px var(--gutter) 64px;
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

        .modal-title,
        .post-card h2,
        .achievement-card-head h3 {
            font-family: var(--font-ui);
            font-weight: 800;
            letter-spacing: 0;
        }

        .modal-title,
        .post-card h2 { padding-left: 0; }

        .modal-title::before,
        .post-card h2::before { display: none; }

        .modal-title { font-size: 1.4rem; text-transform: uppercase; }

        /* ===== Eckige Formen überall ===== */
        .primary-button, .secondary-button, .danger-button,
        .tag-chip, .blog-teaser-label, .info-icon-button, .stats-period, .stats-avatar,
        .board, .achievements, .blog-panel, .games-panel,
        .post-card, .achievement-card, dialog, .blog-teaser { clip-path: none; border-radius: 0; }

        /* ===== Buttons ===== */
        .primary-button,
        .secondary-button,
        .danger-button {
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

        .secondary-button {
            color: var(--text);
            background: #fff;
            box-shadow: inset 0 0 0 1px var(--line-strong);
        }

        .secondary-button:hover {
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

        /* ===== Flächen ===== */
        /* Kasten-Flächen: weiß mit feinem Rahmen */
        .board, .netzje-panel {
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

        .status-button { border-radius: 0; padding: 5px 3px; }
        .status-cell { padding: 2px; }

        /* Neben der Seitenleiste ist der Kalender schmaler: Spielerspalte etwas enger */
        @media (min-width: 681px) {
            thead th.player-heading,
            .player-heading,
            .player-cell { width: 140px; min-width: 140px; }
        }
        .status-badge-main { font-family: var(--font-ui); font-size: .68rem; font-weight: 800; letter-spacing: 0; }
        /* Statusfelder kräftig getönt, damit sie sich klar von offenen Feldern abheben */
        .page-shell .status-button.online { color: #0b5e2b; border-color: #4cbf6e; background-color: #bdeec8; }
        .page-shell .status-button.late { color: #6b4300; border-color: #e3a42c; background-color: #ffe1a1; }
        .page-shell .status-button.absent { color: #8c1325; border-color: #e0657a; background-color: #ffc8d0; }
        .page-shell .status-button.vacation { color: #37239f; border-color: #8a74ef; background-color: #d8cfff; }
        .page-shell .status-button.open { color: #8a929e; border: 1px dashed #c9ced6; background-color: #f6f7f9; }
        .page-shell .status-button:not(.open) .status-badge-game,
        .page-shell .status-button:not(.open) .status-badge-note { color: inherit; opacity: .8; }
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
            input[type="datetime-local"], input[type="file"], input[type="number"], select, textarea) {
            border: 0;
            border-radius: 0;
            color: var(--text);
            background: #f6f7f9;
            box-shadow: inset 0 0 0 1px var(--line-strong);
            color-scheme: light;
        }

        /* Zahlenfelder (Plätze, Sitzplan-Raster) wie die übrigen Eingabefelder */
        :is(.page-shell, dialog) input[type="number"] { width: 100%; min-height: 47px; padding: 10px 12px; outline: none; }

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
        /* Hinweis innerhalb eines offenen Dialogs (dort gelten helle Farben): dunkel lassen */
        dialog .toast { color: #fff; background: #1c1f25; border-color: #1c1f25; border-left-color: var(--accent); }

        /* ===== Fußzeile: dunkles Band über die Breite der Spalte ===== */
        .site-footer {
            position: relative;
            z-index: 1;
            margin-top: 0;
            margin-bottom: 0;
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
        .blog-layout { grid-template-columns: minmax(0, 1fr); gap: 24px; }

        /* Filterreihe (Blog-Themen) wie NAVIs Spiele-Filter oben im Inhalt */
        .filter-row { display: flex; flex-wrap: wrap; gap: 6px; margin: 0 0 24px; }
        .filter-row[hidden] { display: none; }
        .filter-row .tag-chip { min-height: 40px; padding: 8px 14px; }

        .post-card { padding: 24px; }
        .post-card h2 { font-size: 1.45rem; text-transform: none; letter-spacing: -.005em; line-height: 1.2; }

        .post-meta {
            color: var(--muted);
            font-family: var(--font-ui);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .12em;
        }

        /* Links in Texten aus dem Editor (Home, Blog) */
        /* Überschrift mit Link = klickbare Kachel: weiß, grüner Rand links, Pfeil rechts */
        .post-body :is(h2, h3, h4):has(> a) {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 22px 0 8px;
            padding: 10px 14px;
            border: 1px solid var(--line);
            border-left: 4px solid var(--accent);
            background: #fff;
            transition: background-color .15s ease;
        }
        .post-body :is(h2, h3, h4):has(> a:hover, > a:focus-visible) { background: rgba(var(--accent-rgb), .18); }
        .post-body :is(h2, h3, h4) > a { flex: 1; display: flex; align-items: center; justify-content: space-between; gap: 10px; color: var(--text); text-decoration: none; outline: none; }
        .post-body :is(h2, h3, h4) > a::after { content: "→"; color: var(--accent-text); font-weight: 900; }
        /* Links im Fließtext */
        .post-body :is(p, li, blockquote) a { color: var(--text); font-weight: 700; text-decoration: none; border-bottom: 2px solid var(--accent); }
        .post-body :is(p, li, blockquote) a:hover, .post-body :is(p, li, blockquote) a:focus-visible { color: var(--accent-text); }
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

        /* ===== Unternavigation der Rubrik „Spiele-Statistik“ (Spiele · Spieler · Jahresrückblick) ===== */
        .sub-nav { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 18px; }
        .sub-nav-link {
            display: inline-flex;
            align-items: center;
            min-height: 40px;
            padding: 8px 16px;
            color: var(--text);
            background: #fff;
            box-shadow: inset 0 0 0 1px var(--line-strong);
            font-family: var(--font-ui);
            font-size: .74rem;
            font-weight: 800;
            letter-spacing: .1em;
            text-decoration: none;
            text-transform: uppercase;
        }

        .sub-nav-link:hover, .sub-nav-link:focus-visible { outline: none; background: rgba(var(--accent-rgb), .18); }
        .sub-nav-link.active { color: var(--accent-ink); background: var(--accent); box-shadow: none; }

        /* Nur für Angemeldete (Eindrücke): bis assets/sidebar.js den Punkt einblendet */
        .nav-members[hidden] { display: none; }

        /* Rangliste: Zeilen, die zum Spielerprofil führen */
        a.stats-row.is-link { color: inherit; text-decoration: none; grid-template-columns: 2.2em 40px minmax(0, 1fr) auto 1.2em; }
        a.stats-row.is-link:hover, a.stats-row.is-link:focus-visible { outline: none; box-shadow: inset 0 0 0 3px var(--accent); }

        /* ===== Seitenleiste „Kurz & knapp“ (includes/site-footer.php, Daten: assets/sidebar.js) ===== */
        .site-sidebar {
            min-width: 0;
            padding: 46px 25px 48px;
            color: var(--text);
            background: #fff;
        }

        .sidebar-section { margin: 0 0 44px; }
        .sidebar-section[hidden] { display: none; }
        .sidebar-section:last-child { margin-bottom: 0; }

        .sidebar-title {
            margin: 0 0 18px;
            color: var(--text);
            font-family: var(--font-ui);
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: .04em;
            line-height: 1.15;
            text-transform: uppercase;
        }

        /* Liste wie NAVIs „Our Channels“: Bild, Name, kleine Zeile */
        .sidebar-list { margin: 0; padding: 0; list-style: none; border: 1px solid var(--line); }
        .sidebar-list li + li { border-top: 1px solid var(--line); }

        .sidebar-row {
            display: grid;
            grid-template-columns: 44px minmax(0, 1fr);
            align-items: center;
            gap: 12px;
            min-height: 66px;
            padding: 11px 14px;
            color: var(--text);
            text-decoration: none;
            transition: background-color .15s ease, box-shadow .15s ease;
        }

        a.sidebar-row:hover,
        a.sidebar-row:focus-visible { outline: none; background: var(--panel-strong); box-shadow: inset 3px 0 0 var(--accent); }

        .sidebar-row-media {
            width: 44px;
            height: 44px;
            object-fit: cover;
            background: var(--panel-soft);
        }

        .sidebar-row-media.placeholder { display: grid; place-items: center; font-size: 1.2rem; }

        .sidebar-list li:not(:has(.sidebar-row-media)) .sidebar-row { grid-template-columns: minmax(0, 1fr); }

        .sidebar-row-text { display: grid; min-width: 0; }

        .sidebar-row-text strong {
            overflow: hidden;
            font-size: .92rem;
            font-weight: 800;
            line-height: 1.3;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar-row-text small {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--muted);
            font-size: .78rem;
        }

        .sidebar-dot { width: 8px; height: 8px; flex: none; background: var(--open); }
        .sidebar-dot.online { background: var(--online); }
        .sidebar-dot.offline { background: var(--absent); }

        /* Große Zahl wie „2,571,220 Subscribers around the world“ */
        .sidebar-stat { display: grid; gap: 4px; margin: 26px 0 0; }
        .sidebar-stat[hidden] { display: none; }
        .sidebar-stat strong { color: var(--text); font-family: var(--font-ui); font-size: 2.6rem; font-weight: 900; letter-spacing: -.02em; line-height: 1; }
        .sidebar-stat span { color: var(--muted); font-family: var(--font-ui); font-size: .72rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }

        .sidebar-more {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 18px;
            color: var(--text);
            font-family: var(--font-ui);
            font-size: .72rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-decoration: none;
            text-transform: uppercase;
            box-shadow: inset 0 -3px 0 var(--accent);
        }

        .sidebar-more::after { content: "→"; }
        .sidebar-more:hover { color: var(--accent-text); }

        /* Hervorgehobener Kasten (nächste Netzje): dunkel, wie NAVIs Werbefläche */
        .sidebar-feature {
            display: grid;
            gap: 6px;
            padding: 22px 20px;
            color: #c9ced8;
            background: #1c1f25;
            text-decoration: none;
            box-shadow: inset 0 -4px 0 var(--accent);
            transition: box-shadow .15s ease;
        }

        .sidebar-feature:hover,
        .sidebar-feature:focus-visible { outline: none; box-shadow: inset 0 0 0 3px var(--accent); }

        .sidebar-feature-tag {
            justify-self: start;
            margin-bottom: 6px;
            padding: 4px 10px;
            color: var(--accent-ink);
            background: var(--accent);
            font-family: var(--font-ui);
            font-size: .66rem;
            font-weight: 800;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .sidebar-feature strong { color: #fff; font-family: var(--font-ui); font-size: 1.5rem; font-weight: 900; line-height: 1.1; text-transform: uppercase; }
        .sidebar-feature-meta { color: #8e95a1; font-size: .8rem; font-weight: 600; }

        /* „Gerade online“: grüner Puls am Titel */
        .sidebar-now .sidebar-title { display: flex; align-items: center; gap: 10px; }
        .sidebar-count { color: var(--muted); font-size: .9rem; }
        .live-dot { width: 10px; height: 10px; flex: none; border-radius: 50%; background: var(--accent); box-shadow: 0 0 0 0 rgba(var(--accent-rgb), .6); animation: livePulse 1.8s ease-out infinite; }
        @keyframes livePulse { 0% { box-shadow: 0 0 0 0 rgba(var(--accent-rgb), .6); } 80%, 100% { box-shadow: 0 0 0 9px rgba(var(--accent-rgb), 0); } }
        .sidebar-row-media.placeholder { font-weight: 900; }

        /* Belegung der Netzje-Plätze in der dunklen Karte */
        .sidebar-seatbar { display: block; height: 6px; margin: 8px 0 2px; background: rgba(255, 255, 255, .12); }
        .sidebar-seatbar[hidden] { display: none; }
        .sidebar-seatbar span { display: block; height: 100%; background: var(--accent); }

        .site-sidebar[hidden] { display: none; }
        .page-frame:has(> .site-sidebar[hidden]) { grid-template-columns: minmax(0, 1fr); }

        /* Von admin.php ausgeschaltete Teile der Startseite */
        .feature-off { display: none !important; }

        /* Menüpunkt „Admin“ mit Zahl offener Netzje-Anfragen */
        .nav-admin[hidden] { display: none; }
        .nav-badge {
            display: inline-grid;
            place-items: center;
            min-width: 18px;
            height: 18px;
            margin-left: 6px;
            padding: 0 5px;
            color: var(--accent-ink);
            background: var(--accent);
            font-size: .62rem;
            font-weight: 900;
            letter-spacing: 0;
        }

        .page-hidden-note {
            margin: 16px 0 0;
            padding: 10px 14px;
            border-left: 4px solid #e3a42c;
            color: #5a3d00;
            background: #fff6e2;
            font-size: .9rem;
        }

        .page-hidden-note a { color: inherit; font-weight: 800; }

        /* ===== Admin-Seite (admin.php) ===== */
        .admin-panel {
            margin: 0 0 22px;
            padding: 24px;
            border: 1px solid var(--line);
            background: #fff;
        }

        .admin-panel[hidden] { display: none; }
        .admin-panel .section-title { margin-bottom: 12px; }
        .admin-panel-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 8px; }
        .admin-panel-head .section-title { margin: 0; }
        .admin-panel a.primary-button,
        .admin-panel a.secondary-button { display: inline-flex; align-items: center; text-decoration: none; }
        .admin-help { margin: 0 0 16px; color: var(--muted); line-height: 1.6; }
        .admin-muted { color: var(--muted); font-size: .75rem; font-weight: 600; letter-spacing: 0; text-transform: none; }

        .admin-order { display: grid; gap: 6px; margin: 0; padding: 0; list-style: none; }
        .admin-order-item {
            display: grid;
            grid-template-columns: 32px minmax(0, 1fr) auto;
            align-items: center;
            gap: 12px;
            padding: 8px 10px;
            border: 1px solid var(--line);
            background: var(--panel-strong);
        }

        .admin-order-number {
            display: grid;
            place-items: center;
            width: 32px;
            height: 32px;
            color: var(--accent-ink);
            background: var(--accent);
            font-weight: 900;
        }

        .admin-moves { display: flex; gap: 6px; }
        /* Auf welchen Seiten ein Kasten erscheint */
        .admin-box-pages { grid-column: 2 / -1; display: flex; flex-wrap: wrap; align-items: center; gap: 6px 8px; }
        label.admin-page-chip { display: inline-flex; align-items: center; gap: 6px; margin: 0; padding: 4px 10px; border: 1px solid var(--line); color: var(--text); background: #fff; font-size: .8rem; font-weight: 700; letter-spacing: 0; text-transform: none; cursor: pointer; }
        .admin-page-chip input { width: 16px; height: 16px; margin: 0; accent-color: var(--accent-text); cursor: pointer; }
        .admin-page-chip:has(input:disabled) { opacity: .45; cursor: default; }
        .admin-move { min-height: 40px; min-width: 44px; padding: 6px 10px; font-size: 1rem; }
        .admin-move:disabled { opacity: .35; cursor: default; }

        .admin-switches { display: grid; gap: 8px; }

        label.admin-switch {
            display: grid;
            grid-template-columns: 22px minmax(0, 1fr);
            align-items: center;
            gap: 2px 12px;
            margin: 0;
            color: var(--text);
            font-size: .95rem;
            font-weight: 700;
            letter-spacing: 0;
            text-transform: none;
            cursor: pointer;
        }

        .admin-switches label.admin-switch { padding: 12px 14px; border: 1px solid var(--line); background: var(--panel-strong); }
        .admin-switch input { width: 22px; height: 22px; margin: 0; accent-color: var(--accent-text); cursor: pointer; }
        .admin-switch small { grid-column: 2; }

        .admin-savebar {
            position: sticky;
            bottom: 0;
            z-index: 5;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            margin: 0 0 22px;
            padding: 14px 16px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 -8px 24px rgba(0, 0, 0, .06);
        }

        .admin-savebar .form-error { flex: 1 1 100%; }
        .admin-dirty { color: #8a5a00; font-size: .85rem; font-weight: 700; }

        .admin-netzje-summary { margin: 0 0 14px; }
        .admin-request-list { display: grid; gap: 8px; margin: 0; padding: 0; list-style: none; }
        .admin-request {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid var(--line);
            background: var(--panel-strong);
        }

        .admin-request-actions { display: flex; flex-wrap: wrap; gap: 8px; }

        /* Benutzerverwaltung */
        .admin-panel .admin-user-list { display: grid; gap: 8px; max-height: none; margin: 0 0 26px; padding: 0; overflow: visible; }
        .page-shell .admin-user-card { align-items: center; padding: 12px 14px; background: var(--panel-strong); }
        .page-shell .admin-user-card strong { display: inline-flex; align-items: center; gap: 2px; color: var(--text); }
        .page-shell .admin-user-card small { color: var(--muted); }
        .admin-names { padding-top: 22px; border-top: 1px solid var(--line); }
        .page-shell label { color: var(--text); }
        :is(.page-shell, dialog) textarea { font: inherit; }
        .admin-names .admin-help { margin: 0 0 10px; }
        .admin-names-actions { display: flex; justify-content: flex-end; margin-top: 10px; }

        /* Daten aufräumen */
        .cleanup-list { display: grid; gap: 6px; margin: 0 0 14px; padding: 0; list-style: none; }
        .cleanup-item { display: grid; grid-template-columns: minmax(2.5ch, auto) minmax(0, 1fr) auto; align-items: baseline; gap: 10px; padding: 9px 12px; border: 1px solid var(--line); background: var(--panel-strong); }
        .cleanup-count { color: var(--accent-text); font-weight: 900; text-align: right; }
        .cleanup-size { color: var(--muted); font-size: .82rem; white-space: nowrap; }
        .cleanup-actions { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; }
        .cleanup-actions .admin-help { margin: 0; }

        /* Tablet & Handy: keine schmale Leiste – die Kästen rutschen als Karten unter den Inhalt.
           Tablet: nebeneinander, Handy: untereinander in voller Breite. */
        @media (max-width: 1023px) {
            .page-frame { display: block; }

            .site-sidebar {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr));
                align-items: start;
                gap: 16px;
                padding: 0 var(--gutter) 56px;
                background: var(--surface);
            }

            .page-shell { padding-bottom: 40px; }

            .sidebar-section {
                margin: 0;
                padding: 20px;
                border: 1px solid var(--line);
                background: #fff;
            }

            .sidebar-title { font-size: 1.1rem; margin-bottom: 14px; }
            .sidebar-stat strong { font-size: 2.2rem; }
        }

        /* ===== Mobil ===== */
        @media (max-width: 680px) {
            :root { --gutter: 12px; }
            .topbar-tagline.subtitle { font-size: .64rem; letter-spacing: .1em; }
            .masthead-row { min-height: 0; padding-top: 10px; }
            .brand-logo { width: 44px; height: 44px; }
            /* Etwas Luft unter Statusleiste/Notch, damit die Infoleiste (Slogan, App, Anmelden) lesbar bleibt */
            .topbar-inner { padding-top: calc(env(safe-area-inset-top) + 20px); padding-bottom: 0; }
            .main-nav { gap: 20px; }
            .main-nav-wrap { order: 3; flex: 1 1 100%; margin: 0; }
            .main-nav-wrap .main-nav { min-height: 40px; }
            .nav-link { font-size: .74rem; }
            .nav-link::after { bottom: 0; height: 3px; }
            .page-hero { padding: 28px 0 2px; }
            .page-hero-inner::after { margin-top: 16px; }
            .page-hero-lead { font-size: .92rem; }
            .page-shell { padding-top: 18px; }
            .achievements { margin-top: 34px; }
            .post-card { padding: 16px; }
            .blog-head-actions { align-self: flex-start; }
            .netzje-panel { padding: 16px 12px; }
            .status-badge-main { font-size: .7rem; letter-spacing: 0; }
            .primary-button, .secondary-button, .danger-button { font-size: .74rem; }

            /* Filter- und Zeitraum-Reihen: waagerecht wischen statt umbrechen */
            .filter-row,
            .stats-periods {
                flex-wrap: nowrap;
                overflow-x: auto;
                margin-left: calc(-1 * var(--gutter));
                margin-right: calc(-1 * var(--gutter));
                padding: 0 var(--gutter) 2px;
                scrollbar-width: none;
                -webkit-overflow-scrolling: touch;
            }

            .filter-row::-webkit-scrollbar,
            .stats-periods::-webkit-scrollbar { display: none; }

            .filter-row > *,
            .stats-periods > * { flex: none; white-space: nowrap; }

            .sidebar-section { padding: 18px 16px; }
            .admin-panel { padding: 18px 14px; }
            .admin-order-item { grid-template-columns: 28px minmax(0, 1fr); }
            .admin-order-number { width: 28px; height: 28px; }
            .admin-moves { grid-column: 1 / -1; justify-content: flex-end; }
            .admin-box-pages { grid-column: 1 / -1; }
            .admin-request-actions { width: 100%; }
            .admin-request-actions > * { flex: 1 1 0; }
            .admin-savebar { margin-left: calc(-1 * var(--gutter)); margin-right: calc(-1 * var(--gutter)); border-left: 0; border-right: 0; }
            .admin-savebar .primary-button { flex: 1 1 100%; }
            .topbar-account-name { max-width: 90px; }
            .topbar-account-button { padding: 0 9px; letter-spacing: .08em; }
            .sidebar-row { min-height: 60px; padding: 10px 12px; }
        }
    </style>
