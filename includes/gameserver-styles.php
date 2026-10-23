<?php
// Ergänzende Styles nur für die Seite „Server“ (Gameserver-Karten im Raster).
?>
    <style>
        .server-panel {
            margin-top: 19px;
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: rgba(var(--panel-deep-rgb), .5);
            box-shadow: var(--shadow);
        }

        .server-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; }
        .server-head .section-title { margin: 0; }

        .server-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(270px, 1fr)); gap: 16px; }

        .server-card {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: rgba(var(--panel-rgb), .9);
            background-image: var(--sheen);
        }

        .server-card.is-online { border-color: rgba(62, 231, 143, .45); }

        .server-top { display: flex; align-items: center; gap: 14px; }

        .server-logo {
            flex: none;
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 12px;
            background: rgba(255, 255, 255, .08);
        }

        .server-logo.placeholder { display: grid; place-items: center; font-size: 1.9rem; }

        .server-title { min-width: 0; }
        .server-title h3 { margin: 0; color: #fff; font-family: var(--font-heading); font-size: 1.1rem; font-weight: 800; overflow-wrap: anywhere; }
        .server-title small { display: block; opacity: .7; overflow-wrap: anywhere; }

        .server-status {
            align-self: flex-start;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 4px 11px;
            border: 1px solid var(--line);
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .server-status::before { content: ''; width: 9px; height: 9px; border-radius: 50%; background: currentColor; }
        .server-status.online { color: var(--green); border-color: rgba(62, 231, 143, .5); }
        .server-status.offline { color: var(--red); border-color: rgba(255, 91, 109, .5); }
        .server-status.unknown { opacity: .65; }

        .server-rows { display: grid; grid-template-columns: minmax(0, 1fr) auto; align-items: center; gap: 2px 8px; margin: 0; }
        .server-rows dt { grid-column: 1 / -1; margin-top: 6px; opacity: .65; font-size: .8rem; }
        .server-rows dd + span { white-space: nowrap; }
        .server-rows dd { margin: 0; min-width: 0; overflow-wrap: anywhere; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; color: #fff; }

        .server-mini {
            padding: 3px 9px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: transparent;
            color: inherit;
            font: inherit;
            font-size: .78rem;
            font-weight: 700;
            cursor: pointer;
        }

        .server-mini:hover, .server-mini:focus-visible { background: rgba(255, 255, 255, .1); outline: none; }

        .server-hint { margin: 0; opacity: .65; font-size: .8rem; }
        .server-hint a { color: inherit; text-decoration: underline; }
        .server-meta { margin: 0; font-size: .85rem; opacity: .8; overflow-wrap: anywhere; }
        .server-card-actions { margin-top: auto; display: flex; gap: 8px; }

        .stats-note { margin: 0 0 12px; opacity: .7; font-size: .85rem; }
        .form-error { margin: 0; color: var(--red); font-weight: 600; }

        .server-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 15px; }
        .server-form-hint { margin: 4px 0 0; opacity: .65; font-size: .8rem; }
    </style>
