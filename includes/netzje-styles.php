<?php
// Ergänzende Styles nur für die Seite „Netzje“ (LAN-Party mit Stundenplan).
?>
    <style>
        .netzje-panel {
            margin-top: 19px;
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: rgba(var(--panel-deep-rgb), .5);
            box-shadow: var(--shadow);
        }

        .netzje-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; }
        .netzje-head .section-title { margin: 0; }
        .netzje-actions { display: flex; flex-wrap: wrap; gap: 8px; }

        .netzje-when { margin: 0 0 6px; color: #fff; font-family: var(--font-heading); font-size: 1.25rem; font-weight: 800; }
        .netzje-countdown {
            display: inline-block;
            margin: 0 0 14px;
            padding: 4px 11px;
            border: 1px solid var(--line);
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
        }
        .netzje-countdown.live { color: var(--green); border-color: rgba(62, 231, 143, .5); }

        .netzje-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-top: 8px; }
        .netzje-box { padding: 14px 16px; border: 1px solid var(--line); border-radius: var(--radius-lg); background: rgba(var(--panel-rgb), .9); background-image: var(--sheen); }
        .netzje-box h3 { margin: 0 0 8px; color: #fff; font-family: var(--font-heading); font-size: .95rem; font-weight: 800; text-transform: uppercase; letter-spacing: .02em; }
        .netzje-text { margin: 0; white-space: pre-line; overflow-wrap: anywhere; }
        .netzje-muted { margin: 0; opacity: .65; font-size: .85rem; }
        .netzje-muted a { color: inherit; text-decoration: underline; }
        .netzje-note { margin: 0 0 12px; opacity: .7; font-size: .85rem; }

        .netzje-chips { display: flex; flex-wrap: wrap; gap: 6px; margin: 0 0 12px; padding: 0; list-style: none; }
        .netzje-chip { padding: 3px 10px; border: 1px solid var(--line); border-radius: 999px; background: rgba(255, 255, 255, .06); font-size: .85rem; font-weight: 600; }
        .netzje-chip.me { border-color: var(--primary); color: #fff; }
        .netzje-chip.wait { opacity: .6; border-style: dashed; }

        .netzje-stream { position: relative; width: 100%; aspect-ratio: 16 / 9; border: 1px solid var(--line); border-radius: var(--radius-lg); overflow: hidden; background: #000; }
        .netzje-stream iframe { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; }

        /* Zeitleiste (Festival-Ansicht): ab 720 px; darunter Liste nach Tag */
        .netzje-timeline-wrap { overflow-x: auto; padding-bottom: 6px; border: 1px solid var(--line); border-radius: var(--radius-lg); background: rgba(var(--panel-rgb), .6); }
        .netzje-timeline { position: relative; }
        .netzje-ruler { position: relative; height: 44px; border-bottom: 1px solid var(--line); }
        .netzje-tick { position: absolute; top: 22px; bottom: 0; border-left: 1px solid rgba(255, 255, 255, .12); padding-left: 4px; font-size: .75rem; opacity: .7; }
        .netzje-day { position: absolute; top: 4px; padding-left: 4px; border-left: 2px solid var(--accent, #e11d48); color: #fff; font-size: .8rem; font-weight: 800; white-space: nowrap; }
        .netzje-gridline { position: absolute; top: 44px; bottom: 0; border-left: 1px solid rgba(255, 255, 255, .05); pointer-events: none; }
        .netzje-now { position: absolute; top: 0; bottom: 0; border-left: 2px solid var(--green); pointer-events: none; z-index: 2; }
        .netzje-now::before { content: 'Jetzt'; position: absolute; top: 2px; left: 4px; color: var(--green); font-size: .7rem; font-weight: 800; }

        .netzje-block {
            position: absolute;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 2px;
            padding: 6px 9px;
            overflow: hidden;
            border: 1px solid rgba(124, 92, 255, .6);
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(124, 92, 255, .42), rgba(225, 29, 72, .28));
            color: #fff;
            font: inherit;
            text-align: left;
            cursor: pointer;
        }
        .netzje-block:hover, .netzje-block:focus-visible { outline: 2px solid #fff; outline-offset: 1px; }
        .netzje-block.mine { border-color: var(--green); box-shadow: inset 0 0 0 1px var(--green); }
        .netzje-block.full { background: linear-gradient(135deg, rgba(120, 120, 140, .4), rgba(80, 80, 100, .35)); }
        .netzje-block strong { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; font-size: .9rem; }
        .netzje-block small { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; opacity: .85; font-size: .75rem; }

        .netzje-list { display: none; }
        .netzje-list h3 { margin: 16px 0 8px; color: #fff; font-family: var(--font-heading); font-size: .95rem; font-weight: 800; text-transform: uppercase; }
        .netzje-list ol { display: grid; gap: 8px; margin: 0; padding: 0; list-style: none; }
        .netzje-row {
            display: grid;
            grid-template-columns: 6.2em minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(var(--panel-rgb), .9);
            color: inherit;
            font: inherit;
            text-align: left;
            cursor: pointer;
        }
        .netzje-row.mine { border-color: var(--green); }
        .netzje-row .time { font-weight: 800; font-size: .85rem; }
        .netzje-row .game { min-width: 0; color: #fff; font-weight: 700; overflow-wrap: anywhere; }
        .netzje-row .game small { display: block; font-weight: 400; opacity: .65; font-size: .78rem; }
        .netzje-row .count { font-weight: 800; white-space: nowrap; }

        @media (max-width: 719px) {
            .netzje-timeline-wrap { display: none; }
            .netzje-list { display: block; }
        }

        .netzje-proposals { display: grid; gap: 8px; margin: 0; padding: 0; list-style: none; }
        .netzje-proposals .netzje-row { cursor: pointer; }

        .netzje-form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .netzje-dialog-meta { margin: 0; opacity: .8; }
        .form-error { margin: 0; color: var(--red); font-weight: 600; }
        input[type="datetime-local"] {
            width: 100%;
            min-height: 47px;
            padding: 10px 12px;
            border: 1px solid var(--line-strong);
            border-radius: var(--radius-md);
            outline: none;
            color: var(--text);
            background: var(--panel-soft);
            color-scheme: dark;
        }
    </style>
