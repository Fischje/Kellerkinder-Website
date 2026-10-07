<?php
declare(strict_types=1);

// Hintergrundarbeiten der Website, nur auf der Kommandozeile, z. B. stündlich per Cron:
//
//   0 * * * * php /srv/kellerkinder-website/steam-refresh.php --force
//
// 1. Steam-Erfolge für das Widget „Steam-Erfolge“ holen (Zwischenspeicher in data/cache).
// 2. Symbole der Spiele aus der Spiele-Seite suchen und nach assets/game-icons laden.
//
// Ohne --force wird nur gearbeitet, wenn nötig (veraltet bzw. Symbole fehlen).
// --force      Steam-Erfolge in jedem Fall neu holen und erfolglose Spiele-Symbole erneut suchen
// --icons-only nur die Spiele-Symbole bearbeiten (so startet die Website es bei Bedarf selbst)
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

define('BLOG_FEED_ONLY', true);
require __DIR__ . '/api.php';

$store = readStore();
$force = in_array('--force', $argv, true);
$iconsOnly = in_array('--icons-only', $argv, true);

// ---- 1. Steam-Erfolge
if (!$iconsOnly) {
    if (!steamApiConfigured()) {
        echo "Steam-Erfolge: STEAM_API_KEY fehlt in config.php, übersprungen.\n";
    } else {
        $members = steamMembers($store);
        $cached = steamAchievementsCached($members);
        if ($members === []) {
            echo "Steam-Erfolge: noch kein Steam-Name hinterlegt.\n";
        } elseif (!$force && $cached !== null && $cached['fresh']) {
            echo "Steam-Erfolge: Zwischenspeicher ist aktuell.\n";
        } else {
            $start = microtime(true);
            if (!steamRefreshNow($store)) {
                echo "Steam-Erfolge: Es läuft bereits eine Aktualisierung.\n";
            } else {
                $result = steamAchievementsCached($members)['data'] ?? ['entries' => [], 'missing' => []];
                printf(
                    "Steam-Erfolge: %d Spieler abgefragt, %d Erfolge, %d ohne Erfolge, %.1f s.\n",
                    count($members),
                    count($result['entries']),
                    count($result['missing'] ?? []),
                    microtime(true) - $start
                );
            }
        }
    }
}

// ---- 2. Spiele-Symbole (alle jemals gespielten Spiele, ohne ausgeblendete Spieler)
if (!defined('BOT_STATS_URL') || BOT_STATS_URL === '') {
    echo "Spiele-Symbole: BOT_STATS_URL fehlt in config.php, übersprungen.\n";
    exit(0);
}
$stats = botStatsData(0, statsExcludedIds($store));
$names = is_array($stats['games'] ?? null) ? array_column($stats['games'], 'name') : [];
if ($names === []) {
    echo "Spiele-Symbole: noch keine Spiele in der Statistik.\n";
    exit(0);
}
$before = count(array_filter(array_map(static fn(string $n): ?string => gameIconUrl(gameIconIndex()[gameIconKey($n)] ?? null), $names)));
if (!gameIconsSyncLocked($names, 60, $force)) {
    echo "Spiele-Symbole: Es läuft bereits eine Suche.\n";
    exit(0);
}
$after = count(array_filter(array_map(static fn(string $n): ?string => gameIconUrl(gameIconIndex()[gameIconKey($n)] ?? null), $names)));
printf("Spiele-Symbole: %d von %d Spielen haben ein Symbol (%+d neu).\n", $after, count($names), $after - $before);
