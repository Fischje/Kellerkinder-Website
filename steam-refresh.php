<?php
declare(strict_types=1);

// Holt die Steam-Erfolge für das Widget „Steam-Erfolge“ und legt sie in den
// Zwischenspeicher (data/cache). Läuft nur auf der Kommandozeile, z. B. stündlich per Cron:
//
//   0 * * * * php /srv/kellerkinder-website/steam-refresh.php --force
//
// Ohne --force wird nur aktualisiert, wenn der Zwischenspeicher veraltet ist oder sich
// ein Steam-Name geändert hat. Die Website startet dieses Skript bei Bedarf auch selbst.
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

define('BLOG_FEED_ONLY', true);
require __DIR__ . '/api.php';

if (!steamApiConfigured()) {
    fwrite(STDERR, "STEAM_API_KEY fehlt in config.php.\n");
    exit(1);
}

$store = readStore();
$members = steamMembers($store);
$cached = steamAchievementsCached($members);
$force = in_array('--force', $argv, true);

if (!$force && $cached !== null && $cached['fresh']) {
    echo "Zwischenspeicher ist aktuell, nichts zu tun.\n";
    exit(0);
}
if ($members === []) {
    echo "Noch kein Steam-Name hinterlegt.\n";
    exit(0);
}

$start = microtime(true);
if (!steamRefreshNow($store)) {
    echo "Es läuft bereits eine Aktualisierung.\n";
    exit(0);
}
$result = steamAchievementsCached($members)['data'] ?? ['entries' => [], 'missing' => []];
printf(
    "%d Spieler abgefragt, %d Erfolge, %d ohne Erfolge, %.1f s.\n",
    count($members),
    count($result['entries']),
    count($result['missing'] ?? []),
    microtime(true) - $start
);
