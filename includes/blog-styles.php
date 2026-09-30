<?php
// Ergänzende Styles nur für die Blog-Seite. Das Grunddesign kommt aus
// includes/styles.php, damit Kalender und Blog identisch aussehen.
?>
    <style>
        .blog-layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 260px;
            gap: 19px;
            align-items: start;
            margin-top: 19px;
        }

        .blog-panel {
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: rgba(var(--panel-deep-rgb), .5);
            box-shadow: var(--shadow);
        }

        .blog-head {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .blog-head h1 {
            margin: 0;
            color: #fff;
            font-family: var(--font-heading);
            font-size: clamp(1.2rem, 4.2vw, 1.5rem);
            font-weight: 900;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        .blog-head-actions { display: flex; flex-wrap: wrap; gap: 9px; }

        .post-list { display: grid; gap: 16px; }

        .post-card {
            padding: 18px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: rgba(var(--panel-rgb), .9);
            background-image: var(--sheen);
        }

        .post-card h2 {
            margin: 0 0 6px;
            color: #fff;
            font-family: var(--font-heading);
            font-size: 1.12rem;
            font-weight: 900;
            letter-spacing: .01em;
            text-transform: uppercase;
        }

        .post-meta {
            margin: 0 0 12px;
            color: var(--muted);
            font-size: .78rem;
        }

        .post-meta .draft-flag {
            display: inline-block;
            margin-left: 6px;
            padding: 1px 7px;
            border: 1px solid rgba(242, 181, 68, .4);
            border-radius: 999px;
            color: var(--gold);
            font-weight: 700;
        }

        .post-body { color: var(--text); line-height: 1.65; }
        .post-body p { margin: 0 0 12px; }
        .post-body h2, .post-body h3, .post-body h4 {
            font-family: var(--font-heading);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .02em;
            color: #fff;
            margin: 18px 0 8px;
        }
        .post-body h2 { font-size: 1.08rem; }
        .post-body h3 { font-size: .98rem; }
        .post-body h4 { font-size: .9rem; }
        .post-body a { color: var(--primary-hover); }
        .post-body img {
            max-width: 100%;
            height: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--line);
        }
        .post-body blockquote {
            margin: 12px 0;
            padding: 8px 14px;
            border-left: 3px solid var(--primary);
            background: rgba(var(--panel-soft-rgb), .7);
            color: var(--muted);
        }
        .post-body pre {
            overflow-x: auto;
            padding: 12px;
            border-radius: var(--radius-md);
            background: rgba(var(--panel-soft-rgb), .9);
            border: 1px solid var(--line);
        }
        .post-body ul, .post-body ol { padding-left: 22px; margin: 0 0 12px; }
        .post-body hr { border: none; border-top: 1px solid var(--line); margin: 18px 0; }

        .post-tags { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 14px; }

        .tag-chip {
            padding: 3px 10px;
            border: 1px solid var(--line-strong);
            border-radius: 999px;
            background-color: rgba(var(--panel-soft-rgb), .9);
            background-image: var(--sheen);
            color: var(--muted);
            font-size: .72rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
        }

        .tag-chip:hover { border-color: var(--primary); color: var(--primary-hover); }

        .tag-chip.active {
            border-color: var(--primary);
            color: var(--primary-hover);
            background-color: rgba(124, 92, 255, .12);
        }

        .tag-chip .tag-count { opacity: .65; margin-left: 4px; }

        .blog-sidebar { display: grid; gap: 16px; }

        .sidebar-box {
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: rgba(var(--panel-deep-rgb), .5);
            box-shadow: var(--shadow);
        }

        .sidebar-box h2 {
            margin: 0 0 12px;
            color: #fff;
            font-family: var(--font-heading);
            font-size: .88rem;
            font-weight: 900;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        .yt-embed {
            display: block;
            position: relative;
            max-width: 480px;
            margin: 12px 0;
            border-radius: var(--radius-md);
            overflow: hidden;
            border: 1px solid var(--line);
        }

        .yt-embed img { display: block; width: 100%; border: none; border-radius: 0; }

        .yt-embed .yt-embed-play {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            font-size: 2.6rem;
            color: #fff;
            text-shadow: 0 2px 12px rgba(0,0,0,.7);
            background: rgba(0,0,0,.18);
        }

        /* ===== Editor ===== */
        .editor-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            padding: 8px;
            border: 1px solid var(--line-strong);
            border-bottom: none;
            border-radius: var(--radius-md) var(--radius-md) 0 0;
            background: rgba(var(--panel-soft-rgb), .95);
        }

        .editor-toolbar button {
            min-width: 34px;
            height: 32px;
            padding: 0 8px;
            border: 1px solid transparent;
            border-radius: 6px;
            background: none;
            color: var(--text);
            cursor: pointer;
            font-size: .82rem;
            font-weight: 700;
        }

        .editor-toolbar button:hover { border-color: var(--line-strong); background: rgba(255,255,255,.05); }
        .editor-toolbar .sep { width: 1px; margin: 4px 3px; background: var(--line-strong); }

        .editor-surface {
            min-height: 320px;
            max-height: 60vh;
            overflow-y: auto;
            padding: 14px;
            border: 1px solid var(--line-strong);
            border-radius: 0 0 var(--radius-md) var(--radius-md);
            background: rgba(var(--panel-soft-rgb), .65);
            color: var(--text);
            line-height: 1.65;
            outline: none;
        }

        .editor-surface:focus { border-color: var(--primary); }
        .editor-surface img { max-width: 100%; height: auto; border-radius: var(--radius-md); }
        .editor-surface:empty::before {
            content: attr(data-placeholder);
            color: var(--muted);
        }

        .editor-hint { margin: 6px 0 0; color: var(--muted); font-size: .76rem; }

        @media (max-width: 900px) {
            .blog-layout { grid-template-columns: minmax(0, 1fr); }
            .blog-sidebar { order: 2; }
        }

        @media (max-width: 680px) {
            .blog-panel { padding: 16px 13px; }
            .post-card { padding: 14px; }
            .blog-head { flex-direction: column; align-items: flex-start; }
        }
    </style>
