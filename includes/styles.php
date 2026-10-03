<?php
// Gemeinsames Stylesheet für alle Seiten (Kalender, Blog).
// Wird von index.php und blog.php eingebunden. Enthält PHP, weil die
// Hintergrundbilder je Theme dynamisch eingesetzt werden.
if (!isset($backgroundImages) || !is_array($backgroundImages)) { $backgroundImages = []; }
?>
    <style>
        :root {
            --font-body: "Montserrat", -apple-system, "Segoe UI", Candara, Aptos, "Segoe UI Variable", sans-serif;
            --font-heading: "Montserrat", -apple-system, "Segoe UI", sans-serif;
        }
    </style>
    <style>
        :root {
            --bg: #060606;
            --bg-soft: #0f1015;
            --panel: #101116;
            --panel-strong: #14151b;
            --panel-soft: #15161d;
            --panel-deep: #0c0d11;
            --panel-rgb: 16, 17, 22;
            --panel-strong-rgb: 20, 21, 27;
            --panel-soft-rgb: 21, 22, 29;
            --panel-deep-rgb: 12, 13, 17;
            --line: rgba(255, 255, 255, 0.08);
            --line-strong: rgba(255, 255, 255, 0.16);
            --text: #e8e9ee;
            --muted: #8d92a3;
            --cyan: #35e7ff;
            --blue: #5674ff;
            --violet: #a95cff;
            --pink: #ff4fc8;
            --green: #3ee78f;
            --orange: #ffad42;
            --red: #ff5b6d;
            --primary: #7c5cff;
            --primary-hover: #9478ff;
            --gold: #f2b544;
            --online: #45d483;
            --late: #f2b544;
            --absent: #ef5b6a;
            --vacation: #7c5cff;
            --open: #565b6b;
            --danger: #ef5b6a;
            --shadow: 0 14px 34px rgba(0, 0, 0, 0.45);
            --glow: none;
            --radius-lg: 16px;
            --radius-md: 10px;
            --radius-sm: 8px;
            --sheen: linear-gradient(180deg, rgba(255,255,255,.12) 0%, rgba(255,255,255,.03) 38%, rgba(255,255,255,0) 60%);
        }

        body[data-theme="summer"] {
            --bg: #050c18;
            --bg-soft: #081020;
            --panel: #0d1524;
            --panel-strong: #101a2c;
            --panel-soft: #121d30;
            --panel-deep: #0a101c;
            --panel-rgb: 13, 21, 36;
            --panel-strong-rgb: 16, 26, 44;
            --panel-soft-rgb: 18, 29, 48;
            --panel-deep-rgb: 10, 16, 28;
            --line: rgba(255, 219, 158, 0.1);
            --line-strong: rgba(255, 219, 158, 0.2);
            --text: #f4f7f4;
            --muted: #97a6a1;
            --cyan: #45f0dd;
            --blue: #3aa8ff;
            --violet: #6fd287;
            --pink: #ff9d5c;
            --green: #53e78e;
            --orange: #ffd166;
            --red: #ff6f6f;
            --primary: #ff9d3f;
            --primary-hover: #ffb464;
            --gold: #ffd166;
            --vacation: #ff9d3f;
            --shadow: 0 14px 34px rgba(0, 12, 12, 0.4);
            --glow: none;
        }

        body[data-theme="winter"] {
            --bg: #17181a;
            --bg-soft: #1c1d1f;
            --panel: #1a1b1d;
            --panel-strong: #1f2022;
            --panel-soft: #212223;
            --panel-deep: #141517;
            --panel-rgb: 26, 27, 29;
            --panel-strong-rgb: 31, 32, 34;
            --panel-soft-rgb: 33, 34, 35;
            --panel-deep-rgb: 20, 21, 23;
            --line: rgba(255, 255, 255, 0.08);
            --line-strong: rgba(180, 216, 255, 0.2);
            --text: #f0f4fa;
            --muted: #8d97ac;
            --cyan: #9de9ff;
            --blue: #5c96ff;
            --violet: #d9f2ff;
            --pink: #ff5b6d;
            --green: #4be39b;
            --orange: #f8d56c;
            --red: #ff4058;
            --primary: #5c96ff;
            --primary-hover: #7fb0ff;
            --gold: #9de9ff;
            --vacation: #5c96ff;
            --shadow: 0 14px 34px rgba(0, 6, 16, 0.45);
            --glow: none;
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
            font-family: var(--font-body);
            color: var(--text);
            background-color: var(--bg);
            background-image: none;
            background-position: center 0;
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
        }

        <?php
        $themeBgColors = [
            'default' => ['20, 11, 27', '.62'],
            'summer' => ['5, 12, 24', '.25'],
            'winter' => ['23, 24, 26', '.25'],
        ];
        foreach ($backgroundImages as $themeKey => $imageUrl):
            if ($imageUrl === null) continue;
            [$bgRgb, $washAlpha] = $themeBgColors[$themeKey];
            $safeUrl = htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8');
        ?>
        body[data-theme="<?= htmlspecialchars($themeKey, ENT_QUOTES, 'UTF-8') ?>"] {
            background-image:
                linear-gradient(rgba(<?= $bgRgb ?>, <?= $washAlpha ?>), rgba(<?= $bgRgb ?>, <?= $washAlpha ?>)),
                radial-gradient(ellipse 90% 78% at 50% 30%, transparent 0%, rgb(<?= $bgRgb ?>) 100%),
                url("<?= $safeUrl ?>");
        }
        <?php endforeach; ?>

        body::before {
            content: "";
            position: fixed;
            inset: -20%;
            z-index: 0;
            pointer-events: none;
            background: radial-gradient(circle at 50% -10%, rgba(124, 92, 255, .07), transparent 40rem);
        }

        body::after {
            content: "";
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(69, 214, 120, .16) 1px, transparent 1px),
                linear-gradient(90deg, rgba(92, 150, 255, .14) 1px, transparent 1px);
            background-size: 120px 120px, 120px 120px;
            mask-image: radial-gradient(circle at 50% 20%, #000 0%, rgba(0,0,0,.5) 55%, transparent 92%);
            -webkit-mask-image: radial-gradient(circle at 50% 20%, #000 0%, rgba(0,0,0,.5) 55%, transparent 92%);
        }

        body[data-theme="summer"]::after {
            background-image:
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='120' height='50' viewBox='0 0 120 50'%3E%3Cpath d='M-10 25 Q 5 5 20 25 T 50 25 T 80 25 T 110 25 T 140 25' stroke='%2387CEFA' stroke-width='2.5' fill='none' stroke-opacity='0.3'/%3E%3C/svg%3E"),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='60' viewBox='0 0 160 60'%3E%3Cpath d='M-15 30 Q 5 5 25 30 T 65 30 T 105 30 T 145 30 T 185 30' stroke='%235ec8ff' stroke-width='2' fill='none' stroke-opacity='0.16'/%3E%3C/svg%3E");
            background-size: 120px 50px, 160px 60px;
            background-position: 0 10%, 30px 55%;
            background-repeat: repeat-x, repeat-x;
        }

        body[data-theme="winter"]::after {
            background-image:
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='72' viewBox='0 0 64 72'%3E%3Cpath d='M32 6 L44 26 L38 26 L48 42 L40 42 L50 58 L14 58 L24 42 L16 42 L26 26 L20 26 Z' fill='%2345c26e' fill-opacity='0.3'/%3E%3Crect x='29' y='58' width='6' height='8' fill='%23345c3f' fill-opacity='0.3'/%3E%3C/svg%3E"),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='72' viewBox='0 0 64 72'%3E%3Cpath d='M32 6 L44 26 L38 26 L48 42 L40 42 L50 58 L14 58 L24 42 L16 42 L26 26 L20 26 Z' fill='%233a9f5c' fill-opacity='0.2'/%3E%3Crect x='29' y='58' width='6' height='8' fill='%23345c3f' fill-opacity='0.2'/%3E%3C/svg%3E");
            background-size: 64px 72px, 64px 72px;
            background-position: 0 0, 32px 36px;
        }

        .season-scene {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .season-scene > * {
            display: none;
            position: absolute;
        }

        body[data-theme="summer"] .summer-sun,
        body[data-theme="summer"] .summer-waves,
        body[data-theme="summer"] .summer-bubbles,
        body[data-theme="winter"] .winter-snow,
        body[data-theme="winter"] .winter-snowbank,
        body[data-theme="winter"] .winter-aurora,
        body[data-theme="winter"] .winter-ember-glow {
            display: block;
        }

        .summer-sun {
            top: clamp(6px, 5vh, 46px);
            right: clamp(4vw, 9vw, 150px);
            width: clamp(220px, 27vw, 400px);
            aspect-ratio: 1;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 226, 158, .5) 0%, rgba(255, 180, 110, .24) 40%, transparent 72%);
            filter: blur(4px);
            opacity: .82;
            animation: glowPulse 7s ease-in-out infinite;
        }

        .summer-waves {
            left: -6vw;
            right: -6vw;
            bottom: -10px;
            height: clamp(150px, 21vh, 240px);
            opacity: .8;
            background:
                radial-gradient(160px 30px at 12% 82%, rgba(255,224,158,.4) 0 60%, transparent 61%),
                radial-gradient(220px 36px at 40% 88%, rgba(255,214,133,.34) 0 60%, transparent 61%),
                radial-gradient(190px 32px at 68% 80%, rgba(255,224,158,.36) 0 60%, transparent 61%),
                radial-gradient(230px 38px at 92% 88%, rgba(255,214,133,.32) 0 60%, transparent 61%),
                linear-gradient(180deg, transparent 0%, rgba(235,190,120,.18) 40%, rgba(214,168,96,.48) 100%);
            filter: blur(1px);
        }

        .summer-waves::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(100deg, transparent 0%, rgba(255, 232, 190, .18) 46%, rgba(255, 232, 190, .32) 50%, rgba(255, 232, 190, .18) 54%, transparent 100%);
            background-size: 260% 100%;
            mix-blend-mode: screen;
            animation: horizonShimmer 9s ease-in-out infinite;
        }

        .summer-bubbles {
            inset: 0;
            opacity: .4;
            background-image:
                radial-gradient(circle, rgba(202,255,247,.42) 0 3px, transparent 4px),
                radial-gradient(circle, rgba(255,244,184,.32) 0 2px, transparent 3px),
                radial-gradient(circle, rgba(108,225,255,.32) 0 4px, transparent 5px);
            background-size: 140px 160px, 210px 190px, 260px 230px;
            background-position: 10% 20%, 80% 32%, 50% 70%;
            filter: blur(.5px);
            animation: bubbleDrift 15s ease-in-out infinite alternate;
        }

        .winter-snow {
            inset: -12vh 0 0;
            opacity: .78;
            background-image:
                radial-gradient(circle, rgba(255,255,255,.92) 0 1.8px, transparent 2.4px),
                radial-gradient(circle, rgba(211,240,255,.82) 0 1.4px, transparent 2px),
                radial-gradient(circle, rgba(255,255,255,.66) 0 2.3px, transparent 3px);
            background-size: 88px 96px, 132px 150px, 190px 220px;
            background-position: 0 0, 36px 48px, 92px 10px;
            animation: snowFall 16s linear infinite;
        }

        .winter-snowbank {
            left: -5vw;
            right: -5vw;
            bottom: -10px;
            height: clamp(120px, 17vh, 200px);
            opacity: .85;
            background:
                radial-gradient(150px 32px at 10% 84%, rgba(255,255,255,.6) 0 58%, transparent 59%),
                radial-gradient(210px 40px at 36% 90%, rgba(230,242,252,.55) 0 58%, transparent 59%),
                radial-gradient(180px 34px at 64% 82%, rgba(255,255,255,.58) 0 58%, transparent 59%),
                radial-gradient(220px 40px at 90% 90%, rgba(230,242,252,.52) 0 58%, transparent 59%),
                linear-gradient(180deg, transparent 0%, rgba(210,230,245,.2) 40%, rgba(214,232,245,.55) 100%);
            filter: blur(1px);
        }

        .winter-aurora {
            top: -14vh;
            bottom: -14vh;
            width: clamp(130px, 19vw, 280px);
            opacity: .32;
            background: linear-gradient(180deg, transparent 0%, rgba(157, 233, 255, .32) 28%, rgba(217, 242, 255, .22) 55%, transparent 88%);
            filter: blur(34px);
            animation: auroraDrift 13s ease-in-out infinite alternate;
        }

        .winter-aurora.left {
            left: clamp(-4vw, -1vw, 40px);
            transform: skewX(-9deg);
        }

        .winter-aurora.right {
            right: clamp(-6vw, -1vw, 20px);
            transform: skewX(9deg);
            opacity: .24;
            animation-delay: -6s;
        }

        .winter-ember-glow {
            right: clamp(18px, 7vw, 110px);
            top: clamp(120px, 22vh, 250px);
            width: clamp(150px, 19vw, 250px);
            aspect-ratio: 1;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 172, 92, .46) 0%, rgba(255, 90, 50, .2) 45%, transparent 76%);
            filter: blur(8px);
            opacity: .58;
            animation: emberPulse 3.6s ease-in-out infinite alternate;
        }

        @keyframes rgbDrift {
            0% { transform: translate3d(-1.5%, -1%, 0) scale(1); }
            50% { transform: translate3d(2%, 1.5%, 0) scale(1.04); }
            100% { transform: translate3d(-.5%, 2.5%, 0) scale(1.02); }
        }

        @keyframes horizonShimmer {
            0% { background-position: 130% 0%; }
            100% { background-position: -30% 0%; }
        }

        @keyframes bubbleDrift {
            0% { transform: translate3d(-8px, 12px, 0); }
            100% { transform: translate3d(12px, -8px, 0); }
        }

        @keyframes snowFall {
            from { transform: translate3d(0, -8vh, 0); }
            to { transform: translate3d(0, 18vh, 0); }
        }

        @keyframes auroraDrift {
            0% { transform: translateX(0) skewX(-9deg); opacity: .26; }
            50% { transform: translateX(2.5vw) skewX(-6deg); opacity: .38; }
            100% { transform: translateX(-1.5vw) skewX(-11deg); opacity: .3; }
        }

        @keyframes emberPulse {
            0% { opacity: .46; transform: scale(1); }
            100% { opacity: .68; transform: scale(1.06); }
        }

        @keyframes glowPulse {
            0%, 100% { opacity: .72; filter: blur(4px) saturate(105%); }
            50% { opacity: 1; filter: blur(2px) saturate(135%); }
        }

        button, input, select { font: inherit; }
        button { -webkit-tap-highlight-color: transparent; }

        .page-shell {
            position: relative;
            z-index: 1;
            width: min(1180px, 100%);
            margin: 0 auto;
            padding: calc(22px + env(safe-area-inset-top)) calc(14px + env(safe-area-inset-right)) calc(46px + env(safe-area-inset-bottom)) calc(14px + env(safe-area-inset-left));
        }

        .masthead {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            padding: 14px 16px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: var(--panel-strong);
            box-shadow: var(--shadow);
        }

        .masthead-row {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 11px;
            color: var(--text);
            text-decoration: none;
            flex: none;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            flex: none;
            display: block;
            object-fit: contain;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .brand-name-row {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .brand-name {
            font-family: var(--font-heading);
            font-size: 1.4rem;
            font-weight: 900;
            letter-spacing: .03em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .brand-sun { display: none; }
        body[data-theme="summer"] .brand-sun { display: inline; font-size: 1.05rem; }

        .main-nav {
            display: flex;
            align-items: center;
            gap: 22px;
            margin: 0 auto 0 28px;
            flex: 1 1 0%;
            min-width: 0;
            overflow-x: auto;
            overflow-y: hidden;
        }

        .nav-link {
            position: relative;
            flex: none;
            padding-bottom: 3px;
            color: var(--muted);
            font-size: .92rem;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
            cursor: pointer;
            transition: color .14s ease;
        }

        .nav-link::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -3px;
            height: 2px;
            border-radius: 2px;
            background: var(--primary);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .16s ease;
        }

        .nav-link:hover,
        .nav-link.active { color: var(--text); }
        .nav-link:hover::after,
        .nav-link.active::after { transform: scaleX(1); }

        .nav-link.disabled {
            color: var(--muted);
            opacity: .55;
            cursor: default;
            pointer-events: none;
        }

        .nav-link small {
            font-size: .68rem;
            font-weight: 600;
            opacity: .85;
        }

        .masthead::after {
            content: "";
            position: absolute;
            inset: 8px;
            z-index: -1;
            border: 1px solid rgba(255,255,255,.03);
            border-radius: 12px;
            pointer-events: none;
        }

        body[data-theme="summer"] .masthead::after {
            background:
                radial-gradient(circle at 12% 22%, rgba(255, 209, 102, .14) 0 4px, transparent 5px),
                radial-gradient(circle at 88% 28%, rgba(69, 240, 221, .12) 0 5px, transparent 6px);
        }

        body[data-theme="winter"] .masthead::after {
            background:
                radial-gradient(28px 9px at 8% 0, rgba(255,255,255,.5) 0 60%, transparent 61%),
                radial-gradient(42px 12px at 24% 0, rgba(225,244,255,.46) 0 60%, transparent 61%),
                radial-gradient(34px 10px at 44% 0, rgba(255,255,255,.46) 0 60%, transparent 61%),
                radial-gradient(46px 14px at 69% 0, rgba(225,244,255,.44) 0 60%, transparent 61%),
                radial-gradient(32px 10px at 88% 0, rgba(255,255,255,.44) 0 60%, transparent 61%);
        }

        .install-app-button {
            flex: none;
            z-index: 5;
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            padding: 7px;
            border: 1px solid var(--line-strong);
            border-radius: var(--radius-md);
            color: var(--text);
            background: var(--panel-soft);
            cursor: pointer;
            transition: transform .14s ease, border-color .14s ease;
        }

        .install-app-button:hover {
            transform: translateY(-1px);
            border-color: var(--primary);
        }

        .install-app-button img {
            display: block;
            width: 100%;
            height: 100%;
        }

        .install-steps {
            display: grid;
            gap: 10px;
            margin: 4px 0 0;
            padding: 0;
            list-style: none;
            counter-reset: install-step;
        }

        .install-steps li {
            counter-increment: install-step;
            display: grid;
            grid-template-columns: 32px 1fr;
            gap: 10px;
            align-items: start;
            color: #dfe5f5;
            line-height: 1.5;
        }

        .install-steps li::before {
            content: counter(install-step);
            width: 30px;
            height: 30px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(117,230,255,.5);
            border-radius: 9px;
            color: white;
            background: linear-gradient(135deg, #157ca3, #6550d3 58%, #a34093);
            font-weight: 900;
        }

        .info-link-row {
            margin: 22px 0 0;
            display: flex;
            justify-content: center;
        }

        .info-icon-button {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border: 1px solid var(--line-strong);
            border-radius: 999px;
            color: var(--muted);
            background: var(--panel-soft);
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            transition: border-color .14s ease, color .14s ease, transform .14s ease;
        }

        .info-icon-button:hover {
            border-color: var(--primary);
            color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .site-footer {
            margin: 18px 0 0;
            padding: 12px 8px 0;
            color: #9099b2;
            text-align: center;
            font-size: .84rem;
            letter-spacing: .02em;
        }

        .site-footer .heart {
            display: inline-block;
            margin: 0 .18em;
            color: #ff4f91;
        }

        .subtitle {
            margin: 0;
            color: var(--muted);
            font-size: .74rem;
            font-weight: 600;
            line-height: 1.3;
        }

        .subtitle .shine {
            font-style: italic;
            background: linear-gradient(100deg, #cdd4ec 30%, #ffffff 45%, #35e7ff 50%, #cdd4ec 65%);
            background-size: 250% 100%;
            background-position: 0% 0%;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: shine-sweep 4.5s ease-in-out infinite;
        }

        @keyframes shine-sweep {
            0% { background-position: 200% 0%; }
            60%, 100% { background-position: -40% 0%; }
        }

        .board-toolbar {
            padding: 22px 22px 18px;
        }

        .board-heading {
            margin: 0 0 16px;
            color: #fff;
            font-family: var(--font-heading);
            font-size: clamp(1.2rem, 4.2vw, 1.5rem);
            font-weight: 900;
            letter-spacing: .02em;
            text-transform: uppercase;
            text-shadow: none;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 11px;
            align-items: center;
            justify-content: space-between;
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
            padding: 0;
            border: none;
            color: var(--text);
            background: none;
            font-size: .86rem;
        }

        .legend-icon {
            display: block;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            background: currentColor;
        }

        .legend-icon.online { color: var(--online); }
        .legend-icon.late { color: var(--late); }
        .legend-icon.absent { color: var(--absent); }
        .legend-icon.vacation { color: var(--vacation); }
        .legend-icon.open { color: var(--open); }

        .primary-button,
        .secondary-button,
        .danger-button {
            min-height: 44px;
            border-radius: var(--radius-md);
            padding: 10px 16px;
            border: 1px solid;
            cursor: pointer;
            font-size: .92rem;
            font-weight: 700;
            letter-spacing: .01em;
            transition: transform .12s ease, background-color .14s ease, border-color .14s ease;
        }

        .primary-button {
            color: #fff;
            border-color: var(--primary);
            background: var(--primary);
        }

        .primary-button:hover { background: var(--primary-hover); border-color: var(--primary-hover); }

        body[data-theme="summer"] .primary-button,
        body[data-theme="winter"] .primary-button { color: #0a0b0e; }

        .secondary-button {
            color: var(--text);
            border-color: var(--line-strong);
            background: var(--panel-soft);
        }

        .secondary-button:hover { border-color: var(--primary); color: var(--primary-hover); }

        .danger-button {
            color: var(--red);
            border-color: rgba(239, 91, 106, .4);
            background: rgba(239, 91, 106, .08);
        }

        .danger-button:hover { background: rgba(239, 91, 106, .16); border-color: var(--red); }

        .primary-button:hover,
        .secondary-button:hover,
        .danger-button:hover {
            transform: translateY(-1px);
        }

        .primary-button:active,
        .secondary-button:active,
        .danger-button:active { transform: translateY(1px); }

        .blog-teaser {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            padding: 12px 16px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background-color: rgba(var(--panel-deep-rgb), .62);
            background-image: var(--sheen);
            box-shadow: var(--shadow);
            color: var(--text);
            text-decoration: none;
            transition: border-color .14s ease, transform .14s ease;
        }

        .blog-teaser:hover {
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        .blog-teaser-label {
            flex: none;
            padding: 3px 10px;
            border: 1px solid rgba(124, 92, 255, .4);
            border-radius: 999px;
            background-color: rgba(124, 92, 255, .12);
            background-image: var(--sheen);
            color: var(--primary-hover);
            font-size: .64rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .blog-teaser-title {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-weight: 700;
            font-size: .92rem;
        }

        .blog-teaser-meta {
            flex: none;
            margin-left: auto;
            color: var(--muted);
            font-size: .74rem;
            white-space: nowrap;
        }

        .blog-teaser-arrow {
            flex: none;
            color: var(--primary-hover);
            font-size: 1rem;
        }

        .board {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: rgba(var(--panel-deep-rgb), .5);
            box-shadow: var(--shadow);
        }

        .board::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 1px;
            z-index: 6;
            background: var(--line-strong);
        }

        .board::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 7;
            display: none;
            pointer-events: none;
        }

        body[data-theme="summer"] .board::after {
            display: block;
            opacity: .42;
            background:
                radial-gradient(34px 12px at 12% 100%, rgba(255, 219, 130, .6) 0 60%, transparent 61%),
                radial-gradient(26px 9px at 32% 100%, rgba(255, 219, 130, .42) 0 60%, transparent 61%),
                radial-gradient(40px 13px at 72% 100%, rgba(255, 219, 130, .52) 0 60%, transparent 61%),
                linear-gradient(180deg, transparent 0 88%, rgba(55, 212, 196, .1) 89% 100%);
        }

        body[data-theme="winter"] .board::after {
            display: block;
            opacity: .66;
            background:
                radial-gradient(36px 10px at 8% 0, rgba(255,255,255,.9) 0 62%, transparent 63%),
                radial-gradient(54px 14px at 22% 0, rgba(226,245,255,.9) 0 62%, transparent 63%),
                radial-gradient(38px 10px at 39% 0, rgba(255,255,255,.88) 0 62%, transparent 63%),
                radial-gradient(58px 15px at 61% 0, rgba(226,245,255,.86) 0 62%, transparent 63%),
                radial-gradient(42px 11px at 82% 0, rgba(255,255,255,.88) 0 62%, transparent 63%),
                linear-gradient(180deg, rgba(244, 251, 255, .13) 0 16px, transparent 17px 100%);
        }

        .table-scroll {
            overflow-x: auto;
            overscroll-behavior-inline: contain;
            scrollbar-color: var(--line-strong) transparent;
        }

        .table-scroll::-webkit-scrollbar { height: 8px; }
        .table-scroll::-webkit-scrollbar-track { background: transparent; }
        .table-scroll::-webkit-scrollbar-thumb {
            border-radius: 999px;
            background: var(--line-strong);
        }

        table {
            width: 100%;
            min-width: 420px;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
        }

        th,
        td {
            border-right: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
            text-align: center;
            vertical-align: middle;
        }

        thead th {
            height: 46px;
            width: auto;
            min-width: 74px;
            padding: 4px;
            color: var(--text);
            background: rgba(var(--panel-soft-rgb), .9);
        }

        thead th.player-heading {
            width: 150px;
            min-width: 150px;
        }

        thead th.is-today {
            background: rgba(var(--panel-soft-rgb), .9);
            box-shadow: inset 0 0 0 1px var(--primary);
        }

        body[data-theme="summer"] thead th.is-today,
        body[data-theme="winter"] thead th.is-today { box-shadow: inset 0 0 0 1px var(--primary); }

        tbody td,
        tbody th {
            height: 44px;
            background: rgba(var(--panel-rgb), .9);
        }

        tbody tr:nth-child(even) td,
        tbody tr:nth-child(even) th {
            background: rgba(var(--panel-strong-rgb), .9);
        }

        tbody tr:hover td,
        tbody tr:hover th {
            background-color: var(--panel-soft);
        }

        tbody tr td.is-today {
            background-color: rgba(124, 92, 255, .1);
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
            width: 150px;
            min-width: 150px;
        }

        .player-heading {
            z-index: 5;
            padding-left: 8px;
            color: var(--text);
            text-align: left;
        }

        .player-cell {
            padding: 4px;
            background: rgba(var(--panel-strong-rgb), .9) !important;
        }

        .player-button {
            width: 100%;
            min-height: 36px;
            padding: 6px 8px;
            border: 1px solid var(--line-strong);
            border-radius: var(--radius-sm);
            color: var(--text);
            background: var(--panel-soft);
            cursor: pointer;
            font-weight: 700;
            line-height: 1.15;
            font-size: .82rem;
            transition: border-color .14s ease, transform .14s ease;
        }

        .player-button:hover {
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        .player-button small {
            display: none;
        }

        .player-name {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .date-day {
            display: block;
            color: var(--text);
            font-size: .78rem;
            font-weight: 800;
        }

        thead th.is-today .date-day { color: var(--primary-hover); }

        .date-value {
            display: block;
            margin-top: 1px;
            color: var(--muted);
            font-size: .62rem;
            font-weight: 600;
        }

        .today-tag {
            display: none;
            margin-top: 2px;
            padding: 0 5px;
            border-radius: 999px;
            color: #fff;
            background-color: var(--primary);
            background-image: var(--sheen);
            font-size: .5rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        thead th.is-today .today-tag { display: inline-block; }

        .past-tag {
            display: inline-block;
            margin-top: 3px;
            padding: 1px 5px;
            border: 1px solid var(--line-strong);
            border-radius: 999px;
            color: var(--muted);
            background-color: var(--panel);
            background-image: var(--sheen);
            font-size: .52rem;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .date-remove {
            width: 16px;
            height: 16px;
            margin: 3px auto 0;
            display: grid;
            place-items: center;
            border: 1px solid rgba(239, 91, 106, .4);
            border-radius: var(--radius-sm);
            color: var(--red);
            background: rgba(239, 91, 106, .1);
            cursor: pointer;
            font-size: .66rem;
            line-height: 1;
        }

        .date-remove:hover {
            color: #fff;
            border-color: var(--red);
            background: rgba(239, 91, 106, .32);
        }

        .status-cell { padding: 3px; }

        .status-button {
            position: relative;
            width: 100%;
            min-height: 38px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1px;
            padding: 5px 6px;
            border: 1px solid;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: transform .12s ease;
        }

        .status-button:hover {
            transform: translateY(-1px);
        }

        .status-badge {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            line-height: 1.25;
        }

        .status-badge-main {
            font-size: .68rem;
            font-weight: 800;
            letter-spacing: .01em;
        }

        .status-badge-game {
            color: var(--gold);
            font-size: .58rem;
            font-weight: 600;
        }

        .status-badge-note {
            color: var(--muted);
            font-size: .58rem;
            font-weight: 600;
        }

        .status-button.online {
            color: var(--online);
            border-color: rgba(69, 212, 131, .4);
            background-color: rgba(69, 212, 131, .1);
            background-image: var(--sheen);
        }

        .status-button.late {
            color: var(--late);
            border-color: rgba(242, 181, 68, .4);
            background-color: rgba(242, 181, 68, .1);
            background-image: var(--sheen);
        }

        .status-button.absent {
            color: var(--absent);
            border-color: rgba(239, 91, 106, .4);
            background-color: rgba(239, 91, 106, .1);
            background-image: var(--sheen);
        }

        .status-button.vacation {
            color: var(--primary-hover);
            border-color: rgba(124, 92, 255, .4);
            background-color: rgba(124, 92, 255, .1);
            background-image: var(--sheen);
        }

        .status-button.open {
            color: var(--muted);
            border-color: var(--line-strong);
            background-color: rgba(var(--panel-soft-rgb), .9);
            background-image: var(--sheen);
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
            text-shadow: none;
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
            border-radius: var(--radius-md);
            color: #fff;
            background: var(--primary);
            font-weight: 800;
        }

        .instruction-item p { margin: 0; }
        .instruction-item strong { color: var(--primary-hover); }

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
            border: 1px solid var(--line-strong);
            border-radius: var(--radius-lg);
            color: var(--text);
            background: var(--panel-strong);
            box-shadow: 0 24px 60px rgba(0,0,0,.5);
        }

        dialog::backdrop {
            background: rgba(4,5,8,.72);
            backdrop-filter: blur(4px);
        }

        .modal-content { padding: 21px; }

        .modal-title {
            margin: 0;
            color: #fff;
            font-family: var(--font-heading);
            font-size: 1.18rem;
            font-weight: 900;
            letter-spacing: .02em;
            text-transform: uppercase;
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

        .status-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 9px;
            margin-bottom: 16px;
        }

        .status-choice {
            min-height: 70px;
            border: 1px solid var(--line-strong);
            border-radius: var(--radius-md);
            color: var(--text);
            background: var(--panel-soft);
            cursor: pointer;
            font-weight: 700;
            transition: transform .14s ease, border-color .14s ease, background-color .14s ease;
        }

        .status-choice:hover { border-color: var(--primary); transform: translateY(-1px); }

        .status-choice.selected {
            border-color: var(--primary);
            background: rgba(124, 92, 255, .12);
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
            border: 1px solid var(--line-strong);
            border-radius: var(--radius-md);
            color: #fff;
            background: var(--panel-strong);
            box-shadow: 0 15px 40px rgba(0,0,0,.6);
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
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: var(--panel-soft);
        }

        .discord-note::after {
            content: "";
            display: none;
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

        .achievements {
            position: relative;
            overflow: hidden;
            margin-top: 19px;
            padding: 22px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            color: var(--text);
            background: var(--panel-deep);
            box-shadow: var(--shadow);
        }

        .achievements::before {
            content: "";
            display: none;
        }

        .achievements-head {
            position: relative;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            margin: 0 0 16px;
        }

        .achievements-head h2 {
            margin: 0;
            color: #fff;
            font-size: clamp(1.45rem, 4vw, 1.85rem);
            text-shadow: none;
        }

        .achievements-nav {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 10px;
            border: 1px solid rgba(139,151,190,.24);
            border-radius: 999px;
            background: rgba(10,13,24,.7);
        }

        .nav-arrow {
            width: 26px;
            height: 26px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(139,151,190,.3);
            border-radius: 8px;
            color: var(--text);
            background: rgba(21,26,45,.7);
            cursor: pointer;
            line-height: 1;
            transition: border-color .14s ease, transform .14s ease;
        }

        .nav-arrow:hover { border-color: var(--primary); transform: translateY(-1px); }
        .nav-arrow:disabled { opacity: .35; cursor: default; transform: none; }

        .achievements-nav-label {
            color: var(--muted);
            font-size: .82rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .achievement-grid {
            position: relative;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .achievement-card {
            padding: 16px;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: var(--panel);
            transition: transform .14s ease, border-color .14s ease, background-color .14s ease;
        }

        .achievement-card:hover {
            transform: translateY(-2px);
            border-color: var(--line-strong);
            background: var(--panel-soft);
        }

        .achievement-card-head {
            display: flex;
            align-items: center;
            gap: 11px;
            margin-bottom: 14px;
        }

        .achievement-card-head-text { flex: 1; min-width: 0; }

        .achievement-edit-button {
            flex: none;
            width: 30px;
            height: 30px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(139,151,190,.3);
            border-radius: 4px;
            background: rgba(21,26,45,.7);
            color: var(--text);
            cursor: pointer;
            transition: border-color .14s ease, color .14s ease;
        }

        .achievement-edit-button:hover { border-color: var(--primary); color: var(--primary-hover); }

        .achievement-edit-rows {
            display: grid;
            gap: 8px;
            margin-bottom: 12px;
        }

        .achievement-edit-row {
            display: grid;
            grid-template-columns: minmax(0,1fr) minmax(0,1.3fr) auto;
            gap: 8px;
        }

        .achievement-edit-row input {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid rgba(139,151,190,.28);
            border-radius: 8px;
            background: rgba(10,13,24,.65);
            color: var(--text);
            font: inherit;
        }

        .achievement-edit-row input:focus { outline: none; border-color: var(--primary); }

        .achievement-edit-row button {
            width: 34px;
            border: 1px solid rgba(255,107,133,.35);
            border-radius: 8px;
            background: rgba(58,15,23,.5);
            color: #ff9caf;
            cursor: pointer;
            font-size: 1rem;
        }

        .achievement-edit-row button:hover { background: rgba(90,20,32,.7); }

        .achievement-icon {
            width: 40px;
            height: 40px;
            flex: none;
            display: grid;
            place-items: center;
            border-radius: var(--radius-md);
            font-size: 1.15rem;
        }

        .achievement-icon.wow { background: linear-gradient(135deg, #157ca3, #6550d3 58%, #a34093); color: rgba(53,231,255,.5); }
        .achievement-icon.d4 { background: linear-gradient(135deg, #7a1f24, #b83a52 58%, #d93d55); color: rgba(255,83,104,.5); }
        .achievement-icon.hots { background: linear-gradient(135deg, #1f6e7a, #35a3a0 58%, #6be0c8); color: rgba(107,224,200,.5); }
        .achievement-icon.rocket_league { background: linear-gradient(135deg, #16408f, #3a6ea5 58%, #ffb454); color: rgba(255,180,84,.5); }

        .achievement-card-head h3 {
            margin: 0;
            color: #fff;
            font-family: var(--font-heading);
            font-size: .88rem;
            font-weight: 900;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        .achievement-source {
            margin: 3px 0 0;
            color: var(--muted);
            font-size: .78rem;
        }

        .mplus-bars {
            display: grid;
            gap: 9px;
        }

        .mplus-bar-row {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 8px;
        }

        .mplus-bar-label {
            grid-column: 1 / -1;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 2px 8px;
            color: var(--text);
            font-size: .84rem;
        }

        .mplus-bar-label a {
            color: var(--primary-hover);
            font-weight: 700;
            text-decoration: none;
        }

        .mplus-bar-label a:hover { text-decoration: underline; }

        .mplus-bar-label b {
            color: var(--muted);
            background-color: var(--panel-soft);
            background-image: var(--sheen);
            border: 1px solid var(--line-strong);
            border-radius: var(--radius-sm);
            padding: 1px 7px;
            font-size: .78rem;
        }

        .mplus-bar-label b.top-tier {
            color: var(--gold);
            background-color: rgba(242, 181, 68, .12);
            background-image: var(--sheen);
            border-color: rgba(242, 181, 68, .3);
        }

        .mplus-bar-track {
            grid-column: 1 / -1;
            height: 6px;
            border-radius: 999px;
            background: var(--panel-soft);
            overflow: hidden;
        }

        .mplus-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--primary), var(--primary-hover));
        }

        .mplus-bar-fill.top-tier {
            background: linear-gradient(90deg, var(--primary), var(--gold));
        }

        .d4-milestones {
            display: grid;
            gap: 9px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .d4-milestones li {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 4px 10px;
            padding: 9px 11px;
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            background: var(--panel-soft);
            font-size: .88rem;
        }

        .d4-milestones li span:first-child { color: var(--muted); }
        .d4-milestones li span:last-child { color: var(--gold); font-weight: 700; text-align: right; }

        .achievement-links {
            display: grid;
            gap: 9px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .achievement-links li {
            border: 1px solid var(--line);
            border-radius: var(--radius-md);
            background: var(--panel-soft);
            overflow: hidden;
        }

        .achievement-links li a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            color: var(--primary-hover);
            font-weight: 700;
            font-size: .88rem;
            text-decoration: none;
            transition: background-color .14s ease, border-color .14s ease;
        }

        .achievement-links li a::after {
            content: '↗';
            margin-left: auto;
            color: var(--muted);
        }

        .achievement-links li a:hover {
            background: rgba(124, 92, 255, .08);
        }

        .achievement-updated {
            margin: 12px 0 0;
            color: var(--muted);
            font-size: .74rem;
        }

        .achievement-updated .mock-tag {
            display: inline-block;
            margin-left: 6px;
            padding: 1px 7px;
            border: 1px solid rgba(255,173,66,.4);
            border-radius: 999px;
            color: #ffd28d;
            background-image: var(--sheen);
            font-size: .68rem;
            letter-spacing: .03em;
            text-transform: uppercase;
        }

        .widget-loading,
        .widget-empty {
            padding: 14px 10px;
            color: var(--muted);
            font-size: .88rem;
            text-align: center;
        }

        .storage-warning {
            margin: 14px 0;
            padding: 13px 15px;
            border: 1px solid rgba(255,173,66,.52);
            border-radius: 11px;
            color: #ffe0ac;
            background: rgba(70,43,13,.76);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.055);
            line-height: 1.48;
        }

        .storage-warning strong { color: #fff0ce; }
        .storage-warning code { color: #fff; font-weight: 800; }

        [hidden] { display: none !important; }

        .account-strip {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 18px 0 0;
            padding: 13px 15px;
            border: 1px solid rgba(135,151,197,.25);
            border-radius: 14px;
            background: rgba(10,13,24,.56);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.04);
            backdrop-filter: blur(12px);
        }

        .account-summary {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 10px;
            align-items: center;
            min-width: 0;
            color: #cdd4e8;
            line-height: 1.38;
        }

        .account-summary-text { min-width: 0; }
        .account-summary strong { color: #fff; }
        .account-summary small { display: block; margin-top: 2px; color: var(--muted); }

        .avatar,
        .avatar-placeholder {
            flex: 0 0 auto;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 1px solid var(--line-strong);
            object-fit: cover;
            background: var(--panel-soft);
        }

        .avatar-placeholder {
            display: inline-grid;
            place-items: center;
            color: var(--primary-hover);
            font-size: .78rem;
            font-weight: 800;
        }

        .account-avatar {
            width: 42px;
            height: 42px;
        }

        .player-title {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 5px;
            min-width: 0;
            overflow: hidden;
        }

        .player-title .avatar,
        .player-title .avatar-placeholder {
            width: 24px;
            height: 24px;
        }

        .player-name {
            min-width: 0;
        }

        .avatar-upload-row {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 12px;
            align-items: center;
        }

        .avatar-preview {
            width: 50px;
            height: 50px;
        }

        input[type="file"] {
            width: 100%;
            min-height: 47px;
            padding: 10px 12px;
            border: 1px solid rgba(135,151,197,.38);
            border-radius: 10px;
            color: #dbe6ff;
            background: #090d19;
        }

        .account-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        .compact-button {
            min-height: 39px;
            padding: 8px 12px;
            border: 1px solid rgba(135,151,197,.34);
            border-radius: 10px;
            color: #edf2ff;
            background: linear-gradient(180deg, rgba(34,42,68,.94), rgba(17,22,38,.96));
            cursor: pointer;
            font-size: .92rem;
            font-weight: 800;
        }

        .compact-button:hover {
            border-color: var(--primary);
        }

        .account-badge,
        .player-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-left: 5px;
            padding: 2px 7px;
            border: 1px solid rgba(124, 92, 255, .35);
            border-radius: 999px;
            color: var(--primary-hover);
            background-color: rgba(124, 92, 255, .12);
            background-image: var(--sheen);
            font-size: .68rem;
            font-weight: 800;
            vertical-align: middle;
        }

        .account-badge.admin,
        .player-badge.admin {
            border-color: rgba(242, 181, 68, .4);
            color: var(--gold);
            background-color: rgba(242, 181, 68, .12);
            background-image: var(--sheen);
        }

        .setup-callout,
        .password-callout {
            margin: 14px 0;
            padding: 13px 15px;
            border: 1px solid rgba(124, 92, 255, .35);
            border-radius: var(--radius-md);
            color: var(--text);
            background: rgba(124, 92, 255, .1);
            line-height: 1.5;
        }

        .password-callout {
            border-color: rgba(242, 181, 68, .4);
            color: var(--text);
            background: rgba(242, 181, 68, .1);
        }

        .status-button:disabled,
        .player-button.readonly {
            cursor: default;
        }

        .status-button:disabled:hover,
        .player-button.readonly:hover {
            filter: none;
            transform: none;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.08), 0 6px 15px rgba(0,0,0,.24);
        }

        .status-button:disabled { opacity: .86; }

        .recurring-tag {
            position: absolute;
            z-index: 1;
            top: -4px;
            right: -4px;
            padding: 0 4px;
            border: 1px solid rgba(255,255,255,.22);
            border-radius: 999px;
            color: rgba(255,255,255,.82);
            background-color: var(--panel-strong);
            background-image: var(--sheen);
            font-size: .46rem;
            font-weight: 900;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        dialog.wide { width: min(760px, calc(100% - 24px)); }

        .form-stack { display: grid; gap: 15px; }
        .form-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .field-help { margin: -2px 0 0; color: #909ab4; font-size: .84rem; line-height: 1.42; }

        .field-sublabel {
            margin: 0 0 5px;
            color: var(--muted);
            font-size: .78rem;
            font-weight: 600;
        }

        .vacation-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            margin: 10px 0 8px;
        }

        .vacation-actions > button { flex: 1 1 160px; }

        .password-checklist {
            display: grid;
            gap: 5px;
            margin: -2px 0 0;
            padding: 0;
            list-style: none;
        }

        .password-checklist li {
            position: relative;
            padding-left: 22px;
            color: var(--muted);
            font-size: .82rem;
            line-height: 1.4;
            transition: color .15s ease;
        }

        .password-checklist li::before {
            content: "";
            position: absolute;
            left: 0;
            top: 3px;
            width: 14px;
            height: 14px;
            border: 1px solid var(--line-strong);
            border-radius: 50%;
            background: var(--panel-soft);
        }

        .password-checklist li.met {
            color: var(--online);
        }

        .password-checklist li.met::before {
            content: "✓";
            display: flex;
            align-items: center;
            justify-content: center;
            border-color: var(--online);
            background: var(--online);
            color: #04150c;
            font-size: .68rem;
            font-weight: 900;
        }

        .password-checklist li.unmet {
            color: var(--red);
        }

        .password-checklist li.unmet::before {
            border-color: var(--red);
        }

        input.field-invalid {
            border-color: var(--red) !important;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 9px;
            margin: -4px 0 0;
            color: #c7cce0;
            font-size: .9rem;
            cursor: pointer;
        }

        .remember-row input[type="checkbox"] {
            width: 17px;
            height: 17px;
            accent-color: #35e7ff;
            cursor: pointer;
        }
        .section-heading { margin: 5px 0 0; color: #a9ecff; font-size: 1.02rem; }

        input[type="text"],
        input[type="password"],
        input[type="date"],
        select,
        textarea {
            width: 100%;
            min-height: 47px;
            padding: 10px 12px;
            border: 1px solid var(--line-strong);
            border-radius: var(--radius-md);
            outline: none;
            color: var(--text);
            background: var(--panel-soft);
        }

        select {
            color-scheme: dark;
            cursor: pointer;
        }

        textarea { min-height: 104px; resize: vertical; }

        input::placeholder,
        textarea::placeholder { color: var(--muted); }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(124, 92, 255, .16);
        }

        .weekday-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
        }

        .weekday-choice {
            position: relative;
            display: block;
            margin: 0;
        }

        .weekday-choice input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .weekday-choice span {
            min-height: 42px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(135,151,197,.3);
            border-radius: 10px;
            color: #bdc5da;
            background: rgba(20,25,43,.72);
            cursor: pointer;
            font-size: .84rem;
            font-weight: 900;
        }

        .weekday-choice input:checked + span {
            border-color: var(--primary);
            color: #fff;
            background: var(--primary);
        }

        .admin-toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 9px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 13px;
        }

        .admin-user-list {
            display: grid;
            gap: 9px;
            max-height: 330px;
            overflow: auto;
            padding-right: 3px;
        }

        .admin-user-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px;
            border: 1px solid rgba(135,151,197,.22);
            border-radius: 11px;
            background: rgba(19,24,42,.72);
        }

        .admin-user-card strong { color: #fff; }
        .admin-user-card small { display: block; margin-top: 3px; color: var(--muted); }
        .admin-user-card button { flex: 0 0 auto; }

        .separator {
            height: 1px;
            margin: 20px 0;
            background: rgba(135,151,197,.2);
        }

        .forced-password-note {
            margin: 0 0 15px;
            padding: 11px 12px;
            border: 1px solid rgba(255,173,66,.4);
            border-radius: 10px;
            color: #ffe0a8;
            background: rgba(80,46,11,.48);
            line-height: 1.45;
        }


        @media (max-width: 680px) {
            .page-shell { padding: calc(10px + env(safe-area-inset-top)) calc(8px + env(safe-area-inset-right)) calc(30px + env(safe-area-inset-bottom)) calc(8px + env(safe-area-inset-left)); }
            .masthead { padding: 14px 12px; border-radius: 12px; }
            .blog-teaser { flex-wrap: wrap; gap: 6px 10px; padding: 11px 13px; }
            .blog-teaser-title { flex: 1 1 100%; white-space: normal; }
            .blog-teaser-meta { margin-left: 0; }
            .blog-teaser-arrow { margin-left: auto; }
            .board-toolbar { padding: 16px 14px 14px; }
            .masthead-row { flex-wrap: wrap; row-gap: 10px; }
            .brand { order: 1; }
            .install-app-button { order: 2; }
            .main-nav { order: 3; flex: 1 1 100%; margin: 0; gap: 16px; }
            .brand-logo { width: 32px; height: 32px; }
            .brand-name { font-size: 1.1rem; }
            .subtitle { font-size: .66rem; }
            .nav-link { font-size: .84rem; }
            .subtitle-group { margin-top: 12px; gap: 5px; }
            .account-strip { align-items: stretch; padding: 12px; }
            .account-actions { width: 100%; justify-content: stretch; }
            .account-actions > button { flex: 1 1 130px; }
            .account-summary { width: 100%; }
            .form-row { grid-template-columns: 1fr; }
            .weekday-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .admin-user-card { align-items: flex-start; flex-wrap: wrap; }
            .toolbar { align-items: stretch; }
            .toolbar-actions { width: 100%; }
            .toolbar-actions > button { flex: 1 1 190px; }
            .legend { width: 100%; gap: 6px; }
            .legend-item { flex: 1 1 calc(50% - 6px); justify-content: flex-start; }
            table { min-width: 400px; }
            thead th { width: auto; min-width: 58px; }
            .player-heading, .player-cell,
            thead th.player-heading { width: 118px; min-width: 118px; }
            .player-heading { padding-left: 6px; }
            .player-title { gap: 4px; }
            .player-title .avatar,
            .player-title .avatar-placeholder { width: 19px; height: 19px; }
            .status-button { min-height: 34px; }
            .modal-actions > button { flex: 1 1 120px; }
            .modal-actions .danger-button { margin-right: 0; }
            .instruction-grid { grid-template-columns: 1fr; gap: 9px; }
            .instruction-item { grid-template-columns: 36px 1fr; padding: 12px; font-size: 1.04rem; }
            .editing-note { padding-inline: 4px; font-size: 1.04rem; }
            .achievements { padding: 17px 13px; border-radius: 8px; }
            .achievement-grid { grid-template-columns: 1fr; gap: 10px; }
            .achievements-head { flex-direction: column; align-items: flex-start; }
            .achievement-edit-row { grid-template-columns: 1fr; }
            .achievement-edit-row button { justify-self: end; width: 34px; }
        }

        @media (max-width: 480px) {
            body { background-attachment: scroll; }
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

        .pull-refresh {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 40;
            display: flex;
            justify-content: center;
            padding-top: max(10px, env(safe-area-inset-top));
            pointer-events: none;
            opacity: 0;
            transition: opacity .18s ease;
        }

        .pull-refresh.pull-refresh-visible { opacity: 1; }

        .pull-refresh-badge {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border: 1px solid rgba(126, 229, 255, .48);
            border-radius: 50%;
            background: rgba(9, 14, 28, .86);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.1);
        }

        .pull-refresh-arrow {
            display: inline-block;
            font-size: 18px;
            line-height: 1;
            color: var(--cyan);
            transition: transform .05s linear;
        }

        .pull-refresh-ready .pull-refresh-badge {
            border-color: rgba(62, 231, 143, .6);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.14);
        }

        .pull-refresh-ready .pull-refresh-arrow { color: var(--green); }

        .pull-refresh-loading .pull-refresh-arrow {
            animation: pullRefreshSpin .8s linear infinite;
        }

        @keyframes pullRefreshSpin {
            to { transform: rotate(360deg); }
        }

        @media (prefers-reduced-motion: reduce) {
            .pull-refresh-loading .pull-refresh-arrow { animation: none; }
        }
    </style>
