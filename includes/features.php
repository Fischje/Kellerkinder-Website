<?php
// Schalter der zentralen Admin-Seite (admin.php): welche Kästen die Seitenleiste
// in welcher Reihenfolge zeigt, welche Seiten im Menü stehen und welche Teile
// die Startseite zeigt. Gespeichert in der Datendatei unter settings.features.
// Wird von api.php (Lesen/Speichern) und von den Seiten (Menü, Seitenleiste,
// Startseite) eingebunden. Die Seiten lesen die Datendatei nur lesend.

/** Kästen der Seitenleiste in Standard-Reihenfolge (die nächste Netzje steht unten).
 *  „Gerade online“ steht immer ganz oben und erscheint nur, wenn jemand spielt. */
const FEATURE_SIDEBAR_PINNED = 'now';
const FEATURE_SIDEBAR_BOXES = [
    'now' => 'Gerade online',
    'games' => 'Meist gespielt',
    'blog' => 'Neu im Blog',
    'servers' => 'Unsere Server',
    'netzje' => 'Nächste Netzje',
];

/** Seiten, auf denen Kästen der Seitenleiste erscheinen können (Schlüssel wie $activeNav). */
const FEATURE_SIDEBAR_PAGES = [
    'home' => 'Home',
    'calendar' => 'Kalender',
    'blog' => 'Blog',
    'games' => 'Spiele-Statistik',
    'servers' => 'Server',
    'netzje' => 'Netzje',
    'impressions' => 'Eindrücke',
    'admin' => 'Admin',
];

/**
 * Standard: Ein Kasten erscheint überall außer auf „seiner“ Seite (dort steht der Inhalt schon),
 * „Neu im Blog“ nicht auf Home (dort stehen die neuesten Beiträge unter dem Text) und auf der
 * Netzje-Seite gar keine Kästen (Wunsch: volle Breite für Party und Stundenplan).
 */
function featureSidebarDefaultPages(string $box): array
{
    $hidden = ['netzje', $box];
    if ($box === 'blog') {
        $hidden[] = 'home';
    }
    return array_values(array_diff(array_keys(FEATURE_SIDEBAR_PAGES), $hidden));
}

/** Seiten, die sich im Menü aus- und einblenden lassen (der Kalender bleibt immer). */
const FEATURE_PAGES = [
    'blog' => 'Blog',
    'games' => 'Spiele-Statistik',
    'servers' => 'Server',
    'netzje' => 'Netzje',
    'impressions' => 'Eindrücke (nur für Angemeldete)',
];

/** Teile der Seiten Home und Kalender (Schlüssel bleiben aus Kompatibilität gleich). */
const FEATURE_HOME = [
    'blog_teaser' => 'Home: „Neu im Blog“ (die drei neuesten Beiträge) unter dem Starttext',
    'achievements' => 'Kalender: Abschnitt „Unsere Erfolge“',
];

/**
 * Bringt gespeicherte Schalter in eine feste Form:
 * sidebar = Liste [box, enabled] in Anzeige-Reihenfolge (unbekannte fallen weg, fehlende kommen hinten dazu),
 * pages / home = [schlüssel => bool], fehlende Werte sind eingeschaltet.
 */
function normalizeFeatures($value): array
{
    $value = is_array($value) ? $value : [];
    $sidebar = [];
    foreach (is_array($value['sidebar'] ?? null) ? $value['sidebar'] : [] as $entry) {
        $box = is_array($entry) ? (string) ($entry['box'] ?? '') : '';
        if (isset(FEATURE_SIDEBAR_BOXES[$box]) && !isset($sidebar[$box])) {
            // pages: auf welchen Seiten der Kasten erscheint (fehlt die Angabe, gelten die Standardwerte)
            $pages = is_array($entry['pages'] ?? null)
                ? array_values(array_intersect(array_keys(FEATURE_SIDEBAR_PAGES), array_map('strval', $entry['pages'])))
                : featureSidebarDefaultPages($box);
            $sidebar[$box] = ['box' => $box, 'enabled' => (bool) ($entry['enabled'] ?? true), 'pages' => $pages];
        }
    }
    foreach (array_keys(FEATURE_SIDEBAR_BOXES) as $box) {
        $sidebar[$box] ??= ['box' => $box, 'enabled' => true, 'pages' => featureSidebarDefaultPages($box)];
    }
    // „Gerade online“ immer an erster Stelle
    $pinned = $sidebar[FEATURE_SIDEBAR_PINNED];
    unset($sidebar[FEATURE_SIDEBAR_PINNED]);
    $sidebar = [FEATURE_SIDEBAR_PINNED => $pinned] + $sidebar;
    $flags = static function ($raw, array $keys): array {
        $raw = is_array($raw) ? $raw : [];
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = array_key_exists($key, $raw) ? (bool) $raw[$key] : true;
        }
        return $result;
    };
    return [
        'sidebar' => array_values($sidebar),
        'pages' => $flags($value['pages'] ?? null, array_keys(FEATURE_PAGES)),
        'home' => $flags($value['home'] ?? null, array_keys(FEATURE_HOME)),
    ];
}

/** Schalter für die Seiten: liest die Datendatei nur lesend; bei Problemen gelten die Standardwerte. */
function siteFeatures(): array
{
    static $features = null;
    if ($features !== null) {
        return $features;
    }
    $features = normalizeFeatures([]);
    $path = dirname(__DIR__) . '/data/store.php';
    $handle = is_file($path) ? @fopen($path, 'rb') : false;
    if ($handle === false) {
        return $features;
    }
    $contents = '';
    if (flock($handle, LOCK_SH)) {
        $contents = (string) stream_get_contents($handle);
        flock($handle, LOCK_UN);
    }
    fclose($handle);
    // Erste Zeile ist der PHP-Schutz, danach folgt JSON
    $newline = strpos($contents, "\n");
    $store = $newline === false ? null : json_decode(substr($contents, $newline + 1), true);
    if (is_array($store) && is_array($store['settings'] ?? null)) {
        $features = normalizeFeatures($store['settings']['features'] ?? []);
    }
    return $features;
}

/** Ist eine Seite im Menü eingeschaltet? Unbekannte Seiten (z. B. Kalender) immer ja. */
function featurePageEnabled(string $page): bool
{
    return siteFeatures()['pages'][$page] ?? true;
}
