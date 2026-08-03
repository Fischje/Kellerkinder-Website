<?php
declare(strict_types=1);

date_default_timezone_set('Europe/Berlin');
$appVersion = trim((string) @file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'VERSION'));
if ($appVersion === '') {
    $appVersion = '2.2.0';
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" id="themeColorMeta" content="#070914">
    <meta name="application-name" content="Kellerkinder-Online-Kalender">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Kellerkinder">
    <title>Kellerkinder-Online-Kalender</title>
    <meta name="description" content="Der gemeinsame Kellerkinder-Online-Kalender für eure Spieltage.">
    <link rel="icon" href="assets/kellerkinder-logo.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png">
    <link rel="manifest" href="manifest.webmanifest">
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

        body[data-theme="summer"] {
            --bg: #061619;
            --bg-soft: #0c2628;
            --panel: rgba(9, 43, 47, 0.88);
            --panel-strong: rgba(8, 35, 42, 0.96);
            --panel-soft: rgba(19, 62, 60, 0.82);
            --line: rgba(151, 233, 215, 0.24);
            --line-strong: rgba(255, 214, 133, 0.42);
            --text: #f7fff8;
            --muted: #b8d7ce;
            --cyan: #45f0dd;
            --blue: #3aa8ff;
            --violet: #6fd287;
            --pink: #ff9d5c;
            --green: #53e78e;
            --orange: #ffd166;
            --red: #ff6f6f;
            --vacation: #ffd166;
            --shadow: 0 22px 65px rgba(0, 32, 34, 0.5);
            --glow: 0 0 24px rgba(69, 240, 221, 0.16), 0 0 48px rgba(255, 209, 102, 0.1);
        }

        body[data-theme="winter"] {
            --bg: #030812;
            --bg-soft: #081321;
            --panel: rgba(8, 19, 34, 0.9);
            --panel-strong: rgba(7, 16, 30, 0.97);
            --panel-soft: rgba(18, 35, 58, 0.82);
            --line: rgba(177, 222, 255, 0.25);
            --line-strong: rgba(230, 242, 255, 0.45);
            --text: #f6fbff;
            --muted: #b7c8da;
            --cyan: #9de9ff;
            --blue: #5c96ff;
            --violet: #d9f2ff;
            --pink: #ff5b6d;
            --green: #4be39b;
            --orange: #f8d56c;
            --red: #ff4058;
            --vacation: #9de9ff;
            --shadow: 0 22px 65px rgba(0, 10, 24, 0.62);
            --glow: 0 0 24px rgba(157, 233, 255, 0.18), 0 0 52px rgba(255, 64, 88, 0.1);
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

        body[data-theme="summer"] {
            background:
                radial-gradient(circle at 50% -15%, rgba(255, 209, 102, .24), transparent 33rem),
                linear-gradient(180deg, #06383d 0%, #082420 58%, #051316 100%);
        }

        body[data-theme="winter"] {
            background:
                radial-gradient(circle at 50% -16%, rgba(180, 230, 255, .28), transparent 34rem),
                linear-gradient(180deg, #07182b 0%, #030812 60%, #07101c 100%);
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

        body[data-theme="summer"]::before {
            background:
                radial-gradient(circle at 16% 20%, rgba(69, 240, 221, .24) 0 0, transparent 22rem),
                radial-gradient(circle at 84% 18%, rgba(255, 209, 102, .22) 0 0, transparent 25rem),
                radial-gradient(circle at 72% 78%, rgba(68, 214, 116, .19) 0 0, transparent 28rem),
                radial-gradient(circle at 18% 84%, rgba(255, 157, 92, .18) 0 0, transparent 26rem);
        }

        body[data-theme="winter"]::before {
            background:
                radial-gradient(circle at 16% 22%, rgba(157, 233, 255, .22) 0 0, transparent 23rem),
                radial-gradient(circle at 82% 18%, rgba(255, 64, 88, .2) 0 0, transparent 24rem),
                radial-gradient(circle at 72% 78%, rgba(92, 150, 255, .22) 0 0, transparent 28rem),
                radial-gradient(circle at 20% 82%, rgba(245, 255, 255, .16) 0 0, transparent 27rem);
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

        body[data-theme="summer"]::after {
            opacity: .34;
            background-image:
                linear-gradient(rgba(170, 255, 225, .06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(170, 255, 225, .06) 1px, transparent 1px),
                radial-gradient(circle, rgba(255,255,255,.08) 1px, transparent 1.5px);
            background-size: 46px 46px, 46px 46px, 26px 26px;
        }

        body[data-theme="winter"]::after {
            opacity: .42;
            background-image:
                linear-gradient(rgba(180, 220, 255, .06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(180, 220, 255, .06) 1px, transparent 1px),
                radial-gradient(circle, rgba(245,255,255,.18) 1px, transparent 1.8px);
            background-size: 44px 44px, 44px 44px, 28px 28px;
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
        body[data-theme="summer"] .summer-icons,
        body[data-theme="summer"] .summer-bubbles,
        body[data-theme="winter"] .winter-snow,
        body[data-theme="winter"] .winter-snowbank,
        body[data-theme="winter"] .winter-tree,
        body[data-theme="winter"] .winter-fireplace {
            display: block;
        }

        .summer-sun {
            top: clamp(18px, 7vh, 64px);
            right: clamp(18px, 6vw, 92px);
            width: clamp(88px, 16vw, 170px);
            aspect-ratio: 1;
            border-radius: 50%;
            background:
                radial-gradient(circle at 35% 32%, #fff8c7 0 16%, #ffd166 17% 58%, #ff9f3d 59% 100%);
            box-shadow:
                0 0 44px rgba(255, 209, 102, .55),
                0 0 110px rgba(255, 157, 92, .28);
            opacity: .88;
            animation: sunFloat 7s ease-in-out infinite;
        }

        .summer-sun::before {
            content: "";
            position: absolute;
            inset: -22px;
            border-radius: 50%;
            background: repeating-conic-gradient(from 0deg, rgba(255, 224, 122, .55) 0 8deg, transparent 8deg 18deg);
            filter: blur(.5px);
            animation: sunSpin 24s linear infinite;
        }

        .summer-waves {
            left: -6vw;
            right: -6vw;
            bottom: -44px;
            height: clamp(116px, 18vh, 190px);
            opacity: .72;
            background:
                radial-gradient(80px 34px at 5% 22%, rgba(156, 255, 240, .65) 0 42%, transparent 43%),
                radial-gradient(86px 36px at 17% 32%, rgba(88, 218, 255, .55) 0 42%, transparent 43%),
                radial-gradient(92px 38px at 31% 25%, rgba(156, 255, 240, .58) 0 42%, transparent 43%),
                radial-gradient(84px 34px at 46% 34%, rgba(88, 218, 255, .52) 0 42%, transparent 43%),
                radial-gradient(98px 40px at 63% 24%, rgba(156, 255, 240, .58) 0 42%, transparent 43%),
                radial-gradient(86px 36px at 79% 34%, rgba(88, 218, 255, .55) 0 42%, transparent 43%),
                radial-gradient(100px 40px at 96% 25%, rgba(156, 255, 240, .58) 0 42%, transparent 43%),
                linear-gradient(180deg, transparent 0 28%, rgba(25, 152, 172, .52) 29% 58%, rgba(7, 77, 99, .72) 100%);
            filter: drop-shadow(0 -8px 22px rgba(69, 240, 221, .16));
            animation: waveMove 8s ease-in-out infinite alternate;
        }

        .summer-icons {
            left: clamp(14px, 5vw, 78px);
            bottom: clamp(70px, 13vh, 145px);
            width: min(430px, 78vw);
            min-height: 78px;
            opacity: .9;
        }

        .summer-icons::before {
            content: "≋  ☀  💧  ≋  🏖";
            position: absolute;
            left: 0;
            bottom: 0;
            color: #dffff7;
            font-size: clamp(1.35rem, 4vw, 2.45rem);
            letter-spacing: .18em;
            text-shadow: 0 0 16px rgba(69, 240, 221, .35), 0 4px 18px rgba(0, 0, 0, .45);
        }

        .summer-bubbles {
            inset: 0;
            opacity: .45;
            background-image:
                radial-gradient(circle, rgba(202,255,247,.48) 0 3px, transparent 4px),
                radial-gradient(circle, rgba(255,244,184,.35) 0 2px, transparent 3px),
                radial-gradient(circle, rgba(108,225,255,.36) 0 4px, transparent 5px);
            background-size: 140px 160px, 210px 190px, 260px 230px;
            background-position: 10% 20%, 80% 32%, 50% 70%;
            animation: bubbleDrift 13s ease-in-out infinite alternate;
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
            bottom: -38px;
            height: clamp(96px, 16vh, 160px);
            opacity: .86;
            background:
                radial-gradient(170px 54px at 8% 24%, rgba(255,255,255,.92) 0 58%, transparent 59%),
                radial-gradient(220px 68px at 28% 18%, rgba(220,243,255,.9) 0 57%, transparent 58%),
                radial-gradient(190px 56px at 53% 28%, rgba(255,255,255,.9) 0 58%, transparent 59%),
                radial-gradient(240px 72px at 78% 17%, rgba(220,243,255,.88) 0 57%, transparent 58%),
                radial-gradient(190px 58px at 97% 30%, rgba(255,255,255,.9) 0 58%, transparent 59%),
                linear-gradient(180deg, transparent 0 30%, rgba(214,239,255,.88) 31% 100%);
            filter: drop-shadow(0 -8px 24px rgba(200, 238, 255, .18));
        }

        .winter-tree {
            bottom: clamp(62px, 11vh, 118px);
            width: clamp(64px, 10vw, 112px);
            height: clamp(104px, 17vw, 184px);
            opacity: .9;
            filter: drop-shadow(0 0 20px rgba(157, 233, 255, .18));
        }

        .winter-tree.left { left: clamp(10px, 4vw, 70px); }
        .winter-tree.right {
            right: clamp(10px, 5vw, 96px);
            transform: scale(.82);
            opacity: .72;
        }

        .winter-tree::before,
        .winter-tree::after {
            content: "";
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .winter-tree::before {
            bottom: 20%;
            width: 18%;
            height: 25%;
            border-radius: 3px;
            background: linear-gradient(180deg, #7a4b2f, #3e2418);
        }

        .winter-tree::after {
            bottom: 26%;
            width: 100%;
            height: 74%;
            background:
                linear-gradient(135deg, transparent 0 50%, rgba(255,255,255,.88) 51% 56%, transparent 57%) 50% 5% / 70% 30% no-repeat,
                linear-gradient(45deg, transparent 0 50%, rgba(255,255,255,.8) 51% 56%, transparent 57%) 50% 38% / 88% 30% no-repeat,
                linear-gradient(135deg, transparent 0 50%, rgba(255,255,255,.72) 51% 56%, transparent 57%) 50% 72% / 100% 30% no-repeat,
                linear-gradient(135deg, transparent 0 50%, #0f684a 51%) 50% 4% / 70% 30% no-repeat,
                linear-gradient(135deg, transparent 0 50%, #0b563e 51%) 50% 38% / 88% 30% no-repeat,
                linear-gradient(135deg, transparent 0 50%, #094530 51%) 50% 72% / 100% 30% no-repeat;
            clip-path: polygon(50% 0, 82% 32%, 70% 32%, 96% 65%, 78% 65%, 100% 100%, 0 100%, 22% 65%, 4% 65%, 30% 32%, 18% 32%);
        }

        .winter-fireplace {
            right: clamp(18px, 6vw, 90px);
            top: clamp(150px, 25vh, 260px);
            width: clamp(120px, 18vw, 190px);
            height: clamp(92px, 14vw, 140px);
            border: 5px solid rgba(153, 78, 45, .82);
            border-top-width: 15px;
            border-radius: 12px 12px 8px 8px;
            background:
                radial-gradient(circle at 49% 66%, rgba(255, 219, 118, .98) 0 10%, rgba(255, 105, 43, .96) 11% 24%, transparent 25%),
                radial-gradient(ellipse at 48% 84%, #2b130d 0 44%, #100707 45% 100%);
            box-shadow: 0 0 26px rgba(255, 120, 50, .22), inset 0 0 0 2px rgba(255,255,255,.08);
            opacity: .82;
        }

        .winter-fireplace::before {
            content: "";
            position: absolute;
            left: -13px;
            right: -13px;
            top: -26px;
            height: 12px;
            border-radius: 7px;
            background: linear-gradient(180deg, #885335, #4c2b1e);
            box-shadow:
                24px 16px 0 -3px #d93045,
                52px 16px 0 -3px #f4f7ff,
                80px 16px 0 -3px #d93045;
        }

        .winter-fireplace::after {
            content: "";
            position: absolute;
            left: 42%;
            bottom: 20%;
            width: 24%;
            height: 44%;
            border-radius: 60% 40% 52% 48%;
            background: linear-gradient(180deg, #fff3a6 0 24%, #ffb33f 25% 60%, #ff5a32 61% 100%);
            filter: drop-shadow(0 0 14px rgba(255, 163, 54, .68));
            animation: fireFlicker .9s ease-in-out infinite alternate;
        }

        @keyframes rgbDrift {
            0% { transform: translate3d(-1.5%, -1%, 0) scale(1); }
            50% { transform: translate3d(2%, 1.5%, 0) scale(1.04); }
            100% { transform: translate3d(-.5%, 2.5%, 0) scale(1.02); }
        }

        @keyframes sunFloat {
            0%, 100% { transform: translateY(0) rotate(-2deg); }
            50% { transform: translateY(8px) rotate(2deg); }
        }

        @keyframes sunSpin {
            to { transform: rotate(360deg); }
        }

        @keyframes waveMove {
            0% { transform: translateX(-18px); }
            100% { transform: translateX(18px); }
        }

        @keyframes bubbleDrift {
            0% { transform: translate3d(-8px, 12px, 0); }
            100% { transform: translate3d(12px, -8px, 0); }
        }

        @keyframes snowFall {
            from { transform: translate3d(0, -8vh, 0); }
            to { transform: translate3d(0, 18vh, 0); }
        }

        @keyframes fireFlicker {
            0% { transform: translateX(-1px) scaleY(.92) rotate(-4deg); opacity: .86; }
            100% { transform: translateX(2px) scaleY(1.08) rotate(5deg); opacity: 1; }
        }

        @keyframes glowPulse {
            0%, 100% { opacity: .72; filter: saturate(105%); }
            50% { opacity: 1; filter: saturate(135%); }
        }

        button, input, select { font: inherit; }
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

        body[data-theme="summer"] .masthead {
            background:
                linear-gradient(var(--panel-strong), var(--panel-strong)) padding-box,
                linear-gradient(110deg, rgba(69,240,221,.72), rgba(255,209,102,.58), rgba(255,157,92,.7)) border-box;
        }

        body[data-theme="winter"] .masthead {
            background:
                linear-gradient(var(--panel-strong), var(--panel-strong)) padding-box,
                linear-gradient(110deg, rgba(157,233,255,.78), rgba(92,150,255,.42), rgba(255,64,88,.68)) border-box;
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

        body[data-theme="summer"] .masthead::after {
            background:
                radial-gradient(circle at 12% 22%, rgba(255, 209, 102, .22) 0 4px, transparent 5px),
                radial-gradient(circle at 88% 28%, rgba(69, 240, 221, .2) 0 5px, transparent 6px),
                linear-gradient(90deg, transparent 0 12%, rgba(255, 209, 102, .2) 13% 17%, transparent 18% 100%);
            box-shadow: inset 0 -9px 0 rgba(255, 214, 133, .08), inset 0 -15px 0 rgba(69, 240, 221, .06);
        }

        body[data-theme="winter"] .masthead::after {
            background:
                radial-gradient(28px 9px at 8% 0, rgba(255,255,255,.94) 0 60%, transparent 61%),
                radial-gradient(42px 12px at 24% 0, rgba(225,244,255,.92) 0 60%, transparent 61%),
                radial-gradient(34px 10px at 44% 0, rgba(255,255,255,.92) 0 60%, transparent 61%),
                radial-gradient(46px 14px at 69% 0, rgba(225,244,255,.9) 0 60%, transparent 61%),
                radial-gradient(32px 10px at 88% 0, rgba(255,255,255,.9) 0 60%, transparent 61%);
            box-shadow: inset 0 10px 0 rgba(242, 250, 255, .12), inset 0 1px 0 rgba(255,255,255,.16);
        }

        .install-app-button {
            position: absolute;
            z-index: 5;
            top: 11px;
            right: 11px;
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            padding: 7px;
            border: 1px solid rgba(126, 229, 255, .48);
            border-radius: 11px;
            color: #dffaff;
            background: rgba(9, 14, 28, .78);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.1), 0 0 18px rgba(53,231,255,.15);
            cursor: pointer;
            transition: transform .14s ease, filter .14s ease, box-shadow .14s ease;
        }

        .install-app-button:hover {
            transform: translateY(-1px) scale(1.04);
            filter: brightness(1.18);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.14), 0 0 24px rgba(53,231,255,.28);
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
            filter: drop-shadow(0 0 6px rgba(255,79,145,.55));
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

        body[data-theme="summer"] .primary-button {
            border-color: rgba(255, 222, 118, .68);
            background: linear-gradient(115deg, #058f91, #3aa85d 58%, #e9902f);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.24), 0 0 20px rgba(255,209,102,.22), 0 7px 18px rgba(0,0,0,.3);
        }

        body[data-theme="winter"] .primary-button {
            border-color: rgba(180, 236, 255, .68);
            background: linear-gradient(115deg, #165e9c, #3650b8 58%, #b51f3d);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.24), 0 0 20px rgba(157,233,255,.24), 0 7px 18px rgba(0,0,0,.36);
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

        body[data-theme="summer"] .board {
            background:
                linear-gradient(var(--panel-strong), var(--panel-strong)) padding-box,
                linear-gradient(115deg, rgba(69,240,221,.4), rgba(255,209,102,.22) 48%, rgba(255,157,92,.36)) border-box;
        }

        body[data-theme="winter"] .board {
            background:
                linear-gradient(var(--panel-strong), var(--panel-strong)) padding-box,
                linear-gradient(115deg, rgba(157,233,255,.42), rgba(92,150,255,.22) 48%, rgba(255,64,88,.35)) border-box;
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

        .status-button .game {
            position: relative;
            z-index: 1;
            max-width: 94px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: #fff1a8;
            font-size: .64rem;
            font-weight: 900;
            text-shadow: 0 0 8px rgba(255, 214, 92, .28);
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
            background: rgba(10,13,24,.82);
            box-shadow: inset 0 1px 0 rgba(255,255,255,.04), 0 0 24px rgba(53,231,255,.06);
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
            border: 1px solid rgba(53,231,255,.42);
            object-fit: cover;
            background:
                radial-gradient(circle at 35% 26%, rgba(255,255,255,.32), transparent 23px),
                linear-gradient(135deg, rgba(53,231,255,.2), rgba(169,92,255,.22)),
                #090d19;
            box-shadow: inset 0 1px 0 rgba(255,255,255,.08), 0 0 14px rgba(53,231,255,.12);
        }

        .avatar-placeholder {
            display: inline-grid;
            place-items: center;
            color: #cfefff;
            font-size: .78rem;
            font-weight: 900;
        }

        .account-avatar {
            width: 42px;
            height: 42px;
        }

        .player-title {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-width: 0;
        }

        .player-title .avatar,
        .player-title .avatar-placeholder {
            width: 28px;
            height: 28px;
        }

        .player-name {
            min-width: 0;
            overflow-wrap: anywhere;
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
            border-color: rgba(53,231,255,.62);
            box-shadow: 0 0 16px rgba(53,231,255,.1);
        }

        .account-badge,
        .player-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-left: 5px;
            padding: 2px 7px;
            border: 1px solid rgba(53,231,255,.3);
            border-radius: 999px;
            color: #a9ecff;
            background: rgba(16,83,105,.25);
            font-size: .68rem;
            font-weight: 900;
            vertical-align: middle;
        }

        .account-badge.admin,
        .player-badge.admin {
            border-color: rgba(255,79,200,.38);
            color: #ffb5e9;
            background: rgba(120,32,93,.25);
        }

        .setup-callout,
        .password-callout {
            margin: 14px 0;
            padding: 13px 15px;
            border: 1px solid rgba(53,231,255,.4);
            border-radius: 12px;
            color: #dff9ff;
            background: rgba(10,69,86,.42);
            line-height: 1.5;
        }

        .password-callout {
            border-color: rgba(255,173,66,.48);
            color: #ffe1ad;
            background: rgba(84,48,10,.52);
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
            position: relative;
            z-index: 1;
            padding: 1px 5px;
            border: 1px solid rgba(255,255,255,.22);
            border-radius: 999px;
            color: rgba(255,255,255,.82);
            background: rgba(0,0,0,.2);
            font-size: .52rem;
            font-weight: 900;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        dialog.wide { width: min(760px, calc(100% - 24px)); }

        .form-stack { display: grid; gap: 15px; }
        .form-row { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; }
        .field-help { margin: -2px 0 0; color: #909ab4; font-size: .84rem; line-height: 1.42; }
        .section-heading { margin: 5px 0 0; color: #a9ecff; font-size: 1.02rem; }

        input[type="text"],
        input[type="password"],
        input[type="date"],
        select,
        textarea {
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

        select {
            color-scheme: dark;
            cursor: pointer;
        }

        textarea { min-height: 104px; resize: vertical; }

        input::placeholder,
        textarea::placeholder { color: #6f7890; }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: rgba(53,231,255,.72);
            box-shadow: 0 0 0 3px rgba(53,231,255,.11), 0 0 18px rgba(53,231,255,.1);
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
            border-color: rgba(53,231,255,.7);
            color: #fff;
            background: linear-gradient(135deg, rgba(20,124,163,.76), rgba(101,80,211,.72));
            box-shadow: 0 0 14px rgba(53,231,255,.13);
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
            .page-shell { padding: 10px 8px 30px; }
            .masthead { padding: 21px 12px 20px; border-radius: 14px; }
            .crest { width: 88px; }
            .subtitle-group { margin-top: 12px; gap: 5px; }
            .subtitle { font-size: 1rem; }
            .account-strip { align-items: stretch; padding: 12px; }
            .account-actions { width: 100%; justify-content: stretch; }
            .account-actions > button { flex: 1 1 130px; }
            .account-summary { width: 100%; }
            .form-row { grid-template-columns: 1fr; }
            .weekday-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .admin-user-card { align-items: flex-start; }
            .toolbar { align-items: stretch; }
            .toolbar-actions { width: 100%; }
            .toolbar-actions > button { flex: 1 1 190px; }
            .legend { width: 100%; gap: 6px; }
            .legend-item { flex: 1 1 calc(50% - 6px); justify-content: center; padding: 6px 8px; }
            table { min-width: 790px; }
            .player-heading, .player-cell { width: 125px; min-width: 125px; }
            .player-heading { padding-left: 9px; }
            .player-title { gap: 5px; }
            .player-title .avatar,
            .player-title .avatar-placeholder { width: 24px; height: 24px; }
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
<body data-theme="default">
<div class="season-scene" aria-hidden="true">
    <div class="summer-sun"></div>
    <div class="summer-bubbles"></div>
    <div class="summer-icons"></div>
    <div class="summer-waves"></div>
    <div class="winter-snow"></div>
    <div class="winter-tree left"></div>
    <div class="winter-tree right"></div>
    <div class="winter-fireplace"></div>
    <div class="winter-snowbank"></div>
</div>
<main class="page-shell">
    <header class="masthead">
        <button class="install-app-button" id="installAppButton" type="button" title="Als App zum Home-Bildschirm hinzufügen" aria-label="Kellerkinder-Kalender als App zum Home-Bildschirm hinzufügen">
            <img src="assets/smartphone-install.svg" alt="">
        </button>
        <div class="crest">
            <img src="assets/kellerkinder-logo.svg" alt="Kellerkinder Gaming-Logo">
        </div>
        <h1>Kellerkinder-Online-Kalender</h1>
        <div class="subtitle-group">
            <p class="subtitle">Wer ist wann da und zockt mit</p>
        </div>
    </header>

    <section class="account-strip" aria-label="Benutzerkonto">
        <div class="account-summary" id="accountSummary">
            <strong>Kalender wird geladen …</strong>
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

    <section class="toolbar" aria-label="Steuerung und Legende">
        <div class="legend" aria-label="Status-Legende">
            <span class="legend-item"><span class="legend-icon online">⚔</span> Online</span>
            <span class="legend-item"><span class="legend-icon late">◷</span> Später</span>
            <span class="legend-item"><span class="legend-icon absent">✕</span> Verhindert</span>
            <span class="legend-item"><span class="legend-icon vacation">☀</span> Urlaub</span>
            <span class="legend-item"><span class="legend-icon open">?</span> Offen</span>
        </div>
        <div class="toolbar-actions">
            <button class="secondary-button" id="addDateButton" type="button" hidden>＋ Spieltag hinzufügen</button>
            <button class="primary-button" id="addPlayerButton" type="button" hidden>＋ Spieler hinzufügen</button>
        </div>
    </section>

    <div id="storageWarning" class="storage-warning" hidden>
        <strong>Nur-Lese-Modus:</strong> Der Plan ist sichtbar, Änderungen können aber nicht gespeichert werden.
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
    </section>

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

<script>
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

    async function api(action, payload = null) {
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

    async function loadPlan() {
        try {
            applyData(await api('bootstrap'));
        } catch (error) {
            loading.textContent = 'Der Plan konnte nicht geladen werden.';
            showToast(error.message);
        }
    }

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
        const table = dateHeaderRow.closest('table');
        table.style.minWidth = `${160 + (state.eventDates.length * 112)}px`;

        while (dateHeaderRow.children.length > 1) dateHeaderRow.lastElementChild.remove();

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

            for (const eventDate of state.eventDates) {
                const date = eventDate.date;
                const dateLabel = formatDateParts(date).label;
                const cell = document.createElement('td');
                cell.className = 'status-cell';
                const entry = state.availability[`${player.id}:${date}`] || { status: '', note: '', game: '', source: '' };
                const meta = STATUS_META[entry.status] || STATUS_META[''];

                const button = document.createElement('button');
                button.type = 'button';
                button.className = `status-button ${meta.className}`;
                const entryDetails = [entry.game ? `Spiel: ${entry.game}` : '', entry.note || ''].filter(Boolean).join(', ');
                button.setAttribute('aria-label', `${player.name}, ${dateLabel}: ${meta.label}${entryDetails ? ', ' + entryDetails : ''}`);
                button.title = player.can_edit ? (entryDetails || `${meta.label} · Antippen zum Ändern`) : (entryDetails || meta.label);

                const icon = document.createElement('span');
                icon.className = 'icon';
                icon.textContent = meta.icon;
                const label = document.createElement('span');
                label.className = 'label';
                label.textContent = meta.label;
                button.append(icon, label);

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
            ? 'Bleibt das Passwortfeld leer, wird das bisherige Passwort beibehalten. Ein neues Passwort benötigt mindestens 8 Zeichen, einen Buchstaben, eine Zahl und ein Sonderzeichen. Danach wird der Benutzer beim nächsten Login zur erneuten Passwortänderung aufgefordert.'
            : 'Beim Anlegen ist ein Passwort mit mindestens 8 Zeichen, einem Buchstaben, einer Zahl und einem Sonderzeichen wie !, ?, #, +, -, _, @ oder € erforderlich. Umlaute zählen als Buchstaben. Der Benutzer wird nach der ersten Anmeldung zur Änderung aufgefordert.';
        byId('adminUserPassword').required = !editMode;
        byId('adminUserPasswordConfirmation').required = !editMode;
        byId('deleteUserButton').hidden = !editMode;
        adminUserDialog.showModal();
        requestAnimationFrame(() => byId('adminUsername').focus());
    }

    byId('installAppButton').addEventListener('click', installApp);
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
            [installDialog, profileDialog, passwordDialog, adminDialog, adminUserDialog, playerDialog, dateDialog, statusDialog].forEach(dialog => dialog.open && dialog.close());
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
                password: byId('loginPassword').value
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
                password_confirmation: passwordConfirmation
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
        if (!editMode || password !== '' || passwordConfirmation !== '') {
            const passwordError = passwordValidationMessage(password, passwordConfirmation);
            if (passwordError) return showToast(passwordError);
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

    [installDialog, loginDialog, registerDialog, profileDialog, passwordDialog, adminDialog, adminUserDialog, playerDialog, dateDialog, statusDialog].forEach(dialog => {
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

    loadPlan();
</script>
</body>
</html>
