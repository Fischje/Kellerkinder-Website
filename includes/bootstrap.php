<?php
// Gemeinsamer Vorspann für alle Seiten: Version, Hintergrundbilder,
// Sicherheits-Header und CSP-Nonce. Eingebunden von index.php und blog.php.
date_default_timezone_set('Europe/Berlin');
$appVersion = trim((string) @file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'VERSION'));
if ($appVersion === '') {
    $appVersion = '2.19.3';
}

/**
 * Liest Bilddateien (jpg/jpeg/png/webp) aus einem assets/backgrounds/*-Ordner.
 * $random = true: zufälliges Bild bei jedem Aufruf (Standard-Theme).
 * $random = false: immer dieselbe (alphabetisch erste) Datei (Sommer/Winter).
 * Gibt einen relativen URL-Pfad zurück, oder null, falls der Ordner leer ist.
 */
function pickBackgroundImage(string $folder, bool $random): ?string
{
    $directory = dirname(__DIR__) . '/assets/backgrounds/' . $folder;
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
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=()');
header(
    "Content-Security-Policy: default-src 'self'; "
    . "script-src 'self' 'nonce-{$cspNonce}' https://cdn.tailwindcss.com; "
    . "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; "
    . "img-src 'self' data: https://i.ytimg.com https://cdn.akamai.steamstatic.com https://shared.akamai.steamstatic.com https://shared.fastly.steamstatic.com https://cdn.cloudflare.steamstatic.com https://steamcdn-a.akamaihd.net https://media.rawg.io; "
    . "font-src 'self' https://fonts.gstatic.com; "
    . "connect-src 'self'; "
    . "worker-src 'self'; "
    . "manifest-src 'self'; "
    . "frame-ancestors 'none'; "
    . "base-uri 'self'; "
    . "form-action 'self'; "
    . "object-src 'none'"
);
