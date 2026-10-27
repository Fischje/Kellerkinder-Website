<?php
// Styles der Seite „Eindrücke“ (eindruecke.php): Kachelraster, Album-Filter, Großansicht.
// Wird nach includes/navi-styles.php eingebunden.
?>
    <style>
        .gallery-gate { padding: 24px; border: 1px solid var(--line); background: #fff; }
        .gallery-gate[hidden] { display: none; }
        .gallery-gate a.primary-button { display: inline-flex; align-items: center; text-decoration: none; }
        .gallery-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px; }
        .gallery-head .section-title { margin: 0; }
        .gallery-actions { display: flex; flex-wrap: wrap; gap: 8px; }

        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 200px), 1fr)); gap: 8px; }
        .gallery-tile {
            position: relative;
            display: block;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            padding: 0;
            border: 0;
            background: #1c1f25;
            cursor: zoom-in;
        }
        .gallery-tile img { width: 100%; height: 100%; display: block; object-fit: cover; transition: transform .25s ease; }
        .gallery-tile:hover img, .gallery-tile:focus-visible img { transform: scale(1.04); }
        .gallery-tile:focus-visible { outline: 3px solid var(--accent); outline-offset: 2px; }
        .gallery-play {
            position: absolute;
            left: 50%;
            top: 50%;
            display: grid;
            place-items: center;
            width: 54px;
            height: 54px;
            transform: translate(-50%, -50%);
            color: var(--accent-ink);
            background: var(--accent);
            font-size: 1.3rem;
        }
        .gallery-clip-label { position: absolute; top: 10px; left: 10px; color: #fff; font-size: .72rem; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .gallery-tile.twitch_clip { background: linear-gradient(135deg, #2a1d4a, #1c1f25); }
        .gallery-caption {
            position: absolute;
            inset: auto 0 0 0;
            overflow: hidden;
            padding: 22px 10px 8px;
            color: #fff;
            background: linear-gradient(transparent, rgba(0, 0, 0, .75));
            font-size: .78rem;
            font-weight: 700;
            text-align: left;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .gallery-progress { margin: 0; color: var(--muted); font-weight: 700; }

        /* Großansicht */
        dialog.gallery-viewer {
            width: min(1200px, calc(100% - 16px));
            max-height: calc(100vh - 16px);
            padding: 0;
            border: 0;
            background: #0d0f13;
            box-shadow: inset 0 4px 0 var(--accent), 0 30px 70px rgba(0, 0, 0, .7);
        }
        .viewer-stage { display: grid; place-items: center; min-height: 240px; background: #000; }
        .viewer-stage img { display: block; max-width: 100%; max-height: calc(100vh - 150px); object-fit: contain; }
        .viewer-stage iframe { width: 100%; aspect-ratio: 16 / 9; border: 0; display: block; }
        .viewer-bar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 14px; }
        .viewer-info { display: grid; gap: 2px; min-width: 0; color: #fff; }
        .viewer-info small { color: #9aa1ad; }
        .viewer-actions { display: flex; flex-wrap: wrap; gap: 6px; }
        .viewer-actions .secondary-button { min-width: 44px; color: #fff; background: transparent; box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .3); }
        .viewer-actions .secondary-button:disabled { opacity: .35; }

        @media (max-width: 680px) {
            .gallery-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 4px; }
            .gallery-actions { width: 100%; }
            .gallery-actions > * { flex: 1 1 0; }
            .viewer-actions { width: 100%; }
            .viewer-actions > * { flex: 1 1 0; }
        }
    </style>
