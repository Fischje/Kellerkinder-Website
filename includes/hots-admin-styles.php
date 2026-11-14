<?php
// Ergänzende Styles nur für die Admin-Unterseite „Heroes of the Storm – Builds“ (admin-builds.php).
?>
    <style>
        .hots-back { margin: 0 0 14px; font-weight: 700; }
        .hots-back a { color: var(--accent-text); text-decoration: none; }
        .hots-back a:hover, .hots-back a:focus-visible { text-decoration: underline; }

        .hots-layout { display: grid; grid-template-columns: minmax(240px, 320px) minmax(0, 1fr); gap: 22px; align-items: start; }
        .hots-layout > .admin-panel { margin: 0; }

        .hots-search { display: grid; gap: 8px; margin-bottom: 12px; }
        .hots-search label.admin-switch { padding: 0; }

        .hots-hero-list { display: grid; gap: 4px; max-height: 62vh; margin: 0; padding: 0; overflow-y: auto; list-style: none; }
        .hots-hero {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--line);
            background: var(--panel-strong);
            color: var(--text);
            font: inherit;
            text-align: left;
            cursor: pointer;
        }
        .hots-hero:hover, .hots-hero:focus-visible { border-color: var(--accent); outline: none; }
        .hots-hero[aria-current="true"] { border-color: var(--accent); background: rgba(var(--accent-rgb), .12); }
        .hots-hero-name { min-width: 0; overflow-wrap: anywhere; font-weight: 700; }
        .hots-hero-name small { display: block; color: var(--muted); font-weight: 500; }
        .hots-count { flex: none; min-width: 1.9em; padding: 2px 8px; border: 1px solid var(--line); text-align: center; font-size: .8rem; font-weight: 700; }
        .hots-count.has { border-color: var(--accent); color: var(--accent-text); }

        .hots-detail-head { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 6px; }
        .hots-detail-head .section-title { margin: 0; overflow-wrap: anywhere; }
        .hots-detail-actions { display: flex; flex-wrap: wrap; gap: 8px; }
        .hots-meta { margin: 0 0 16px; color: var(--muted); font-size: .85rem; }

        .hots-builds { display: grid; gap: 10px; margin: 0; padding: 0; list-style: none; }
        .hots-build { padding: 14px 16px; border: 1px solid var(--line); background: var(--panel-strong); }
        .hots-build-title { margin: 0 0 4px; color: var(--text); font-size: 1.05rem; font-weight: 800; overflow-wrap: anywhere; }
        .hots-build-note { margin: 0 0 10px; color: var(--muted); overflow-wrap: anywhere; }
        .hots-code-row { display: flex; flex-wrap: wrap; align-items: center; gap: 8px; margin-bottom: 10px; }
        .hots-code { padding: 6px 10px; border: 1px solid var(--line); background: #fff; color: var(--text); font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: .95rem; user-select: all; overflow-wrap: anywhere; }
        .hots-build-actions { display: flex; flex-wrap: wrap; gap: 6px; }
        .hots-build-actions button { padding: 6px 12px; font-size: .8rem; }
        .hots-empty { margin: 0; padding: 18px; border: 1px dashed var(--line); color: var(--muted); text-align: center; }

        @media (max-width: 760px) {
            .hots-layout { grid-template-columns: minmax(0, 1fr); }
            .hots-hero-list { max-height: 38vh; }
        }
    </style>
