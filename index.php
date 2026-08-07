<?php
declare(strict_types=1);

date_default_timezone_set('Europe/Berlin');
$appVersion = trim((string) @file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'VERSION'));
if ($appVersion === '') {
    $appVersion = '2.6.2';
}

/**
 * Liest Bilddateien (jpg/jpeg/png/webp) aus einem assets/backgrounds/*-Ordner.
 * $random = true: zufälliges Bild bei jedem Aufruf (Standard-Theme).
 * $random = false: immer dieselbe (alphabetisch erste) Datei (Sommer/Winter).
 * Gibt einen relativen URL-Pfad zurück, oder null, falls der Ordner leer ist.
 */
function pickBackgroundImage(string $folder, bool $random): ?string
{
    $directory = __DIR__ . '/assets/backgrounds/' . $folder;
    if (!is_dir($directory)) {
        return null;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $candidates = [];
    foreach (scandir($directory) ?: [] as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $extension = strtolower((string) pathinfo($entry, PATHINFO_EXTENSION));
        if (in_array($extension, $allowedExtensions, true) && is_file($directory . '/' . $entry)) {
            $candidates[] = $entry;
        }
    }

    if ($candidates === []) {
        return null;
    }

    sort($candidates, SORT_STRING);
    $chosen = $random ? $candidates[random_int(0, count($candidates) - 1)] : $candidates[0];

    return 'assets/backgrounds/' . $folder . '/' . rawurlencode($chosen);
}

$backgroundImages = [
    'default' => pickBackgroundImage('default', true),
    'summer' => pickBackgroundImage('summer', false),
    'winter' => pickBackgroundImage('winter', false),
];

$cspNonce = base64_encode(random_bytes(16));
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=()');
header(
    "Content-Security-Policy: default-src 'self'; "
    . "script-src 'self' 'nonce-{$cspNonce}' https://cdn.tailwindcss.com; "
    . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
    . "img-src 'self' data:; "
    . "font-src 'self' https://fonts.gstatic.com; "
    . "connect-src 'self'; "
    . "worker-src 'self'; "
    . "manifest-src 'self'; "
    . "frame-ancestors 'none'; "
    . "base-uri 'self'; "
    . "form-action 'self'; "
    . "object-src 'none'"
);
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" id="themeColorMeta" content="#070914">
    <meta name="application-name" content="Kellerkinder">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Kellerkinder">
    <title>Kellerkinder</title>
    <meta name="description" content="Der gemeinsame Kellerkinder-Online-Kalender für eure Spieltage.">
    <link rel="icon" href="assets/kellerkinder-logo.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com" nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>"></script>
    <script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: '#0a0b0e', panel: '#14151b', panel2: '#1a1c24',
                        line: '#262935', muted: '#8d92a3',
                        primary: '#7c5cff', primaryhi: '#9478ff', gold: '#f2b544',
                        positive: '#45d483', negative: '#ef5b6a',
                    },
                    borderRadius: { card: '16px', control: '10px' },
                }
            }
        };
    </script>
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
            'default' => ['6, 6, 6', '.28'],
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
            padding: 6px 11px;
            border: 1px solid var(--line);
            border-radius: 999px;
            color: var(--text);
            background-color: var(--panel-soft);
            background-image: var(--sheen);
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

        .board {
            position: relative;
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            background: var(--panel-deep);
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
            background: var(--panel-soft);
        }

        thead th.player-heading {
            width: 150px;
            min-width: 150px;
        }

        thead th.is-today {
            background: var(--panel-soft);
            box-shadow: inset 0 0 0 1px var(--primary);
        }

        body[data-theme="summer"] thead th.is-today,
        body[data-theme="winter"] thead th.is-today { box-shadow: inset 0 0 0 1px var(--primary); }

        tbody td,
        tbody th {
            height: 44px;
            background: var(--panel);
        }

        tbody tr:nth-child(even) td,
        tbody tr:nth-child(even) th {
            background: var(--panel-strong);
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
            background: var(--panel-strong) !important;
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
            overflow: hidden;
            width: 100%;
            min-height: 38px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1px;
            padding: 3px 4px;
            border: 1px solid;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: transform .12s ease, background-color .14s ease, border-color .14s ease;
        }

        .status-button:hover {
            transform: translateY(-1px);
        }

        .status-button .icon-row {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .status-button .icon {
            font-size: .82rem;
            line-height: 1;
        }

        .status-button .label {
            font-size: .64rem;
            font-weight: 800;
            letter-spacing: .01em;
            line-height: 1;
        }

        .status-button .game,
        .status-button .note {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            line-height: 1.15;
        }

        .status-button .game {
            color: var(--gold);
            font-size: .54rem;
            font-weight: 700;
        }

        .status-button .note {
            color: var(--muted);
            font-size: .52rem;
        }

        .status-button.online {
            color: var(--online);
            border-color: rgba(69, 212, 131, .35);
            background-color: rgba(69, 212, 131, .1);
            background-image: var(--sheen);
        }

        .status-button.late {
            color: var(--late);
            border-color: rgba(242, 181, 68, .35);
            background-color: rgba(242, 181, 68, .1);
            background-image: var(--sheen);
        }

        .status-button.absent {
            color: var(--absent);
            border-color: rgba(239, 91, 106, .35);
            background-color: rgba(239, 91, 106, .1);
            background-image: var(--sheen);
        }

        .status-button.vacation {
            color: var(--primary-hover);
            border-color: rgba(124, 92, 255, .35);
            background-color: rgba(124, 92, 255, .1);
            background-image: var(--sheen);
        }

        .status-button.open {
            color: var(--muted);
            border-color: var(--line);
            background-color: var(--panel-soft);
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
            .legend-item { flex: 1 1 calc(50% - 6px); justify-content: center; padding: 6px 8px; }
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
</head>
<body data-theme="default">
<div id="pullRefresh" class="pull-refresh" aria-hidden="true">
    <span class="pull-refresh-badge"><span class="pull-refresh-arrow">↓</span></span>
</div>
<div class="season-scene" aria-hidden="true">
    <div class="summer-sun"></div>
    <div class="summer-bubbles"></div>
    <div class="summer-waves"></div>
    <div class="winter-snow"></div>
    <div class="winter-aurora left"></div>
    <div class="winter-aurora right"></div>
    <div class="winter-ember-glow"></div>
    <div class="winter-snowbank"></div>
</div>
<main class="page-shell">
    <header class="masthead">
        <div class="masthead-row">
            <span class="brand">
                <img src="assets/kellerkinder-logo.svg" alt="" class="brand-logo">
                <span class="brand-text">
                    <span class="brand-name-row">
                        <span class="brand-name">Kellerkinder</span>
                        <span class="brand-sun" aria-hidden="true">☀️💦</span>
                    </span>
                    <span class="subtitle">Online-Gaming mit Freunden seit <em class="shine">ewig</em></span>
                </span>
            </span>
            <nav class="main-nav" aria-label="Hauptnavigation">
                <a href="#" class="nav-link active">Kalender</a>
                <span class="nav-link disabled">Tagebuch <small>(folgt)</small></span>
                <span class="nav-link disabled">Netzje <small>(folgt)</small></span>
            </nav>
            <button class="install-app-button" id="installAppButton" type="button" title="Als App zum Home-Bildschirm hinzufügen" aria-label="Kellerkinder-Kalender als App zum Home-Bildschirm hinzufügen">
                <img src="assets/smartphone-install.svg" alt="">
            </button>
        </div>
    </header>

    <section class="account-strip" aria-label="Benutzerkonto">
        <div class="account-summary" id="accountSummary">
            <strong>Wird geladen …</strong>
            <small>Die Übersicht ist öffentlich sichtbar. Änderungen erfordern ein Benutzerkonto.</small>
        </div>
        <div class="account-actions">
            <button class="compact-button" id="loginButton" type="button">Anmelden</button>
            <button class="compact-button" id="registerButton" type="button">Account anlegen</button>
            <button class="compact-button" id="profileButton" type="button" hidden>Mein Account</button>
            <button class="compact-button" id="adminButton" type="button" hidden>Adminbereich</button>
            <button class="compact-button" id="logoutButton" type="button" hidden>Abmelden</button>
        </div>
    </section>

    <div id="setupCallout" class="setup-callout" hidden>
        <strong>Ersteinrichtung:</strong> Es existiert noch kein Benutzerkonto. Der erste registrierte Account wird automatisch als Administrator eingetragen.
    </div>

    <div id="passwordCallout" class="password-callout" hidden>
        <strong>Passwortänderung erforderlich:</strong> Ein Administrator hat dein Passwort geändert. Lege jetzt ein eigenes neues Passwort fest.
    </div>

    <div id="storageWarning" class="storage-warning" hidden>
        <strong>Nur-Lese-Modus:</strong> Der Plan ist sichtbar, Änderungen können aber nicht gespeichert werden.
        Gib dem Ordner <code>data</code> Schreibrechte für den PHP-Webserver.
    </div>

    <section class="board" aria-label="Verfügbarkeitsplan">
        <div class="board-toolbar">
            <h2 class="board-heading">Online-Kalender</h2>
            <div class="toolbar" aria-label="Steuerung und Legende">
                <div class="legend" aria-label="Status-Legende">
                    <span class="legend-item"><span class="legend-icon online"></span> Online</span>
                    <span class="legend-item"><span class="legend-icon late"></span> Später</span>
                    <span class="legend-item"><span class="legend-icon absent"></span> Verhindert</span>
                    <span class="legend-item"><span class="legend-icon vacation"></span> Urlaub</span>
                    <span class="legend-item"><span class="legend-icon open"></span> Offen</span>
                </div>
                <div class="toolbar-actions">
                    <button class="secondary-button" id="addDateButton" type="button" hidden>＋ Spieltag hinzufügen</button>
                    <button class="primary-button" id="addPlayerButton" type="button" hidden>＋ Spieler hinzufügen</button>
                </div>
            </div>
        </div>
        <div id="loading" class="loading">Wird geladen …</div>
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

    <section class="achievements" aria-label="Erfolge der Kellerkinder">
        <div class="achievements-head">
            <div class="achievements-nav" id="achievementsNav" aria-label="Spielpaar wechseln">
                <button class="nav-arrow" id="achievementsPrev" type="button" aria-label="Vorheriges Spielpaar" disabled>‹</button>
                <span class="achievements-nav-label" id="achievementsNavLabel">World of Warcraft · Diablo IV</span>
                <button class="nav-arrow" id="achievementsNext" type="button" aria-label="Nächstes Spielpaar" disabled>›</button>
            </div>
        </div>

        <div class="achievement-grid" id="achievementGrid"></div>
    </section>

    <div class="info-link-row">
        <button class="info-icon-button" id="infoButton" type="button" title="Was soll das?" aria-label="Was soll das? Erklärung öffnen">?</button>
    </div>

    <footer class="site-footer">Created by Fischje with <span class="heart" aria-label="Love">♥</span> Version <?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?></footer>
</main>

<dialog id="installDialog">
    <div class="modal-content">
        <h2 class="modal-title">Kalender als App speichern</h2>
        <p class="modal-subtitle" id="installDialogSubtitle">Lege den Kellerkinder-Kalender als Symbol auf deinem Home-Bildschirm ab.</p>
        <ol class="install-steps" id="installSteps"></ol>
        <div class="modal-actions">
            <button class="primary-button" type="button" data-close-dialog="installDialog">Verstanden</button>
        </div>
    </div>
</dialog>

<dialog id="infoDialog" class="wide">
    <div class="modal-content">
        <h2 class="modal-title">So funktioniert der Online-Kalender</h2>
        <div class="instruction-grid">
            <div class="instruction-item">
                <span class="instruction-number">1</span>
                <p><strong>Account anlegen:</strong> Registriere dich mit einem Benutzernamen, einem sicheren Passwort und deinem Spieler- oder Charakternamen. Das Passwort benötigt mindestens acht Zeichen, einen Buchstaben, eine Zahl und ein Sonderzeichen.</p>
            </div>
            <div class="instruction-item">
                <span class="instruction-number">2</span>
                <p><strong>Eigene Verfügbarkeit pflegen:</strong> Nach der Anmeldung kannst du zusätzliche Spieltage anlegen und ausschließlich deine eigene Spielerzeile bearbeiten. Für jeden Termin stehen „Online“, „Später“, „Verhindert“, „Urlaub“ und „Offen“ zur Auswahl. Zusätzlich kannst du angeben, welches Spiel du spielen möchtest und einen kurzen Hinweis ergänzen.</p>
            </div>
            <div class="instruction-item">
                <span class="instruction-number">3</span>
                <p><strong>Feste Wochentage einstellen:</strong> In „Mein Account“ kannst du Wochentage markieren, an denen du normalerweise online bist. Künftige Termine an diesen Tagen werden automatisch als „Online“ vorbelegt und lassen sich einzeln überschreiben.</p>
            </div>
            <div class="instruction-item">
                <span class="instruction-number">4</span>
                <p><strong>Administration:</strong> Administratoren verwalten Benutzerkonten, Spieler, zusätzliche Spieltage und sämtliche Statusangaben. Alle angemeldeten Benutzer dürfen neue Spieltage anlegen; löschen kann sie weiterhin nur ein Administrator. Wird ein Passwort durch einen Admin geändert, muss der Benutzer nach der nächsten Anmeldung ein eigenes neues Passwort festlegen.</p>
            </div>
        </div>
        <p class="editing-note"><strong>Geschützte Bearbeitung:</strong> Die Kalenderübersicht bleibt für alle Besucher sichtbar. Angemeldete Benutzer dürfen zusätzliche Spieltage anlegen und ausschließlich den eigenen Spieler bearbeiten. Administratoren können außerdem Termine löschen und besitzen vollständige Verwaltungsrechte.</p>
        <div class="discord-note">
            <h3>💬 Auch in Discord nutzbar</h3>
            <p>Im Kellerkinder-Discord könnt ihr den aktuellen Kalender jederzeit mit dem Befehl <code>/kalender</code> aufrufen und anzeigen lassen.</p>
        </div>
        <div class="modal-actions">
            <button class="primary-button" type="button" data-close-dialog="infoDialog">Verstanden</button>
        </div>
    </div>
</dialog>

<dialog id="achievementEditDialog" class="wide">
    <div class="modal-content form-stack">
        <h2 class="modal-title" id="achievementEditTitle">Widget bearbeiten</h2>
        <p class="modal-subtitle">Überschrift und Inhalt für dieses Kästchen.</p>

        <div>
            <label for="achievementEditTitleInput">Überschrift</label>
            <input type="text" id="achievementEditTitleInput" maxlength="40" placeholder="Standard-Name verwenden">
        </div>

        <div id="achievementEditStatsSection">
            <label>Statistik (bis zu 8 Zeilen, je Bezeichnung + Wert)</label>
            <div id="achievementEditRows" class="achievement-edit-rows"></div>
            <button class="secondary-button" id="achievementEditAddRow" type="button">+ Zeile hinzufügen</button>
        </div>

        <div id="achievementEditLinksSection">
            <label>Links (bis zu 6, je Linktext + URL)</label>
            <div id="achievementEditLinkRows" class="achievement-edit-rows"></div>
            <button class="secondary-button" id="achievementEditAddLinkRow" type="button">+ Link hinzufügen</button>
        </div>

        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="achievementEditDialog">Abbrechen</button>
            <button class="primary-button" id="achievementEditSave" type="button">Speichern</button>
        </div>
    </div>
</dialog>

<dialog id="loginDialog">
    <form class="modal-content form-stack" id="loginForm" method="dialog">
        <div>
            <h2 class="modal-title">Anmelden</h2>
            <p class="modal-subtitle">Melde dich an, um deine eigene Verfügbarkeit zu bearbeiten.</p>
        </div>
        <div>
            <label for="loginUsername">Benutzername</label>
            <input type="text" id="loginUsername" autocomplete="username" required>
        </div>
        <div>
            <label for="loginPassword">Passwort</label>
            <input type="password" id="loginPassword" autocomplete="current-password" required>
        </div>
        <label class="remember-row">
            <input type="checkbox" id="loginRemember">
            <span>Angemeldet bleiben auf diesem Gerät</span>
        </label>
        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="loginDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Anmelden</button>
        </div>
    </form>
</dialog>

<dialog id="registerDialog">
    <form class="modal-content form-stack" id="registerForm" method="dialog">
        <div>
            <h2 class="modal-title" id="registerDialogTitle">Account anlegen</h2>
            <p class="modal-subtitle" id="registerDialogSubtitle">Lege deinen persönlichen Zugang zum Kalender an.</p>
        </div>
        <div>
            <label for="registerUsername">Benutzername</label>
            <input type="text" id="registerUsername" autocomplete="username" required>
        </div>
        <div>
            <label for="registerPlayerName">Spielername</label>
            <input type="text" id="registerPlayerName" maxlength="40" autocomplete="nickname" required>
            <p class="field-help">Existiert dieser Spieler bereits ohne Account, wird er mit deinem neuen Account verbunden.</p>
        </div>
        <div class="form-row">
            <div>
                <label for="registerPassword">Passwort</label>
                <input type="password" id="registerPassword" minlength="8" autocomplete="new-password" required>
            </div>
            <div>
                <label for="registerPasswordConfirmation">Passwort wiederholen</label>
                <input type="password" id="registerPasswordConfirmation" minlength="8" autocomplete="new-password" required>
            </div>
        </div>
        <p class="field-help"><strong>Passwortregel:</strong> Mindestens 8 Zeichen sowie mindestens ein Buchstabe, eine Zahl und ein Sonderzeichen, zum Beispiel <code>! ? # + - _ @ €</code>. Umlaute wie ä, ö und ü zählen als Buchstaben.</p>
        <label class="remember-row">
            <input type="checkbox" id="registerRemember" checked>
            <span>Angemeldet bleiben auf diesem Gerät</span>
        </label>
        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="registerDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Account anlegen</button>
        </div>
    </form>
</dialog>

<dialog id="profileDialog">
    <form class="modal-content form-stack" id="profileForm" method="dialog">
        <div>
            <h2 class="modal-title">Mein Account</h2>
            <p class="modal-subtitle">Verwalte deinen Spielernamen und deine üblichen Online-Tage.</p>
        </div>
        <div>
            <label>Benutzername</label>
            <input type="text" id="profileUsername" disabled>
        </div>
        <div>
            <label for="profilePlayerName">Spielername</label>
            <input type="text" id="profilePlayerName" maxlength="40" autocomplete="nickname" required>
        </div>
        <div>
            <label for="profileAvatarInput">Profilbild / Avatar</label>
            <div class="avatar-upload-row">
                <span class="avatar-placeholder avatar-preview" id="profileAvatarPreview" aria-hidden="true">?</span>
                <div>
                    <input type="file" id="profileAvatarInput" accept="image/png,image/jpeg,image/gif,image/webp">
                    <p class="field-help">PNG, JPG, GIF oder WebP. Wird automatisch auf maximal 50 × 50 Pixel verkleinert und in der Tabelle klein neben deinem Namen angezeigt.</p>
                    <button class="secondary-button" id="removeAvatarButton" type="button">Avatar entfernen</button>
                </div>
            </div>
        </div>
        <div>
            <label>Normalerweise online an</label>
            <div class="weekday-grid" id="profileWeekdays">
                <label class="weekday-choice"><input type="checkbox" value="1"><span>Mo</span></label>
                <label class="weekday-choice"><input type="checkbox" value="2"><span>Di</span></label>
                <label class="weekday-choice"><input type="checkbox" value="3"><span>Mi</span></label>
                <label class="weekday-choice"><input type="checkbox" value="4"><span>Do</span></label>
                <label class="weekday-choice"><input type="checkbox" value="5"><span>Fr</span></label>
                <label class="weekday-choice"><input type="checkbox" value="6"><span>Sa</span></label>
                <label class="weekday-choice"><input type="checkbox" value="7"><span>So</span></label>
            </div>
            <p class="field-help">Künftige Spieltage an diesen Wochentagen werden automatisch als „Online“ angezeigt. Ein einzelner Termin kann jederzeit überschrieben werden.</p>
        </div>
        <div class="modal-actions">
            <button class="secondary-button" id="openPasswordButton" type="button">Passwort ändern</button>
            <button class="secondary-button" type="button" data-close-dialog="profileDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Profil speichern</button>
        </div>
    </form>
</dialog>

<dialog id="passwordDialog">
    <form class="modal-content form-stack" id="passwordForm" method="dialog">
        <div>
            <h2 class="modal-title">Passwort ändern</h2>
            <p class="modal-subtitle" id="passwordDialogSubtitle">Lege ein neues Passwort fest.</p>
        </div>
        <div id="forcedPasswordNote" class="forced-password-note" hidden>
            Ein Administrator hat dein Passwort geändert. Bevor du den Kalender wieder bearbeiten kannst, musst du ein eigenes neues Passwort festlegen.
        </div>
        <div id="currentPasswordField">
            <label for="currentPassword">Aktuelles Passwort</label>
            <input type="password" id="currentPassword" autocomplete="current-password">
        </div>
        <div class="form-row">
            <div>
                <label for="newPassword">Neues Passwort</label>
                <input type="password" id="newPassword" minlength="8" autocomplete="new-password" required>
            </div>
            <div>
                <label for="newPasswordConfirmation">Neues Passwort wiederholen</label>
                <input type="password" id="newPasswordConfirmation" minlength="8" autocomplete="new-password" required>
            </div>
        </div>
        <p class="field-help"><strong>Passwortregel:</strong> Mindestens 8 Zeichen sowie mindestens ein Buchstabe, eine Zahl und ein Sonderzeichen, zum Beispiel <code>! ? # + - _ @ €</code>. Umlaute wie ä, ö und ü zählen als Buchstaben.</p>
        <div class="modal-actions">
            <button class="secondary-button" id="cancelPasswordButton" type="button" data-close-dialog="passwordDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Passwort speichern</button>
        </div>
    </form>
</dialog>

<dialog id="adminDialog" class="wide">
    <div class="modal-content">
        <div class="admin-toolbar">
            <div>
                <h2 class="modal-title">Adminbereich</h2>
                <p class="modal-subtitle">Benutzerkonten, Adminrechte und zentrale Kalenderdaten verwalten.</p>
            </div>
            <button class="primary-button" id="createUserButton" type="button">＋ Benutzer anlegen</button>
        </div>

        <h3 class="section-heading">Benutzerkonten</h3>
        <div class="admin-user-list" id="adminUserList"></div>

        <div class="separator"></div>

        <form id="adminSettingsForm" class="form-stack">
            <div>
                <h3 class="section-heading">Style für alle</h3>
                <p class="field-help">Nur Administratoren können den globalen Style ändern. Die Auswahl gilt nach dem Speichern für alle Besucher und Benutzer.</p>
            </div>
            <div>
                <label for="adminTheme">Style auswählen</label>
                <select id="adminTheme">
                    <option value="default">Standard: RGB-Gaming</option>
                    <option value="summer">Sommer: Sonne, Strand und Wasser</option>
                    <option value="winter">Winter: Schnee und Weihnachten</option>
                </select>
            </div>

            <div class="separator"></div>

            <div>
                <h3 class="section-heading">Administratoren nach Spielername</h3>
                <p class="field-help">Diese Liste wird in der Datendatei unter <code>settings.admin_player_names</code> gespeichert. Jeder Name muss zu einem bestehenden Account gehören. Mehrere Namen mit Komma oder in einzelnen Zeilen eintragen.</p>
            </div>
            <div>
                <label for="adminPlayerNames">Admin-Spielernamen</label>
                <textarea id="adminPlayerNames" required></textarea>
            </div>
            <div class="modal-actions">
                <button class="secondary-button" type="button" data-close-dialog="adminDialog">Schließen</button>
                <button class="primary-button" type="submit">Einstellungen speichern</button>
            </div>
        </form>
    </div>
</dialog>

<dialog id="adminUserDialog">
    <form class="modal-content form-stack" id="adminUserForm" method="dialog">
        <div>
            <h2 class="modal-title" id="adminUserDialogTitle">Benutzer anlegen</h2>
            <p class="modal-subtitle" id="adminUserDialogSubtitle">Der Benutzer muss das vorläufige Passwort nach der ersten Anmeldung ändern.</p>
        </div>
        <input type="hidden" id="adminUserId">
        <div>
            <label for="adminUsername">Benutzername</label>
            <input type="text" id="adminUsername" autocomplete="off" required>
        </div>
        <div>
            <label for="adminUserPlayerName">Spielername</label>
            <input type="text" id="adminUserPlayerName" maxlength="40" autocomplete="off" required>
        </div>
        <div class="form-row">
            <div>
                <label for="adminUserPassword" id="adminUserPasswordLabel">Vorläufiges Passwort</label>
                <input type="password" id="adminUserPassword" minlength="8" autocomplete="new-password">
            </div>
            <div>
                <label for="adminUserPasswordConfirmation">Passwort wiederholen</label>
                <input type="password" id="adminUserPasswordConfirmation" minlength="8" autocomplete="new-password">
            </div>
        </div>
        <p class="field-help" id="adminPasswordHelp">Beim Anlegen ist ein Passwort mit mindestens 8 Zeichen, einem Buchstaben, einer Zahl und einem Sonderzeichen wie !, ?, #, +, -, _, @ oder € erforderlich. Umlaute zählen als Buchstaben. Der Benutzer wird nach der ersten Anmeldung zur Änderung aufgefordert.</p>
        <div class="modal-actions">
            <button class="danger-button" id="deleteUserButton" type="button" hidden>Benutzer löschen</button>
            <button class="secondary-button" type="button" data-close-dialog="adminUserDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Speichern</button>
        </div>
    </form>
</dialog>

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

        <div>
            <label for="statusGame">Was möchtest du spielen? <small>(optional)</small></label>
            <input type="text" id="statusGame" list="gameOptions" maxlength="60" placeholder="z. B. WoW, Diablo 4 oder Factorio" autocomplete="off">
            <datalist id="gameOptions"></datalist>
            <p class="field-help">Du kannst ein neues Spiel eintragen oder einen bereits genannten Titel aus der Liste auswählen.</p>
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

<script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
    const STATUS_META = {
        online: { icon: '⚔', label: 'Online', className: 'online' },
        late: { icon: '◷', label: 'Später', className: 'late' },
        absent: { icon: '✕', label: 'Verhindert', className: 'absent' },
        vacation: { icon: '☀', label: 'Urlaub', className: 'vacation' },
        '': { icon: '?', label: 'Offen', className: 'open' }
    };

    const THEME_COLORS = {
        default: '#070914',
        summer: '#06383d',
        winter: '#07182b'
    };

    const state = {
        players: [],
        availability: {},
        gameOptions: [],
        eventDates: [],
        auth: { logged_in: false, setup_required: false, is_admin: false, must_change_password: false, can_write: false, user: null },
        admin: null,
        settings: { theme: 'default' },
        csrf: '',
        storageWritable: true
    };

    const byId = id => document.getElementById(id);
    const loading = byId('loading');
    const tableScroll = byId('tableScroll');
    const planBody = byId('planBody');
    const dateHeaderRow = byId('dateHeaderRow');
    const emptyState = byId('emptyState');
    const toast = byId('toast');
    const storageWarning = byId('storageWarning');
    const setupCallout = byId('setupCallout');
    const passwordCallout = byId('passwordCallout');
    const accountSummary = byId('accountSummary');

    const installDialog = byId('installDialog');
    const infoDialog = byId('infoDialog');
    const achievementEditDialog = byId('achievementEditDialog');
    const loginDialog = byId('loginDialog');
    const registerDialog = byId('registerDialog');
    const profileDialog = byId('profileDialog');
    const passwordDialog = byId('passwordDialog');
    const adminDialog = byId('adminDialog');
    const adminUserDialog = byId('adminUserDialog');
    const playerDialog = byId('playerDialog');
    const dateDialog = byId('dateDialog');
    const statusDialog = byId('statusDialog');

    const playerForm = byId('playerForm');
    const playerId = byId('playerId');
    const playerName = byId('playerName');
    const deletePlayerButton = byId('deletePlayerButton');
    const dateForm = byId('dateForm');
    const eventDateInput = byId('eventDateInput');
    const statusForm = byId('statusForm');
    const statusGame = byId('statusGame');
    const gameOptionsList = byId('gameOptions');
    const statusNote = byId('statusNote');
    const selectedStatus = byId('selectedStatus');
    let toastTimer;
    let passwordChangeForced = false;
    let deferredInstallPrompt = null;
    let profileAvatarData = '';

    function playerInitial(name) {
        const clean = String(name || '').trim();
        return clean ? clean.slice(0, 1).toUpperCase() : '?';
    }

    function createAvatarElement(src, name, className = '') {
        if (src) {
            const image = document.createElement('img');
            image.className = `avatar ${className}`.trim();
            image.src = src;
            image.alt = `${name || 'Spieler'} Avatar`;
            image.loading = 'lazy';
            image.decoding = 'async';
            return image;
        }

        const placeholder = document.createElement('span');
        placeholder.className = `avatar-placeholder ${className}`.trim();
        placeholder.textContent = playerInitial(name);
        placeholder.setAttribute('aria-hidden', 'true');
        return placeholder;
    }

    function setAvatarPreview(src, name) {
        const preview = byId('profileAvatarPreview');
        const replacement = createAvatarElement(src, name || byId('profilePlayerName').value, 'avatar-preview');
        replacement.id = 'profileAvatarPreview';
        preview.replaceWith(replacement);
    }

    function readImageFile(file) {
        return new Promise((resolve, reject) => {
            if (!file || !file.type.startsWith('image/')) {
                reject(new Error('Bitte wähle eine Bilddatei aus.'));
                return;
            }

            const reader = new FileReader();
            reader.onerror = () => reject(new Error('Das Bild konnte nicht gelesen werden.'));
            reader.onload = () => {
                const image = new Image();
                image.onerror = () => reject(new Error('Das Bild konnte nicht verarbeitet werden.'));
                image.onload = () => {
                    const scale = Math.min(1, 50 / image.naturalWidth, 50 / image.naturalHeight);
                    const width = Math.max(1, Math.round(image.naturalWidth * scale));
                    const height = Math.max(1, Math.round(image.naturalHeight * scale));
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const context = canvas.getContext('2d');
                    context.clearRect(0, 0, width, height);
                    context.drawImage(image, 0, 0, width, height);
                    resolve(canvas.toDataURL('image/png'));
                };
                image.src = String(reader.result || '');
            };
            reader.readAsDataURL(file);
        });
    }

    function showToast(message) {
        toast.textContent = message;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 3200);
    }

    async function api(action, payload = null, extraQuery = null) {
        const options = payload === null
            ? { headers: { Accept: 'application/json' }, credentials: 'same-origin' }
            : {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': state.csrf
                },
                body: JSON.stringify({ action, ...payload })
            };

        let url = payload === null ? `api.php?action=${encodeURIComponent(action)}` : 'api.php';
        if (payload === null && extraQuery) {
            for (const [key, value] of Object.entries(extraQuery)) {
                url += `&${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
            }
        }
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
            apiError.status = response.status;
            throw apiError;
        }

        return data;
    }

    function applyData(data) {
        state.players = data.players || [];
        state.availability = data.availability || {};
        state.gameOptions = data.game_options || [];
        state.eventDates = data.event_dates || [];
        state.settings = data.settings || state.settings;
        applyTheme(state.settings.theme);
        renderGameOptions();
        state.auth = data.auth || state.auth;
        state.admin = data.admin || null;
        state.csrf = data.csrf_token || state.csrf;
        state.storageWritable = data.storage_writable !== false;
        storageWarning.hidden = state.storageWritable;
        loading.hidden = true;
        renderAuth();
        renderPlan();
        renderAchievementGrid();
        if (adminDialog.open) renderAdminPanel();
    }

    function applyTheme(theme) {
        const normalized = Object.prototype.hasOwnProperty.call(THEME_COLORS, theme) ? theme : 'default';
        document.body.dataset.theme = normalized;
        byId('themeColorMeta')?.setAttribute('content', THEME_COLORS[normalized]);
    }

    function renderGameOptions() {
        gameOptionsList.replaceChildren();
        for (const game of state.gameOptions) {
            const option = document.createElement('option');
            option.value = game;
            gameOptionsList.appendChild(option);
        }
    }

    function isInstalledApp() {
        return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    }

    function showInstallInstructions() {
        const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
        const steps = isIos
            ? [
                'Öffne diese Seite in Safari.',
                'Tippe in Safari auf „Teilen“.',
                'Wähle „Zum Home-Bildschirm hinzufügen“, aktiviere „Als Web-App öffnen“ und tippe auf „Hinzufügen“.'
            ]
            : [
                'Öffne das Browsermenü oben rechts.',
                'Wähle „App installieren“ oder „Zum Startbildschirm hinzufügen“.',
                'Bestätige die Installation. Danach erscheint das Kellerkinder-Symbol wie eine App auf deinem Home-Bildschirm.'
            ];
        byId('installDialogSubtitle').textContent = isIos
            ? 'Auf dem iPhone wird die Web-App über das Teilen-Menü in Safari installiert.'
            : 'Falls dein Browser keinen direkten Installationsdialog anbietet, nutze diese Schritte.';
        const list = byId('installSteps');
        list.replaceChildren();
        for (const text of steps) {
            const item = document.createElement('li');
            item.textContent = text;
            list.appendChild(item);
        }
        installDialog.showModal();
    }

    async function installApp() {
        if (isInstalledApp()) {
            showToast('Der Kalender ist bereits als App geöffnet.');
            return;
        }
        if (deferredInstallPrompt) {
            deferredInstallPrompt.prompt();
            const choice = await deferredInstallPrompt.userChoice;
            deferredInstallPrompt = null;
            if (choice.outcome === 'accepted') {
                showToast('Der Kellerkinder-Kalender wurde installiert.');
            }
            return;
        }
        showInstallInstructions();
    }

    function computeDesiredDayCount() {
        const boardEl = document.querySelector('.board');
        const minDays = 4;
        const normalDays = 8;
        if (!boardEl) return normalDays;
        const isMobile = window.innerWidth <= 680;
        const boardPadding = isMobile ? 28 : 44;
        const playerColWidth = isMobile ? 118 : 150;
        const dateColWidth = isMobile ? 58 : 75;
        const available = boardEl.clientWidth - boardPadding - playerColWidth;
        const fitting = Math.floor(available / dateColWidth);
        // Genug Platz für mehr als die normalen 8 Spalten? Dann bis zum Rand auffüllen.
        if (fitting >= normalDays) return fitting;
        // Sonst so viele wie reinpassen, aber nie unter dem Minimum von 4.
        return Math.max(minDays, fitting);
    }

    function getVisibleEventDates() {
        const pastDate = state.eventDates.find(d => d.is_past);
        const futureDates = state.eventDates.filter(d => !d.is_past);
        const desiredFuture = computeDesiredDayCount();
        const futureToShow = futureDates.slice(0, Math.max(4, Math.min(desiredFuture, futureDates.length)));
        return pastDate ? [pastDate, ...futureToShow] : futureToShow;
    }

    async function loadPlan() {
        try {
            applyData(await api('bootstrap'));
        } catch (error) {
            loading.textContent = 'Der Plan konnte nicht geladen werden.';
            showToast(error.message);
        }
    }

    // Erfolgs-Widgets: jedes Spiel hat GENAU EINE Seite mit zwei Karten —
    // links Statistik, rechts die dazugehörigen Links. Bei WoW kommt die
    // Statistik live von Raider.IO, bei den anderen drei ist sie manuell
    // gepflegt. Titel und Links sind bei allen vier Admin-editierbar.
    const achievementGames = [
        { id: 'wow', label: 'World of Warcraft', icon: '⚔', type: 'wow', source: 'Beste Mythisch-Plus-Läufe, live via Raider.IO' },
        { id: 'hots', label: 'Heroes of the Storm', icon: '🌀', type: 'manual', source: 'Manuell gepflegt' },
        { id: 'diablo4', label: 'Diablo IV', icon: '🔥', type: 'manual', source: 'Manuell gepflegt' },
        { id: 'rocket_league', label: 'Rocket League', icon: '🚀', type: 'manual', source: 'Manuell gepflegt' },
    ];
    const achievementGamesById = Object.fromEntries(achievementGames.map(game => [game.id, game]));
    let achievementPairIndex = 0;
    let achievementsData = null;

    function statsCardTitle(game) {
        if (game.type === 'wow') return 'M+ Wertungen';
        const data = achievementsData ? achievementsData[game.id] : null;
        return (data && data.title) || game.label;
    }

    function linksCardTitle(game) {
        const data = achievementsData ? achievementsData[game.id] : null;
        return (data && data.links_title) || `${game.label} Links`;
    }

    function renderAchievementNav() {
        const game = achievementGames[achievementPairIndex];
        byId('achievementsNavLabel').textContent = game.label;
        byId('achievementsPrev').disabled = achievementGames.length <= 1;
        byId('achievementsNext').disabled = achievementGames.length <= 1;
    }

    function formatUpdatedAt(isoString) {
        if (!isoString) return '';
        const date = new Date(isoString);
        if (Number.isNaN(date.getTime())) return '';
        return `Aktualisiert ${date.toLocaleString('de-DE', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })}`;
    }

    function buildWowBody(wow) {
        const container = document.createElement('div');
        container.className = 'mplus-bars';

        if (!wow || !wow.configured) {
            container.innerHTML = '<p class="widget-empty">Noch keine WoW-Charaktere für Raider.IO hinterlegt.</p>';
            return { body: container, updated: '' };
        }
        if (!wow.runs || wow.runs.length === 0) {
            container.innerHTML = '<p class="widget-empty">Für die hinterlegten Charaktere liegen noch keine Season-Läufe vor.</p>';
            return { body: container, updated: formatUpdatedAt(wow.updated_at) };
        }

        const maxScore = Math.max(...wow.runs.map(run => run.score || 0), 1);
        for (const run of wow.runs) {
            const row = document.createElement('div');
            row.className = 'mplus-bar-row';

            const label = document.createElement('div');
            label.className = 'mplus-bar-label';

            const textSpan = document.createElement('span');
            const isSafeProfileLink = typeof run.profile_url === 'string' && /^https:\/\//i.test(run.profile_url);
            const nameNode = document.createElement(isSafeProfileLink ? 'a' : 'span');
            if (isSafeProfileLink) {
                nameNode.href = run.profile_url;
                nameNode.target = '_blank';
                nameNode.rel = 'noopener';
            }
            nameNode.textContent = run.character || '';
            textSpan.appendChild(nameNode);

            if (run.score) {
                const scoreNode = document.createElement('small');
                scoreNode.textContent = ` · Score ${run.score}`;
                textSpan.appendChild(scoreNode);
            }
            textSpan.appendChild(document.createTextNode(` — ${run.dungeon || ''}`));

            const levelNode = document.createElement('b');
            levelNode.textContent = `+${run.level}`;
            levelNode.classList.toggle('top-tier', run.score === maxScore);

            label.append(textSpan, levelNode);

            const track = document.createElement('div');
            track.className = 'mplus-bar-track';
            const fill = document.createElement('div');
            fill.className = 'mplus-bar-fill' + (run.score === maxScore ? ' top-tier' : '');
            fill.style.width = `${Math.max(6, Math.round(((run.score || 0) / maxScore) * 100))}%`;
            track.appendChild(fill);

            row.append(label, track);
            container.appendChild(row);
        }

        return { body: container, updated: formatUpdatedAt(wow.updated_at) };
    }

    function buildManualStatsBody(data) {
        const list = document.createElement('ul');
        list.className = 'd4-milestones';

        const milestones = (data && data.milestones) || [];
        if (milestones.length === 0) {
            list.innerHTML = '<li class="widget-empty">Noch keine Einträge hinterlegt.</li>';
        } else {
            for (const milestone of milestones) {
                const item = document.createElement('li');
                const label = document.createElement('span');
                label.textContent = milestone.label;
                const value = document.createElement('span');
                value.textContent = milestone.value;
                item.append(label, value);
                list.appendChild(item);
            }
        }

        const updated = data && data.updated_at
            ? formatUpdatedAt(data.updated_at)
            : 'Noch nicht aktualisiert';

        return { body: list, updated };
    }

    function buildLinksBody(data) {
        const list = document.createElement('ul');
        list.className = 'achievement-links';

        const links = (data && data.links) || [];
        if (links.length === 0) {
            list.innerHTML = '<li class="widget-empty">Noch keine Links hinterlegt.</li>';
        } else {
            for (const link of links) {
                const item = document.createElement('li');
                const anchor = document.createElement('a');
                anchor.href = link.url;
                anchor.target = '_blank';
                anchor.rel = 'noopener';
                anchor.textContent = link.label;
                item.appendChild(anchor);
                list.appendChild(item);
            }
        }

        return { body: list, updated: '' };
    }

    function buildAchievementCardShell(game, cardType) {
        const card = document.createElement('article');
        card.className = 'achievement-card';
        card.dataset.widget = `${game.id}-${cardType}`;

        const head = document.createElement('div');
        head.className = 'achievement-card-head';

        const icon = document.createElement('span');
        icon.className = `achievement-icon ${game.id}`;
        icon.setAttribute('aria-hidden', 'true');
        icon.textContent = cardType === 'links' ? '🔗' : game.icon;

        const headText = document.createElement('div');
        headText.className = 'achievement-card-head-text';
        const title = document.createElement('h3');
        title.textContent = cardType === 'links' ? linksCardTitle(game) : statsCardTitle(game);
        const source = document.createElement('p');
        source.className = 'achievement-source';
        source.textContent = cardType === 'links' ? 'Nützliche Links' : game.source;
        headText.append(title, source);

        head.append(icon, headText);

        const canEdit = cardType === 'links' ? state.admin : (state.admin && game.type === 'manual');
        if (canEdit) {
            const editButton = document.createElement('button');
            editButton.type = 'button';
            editButton.className = 'achievement-edit-button';
            editButton.title = `${game.label} bearbeiten`;
            editButton.setAttribute('aria-label', `${game.label} bearbeiten`);
            editButton.textContent = '✎';
            editButton.addEventListener('click', () => openAchievementEditDialog(game.id, cardType));
            head.appendChild(editButton);
        }

        card.appendChild(head);
        return card;
    }

    function buildAchievementCard(game, cardType) {
        const card = buildAchievementCardShell(game, cardType);
        const gameData = achievementsData ? achievementsData[game.id] : null;

        if (!achievementsData) {
            const loading = document.createElement('p');
            loading.className = 'widget-loading';
            loading.textContent = 'Wird geladen …';
            card.appendChild(loading);
            card.appendChild(document.createElement('p')).className = 'achievement-updated';
            return card;
        }

        let built;
        if (cardType === 'links') {
            built = buildLinksBody(gameData);
        } else {
            built = game.type === 'wow' ? buildWowBody(gameData) : buildManualStatsBody(gameData);
        }
        card.appendChild(built.body);

        const updated = document.createElement('p');
        updated.className = 'achievement-updated';
        if (cardType === 'stats' && game.type === 'manual' && (!gameData || !gameData.configured)) {
            updated.innerHTML = 'Platzhalter-Daten <span class="mock-tag">Beispiel</span>';
        } else {
            updated.textContent = built.updated;
        }
        card.appendChild(updated);

        return card;
    }

    function renderAchievementGrid() {
        renderAchievementNav();
        const grid = byId('achievementGrid');
        grid.replaceChildren();
        const game = achievementGames[achievementPairIndex];
        grid.appendChild(buildAchievementCard(game, 'stats'));
        grid.appendChild(buildAchievementCard(game, 'links'));
    }

    async function loadAchievements() {
        renderAchievementGrid();
        try {
            const data = await api('achievements');
            achievementsData = {
                wow: data.wow,
                hots: data.hots,
                diablo4: data.diablo4,
                rocket_league: data.rocket_league,
            };
        } catch (error) {
            achievementsData = null;
            showToast('Die Erfolge konnten nicht geladen werden.');
        }
        renderAchievementGrid();
    }

    let achievementEditRowId = 0;

    function addAchievementEditRow(label = '', value = '') {
        const rowId = `achievementRow${achievementEditRowId++}`;
        const row = document.createElement('div');
        row.className = 'achievement-edit-row';
        row.dataset.rowId = rowId;

        const labelInput = document.createElement('input');
        labelInput.type = 'text';
        labelInput.placeholder = 'Bezeichnung, z. B. „Rang“';
        labelInput.maxLength = 40;
        labelInput.value = label;
        labelInput.className = 'achievement-edit-label';

        const valueInput = document.createElement('input');
        valueInput.type = 'text';
        valueInput.placeholder = 'Wert, z. B. „Diamant 2“';
        valueInput.maxLength = 60;
        valueInput.value = value;
        valueInput.className = 'achievement-edit-value';

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = '✕';
        removeButton.setAttribute('aria-label', 'Zeile entfernen');
        removeButton.addEventListener('click', () => row.remove());

        row.append(labelInput, valueInput, removeButton);
        byId('achievementEditRows').appendChild(row);
    }

    function addAchievementEditLinkRow(label = '', url = '') {
        const row = document.createElement('div');
        row.className = 'achievement-edit-row';

        const labelInput = document.createElement('input');
        labelInput.type = 'text';
        labelInput.placeholder = 'Linktext, z. B. „Raider.IO Gilde“';
        labelInput.maxLength = 30;
        labelInput.value = label;
        labelInput.className = 'achievement-edit-link-label';

        const urlInput = document.createElement('input');
        urlInput.type = 'url';
        urlInput.placeholder = 'https://…';
        urlInput.maxLength = 200;
        urlInput.value = url;
        urlInput.className = 'achievement-edit-link-url';

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = '✕';
        removeButton.setAttribute('aria-label', 'Link entfernen');
        removeButton.addEventListener('click', () => row.remove());

        row.append(labelInput, urlInput, removeButton);
        byId('achievementEditLinkRows').appendChild(row);
    }

    function openAchievementEditDialog(gameId, part) {
        const game = achievementGamesById[gameId];
        const data = achievementsData ? achievementsData[gameId] : null;

        const statsSection = byId('achievementEditStatsSection');
        const linksSection = byId('achievementEditLinksSection');
        statsSection.hidden = part !== 'stats';
        linksSection.hidden = part !== 'links';

        const titleInput = byId('achievementEditTitleInput');
        if (part === 'stats') {
            byId('achievementEditTitle').textContent = `${statsCardTitle(game)} bearbeiten`;
            titleInput.value = (data && data.title) || '';
            titleInput.placeholder = game.label;

            byId('achievementEditRows').replaceChildren();
            const currentMilestones = (data && data.milestones) || [];
            if (currentMilestones.length === 0) {
                addAchievementEditRow();
            } else {
                for (const milestone of currentMilestones) addAchievementEditRow(milestone.label, milestone.value);
            }
        } else {
            byId('achievementEditTitle').textContent = `${linksCardTitle(game)} bearbeiten`;
            titleInput.value = (data && data.links_title) || '';
            titleInput.placeholder = `${game.label} Links`;

            byId('achievementEditLinkRows').replaceChildren();
            const currentLinks = (data && data.links) || [];
            if (currentLinks.length === 0) {
                addAchievementEditLinkRow();
            } else {
                for (const link of currentLinks) addAchievementEditLinkRow(link.label, link.url);
            }
        }

        const saveButton = byId('achievementEditSave');
        saveButton.onclick = async () => {
            const title = titleInput.value.trim();
            const payload = { game: gameId, part };

            if (part === 'stats') {
                const milestoneRows = [...byId('achievementEditRows').querySelectorAll('.achievement-edit-row')];
                payload.title = title;
                payload.milestones = milestoneRows
                    .map(row => ({
                        label: row.querySelector('.achievement-edit-label').value.trim(),
                        value: row.querySelector('.achievement-edit-value').value.trim(),
                    }))
                    .filter(entry => entry.label !== '' && entry.value !== '');
            } else {
                const linkRows = [...byId('achievementEditLinkRows').querySelectorAll('.achievement-edit-row')];
                const links = linkRows
                    .map(row => ({
                        label: row.querySelector('.achievement-edit-link-label').value.trim(),
                        url: row.querySelector('.achievement-edit-link-url').value.trim(),
                    }))
                    .filter(entry => entry.label !== '' && entry.url !== '');

                for (const link of links) {
                    if (!/^https?:\/\//i.test(link.url)) {
                        showToast(`Der Link „${link.label}“ muss mit http:// oder https:// beginnen.`);
                        return;
                    }
                }
                payload.links_title = title;
                payload.links = links;
            }

            saveButton.disabled = true;
            try {
                await api('admin_save_achievement', payload);
                achievementEditDialog.close();
                showToast('Wurde aktualisiert.');
                await loadAchievements();
            } catch (error) {
                handleApiError(error);
                showToast(error.message);
            } finally {
                saveButton.disabled = false;
            }
        };

        achievementEditDialog.showModal();
    }

    byId('achievementEditAddRow').addEventListener('click', () => addAchievementEditRow());
    byId('achievementEditAddLinkRow').addEventListener('click', () => addAchievementEditLinkRow());

    function handleApiError(error) {
        if (error.code === 'storage_not_writable') storageWarning.hidden = false;
        if (error.code === 'password_change_required') openPasswordDialog(true);
        if (error.status === 419) loadPlan();
        showToast(error.message);
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

    function passwordValidationMessage(password, confirmation = null) {
        const characters = [...password];
        const hasLetter = characters.some(character => /^\p{L}$/u.test(character));
        const hasNumber = characters.some(character => /^\p{N}$/u.test(character));
        const hasSpecial = characters.some(character =>
            !/^\p{L}$/u.test(character)
            && !/^\p{N}$/u.test(character)
            && !/^\s$/u.test(character)
        );

        if (characters.length < 8) return 'Das Passwort muss mindestens 8 Zeichen lang sein.';
        if (!hasLetter) return 'Das Passwort muss mindestens einen Buchstaben enthalten.';
        if (!hasNumber) return 'Das Passwort muss mindestens eine Zahl enthalten.';
        if (!hasSpecial) return 'Das Passwort muss mindestens ein Sonderzeichen wie !, ?, #, +, -, _, @ oder € enthalten. Umlaute gelten als Buchstaben.';
        if (confirmation !== null && password !== confirmation) return 'Die beiden Passwörter stimmen nicht überein.';
        return '';
    }

    function renderAuth() {
        const auth = state.auth;
        setupCallout.hidden = !auth.setup_required;
        passwordCallout.hidden = !auth.must_change_password;

        byId('loginButton').hidden = auth.logged_in;
        byId('registerButton').hidden = auth.logged_in;
        byId('profileButton').hidden = !auth.logged_in || auth.must_change_password;
        byId('adminButton').hidden = !auth.is_admin || auth.must_change_password;
        byId('logoutButton').hidden = !auth.logged_in;
        byId('addDateButton').hidden = !auth.can_write;
        byId('addPlayerButton').hidden = !auth.is_admin || auth.must_change_password;

        accountSummary.replaceChildren();
        const avatar = createAvatarElement(auth.user?.avatar || '', auth.user?.player_name || auth.user?.username || '', 'account-avatar');
        const text = document.createElement('div');
        text.className = 'account-summary-text';
        const strong = document.createElement('strong');
        const small = document.createElement('small');

        if (!auth.logged_in) {
            strong.textContent = auth.setup_required ? 'Noch kein Administrator eingerichtet' : 'Nur-Lese-Ansicht';
            small.textContent = auth.setup_required
                ? 'Lege den ersten Account an. Dieser wird automatisch Administrator.'
                : 'Melde dich an oder registriere dich, um deinen eigenen Spieler zu bearbeiten.';
        } else {
            strong.textContent = auth.user?.player_name || 'Spielername noch nicht eingerichtet';
            if (auth.is_admin) {
                const badge = document.createElement('span');
                badge.className = 'account-badge admin';
                badge.textContent = 'Admin';
                strong.appendChild(badge);
            }
            small.textContent = `Angemeldet als ${auth.user?.username || ''}${auth.must_change_password ? ' · Passwortänderung erforderlich' : ''}`;
        }
        text.append(strong, small);
        accountSummary.append(avatar, text);

        if (auth.must_change_password && !passwordDialog.open) {
            setTimeout(() => openPasswordDialog(true), 50);
        }
    }

    function renderDateHeaders() {
        while (dateHeaderRow.children.length > 1) dateHeaderRow.lastElementChild.remove();

        for (const eventDate of getVisibleEventDates()) {
            const parts = formatDateParts(eventDate.date);
            const heading = document.createElement('th');
            heading.scope = 'col';
            heading.dataset.date = eventDate.date;
            if (eventDate.is_today) heading.classList.add('is-today');

            const day = document.createElement('span');
            day.className = 'date-day';
            day.textContent = parts.day;
            const value = document.createElement('span');
            value.className = 'date-value';
            value.textContent = parts.value;
            heading.append(day, value);

            const today = document.createElement('span');
            today.className = 'today-tag';
            today.textContent = 'Heute';
            heading.appendChild(today);

            if (eventDate.is_past) {
                const past = document.createElement('span');
                past.className = 'past-tag';
                past.textContent = 'Vergangen';
                heading.appendChild(past);
            }

            if (eventDate.is_custom && state.auth.is_admin && !state.auth.must_change_password) {
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
        planBody.replaceChildren();
        renderDateHeaders();

        if (state.players.length === 0) {
            tableScroll.hidden = true;
            emptyState.style.display = 'block';
            emptyState.querySelector('strong').textContent = state.auth.is_admin ? 'Noch keine Spieler eingetragen.' : 'Noch keine Spieler eingetragen.';
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
            const title = document.createElement('span');
            title.className = 'player-title';
            title.appendChild(createAvatarElement(player.avatar || '', player.name));
            const nameText = document.createElement('span');
            nameText.className = 'player-name';
            nameText.textContent = player.name;
            title.appendChild(nameText);
            if (player.is_own) {
                const badge = document.createElement('span');
                badge.className = 'player-badge';
                badge.textContent = 'Du';
                title.appendChild(badge);
            }
            playerButton.appendChild(title);
            const playerHint = document.createElement('small');
            if (player.can_edit) {
                playerHint.textContent = state.auth.is_admin ? 'Antippen zum Bearbeiten' : 'Dein Spieler · Account öffnen';
                playerButton.addEventListener('click', () => state.auth.is_admin ? openPlayerDialog(player) : openProfileDialog());
            } else {
                playerHint.textContent = player.has_account ? 'Durch Account geschützt' : 'Noch keinem Account zugeordnet';
                playerButton.classList.add('readonly');
                playerButton.disabled = true;
            }
            playerButton.appendChild(playerHint);
            playerCell.appendChild(playerButton);
            row.appendChild(playerCell);

            for (const eventDate of getVisibleEventDates()) {
                const date = eventDate.date;
                const dateLabel = formatDateParts(date).label;
                const cell = document.createElement('td');
                cell.className = 'status-cell';
                if (eventDate.is_today) cell.classList.add('is-today');
                const entry = state.availability[`${player.id}:${date}`] || { status: '', note: '', game: '', source: '' };
                const meta = STATUS_META[entry.status] || STATUS_META[''];

                const button = document.createElement('button');
                button.type = 'button';
                button.className = `status-button ${meta.className}`;
                const entryDetails = [entry.game ? `Spiel: ${entry.game}` : '', entry.note || ''].filter(Boolean).join(', ');
                button.setAttribute('aria-label', `${player.name}, ${dateLabel}: ${meta.label}${entryDetails ? ', ' + entryDetails : ''}`);
                button.title = player.can_edit ? (entryDetails || `${meta.label} · Antippen zum Ändern`) : (entryDetails || meta.label);

                const iconRow = document.createElement('span');
                iconRow.className = 'icon-row';
                const icon = document.createElement('span');
                icon.className = 'icon';
                icon.textContent = meta.icon;
                const label = document.createElement('span');
                label.className = 'label';
                label.textContent = meta.label;
                iconRow.append(icon, label);
                button.appendChild(iconRow);

                if (entry.source === 'recurring') {
                    const recurring = document.createElement('span');
                    recurring.className = 'recurring-tag';
                    recurring.textContent = 'Standard';
                    button.appendChild(recurring);
                }
                if (entry.game) {
                    const game = document.createElement('span');
                    game.className = 'game';
                    game.textContent = `🎮 ${entry.game}`;
                    button.appendChild(game);
                }
                if (entry.note) {
                    const note = document.createElement('span');
                    note.className = 'note';
                    note.textContent = entry.note;
                    button.appendChild(note);
                }

                button.disabled = !player.can_edit || !state.storageWritable;
                if (!button.disabled) button.addEventListener('click', () => openStatusDialog(player, date, entry));
                cell.appendChild(button);
                row.appendChild(cell);
            }
            planBody.appendChild(row);
        }
    }

    function openDateDialog() {
        if (!state.auth.can_write || !state.storageWritable) return;
        eventDateInput.value = '';
        dateDialog.showModal();
        requestAnimationFrame(() => {
            eventDateInput.focus();
            if (typeof eventDateInput.showPicker === 'function') {
                try { eventDateInput.showPicker(); } catch (error) { /* Fokus reicht als Rückfall. */ }
            }
        });
    }

    async function deleteEventDate(date, label) {
        if (!confirm(`Den zusätzlichen Spieltag „${label}“ samt aller Einträge wirklich löschen?`)) return;
        try {
            applyData(await api('delete_event_date', { event_date: date }));
            showToast('Spieltag wurde gelöscht.');
        } catch (error) { handleApiError(error); }
    }

    function openPlayerDialog(player = null) {
        if (!state.auth.is_admin) return;
        const isEdit = Boolean(player);
        byId('playerDialogTitle').textContent = isEdit ? 'Spieler bearbeiten' : 'Spieler hinzufügen';
        byId('playerDialogSubtitle').textContent = isEdit
            ? 'Name ändern oder den Spieler vollständig löschen. Verknüpfte Accounts bleiben bestehen, wenn ein Spieler gelöscht wird.'
            : 'Lege einen zusätzlichen Spieler ohne Benutzerkonto an.';
        playerId.value = player?.id || '';
        playerName.value = player?.name || '';
        deletePlayerButton.hidden = !isEdit;
        playerDialog.showModal();
        requestAnimationFrame(() => playerName.focus());
    }

    function openStatusDialog(player, date, entry) {
        if (!player.can_edit) return;
        byId('statusDialogTitle').textContent = player.name;
        byId('statusDialogSubtitle').textContent = formatDateParts(date).label + (entry.source === 'recurring' ? ' · automatisch aus deinem Wochenstandard' : '');
        byId('statusPlayerId').value = player.id;
        byId('statusDate').value = date;
        selectedStatus.value = entry.status || '';
        statusGame.value = entry.game || '';
        statusNote.value = entry.note || '';
        updateStatusChoices();
        statusDialog.showModal();
    }

    function updateStatusChoices() {
        document.querySelectorAll('.status-choice').forEach(button => {
            button.classList.toggle('selected', button.dataset.status === selectedStatus.value);
        });
    }

    function openLoginDialog() {
        byId('loginForm').reset();
        loginDialog.showModal();
        requestAnimationFrame(() => byId('loginUsername').focus());
    }

    function openRegisterDialog() {
        byId('registerForm').reset();
        byId('registerDialogTitle').textContent = state.auth.setup_required ? 'Ersten Administrator anlegen' : 'Account anlegen';
        byId('registerDialogSubtitle').textContent = state.auth.setup_required
            ? 'Der erste Account erhält automatisch vollständige Administratorrechte.'
            : 'Lege deinen persönlichen Zugang zum Kalender an.';
        registerDialog.showModal();
        requestAnimationFrame(() => byId('registerUsername').focus());
    }

    function openProfileDialog() {
        if (!state.auth.logged_in || state.auth.must_change_password) return;
        const user = state.auth.user || {};
        byId('profileUsername').value = user.username || '';
        byId('profilePlayerName').value = user.player_name || '';
        byId('profileAvatarInput').value = '';
        profileAvatarData = user.avatar || '';
        setAvatarPreview(profileAvatarData, user.player_name || user.username || '');
        const days = new Set((user.default_weekdays || []).map(Number));
        document.querySelectorAll('#profileWeekdays input').forEach(input => input.checked = days.has(Number(input.value)));
        profileDialog.showModal();
        requestAnimationFrame(() => byId('profilePlayerName').focus());
    }

    function openPasswordDialog(forced = false) {
        if (!state.auth.logged_in) return;
        passwordChangeForced = forced || state.auth.must_change_password;
        byId('passwordForm').reset();
        byId('forcedPasswordNote').hidden = !passwordChangeForced;
        byId('currentPasswordField').hidden = passwordChangeForced;
        byId('currentPassword').required = !passwordChangeForced;
        byId('cancelPasswordButton').hidden = passwordChangeForced;
        byId('passwordDialogSubtitle').textContent = passwordChangeForced
            ? 'Lege jetzt ein eigenes neues Passwort fest, um den Kalender wieder bearbeiten zu können.'
            : 'Bestätige dein aktuelles Passwort und lege danach ein neues fest.';
        if (!passwordDialog.open) passwordDialog.showModal();
        requestAnimationFrame(() => (passwordChangeForced ? byId('newPassword') : byId('currentPassword')).focus());
    }

    function renderAdminPanel() {
        if (!state.auth.is_admin || !state.admin) return;
        const list = byId('adminUserList');
        list.replaceChildren();
        for (const user of state.admin.users || []) {
            const card = document.createElement('div');
            card.className = 'admin-user-card';
            const info = document.createElement('div');
            const title = document.createElement('strong');
            title.textContent = user.username;
            if (user.is_admin) {
                const badge = document.createElement('span');
                badge.className = 'account-badge admin';
                badge.textContent = 'Admin';
                title.appendChild(badge);
            }
            const details = document.createElement('small');
            details.textContent = `Spieler: ${user.player_name || 'nicht zugeordnet'}${user.must_change_password ? ' · Passwortänderung offen' : ''}`;
            info.append(title, details);
            const edit = document.createElement('button');
            edit.type = 'button';
            edit.className = 'compact-button';
            edit.textContent = 'Bearbeiten';
            edit.addEventListener('click', () => openAdminUserDialog(user));
            card.append(info, edit);
            list.appendChild(card);
        }
        byId('adminPlayerNames').value = (state.admin.admin_player_names || []).join('\n');
        byId('adminTheme').value = state.admin.theme || state.settings.theme || 'default';
    }

    function openAdminDialog() {
        if (!state.auth.is_admin) return;
        renderAdminPanel();
        adminDialog.showModal();
    }

    function openAdminUserDialog(user = null) {
        const editMode = Boolean(user);
        byId('adminUserForm').reset();
        byId('adminUserId').value = user?.id || '';
        byId('adminUsername').value = user?.username || '';
        byId('adminUserPlayerName').value = user?.player_name || '';
        byId('adminUserDialogTitle').textContent = editMode ? 'Benutzer bearbeiten' : 'Benutzer anlegen';
        byId('adminUserDialogSubtitle').textContent = editMode
            ? 'Benutzername und Spielerzuordnung ändern oder ein vorläufiges neues Passwort setzen.'
            : 'Der Benutzer muss das vorläufige Passwort nach der ersten Anmeldung ändern.';
        byId('adminUserPasswordLabel').textContent = editMode ? 'Neues Passwort (optional)' : 'Vorläufiges Passwort';
        byId('adminPasswordHelp').textContent = editMode
            ? 'Bleibt das Passwortfeld leer, wird das bisherige Passwort beibehalten. Beim Zurücksetzen gelten keine Komplexitätsregeln — der Benutzer muss beim nächsten Login ohnehin ein eigenes, regelkonformes Passwort festlegen.'
            : 'Beim Anlegen ist ein Passwort mit mindestens 8 Zeichen, einem Buchstaben, einer Zahl und einem Sonderzeichen wie !, ?, #, +, -, _, @ oder € erforderlich. Umlaute zählen als Buchstaben. Der Benutzer wird nach der ersten Anmeldung zur Änderung aufgefordert.';
        byId('adminUserPassword').required = !editMode;
        byId('adminUserPasswordConfirmation').required = !editMode;
        byId('adminUserPassword').minLength = editMode ? 0 : 8;
        byId('adminUserPasswordConfirmation').minLength = editMode ? 0 : 8;
        byId('deleteUserButton').hidden = !editMode;
        adminUserDialog.showModal();
        requestAnimationFrame(() => byId('adminUsername').focus());
    }

    byId('installAppButton').addEventListener('click', installApp);
    byId('infoButton').addEventListener('click', () => infoDialog.showModal());
    byId('loginButton').addEventListener('click', openLoginDialog);
    byId('registerButton').addEventListener('click', openRegisterDialog);
    byId('profileButton').addEventListener('click', openProfileDialog);
    byId('adminButton').addEventListener('click', openAdminDialog);
    byId('addDateButton').addEventListener('click', openDateDialog);
    byId('addPlayerButton').addEventListener('click', () => openPlayerDialog());
    byId('openPasswordButton').addEventListener('click', () => {
        profileDialog.close();
        openPasswordDialog(false);
    });
    byId('removeAvatarButton').addEventListener('click', () => {
        profileAvatarData = '';
        byId('profileAvatarInput').value = '';
        setAvatarPreview('', byId('profilePlayerName').value);
    });
    byId('profileAvatarInput').addEventListener('change', async event => {
        const file = event.target.files?.[0];
        if (!file) return;
        try {
            profileAvatarData = await readImageFile(file);
            setAvatarPreview(profileAvatarData, byId('profilePlayerName').value);
            showToast('Avatar wurde vorbereitet.');
        } catch (error) {
            profileAvatarData = state.auth.user?.avatar || '';
            setAvatarPreview(profileAvatarData, byId('profilePlayerName').value);
            byId('profileAvatarInput').value = '';
            showToast(error.message || 'Das Bild konnte nicht verarbeitet werden.');
        }
    });
    byId('profilePlayerName').addEventListener('input', () => {
        if (!profileAvatarData) setAvatarPreview('', byId('profilePlayerName').value);
    });
    byId('createUserButton').addEventListener('click', () => openAdminUserDialog());

    byId('logoutButton').addEventListener('click', async () => {
        try {
            await api('logout', {});
            [installDialog, infoDialog, achievementEditDialog, profileDialog, passwordDialog, adminDialog, adminUserDialog, playerDialog, dateDialog, statusDialog].forEach(dialog => dialog.open && dialog.close());
            await loadPlan();
            showToast('Du wurdest abgemeldet.');
        } catch (error) { handleApiError(error); }
    });

    document.querySelectorAll('[data-close-dialog]').forEach(button => {
        button.addEventListener('click', () => {
            const dialog = byId(button.dataset.closeDialog);
            if (dialog === passwordDialog && passwordChangeForced) return;
            dialog.close();
        });
    });

    document.querySelectorAll('.status-choice').forEach(button => {
        button.addEventListener('click', () => {
            selectedStatus.value = button.dataset.status;
            updateStatusChoices();
        });
    });

    byId('loginForm').addEventListener('submit', async event => {
        event.preventDefault();
        try {
            const data = await api('login', {
                username: byId('loginUsername').value,
                password: byId('loginPassword').value,
                remember: byId('loginRemember').checked
            });
            loginDialog.close();
            applyData(data);
            showToast('Anmeldung erfolgreich.');
        } catch (error) { handleApiError(error); }
    });

    byId('registerForm').addEventListener('submit', async event => {
        event.preventDefault();
        const password = byId('registerPassword').value;
        const passwordConfirmation = byId('registerPasswordConfirmation').value;
        const passwordError = passwordValidationMessage(password, passwordConfirmation);
        if (passwordError) return showToast(passwordError);
        try {
            const data = await api('register', {
                username: byId('registerUsername').value,
                player_name: byId('registerPlayerName').value,
                password,
                password_confirmation: passwordConfirmation,
                remember: byId('registerRemember').checked
            });
            registerDialog.close();
            applyData(data);
            showToast(state.auth.is_admin ? 'Erster Administrator wurde eingerichtet.' : 'Account wurde angelegt.');
        } catch (error) { handleApiError(error); }
    });

    byId('profileForm').addEventListener('submit', async event => {
        event.preventDefault();
        const weekdays = [...document.querySelectorAll('#profileWeekdays input:checked')].map(input => Number(input.value));
        try {
            const data = await api('update_profile', {
                player_name: byId('profilePlayerName').value,
                default_weekdays: weekdays,
                avatar: profileAvatarData
            });
            profileDialog.close();
            applyData(data);
            showToast('Dein Account wurde gespeichert.');
        } catch (error) { handleApiError(error); }
    });

    byId('passwordForm').addEventListener('submit', async event => {
        event.preventDefault();
        const newPassword = byId('newPassword').value;
        const newPasswordConfirmation = byId('newPasswordConfirmation').value;
        const passwordError = passwordValidationMessage(newPassword, newPasswordConfirmation);
        if (passwordError) return showToast(passwordError);
        try {
            const data = await api('change_password', {
                current_password: byId('currentPassword').value,
                new_password: newPassword,
                new_password_confirmation: newPasswordConfirmation
            });
            passwordChangeForced = false;
            passwordDialog.close();
            applyData(data);
            showToast('Dein Passwort wurde geändert.');
        } catch (error) { handleApiError(error); }
    });

    dateForm.addEventListener('submit', async event => {
        event.preventDefault();
        const eventDate = eventDateInput.value;
        if (!eventDate) return eventDateInput.focus();
        const weekday = parseLocalDate(eventDate).getDay();
        if (weekday === 0 || weekday === 3) {
            showToast('Mittwoche und Sonntage werden automatisch angezeigt.');
            return;
        }
        try {
            applyData(await api('create_event_date', { event_date: eventDate }));
            dateDialog.close();
            showToast('Spieltag wurde hinzugefügt.');
        } catch (error) { handleApiError(error); }
    });

    playerForm.addEventListener('submit', async event => {
        event.preventDefault();
        const name = playerName.value.trim();
        if (!name) return playerName.focus();
        try {
            const action = playerId.value ? 'update_player' : 'create_player';
            const data = await api(action, { id: playerId.value || undefined, name });
            playerDialog.close();
            applyData(data);
            showToast(playerId.value ? 'Spieler wurde geändert.' : 'Spieler wurde hinzugefügt.');
        } catch (error) { handleApiError(error); }
    });

    deletePlayerButton.addEventListener('click', async () => {
        const player = state.players.find(item => String(item.id) === String(playerId.value));
        if (!player || !confirm(`„${player.name}“ samt aller Termineinträge wirklich löschen? Ein verknüpfter Account bleibt bestehen, besitzt danach aber zunächst keinen Spieler.`)) return;
        try {
            const data = await api('delete_player', { id: player.id });
            playerDialog.close();
            applyData(data);
            showToast('Spieler wurde gelöscht.');
        } catch (error) { handleApiError(error); }
    });

    statusForm.addEventListener('submit', async event => {
        event.preventDefault();
        try {
            const data = await api('set_status', {
                player_id: Number(byId('statusPlayerId').value),
                event_date: byId('statusDate').value,
                status: selectedStatus.value,
                game: statusGame.value.trim(),
                note: statusNote.value.trim()
            });
            statusDialog.close();
            applyData(data);
            showToast('Verfügbarkeit wurde gespeichert.');
        } catch (error) { handleApiError(error); }
    });

    byId('adminUserForm').addEventListener('submit', async event => {
        event.preventDefault();
        const editMode = Boolean(byId('adminUserId').value);
        const password = byId('adminUserPassword').value;
        const passwordConfirmation = byId('adminUserPasswordConfirmation').value;
        if (!editMode) {
            const passwordError = passwordValidationMessage(password, passwordConfirmation);
            if (passwordError) return showToast(passwordError);
        } else if (password !== '' || passwordConfirmation !== '') {
            if (password === '') return showToast('Bitte ein Passwort festlegen.');
            if (password !== passwordConfirmation) return showToast('Die beiden Passwörter stimmen nicht überein.');
        }
        try {
            const data = await api(editMode ? 'admin_update_user' : 'admin_create_user', {
                user_id: byId('adminUserId').value || undefined,
                username: byId('adminUsername').value,
                player_name: byId('adminUserPlayerName').value,
                password,
                password_confirmation: passwordConfirmation
            });
            adminUserDialog.close();
            applyData(data);
            renderAdminPanel();
            showToast(editMode ? 'Benutzer wurde geändert.' : 'Benutzer wurde angelegt.');
        } catch (error) { handleApiError(error); }
    });

    byId('deleteUserButton').addEventListener('click', async () => {
        const userId = Number(byId('adminUserId').value);
        const user = state.admin?.users?.find(item => Number(item.id) === userId);
        if (!user || !confirm(`Benutzer „${user.username}“ wirklich löschen? Der Spieler und seine bisherigen Kalenderdaten bleiben erhalten.`)) return;
        try {
            const data = await api('admin_delete_user', { user_id: userId });
            adminUserDialog.close();
            applyData(data);
            if (!state.auth.logged_in) adminDialog.close();
            showToast('Benutzer wurde gelöscht.');
        } catch (error) { handleApiError(error); }
    });

    byId('adminSettingsForm').addEventListener('submit', async event => {
        event.preventDefault();
        try {
            const names = byId('adminPlayerNames').value.split(/[,;\n]+/).map(name => name.trim()).filter(Boolean);
            const data = await api('admin_save_settings', {
                admin_player_names: names,
                theme: byId('adminTheme').value
            });
            applyData(data);
            if (!state.auth.is_admin) adminDialog.close();
            showToast('Einstellungen wurden gespeichert.');
        } catch (error) { handleApiError(error); }
    });

    passwordDialog.addEventListener('cancel', event => {
        if (passwordChangeForced) event.preventDefault();
    });

    [installDialog, infoDialog, achievementEditDialog, loginDialog, registerDialog, profileDialog, passwordDialog, adminDialog, adminUserDialog, playerDialog, dateDialog, statusDialog].forEach(dialog => {
        dialog.addEventListener('click', event => {
            if (event.target !== dialog) return;
            if (dialog === passwordDialog && passwordChangeForced) return;
            dialog.close();
        });
    });

    window.addEventListener('beforeinstallprompt', event => {
        event.preventDefault();
        deferredInstallPrompt = event;
    });

    window.addEventListener('appinstalled', () => {
        deferredInstallPrompt = null;
        showToast('Der Kellerkinder-Kalender ist jetzt auf deinem Home-Bildschirm.');
    });

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('./service-worker.js').catch(() => {
                /* Die Website funktioniert auch ohne Service Worker weiter. */
            });
        });
    }

    (function setupPullToRefresh() {
        if (!isInstalledApp() || !('ontouchstart' in window)) return;

        const container = byId('pullRefresh');
        const arrow = container.querySelector('.pull-refresh-arrow');
        const THRESHOLD = 72;
        const MAX_PULL = 130;
        let startY = 0;
        let pulling = false;
        let ready = false;
        let refreshing = false;

        document.documentElement.style.overscrollBehaviorY = 'contain';

        const pageScrollTop = () => window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0;

        function reset() {
            pulling = false;
            ready = false;
            container.classList.remove('pull-refresh-visible', 'pull-refresh-ready');
            arrow.style.transform = 'rotate(0deg)';
        }

        window.addEventListener('touchstart', event => {
            if (refreshing || pageScrollTop() > 0 || event.touches.length !== 1) return;
            startY = event.touches[0].clientY;
            pulling = true;
        }, { passive: true });

        window.addEventListener('touchmove', event => {
            if (!pulling || refreshing) return;
            const distance = event.touches[0].clientY - startY;
            if (distance <= 0 || pageScrollTop() > 0) { reset(); return; }
            event.preventDefault();
            const pull = Math.min(MAX_PULL, distance * 0.5);
            ready = pull >= THRESHOLD;
            container.classList.add('pull-refresh-visible');
            container.classList.toggle('pull-refresh-ready', ready);
            arrow.style.transform = `rotate(${Math.min(180, (pull / THRESHOLD) * 180)}deg)`;
        }, { passive: false });

        window.addEventListener('touchend', () => {
            if (!pulling || refreshing) { pulling = false; return; }
            pulling = false;
            if (!ready) { reset(); return; }
            refreshing = true;
            container.classList.add('pull-refresh-loading');
            loadPlan().finally(() => {
                refreshing = false;
                container.classList.remove('pull-refresh-loading');
                reset();
            });
        });

        window.addEventListener('touchcancel', () => {
            refreshing = false;
            reset();
        });
    })();

    byId('achievementsPrev').addEventListener('click', () => {
        achievementPairIndex = (achievementPairIndex - 1 + achievementGames.length) % achievementGames.length;
        renderAchievementGrid();
    });
    byId('achievementsNext').addEventListener('click', () => {
        achievementPairIndex = (achievementPairIndex + 1) % achievementGames.length;
        renderAchievementGrid();
    });

    let lastRequestedDayCount = computeDesiredDayCount();
    let resizeReloadTimer = null;
    window.addEventListener('resize', () => {
        clearTimeout(resizeReloadTimer);
        resizeReloadTimer = setTimeout(() => {
            const desired = computeDesiredDayCount();
            if (desired !== lastRequestedDayCount) {
                lastRequestedDayCount = desired;
                loadPlan();
            }
        }, 400);
    });

    loadPlan();
    loadAchievements();
</script>
</body>
</html>
