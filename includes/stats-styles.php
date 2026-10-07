<?php
// Ergänzende Styles nur für die Statistik-Seite (Spielzeit der Kellerkinder).
?>
    <style>
        .games-panel {
            margin-top: 19px;
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: rgba(var(--panel-deep-rgb), .5);
            box-shadow: var(--shadow);
        }

        .stats-periods { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 18px; }

        .stats-period {
            padding: 7px 14px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: transparent;
            color: inherit;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }

        .stats-period:hover, .stats-period:focus-visible { background: rgba(255, 255, 255, .08); outline: none; }
        .stats-period.active { background: rgba(255, 255, 255, .14); border-color: rgba(255, 255, 255, .4); color: #fff; }

        .stats-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 22px; }

        .stats-tile {
            padding: 12px 14px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: rgba(var(--panel-rgb), .9);
            background-image: var(--sheen);
        }

        .stats-tile strong { display: block; color: #fff; font-family: var(--font-heading); font-size: 1.5rem; font-weight: 900; }
        .stats-tile span { opacity: .75; font-size: .85rem; }

        .stats-section { margin: 0 0 10px; color: #fff; font-family: var(--font-heading); font-size: 1rem; font-weight: 800; text-transform: uppercase; letter-spacing: .02em; }

        .stats-list { margin: 0 0 24px; padding: 0; list-style: none; display: grid; gap: 8px; }

        .stats-row {
            position: relative;
            display: grid;
            grid-template-columns: 2.2em minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgba(var(--panel-rgb), .9);
        }

        .stats-bar { position: absolute; inset: 0 auto 0 0; background: rgba(255, 255, 255, .08); pointer-events: none; }
        .stats-rank { position: relative; opacity: .6; font-weight: 800; }
        .stats-name { position: relative; min-width: 0; overflow-wrap: anywhere; font-weight: 700; color: #fff; }
        .stats-name small { display: block; font-weight: 400; opacity: .65; font-size: .78rem; }
        .stats-time { position: relative; font-weight: 800; white-space: nowrap; text-align: right; }

        .stats-note { margin: 0 0 12px; opacity: .7; font-size: .85rem; }
    </style>
