<?php
// Ergänzende Styles nur für die Spiele-Seite (aktuell gespielte Spiele).
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

        .games-head h1 {
            margin: 0 0 6px;
            color: #fff;
            font-family: var(--font-heading);
            font-size: clamp(1.2rem, 4.2vw, 1.5rem);
            font-weight: 900;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        .games-head p { margin: 0 0 18px; opacity: .8; }

        .game-search { position: relative; max-width: 520px; margin-bottom: 22px; }

        .game-results {
            position: absolute;
            z-index: 20;
            left: 0;
            right: 0;
            margin: 4px 0 0;
            padding: 4px;
            list-style: none;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: rgb(var(--panel-deep-rgb));
            box-shadow: var(--shadow);
            max-height: 340px;
            overflow-y: auto;
        }

        .game-result {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 6px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: inherit;
            font: inherit;
            text-align: left;
            cursor: pointer;
        }

        .game-result:hover, .game-result:focus-visible { background: rgba(255, 255, 255, .08); outline: none; }
        .game-result img, .game-result .game-thumb-empty { width: 92px; height: 43px; border-radius: 6px; object-fit: cover; flex: none; }
        .game-result-hint { padding: 10px; opacity: .7; }

        .game-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 14px;
        }

        .game-card {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: rgba(var(--panel-rgb), .9);
            background-image: var(--sheen);
        }

        .game-card img, .game-card .game-thumb-empty { display: block; width: 100%; aspect-ratio: 460 / 215; object-fit: cover; }

        .game-thumb-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, .06);
            font-size: 1.6rem;
        }

        .game-card-body { padding: 10px 12px 12px; }

        .game-card h2 {
            margin: 0;
            color: #fff;
            font-family: var(--font-heading);
            font-size: .95rem;
            font-weight: 800;
            line-height: 1.25;
            overflow-wrap: anywhere;
        }

        .game-card .post-meta { margin: 4px 0 0; font-size: .8rem; }

        .game-remove {
            position: absolute;
            top: 6px;
            right: 6px;
            width: 30px;
            height: 30px;
            border: 0;
            border-radius: 50%;
            background: rgba(0, 0, 0, .65);
            color: #fff;
            font-size: 1rem;
            line-height: 1;
            cursor: pointer;
        }

        .game-remove:hover { background: rgba(190, 30, 45, .9); }
    </style>
