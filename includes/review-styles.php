<?php
// Styles der Rubrik „Spiele-Statistik“: Jahresrückblick (jahresrueckblick.php) und
// Spielerprofil (spieler.php?name=…). Wird nach includes/navi-styles.php eingebunden.
// Diagramm: eine Datenreihe, ein Grün (#2e9e25, besteht 3:1 Kontrast auf Weiß),
// abgerundete Balkenenden, 2 px Abstand, Werte beim Überfahren/Antippen.
?>
    <style>
        .visually-hidden {
            position: absolute !important;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0 0 0 0);
            clip-path: inset(50%);
            white-space: nowrap;
        }

        .review-section { margin: 0 0 40px; }
        .review-section .section-title { margin-bottom: 16px; }
        .review-summary { margin: 4px 0 34px; }
        .review-note { margin-top: 10px; }

        /* ===== Monatsdiagramm ===== */
        .review-chart { position: relative; margin: 0; padding: 20px 20px 14px; border: 1px solid var(--line); background: #fff; }
        .review-chart-caption { margin: 0 0 10px; color: var(--muted); font-size: .72rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        .review-tip {
            position: absolute;
            top: 14px;
            right: 16px;
            padding: 6px 10px;
            color: #fff;
            background: #1c1f25;
            font-size: .8rem;
            font-weight: 700;
            pointer-events: none;
        }
        .review-tip[hidden] { display: none; }

        .review-bars { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 2px; height: 220px; }
        .review-bar-col {
            display: grid;
            grid-template-rows: minmax(0, 1fr) auto;
            min-width: 0;
            padding: 0;
            border: 0;
            background: none;
            cursor: pointer;
        }
        .review-bar-plot { position: relative; display: flex; flex-direction: column; justify-content: flex-end; align-items: stretch; min-height: 0; border-bottom: 1px solid var(--line-strong); }
        .review-bar { display: block; margin: 0 3px; border-radius: 4px 4px 0 0; background: #2e9e25; transition: background-color .12s ease; }
        .review-bar-col.is-active .review-bar,
        .review-bar-col:hover .review-bar,
        .review-bar-col:focus-visible .review-bar { background: #1f7a18; }
        .review-bar-col:focus-visible { outline: 2px solid var(--accent-text); outline-offset: 2px; }
        .review-bar-value { margin-bottom: 4px; color: var(--text); font-size: .68rem; font-weight: 800; text-align: center; white-space: nowrap; }
        .review-bar-label { padding-top: 6px; color: var(--muted); font-size: .66rem; font-weight: 700; text-align: center; }

        /* ===== Auszeichnungen ===== */
        .review-awards { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 220px), 1fr)); gap: 12px; }
        .review-award { padding: 16px; border: 1px solid var(--line); background: #fff; box-shadow: inset 0 4px 0 var(--accent); }
        .review-award-head { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
        .review-award-icon { font-size: 1.2rem; }
        .review-award-title { color: var(--muted); font-size: .7rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        .review-award-body { display: flex; align-items: center; gap: 12px; }
        .review-award-body strong { display: block; color: var(--text); font-size: 1.05rem; font-weight: 900; line-height: 1.2; }
        .review-award-body small { display: block; margin-top: 2px; color: var(--muted); font-size: .8rem; }

        /* ===== Ranglisten ===== */
        .review-columns .section-title { font-size: clamp(1.15rem, 2.4vw, 1.45rem); }
        .review-columns { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 320px), 1fr)); gap: 0 24px; }
        .review-list { display: grid; gap: 6px; margin: 0; padding: 0; list-style: none; }
        .review-list-item {
            position: relative;
            display: grid;
            grid-template-columns: 1.8em 40px minmax(0, 1fr) auto;
            align-items: center;
            gap: 10px;
            overflow: hidden;
            padding: 8px 12px;
            border: 1px solid var(--line);
            color: var(--text);
            background: #fff;
            text-decoration: none;
        }
        a.review-list-item:hover, a.review-list-item:focus-visible { outline: none; box-shadow: inset 0 0 0 3px var(--accent); }
        .review-list-bar { position: absolute; inset: 0 auto 0 0; background: linear-gradient(90deg, rgba(var(--accent-rgb), .3), rgba(var(--accent-rgb), .06)); pointer-events: none; }
        .review-list-item > :not(.review-list-bar) { position: relative; }
        .review-rank { color: var(--accent-text); font-weight: 900; font-size: .9rem; }
        .review-list-text { display: grid; min-width: 0; }
        .review-list-text strong { overflow: hidden; font-size: .92rem; font-weight: 800; text-overflow: ellipsis; white-space: nowrap; }
        .review-list-text small { overflow: hidden; color: var(--muted); font-size: .76rem; text-overflow: ellipsis; white-space: nowrap; }
        .review-list-value { font-size: .9rem; font-weight: 800; white-space: nowrap; }

        .review-avatar, .review-icon { width: 40px; height: 40px; object-fit: cover; background: var(--panel-soft); }
        .review-icon { height: 26px; }
        .review-avatar.placeholder, .review-icon.placeholder { display: grid; place-items: center; color: var(--accent-ink); background: var(--accent); font-weight: 900; }
        .review-icon.placeholder { color: inherit; background: var(--panel-soft); font-size: .9rem; }

        .review-chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .review-chip { display: inline-flex; align-items: center; gap: 8px; padding: 6px 10px 6px 6px; border: 1px solid var(--line); background: #fff; font-size: .85rem; font-weight: 700; }
        .review-chip .review-icon { width: 34px; height: 22px; }

        .review-netzje { display: grid; gap: 6px; margin: 0; padding: 0; list-style: none; }
        .review-netzje li { display: grid; gap: 2px; padding: 10px 14px; border: 1px solid var(--line); background: #fff; box-shadow: inset 3px 0 0 var(--accent); }
        .review-netzje small { color: var(--muted); }

        /* ===== Spielerprofil ===== */
        .profile-card { display: flex; align-items: center; gap: 22px; margin: 18px 0 26px; padding: 22px; border: 1px solid var(--line); background: #fff; box-shadow: inset 0 4px 0 var(--accent); }
        .profile-avatar { width: 96px; height: 96px; flex: none; object-fit: cover; background: var(--panel-soft); }
        .profile-avatar.placeholder { display: grid; place-items: center; color: var(--accent-ink); background: var(--accent); font-size: 2.6rem; font-weight: 900; }
        .profile-info { display: grid; gap: 12px; min-width: 0; }
        .profile-rank { margin: 0; color: var(--muted); font-size: .76rem; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
        .profile-badges { display: flex; flex-wrap: wrap; gap: 8px; margin: 0; padding: 0; list-style: none; }
        .profile-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 10px; background: var(--panel-strong); border: 1px solid var(--line); font-size: .8rem; font-weight: 800; cursor: help; }
        .profile-badge-icon { font-size: 1rem; }

        @media (max-width: 680px) {
            .review-bars { height: 170px; }
            .review-bar { margin: 0 1px; }
            .review-bar-label { font-size: .58rem; }
            .review-chart { padding: 14px 10px 10px; }
            .profile-card { flex-direction: column; align-items: flex-start; padding: 18px; }
            .profile-avatar { width: 76px; height: 76px; }
        }
    </style>
