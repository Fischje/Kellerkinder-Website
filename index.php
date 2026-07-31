<?php
declare(strict_types=1);

date_default_timezone_set('Europe/Berlin');
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#070914">
    <title>Kellerkinder-Online-Kalender</title>
    <meta name="description" content="Der gemeinsame Kellerkinder-Online-Kalender für eure Spieltage.">
    <link rel="icon" href="assets/kellerkinder-logo.svg" type="image/svg+xml">
    <style>
        :root {
            --bg: #05060b;
            --bg-soft: #0a0d16;
            --panel: rgba(10, 13, 24, 0.88);
            --panel-strong: rgba(12, 16, 30, 0.97);
            --panel-soft: rgba(18, 23, 40, 0.8);
            --line: rgba(139, 151, 190, 0.22);
            --line-strong: rgba(163, 179, 225, 0.36);
            --text: #f5f7ff;
            --muted: #a9b1c9;
            --cyan: #35e7ff;
            --blue: #5674ff;
            --violet: #a95cff;
            --pink: #ff4fc8;
            --green: #3ee78f;
            --orange: #ffad42;
            --red: #ff5b6d;
            --online: #24d67c;
            --late: #ffad42;
            --absent: #ff5368;
            --vacation: #43b8ff;
            --open: #66708b;
            --danger: #d93d55;
            --shadow: 0 22px 65px rgba(0, 0, 0, 0.55);
            --glow: 0 0 24px rgba(53, 231, 255, 0.14), 0 0 52px rgba(169, 92, 255, 0.08);
        }

        * { box-sizing: border-box; }

        html {
            min-height: 100%;
            background: var(--bg);
            color-scheme: dark;
        }

        body {
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            font-family: Candara, Aptos, "Segoe UI Variable", "Segoe UI", sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 50% -15%, rgba(44, 61, 125, .34), transparent 35rem),
                linear-gradient(180deg, #070914 0%, #04050a 62%, #080912 100%);
            background-attachment: fixed;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            inset: -20%;
            z-index: 0;
            pointer-events: none;
        }

        body::before {
            background:
                radial-gradient(circle at 16% 22%, rgba(53, 231, 255, .24) 0 0, transparent 23rem),
                radial-gradient(circle at 82% 18%, rgba(255, 79, 200, .21) 0 0, transparent 25rem),
                radial-gradient(circle at 73% 78%, rgba(169, 92, 255, .23) 0 0, transparent 28rem),
                radial-gradient(circle at 20% 82%, rgba(70, 105, 255, .18) 0 0, transparent 27rem);
            filter: blur(26px) saturate(125%);
            animation: rgbDrift 18s ease-in-out infinite alternate;
        }

        body::after {
            inset: 0;
            opacity: .45;
            background-image:
                linear-gradient(rgba(100, 125, 190, .065) 1px, transparent 1px),
                linear-gradient(90deg, rgba(100, 125, 190, .065) 1px, transparent 1px),
                repeating-linear-gradient(180deg, rgba(255,255,255,.018) 0 1px, transparent 1px 4px);
            background-size: 44px 44px, 44px 44px, 100% 4px;
            mask-image: linear-gradient(180deg, #000 0%, rgba(0,0,0,.55) 55%, transparent 100%);
        }

        @keyframes rgbDrift {
            0% { transform: translate3d(-1.5%, -1%, 0) scale(1); }
            50% { transform: translate3d(2%, 1.5%, 0) scale(1.04); }
            100% { transform: translate3d(-.5%, 2.5%, 0) scale(1.02); }
        }

        @keyframes glowPulse {
            0%, 100% { opacity: .72; filter: saturate(105%); }
            50% { opacity: 1; filter: saturate(135%); }
        }

        button, input { font: inherit; }
        button { -webkit-tap-highlight-color: transparent; }

        .page-shell {
            position: relative;
            z-index: 1;
            width: min(1180px, 100%);
            margin: 0 auto;
            padding: 22px 14px 46px;
        }

        .masthead {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            padding: 27px 18px 24px;
            border: 1px solid transparent;
            border-radius: 18px;
            background:
                linear-gradient(var(--panel-strong), var(--panel-strong)) padding-box,
                linear-gradient(110deg, rgba(53,231,255,.75), rgba(86,116,255,.35), rgba(255,79,200,.7)) border-box;
            box-shadow: var(--shadow), var(--glow), inset 0 1px 0 rgba(255,255,255,.055);
            text-align: center;
        }

        .masthead::before {
            content: "";
            position: absolute;
            z-index: -1;
            inset: -80% -15%;
            background: conic-gradient(from 180deg, transparent 0 20%, rgba(53,231,255,.14) 27%, transparent 35% 54%, rgba(255,79,200,.13) 62%, transparent 70% 100%);
            animation: rgbDrift 14s ease-in-out infinite alternate-reverse;
        }

        .masthead::after {
            content: "";
            position: absolute;
            inset: 8px;
            z-index: -1;
            border: 1px solid rgba(255,255,255,.045);
            border-radius: 12px;
            pointer-events: none;
        }

        .crest {
            position: relative;
            width: clamp(86px, 18vw, 118px);
            aspect-ratio: 1;
            margin: -4px auto 12px;
            display: grid;
            place-items: center;
            filter: drop-shadow(0 0 18px rgba(53,231,255,.35)) drop-shadow(0 0 30px rgba(255,79,200,.22));
            animation: logoFloat 5s ease-in-out infinite;
        }

        .crest img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0) rotate(-1deg); }
            50% { transform: translateY(-5px) rotate(1deg); }
        }

        h1 {
            margin: 0;
            color: #fff;
            font-size: clamp(2rem, 6vw, 3.25rem);
            line-height: 1;
            letter-spacing: .02em;
            text-shadow:
                0 0 12px rgba(53,231,255,.4),
                0 0 30px rgba(169,92,255,.32),
                0 3px 18px rgba(0,0,0,.8);
        }

        .subtitle-group {
            display: grid;
            gap: 7px;
            max-width: 760px;
            margin: 15px auto 0;
        }

        .subtitle {
            margin: 0;
            color: #e8ecf8;
            font-size: clamp(1.02rem, 2.3vw, 1.22rem);
            font-weight: 800;
            line-height: 1.38;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 11px;
            align-items: center;
            justify-content: space-between;
            margin: 19px 0 13px;
        }

        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .toolbar-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 11px;
            border: 1px solid rgba(139,151,190,.22);
            border-radius: 999px;
            color: #dfe4f3;
            background: rgba(10, 13, 24, .78);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.045);
            backdrop-filter: blur(10px);
            font-size: .86rem;
        }

        .legend-icon {
            display: grid;
            width: 25px;
            height: 25px;
            place-items: center;
            border: 1px solid rgba(255,255,255,.19);
            border-radius: 8px;
            color: white;
            font-size: .8rem;
            box-shadow: 0 0 12px currentColor;
        }

        .legend-icon.online { background: rgba(36,214,124,.82); color: #69ffb4; }
        .legend-icon.late { background: rgba(255,173,66,.82); color: #ffd28d; }
        .legend-icon.absent { background: rgba(255,83,104,.82); color: #ff96a3; }
        .legend-icon.vacation { background: rgba(67,184,255,.82); color: #99dcff; }
        .legend-icon.open { background: rgba(102,112,139,.78); color: #c5ccdc; }

        .primary-button,
        .secondary-button,
        .danger-button {
            min-height: 46px;
            border-radius: 11px;
            padding: 10px 16px;
            border: 1px solid;
            cursor: pointer;
            font-weight: 800;
            letter-spacing: .01em;
            transition: transform .14s ease, filter .14s ease, box-shadow .14s ease, border-color .14s ease;
        }

        .primary-button {
            color: #fff;
            border-color: rgba(91, 225, 255, .6);
            background: linear-gradient(115deg, #126f9f, #5550d9 58%, #a33f9a);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.24), 0 0 20px rgba(53,231,255,.22), 0 7px 18px rgba(0,0,0,.35);
        }

        .secondary-button {
            color: var(--text);
            border-color: rgba(150,164,205,.34);
            background: linear-gradient(180deg, #20263b, #121727);
        }

        .danger-button {
            color: white;
            border-color: rgba(255,112,128,.54);
            background: linear-gradient(180deg, #b83a52, #6d1f32);
            box-shadow: 0 0 17px rgba(255,83,104,.13);
        }

        .primary-button:hover,
        .secondary-button:hover,
        .danger-button:hover {
            filter: brightness(1.12);
            transform: translateY(-1px);
        }

        .primary-button:active,
        .secondary-button:active,
        .danger-button:active { transform: translateY(1px); }

        .board {
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
            border-radius: 16px;
            background:
                linear-gradient(var(--panel-strong), var(--panel-strong)) padding-box,
                linear-gradient(115deg, rgba(53,231,255,.38), rgba(109,104,255,.2) 48%, rgba(255,79,200,.34)) border-box;
            box-shadow: var(--shadow), var(--glow), inset 0 1px 0 rgba(255,255,255,.04);
        }

        .board::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 2px;
            z-index: 6;
            background: linear-gradient(90deg, transparent, var(--cyan), var(--violet), var(--pink), transparent);
            opacity: .8;
            box-shadow: 0 0 15px rgba(53,231,255,.55);
        }

        .table-scroll {
            overflow-x: auto;
            overscroll-behavior-inline: contain;
            scrollbar-color: #555f83 #0b0e18;
        }

        .table-scroll::-webkit-scrollbar { height: 10px; }
        .table-scroll::-webkit-scrollbar-track { background: #0b0e18; }
        .table-scroll::-webkit-scrollbar-thumb {
            border: 2px solid #0b0e18;
            border-radius: 999px;
            background: linear-gradient(90deg, #277fa2, #7259c8, #a33f87);
        }

        table {
            width: 100%;
            min-width: 850px;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        th,
        td {
            border-right: 1px solid rgba(121,136,178,.18);
            border-bottom: 1px solid rgba(121,136,178,.18);
            text-align: center;
            vertical-align: middle;
        }

        thead th {
            height: 80px;
            padding: 8px 6px;
            color: #f5f7ff;
            background:
                radial-gradient(circle at 50% 0%, rgba(86,116,255,.18), transparent 80%),
                linear-gradient(180deg, #181e33, #101524);
            box-shadow: inset 0 -2px 0 rgba(53,231,255,.13), inset 0 1px 0 rgba(255,255,255,.04);
        }

        tbody td,
        tbody th {
            height: 86px;
            background:
                linear-gradient(180deg, rgba(18,23,40,.92), rgba(10,14,25,.96));
        }

        tbody tr:nth-child(even) td,
        tbody tr:nth-child(even) th {
            background:
                linear-gradient(180deg, rgba(21,27,47,.94), rgba(12,16,29,.97));
        }

        tbody tr:hover td,
        tbody tr:hover th {
            background-color: rgba(86,116,255,.06);
        }

        tr:last-child td,
        tr:last-child th { border-bottom: 0; }
        th:last-child,
        td:last-child { border-right: 0; }

        .player-heading,
        .player-cell {
            position: sticky;
            left: 0;
            z-index: 3;
            width: 160px;
            min-width: 160px;
        }

        .player-heading {
            z-index: 5;
            padding-left: 14px;
            color: #dce6ff;
            text-align: left;
        }

        .player-cell {
            padding: 8px;
            background: linear-gradient(180deg, #181e32, #101522) !important;
            box-shadow: 9px 0 20px rgba(0,0,0,.3);
        }

        .player-button {
            width: 100%;
            min-height: 52px;
            padding: 8px 9px;
            overflow-wrap: anywhere;
            border: 1px solid rgba(86,116,255,.38);
            border-radius: 10px;
            color: #f4f6ff;
            background:
                linear-gradient(135deg, rgba(53,231,255,.055), rgba(169,92,255,.08)),
                rgba(5,8,16,.58);
            cursor: pointer;
            font-weight: 800;
            line-height: 1.15;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.035);
            transition: border-color .14s ease, box-shadow .14s ease, transform .14s ease;
        }

        .player-button:hover {
            border-color: rgba(53,231,255,.65);
            box-shadow: 0 0 15px rgba(53,231,255,.1);
            transform: translateY(-1px);
        }

        .player-button small {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: .67rem;
            font-weight: 400;
        }

        .date-day {
            display: block;
            color: #9ddfff;
            font-size: 1.02rem;
            font-weight: 900;
            text-shadow: 0 0 10px rgba(53,231,255,.25);
        }

        .date-value {
            display: block;
            margin-top: 3px;
            color: #e9edfa;
            font-size: .83rem;
            font-weight: 700;
        }

        .past-tag {
            display: inline-block;
            margin-top: 5px;
            padding: 2px 7px;
            border: 1px solid rgba(164,174,205,.25);
            border-radius: 999px;
            color: #9ca5bd;
            background: rgba(5,7,12,.35);
            font-size: .61rem;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .date-remove {
            width: 22px;
            height: 22px;
            margin: 5px auto 0;
            display: grid;
            place-items: center;
            border: 1px solid rgba(255,112,128,.45);
            border-radius: 7px;
            color: #ff9aaa;
            background: rgba(95,22,40,.48);
            cursor: pointer;
            font-size: .78rem;
            line-height: 1;
        }

        .date-remove:hover {
            color: #fff;
            border-color: rgba(255,112,128,.8);
            background: rgba(180,45,70,.7);
        }

        .status-cell { padding: 6px; }

        .status-button {
            position: relative;
            overflow: hidden;
            width: 100%;
            min-height: 68px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            border: 1px solid;
            border-radius: 11px;
            color: white;
            cursor: pointer;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.13), 0 6px 15px rgba(0,0,0,.28);
            transition: transform .14s ease, filter .14s ease, box-shadow .14s ease;
        }

        .status-button::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(125deg, rgba(255,255,255,.15), transparent 38% 70%, rgba(255,255,255,.04));
            pointer-events: none;
        }

        .status-button:hover {
            filter: brightness(1.12) saturate(1.08);
            transform: translateY(-1px);
        }

        .status-button .icon {
            position: relative;
            z-index: 1;
            font-size: 1.25rem;
            filter: drop-shadow(0 0 7px rgba(255,255,255,.35));
        }

        .status-button .label {
            position: relative;
            z-index: 1;
            font-size: .72rem;
            font-weight: 900;
            letter-spacing: .02em;
        }

        .status-button .note {
            position: relative;
            z-index: 1;
            max-width: 88px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: rgba(255,255,255,.9);
            font-size: .61rem;
        }

        .status-button.online {
            border-color: rgba(105,255,180,.68);
            background: linear-gradient(145deg, #15955a, #0f5c43);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.16), 0 0 17px rgba(36,214,124,.17), 0 6px 15px rgba(0,0,0,.28);
        }

        .status-button.late {
            border-color: rgba(255,210,141,.72);
            background: linear-gradient(145deg, #c9751c, #774115);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.16), 0 0 17px rgba(255,173,66,.16), 0 6px 15px rgba(0,0,0,.28);
        }

        .status-button.absent {
            border-color: rgba(255,150,163,.7);
            background: linear-gradient(145deg, #b42e49, #651e32);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.15), 0 0 17px rgba(255,83,104,.17), 0 6px 15px rgba(0,0,0,.28);
        }

        .status-button.vacation {
            border-color: rgba(153,220,255,.72);
            background: linear-gradient(145deg, #197db9, #214e83);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.15), 0 0 17px rgba(67,184,255,.18), 0 6px 15px rgba(0,0,0,.28);
        }

        .status-button.open {
            border-color: rgba(139,151,190,.35);
            background: linear-gradient(145deg, #3a4259, #242a3a);
            color: #c8cfdf;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.06), 0 6px 15px rgba(0,0,0,.24);
        }

        .empty-state {
            display: none;
            padding: 48px 20px;
            color: #aab3ca;
            text-align: center;
        }

        .empty-state strong {
            display: block;
            margin-bottom: 7px;
            color: #91eaff;
            font-size: 1.15rem;
            text-shadow: 0 0 14px rgba(53,231,255,.24);
        }

        .instructions {
            position: relative;
            overflow: hidden;
            margin-top: 19px;
            padding: 22px;
            border: 1px solid transparent;
            border-radius: 16px;
            color: #e8ecf8;
            background:
                linear-gradient(rgba(11,15,27,.93), rgba(11,15,27,.93)) padding-box,
                linear-gradient(120deg, rgba(53,231,255,.27), rgba(86,116,255,.14), rgba(255,79,200,.27)) border-box;
            box-shadow: var(--shadow), var(--glow), inset 0 1px 0 rgba(255,255,255,.04);
        }

        .instructions::before {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            right: -130px;
            top: -170px;
            border-radius: 50%;
            background: rgba(169,92,255,.12);
            filter: blur(20px);
            pointer-events: none;
        }

        .instructions h2 {
            position: relative;
            margin: 0 0 16px;
            color: #fff;
            font-size: clamp(1.45rem, 4vw, 1.85rem);
            text-shadow: 0 0 18px rgba(53,231,255,.18);
        }

        .instruction-grid {
            position: relative;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .instruction-item {
            display: grid;
            grid-template-columns: 38px 1fr;
            gap: 11px;
            align-items: start;
            padding: 14px;
            border: 1px solid rgba(137,151,193,.2);
            border-radius: 12px;
            background: rgba(21,26,45,.62);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.035);
            font-size: 1.06rem;
            line-height: 1.58;
        }

        .instruction-number {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(117,230,255,.58);
            border-radius: 11px;
            color: white;
            background: linear-gradient(135deg, #157ca3, #6550d3 58%, #a34093);
            box-shadow: 0 0 15px rgba(53,231,255,.16), inset 0 1px 0 rgba(255,255,255,.2);
            font-weight: 900;
        }

        .instruction-item p { margin: 0; }
        .instruction-item strong { color: #9eeaff; }

        .editing-note {
            position: relative;
            margin: 16px 0 0;
            padding: 15px 16px 0;
            border-top: 1px solid rgba(139,151,190,.22);
            color: #d7dced;
            font-size: 1.08rem;
            line-height: 1.62;
        }

        .editing-note strong { color: #ff8edb; }

        dialog {
            width: min(480px, calc(100% - 24px));
            max-height: calc(100vh - 24px);
            overflow: auto;
            padding: 0;
            border: 1px solid transparent;
            border-radius: 16px;
            color: var(--text);
            background:
                linear-gradient(#111628, #0b0f1c) padding-box,
                linear-gradient(120deg, rgba(53,231,255,.62), rgba(86,116,255,.28), rgba(255,79,200,.55)) border-box;
            box-shadow: 0 28px 90px rgba(0,0,0,.75), 0 0 40px rgba(86,116,255,.12), inset 0 1px 0 rgba(255,255,255,.04);
        }

        dialog::backdrop {
            background: rgba(1,2,6,.8);
            backdrop-filter: blur(6px);
        }

        .modal-content { padding: 21px; }

        .modal-title {
            margin: 0;
            color: #fff;
            font-size: 1.52rem;
            text-shadow: 0 0 14px rgba(53,231,255,.2);
        }

        .modal-subtitle {
            margin: 7px 0 18px;
            color: #aeb7ce;
            font-size: .94rem;
        }

        label {
            display: block;
            margin: 0 0 7px;
            color: #dce3f5;
            font-weight: 800;
        }

        input[type="text"] {
            width: 100%;
            min-height: 47px;
            padding: 10px 12px;
            border: 1px solid rgba(135,151,197,.38);
            border-radius: 10px;
            outline: none;
            color: #fff;
            background: #090d19;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.025);
        }

        input[type="text"]::placeholder { color: #6f7890; }

        input[type="text"]:focus {
            border-color: rgba(53,231,255,.72);
            box-shadow: 0 0 0 3px rgba(53,231,255,.11), 0 0 18px rgba(53,231,255,.1);
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 9px;
            margin-bottom: 16px;
        }

        .status-choice {
            min-height: 70px;
            border: 1px solid rgba(135,151,197,.28);
            border-radius: 11px;
            color: var(--text);
            background: linear-gradient(180deg, #1d2439, #111625);
            cursor: pointer;
            font-weight: 800;
            transition: transform .14s ease, border-color .14s ease, box-shadow .14s ease, filter .14s ease;
        }

        .status-choice:hover { filter: brightness(1.12); transform: translateY(-1px); }

        .status-choice.selected {
            border-color: rgba(53,231,255,.78);
            box-shadow: 0 0 0 3px rgba(53,231,255,.1), 0 0 20px rgba(169,92,255,.13);
        }

        .status-choice[data-status=""] {
            grid-column: 1 / -1;
            min-height: 57px;
        }

        .status-choice span {
            display: block;
            margin-bottom: 3px;
            font-size: 1.3rem;
        }

        .modal-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            justify-content: flex-end;
            margin-top: 18px;
        }

        .modal-actions .danger-button { margin-right: auto; }

        .toast {
            position: fixed;
            z-index: 30;
            right: 14px;
            bottom: max(14px, env(safe-area-inset-bottom));
            max-width: min(380px, calc(100% - 28px));
            padding: 12px 15px;
            border: 1px solid rgba(53,231,255,.38);
            border-radius: 11px;
            color: #fff;
            background: rgba(11,15,27,.96);
            box-shadow: 0 15px 40px rgba(0,0,0,.6), 0 0 22px rgba(53,231,255,.11);
            opacity: 0;
            transform: translateY(14px);
            pointer-events: none;
            transition: opacity .18s ease, transform .18s ease;
        }

        .toast.show { opacity: 1; transform: translateY(0); }

        .loading {
            padding: 46px 20px;
            color: #aeb7cc;
            text-align: center;
        }

        .discord-note {
            position: relative;
            overflow: hidden;
            margin-top: 18px;
            padding: 18px 19px;
            border: 1px solid transparent;
            border-radius: 14px;
            background:
                linear-gradient(rgba(14,18,34,.94), rgba(14,18,34,.94)) padding-box,
                linear-gradient(115deg, rgba(88,101,242,.95), rgba(53,231,255,.72), rgba(255,79,200,.72)) border-box;
            box-shadow: 0 0 30px rgba(88,101,242,.12), inset 0 1px 0 rgba(255,255,255,.05);
        }

        .discord-note::after {
            content: "";
            position: absolute;
            width: 170px;
            height: 170px;
            right: -70px;
            top: -90px;
            border-radius: 50%;
            background: rgba(88,101,242,.18);
            filter: blur(10px);
            pointer-events: none;
        }

        .discord-note h3 {
            position: relative;
            margin: 0 0 8px;
            color: #fff;
            font-size: 1.18rem;
        }

        .discord-note p {
            position: relative;
            margin: 0;
            color: #cdd4e9;
            font-size: 1.05rem;
            line-height: 1.62;
        }

        .discord-note code {
            color: #fff;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .storage-warning {
            margin: 14px 0;
            padding: 13px 15px;
            border: 1px solid rgba(255,173,66,.52);
            border-radius: 11px;
            color: #ffe0ac;
            background: rgba(70,43,13,.76);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.055), 0 0 20px rgba(255,173,66,.08);
            line-height: 1.48;
        }

        .storage-warning strong { color: #fff0ce; }
        .storage-warning code { color: #fff; font-weight: 800; }

        @media (max-width: 680px) {
            .page-shell { padding: 10px 8px 30px; }
            .masthead { padding: 21px 12px 20px; border-radius: 14px; }
            .crest { width: 88px; }
            .subtitle-group { margin-top: 12px; gap: 5px; }
            .subtitle { font-size: 1rem; }
            .toolbar { align-items: stretch; }
            .toolbar-actions { width: 100%; }
            .toolbar-actions > button { flex: 1 1 190px; }
            .legend { width: 100%; gap: 6px; }
            .legend-item { flex: 1 1 calc(50% - 6px); justify-content: center; padding: 6px 8px; }
            table { min-width: 790px; }
            .player-heading, .player-cell { width: 125px; min-width: 125px; }
            .player-heading { padding-left: 9px; }
            .status-button { min-height: 64px; }
            .modal-actions > button { flex: 1 1 120px; }
            .modal-actions .danger-button { margin-right: 0; }
            .instructions { padding: 17px 13px; border-radius: 14px; }
            .instruction-grid { grid-template-columns: 1fr; gap: 9px; }
            .instruction-item { grid-template-columns: 36px 1fr; padding: 12px; font-size: 1.04rem; }
            .editing-note { padding-inline: 4px; font-size: 1.04rem; }
        }

        @media (max-width: 390px) {
            .legend-item { font-size: .8rem; }
            .status-grid { grid-template-columns: 1fr; }
            .status-choice[data-status=""] { grid-column: auto; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                scroll-behavior: auto !important;
                animation: none !important;
                transition: none !important;
            }
        }
    </style>
</head>
<body>
<main class="page-shell">
    <header class="masthead">
        <div class="crest">
            <img src="assets/kellerkinder-logo.svg" alt="Kellerkinder Gaming-Logo">
        </div>
        <h1>Kellerkinder-Online-Kalender</h1>
        <div class="subtitle-group">
            <p class="subtitle">Wer ist wann da und zockt mit</p>
        </div>
    </header>

    <section class="toolbar" aria-label="Steuerung und Legende">
        <div class="legend" aria-label="Status-Legende">
            <span class="legend-item"><span class="legend-icon online">⚔</span> Online</span>
            <span class="legend-item"><span class="legend-icon late">◷</span> Später</span>
            <span class="legend-item"><span class="legend-icon absent">✕</span> Verhindert</span>
            <span class="legend-item"><span class="legend-icon vacation">☀</span> Urlaub</span>
            <span class="legend-item"><span class="legend-icon open">?</span> Offen</span>
        </div>
        <div class="toolbar-actions">
            <button class="secondary-button" id="addDateButton" type="button">＋ Spieltag hinzufügen</button>
            <button class="primary-button" id="addPlayerButton" type="button">＋ Spieler hinzufügen</button>
        </div>
    </section>

    <div id="storageWarning" class="storage-warning" hidden>
        <strong>Nur-Lese-Modus:</strong> Der Plan ist sichtbar, Änderungen können aber noch nicht gespeichert werden.
        Gib dem Ordner <code>data</code> Schreibrechte für den PHP-Webserver.
    </div>

    <section class="board" aria-label="Verfügbarkeitsplan">
        <div id="loading" class="loading">Der Gildenplan wird geladen …</div>
        <div id="tableScroll" class="table-scroll" hidden>
            <table>
                <thead>
                <tr id="dateHeaderRow">
                    <th scope="col" class="player-heading">Spieler</th>
                </tr>
                </thead>
                <tbody id="planBody"></tbody>
            </table>
        </div>
        <div id="emptyState" class="empty-state">
            <strong>Noch keine Helden eingetragen.</strong>
            Füge den ersten Spieler hinzu und trage die Verfügbarkeit ein.
        </div>
    </section>

    <section class="instructions" aria-labelledby="instructionsTitle">
        <h2 id="instructionsTitle">So funktioniert der Kellerkinder-Online-Kalender</h2>
        <div class="instruction-grid">
            <div class="instruction-item">
                <span class="instruction-number">1</span>
                <p><strong>Spieler hinzufügen:</strong> Tippe auf „Spieler hinzufügen“ und trage den Spieler- oder Charakternamen ein. Jeder Name erscheint anschließend als eigene Zeile.</p>
            </div>
            <div class="instruction-item">
                <span class="instruction-number">2</span>
                <p><strong>Status auswählen:</strong> Tippe in der Tabelle auf den gewünschten Termin und wähle „Online“, „Später“, „Verhindert“, „Urlaub“ oder „Offen“.</p>
            </div>
            <div class="instruction-item">
                <span class="instruction-number">3</span>
                <p><strong>Hinweis ergänzen:</strong> Bei Bedarf kannst du zusätzlich eine kurze Information eintragen, zum Beispiel „ab 21:00 Uhr“ oder „nur bis 22:30 Uhr“.</p>
            </div>
            <div class="instruction-item">
                <span class="instruction-number">4</span>
                <p><strong>Einträge ändern oder löschen:</strong> Tippe auf einen Spielernamen, um ihn umzubenennen oder vollständig zu löschen. Einen Terminstatus änderst du einfach durch erneutes Antippen.</p>
            </div>
        </div>
        <p class="editing-note"><strong>Gemeinsamer Plan:</strong> Alle Besucher dieser Website dürfen Spieler und Verfügbarkeiten anlegen, verändern oder löschen. Änderungen werden sofort zentral auf dem Webserver gespeichert und sind anschließend für alle sichtbar.</p>
        <div class="discord-note">
            <h3>💬 Auch in Discord nutzbar</h3>
            <p>Im Kellerkinder-Discord könnt ihr den aktuellen Kalender jederzeit mit dem Befehl <code>/kalender</code> aufrufen und anzeigen lassen.</p>
        </div>
    </section>
</main>

<dialog id="playerDialog">
    <form class="modal-content" id="playerForm" method="dialog">
        <h2 class="modal-title" id="playerDialogTitle">Spieler hinzufügen</h2>
        <p class="modal-subtitle" id="playerDialogSubtitle">Trage den Charakternamen ein.</p>
        <input type="hidden" id="playerId">
        <label for="playerName">Spielername</label>
        <input type="text" id="playerName" maxlength="40" autocomplete="off" required>
        <div class="modal-actions">
            <button class="danger-button" id="deletePlayerButton" type="button" hidden>Spieler löschen</button>
            <button class="secondary-button" type="button" data-close-dialog="playerDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Speichern</button>
        </div>
    </form>
</dialog>

<dialog id="dateDialog">
    <form class="modal-content" id="dateForm" method="dialog">
        <h2 class="modal-title">Spieltag hinzufügen</h2>
        <p class="modal-subtitle">Wähle einen zusätzlichen Spieltag. Mittwoche und Sonntage der nächsten drei Wochen erscheinen automatisch.</p>
        <label for="eventDateInput">Datum</label>
        <input type="date" id="eventDateInput" required>
        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="dateDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Spieltag anlegen</button>
        </div>
    </form>
</dialog>

<dialog id="statusDialog">
    <form class="modal-content" id="statusForm" method="dialog">
        <h2 class="modal-title" id="statusDialogTitle">Verfügbarkeit</h2>
        <p class="modal-subtitle" id="statusDialogSubtitle"></p>
        <input type="hidden" id="statusPlayerId">
        <input type="hidden" id="statusDate">
        <input type="hidden" id="selectedStatus" value="">

        <div class="status-grid" role="group" aria-label="Status auswählen">
            <button class="status-choice" type="button" data-status="online"><span>⚔</span>Online</button>
            <button class="status-choice" type="button" data-status="late"><span>◷</span>Später</button>
            <button class="status-choice" type="button" data-status="absent"><span>✕</span>Verhindert</button>
            <button class="status-choice" type="button" data-status="vacation"><span>☀</span>Urlaub</button>
            <button class="status-choice" type="button" data-status=""><span>?</span>Offen</button>
        </div>

        <label for="statusNote">Hinweis <small>(optional)</small></label>
        <input type="text" id="statusNote" maxlength="60" placeholder="z. B. ab 21:00 Uhr" autocomplete="off">

        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="statusDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Speichern</button>
        </div>
    </form>
</dialog>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script>
    const STATUS_META = {
        online: { icon: '⚔', label: 'Online', className: 'online' },
        late: { icon: '◷', label: 'Später', className: 'late' },
        absent: { icon: '✕', label: 'Verhindert', className: 'absent' },
        vacation: { icon: '☀', label: 'Urlaub', className: 'vacation' },
        '': { icon: '?', label: 'Offen', className: 'open' }
    };

    const state = {
        players: [],
        availability: {},
        eventDates: []
    };

    const loading = document.getElementById('loading');
    const tableScroll = document.getElementById('tableScroll');
    const planBody = document.getElementById('planBody');
    const dateHeaderRow = document.getElementById('dateHeaderRow');
    const emptyState = document.getElementById('emptyState');
    const playerDialog = document.getElementById('playerDialog');
    const playerForm = document.getElementById('playerForm');
    const playerId = document.getElementById('playerId');
    const playerName = document.getElementById('playerName');
    const deletePlayerButton = document.getElementById('deletePlayerButton');
    const dateDialog = document.getElementById('dateDialog');
    const dateForm = document.getElementById('dateForm');
    const eventDateInput = document.getElementById('eventDateInput');
    const statusDialog = document.getElementById('statusDialog');
    const statusForm = document.getElementById('statusForm');
    const statusNote = document.getElementById('statusNote');
    const selectedStatus = document.getElementById('selectedStatus');
    const toast = document.getElementById('toast');
    const storageWarning = document.getElementById('storageWarning');
    let toastTimer;

    function showToast(message) {
        toast.textContent = message;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 2600);
    }

    async function api(action, payload = null) {
        const options = payload === null
            ? { headers: { 'Accept': 'application/json' } }
            : {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action, ...payload })
            };

        const url = payload === null ? `api.php?action=${encodeURIComponent(action)}` : 'api.php';
        const response = await fetch(url, options);
        const raw = await response.text();
        let data;

        try {
            data = raw ? JSON.parse(raw) : {};
        } catch (error) {
            throw new Error(`Der Server liefert keine gültige PHP-Antwort (HTTP ${response.status}). Prüfe, ob PHP für diese Website aktiviert ist.`);
        }

        if (!response.ok || data.ok === false) {
            const apiError = new Error(data.error || 'Die Anfrage konnte nicht verarbeitet werden.');
            apiError.code = data.code || '';
            throw apiError;
        }

        return data;
    }

    async function loadPlan() {
        try {
            const data = await api('bootstrap');
            state.players = data.players || [];
            state.availability = data.availability || {};
            state.eventDates = data.event_dates || [];
            storageWarning.hidden = data.storage_writable !== false;
            renderPlan();
        } catch (error) {
            loading.textContent = 'Der Plan konnte nicht geladen werden.';
            showToast(error.message);
        }
    }

    function parseLocalDate(isoDate) {
        return new Date(`${isoDate}T12:00:00`);
    }

    function formatDateParts(isoDate) {
        const date = parseLocalDate(isoDate);
        return {
            day: new Intl.DateTimeFormat('de-DE', { weekday: 'short' }).format(date).replace('.', ''),
            value: new Intl.DateTimeFormat('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(date),
            label: new Intl.DateTimeFormat('de-DE', { weekday: 'short', day: '2-digit', month: '2-digit', year: 'numeric' }).format(date)
        };
    }

    function renderDateHeaders() {
        const table = dateHeaderRow.closest('table');
        table.style.minWidth = `${160 + (state.eventDates.length * 112)}px`;

        while (dateHeaderRow.children.length > 1) {
            dateHeaderRow.lastElementChild.remove();
        }

        for (const eventDate of state.eventDates) {
            const parts = formatDateParts(eventDate.date);
            const heading = document.createElement('th');
            heading.scope = 'col';
            heading.dataset.date = eventDate.date;

            const day = document.createElement('span');
            day.className = 'date-day';
            day.textContent = parts.day;
            const value = document.createElement('span');
            value.className = 'date-value';
            value.textContent = parts.value;
            heading.append(day, value);

            if (eventDate.is_past) {
                const past = document.createElement('span');
                past.className = 'past-tag';
                past.textContent = 'Vergangen';
                heading.appendChild(past);
            }

            if (eventDate.is_custom) {
                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'date-remove';
                removeButton.textContent = '✕';
                removeButton.title = `${parts.label} entfernen`;
                removeButton.setAttribute('aria-label', `${parts.label} als Spieltag entfernen`);
                removeButton.addEventListener('click', () => deleteEventDate(eventDate.date, parts.label));
                heading.appendChild(removeButton);
            }

            dateHeaderRow.appendChild(heading);
        }
    }

    function renderPlan() {
        loading.hidden = true;
        planBody.replaceChildren();
        renderDateHeaders();

        if (state.players.length === 0) {
            tableScroll.hidden = true;
            emptyState.style.display = 'block';
            return;
        }

        emptyState.style.display = 'none';
        tableScroll.hidden = false;

        for (const player of state.players) {
            const row = document.createElement('tr');

            const playerCell = document.createElement('th');
            playerCell.scope = 'row';
            playerCell.className = 'player-cell';

            const playerButton = document.createElement('button');
            playerButton.type = 'button';
            playerButton.className = 'player-button';
            playerButton.innerHTML = `${escapeHtml(player.name)}<small>Antippen zum Bearbeiten</small>`;
            playerButton.addEventListener('click', () => openPlayerDialog(player));
            playerCell.appendChild(playerButton);
            row.appendChild(playerCell);

            for (const eventDate of state.eventDates) {
                const date = eventDate.date;
                const dateLabel = formatDateParts(date).label;
                const cell = document.createElement('td');
                cell.className = 'status-cell';
                const entry = state.availability[`${player.id}:${date}`] || { status: '', note: '' };
                const meta = STATUS_META[entry.status] || STATUS_META[''];

                const button = document.createElement('button');
                button.type = 'button';
                button.className = `status-button ${meta.className}`;
                button.setAttribute('aria-label', `${player.name}, ${dateLabel}: ${meta.label}${entry.note ? ', ' + entry.note : ''}`);
                button.title = entry.note || meta.label;

                const icon = document.createElement('span');
                icon.className = 'icon';
                icon.textContent = meta.icon;
                const label = document.createElement('span');
                label.className = 'label';
                label.textContent = meta.label;
                button.append(icon, label);

                if (entry.note) {
                    const note = document.createElement('span');
                    note.className = 'note';
                    note.textContent = entry.note;
                    button.appendChild(note);
                }

                button.addEventListener('click', () => openStatusDialog(player, date, entry));
                cell.appendChild(button);
                row.appendChild(cell);
            }

            planBody.appendChild(row);
        }
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>'"]/g, character => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;'
        })[character]);
    }

    function openDateDialog() {
        eventDateInput.value = '';
        dateDialog.showModal();
        requestAnimationFrame(() => {
            eventDateInput.focus();
            if (typeof eventDateInput.showPicker === 'function') {
                try { eventDateInput.showPicker(); } catch (error) { /* Browser erlaubt Fokus dennoch. */ }
            }
        });
    }

    async function deleteEventDate(date, label) {
        if (!confirm(`Den zusätzlichen Spieltag „${label}“ samt aller Einträge wirklich löschen?`)) return;
        try {
            await api('delete_event_date', { event_date: date });
            showToast('Spieltag wurde gelöscht.');
            await loadPlan();
        } catch (error) {
            if (error.code === 'storage_not_writable') storageWarning.hidden = false;
            showToast(error.message);
        }
    }

    function openPlayerDialog(player = null) {
        const isEdit = Boolean(player);
        document.getElementById('playerDialogTitle').textContent = isEdit ? 'Spieler bearbeiten' : 'Spieler hinzufügen';
        document.getElementById('playerDialogSubtitle').textContent = isEdit
            ? 'Name ändern oder den Spieler vollständig löschen.'
            : 'Trage den Spieler- oder Charakternamen ein.';
        playerId.value = player?.id || '';
        playerName.value = player?.name || '';
        deletePlayerButton.hidden = !isEdit;
        playerDialog.showModal();
        requestAnimationFrame(() => playerName.focus());
    }

    function openStatusDialog(player, date, entry) {
        document.getElementById('statusDialogTitle').textContent = player.name;
        document.getElementById('statusDialogSubtitle').textContent = formatDateParts(date).label;
        document.getElementById('statusPlayerId').value = player.id;
        document.getElementById('statusDate').value = date;
        selectedStatus.value = entry.status || '';
        statusNote.value = entry.note || '';
        updateStatusChoices();
        statusDialog.showModal();
    }

    function updateStatusChoices() {
        document.querySelectorAll('.status-choice').forEach(button => {
            button.classList.toggle('selected', button.dataset.status === selectedStatus.value);
        });
    }

    document.getElementById('addDateButton').addEventListener('click', openDateDialog);
    document.getElementById('addPlayerButton').addEventListener('click', () => openPlayerDialog());

    document.querySelectorAll('[data-close-dialog]').forEach(button => {
        button.addEventListener('click', () => document.getElementById(button.dataset.closeDialog).close());
    });

    document.querySelectorAll('.status-choice').forEach(button => {
        button.addEventListener('click', () => {
            selectedStatus.value = button.dataset.status;
            updateStatusChoices();
        });
    });

    dateForm.addEventListener('submit', async event => {
        event.preventDefault();
        const eventDate = eventDateInput.value;
        if (!eventDate) {
            eventDateInput.focus();
            return;
        }

        const weekday = parseLocalDate(eventDate).getDay();
        if (weekday === 0 || weekday === 3) {
            showToast('Mittwoche und Sonntage werden automatisch angezeigt.');
            return;
        }

        try {
            await api('create_event_date', { event_date: eventDate });
            dateDialog.close();
            showToast('Spieltag wurde hinzugefügt.');
            await loadPlan();
        } catch (error) {
            if (error.code === 'storage_not_writable') storageWarning.hidden = false;
            showToast(error.message);
        }
    });

    playerForm.addEventListener('submit', async event => {
        event.preventDefault();
        const name = playerName.value.trim();
        if (!name) {
            playerName.focus();
            return;
        }

        try {
            const action = playerId.value ? 'update_player' : 'create_player';
            await api(action, { id: playerId.value || undefined, name });
            playerDialog.close();
            showToast(playerId.value ? 'Spieler wurde geändert.' : 'Spieler wurde hinzugefügt.');
            await loadPlan();
        } catch (error) {
            if (error.code === 'storage_not_writable') storageWarning.hidden = false;
            showToast(error.message);
        }
    });

    deletePlayerButton.addEventListener('click', async () => {
        const player = state.players.find(item => String(item.id) === String(playerId.value));
        if (!player) return;
        if (!confirm(`„${player.name}“ samt aller Einträge wirklich löschen?`)) return;

        try {
            await api('delete_player', { id: player.id });
            playerDialog.close();
            showToast('Spieler wurde gelöscht.');
            await loadPlan();
        } catch (error) {
            if (error.code === 'storage_not_writable') storageWarning.hidden = false;
            showToast(error.message);
        }
    });

    statusForm.addEventListener('submit', async event => {
        event.preventDefault();
        try {
            await api('set_status', {
                player_id: Number(document.getElementById('statusPlayerId').value),
                event_date: document.getElementById('statusDate').value,
                status: selectedStatus.value,
                note: statusNote.value.trim()
            });
            statusDialog.close();
            showToast('Verfügbarkeit wurde gespeichert.');
            await loadPlan();
        } catch (error) {
            if (error.code === 'storage_not_writable') storageWarning.hidden = false;
            showToast(error.message);
        }
    });

    [playerDialog, dateDialog, statusDialog].forEach(dialog => {
        dialog.addEventListener('click', event => {
            if (event.target === dialog) dialog.close();
        });
    });

    loadPlan();
</script>
</body>
</html>
