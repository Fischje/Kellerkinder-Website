<?php
declare(strict_types=1);

// feed.php bindet diese Datei nur ein, um die Lesefunktionen zu nutzen.
// In dem Fall keine JSON-Header senden und keine Anfrage verarbeiten.
if (!defined('BLOG_FEED_ONLY')) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');
}

date_default_timezone_set('Europe/Berlin');

// Optionale lokale Konfiguration (nicht im Repository, siehe .gitignore),
// z. B.: <?php const RAWG_API_KEY = 'dein-key';
if (is_file(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

// Schalter der zentralen Admin-Seite (Seitenleiste, Menü, Startseite)
require_once __DIR__ . '/includes/features.php';

const PLAYER_NAME_MAX = 40;
const USERNAME_MAX = 50;
const NOTE_MAX = 60;
const GAME_MAX = 60;
const PASSWORD_MIN_LENGTH = 8;
const AVATAR_MAX_SIZE = 50;
const AVATAR_MAX_DATA_LENGTH = 30000;
const ALLOWED_AVATAR_MIME_TYPES = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];
const ALLOWED_STATUSES = ['', 'online', 'late', 'absent', 'vacation'];
const ALLOWED_THEMES = ['default', 'summer', 'winter'];
const STORE_PREFIX = "<?php http_response_code(403); exit; ?>\n";

// Erfolgs-Widgets: WoW-Charaktere, die für die Mythisch-Plus-Anzeige über
// Raider.IO abgefragt werden. Leer lassen, bis Charakternamen feststehen –
// das Widget zeigt dann automatisch einen "noch nicht eingerichtet"-Hinweis.
const WOW_ACHIEVEMENT_CHARACTERS = [
    ['name' => 'Fischjö', 'realm' => 'alexstrasza', 'region' => 'eu'],
    ['name' => 'Crydamoure', 'realm' => 'mannoroth', 'region' => 'eu'],
    ['name' => 'Redan', 'realm' => 'rexxar', 'region' => 'eu'],
    ['name' => 'Baalgrim', 'realm' => 'rexxar', 'region' => 'eu'],
    ['name' => 'Shootingjack', 'realm' => 'rexxar', 'region' => 'eu'],
    ['name' => 'Vakur', 'realm' => 'rexxar', 'region' => 'eu'],
    ['name' => 'Idran', 'realm' => 'rexxar', 'region' => 'eu'],
];

const ACHIEVEMENTS_CACHE_TTL_SECONDS = 1800;

// Spielzeit-Statistik vom Discord-Bot. Die Adresse steht (nicht im Repository)
// in config.php, z. B.: const BOT_STATS_URL = 'http://127.0.0.1:3100/api/stats';
const BOT_STATS_TTL_SECONDS = 300;
const BOT_STATS_ALLOWED_DAYS = [7, 30, 365, 0]; // 0 = gesamte Zeit (Immer)
const STATS_EXCLUDED_MAX = 200;

// Gameserver (Seite „Server“): Liste im Store, Online-Status liefert der Discord-Bot
// (abgeleitet aus BOT_STATS_URL: …/api/stats → …/api/gameservers).
const GAMESERVER_MAX = 30;

// Netzje (LAN-Party): genau eine Party mit Stundenplan; Zeiten als lokale Zeit „YYYY-MM-DDTHH:MM“ (Europe/Berlin).
const NETZJE_SLOTS_MAX = 120;
const NETZJE_PROPOSALS_PER_USER = 10;
const NETZJE_TEXT_MAX = 2000;
const NETZJE_STREAM_TYPES = ['none', 'twitch', 'youtube'];
const NETZJE_BACKGROUND_MAX_WIDTH = 1920;
const NETZJE_BACKGROUND_MAX_BYTES = 6000000;
const NETZJE_SEATS_MAX = 999;
const NETZJE_HISTORY_MAX = 200;
// Sitzplan: Raster (Spalten × Reihen) mit markierten Sitzplätzen
const NETZJE_SEATING_COLS_MAX = 20;
const NETZJE_SEATING_ROWS_MAX = 14;
// Jahresrückblick: erstes Jahr mit Daten, Cache (laufendes Jahr kurz, vergangene Jahre lang)
const STATS_FIRST_YEAR = 2026;
const YEAR_REVIEW_TTL_CURRENT = 600;
const YEAR_REVIEW_TTL_PAST = 21600;
const NOW_PLAYING_TTL_SECONDS = 30;
// Eindrücke (Galerie nur für Angemeldete): Bilder in data/impressions, ausgeliefert nur über api.php
const IMPRESSION_MAX_BYTES = 8000000;
const IMPRESSION_MAX_WIDTH = 2400;
const IMPRESSION_THUMB_WIDTH = 640;
const IMPRESSIONS_MAX = 2000;
const IMPRESSION_ALBUM_MAX = 60;
const IMPRESSION_CAPTION_MAX = 300;
const GAMESERVER_QUERY_TYPES = ['a2s', 'minecraft', 'tcp'];
const GAMESERVER_STATUS_TTL_SECONDS = 20;
const GAMESERVER_STATUS_MAX_AGE_SECONDS = 300; // älter: Status gilt als unbekannt

// Steam-Erfolge: Schlüssel (nicht im Repository) in config.php, z. B.
// const STEAM_API_KEY = '...'; — kostenlos unter steamcommunity.com/dev/apikey
const STEAM_NAME_MAX = 64;
const STEAM_ACHIEVEMENTS_PLAYERS_MAX = 7;
const STEAM_RECENT_GAMES_MAX = 8;      // so viele Spiele je Spieler auf Erfolge prüfen
const STEAM_CACHE_TTL_SECONDS = 3600;  // Erfolge höchstens stündlich bei Steam abholen
const STEAM_RETRY_TTL_SECONDS = 120;  // nach einer Steam-Störung schon nach 2 Minuten neu versuchen
const STEAM_PARALLEL_MAX = 6;          // so viele Steam-Abrufe gleichzeitig
const STEAM_ACHIEVEMENTS_CACHE_FILE = 'steam-achievements-v3.json'; // v3: Ergebnisse der fehlerhaften Version 2.14.3 verwerfen
const STEAM_SCHEMA_TTL_SECONDS = 86400;
const ACHIEVEMENT_GAMES = ['wow', 'hots', 'diablo4', 'rocket_league']; // Alle Spiele (inkl. WoW, das Titel/Links aber nicht Stats manuell hat)
const ACHIEVEMENT_LABEL_MAX = 40;
const ACHIEVEMENT_VALUE_MAX = 60;
const ACHIEVEMENT_MILESTONES_MAX = 8;
const ACHIEVEMENT_TITLE_MAX = 40;
const ACHIEVEMENT_LINK_LABEL_MAX = 30;
const ACHIEVEMENT_LINK_URL_MAX = 200;
const ACHIEVEMENT_LINKS_MAX = 6;
const REMEMBER_ME_SECONDS = 60 * 60 * 24 * 30; // 30 Tage
const EMAIL_MAX = 190;
const PASSWORD_RESET_SECONDS = 3600;         // Reset-Link aus der Mail gilt 60 Minuten
const PASSWORD_RESET_MAX_PER_CLIENT = 5;     // Anfragen je Netz/Gerät und Stunde
const PASSWORD_RESET_MAX_PER_ADDRESS = 3;    // Anfragen je E-Mail-Adresse und Stunde

const BLOG_TITLE_MAX = 120;
const BLOG_TAG_MAX = 24;
const BLOG_TAGS_MAX = 8;
const BLOG_CONTENT_MAX = 120000;
const BLOG_IMAGE_MAX_WIDTH = 1600;
const BLOG_IMAGE_MAX_BYTES = 6000000;

const CURRENT_GAME_NAME_MAX = 100;
const STEAM_SEARCH_RESULTS_MAX = 8;

// Discord-Profilbilder der Spieler: Der Bot liefert die Bild-Adresse, die Website lädt das Bild
// nach data/media (die Besucher laden also nichts von Discord; ausgeliefert über media.php) und prüft es einmal pro Woche.
const DISCORD_AVATAR_INDEX_FILE = 'discord-avatars.json';
const DISCORD_AVATAR_CHECK_SECONDS = 604800;   // Bild einmal pro Woche auf Änderung prüfen
const DISCORD_AVATAR_RETRY_SECONDS = 3600;     // nach einem Fehlschlag frühestens nach 1 Stunde erneut
const DISCORD_AVATAR_MAX_BYTES = 400000;
const DISCORD_AVATAR_BATCH_MAX = 25;

// Spiele-Symbole: Zu jedem Spiel der Statistik wird einmal ein Bild über die Spielsuche
// (RAWG bzw. Steam-Store) gefunden und nach data/media heruntergeladen (ausgeliefert über media.php).
const GAME_ICON_INDEX_FILE = 'game-icons.json';
const GAME_ICON_LOCK_FILE = 'game-icons.lock';
const GAME_ICON_BATCH_MAX = 12;            // so viele neue Symbole je Durchlauf
const GAME_ICON_MAX_BYTES = 1500000;
const GAME_ICON_RETRY_NOTFOUND_SECONDS = 604800; // „nicht gefunden“: nach 7 Tagen erneut suchen
const GAME_ICON_RETRY_ERROR_SECONDS = 3600;      // Suchdienst nicht erreichbar: nach 1 Stunde

/**
 * Erlaubte Tags und Attribute für Blog-Inhalte. Alles andere wird entfernt.
 * Das ist die zentrale Absicherung gegen eingeschleusten Schadcode: Autoren
 * schreiben HTML, das später bei allen Besuchern im Browser dargestellt wird.
 */
const BLOG_ALLOWED_HTML = [
    'p' => [], 'br' => [], 'strong' => [], 'b' => [], 'em' => [], 'i' => [],
    'u' => [], 's' => [], 'h2' => [], 'h3' => [], 'h4' => [],
    'ul' => [], 'ol' => [], 'li' => [], 'blockquote' => [], 'hr' => [],
    'code' => [], 'pre' => [],
    'a' => ['href', 'title'],
    'img' => ['src', 'alt', 'width'],
    'figure' => ['class'], 'figcaption' => [],
    'div' => ['class'], 'span' => ['class'],
];

const BLOG_ALLOWED_CLASSES = ['yt-embed', 'yt-embed-thumb', 'yt-embed-play', 'blog-figure'];
const CALENDAR_UPCOMING_DAYS_LIMIT = 21; // Server liefert bis zu so viele kommende Spieltage; wie viele angezeigt werden, entscheidet das Frontend anhand der verfügbaren Breite (mindestens 7)
const CALENDAR_PAST_DAY_VISIBLE_UNTIL_HOUR = 12; // Letzter vergangener Spieltag ist nur bis zu dieser Uhrzeit (Folgetag) sichtbar

function isHttpsRequest(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    return strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
}

if (!defined('BLOG_FEED_ONLY')) {
session_name('kellerkinder_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => isHttpsRequest(),
    'httponly' => true,
    'samesite' => 'Lax',
]);
// Zusätzliche, aber allein nicht ausreichende Absicherung: hebt PHPs eigene
// probabilistische Aufräumung an. Viele Server (u. a. Debian/Ubuntu) räumen
// Sitzungsdateien aber per Cron/Systemd-Timer anhand der echten php.ini auf,
// worauf dieses ini_set() zur Laufzeit keinen Einfluss hat — deshalb gibt es
// zusätzlich das dauerhafte, store-gestützte Token weiter unten.
$configuredGcLifetime = (int) ini_get('session.gc_maxlifetime');
if ($configuredGcLifetime < REMEMBER_ME_SECONDS) {
    ini_set('session.gc_maxlifetime', (string) REMEMBER_ME_SECONDS);
}
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['user_id']) && isset($_COOKIE['kellerkinder_remember'])) {
    $rememberParts = explode(':', (string) $_COOKIE['kellerkinder_remember'], 2);
    $rememberRestored = false;
    if (count($rememberParts) === 2 && $rememberParts[0] !== '' && $rememberParts[1] !== '') {
        [$rememberSelector, $rememberValidator] = $rememberParts;
        $rememberStore = readStore();
        foreach ($rememberStore['remember_tokens'] as $rememberToken) {
            if (!hash_equals($rememberToken['selector'], $rememberSelector)) {
                continue;
            }
            if (hash_equals($rememberToken['validator_hash'], hash('sha256', $rememberValidator))) {
                $rememberUserIndex = findUserIndex($rememberStore['users'], (int) $rememberToken['user_id']);
                if ($rememberUserIndex !== null) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = (int) $rememberStore['users'][$rememberUserIndex]['id'];
                    $_SESSION['auth_version'] = max(1, (int) ($rememberStore['users'][$rememberUserIndex]['session_version'] ?? 1));
                    $rememberRestored = true;
                }
            }
            break;
        }
    }
    if (!$rememberRestored) {
        clearRememberCookie();
    }
}
} // Ende: if (!defined('BLOG_FEED_ONLY'))


function sanitizeBlogHtml(string $html): string
{
    $html = trim($html);
    if ($html === '') {
        return '';
    }
    if (!class_exists('DOMDocument')) {
        // Ohne DOM-Erweiterung lieber gar kein HTML zulassen als ungeprüftes.
        return htmlspecialchars(strip_tags($html), ENT_QUOTES, 'UTF-8');
    }

    $document = new DOMDocument('1.0', 'UTF-8');
    $previous = libxml_use_internal_errors(true);
    $document->loadHTML(
        '<?xml encoding="UTF-8"><div id="blog-root">' . $html . '</div>',
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
    );
    libxml_clear_errors();
    libxml_use_internal_errors($previous);

    $root = $document->getElementById('blog-root');
    if ($root === null) {
        return '';
    }

    sanitizeBlogNode($root);

    $result = '';
    foreach ($root->childNodes as $child) {
        $result .= $document->saveHTML($child);
    }
    return trim($result);
}

function sanitizeBlogNode(DOMNode $node): void
{
    // Rückwärts laufen, da Elemente während der Prüfung entfernt werden können.
    for ($index = $node->childNodes->length - 1; $index >= 0; $index--) {
        $child = $node->childNodes->item($index);
        if ($child === null) {
            continue;
        }

        if ($child->nodeType === XML_TEXT_NODE) {
            continue;
        }
        if ($child->nodeType !== XML_ELEMENT_NODE) {
            $node->removeChild($child);
            continue;
        }

        /** @var DOMElement $child */
        $tag = strtolower($child->nodeName);
        if (!array_key_exists($tag, BLOG_ALLOWED_HTML)) {
            // Unerlaubtes Element: Inhalt behalten, Hülle entfernen.
            while ($child->firstChild !== null) {
                $node->insertBefore($child->firstChild, $child);
            }
            $node->removeChild($child);
            continue;
        }

        $allowedAttributes = BLOG_ALLOWED_HTML[$tag];
        for ($attrIndex = $child->attributes->length - 1; $attrIndex >= 0; $attrIndex--) {
            $attribute = $child->attributes->item($attrIndex);
            if ($attribute === null) {
                continue;
            }
            $name = strtolower($attribute->nodeName);
            if (!in_array($name, $allowedAttributes, true)) {
                $child->removeAttribute($attribute->nodeName);
                continue;
            }
            $value = trim($attribute->nodeValue ?? '');

            if ($name === 'href') {
                // Eigene Seiten (z. B. kalender.php#registrieren) öffnen im selben Fenster
                if (preg_match('#^[a-z0-9-]+\.php(?:[?\#][A-Za-z0-9=&%_.\#-]*)?$#', $value)) {
                    continue;
                }
                if (!preg_match('#^(https?://|mailto:)#i', $value)) {
                    $child->removeAttribute('href');
                    continue;
                }
                $child->setAttribute('target', '_blank');
                $child->setAttribute('rel', 'noopener noreferrer');
            } elseif ($name === 'src') {
                // Nur eigene Uploads (neu: data/media über media.php, früher: assets/blog) und YouTube-Vorschaubilder zulassen.
                $isLocalUpload = preg_match('#^assets/blog/[A-Za-z0-9._-]+$#', $value) === 1
                    || preg_match('#^media\.php\?f=blog-[a-z0-9-]+\.(?:png|jpg|gif|webp)$#', $value) === 1;
                $isYoutubeThumb = preg_match('#^https://i\.ytimg\.com/vi/[A-Za-z0-9_-]{6,20}/[a-z0-9]+\.jpg$#', $value) === 1;
                if (!$isLocalUpload && !$isYoutubeThumb) {
                    $child->removeAttribute('src');
                }
            } elseif ($name === 'class') {
                $keep = array_values(array_filter(
                    preg_split('/\s+/', $value) ?: [],
                    static fn(string $class): bool => in_array($class, BLOG_ALLOWED_CLASSES, true)
                ));
                if ($keep === []) {
                    $child->removeAttribute('class');
                } else {
                    $child->setAttribute('class', implode(' ', $keep));
                }
            } elseif ($name === 'width') {
                $width = (int) $value;
                if ($width < 40 || $width > BLOG_IMAGE_MAX_WIDTH) {
                    $child->removeAttribute('width');
                } else {
                    $child->setAttribute('width', (string) $width);
                }
            }
        }

        if ($tag === 'img' && !$child->hasAttribute('src')) {
            $node->removeChild($child);
            continue;
        }

        sanitizeBlogNode($child);
    }
}

function blogExcerpt(string $html, int $length = 200): string
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags($html)) ?? '');
    if (textLength($text) <= $length) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $length, 'UTF-8')) . '…';
}

function blogSlug(string $title, int $id): string
{
    $slug = strtolower(trim($title));
    $map = ['ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss'];
    $slug = strtr($slug, $map);
    $slug = preg_replace('/[^a-z0-9]+/u', '-', $slug) ?? '';
    $slug = trim($slug, '-');
    return $slug === '' ? (string) $id : $id . '-' . mb_substr($slug, 0, 60, 'UTF-8');
}

function normalizeBlogTags($value): array
{
    if (!is_array($value)) {
        return [];
    }
    $tags = [];
    foreach ($value as $tag) {
        $clean = trim(preg_replace('/\s+/u', ' ', (string) $tag) ?? '');
        if ($clean === '') {
            continue;
        }
        $clean = mb_substr($clean, 0, BLOG_TAG_MAX, 'UTF-8');
        $tags[textLower($clean)] = $clean;
        if (count($tags) >= BLOG_TAGS_MAX) {
            break;
        }
    }
    return array_values($tags);
}

function normalizeBlogPosts($posts): array
{
    if (!is_array($posts)) {
        return [];
    }
    $result = [];
    foreach ($posts as $post) {
        if (!is_array($post) || (int) ($post['id'] ?? 0) <= 0) {
            continue;
        }
        $id = (int) $post['id'];
        $title = mb_substr(trim((string) ($post['title'] ?? '')), 0, BLOG_TITLE_MAX, 'UTF-8');
        $result[] = [
            'id' => $id,
            'title' => $title,
            'slug' => (string) ($post['slug'] ?? blogSlug($title, $id)),
            'content_html' => (string) ($post['content_html'] ?? ''),
            'excerpt' => (string) ($post['excerpt'] ?? ''),
            'tags' => normalizeBlogTags($post['tags'] ?? []),
            'author_user_id' => (int) ($post['author_user_id'] ?? 0),
            'author_name' => (string) ($post['author_name'] ?? ''),
            'status' => ($post['status'] ?? 'published') === 'draft' ? 'draft' : 'published',
            'created_at' => (string) ($post['created_at'] ?? gmdate('c')),
            'updated_at' => (string) ($post['updated_at'] ?? gmdate('c')),
        ];
    }
    usort($result, static fn(array $a, array $b): int => strcmp($b['created_at'], $a['created_at']));
    return $result;
}

function steamImageUrl(int $appId): string
{
    return 'https://cdn.akamai.steamstatic.com/steam/apps/' . $appId . '/header.jpg';
}

/** Nur Bilder von bekannten Spiele-Diensten zulassen (passt zur CSP). */
function validGameImage($value): ?string
{
    $url = is_string($value) ? trim($value) : '';
    // GAME_IMAGE_TEST_PREFIX nur für lokale Tests mit einer Attrappe (config.php), im Betrieb nicht setzen.
    if ($url !== '' && defined('GAME_IMAGE_TEST_PREFIX') && str_starts_with($url, GAME_IMAGE_TEST_PREFIX) && strlen($url) <= 300) {
        return $url;
    }
    if ($url !== '' && strlen($url) <= 300
        && preg_match('#^https://(media\.rawg\.io/media/[A-Za-z0-9_/.-]+|cdn\.akamai\.steamstatic\.com/steam/apps/\d+/[A-Za-z0-9_.-]+)$#', $url)) {
        return $url;
    }
    return null;
}

function gameHttpGet(string $url): ?array
{
    $context = stream_context_create(['http' => [
        'timeout' => 6,
        'ignore_errors' => true,
        'header' => "User-Agent: Kellerkinder-Kalender/1.0 (+https://github.com/Fischje)\r\n",
    ]]);
    $raw = @file_get_contents($url, false, $context);
    $data = $raw === false ? null : json_decode($raw, true);
    return is_array($data) ? $data : null;
}

/**
 * Steam-Name aus Eingabe: Profil-URL (…/id/NAME oder …/profiles/ID),
 * SteamID64 (17 Ziffern) oder eigener Profilname. Leer = kein Steam-Konto.
 */
function validateSteamName($value): string
{
    $raw = trim((string) $value);
    if ($raw === '') {
        return '';
    }
    if (preg_match('#steamcommunity\.com/(?:id|profiles)/([^/?\#\s]+)#i', $raw, $match)) {
        $raw = $match[1];
    }
    if (!preg_match('/^(?:\d{17}|[A-Za-z0-9_-]{2,' . STEAM_NAME_MAX . '})$/', $raw)) {
        respond(['ok' => false, 'error' => 'Bitte den Steam-Profilnamen, die SteamID oder den Link zum Steam-Profil angeben.'], 422);
    }
    return $raw;
}

function steamApiConfigured(): bool
{
    return defined('STEAM_API_KEY') && STEAM_API_KEY !== '';
}

function steamApiGet(string $path, array $params): ?array
{
    $params['key'] = STEAM_API_KEY;
    $params['format'] = 'json';
    // STEAM_API_BASE nur für lokale Tests mit einer Attrappe setzen.
    $base = defined('STEAM_API_BASE') ? STEAM_API_BASE : 'https://api.steampowered.com';
    return gameHttpGet($base . '/' . $path . '/?' . http_build_query($params));
}

/** Link zum Steam-Profil für die Anzeige (gespeichert wird nur Profilname oder SteamID). */
function steamProfileUrl(string $steamName): string
{
    if ($steamName === '') {
        return '';
    }
    return preg_match('/^\d{17}$/', $steamName)
        ? 'https://steamcommunity.com/profiles/' . $steamName
        : 'https://steamcommunity.com/id/' . $steamName;
}

/**
 * Mehrere Steam-Abrufe gleichzeitig (curl_multi), höchstens STEAM_PARALLEL_MAX
 * auf einmal. Schlägt ein gleichzeitiger Abruf fehl (curl fehlt oder ist
 * blockiert, Zertifikatsproblem, Steam drosselt), wird er einzeln über den
 * bewährten Weg (steamApiGet) wiederholt – langsamer, aber zuverlässig.
 * $calls: Schlüssel => [Pfad, Parameter]; Ergebnis mit denselben Schlüsseln,
 * null bei endgültigem Fehler. $GLOBALS['steam_transport'] zählt für die
 * Diagnose, wie viele Abrufe parallel gelangen bzw. nachgeholt werden mussten.
 */
function steamApiGetMany(array $calls): array
{
    $stats = &$GLOBALS['steam_transport'];
    $stats ??= ['parallel_ok' => 0, 'retried' => 0, 'curl' => function_exists('curl_multi_init')];
    $results = [];

    if (function_exists('curl_multi_init')) {
        $base = defined('STEAM_API_BASE') ? STEAM_API_BASE : 'https://api.steampowered.com';
        foreach (array_chunk($calls, STEAM_PARALLEL_MAX, true) as $batch) {
            $multi = curl_multi_init();
            $handles = [];
            foreach ($batch as $key => [$path, $params]) {
                $params['key'] = STEAM_API_KEY;
                $params['format'] = 'json';
                $handle = curl_init($base . '/' . $path . '/?' . http_build_query($params));
                curl_setopt_array($handle, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 8,
                    CURLOPT_CONNECTTIMEOUT => 4,
                    CURLOPT_USERAGENT => 'Kellerkinder-Kalender/1.0 (+https://github.com/Fischje)',
                ]);
                curl_multi_add_handle($multi, $handle);
                $handles[$key] = $handle;
            }
            do {
                $status = curl_multi_exec($multi, $running);
                if ($running) {
                    curl_multi_select($multi, 1.0);
                }
            } while ($running && $status === CURLM_OK);
            foreach ($handles as $key => $handle) {
                $raw = curl_multi_getcontent($handle);
                $code = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
                $data = ($code === 200 && is_string($raw) && $raw !== '') ? json_decode($raw, true) : null;
                if (is_array($data)) {
                    $results[$key] = $data;
                    $stats['parallel_ok']++;
                }
                curl_multi_remove_handle($multi, $handle);
                curl_close($handle);
            }
            curl_multi_close($multi);
        }
    }

    // Alles, was nicht gleichzeitig geklappt hat, einzeln nachholen.
    foreach ($calls as $key => [$path, $params]) {
        if (!isset($results[$key])) {
            $results[$key] = steamApiGet($path, $params);
            $stats['retried']++;
        }
    }
    return $results;
}

/** Liefert die SteamID64 zu einem Profilnamen (dauerhaft zwischengespeichert). */
function steamResolveId(string $steamName, bool $askSteam = true): ?string
{
    if (preg_match('/^\d{17}$/', $steamName)) {
        return $steamName;
    }
    $cacheFile = 'steam-id-' . md5(strtolower($steamName)) . '.json';
    $cached = achievementsCacheRead($cacheFile, null); // Profilnamen ändern sich nicht
    if (is_array($cached) && isset($cached['steamid'])) {
        return (string) $cached['steamid'];
    }
    if (!$askSteam) {
        return null;
    }
    $data = steamApiGet('ISteamUser/ResolveVanityURL/v1', ['vanityurl' => $steamName]);
    $steamId = (string) ($data['response']['steamid'] ?? '');
    if ((int) ($data['response']['success'] ?? 0) !== 1 || !preg_match('/^\d{17}$/', $steamId)) {
        return null;
    }
    achievementsCacheWrite($cacheFile, ['steamid' => $steamId]);
    return $steamId;
}

/** Namen und Symbole aller Erfolge eines Spiels (einen Tag zwischengespeichert). */
function steamAchievementSchema(int $appId): array
{
    $cacheFile = 'steam-schema-' . $appId . '.json';
    $path = achievementsCacheDirectory() . DIRECTORY_SEPARATOR . $cacheFile;
    if (is_file($path) && (time() - filemtime($path)) < STEAM_SCHEMA_TTL_SECONDS) {
        $cached = achievementsCacheRead($cacheFile, null);
        if (is_array($cached)) {
            return $cached;
        }
    }
    $schema = steamSchemaFromResponse(steamApiGet('ISteamUserStats/GetSchemaForGame/v2', ['appid' => $appId, 'l' => 'german']));
    achievementsCacheWrite($cacheFile, $schema);
    return $schema;
}

function steamSchemaFromResponse(?array $data): array
{
    $schema = [];
    foreach ($data['game']['availableGameStats']['achievements'] ?? [] as $entry) {
        $apiName = (string) ($entry['name'] ?? '');
        if ($apiName === '') {
            continue;
        }
        $schema[$apiName] = [
            'name' => mb_substr((string) ($entry['displayName'] ?? $apiName), 0, 120, 'UTF-8'),
            'description' => mb_substr((string) ($entry['description'] ?? ''), 0, 200, 'UTF-8'),
            'icon' => (string) ($entry['icon'] ?? ''),
        ];
    }
    return $schema;
}

/** Lädt fehlende bzw. veraltete Erfolgs-Schemas mehrerer Spiele gleichzeitig in den Cache. */
function steamPrefetchSchemas(array $appIds): void
{
    $calls = [];
    foreach ($appIds as $appId) {
        $path = achievementsCacheDirectory() . DIRECTORY_SEPARATOR . 'steam-schema-' . $appId . '.json';
        if ($appId > 0 && (!is_file($path) || (time() - filemtime($path)) >= STEAM_SCHEMA_TTL_SECONDS)) {
            $calls[$appId] = ['ISteamUserStats/GetSchemaForGame/v2', ['appid' => $appId, 'l' => 'german']];
        }
    }
    foreach (steamApiGetMany($calls) as $appId => $data) {
        if ($data !== null) {
            achievementsCacheWrite('steam-schema-' . $appId . '.json', steamSchemaFromResponse($data));
        }
    }
}

/** Nur Bilder von Steams eigenen Servern weitergeben (passt zur CSP). */
function steamSafeImage(string $url): string
{
    return preg_match('#^https://(?:steamcdn-a\.akamaihd\.net|cdn\.akamai\.steamstatic\.com|cdn\.cloudflare\.steamstatic\.com|shared\.akamai\.steamstatic\.com|shared\.fastly\.steamstatic\.com)/[A-Za-z0-9_/.%-]+$#', $url)
        ? $url
        : '';
}

/** Steam-Abrufe für die Spielauswahl eines Spielers (für steamApiGetMany). */
function steamCandidateCalls(string $steamId): array
{
    return [
        'recent' => ['IPlayerService/GetRecentlyPlayedGames/v1', ['steamid' => $steamId, 'count' => 10]],
        'owned' => ['IPlayerService/GetOwnedGames/v1', [
            'steamid' => $steamId,
            'include_appinfo' => 1,
            'include_played_free_games' => 1,
        ]],
    ];
}

/**
 * Spiele, die auf Erfolge geprüft werden, in dieser Reihenfolge:
 *   1. die in den letzten zwei Wochen gespielten (GetRecentlyPlayedGames),
 *   2. die Bibliothek nach „zuletzt gespielt“ (rtime_last_played),
 *   3. die Bibliothek nach Gesamtspielzeit.
 * Stufe 3 ist nötig, weil Steam „zuletzt gespielt“ bei fremden Profilen oft
 * nicht mitliefert (z. B. wenn die Spielzeit privat gehalten wird). Spiele ohne
 * Community-Statistik (also ohne Erfolge) werden übersprungen.
 * Gibt null zurück, wenn Steam nicht antwortet; sonst
 * ['games' => [[appid, name], …], 'owned' => Anzahl, 'recent' => Anzahl, 'with_last_played' => Anzahl].
 */
function steamPickCandidates(?array $recent, ?array $owned): ?array
{
    if ($recent === null && $owned === null) {
        return null;
    }
    $recentGames = is_array($recent['response']['games'] ?? null) ? $recent['response']['games'] : [];
    $ownedGames = is_array($owned['response']['games'] ?? null) ? $owned['response']['games'] : [];
    $ownedGames = array_values(array_filter($ownedGames, 'is_array'));

    $byLastPlayed = array_values(array_filter($ownedGames, static fn(array $game): bool => (int) ($game['rtime_last_played'] ?? 0) > 0));
    usort($byLastPlayed, static fn(array $x, array $y): int => (int) $y['rtime_last_played'] <=> (int) $x['rtime_last_played']);
    $byPlaytime = array_values(array_filter($ownedGames, static fn(array $game): bool => (int) ($game['playtime_forever'] ?? 0) > 0));
    usort($byPlaytime, static fn(array $x, array $y): int => (int) $y['playtime_forever'] <=> (int) $x['playtime_forever']);

    $candidates = [];
    foreach ([$recentGames, $byLastPlayed, $byPlaytime] as $source) {
        foreach ($source as $game) {
            $appId = (int) ($game['appid'] ?? 0);
            if ($appId <= 0 || isset($candidates[$appId])) {
                continue;
            }
            if (array_key_exists('has_community_visible_stats', $game) && empty($game['has_community_visible_stats'])) {
                continue;
            }
            $candidates[$appId] = ['appid' => $appId, 'name' => mb_substr((string) ($game['name'] ?? ''), 0, 100, 'UTF-8')];
            if (count($candidates) >= STEAM_RECENT_GAMES_MAX) {
                break 2;
            }
        }
    }
    return [
        'games' => array_values($candidates),
        'owned' => count($ownedGames),
        'recent' => count($recentGames),
        'with_last_played' => count($byLastPlayed),
    ];
}

/** Steam-Abrufe der Erfolge für die ausgewählten Spiele (Schlüssel = appid). */
function steamAchievementCalls(string $steamId, array $candidates): array
{
    $calls = [];
    foreach ($candidates['games'] as $game) {
        $calls[$game['appid']] = ['ISteamUserStats/GetPlayerAchievements/v1', ['steamid' => $steamId, 'appid' => $game['appid']]];
    }
    return $calls;
}

/**
 * Wertet die Erfolge der ausgewählten Spiele aus und liefert den jüngsten.
 * Gibt ['entry' => …] oder ['reason' => …] zurück:
 *   games_private        keine Spiele sichtbar (Spieldetails nicht öffentlich)
 *   achievements_private Spieleliste sichtbar, Erfolge aber gesperrt („Profile is not
 *                        public“) – typisch für Spieldetails „Nur Freunde“: Die
 *                        Bibliothek sieht der Schlüsselbesitzer als Freund, die
 *                        Erfolge gibt Steam nur bei „Öffentlich“ heraus
 *   no_achievements      in den geprüften Spielen kein Erfolg freigeschaltet
 *   error                Steam hat nicht geantwortet
 * $trace enthält für die Admin-Diagnose das Ergebnis je Spiel.
 */
function steamEvaluateAchievements(?array $candidates, array $allStats, ?array &$trace = null, bool $resolveNames = true): array
{
    $trace = ['candidates' => $candidates === null ? null : array_diff_key($candidates, ['games' => true]), 'checked' => []];
    if ($candidates === null) {
        return ['reason' => 'error'];
    }
    if ($candidates['games'] === []) {
        return ['reason' => $candidates['owned'] === 0 && $candidates['recent'] === 0 ? 'games_private' : 'no_achievements'];
    }

    $latest = null;
    $failed = 0;
    $locked = 0;
    $withStats = 0;
    foreach ($candidates['games'] as $game) {
        $appId = $game['appid'];
        $stats = $allStats[$appId] ?? null;
        if ($stats === null) {
            $failed++;
            $trace['checked'][] = ['game' => $game['name'], 'appid' => $appId, 'result' => 'keine Antwort'];
            continue;
        }
        if (empty($stats['playerstats']['success'])) {
            $steamError = (string) ($stats['playerstats']['error'] ?? 'kein Erfolg abrufbar');
            if (stripos($steamError, 'not public') !== false) {
                $locked++;
                $withStats++;
            }
            $trace['checked'][] = ['game' => $game['name'], 'appid' => $appId, 'result' => 'Steam: ' . mb_substr($steamError, 0, 80, 'UTF-8')];
            continue;
        }
        $withStats++;
        $unlockedCount = 0;
        foreach ($stats['playerstats']['achievements'] ?? [] as $entry) {
            $unlocked = (int) ($entry['unlocktime'] ?? 0);
            if ((int) ($entry['achieved'] ?? 0) !== 1 || $unlocked <= 0) {
                continue;
            }
            $unlockedCount++;
            if ($latest === null || $unlocked > $latest['unlocktime']) {
                $latest = [
                    'unlocktime' => $unlocked,
                    'apiname' => (string) ($entry['apiname'] ?? ''),
                    'appid' => $appId,
                    'game' => $game['name'] !== '' ? $game['name'] : mb_substr((string) ($stats['playerstats']['gameName'] ?? ''), 0, 100, 'UTF-8'),
                ];
            }
        }
        $trace['checked'][] = ['game' => $game['name'], 'appid' => $appId, 'result' => $unlockedCount . ' Erfolge freigeschaltet'];
    }
    if ($latest === null) {
        if ($failed === count($candidates['games'])) {
            return ['reason' => 'error'];
        }
        return ['reason' => ($locked > 0 && $locked === $withStats) ? 'achievements_private' : 'no_achievements'];
    }
    if (!$resolveNames) {
        // Namen/Symbol ergänzt der Aufrufer per steamFillAchievementNames().
        return ['entry' => [
            'game' => $latest['game'],
            'appid' => $latest['appid'],
            'apiname' => $latest['apiname'],
            'unlocked_at' => gmdate('c', $latest['unlocktime']),
        ]];
    }
    $schemaEntry = steamAchievementSchema($latest['appid'])[$latest['apiname']] ?? null;
    return ['entry' => [
        'game' => $latest['game'],
        'appid' => $latest['appid'],
        'achievement' => $schemaEntry['name'] ?? $latest['apiname'],
        'description' => $schemaEntry['description'] ?? '',
        'icon' => steamSafeImage((string) ($schemaEntry['icon'] ?? '')),
        'unlocked_at' => gmdate('c', $latest['unlocktime']),
    ]];
}

/** Jüngster Erfolg eines einzelnen Spielers (für die Admin-Diagnose). */
function steamLatestAchievement(string $steamId, ?array &$trace = null): array
{
    $lists = steamApiGetMany(steamCandidateCalls($steamId));
    $candidates = steamPickCandidates($lists['recent'], $lists['owned']);
    $stats = $candidates === null ? [] : steamApiGetMany(steamAchievementCalls($steamId, $candidates));
    return steamEvaluateAchievements($candidates, $stats, $trace);
}

/** Sichtbarkeit der Profile (ein Abruf für bis zu 100 Spieler): steamid => true, wenn öffentlich. */
function steamProfileVisibility(array $steamIds): ?array
{
    if ($steamIds === []) {
        return [];
    }
    $data = steamApiGet('ISteamUser/GetPlayerSummaries/v2', ['steamids' => implode(',', array_slice($steamIds, 0, 100))]);
    if ($data === null) {
        return null;
    }
    $visible = [];
    foreach ($data['response']['players'] ?? [] as $player) {
        $visible[(string) ($player['steamid'] ?? '')] = (int) ($player['communityvisibilitystate'] ?? 0) === 3;
    }
    return $visible;
}

/** Mitglieder mit Steam-Namen: [['steam_name' => …, 'player_name' => …], …] */
function steamMembers(array $store): array
{
    $members = [];
    foreach ($store['users'] as $user) {
        $steamName = (string) ($user['steam_name'] ?? '');
        if ($steamName === '') {
            continue;
        }
        $player = playerForUser($store, $user);
        $members[] = [
            'steam_name' => $steamName,
            'player_name' => $player === null ? (string) $user['username'] : (string) $player['name'],
        ];
    }
    return $members;
}

/**
 * Zwischengespeicherte Widget-Daten (ohne Altersgrenze) oder null, wenn es noch
 * keine gibt. 'fresh' ist true, wenn sie jung genug sind UND zu genau diesen
 * Mitgliedern gehören; 'matches' sagt, ob die Mitglieder (Steam-Namen) noch
 * dieselben sind. Ändert jemand seinen Steam-Namen, wird also sofort neu geholt
 * statt eine Stunde zu warten.
 */
function steamAchievementsCached(array $members): ?array
{
    $cached = achievementsCacheRead(STEAM_ACHIEVEMENTS_CACHE_FILE, null);
    if (!is_array($cached) || !is_array($cached['data'] ?? null)) {
        return null;
    }
    $matches = ($cached['key'] ?? '') === md5(json_encode($members));
    $path = achievementsCacheDirectory() . DIRECTORY_SEPARATOR . STEAM_ACHIEVEMENTS_CACHE_FILE;
    $ttl = (int) ($cached['ttl'] ?? STEAM_CACHE_TTL_SECONDS);
    return [
        'data' => $cached['data'],
        'matches' => $matches,
        'fresh' => $matches && is_file($path) && (time() - filemtime($path)) < $ttl,
    ];
}

// ---------- Hintergrund-Aktualisierung der Steam-Erfolge ----------

const STEAM_REFRESH_LOCK_FILE = 'steam-refresh.lock';
const STEAM_REFRESH_LOCK_SECONDS = 300; // so lange gilt eine laufende Aktualisierung als „läuft“

function steamRefreshRunning(): bool
{
    $path = achievementsCacheDirectory() . DIRECTORY_SEPARATOR . STEAM_REFRESH_LOCK_FILE;
    return is_file($path) && (time() - filemtime($path)) < STEAM_REFRESH_LOCK_SECONDS;
}

/**
 * Holt die Erfolge von Steam und speichert sie – höchstens eine Aktualisierung
 * gleichzeitig. Gibt true zurück, wenn aktualisiert wurde, false wenn schon eine
 * andere Aktualisierung lief.
 */
function steamRefreshNow(array $store): bool
{
    if (steamRefreshRunning()) {
        return false;
    }
    $directory = achievementsCacheDirectory();
    if (!is_dir($directory) && !@mkdir($directory, 0750, true) && !is_dir($directory)) {
        return false;
    }
    $lock = $directory . DIRECTORY_SEPARATOR . STEAM_REFRESH_LOCK_FILE;
    @file_put_contents($lock, (string) getmypid());
    try {
        steamAchievementsData($store);
    } finally {
        @unlink($lock);
    }
    return true;
}

/**
 * Startet die Aktualisierung, ohne dass ein Besucher darauf warten muss:
 * entweder nach dem Senden der Antwort (PHP-FPM) oder als eigener PHP-Prozess im
 * Hintergrund. Geht beides nicht, übernimmt der Cron-Job (steam-refresh.php).
 * $response: Antwort, die zuerst ausgeliefert wird.
 */
function steamRefreshInBackground(array $store, array $response): void
{
    if (!steamRefreshRunning() && function_exists('fastcgi_finish_request')) {
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        fastcgi_finish_request();
        steamRefreshNow($store);
        exit;
    }
    if (!steamRefreshRunning()) {
        backgroundSpawnCli('');
    }
    respond($response);
}

/**
 * Startet steam-refresh.php als eigenen, abgekoppelten PHP-Prozess (wenn exec erlaubt ist).
 * $argument: z. B. '--icons-only'.
 */
function backgroundSpawnCli(string $argument): void
{
    $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
    if (!function_exists('exec') || in_array('exec', $disabled, true)) {
        return;
    }
    foreach ([PHP_BINDIR . '/php', '/usr/bin/php', '/usr/local/bin/php'] as $php) {
        if (is_file($php) && is_executable($php)) {
            @exec(escapeshellarg($php) . ' ' . escapeshellarg(__DIR__ . '/steam-refresh.php') . ($argument !== '' ? ' ' . escapeshellarg($argument) : '') . ' > /dev/null 2>&1 &');
            return;
        }
    }
}

/**
 * Widget „Steam-Erfolge“: je Spieler mit hinterlegtem Steam-Namen der jüngste
 * Erfolg, neueste zuerst, höchstens STEAM_ACHIEVEMENTS_PLAYERS_MAX Spieler.
 * Fragt Steam ab und legt das Ergebnis in den Zwischenspeicher.
 */
function steamAchievementsData(array $store): array
{
    if (!steamApiConfigured()) {
        return ['configured' => false, 'entries' => [], 'linked_players' => 0];
    }
    $members = steamMembers($store);

    // Erst alle Profile auflösen (unbekannte Profilnamen gleichzeitig), dann die
    // Sichtbarkeit in einem Abruf prüfen.
    $vanityCalls = [];
    foreach ($members as $member) {
        $name = $member['steam_name'];
        if (!preg_match('/^\d{17}$/', $name) && achievementsCacheRead('steam-id-' . md5(strtolower($name)) . '.json', null) === null) {
            $vanityCalls[strtolower($name)] = ['ISteamUser/ResolveVanityURL/v1', ['vanityurl' => $name]];
        }
    }
    foreach (steamApiGetMany($vanityCalls) as $name => $data) {
        $steamId = (string) ($data['response']['steamid'] ?? '');
        if ((int) ($data['response']['success'] ?? 0) === 1 && preg_match('/^\d{17}$/', $steamId)) {
            achievementsCacheWrite('steam-id-' . md5((string) $name) . '.json', ['steamid' => $steamId]);
        }
    }
    $resolved = [];
    foreach ($members as $member) {
        // Gerade erfolglos nachgeschlagene Namen nicht noch einmal einzeln abfragen.
        $alreadyTried = isset($vanityCalls[strtolower($member['steam_name'])]);
        $steamId = steamResolveId($member['steam_name'], !$alreadyTried);
        $resolved[] = $member + ['steam_id' => $steamId];
    }
    $visibility = steamProfileVisibility(array_values(array_filter(array_column($resolved, 'steam_id'))));

    // Alle Spieler gleichzeitig abfragen: erst die Spielelisten, dann die Erfolge.
    $active = [];
    foreach ($resolved as $index => $member) {
        $steamId = $member['steam_id'];
        if ($steamId !== null && ($visibility === null || ($visibility[$steamId] ?? false) === true)) {
            $active[$index] = $steamId;
        }
    }
    $listCalls = [];
    foreach ($active as $index => $steamId) {
        foreach (steamCandidateCalls($steamId) as $kind => $call) {
            $listCalls[$index . ':' . $kind] = $call;
        }
    }
    $lists = steamApiGetMany($listCalls);
    $candidatesByIndex = [];
    $statsCalls = [];
    foreach ($active as $index => $steamId) {
        $candidatesByIndex[$index] = steamPickCandidates($lists[$index . ':recent'] ?? null, $lists[$index . ':owned'] ?? null);
        if ($candidatesByIndex[$index] !== null) {
            foreach (steamAchievementCalls($steamId, $candidatesByIndex[$index]) as $appId => $call) {
                $statsCalls[$index . ':' . $appId] = $call;
            }
        }
    }
    $allStats = steamApiGetMany($statsCalls);

    $entries = [];
    $missing = [];
    foreach ($resolved as $index => $member) {
        $steamId = $member['steam_id'];
        if ($steamId === null) {
            $result = ['reason' => 'not_found'];
        } elseif (!isset($active[$index])) {
            $result = ['reason' => 'profile_private'];
        } else {
            $statsForPlayer = [];
            foreach ($candidatesByIndex[$index]['games'] ?? [] as $game) {
                $statsForPlayer[$game['appid']] = $allStats[$index . ':' . $game['appid']] ?? null;
            }
            $result = steamEvaluateAchievements($candidatesByIndex[$index], $statsForPlayer, $unusedTrace, false);
        }
        if (!isset($result['entry'])) {
            $missing[] = ['player_name' => $member['player_name'], 'reason' => $result['reason']];
            continue;
        }
        $entry = $result['entry'];
        $entry['player_name'] = $member['player_name'];
        $entry['profile_url'] = 'https://steamcommunity.com/profiles/' . $steamId;
        $entries[] = $entry;
    }
    usort($entries, static fn(array $a, array $b): int => strcmp($b['unlocked_at'], $a['unlocked_at']));
    $entries = array_slice($entries, 0, STEAM_ACHIEVEMENTS_PLAYERS_MAX);

    // Namen und Symbole nur für die angezeigten Erfolge holen, alle gleichzeitig.
    steamPrefetchSchemas(array_unique(array_column($entries, 'appid')));
    foreach ($entries as &$entry) {
        $schemaEntry = steamAchievementSchema($entry['appid'])[$entry['apiname']] ?? null;
        $entry['achievement'] = $schemaEntry['name'] ?? $entry['apiname'];
        $entry['description'] = $schemaEntry['description'] ?? '';
        $entry['icon'] = steamSafeImage((string) ($schemaEntry['icon'] ?? ''));
        unset($entry['apiname']);
    }
    unset($entry);

    $data = [
        'configured' => true,
        'entries' => $entries,
        'missing' => $missing,
        'linked_players' => count($members),
        'updated_at' => gmdate('c'),
    ];
    // Nach einer Steam-Störung nicht 15 Minuten lang „keine Daten“ zeigen.
    $hadError = $visibility === null || in_array('error', array_column($missing, 'reason'), true);
    achievementsCacheWrite(STEAM_ACHIEVEMENTS_CACHE_FILE, [
        'key' => md5(json_encode($members)),
        'ttl' => $hadError ? STEAM_RETRY_TTL_SECONDS : STEAM_CACHE_TTL_SECONDS,
        'data' => $data,
    ]);
    return $data;
}

/**
 * Spielsuche. Mit RAWG_API_KEY (config.php) über RAWG, das auch
 * Blizzard-, Epic- und Konsolenspiele kennt; sonst über den Steam-Store.
 * Läuft serverseitig, weil der Browser beides wegen CORS/CSP nicht direkt
 * abfragen darf. Gibt Treffer als [name, image, steam_appid] zurück,
 * oder null, falls der Dienst nicht erreichbar ist.
 */
function searchGames(string $term): ?array
{
    $results = [];
    if (defined('RAWG_API_KEY') && RAWG_API_KEY !== '') {
        $data = gameHttpGet((defined('RAWG_API_BASE') ? RAWG_API_BASE : 'https://api.rawg.io') . '/api/games?key=' . rawurlencode(RAWG_API_KEY)
            . '&search=' . rawurlencode($term) . '&search_precise=true&page_size=' . STEAM_SEARCH_RESULTS_MAX);
        if ($data === null || !is_array($data['results'] ?? null)) {
            return null;
        }
        foreach ($data['results'] as $item) {
            $name = trim((string) ($item['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $image = (string) ($item['background_image'] ?? '');
            // Verkleinerte Variante verwenden (RAWG-Bild-Crop).
            // Auch Screenshots (manche Spiele haben nur einen): die Originale sind oft größer als GAME_ICON_MAX_BYTES.
            $image = str_replace(['/media/games/', '/media/screenshots/'], ['/media/crop/600/400/games/', '/media/crop/600/400/screenshots/'], $image);
            $results[] = [
                'name' => mb_substr($name, 0, CURRENT_GAME_NAME_MAX, 'UTF-8'),
                'image' => validGameImage($image),
                'steam_appid' => null,
            ];
        }
        return $results;
    }

    $data = gameHttpGet('https://store.steampowered.com/api/storesearch/?term=' . rawurlencode($term) . '&l=german&cc=DE');
    if ($data === null || !is_array($data['items'] ?? null)) {
        return null;
    }
    foreach ($data['items'] as $item) {
        $appId = (int) ($item['id'] ?? 0);
        $name = trim((string) ($item['name'] ?? ''));
        if ($appId <= 0 || $name === '' || ($item['type'] ?? 'app') !== 'app') {
            continue;
        }
        $results[] = [
            'name' => mb_substr($name, 0, CURRENT_GAME_NAME_MAX, 'UTF-8'),
            'image' => steamImageUrl($appId),
            'steam_appid' => $appId,
        ];
        if (count($results) >= STEAM_SEARCH_RESULTS_MAX) {
            break;
        }
    }
    return $results;
}

// ---------- Heruntergeladene Bilder (Spiele-Symbole, Profilbilder) ----------
//
// Die Bilder liegen in data/media – dort muss PHP ohnehin schreiben dürfen – und werden über
// media.php ausgeliefert. So braucht es keine zusätzlichen Schreibrechte auf Ordner des
// Programmcodes (die per Git übertragen werden und oft einem anderen Benutzer gehören).

function mediaDirectory(): string
{
    return runtimeDirectory('media');
}

/** Legt data/media an, falls nötig. false, wenn das nicht geht. */
function mediaDirectoryEnsure(): bool
{
    $directory = mediaDirectory();
    return (is_dir($directory) || @mkdir($directory, 0750, true) || is_dir($directory)) && is_writable($directory);
}

/** Gibt es diese heruntergeladene Datei (nur Dateiname, kein Pfad)? */
function mediaFileExists(string $file): bool
{
    return preg_match('/^[a-z0-9][a-z0-9._-]{0,120}\.(?:png|jpe?g|webp|gif)$/', $file) === 1
        && is_readable(mediaDirectory() . DIRECTORY_SEPARATOR . $file); // is_readable: von root angelegte Dateien kann PHP evtl. nicht lesen
}

/**
 * Adresse einer heruntergeladenen Datei für den Browser, oder null, wenn es sie nicht gibt.
 * Ältere Einträge können noch einen Pfad unter assets/ enthalten – der gilt weiter, solange die Datei da ist.
 */
function mediaUrl(string $file): ?string
{
    if ($file === '') {
        return null;
    }
    if (str_starts_with($file, 'assets/')) {
        return is_file(__DIR__ . '/' . $file) ? $file : null;
    }
    return mediaFileExists($file) ? 'media.php?f=' . rawurlencode($file) : null;
}

/** Speichert ein heruntergeladenes Bild nach data/media; gibt den Dateinamen zurück. */
function mediaStore(string $binary, string $file): ?string
{
    if (!mediaDirectoryEnsure()) {
        return null;
    }
    $path = mediaDirectory() . DIRECTORY_SEPARATOR . $file;
    if (is_file($path) && (!is_readable($path) || !is_writable($path))) {
        // Datei gehört einem anderen Benutzer (z. B. von einem Lauf als root): im eigenen Ordner löschen und neu schreiben.
        @unlink($path);
    }
    return @file_put_contents($path, $binary) === false ? null : $file;
}

/** Löscht eine heruntergeladene Datei (nur neue Ablage, alte assets/-Pfade bleiben unangetastet). */
function mediaDelete(string $file): void
{
    if ($file !== '' && !str_starts_with($file, 'assets/') && mediaFileExists($file)) {
        @unlink(mediaDirectory() . DIRECTORY_SEPARATOR . $file);
    }
}

// ---------- Discord-Profilbilder ----------

/** Nur Bilder von Discords eigenem Bildserver zulassen. */
function validDiscordAvatarUrl($value): ?string
{
    $url = is_string($value) ? trim($value) : '';
    if ($url !== '' && strlen($url) <= 300
        && preg_match('#^https://(?:cdn\.discordapp\.com|media\.discordapp\.net)/[A-Za-z0-9_/.-]+(?:\?[A-Za-z0-9_=&.-]*)?$#', $url)) {
        return $url;
    }
    // DISCORD_AVATAR_TEST_PREFIX nur für lokale Tests mit einer Attrappe (config.php), im Betrieb nicht setzen.
    if ($url !== '' && defined('DISCORD_AVATAR_TEST_PREFIX') && str_starts_with($url, DISCORD_AVATAR_TEST_PREFIX) && strlen($url) <= 300) {
        return $url;
    }
    return null;
}

function discordAvatarIndex(): array
{
    return achievementsCacheRead(DISCORD_AVATAR_INDEX_FILE, null) ?? [];
}

/** Heruntergeladenes Profilbild (relativer Pfad) oder null. Es wird nie direkt von Discord eingebunden. */
function discordAvatarLocal(string $playerId, array $index): ?string
{
    return mediaUrl((string) ($index[$playerId]['file'] ?? ''));
}

/** Muss dieses Profilbild neu geholt bzw. geprüft werden? */
function discordAvatarDue(string $playerId, ?array $entry): bool
{
    if ($entry === null || discordAvatarLocal($playerId, [$playerId => $entry]) === null) {
        return time() - (int) ($entry['failed_at'] ?? 0) >= DISCORD_AVATAR_RETRY_SECONDS;
    }
    return time() - (int) ($entry['checked_at'] ?? 0) >= DISCORD_AVATAR_CHECK_SECONDS;
}

/** $sources: Discord-ID => Bild-Adresse vom Bot. */
function discordAvatarsMissing(array $sources): bool
{
    $index = discordAvatarIndex();
    foreach ($sources as $playerId => $url) {
        if (validDiscordAvatarUrl($url) !== null && discordAvatarDue((string) $playerId, $index[$playerId] ?? null)) {
            return true;
        }
    }
    return false;
}

/**
 * Lädt fehlende Profilbilder und prüft vorhandene einmal pro Woche. Ändert sich die Adresse
 * (Discord baut die Bild-Kennung hinein), wird das neue Bild geholt und das alte gelöscht.
 * Gibt die Anzahl neu geladener Bilder zurück.
 */
function discordAvatarsSync(array $sources, int $max = DISCORD_AVATAR_BATCH_MAX): int
{
    $index = discordAvatarIndex();
    $downloaded = 0;
    $worked = 0;
    foreach ($sources as $playerId => $rawUrl) {
        $playerId = (string) $playerId;
        $url = validDiscordAvatarUrl($rawUrl);
        if ($url === null || !preg_match('/^\d{5,25}$/', $playerId) || !discordAvatarDue($playerId, $index[$playerId] ?? null)) {
            continue;
        }
        if (++$worked > $max) {
            break;
        }
        $entry = $index[$playerId] ?? [];
        $currentFile = discordAvatarLocal($playerId, [$playerId => $entry]) !== null ? (string) $entry['file'] : null;
        if ($currentFile !== null && ($entry['url'] ?? '') === $url) {
            $entry['checked_at'] = time(); // unverändert
            unset($entry['failed_at']);
            $index[$playerId] = $entry;
            continue;
        }
        $file = discordAvatarDownload($url, $playerId);
        if ($file === null) {
            $entry['failed_at'] = time();
            $index[$playerId] = $entry;
            continue;
        }
        if ($currentFile !== null && $currentFile !== $file) {
            mediaDelete($currentFile);
        }
        $index[$playerId] = ['url' => $url, 'file' => $file, 'checked_at' => time()];
        $downloaded++;
    }
    if ($worked > 0) {
        achievementsCacheWrite(DISCORD_AVATAR_INDEX_FILE, $index);
    }
    return $downloaded;
}

function discordAvatarDownload(string $url, string $playerId): ?string
{
    $context = stream_context_create(['http' => [
        'timeout' => 8,
        'ignore_errors' => true,
        'header' => "User-Agent: Kellerkinder-Kalender/1.0 (+https://github.com/Fischje)\r\n",
    ]]);
    $binary = @file_get_contents($url, false, $context);
    if ($binary === false || $binary === '' || strlen($binary) > DISCORD_AVATAR_MAX_BYTES) {
        return null;
    }
    $info = @getimagesizefromstring($binary);
    $extensions = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
    if ($info === false || !isset($extensions[$info[2]])) {
        return null;
    }
    return mediaStore($binary, 'discord-' . substr(md5($playerId), 0, 10) . '-' . substr(md5($url), 0, 8) . '.' . $extensions[$info[2]]);
}

// ---------- Spiele-Symbole ----------

/** Vergleichsschlüssel für Spielnamen: klein, ohne ™ ® © und Satzzeichen. */
function gameIconKey(string $name): string
{
    $name = mb_strtolower(str_replace(['™', '®', '©'], '', $name), 'UTF-8');
    return trim((string) preg_replace('/[^\p{L}\p{N}]+/u', ' ', $name));
}

function gameIconIndex(): array
{
    return achievementsCacheRead(GAME_ICON_INDEX_FILE, null) ?? [];
}

function gameIconIndexWrite(array $index): void
{
    achievementsCacheWrite(GAME_ICON_INDEX_FILE, $index);
}

/** Adresse des Symbols für den Browser: die heruntergeladene Datei, sonst das Bild beim Anbieter. */
function gameIconUrl(?array $entry): ?string
{
    if ($entry === null) {
        return null;
    }
    return mediaUrl((string) ($entry['file'] ?? '')) ?? validGameImage($entry['remote'] ?? null);
}

/** Muss für dieses Spiel (noch) nach einem Symbol gesucht werden? */
function gameIconNeedsLookup(?array $entry, bool $retryAll = false): bool
{
    if ($entry === null) {
        return true;
    }
    $age = time() - (int) ($entry['tried_at'] ?? 0);
    if (mediaUrl((string) ($entry['file'] ?? '')) !== null) {
        return false;
    }
    if (validGameImage($entry['remote'] ?? null) !== null) {
        // Bild gefunden, aber noch nicht heruntergeladen (bis dahin wird das Bild des Anbieters gezeigt): nach 1 Stunde erneut versuchen.
        return $retryAll || $age >= GAME_ICON_RETRY_ERROR_SECONDS;
    }
    return $retryAll || $age >= (!empty($entry['error']) ? GAME_ICON_RETRY_ERROR_SECONDS : GAME_ICON_RETRY_NOTFOUND_SECONDS);
}

function gameIconsMissing(array $names): bool
{
    $index = gameIconIndex();
    foreach ($names as $name) {
        if (gameIconKey((string) $name) !== '' && gameIconNeedsLookup($index[gameIconKey((string) $name)] ?? null)) {
            return true;
        }
    }
    return false;
}

/**
 * Sucht über die Spielsuche das passende Bild: erster Treffer mit gleichem Namen,
 * sonst der ähnlichste (mindestens 70 %). null = nichts Passendes, false = Suchdienst nicht erreichbar.
 *
 * @return array|null|false
 */
function gameIconLookup(string $name)
{
    $results = searchGames(mb_substr($name, 0, CURRENT_GAME_NAME_MAX, 'UTF-8'));
    if ($results === null) {
        return false;
    }
    $wanted = gameIconKey($name);
    $best = null;
    $bestScore = 0.0;
    foreach ($results as $result) {
        if (empty($result['image'])) {
            continue;
        }
        $found = gameIconKey((string) $result['name']);
        if ($found === $wanted) {
            return $result;
        }
        similar_text($wanted, $found, $percent);
        if ($percent > $bestScore) {
            $bestScore = $percent;
            $best = $result;
        }
    }
    return $bestScore >= 70.0 ? $best : null;
}

/** Lädt das Bild herunter und speichert es unter data/media. Gibt den Dateinamen zurück. */
function gameIconDownload(string $url, string $key): ?string
{
    $url = validGameImage($url);
    if ($url === null) {
        return null;
    }
    $context = stream_context_create(['http' => [
        'timeout' => 8,
        'ignore_errors' => true,
        'header' => "User-Agent: Kellerkinder-Kalender/1.0 (+https://github.com/Fischje)\r\n",
    ]]);
    $binary = @file_get_contents($url, false, $context);
    if ($binary === false || $binary === '' || strlen($binary) > GAME_ICON_MAX_BYTES) {
        return null;
    }
    // Tatsächlichen Bildinhalt prüfen, nicht die Endung.
    $info = @getimagesizefromstring($binary);
    $extensions = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if ($info === false || !isset($extensions[$info[2]])) {
        return null;
    }
    $slug = substr(trim((string) preg_replace('/[^a-z0-9]+/', '-', strtolower($key)), '-'), 0, 40);
    return mediaStore($binary, 'spiel-' . ($slug !== '' ? $slug : 'x') . '-' . substr(md5($url), 0, 8) . '.' . $extensions[$info[2]]);
}

/**
 * Sucht und lädt Symbole für Spiele, die noch keines haben (höchstens $max pro Durchlauf).
 * $retryAll: auch bisher erfolglose Spiele sofort erneut versuchen. Gibt die Anzahl neuer Symbole zurück.
 */
function gameIconsSync(array $names, int $max = GAME_ICON_BATCH_MAX, bool $retryAll = false): int
{
    $index = gameIconIndex();
    $todo = [];
    foreach ($names as $name) {
        $name = trim((string) $name);
        $key = gameIconKey($name);
        if ($key !== '' && !isset($todo[$key]) && gameIconNeedsLookup($index[$key] ?? null, $retryAll)) {
            $todo[$key] = $name;
        }
    }
    $added = 0;
    foreach (array_slice($todo, 0, $max, true) as $key => $name) {
        $known = $index[$key] ?? null;
        if ($known !== null && validGameImage($known['remote'] ?? null) !== null) {
            // Bild schon gefunden, nur der Download fehlt noch: zuerst die gemerkte Adresse versuchen.
            $known['file'] = gameIconDownload($known['remote'], $key);
            $known['tried_at'] = time();
            $index[$key] = $known;
            gameIconIndexWrite($index);
            if ($known['file'] !== null) {
                $added++;
                continue;
            }
            // Scheitert der Download, ist die Adresse beim Anbieter oft veraltet (Bild ersetzt oder entfernt): neu suchen.
        }
        $found = gameIconLookup($name);
        $entry = ['name' => mb_substr($name, 0, CURRENT_GAME_NAME_MAX, 'UTF-8'), 'tried_at' => time(), 'file' => null, 'remote' => null];
        if ($found === false) {
            $entry = $known !== null ? $known : $entry; // Suchdienst gerade nicht erreichbar: bisherigen Eintrag behalten
            $entry['tried_at'] = time();
            $entry['error'] = true;
        } elseif ($found !== null) {
            $entry['remote'] = validGameImage($found['image'] ?? null);
            $entry['file'] = $entry['remote'] === null ? null : gameIconDownload($entry['remote'], $key);
            $added += $entry['file'] !== null ? 1 : 0;
        }
        $index[$key] = $entry;
        gameIconIndexWrite($index); // nach jedem Spiel sichern, falls der Durchlauf abbricht
    }
    return $added;
}

/** Wie gameIconsSync, aber nur eine Suche gleichzeitig. false = es lief schon eine. */
function gameIconsSyncLocked(array $names, int $max = GAME_ICON_BATCH_MAX, bool $retryAll = false, array $avatars = []): bool
{
    $lock = achievementsCacheDirectory() . DIRECTORY_SEPARATOR . GAME_ICON_LOCK_FILE;
    if (is_file($lock) && (time() - filemtime($lock)) < STEAM_REFRESH_LOCK_SECONDS) {
        return false;
    }
    if (!is_dir(dirname($lock)) && !@mkdir(dirname($lock), 0750, true) && !is_dir(dirname($lock))) {
        return false;
    }
    @file_put_contents($lock, (string) getmypid());
    try {
        gameIconsSync($names, $max, $retryAll);
        discordAvatarsSync($avatars);
    } finally {
        @unlink($lock);
    }
    return true;
}

/**
 * Antwortet sofort und holt fehlende Symbole danach (PHP-FPM) bzw. in einem eigenen
 * PHP-Prozess. Geht beides nicht, erledigt das der Cron-Job (steam-refresh.php).
 */
function gameIconsInBackground(array $names, array $response, array $avatars = []): void
{
    $lock = achievementsCacheDirectory() . DIRECTORY_SEPARATOR . GAME_ICON_LOCK_FILE;
    $running = is_file($lock) && (time() - filemtime($lock)) < STEAM_REFRESH_LOCK_SECONDS;
    if (!$running && function_exists('fastcgi_finish_request')) {
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        fastcgi_finish_request();
        gameIconsSyncLocked($names, GAME_ICON_BATCH_MAX, false, $avatars);
        exit;
    }
    if (!$running) {
        backgroundSpawnCli('--icons-only');
    }
    respond($response);
}

/**
 * Verkleinert zu breite Bilder serverseitig auf BLOG_IMAGE_MAX_WIDTH.
 * Ohne GD-Erweiterung wird das Original unverändert zurückgegeben.
 */
function downscaleBlogImage(string $binary, array $imageInfo, int $maxWidth = BLOG_IMAGE_MAX_WIDTH): string
{
    [$width, $height, $type] = $imageInfo;
    if ($width <= $maxWidth || !function_exists('imagecreatefromstring')) {
        return $binary;
    }
    // Animierte GIFs würden beim Neuzeichnen ihre Animation verlieren.
    if ($type === IMAGETYPE_GIF) {
        return $binary;
    }

    $source = @imagecreatefromstring($binary);
    if ($source === false) {
        return $binary;
    }
    $targetWidth = $maxWidth;
    $targetHeight = (int) round($height * ($targetWidth / $width));
    $target = imagecreatetruecolor($targetWidth, $targetHeight);
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
        imagealphablending($target, false);
        imagesavealpha($target, true);
    }
    imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

    ob_start();
    if ($type === IMAGETYPE_PNG) {
        imagepng($target, null, 6);
    } elseif ($type === IMAGETYPE_WEBP && function_exists('imagewebp')) {
        imagewebp($target, null, 82);
    } else {
        imagejpeg($target, null, 82);
    }
    $resized = (string) ob_get_clean();

    imagedestroy($source);
    imagedestroy($target);

    return $resized !== '' ? $resized : $binary;
}

function pruneRememberTokens($tokens): array
{
    if (!is_array($tokens)) {
        return [];
    }
    $now = time();
    $result = [];
    foreach ($tokens as $token) {
        if (!is_array($token)) {
            continue;
        }
        if (!isset($token['selector'], $token['validator_hash'], $token['user_id'], $token['expires_at'])) {
            continue;
        }
        $expiresAt = strtotime((string) $token['expires_at']);
        if ($expiresAt === false || $expiresAt < $now) {
            continue;
        }
        $result[] = [
            'selector' => (string) $token['selector'],
            'validator_hash' => (string) $token['validator_hash'],
            'user_id' => (int) $token['user_id'],
            'expires_at' => (string) $token['expires_at'],
        ];
    }
    return $result;
}

function rememberCookieOptions(int $expiresInSeconds): array
{
    return [
        'expires' => $expiresInSeconds > 0 ? time() + $expiresInSeconds : time() - 3600,
        'path' => '/',
        'secure' => isHttpsRequest(),
        'httponly' => true,
        'samesite' => 'Lax',
    ];
}

function clearRememberCookie(): void
{
    setcookie('kellerkinder_remember', '', rememberCookieOptions(0));
}

// Legt ein neues, dauerhaftes "Angemeldet bleiben"-Token an. Bewusst
// unabhängig von PHPs eigener Sitzungs-Datei: Auf vielen Servern (u. a.
// Debian/Ubuntu) räumt ein Cron-/Systemd-Job Sitzungsdateien anhand der in
// der echten php.ini hinterlegten Lebensdauer auf — ein `ini_set()` zur
// Laufzeit hat darauf keinen Einfluss. Das Token hier wird stattdessen
// gegen die eigene, dauerhafte Datenspeicherung geprüft und übersteht so
// auch lange Pausen.
function issueRememberToken(array &$store, int $userId): void
{
    $selector = bin2hex(random_bytes(9));
    $validator = bin2hex(random_bytes(33));
    if (!is_array($store['remember_tokens'] ?? null)) {
        $store['remember_tokens'] = [];
    }
    $store['remember_tokens'][] = [
        'selector' => $selector,
        'validator_hash' => hash('sha256', $validator),
        'user_id' => $userId,
        'expires_at' => gmdate('c', time() + REMEMBER_ME_SECONDS),
    ];
    setcookie('kellerkinder_remember', $selector . ':' . $validator, rememberCookieOptions(REMEMBER_ME_SECONDS));
}

function revokeRememberToken(array &$store, string $selector): void
{
    $store['remember_tokens'] = array_values(array_filter(
        is_array($store['remember_tokens'] ?? null) ? $store['remember_tokens'] : [],
        static fn(array $token): bool => (string) ($token['selector'] ?? '') !== $selector
    ));
}

function revokeAllRememberTokensForUser(array &$store, int $userId): void
{
    $store['remember_tokens'] = array_values(array_filter(
        is_array($store['remember_tokens'] ?? null) ? $store['remember_tokens'] : [],
        static fn(array $token): bool => (int) ($token['user_id'] ?? 0) !== $userId
    ));
}

function respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function requestPayload(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return $_POST;
    }

    try {
        $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        respond(['ok' => false, 'error' => 'Ungültige Anfrage.'], 400);
    }

    return is_array($data) ? $data : [];
}

function csrfToken(): string
{
    if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token']) || $_SESSION['csrf_token'] === '') {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrf(array $payload): void
{
    $provided = (string) ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($payload['csrf_token'] ?? ''));
    if ($provided === '' || !hash_equals(csrfToken(), $provided)) {
        respond(['ok' => false, 'error' => 'Die Sitzung ist abgelaufen. Bitte lade die Seite neu.'], 419);
    }
}

// ---------- Schutz vor Passwort-Raten und Massen-Registrierung ----------
//
// Fehlversuche beim Anmelden werden je Netz/Gerät und Benutzername sowie je Benutzername insgesamt
// gezählt (data/cache/login-throttle.json, nur Hashes, keine Klartext-Adressen). Zu viele Fehlversuche
// sperren die Anmeldung für dieses Konto kurz. Neue Konten gibt es höchstens ein paar pro Stunde und Netz.

const THROTTLE_FILE = 'login-throttle.json';
const LOGIN_THROTTLE_WINDOW_SECONDS = 900;    // 15 Minuten
const LOGIN_THROTTLE_MAX_PER_CLIENT = 8;      // je Netz/Gerät und Benutzername
const LOGIN_THROTTLE_MAX_PER_ACCOUNT = 30;    // je Benutzername von überall zusammen
const REGISTER_THROTTLE_WINDOW_SECONDS = 3600;
const REGISTER_THROTTLE_MAX_PER_CLIENT = 5;
// Bei unbekannten Benutzernamen wird gegen diesen Hash geprüft, damit die Antwortzeit nichts verrät.
const LOGIN_DUMMY_HASH = '$2y$10$VNy68FlFvfbiWBVJasDTku0zV213kfPjwKARLWVubEDB0kR5qyzrK';

/** Kennung des Absenders: Hinter Cloudflare steht die echte Adresse in CF-Connecting-IP. */
function clientAddress(): string
{
    $remote = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    $forwarded = (string) ($_SERVER['HTTP_CF_CONNECTING_IP'] ?? '');
    // Der Kopf lässt sich fälschen, wenn jemand den Server direkt anspricht; deshalb immer mit REMOTE_ADDR
    // kombinieren. Wer ihn fälscht, umgeht nur die Grenze je Netz, nicht die je Benutzername.
    return filter_var($forwarded, FILTER_VALIDATE_IP) !== false ? $remote . '|' . $forwarded : $remote;
}

/** Liest die Zählerdatei unter Sperre, lässt $change sie ändern und schreibt sie zurück. */
function throttleUpdate(?callable $change = null): array
{
    $directory = achievementsCacheDirectory();
    if (!is_dir($directory) && !@mkdir($directory, 0750, true) && !is_dir($directory)) {
        return [];
    }
    $handle = @fopen($directory . DIRECTORY_SEPARATOR . THROTTLE_FILE, 'c+');
    if ($handle === false) {
        return [];
    }
    try {
        flock($handle, LOCK_EX);
        $data = json_decode((string) stream_get_contents($handle), true);
        $data = is_array($data) ? $data : [];
        $now = time();
        foreach ($data as $key => $times) {
            $data[$key] = array_values(array_filter(is_array($times) ? $times : [], static fn($time): bool => is_int($time) && $now - $time < REGISTER_THROTTLE_WINDOW_SECONDS));
            if ($data[$key] === []) {
                unset($data[$key]);
            }
        }
        if ($change !== null) {
            $data = $change($data);
        }
        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, json_encode($data));
        fflush($handle);
        return $data;
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

function throttleCount(array $data, string $key, int $window): int
{
    $now = time();
    return count(array_filter($data[$key] ?? [], static fn(int $time): bool => $now - $time < $window));
}

/** Schlüssel der Zähler: je Netz und Benutzername ('client') und je Benutzername ('account'). */
function loginThrottleKeys(string $username): array
{
    $name = textLower(trim($username));
    return ['client' => 'l:' . hash('sha256', clientAddress() . '|' . $name), 'account' => 'a:' . hash('sha256', $name)];
}

function loginBlocked(string $username): bool
{
    $keys = loginThrottleKeys($username);
    $data = throttleUpdate();
    return throttleCount($data, $keys['client'], LOGIN_THROTTLE_WINDOW_SECONDS) >= LOGIN_THROTTLE_MAX_PER_CLIENT
        || throttleCount($data, $keys['account'], LOGIN_THROTTLE_WINDOW_SECONDS) >= LOGIN_THROTTLE_MAX_PER_ACCOUNT;
}

function loginFailed(string $username): void
{
    $keys = loginThrottleKeys($username);
    throttleUpdate(static function (array $data) use ($keys): array {
        $data[$keys['client']][] = time();
        $data[$keys['account']][] = time();
        return $data;
    });
}

/** Erfolgreich angemeldet: eigene Fehlversuche vergessen (die Zählung je Benutzername bleibt bis zum Ablauf). */
function loginSucceeded(string $username): void
{
    $keys = loginThrottleKeys($username);
    throttleUpdate(static function (array $data) use ($keys): array {
        unset($data[$keys['client']]);
        return $data;
    });
}

function registerThrottleKey(): string
{
    return 'r:' . hash('sha256', clientAddress());
}

function registerBlocked(): bool
{
    return throttleCount(throttleUpdate(), registerThrottleKey(), REGISTER_THROTTLE_WINDOW_SECONDS) >= REGISTER_THROTTLE_MAX_PER_CLIENT;
}

function registerCounted(): void
{
    $key = registerThrottleKey();
    throttleUpdate(static function (array $data) use ($key): array {
        $data[$key][] = time();
        return $data;
    });
}

// ---------- Passwort vergessen: E-Mail mit Reset-Link ----------
//
// Versand über PHPs mail(), also über das Mailprogramm des Servers (dort msmtp, wie bei Strahlemännkes).
// Absender und Basisadresse lassen sich in config.php setzen: MAIL_FROM, MAIL_FROM_NAME, SITE_URL.

function passwordResetThrottleKeys(string $email): array
{
    return ['client' => 'p:' . hash('sha256', clientAddress()), 'address' => 'pa:' . hash('sha256', strtolower($email))];
}

function passwordResetBlocked(string $email): bool
{
    $keys = passwordResetThrottleKeys($email);
    $data = throttleUpdate();
    return throttleCount($data, $keys['client'], 3600) >= PASSWORD_RESET_MAX_PER_CLIENT
        || throttleCount($data, $keys['address'], 3600) >= PASSWORD_RESET_MAX_PER_ADDRESS;
}

function passwordResetCounted(string $email): void
{
    $keys = passwordResetThrottleKeys($email);
    throttleUpdate(static function (array $data) use ($keys): array {
        $data[$keys['client']][] = time();
        $data[$keys['address']][] = time();
        return $data;
    });
}

/** Leere Eingabe ist erlaubt (Feld ist freiwillig), sonst muss es eine gültige Adresse sein. */
function validateEmail($value, bool $allowEmpty = true): string
{
    $email = trim((string) $value);
    if ($email === '') {
        if ($allowEmpty) {
            return '';
        }
        respond(['ok' => false, 'error' => 'Bitte gib deine E-Mail-Adresse ein.'], 422);
    }
    if (strlen($email) > EMAIL_MAX || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        respond(['ok' => false, 'error' => 'Bitte gib eine gültige E-Mail-Adresse ein.'], 422);
    }
    return $email;
}

function findUserIndexByEmail(array $users, string $email): ?int
{
    $wanted = strtolower(trim($email));
    if ($wanted === '') {
        return null;
    }
    foreach ($users as $index => $user) {
        if (strtolower((string) ($user['email'] ?? '')) === $wanted) {
            return (int) $index;
        }
    }
    return null;
}

function ensureUniqueEmail(array $users, string $email, ?int $exceptId = null): void
{
    $index = findUserIndexByEmail($users, $email);
    if ($index !== null && (int) $users[$index]['id'] !== $exceptId) {
        respond(['ok' => false, 'error' => 'Diese E-Mail-Adresse gehört schon zu einem anderen Account.'], 409);
    }
}

/** Speichert die Adresse; ändert sie sich, verfällt ein offener Reset-Link. */
function setUserEmail(array &$store, int $userIndex, string $email): void
{
    $old = strtolower((string) ($store['users'][$userIndex]['email'] ?? ''));
    $store['users'][$userIndex]['email'] = $email;
    if ($old !== strtolower($email)) {
        unset($store['users'][$userIndex]['reset_token_hash'], $store['users'][$userIndex]['reset_expires_at']);
    }
}

/** Öffentliche Adresse der Seite für Links in Mails: SITE_URL aus config.php, sonst aus der Anfrage. */
function siteBaseUrl(): string
{
    if (defined('SITE_URL') && trim((string) SITE_URL) !== '') {
        return rtrim(trim((string) SITE_URL), '/');
    }
    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $basePath = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
    // Hinter Cloudflare/NPMplus/Caddy kommt oft nicht an, dass von außen HTTPS benutzt wird. Öffentliche
    // Namen bekommen deshalb immer https, nur lokale Tests (localhost, IP-Adresse) bleiben bei http.
    $hostName = str_starts_with($host, '[') ? substr($host, 1, max(0, (int) strpos($host, ']') - 1)) : (string) preg_replace('/:\d+$/', '', $host);
    $local = $hostName === 'localhost' || filter_var($hostName, FILTER_VALIDATE_IP) !== false;
    return (isHttpsRequest() || !$local ? 'https' : 'http') . '://' . $host . $basePath;
}

function sendPasswordResetMail(string $email, string $username, string $resetUrl): bool
{
    $from = defined('MAIL_FROM') && (string) MAIL_FROM !== '' ? (string) MAIL_FROM : 'fischje@fischje.de';
    $fromName = defined('MAIL_FROM_NAME') && (string) MAIL_FROM_NAME !== '' ? (string) MAIL_FROM_NAME : 'Kellerkinder';
    $encode = static fn(string $text): string => function_exists('mb_encode_mimeheader') ? mb_encode_mimeheader($text, 'UTF-8') : $text;
    $subject = $encode('Kellerkinder – Passwort zurücksetzen');
    $minutes = intdiv(PASSWORD_RESET_SECONDS, 60);
    $body = "Hallo {$username},\n\n"
        . "über diesen Link kannst du ein neues Passwort für die Kellerkinder-Seite festlegen:\n{$resetUrl}\n\n"
        . "Der Link ist {$minutes} Minuten gültig und funktioniert nur einmal. "
        . "Falls du das nicht angefordert hast, kannst du diese E-Mail einfach ignorieren.\n\n"
        . "Deine Kellerkinder";
    $domain = substr(strrchr($from, '@') ?: '@localhost', 1);
    // Vollständige Kopfzeilen: sonst zeigen manche Mailprogramme Umlaute falsch an oder sortieren die Mail als Spam ein.
    $headers = [
        'From: ' . $encode($fromName) . ' <' . $from . '>',
        'Date: ' . date(DATE_RFC2822),
        'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . $domain . '>',
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];
    $sent = function_exists('mail') && @mail($email, $subject, $body, implode("\r\n", $headers));
    if (!$sent) {
        error_log('Kellerkinder: Passwort-Reset-Mail konnte nicht verschickt werden (mail() fehlgeschlagen).');
    }
    return $sent;
}

/**
 * Schnittstellen nur für den Discord-Bot (gameservers_config mit internen Adressen, stats_exclusions mit
 * Discord-IDs). Steht in config.php BOT_SHARED_TOKEN, muss der Bot denselben Wert im Kopf X-Bot-Token
 * mitschicken (im Bot: WEBSITE_API_TOKEN). Ohne Eintrag bleiben sie wie bisher offen.
 */
function requireBotToken(): void
{
    if (!defined('BOT_SHARED_TOKEN') || (string) BOT_SHARED_TOKEN === '') {
        return;
    }
    $given = (string) ($_SERVER['HTTP_X_BOT_TOKEN'] ?? '');
    if ($given === '' || !hash_equals((string) BOT_SHARED_TOKEN, $given)) {
        respond(['ok' => false, 'error' => 'Diese Schnittstelle ist nur für den Discord-Bot.'], 403);
    }
}

function storageDirectory(): string
{
    return __DIR__ . DIRECTORY_SEPARATOR . 'data';
}

function storagePath(): string
{
    return storageDirectory() . DIRECTORY_SEPARATOR . 'store.php';
}

function ensureStorageDirectory(): bool
{
    $directory = storageDirectory();
    if (is_dir($directory)) {
        return true;
    }
    return @mkdir($directory, 0750, true) || is_dir($directory);
}

/**
 * Ordner für Zwischenspeicher ('cache') und heruntergeladene Bilder ('media'): bevorzugt unter data/.
 * Darf PHP dort keine Unterordner anlegen (Datei data/store.php beschreibbar, Ordner data aber nicht –
 * kommt vor, wenn die Rechte nur auf der Datei gesetzt wurden), dient ein Ordner im Temp-Verzeichnis des
 * Servers als Ersatz. Das ist nur ein Zwischenspeicher: Nach einem Neustart wird alles automatisch neu geholt.
 * Dauerhaft löst das: chown -R <PHP-Benutzer> data
 */
function runtimeDirectory(string $name): string
{
    static $resolved = [];
    if (isset($resolved[$name])) {
        return $resolved[$name];
    }
    $primary = storageDirectory() . DIRECTORY_SEPARATOR . $name;
    if ((is_dir($primary) && is_writable($primary))
        || (!is_dir($primary) && is_dir(storageDirectory()) && is_writable(storageDirectory()) && (@mkdir($primary, 0750, true) || is_dir($primary)))) {
        return $resolved[$name] = $primary;
    }
    $fallback = runtimeFallbackDirectory($name);
    if ((is_dir($fallback) || @mkdir($fallback, 0700, true) || is_dir($fallback)) && is_writable($fallback)) {
        return $resolved[$name] = $fallback;
    }
    return $resolved[$name] = $primary; // nichts geht: Schreibversuche scheitern still, Seite läuft ohne Zwischenspeicher
}

function runtimeFallbackDirectory(string $name): string
{
    return rtrim(sys_get_temp_dir(), '/\\') . DIRECTORY_SEPARATOR . 'kellerkinder-' . substr(md5(__DIR__), 0, 10) . '-' . $name;
}

/** 'data' (normal), 'temp' (Ersatzordner) oder 'none' (nichts beschreibbar) für die Statusanzeige. */
function runtimeLocation(string $name): string
{
    $directory = runtimeDirectory($name);
    if (!is_dir($directory) || !is_writable($directory)) {
        return 'none';
    }
    return $directory === storageDirectory() . DIRECTORY_SEPARATOR . $name ? 'data' : 'temp';
}

function achievementsCacheDirectory(): string
{
    return runtimeDirectory('cache');
}

function achievementsCacheRead(string $file, ?int $maxAge = ACHIEVEMENTS_CACHE_TTL_SECONDS): ?array
{
    $path = achievementsCacheDirectory() . DIRECTORY_SEPARATOR . $file;
    if (!is_file($path) || ($maxAge !== null && (time() - filemtime($path)) >= $maxAge)) {
        return null;
    }
    $decoded = json_decode((string) file_get_contents($path), true);
    return is_array($decoded) ? $decoded : null;
}

function achievementsCacheWrite(string $file, array $data): void
{
    $directory = achievementsCacheDirectory();
    if (!is_dir($directory) && !@mkdir($directory, 0750, true) && !is_dir($directory)) {
        return;
    }
    @file_put_contents(
        $directory . DIRECTORY_SEPARATOR . $file,
        json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    );
}

/**
 * Liste der Discord-Spieler, die ein Admin aus der Statistik ausgeblendet hat:
 * [['id' => Discord-ID, 'name' => Anzeigename], ...]
 */
function normalizeStatsExcluded($value): array
{
    if (!is_array($value)) {
        return [];
    }
    $result = [];
    foreach ($value as $entry) {
        $id = is_array($entry) ? (string) ($entry['id'] ?? '') : (string) $entry;
        if (!preg_match('/^\d{5,25}$/', $id) || isset($result[$id])) {
            continue;
        }
        $name = is_array($entry) ? trim((string) ($entry['name'] ?? '')) : '';
        $result[$id] = ['id' => $id, 'name' => mb_substr($name, 0, 60, 'UTF-8')];
        if (count($result) >= STATS_EXCLUDED_MAX) {
            break;
        }
    }
    return array_values($result);
}

function statsExcludedIds(array $store): array
{
    return array_map(static fn(array $entry): string => $entry['id'], $store['settings']['stats_excluded'] ?? []);
}

// ---------- Netzje (LAN-Party) ----------

/** Lokale Zeit „YYYY-MM-DDTHH:MM“ (wie <input type="datetime-local">); sonst null. */
function validNetzjeTime($value): ?string
{
    $text = substr(trim((string) $value), 0, 16);
    if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/', $text)) {
        return null;
    }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $text, new DateTimeZone('Europe/Berlin'));
    return $date !== false && $date->format('Y-m-d\TH:i') === $text ? $text : null;
}

function netzjeText($value, int $max): string
{
    $text = str_replace("\r\n", "\n", trim((string) $value));
    return mb_substr($text, 0, $max, 'UTF-8');
}

/** Twitch: Kanalname; YouTube: Video-ID aus Link oder ID. Sonst ''. */
function validNetzjeStream(string $type, $value): string
{
    $raw = trim((string) $value);
    if ($type === 'twitch') {
        if (preg_match('~twitch\.tv/([A-Za-z0-9_]{3,25})~', $raw, $match)) {
            $raw = $match[1];
        }
        return preg_match('/^[A-Za-z0-9_]{3,25}$/', $raw) ? strtolower($raw) : '';
    }
    if ($type === 'youtube') {
        if (preg_match('~(?:youtu\.be/|[?&]v=|/live/|/embed/)([A-Za-z0-9_-]{11})~', $raw, $match)) {
            $raw = $match[1];
        }
        return preg_match('/^[A-Za-z0-9_-]{11}$/', $raw) ? $raw : '';
    }
    return '';
}

function normalizeNetzjeUserIds($value): array
{
    $ids = [];
    foreach (is_array($value) ? $value : [] as $id) {
        $id = (int) $id;
        if ($id > 0 && !in_array($id, $ids, true)) {
            $ids[] = $id;
        }
    }
    return $ids;
}

/**
 * Party: title, description, location (nur für Angemeldete), info (Mitbringliste), start, end,
 * stream_type (none|twitch|youtube), stream, background (Datei in data/media), slots, next_slot_id.
 * Plätze: seats (Anzahl Plätze, bei der Erstellung festgelegt; 0 = noch nicht festgelegt),
 * requests (User-IDs mit offener Platz-Anfrage, in Reihenfolge der Anfrage),
 * attendees (vom Admin bestätigte User-IDs), declined (abgelehnte User-IDs). Die drei Listen schließen sich aus.
 * Sitzplan: seating = cols, rows (Raster), seats (Liste „x-y“ der Sitzplätze), assign (Sitz → User-ID,
 * nur bestätigte Teilnehmer, je Person höchstens ein Platz), self_select (Teilnehmer wählen selbst).
 * Slot: id, game, start, end, max (0 = ohne Limit), note, status (planned|proposed), proposed_by,
 * signups (User-IDs in Anmeldereihenfolge: die ersten `max` spielen mit, der Rest steht auf der Warteliste).
 */
function normalizeNetzje($value): array
{
    $value = is_array($value) ? $value : [];
    $streamType = (string) ($value['stream_type'] ?? 'none');
    $streamType = in_array($streamType, NETZJE_STREAM_TYPES, true) ? $streamType : 'none';
    $party = [
        'title' => mb_substr(trim((string) ($value['title'] ?? '')), 0, 80, 'UTF-8'),
        'description' => netzjeText($value['description'] ?? '', NETZJE_TEXT_MAX),
        'location' => netzjeText($value['location'] ?? '', 300),
        'info' => netzjeText($value['info'] ?? '', NETZJE_TEXT_MAX),
        'start' => validNetzjeTime($value['start'] ?? '') ?? '',
        'end' => validNetzjeTime($value['end'] ?? '') ?? '',
        'stream_type' => $streamType,
        'stream' => validNetzjeStream($streamType, $value['stream'] ?? ''),
        'seats' => max(0, min(NETZJE_SEATS_MAX, (int) ($value['seats'] ?? 0))),
        'attendees' => normalizeNetzjeUserIds($value['attendees'] ?? []),
        'requests' => [],
        'declined' => [],
        'background' => preg_match('/^netzje-[a-f0-9]{16}\.(?:jpg|png|webp)$/', (string) ($value['background'] ?? '')) ? (string) $value['background'] : '',
        'slots' => [],
        'next_slot_id' => max(1, (int) ($value['next_slot_id'] ?? 1)),
    ];
    $seen = [];
    foreach (is_array($value['slots'] ?? null) ? $value['slots'] : [] as $slot) {
        if (!is_array($slot)) {
            continue;
        }
        $id = (int) ($slot['id'] ?? 0);
        $start = validNetzjeTime($slot['start'] ?? '');
        $end = validNetzjeTime($slot['end'] ?? '');
        $game = mb_substr(trim((string) ($slot['game'] ?? '')), 0, CURRENT_GAME_NAME_MAX, 'UTF-8');
        if ($id <= 0 || isset($seen[$id]) || $start === null || $end === null || $end <= $start || $game === '') {
            continue;
        }
        $seen[$id] = true;
        $party['slots'][] = [
            'id' => $id,
            'game' => $game,
            'start' => $start,
            'end' => $end,
            'max' => max(0, min(999, (int) ($slot['max'] ?? 0))),
            'note' => mb_substr(trim((string) ($slot['note'] ?? '')), 0, 200, 'UTF-8'),
            'status' => ($slot['status'] ?? 'planned') === 'proposed' ? 'proposed' : 'planned',
            'proposed_by' => max(0, (int) ($slot['proposed_by'] ?? 0)),
            'signups' => normalizeNetzjeUserIds($slot['signups'] ?? []),
        ];
        $party['next_slot_id'] = max($party['next_slot_id'], $id + 1);
    }
    $party['seating'] = normalizeNetzjeSeating($value['seating'] ?? [], $party['attendees']);
    // Jede Person steht höchstens in einer der Listen: bestätigt vor angefragt vor abgelehnt
    $party['requests'] = array_values(array_diff(normalizeNetzjeUserIds($value['requests'] ?? []), $party['attendees']));
    $party['declined'] = array_values(array_diff(normalizeNetzjeUserIds($value['declined'] ?? []), $party['attendees'], $party['requests']));
    usort($party['slots'], static fn(array $a, array $b): int => [$a['start'], $a['id']] <=> [$b['start'], $b['id']]);
    $party['slots'] = array_slice($party['slots'], 0, NETZJE_SLOTS_MAX);
    return $party;
}

/** Sitzplan in feste Form bringen; Zuweisungen nur für Sitze im Raster und bestätigte Teilnehmer. */
function normalizeNetzjeSeating($value, array $attendees): array
{
    $value = is_array($value) ? $value : [];
    $cols = max(2, min(NETZJE_SEATING_COLS_MAX, (int) ($value['cols'] ?? 8)));
    $rows = max(2, min(NETZJE_SEATING_ROWS_MAX, (int) ($value['rows'] ?? 5)));
    $seats = [];
    foreach (is_array($value['seats'] ?? null) ? $value['seats'] : [] as $seat) {
        if (preg_match('/^(\d{1,2})-(\d{1,2})$/', (string) $seat, $match) && (int) $match[1] < $cols && (int) $match[2] < $rows) {
            $seats[(int) $match[2] * 100 + (int) $match[1]] = (int) $match[1] . '-' . (int) $match[2];
        }
    }
    ksort($seats); // Reihenfolge: Reihe für Reihe, von links nach rechts (= Platznummern)
    $seats = array_values(array_unique($seats));
    $assign = [];
    $taken = [];
    foreach (is_array($value['assign'] ?? null) ? $value['assign'] : [] as $seat => $userId) {
        $userId = (int) $userId;
        if (in_array((string) $seat, $seats, true) && in_array($userId, $attendees, true) && !isset($taken[$userId])) {
            $assign[(string) $seat] = $userId;
            $taken[$userId] = true;
        }
    }
    return ['cols' => $cols, 'rows' => $rows, 'seats' => $seats, 'assign' => $assign, 'self_select' => (bool) ($value['self_select'] ?? true)];
}

/** Prüft ein Spiel aus dem Formular; gibt [Felder, Fehlertext|null] zurück. */
function validateNetzjeSlotInput(array $payload, array $party): array
{
    $game = trim((string) ($payload['game'] ?? ''));
    if ($game === '' || mb_strlen($game, 'UTF-8') > CURRENT_GAME_NAME_MAX) {
        return [null, 'Bitte das Spiel angeben.'];
    }
    $start = validNetzjeTime($payload['start'] ?? '');
    $end = validNetzjeTime($payload['end'] ?? '');
    if ($start === null || $end === null) {
        return [null, 'Bitte Beginn und Ende angeben.'];
    }
    if ($end <= $start) {
        return [null, 'Das Ende muss nach dem Beginn liegen.'];
    }
    if ($party['start'] !== '' && $party['end'] !== '' && ($start < $party['start'] || $end > $party['end'])) {
        return [null, 'Das Spiel muss innerhalb der Party liegen (' . netzjeTimeLabel($party['start']) . ' bis ' . netzjeTimeLabel($party['end']) . ').'];
    }
    $max = trim((string) ($payload['max'] ?? ''));
    if ($max !== '' && (filter_var($max, FILTER_VALIDATE_INT) === false || (int) $max < 0 || (int) $max > 999)) {
        return [null, 'Die Platzzahl muss leer (ohne Limit) oder eine Zahl bis 999 sein.'];
    }
    $note = trim((string) ($payload['note'] ?? ''));
    if (mb_strlen($note, 'UTF-8') > 200) {
        return [null, 'Der Hinweis darf höchstens 200 Zeichen lang sein.'];
    }
    return [['game' => $game, 'start' => $start, 'end' => $end, 'max' => (int) $max, 'note' => $note], null];
}

function netzjeTimeLabel(string $time): string
{
    $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $time, new DateTimeZone('Europe/Berlin'));
    return $date === false ? $time : $date->format('d.m. H:i');
}

function netzjeSlotIndex(array $party, int $id): ?int
{
    foreach ($party['slots'] as $index => $slot) {
        if ($slot['id'] === $id) {
            return $index;
        }
    }
    return null;
}

/** Anzeigename eines Accounts: Spielername, sonst Benutzername. */
function netzjeUserName(array $store, int $userId): string
{
    $index = findUserIndex($store['users'], $userId);
    if ($index === null) {
        return '';
    }
    $player = playerForUser($store, $store['users'][$index]);
    return $player !== null ? (string) $player['name'] : (string) $store['users'][$index]['username'];
}

/** Schalter (für alle lesbar), Style und Anmeldestatus: für admin.php und den Account-Knopf in der Kopfleiste. */
function siteSettingsResponse(array $store): array
{
    $userIndex = currentUserIndex($store);
    $user = $userIndex === null ? null : $store['users'][$userIndex];
    $loggedIn = $user !== null && empty($user['must_change_password']);
    $isAdmin = $loggedIn && isAdminUser($store, $user);
    $player = $user === null ? null : playerForUser($store, $user);
    return [
        'ok' => true,
        'csrf_token' => csrfToken(),
        'logged_in' => $loggedIn,
        'is_admin' => $isAdmin,
        // Für den Account-Knopf in der Kopfleiste (auf allen Seiten)
        'user' => $user === null ? null : [
            'name' => $player !== null ? (string) $player['name'] : (string) $user['username'],
            'avatar' => (string) ($user['avatar'] ?? ''),
            'must_change_password' => !empty($user['must_change_password']),
        ],
        // Offene Platz-Anfragen der Netzje (nur für Admins, für den Hinweis am Menüpunkt)
        'netzje_requests' => $isAdmin ? count(normalizeNetzje($store['netzje'] ?? [])['requests']) : 0,
        'features' => normalizeFeatures($store['settings']['features'] ?? []),
        'theme' => validateThemeValue($store['settings']['theme'] ?? 'default', 'default'),
        'labels' => ['sidebar' => FEATURE_SIDEBAR_BOXES, 'sidebar_pages' => FEATURE_SIDEBAR_PAGES, 'pages' => FEATURE_PAGES, 'home' => FEATURE_HOME],
    ];
}

/** Antwort für die Seite „Netzje“. Ort nur für Angemeldete; Vorschläge sind (nur lesend) für alle sichtbar. */
function netzjeResponse(array $store): array
{
    $userIndex = currentUserIndex($store);
    $user = $userIndex === null ? null : $store['users'][$userIndex];
    $loggedIn = $user !== null && empty($user['must_change_password']);
    $userId = $loggedIn ? (int) $user['id'] : 0;
    $isAdmin = $loggedIn && isAdminUser($store, $user);
    $party = normalizeNetzje($store['netzje'] ?? []);
    $names = static function (array $ids) use ($store): array {
        $result = [];
        foreach ($ids as $id) {
            $name = netzjeUserName($store, $id);
            if ($name !== '') {
                $result[] = ['id' => $id, 'name' => $name];
            }
        }
        return $result;
    };
    $slots = [];
    foreach ($party['slots'] as $slot) {
        $people = $names($slot['signups']);
        $limit = $slot['max'] > 0 ? $slot['max'] : count($people);
        $slots[] = [
            'id' => $slot['id'],
            'game' => $slot['game'],
            'start' => $slot['start'],
            'end' => $slot['end'],
            'max' => $slot['max'],
            'note' => $slot['note'],
            'status' => $slot['status'],
            'proposed_by' => $slot['status'] === 'proposed' ? netzjeUserName($store, $slot['proposed_by']) : '',
            'own_proposal' => $slot['status'] === 'proposed' && $userId > 0 && $slot['proposed_by'] === $userId,
            'players' => array_slice($people, 0, $limit),
            'waitlist' => array_slice($people, $limit),
            'signed_up' => $userId > 0 && in_array($userId, $slot['signups'], true),
        ];
    }
    return [
        'ok' => true,
        'csrf_token' => csrfToken(),
        'logged_in' => $loggedIn,
        'is_admin' => $isAdmin,
        'my_user_id' => $userId, // für „Hier sitze ich“ im Sitzplan
        'now' => (new DateTimeImmutable('now', new DateTimeZone('Europe/Berlin')))->format('Y-m-d\TH:i'),
        'party' => [
            'title' => $party['title'],
            'description' => $party['description'],
            'has_location' => $party['location'] !== '',
            'location' => $loggedIn ? $party['location'] : null,
            'info' => $party['info'],
            'start' => $party['start'],
            'end' => $party['end'],
            'stream_type' => $party['stream_type'],
            'stream' => $party['stream'],
            'seats' => $party['seats'],
            'seats_taken' => count($party['attendees']),
            'attendees' => $names($party['attendees']),
            'attending' => $userId > 0 && in_array($userId, $party['attendees'], true),
            // Eigener Stand: none | pending (angefragt) | approved (bestätigt) | declined (abgelehnt)
            'my_status' => $userId <= 0 ? 'none'
                : (in_array($userId, $party['attendees'], true) ? 'approved'
                : (in_array($userId, $party['requests'], true) ? 'pending'
                : (in_array($userId, $party['declined'], true) ? 'declined' : 'none'))),
            'requests_count' => count($party['requests']),
            // Namen der Anfragen und Absagen nur für Admins
            'requests' => $isAdmin ? $names($party['requests']) : [],
            'declined' => $isAdmin ? $names($party['declined']) : [],
            'seating' => [
                'cols' => $party['seating']['cols'],
                'rows' => $party['seating']['rows'],
                'seats' => $party['seating']['seats'],
                'self_select' => $party['seating']['self_select'],
                'assign' => array_map(static fn(int $id): array => ['id' => $id, 'name' => netzjeUserName($store, $id)], $party['seating']['assign']),
                'my_seat' => $userId > 0 ? (string) (array_search($userId, $party['seating']['assign'], true) ?: '') : '',
            ],
            'background' => $party['background'] === '' ? null : mediaUrl($party['background']),
        ],
        'slots' => $slots,
    ];
}

/** Host als IPv4/IPv6-Adresse oder Domainname; sonst null. */
function validGameserverHost($value): ?string
{
    $host = trim((string) $value);
    if ($host === '' || strlen($host) > 100) {
        return null;
    }
    if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
        return $host;
    }
    return preg_match('/^(?=.{1,100}$)[A-Za-z0-9]([A-Za-z0-9-]*[A-Za-z0-9])?(\.[A-Za-z0-9]([A-Za-z0-9-]*[A-Za-z0-9])?)*$/', $host) === 1 ? $host : null;
}

function validGameserverPort($value, bool $allowEmpty = false): ?int
{
    if ($allowEmpty && trim((string) $value) === '') {
        return 0;
    }
    $port = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
    return $port === false ? null : (int) $port;
}

/**
 * Gameserver: [id, name, game (für das Logo), host, port, probe_host (interne Adresse für
 * die Statusabfrage, optional), query_port (0 = wie port), query (a2s|minecraft|tcp),
 * password (im Klartext, nur für Angemeldete sichtbar), note]
 */
function normalizeGameservers($value): array
{
    if (!is_array($value)) {
        return [];
    }
    $result = [];
    $seen = [];
    foreach ($value as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $id = (int) ($entry['id'] ?? 0);
        $host = validGameserverHost($entry['host'] ?? '');
        $port = validGameserverPort($entry['port'] ?? 0);
        $name = trim((string) ($entry['name'] ?? ''));
        if ($id <= 0 || isset($seen[$id]) || $host === null || $port === null || $name === '') {
            continue;
        }
        $seen[$id] = true;
        $probeRaw = trim((string) ($entry['probe_host'] ?? ''));
        $query = (string) ($entry['query'] ?? 'tcp');
        $result[] = [
            'id' => $id,
            'name' => mb_substr($name, 0, 60, 'UTF-8'),
            'game' => mb_substr(trim((string) ($entry['game'] ?? '')), 0, CURRENT_GAME_NAME_MAX, 'UTF-8'),
            'host' => $host,
            'port' => $port,
            'probe_host' => $probeRaw === '' ? '' : (string) (validGameserverHost($probeRaw) ?? ''),
            'query_port' => validGameserverPort($entry['query_port'] ?? 0, true) ?? 0,
            'query' => in_array($query, GAMESERVER_QUERY_TYPES, true) ? $query : 'tcp',
            'password' => mb_substr((string) ($entry['password'] ?? ''), 0, 100, 'UTF-8'),
            'note' => mb_substr(trim((string) ($entry['note'] ?? '')), 0, 120, 'UTF-8'),
        ];
    }
    return array_slice($result, 0, GAMESERVER_MAX);
}

/** Prüft die Eingabe aus dem Formular; gibt [Server, Fehlertext|null] zurück (id wird vom Aufrufer gesetzt). */
function validateGameserverInput(array $payload): array
{
    $name = trim((string) ($payload['name'] ?? ''));
    if ($name === '' || mb_strlen($name, 'UTF-8') > 60) {
        return [null, 'Bitte einen Namen mit höchstens 60 Zeichen eingeben.'];
    }
    $game = trim((string) ($payload['game'] ?? ''));
    if ($game === '' || mb_strlen($game, 'UTF-8') > CURRENT_GAME_NAME_MAX) {
        return [null, 'Bitte das Spiel angeben (daraus entsteht das Logo).'];
    }
    $host = validGameserverHost($payload['host'] ?? '');
    if ($host === null) {
        return [null, 'Die IP-Adresse oder der Hostname ist ungültig.'];
    }
    $port = validGameserverPort($payload['port'] ?? '');
    if ($port === null) {
        return [null, 'Der Port muss zwischen 1 und 65535 liegen.'];
    }
    $probeRaw = trim((string) ($payload['probe_host'] ?? ''));
    $probeHost = $probeRaw === '' ? '' : validGameserverHost($probeRaw);
    if ($probeHost === null) {
        return [null, 'Die interne Adresse für die Statusabfrage ist ungültig.'];
    }
    $queryPort = validGameserverPort($payload['query_port'] ?? '', true);
    if ($queryPort === null) {
        return [null, 'Der Abfrage-Port muss leer sein oder zwischen 1 und 65535 liegen.'];
    }
    $query = (string) ($payload['query'] ?? 'tcp');
    if (!in_array($query, GAMESERVER_QUERY_TYPES, true)) {
        return [null, 'Unbekannte Abfrageart.'];
    }
    $password = (string) ($payload['password'] ?? '');
    if (mb_strlen($password, 'UTF-8') > 100) {
        return [null, 'Das Passwort darf höchstens 100 Zeichen lang sein.'];
    }
    $note = trim((string) ($payload['note'] ?? ''));
    if (mb_strlen($note, 'UTF-8') > 120) {
        return [null, 'Der Hinweis darf höchstens 120 Zeichen lang sein.'];
    }
    return [[
        'name' => $name,
        'game' => $game,
        'host' => $host,
        'port' => $port,
        'probe_host' => $probeHost,
        'query_port' => $queryPort,
        'query' => $query,
        'password' => $password,
        'note' => $note,
    ], null];
}

/** Adresse der Gameserver-Abfrage des Bots, abgeleitet aus BOT_STATS_URL; null = Bot nicht eingerichtet. */
function botGameserversUrl(): ?string
{
    return botApiUrl('gameservers');
}

/**
 * Online-Status der Gameserver vom Discord-Bot (20 Sekunden zwischengespeichert).
 * Rückgabe: Status je Server-ID (state online|offline|unknown, players, max, names, checked_at)
 * und 'bot' => ob der Bot erreichbar war.
 */
function gameserverStatuses(): array
{
    $url = botGameserversUrl();
    if ($url === null) {
        return ['bot' => false, 'servers' => []];
    }
    $file = 'gameservers-status.json';
    $cached = achievementsCacheRead($file, GAMESERVER_STATUS_TTL_SECONDS);
    $data = $cached;
    if ($data === null) {
        $context = stream_context_create(['http' => [
            'timeout' => 3,
            'ignore_errors' => true,
            'header' => "User-Agent: Kellerkinder-Website/1.0\r\nAccept: application/json\r\n",
        ]]);
        $raw = @file_get_contents($url, false, $context);
        $decoded = $raw === false ? null : json_decode($raw, true);
        if (is_array($decoded) && ($decoded['ok'] ?? false) === true && is_array($decoded['servers'] ?? null)) {
            $data = $decoded;
            achievementsCacheWrite($file, $decoded);
        } else {
            $data = achievementsCacheRead($file, null); // letzter bekannter Stand
        }
    }
    if ($data === null) {
        return ['bot' => false, 'servers' => []];
    }
    $servers = [];
    foreach ($data['servers'] as $row) {
        if (!is_array($row) || !isset($row['id'])) {
            continue;
        }
        $checked = strtotime((string) ($row['checkedAt'] ?? '')) ?: 0;
        $fresh = $checked > 0 && (time() - $checked) < GAMESERVER_STATUS_MAX_AGE_SECONDS;
        $state = (string) ($row['state'] ?? 'unknown');
        $names = [];
        foreach (is_array($row['players'] ?? null) ? $row['players'] : [] as $playerName) {
            $names[] = mb_substr((string) $playerName, 0, 60, 'UTF-8');
        }
        $servers[(int) $row['id']] = [
            'state' => $fresh && in_array($state, ['online', 'offline'], true) ? $state : 'unknown',
            'players' => isset($row['playerCount']) ? max(0, (int) $row['playerCount']) : null,
            'max' => isset($row['maxPlayers']) ? max(0, (int) $row['maxPlayers']) : null,
            'names' => array_slice($names, 0, 50),
            'checked_at' => (string) ($row['checkedAt'] ?? ''),
        ];
    }
    return ['bot' => true, 'servers' => $servers];
}

/**
 * Spielzeit-Statistik des Discord-Bots (serverseitig abgerufen, 5 Minuten
 * zwischengespeichert). Ist der Bot nicht erreichbar, werden die letzten
 * bekannten Daten mit 'stale' => true geliefert; ohne Daten gibt es null.
 */
function botStatsData(int $days, array $excludedIds = []): ?array
{
    sort($excludedIds, SORT_STRING);
    // Die Ausschlussliste ist Teil des Cache-Schlüssels: Ändert ein Admin sie,
    // wird sofort frisch beim Bot abgefragt.
    $file = 'playtime-' . $days . '-' . substr(md5(implode(',', $excludedIds)), 0, 10) . '.json';
    $directory = achievementsCacheDirectory();
    $path = $directory . DIRECTORY_SEPARATOR . $file;
    $hasCache = is_file($path);

    if ($hasCache && (time() - filemtime($path)) < BOT_STATS_TTL_SECONDS) {
        $cached = json_decode((string) file_get_contents($path), true);
        if (is_array($cached)) {
            return $cached;
        }
    }

    $context = stream_context_create(['http' => [
        'timeout' => 4,
        'ignore_errors' => true,
        'header' => "User-Agent: Kellerkinder-Website/1.0\r\nAccept: application/json\r\n",
    ]]);
    // exclude immer mitschicken (auch leer), damit der Bot nicht auf seine
    // eigene, bis zu 5 Minuten alte Kopie der Liste zurückgreift.
    $query = 'days=' . $days . '&roster=1&exclude=' . implode(',', $excludedIds);
    $raw = @file_get_contents(BOT_STATS_URL . '?' . $query, false, $context);
    $data = $raw === false ? null : json_decode($raw, true);

    if (is_array($data) && ($data['ok'] ?? false) === true && is_array($data['games'] ?? null)) {
        achievementsCacheWrite($file, $data);
        return $data;
    }

    if ($hasCache) {
        $stale = json_decode((string) file_get_contents($path), true);
        if (is_array($stale)) {
            $stale['stale'] = true;
            return $stale;
        }
    }
    return null;
}

/** Adresse eines weiteren Bot-Abrufs, abgeleitet aus BOT_STATS_URL (…/api/stats → …/api/<pfad>). */
function botApiUrl(string $path): ?string
{
    if (!defined('BOT_STATS_URL') || BOT_STATS_URL === '') {
        return null;
    }
    $url = (string) preg_replace('~/api/stats/?$~', '/api/' . $path, (string) BOT_STATS_URL);
    return $url === (string) BOT_STATS_URL ? null : $url;
}

/** JSON vom Bot holen und zwischenspeichern; bei Ausfall der letzte Stand (mit 'stale'), sonst null. */
function botCachedJson(string $url, string $cacheFile, int $ttl): ?array
{
    $cached = achievementsCacheRead($cacheFile, $ttl);
    if ($cached !== null) {
        return $cached;
    }
    $context = stream_context_create(['http' => [
        'timeout' => 4,
        'ignore_errors' => true,
        'header' => "User-Agent: Kellerkinder-Website/1.0\r\nAccept: application/json\r\n",
    ]]);
    $raw = @file_get_contents($url, false, $context);
    $data = $raw === false ? null : json_decode($raw, true);
    if (is_array($data) && ($data['ok'] ?? false) === true) {
        achievementsCacheWrite($cacheFile, $data);
        return $data;
    }
    $stale = achievementsCacheRead($cacheFile, null);
    if ($stale !== null) {
        $stale['stale'] = true;
    }
    return $stale;
}

/** Wer spielt gerade was (Bot ab 1.12.0, 30 Sekunden zwischengespeichert). */
function botNowPlaying(): ?array
{
    $url = botApiUrl('now');
    return $url === null ? null : botCachedJson($url, 'now-playing.json', NOW_PLAYING_TTL_SECONDS);
}

/** Jahresrückblick vom Bot (ab 1.12.0). Die Ausblendeliste gehört zum Cache-Schlüssel. */
function botYearReview(int $year, array $excludedIds = []): ?array
{
    if (!defined('BOT_STATS_URL') || BOT_STATS_URL === '') {
        return null;
    }
    sort($excludedIds, SORT_STRING);
    $file = 'review-' . $year . '-' . substr(md5(implode(',', $excludedIds)), 0, 10) . '.json';
    $ttl = $year >= (int) date('Y') ? YEAR_REVIEW_TTL_CURRENT : YEAR_REVIEW_TTL_PAST;
    $data = botCachedJson(BOT_STATS_URL . '?year=' . $year . '&roster=1&exclude=' . implode(',', $excludedIds), $file, $ttl);
    return is_array($data) && is_array($data['review'] ?? null) ? $data : null;
}

/** Gibt es einen Spieler mit diesem Namen (ohne Groß-/Kleinschreibung)? Liefert den Eintrag oder null. */
function findByName(array $rows, string $name): ?array
{
    $needle = textLower(trim($name));
    foreach ($rows as $row) {
        if (is_array($row) && textLower(trim((string) ($row['name'] ?? ''))) === $needle) {
            return $row;
        }
    }
    return null;
}

/** Alle Netzje-Partys (Archiv + aktuelle, falls geplant): title, start, end, attendees (User-IDs). */
function netzjeAllParties(array $store): array
{
    $parties = $store['netzje_history'] ?? [];
    $current = $store['netzje'];
    if ($current['start'] !== '') {
        $parties[] = ['title' => $current['title'], 'start' => $current['start'], 'end' => $current['end'], 'attendees' => $current['attendees']];
    }
    return $parties;
}

// ---------- Eindrücke (Galerie) ----------

function impressionsDirectory(): string
{
    return storageDirectory() . DIRECTORY_SEPARATOR . 'impressions';
}

/** Dateinamen der Galerie: zufällig und nicht erratbar (32 Hex-Zeichen), optional „-t“ für Vorschaubilder. */
function validImpressionFile(string $file): bool
{
    return (bool) preg_match('/^[a-f0-9]{32}(?:-t)?\.(?:jpg|png|webp)$/', $file);
}

/** Speichert ein Bild aus einer data-URL (verkleinert auf $maxWidth). Gibt den Dateinamen oder einen Fehlertext zurück. */
function impressionStoreImage(string $dataUrl, string $baseName, int $maxWidth): array
{
    if (!preg_match('#^data:image/(png|jpeg|webp);base64,([A-Za-z0-9+/=]+)$#', $dataUrl, $match)) {
        return [null, 'Es werden nur PNG-, JPEG- und WEBP-Bilder unterstützt.'];
    }
    $binary = base64_decode($match[2], true);
    if ($binary === false || $binary === '' || strlen($binary) > IMPRESSION_MAX_BYTES) {
        return [null, 'Das Bild ist ungültig oder zu groß (max. 8 MB).'];
    }
    $info = @getimagesizefromstring($binary);
    $extensions = [IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_WEBP => 'webp'];
    if ($info === false || !isset($extensions[$info[2]])) {
        return [null, 'Die Datei ist kein gültiges PNG-, JPEG- oder WEBP-Bild.'];
    }
    $binary = downscaleBlogImage($binary, $info, $maxWidth);
    $directory = impressionsDirectory();
    if (!is_dir($directory) && !@mkdir($directory, 0750, true) && !is_dir($directory)) {
        return [null, 'Der Ordner für Bilder fehlt (Schreibrechte für data?).'];
    }
    $file = $baseName . '.' . $extensions[$info[2]];
    if (@file_put_contents($directory . DIRECTORY_SEPARATOR . $file, $binary) === false) {
        return [null, 'Das Bild konnte nicht gespeichert werden (Schreibrechte für data?).'];
    }
    $size = @getimagesizefromstring($binary) ?: $info;
    return [['file' => $file, 'width' => (int) $size[0], 'height' => (int) $size[1]], null];
}

/** YouTube-Video-ID oder Twitch-Clip aus einem Link. Gibt ['type' => youtube|twitch_clip, 'ref' => …] oder null. */
function impressionVideoRef(string $url): ?array
{
    $url = trim($url);
    if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?(?:.*&)?v=|shorts/|live/|embed/))([A-Za-z0-9_-]{11})~', $url, $match)) {
        return ['type' => 'youtube', 'ref' => $match[1]];
    }
    if (preg_match('~(?:clips\.twitch\.tv/|twitch\.tv/[A-Za-z0-9_]{3,25}/clip/)([A-Za-z0-9_-]{3,100})~', $url, $match)) {
        return ['type' => 'twitch_clip', 'ref' => $match[1]];
    }
    return null;
}

function normalizeImpressions($value): array
{
    $items = [];
    foreach (is_array($value) ? $value : [] as $item) {
        if (!is_array($item) || (int) ($item['id'] ?? 0) <= 0) {
            continue;
        }
        $type = (string) ($item['type'] ?? 'image');
        if ($type === 'image' && (!validImpressionFile((string) ($item['file'] ?? '')) || !validImpressionFile((string) ($item['thumb'] ?? '')))) {
            continue;
        }
        if (in_array($type, ['youtube', 'twitch_clip'], true) && !preg_match('/^[A-Za-z0-9_-]{3,100}$/', (string) ($item['ref'] ?? ''))) {
            continue;
        }
        if (!in_array($type, ['image', 'youtube', 'twitch_clip'], true)) {
            continue;
        }
        $items[] = [
            'id' => (int) $item['id'],
            'type' => $type,
            'file' => $type === 'image' ? (string) $item['file'] : '',
            'thumb' => $type === 'image' ? (string) $item['thumb'] : '',
            'ref' => $type === 'image' ? '' : (string) $item['ref'],
            'width' => max(0, (int) ($item['width'] ?? 0)),
            'height' => max(0, (int) ($item['height'] ?? 0)),
            'caption' => mb_substr(trim((string) ($item['caption'] ?? '')), 0, IMPRESSION_CAPTION_MAX, 'UTF-8'),
            'album' => mb_substr(trim((string) ($item['album'] ?? '')), 0, IMPRESSION_ALBUM_MAX, 'UTF-8'),
            'user_id' => (int) ($item['user_id'] ?? 0),
            'created_at' => (string) ($item['created_at'] ?? ''),
        ];
    }
    return array_slice($items, -IMPRESSIONS_MAX);
}

/** Liste für die Seite „Eindrücke“ (neueste zuerst). */
function impressionsResponse(array $store, array $user): array
{
    $isAdmin = isAdminUser($store, $user);
    $items = [];
    $albums = [];
    foreach (array_reverse($store['impressions']) as $item) {
        if ($item['album'] !== '') {
            $albums[$item['album']] = ($albums[$item['album']] ?? 0) + 1;
        }
        $items[] = [
            'id' => $item['id'],
            'type' => $item['type'],
            'src' => $item['type'] === 'image' ? 'api.php?action=impression_file&f=' . rawurlencode($item['file']) : null,
            'thumb' => $item['type'] === 'image' ? 'api.php?action=impression_file&f=' . rawurlencode($item['thumb']) : null,
            'ref' => $item['ref'],
            'width' => $item['width'],
            'height' => $item['height'],
            'caption' => $item['caption'],
            'album' => $item['album'],
            'author' => netzjeUserName($store, $item['user_id']),
            'created_at' => $item['created_at'],
            'can_delete' => $isAdmin || $item['user_id'] === (int) $user['id'],
        ];
    }
    arsort($albums);
    return [
        'ok' => true,
        'csrf_token' => csrfToken(),
        'items' => $items,
        'albums' => array_map(static fn(string $name, int $count): array => ['name' => $name, 'count' => $count], array_keys($albums), array_values($albums)),
    ];
}

/** Archiv vergangener Partys: Liste aus title, start, end, attendees (bestätigte User-IDs). */
function normalizeNetzjeHistory($value): array
{
    $history = [];
    foreach (is_array($value) ? $value : [] as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $start = validNetzjeTime($entry['start'] ?? '');
        if ($start === null) {
            continue;
        }
        $history[] = [
            'title' => mb_substr(trim((string) ($entry['title'] ?? '')), 0, 80, 'UTF-8'),
            'start' => $start,
            'end' => validNetzjeTime($entry['end'] ?? '') ?? $start,
            'attendees' => normalizeNetzjeUserIds($entry['attendees'] ?? []),
        ];
    }
    usort($history, static fn(array $a, array $b): int => strcmp($a['start'], $b['start']));
    return array_slice($history, -NETZJE_HISTORY_MAX);
}

function raiderIoCharacterSlug(string $name, string $realm, string $region): string
{
    return $region . '-' . $realm . '-' . $name;
}

function fetchRaiderIoCharacter(string $name, string $realm, string $region): ?array
{
    $url = 'https://raider.io/api/v1/characters/profile'
        . '?region=' . rawurlencode($region)
        . '&realm=' . rawurlencode($realm)
        . '&name=' . rawurlencode($name)
        . '&fields=' . rawurlencode('mythic_plus_best_runs,mythic_plus_scores_by_season:current');

    $context = stream_context_create(['http' => [
        'timeout' => 6,
        'ignore_errors' => true,
        'header' => "User-Agent: Kellerkinder-Kalender/1.0 (+https://github.com/Fischje)\r\n",
    ]]);

    // Bis zu 2 Versuche, da einzelne Raider.IO-Anfragen gelegentlich ohne
    // erkennbaren Grund fehlschlagen (Timeout, kurzzeitige Störung o. ä.).
    for ($attempt = 0; $attempt < 2; $attempt++) {
        $raw = @file_get_contents($url, false, $context);
        if ($raw !== false) {
            $data = json_decode($raw, true);
            if (is_array($data) && isset($data['mythic_plus_best_runs']) && is_array($data['mythic_plus_best_runs'])) {
                return $data;
            }
        }
        if ($attempt === 0) {
            usleep(300000);
        }
    }
    return null;
}

function achievementsWowData(): array
{
    if (WOW_ACHIEVEMENT_CHARACTERS === []) {
        return ['configured' => false, 'runs' => [], 'updated_at' => null];
    }

    $cached = achievementsCacheRead('wow-mplus.json');
    if ($cached !== null) {
        return $cached;
    }

    $runs = [];
    foreach (WOW_ACHIEVEMENT_CHARACTERS as $character) {
        $name = (string) ($character['name'] ?? '');
        $realm = (string) ($character['realm'] ?? '');
        $region = (string) ($character['region'] ?? 'eu');
        if ($name === '' || $realm === '') {
            continue;
        }

        $characterCacheFile = 'wow-char-' . md5(raiderIoCharacterSlug($name, $realm, $region)) . '.json';
        $data = fetchRaiderIoCharacter($name, $realm, $region);

        if ($data !== null) {
            // Frischer Erfolg: als neuen Fallback für künftige Fehlschläge sichern.
            achievementsCacheWrite($characterCacheFile, $data);
        } else {
            // Fehlgeschlagen: auf den letzten bekannten Stand dieses Charakters
            // zurückfallen, statt ihn komplett aus der Liste zu werfen. Der
            // Charakter-Cache hat absichtlich keine eigene Ablaufzeit — lieber
            // einmal veraltete Daten zeigen als den Charakter unsichtbar machen.
            $fallbackPath = achievementsCacheDirectory() . DIRECTORY_SEPARATOR . $characterCacheFile;
            if (is_file($fallbackPath)) {
                $decoded = json_decode((string) file_get_contents($fallbackPath), true);
                if (is_array($decoded)) {
                    $data = $decoded;
                }
            }
        }

        if ($data === null) {
            continue;
        }

        // Nur den besten Run des Charakters übernehmen (höchster Schlüsselstufe).
        $bestRun = null;
        foreach ($data['mythic_plus_best_runs'] as $run) {
            $level = (int) ($run['mythic_level'] ?? 0);
            if ($bestRun === null || $level > $bestRun['level']) {
                $bestRun = [
                    'dungeon' => (string) ($run['dungeon'] ?? '?'),
                    'level' => $level,
                    'upgrades' => (int) ($run['num_keystone_upgrades'] ?? 0),
                ];
            }
        }
        if ($bestRun === null) {
            continue;
        }

        $score = 0.0;
        $seasons = $data['mythic_plus_scores_by_season'] ?? [];
        if (is_array($seasons) && isset($seasons[0]['scores']['all'])) {
            $score = (float) $seasons[0]['scores']['all'];
        }

        $runs[] = [
            'character' => (string) ($data['name'] ?? $name),
            'dungeon' => $bestRun['dungeon'],
            'level' => $bestRun['level'],
            'upgrades' => $bestRun['upgrades'],
            'score' => round($score, 1),
            // Nur echte Raider.IO-Links übernehmen (die Adresse landet als Link im Browser)
            'profile_url' => preg_match('#^https://raider\.io/[A-Za-z0-9_/%.-]+$#', (string) ($data['profile_url'] ?? '')) ? (string) $data['profile_url'] : '',
        ];
    }

    usort($runs, static fn(array $a, array $b): int => $b['score'] <=> $a['score']);

    $result = [
        'configured' => true,
        'runs' => $runs,
        'updated_at' => date('c'),
    ];

    achievementsCacheWrite('wow-mplus.json', $result);

    return $result;
}

function achievementsManualData(array $store, string $game): array
{
    $entry = $store['achievements_manual'][$game] ?? ['title' => null, 'milestones' => [], 'links_title' => null, 'links' => [], 'updated_at' => null];
    $milestones = $entry['milestones'] ?? [];
    return [
        'configured' => $milestones !== [],
        'title' => $entry['title'] ?? null,
        'milestones' => $milestones,
        'links_title' => $entry['links_title'] ?? null,
        'links' => $entry['links'] ?? [],
        'updated_at' => $entry['updated_at'] ?? null,
    ];
}

function storageIsWritable(): bool
{
    $path = storagePath();
    if (is_file($path)) {
        return is_writable($path);
    }
    $directory = storageDirectory();
    return is_dir($directory) && is_writable($directory);
}

function defaultStore(): array
{
    return [
        'schema_version' => 2,
        'next_user_id' => 1,
        'next_player_id' => 1,
        'users' => [],
        'players' => [],
        'availability' => [],
        'custom_dates' => [],
        'remember_tokens' => [],
        'blog_posts' => [],
        'next_post_id' => 1,
        'gameservers' => [],
        'next_gameserver_id' => 1,
        'netzje' => normalizeNetzje([]),
        'netzje_history' => [],
        'impressions' => [],
        'next_impression_id' => 1,
        // Startseite „Home“: Text des Admins (HTML wie im Blog); leer = Vorlage defaultHomeHtml()
        'home' => ['html' => '', 'updated_at' => null],
        'settings' => [
            'admin_player_names' => [],
            'theme' => 'default',
            'stats_excluded' => [],
            'features' => normalizeFeatures([]),
        ],
        'achievements_manual' => [
            'wow' => [
                'title' => null,
                'milestones' => [],
                'links_title' => null,
                'links' => [
                    ['label' => 'Raider.IO Gilde', 'url' => 'https://raider.io/'],
                ],
                'updated_at' => null,
            ],
            'hots' => [
                'title' => null,
                'milestones' => [
                    ['label' => 'Rang', 'value' => 'Noch nicht gepflegt'],
                ],
                'links_title' => null,
                'links' => [],
                'updated_at' => null,
            ],
            'diablo4' => [
                'title' => null,
                'milestones' => [
                    ['label' => 'Season-Reise', 'value' => 'Kapitel 3 von 7 abgeschlossen'],
                    ['label' => 'Höchste Weltstufe', 'value' => 'Weltstufe 4'],
                    ['label' => 'Letzter besiegter Boss', 'value' => 'Uber-Lilith (Gruppe)'],
                ],
                'links_title' => null,
                'links' => [],
                'updated_at' => null,
            ],
            'rocket_league' => [
                'title' => null,
                'milestones' => [
                    ['label' => 'Rang', 'value' => 'Noch nicht gepflegt'],
                ],
                'links_title' => null,
                'links' => [],
                'updated_at' => null,
            ],
        ],
    ];
}

function validIsoDate(string $date): bool
{
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date, new DateTimeZone('Europe/Berlin'));
    $errors = DateTimeImmutable::getLastErrors();
    return $parsed !== false
        && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
        && $parsed->format('Y-m-d') === $date;
}

function normalizeWeekdays($value): array
{
    if (!is_array($value)) {
        return [];
    }
    $days = [];
    foreach ($value as $day) {
        $number = filter_var($day, FILTER_VALIDATE_INT);
        if ($number !== false && $number >= 1 && $number <= 7) {
            $days[(int) $number] = (int) $number;
        }
    }
    ksort($days);
    return array_values($days);
}

function normalizeStore(array $store): array
{
    $defaults = defaultStore();
    $store['schema_version'] = 2;
    $store['users'] = is_array($store['users'] ?? null) ? array_values($store['users']) : [];
    $store['players'] = is_array($store['players'] ?? null) ? array_values($store['players']) : [];
    $store['availability'] = is_array($store['availability'] ?? null) ? $store['availability'] : [];
    $store['custom_dates'] = is_array($store['custom_dates'] ?? null) ? $store['custom_dates'] : [];
    $store['remember_tokens'] = pruneRememberTokens($store['remember_tokens'] ?? []);
    $store['blog_posts'] = normalizeBlogPosts($store['blog_posts'] ?? []);
    $store['next_post_id'] = max(1, (int) ($store['next_post_id'] ?? 1));
    $store['gameservers'] = normalizeGameservers($store['gameservers'] ?? []);
    $store['netzje'] = normalizeNetzje($store['netzje'] ?? []);
    $store['netzje_history'] = normalizeNetzjeHistory($store['netzje_history'] ?? []);
    $store['impressions'] = normalizeImpressions($store['impressions'] ?? []);
    $store['home'] = [
        'html' => (string) ($store['home']['html'] ?? ''),
        'updated_at' => is_string($store['home']['updated_at'] ?? null) ? $store['home']['updated_at'] : null,
    ];
    $store['next_impression_id'] = max(1, (int) ($store['next_impression_id'] ?? 1), 1 + max([0, ...array_map(static fn(array $item): int => $item['id'], $store['impressions'])]));
    $store['next_gameserver_id'] = max(
        1,
        (int) ($store['next_gameserver_id'] ?? 1),
        1 + max([0, ...array_map(static fn(array $server): int => $server['id'], $store['gameservers'])])
    );
    // Die frühere, von Hand gepflegte Spiele-Bibliothek gibt es nicht mehr; alte Einträge fallen beim nächsten Speichern weg.
    unset($store['current_games'], $store['next_game_id']);
    $store['settings'] = is_array($store['settings'] ?? null) ? $store['settings'] : $defaults['settings'];
    $store['settings']['admin_player_names'] = is_array($store['settings']['admin_player_names'] ?? null)
        ? array_values($store['settings']['admin_player_names'])
        : [];
    $store['settings']['theme'] = validateThemeValue($store['settings']['theme'] ?? 'default', 'default');
    $store['settings']['stats_excluded'] = normalizeStatsExcluded($store['settings']['stats_excluded'] ?? []);
    $store['settings']['features'] = normalizeFeatures($store['settings']['features'] ?? []);

    $defaultAchievements = $defaults['achievements_manual'];
    $rawAchievements = is_array($store['achievements_manual'] ?? null) ? $store['achievements_manual'] : [];
    $store['achievements_manual'] = [];
    foreach (ACHIEVEMENT_GAMES as $game) {
        $entry = is_array($rawAchievements[$game] ?? null) ? $rawAchievements[$game] : $defaultAchievements[$game];
        $title = trim((string) ($entry['title'] ?? ''));
        $linksTitle = trim((string) ($entry['links_title'] ?? ''));
        $store['achievements_manual'][$game] = [
            'title' => $title === '' ? null : mb_substr($title, 0, ACHIEVEMENT_TITLE_MAX, 'UTF-8'),
            'milestones' => normalizeAchievementMilestones($entry['milestones'] ?? []),
            'links_title' => $linksTitle === '' ? null : mb_substr($linksTitle, 0, ACHIEVEMENT_TITLE_MAX, 'UTF-8'),
            'links' => normalizeAchievementLinks($entry['links'] ?? []),
            'updated_at' => is_string($entry['updated_at'] ?? null) ? $entry['updated_at'] : null,
        ];
    }

    $maxPlayerId = 0;
    foreach ($store['players'] as &$player) {
        $player['id'] = max(1, (int) ($player['id'] ?? 0));
        $player['name'] = trim((string) ($player['name'] ?? ''));
        $player['user_id'] = isset($player['user_id']) && $player['user_id'] !== null ? (int) $player['user_id'] : null;
        $maxPlayerId = max($maxPlayerId, $player['id']);
    }
    unset($player);

    $maxUserId = 0;
    foreach ($store['users'] as &$user) {
        $user['id'] = max(1, (int) ($user['id'] ?? 0));
        $user['username'] = trim((string) ($user['username'] ?? ''));
        $user['password_hash'] = (string) ($user['password_hash'] ?? '');
        $user['player_id'] = isset($user['player_id']) && $user['player_id'] !== null ? (int) $user['player_id'] : null;
        $user['must_change_password'] = !empty($user['must_change_password']);
        $user['session_version'] = max(1, (int) ($user['session_version'] ?? 1));
        $user['default_weekdays'] = normalizeWeekdays($user['default_weekdays'] ?? []);
        $user['avatar'] = validateAvatarData($user['avatar'] ?? '', true);
        $user['steam_name'] = preg_match('/^(?:\d{17}|[A-Za-z0-9_-]{2,' . STEAM_NAME_MAX . '})$/', (string) ($user['steam_name'] ?? ''))
            ? (string) $user['steam_name']
            : '';
        $user['defaults_effective_from'] = validIsoDate((string) ($user['defaults_effective_from'] ?? ''))
            ? (string) $user['defaults_effective_from']
            : (new DateTimeImmutable('today', new DateTimeZone('Europe/Berlin')))->format('Y-m-d');
        $email = trim((string) ($user['email'] ?? ''));
        $user['email'] = strlen($email) <= EMAIL_MAX && filter_var($email, FILTER_VALIDATE_EMAIL) !== false ? $email : '';
        // Abgelaufene oder kaputte Reset-Links wegräumen
        $resetExpires = strtotime((string) ($user['reset_expires_at'] ?? ''));
        if (!preg_match('/^[a-f0-9]{64}$/', (string) ($user['reset_token_hash'] ?? '')) || $resetExpires === false || $resetExpires <= time()) {
            unset($user['reset_token_hash'], $user['reset_expires_at']);
        }
        $user['created_at'] = (string) ($user['created_at'] ?? gmdate('c'));
        $user['updated_at'] = (string) ($user['updated_at'] ?? $user['created_at']);
        $maxUserId = max($maxUserId, $user['id']);
    }
    unset($user);

    $store['next_player_id'] = max($maxPlayerId + 1, (int) ($store['next_player_id'] ?? 1), 1);
    $store['next_user_id'] = max($maxUserId + 1, (int) ($store['next_user_id'] ?? 1), 1);

    $validCustomDates = [];
    foreach ($store['custom_dates'] as $customDate) {
        $date = trim((string) $customDate);
        if (validIsoDate($date)) {
            $validCustomDates[$date] = $date;
        }
    }
    ksort($validCustomDates, SORT_STRING);
    $store['custom_dates'] = array_values($validCustomDates);

    $adminNames = [];
    foreach ($store['settings']['admin_player_names'] as $name) {
        $clean = trim((string) $name);
        if ($clean !== '') {
            $adminNames[textLower($clean)] = $clean;
        }
    }
    $store['settings']['admin_player_names'] = array_values($adminNames);

    return $store;
}

function decodeStore(string $contents): array
{
    if (trim($contents) === '') {
        return defaultStore();
    }

    $newline = strpos($contents, "\n");
    $json = $newline === false ? '' : substr($contents, $newline + 1);
    if (trim($json) === '') {
        return defaultStore();
    }

    try {
        $store = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        respond(['ok' => false, 'error' => 'Die Datendatei ist beschädigt.'], 500);
    }

    if (!is_array($store)) {
        respond(['ok' => false, 'error' => 'Die Datendatei ist beschädigt.'], 500);
    }

    return normalizeStore($store);
}

function readStore(): array
{
    $path = storagePath();
    if (!is_file($path)) {
        return defaultStore();
    }

    $handle = @fopen($path, 'rb');
    if ($handle === false) {
        respond(['ok' => false, 'error' => 'Die Datendatei konnte nicht gelesen werden.'], 500);
    }

    try {
        if (!flock($handle, LOCK_SH)) {
            respond(['ok' => false, 'error' => 'Die Datendatei ist momentan gesperrt.'], 503);
        }
        $contents = stream_get_contents($handle);
        return decodeStore($contents === false ? '' : $contents);
    } finally {
        flock($handle, LOCK_UN);
        fclose($handle);
    }
}

function withWritableStore(callable $callback): void
{
    if (!ensureStorageDirectory()) {
        respond(['ok' => false, 'code' => 'storage_not_writable', 'error' => 'Der Datenordner konnte nicht erstellt werden.'], 500);
    }

    $path = storagePath();
    if ((is_file($path) && !is_writable($path)) || (!is_file($path) && !is_writable(storageDirectory()))) {
        respond([
            'ok' => false,
            'code' => 'storage_not_writable',
            'error' => 'Der Kalender kann angezeigt, aber nicht gespeichert werden. Der Webserver hat keine Schreibrechte für den Ordner „data“.',
        ], 500);
    }

    $handle = @fopen($path, 'c+');
    if ($handle === false) {
        respond(['ok' => false, 'code' => 'storage_not_writable', 'error' => 'Die Datendatei konnte nicht zum Speichern geöffnet werden.'], 500);
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            respond(['ok' => false, 'error' => 'Die Datendatei ist momentan gesperrt.'], 503);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        $rawContents = $contents === false ? '' : $contents;
        $needsLegacyBackup = trim($rawContents) !== '' && !preg_match('/\"schema_version\"\s*:/', $rawContents);
        $store = decodeStore($rawContents);

        [$response, $status, $shouldSave] = $callback($store);

        if ($shouldSave) {
            if ($needsLegacyBackup) {
                // WICHTIG: Dateiname muss auf .php enden, damit der Webserver
                // sie als PHP ausführt und der Schutzvorspann (STORE_PREFIX)
                // greift — sonst würde die Backup-Datei bei direktem Aufruf
                // Passwort-Hashes und Kontodaten im Klartext ausliefern.
                $backupPath = storageDirectory() . DIRECTORY_SEPARATOR . 'store-before-accounts-backup.php';
                if (!is_file($backupPath)) {
                    $backupPayload = str_starts_with($rawContents, '<?php')
                        ? $rawContents
                        : STORE_PREFIX . $rawContents;
                    @file_put_contents($backupPath, $backupPayload, LOCK_EX);
                }
            }
            $store = normalizeStore($store);
            $encoded = json_encode($store, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            if ($encoded === false) {
                respond(['ok' => false, 'error' => 'Die Daten konnten nicht gespeichert werden.'], 500);
            }
            rewind($handle);
            if (!ftruncate($handle, 0)) {
                respond(['ok' => false, 'error' => 'Die Datendatei konnte nicht geleert werden.'], 500);
            }
            $bytes = fwrite($handle, STORE_PREFIX . $encoded);
            if ($bytes === false || !fflush($handle)) {
                respond(['ok' => false, 'error' => 'Die Daten konnten nicht gespeichert werden.'], 500);
            }
        }

        flock($handle, LOCK_UN);
        fclose($handle);
        respond($response, $status);
    } finally {
        if (is_resource($handle)) {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}

function textLength(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

function textLower(string $value): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($value, 'UTF-8') : strtolower($value);
}

function normalizeSimpleText($value): string
{
    $text = trim((string) $value);
    return preg_replace('/\s+/u', ' ', $text) ?? $text;
}

function validateName($value): string
{
    $name = normalizeSimpleText($value);
    if ($name === '') {
        respond(['ok' => false, 'error' => 'Bitte einen Spielernamen eintragen.'], 422);
    }
    if (textLength($name) > PLAYER_NAME_MAX) {
        respond(['ok' => false, 'error' => 'Der Spielername ist zu lang.'], 422);
    }
    return $name;
}

function validateUsername($value): string
{
    $username = normalizeSimpleText($value);
    if ($username === '') {
        respond(['ok' => false, 'error' => 'Bitte einen Benutzernamen eintragen.'], 422);
    }
    if (textLength($username) > USERNAME_MAX) {
        respond(['ok' => false, 'error' => 'Der Benutzername ist zu lang.'], 422);
    }
    return $username;
}

function passwordRuleState(string $password): array
{
    $state = [
        'length' => textLength($password) >= PASSWORD_MIN_LENGTH,
        'letter' => false,
        'number' => false,
        'special' => false,
    ];

    $characters = preg_split('//u', $password, -1, PREG_SPLIT_NO_EMPTY);
    if ($characters === false) {
        $characters = str_split($password);
    }

    foreach ($characters as $character) {
        if (preg_match('/^\p{L}$/u', $character) === 1) {
            $state['letter'] = true;
            continue;
        }
        if (preg_match('/^\p{N}$/u', $character) === 1) {
            $state['number'] = true;
            continue;
        }
        if (preg_match('/^\s$/u', $character) !== 1) {
            // Jedes sichtbare Zeichen, das weder Buchstabe noch Zahl ist,
            // gilt als Sonderzeichen. Dazu zählen z. B. ! ? # + - _ @ € und Emojis.
            $state['special'] = true;
        }
    }

    return $state;
}

function validatePassword($value, $confirmation = null): string
{
    $password = (string) $value;
    if ($password === '') {
        respond(['ok' => false, 'error' => 'Bitte ein Passwort festlegen.'], 422);
    }

    $rules = passwordRuleState($password);
    if (!$rules['length']) {
        respond(['ok' => false, 'error' => 'Das Passwort muss mindestens 8 Zeichen lang sein.'], 422);
    }
    if (!$rules['letter']) {
        respond(['ok' => false, 'error' => 'Das Passwort muss mindestens einen Buchstaben enthalten.'], 422);
    }
    if (!$rules['number']) {
        respond(['ok' => false, 'error' => 'Das Passwort muss mindestens eine Zahl enthalten.'], 422);
    }
    if (!$rules['special']) {
        respond(['ok' => false, 'error' => 'Das Passwort muss mindestens ein Sonderzeichen wie !, ?, #, +, -, _, @ oder € enthalten. Umlaute gelten als Buchstaben.'], 422);
    }
    if ($confirmation !== null && $password !== (string) $confirmation) {
        respond(['ok' => false, 'error' => 'Die beiden Passwörter stimmen nicht überein.'], 422);
    }
    return $password;
}

function validateAdminAssignedPassword($value, $confirmation = null): string
{
    // Ein Admin, der einem anderen Benutzer ein neues Passwort zuweist (z. B.
    // nach "Passwort vergessen"), muss die strengen Komplexitätsregeln nicht
    // erfüllen — der Benutzer muss beim nächsten Login ohnehin ein eigenes,
    // regelkonformes Passwort festlegen (must_change_password wird gesetzt).
    $password = (string) $value;
    if ($password === '') {
        respond(['ok' => false, 'error' => 'Bitte ein Passwort festlegen.'], 422);
    }
    if ($confirmation !== null && $password !== (string) $confirmation) {
        respond(['ok' => false, 'error' => 'Die beiden Passwörter stimmen nicht überein.'], 422);
    }
    return $password;
}

function validateId($value, string $field = 'ID'): int
{
    $id = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) {
        respond(['ok' => false, 'error' => $field . ' ist ungültig.'], 422);
    }
    return (int) $id;
}

function validateDate($value): string
{
    $date = trim((string) $value);
    if (!validIsoDate($date)) {
        respond(['ok' => false, 'error' => 'Das Datum ist ungültig.'], 422);
    }
    return $date;
}

function validateStatus($value): string
{
    $status = trim((string) $value);
    if (!in_array($status, ALLOWED_STATUSES, true)) {
        respond(['ok' => false, 'error' => 'Der Status ist ungültig.'], 422);
    }
    return $status;
}

function validateNote($value): string
{
    $note = normalizeSimpleText($value);
    if (textLength($note) > NOTE_MAX) {
        respond(['ok' => false, 'error' => 'Der Hinweis ist zu lang.'], 422);
    }
    return $note;
}

function validateGame($value): string
{
    $game = normalizeSimpleText($value);
    if (textLength($game) > GAME_MAX) {
        respond(['ok' => false, 'error' => 'Der Spielwunsch ist zu lang.'], 422);
    }
    return $game;
}

function validateAchievementGame($value): string
{
    $game = (string) $value;
    if (!in_array($game, ACHIEVEMENT_GAMES, true)) {
        respond(['ok' => false, 'error' => 'Unbekanntes Erfolge-Widget.'], 422);
    }
    return $game;
}

function validateAchievementTitle($value): ?string
{
    $title = trim((string) $value);
    if ($title === '') {
        return null;
    }
    if (textLength($title) > ACHIEVEMENT_TITLE_MAX) {
        respond(['ok' => false, 'error' => 'Die Überschrift ist zu lang.'], 422);
    }
    return $title;
}

function isLikelyHttpUrl(string $url): bool
{
    return (bool) preg_match('#^https?://[^\s]+\.[^\s]+$#i', $url);
}

function validateAchievementLinks($value): array
{
    if (!is_array($value)) {
        return [];
    }
    $links = [];
    foreach ($value as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $label = trim((string) ($entry['label'] ?? ''));
        $url = trim((string) ($entry['url'] ?? ''));
        if ($label === '' || $url === '') {
            continue;
        }
        if (textLength($label) > ACHIEVEMENT_LINK_LABEL_MAX || textLength($url) > ACHIEVEMENT_LINK_URL_MAX) {
            respond(['ok' => false, 'error' => 'Ein Link ist zu lang.'], 422);
        }
        if (!isLikelyHttpUrl($url)) {
            respond(['ok' => false, 'error' => 'Ein Link ist ungültig. Bitte mit http:// oder https:// beginnen.'], 422);
        }
        $links[] = ['label' => $label, 'url' => $url];
        if (count($links) >= ACHIEVEMENT_LINKS_MAX) {
            break;
        }
    }
    return $links;
}

function normalizeAchievementLinks($value): array
{
    if (!is_array($value)) {
        return [];
    }
    $links = [];
    foreach ($value as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $label = trim((string) ($entry['label'] ?? ''));
        $url = trim((string) ($entry['url'] ?? ''));
        if ($label === '' || $url === '' || !isLikelyHttpUrl($url)) {
            continue;
        }
        $links[] = [
            'label' => mb_substr($label, 0, ACHIEVEMENT_LINK_LABEL_MAX, 'UTF-8'),
            'url' => mb_substr($url, 0, ACHIEVEMENT_LINK_URL_MAX, 'UTF-8'),
        ];
        if (count($links) >= ACHIEVEMENT_LINKS_MAX) {
            break;
        }
    }
    return $links;
}

function validateAchievementMilestones($value): array
{
    if (!is_array($value)) {
        return [];
    }
    $milestones = [];
    foreach ($value as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $label = trim((string) ($entry['label'] ?? ''));
        $milestoneValue = trim((string) ($entry['value'] ?? ''));
        if ($label === '' || $milestoneValue === '') {
            continue;
        }
        if (textLength($label) > ACHIEVEMENT_LABEL_MAX || textLength($milestoneValue) > ACHIEVEMENT_VALUE_MAX) {
            respond(['ok' => false, 'error' => 'Ein Eintrag ist zu lang.'], 422);
        }
        $milestones[] = ['label' => $label, 'value' => $milestoneValue];
        if (count($milestones) >= ACHIEVEMENT_MILESTONES_MAX) {
            break;
        }
    }
    return $milestones;
}

function normalizeAchievementMilestones($value): array
{
    if (!is_array($value)) {
        return [];
    }
    $milestones = [];
    foreach ($value as $entry) {
        if (!is_array($entry)) {
            continue;
        }
        $label = trim((string) ($entry['label'] ?? ''));
        $milestoneValue = trim((string) ($entry['value'] ?? ''));
        if ($label === '' || $milestoneValue === '') {
            continue;
        }
        $milestones[] = [
            'label' => mb_substr($label, 0, ACHIEVEMENT_LABEL_MAX, 'UTF-8'),
            'value' => mb_substr($milestoneValue, 0, ACHIEVEMENT_VALUE_MAX, 'UTF-8'),
        ];
        if (count($milestones) >= ACHIEVEMENT_MILESTONES_MAX) {
            break;
        }
    }
    return $milestones;
}

function validateThemeValue($value, ?string $fallback = null): string
{
    $theme = trim((string) $value);
    if (in_array($theme, ALLOWED_THEMES, true)) {
        return $theme;
    }
    if ($fallback !== null) {
        return $fallback;
    }
    respond(['ok' => false, 'error' => 'Der ausgewählte Style ist ungültig.'], 422);
}

function validateAvatarData($value, bool $silentFallback = false): string
{
    $data = trim((string) $value);
    if ($data === '') {
        return '';
    }
    if (strlen($data) > AVATAR_MAX_DATA_LENGTH) {
        if ($silentFallback) return '';
        respond(['ok' => false, 'error' => 'Das Avatarbild ist zu groß. Bitte nutze ein kleines Bild.'], 422);
    }
    if (!preg_match('/^data:(image\/(?:png|jpeg|gif|webp));base64,([A-Za-z0-9+\/=]+)$/', $data, $matches)) {
        if ($silentFallback) return '';
        respond(['ok' => false, 'error' => 'Bitte lade ein PNG-, JPG-, GIF- oder WebP-Bild hoch.'], 422);
    }

    $mime = $matches[1];
    if (!in_array($mime, ALLOWED_AVATAR_MIME_TYPES, true)) {
        if ($silentFallback) return '';
        respond(['ok' => false, 'error' => 'Dieser Bildtyp wird für Avatare nicht unterstützt.'], 422);
    }

    $binary = base64_decode($matches[2], true);
    if ($binary === false) {
        if ($silentFallback) return '';
        respond(['ok' => false, 'error' => 'Das Avatarbild konnte nicht gelesen werden.'], 422);
    }
    if (strlen($binary) > 20000) {
        if ($silentFallback) return '';
        respond(['ok' => false, 'error' => 'Das Avatarbild ist zu groß. Bitte nutze ein kleines Bild.'], 422);
    }

    $info = @getimagesizefromstring($binary);
    if ($info === false || empty($info[0]) || empty($info[1])) {
        if ($silentFallback) return '';
        respond(['ok' => false, 'error' => 'Das Avatarbild ist keine gültige Bilddatei.'], 422);
    }
    if ((int) $info[0] > AVATAR_MAX_SIZE || (int) $info[1] > AVATAR_MAX_SIZE) {
        if ($silentFallback) return '';
        respond(['ok' => false, 'error' => 'Das Avatarbild darf maximal 50 × 50 Pixel groß sein.'], 422);
    }

    return 'data:' . $mime . ';base64,' . $matches[2];
}

function findPlayerIndex(array $players, int $id): ?int
{
    foreach ($players as $index => $player) {
        if ((int) ($player['id'] ?? 0) === $id) {
            return $index;
        }
    }
    return null;
}

function findPlayerIndexByName(array $players, string $name): ?int
{
    $needle = textLower($name);
    foreach ($players as $index => $player) {
        if (textLower((string) ($player['name'] ?? '')) === $needle) {
            return $index;
        }
    }
    return null;
}

function findUserIndex(array $users, int $id): ?int
{
    foreach ($users as $index => $user) {
        if ((int) ($user['id'] ?? 0) === $id) {
            return $index;
        }
    }
    return null;
}

function findUserIndexByUsername(array $users, string $username): ?int
{
    $needle = textLower($username);
    foreach ($users as $index => $user) {
        if (textLower((string) ($user['username'] ?? '')) === $needle) {
            return $index;
        }
    }
    return null;
}

function ensureUniqueName(array $players, string $name, ?int $exceptId = null): void
{
    $needle = textLower($name);
    foreach ($players as $player) {
        if ($exceptId !== null && (int) ($player['id'] ?? 0) === $exceptId) {
            continue;
        }
        if (textLower((string) ($player['name'] ?? '')) === $needle) {
            respond(['ok' => false, 'error' => 'Dieser Spielername ist bereits vorhanden.'], 409);
        }
    }
}

function ensureUniqueUsername(array $users, string $username, ?int $exceptId = null): void
{
    $needle = textLower($username);
    foreach ($users as $user) {
        if ($exceptId !== null && (int) ($user['id'] ?? 0) === $exceptId) {
            continue;
        }
        if (textLower((string) ($user['username'] ?? '')) === $needle) {
            respond(['ok' => false, 'error' => 'Dieser Benutzername ist bereits vergeben.'], 409);
        }
    }
}

function currentUserIndex(array $store): ?int
{
    $userId = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
    if ($userId <= 0) {
        return null;
    }
    $index = findUserIndex($store['users'], $userId);
    if ($index === null) {
        unset($_SESSION['user_id'], $_SESSION['auth_version']);
        return null;
    }
    $sessionVersion = (int) ($_SESSION['auth_version'] ?? 0);
    $userVersion = max(1, (int) ($store['users'][$index]['session_version'] ?? 1));
    if ($sessionVersion !== $userVersion) {
        unset($_SESSION['user_id'], $_SESSION['auth_version']);
        return null;
    }
    return $index;
}

function playerForUser(array $store, array $user): ?array
{
    $playerId = isset($user['player_id']) ? (int) $user['player_id'] : 0;
    if ($playerId <= 0) {
        return null;
    }
    $index = findPlayerIndex($store['players'], $playerId);
    return $index === null ? null : $store['players'][$index];
}

function adminNameMap(array $store): array
{
    $map = [];
    foreach ($store['settings']['admin_player_names'] ?? [] as $name) {
        $clean = trim((string) $name);
        if ($clean !== '') {
            $map[textLower($clean)] = $clean;
        }
    }
    return $map;
}

function isAdminUser(array $store, array $user): bool
{
    $player = playerForUser($store, $user);
    if ($player === null) {
        return false;
    }
    return isset(adminNameMap($store)[textLower((string) $player['name'])]);
}

function requireUser(array $store, bool $allowForcedPasswordChange = false): array
{
    $index = currentUserIndex($store);
    if ($index === null) {
        respond(['ok' => false, 'error' => 'Bitte melde dich zuerst an.'], 401);
    }
    $user = $store['users'][$index];
    if (!$allowForcedPasswordChange && !empty($user['must_change_password'])) {
        respond(['ok' => false, 'code' => 'password_change_required', 'error' => 'Bitte ändere zuerst dein vorläufiges Passwort.'], 403);
    }
    return [$index, $user];
}

function isAuthorUser(array $store, array $user): bool
{
    if (isAdminUser($store, $user)) {
        return true;
    }
    return !empty($user['is_author']);
}

function requireAuthor(array $store): array
{
    [$index, $user] = requireUser($store);
    if (!isAuthorUser($store, $user)) {
        respond(['ok' => false, 'error' => 'Für diese Funktion wird das Autorenrecht benötigt.'], 403);
    }
    return [$index, $user];
}

function requireAdmin(array $store): array
{
    [$index, $user] = requireUser($store);
    if (!isAdminUser($store, $user)) {
        respond(['ok' => false, 'error' => 'Diese Funktion ist nur für Administratoren verfügbar.'], 403);
    }
    return [$index, $user];
}

function buildEventDates(array $store): array
{
    $tz = new DateTimeZone('Europe/Berlin');
    $now = new DateTimeImmutable('now', $tz);
    $today = $now->setTime(0, 0, 0);
    $todayIso = $today->format('Y-m-d');

    // Spieltage entstehen ausschließlich aus:
    //   1. den festen Wochentagen, die Spieler in ihrem Account hinterlegt haben,
    //   2. Terminen, an denen ein Spieler ausdrücklich einen Status gesetzt hat,
    //   3. zusätzlich von Hand angelegten Spieltagen.
    // Feste Vorgabetage wie Mittwoch/Sonntag gibt es bewusst nicht mehr.
    $automaticDates = [];

    // 1. Feste Wochentage aus den Accounts in konkrete Datumsangaben übersetzen.
    foreach ($store['users'] as $user) {
        if ((int) ($user['player_id'] ?? 0) <= 0) {
            continue;
        }
        $weekdays = normalizeWeekdays($user['default_weekdays'] ?? []);
        if ($weekdays === []) {
            continue;
        }
        $effectiveFrom = validIsoDate((string) ($user['defaults_effective_from'] ?? ''))
            ? (string) $user['defaults_effective_from']
            : $todayIso;

        foreach ($weekdays as $targetWeekday) {
            // Rückblick auf den zuletzt vergangenen passenden Wochentag …
            $daysSince = ((int) $today->format('N') - $targetWeekday + 7) % 7;
            $previous = $today->modify('-' . ($daysSince === 0 ? 7 : $daysSince) . ' days');
            $automaticDates[$previous->format('Y-m-d')] = true;

            // … und Vorschau so weit, dass die Anzeigegrenze sicher gefüllt ist.
            $daysUntil = ($targetWeekday - (int) $today->format('N') + 7) % 7;
            $first = $today->modify('+' . $daysUntil . ' days');
            for ($index = 0; $index < CALENDAR_UPCOMING_DAYS_LIMIT; $index++) {
                $candidate = $first->modify('+' . ($index * 7) . ' days')->format('Y-m-d');
                if ($candidate >= $effectiveFrom) {
                    $automaticDates[$candidate] = true;
                }
            }
        }
    }

    // 2. Termine, an denen ausdrücklich ein Status gesetzt wurde.
    foreach ($store['availability'] as $key => $entry) {
        if ((string) ($entry['status'] ?? '') === '') {
            continue;
        }
        $eventDate = (string) ($entry['event_date'] ?? (explode(':', (string) $key, 2)[1] ?? ''));
        if (validIsoDate($eventDate)) {
            $automaticDates[$eventDate] = true;
        }
    }

    // Außerdem: Tage der LAN-Party (Netzje) erscheinen immer und werden im Kalender rot markiert.
    $netzjeDays = netzjeCalendarDays($store);
    foreach (array_keys($netzjeDays) as $netzjeDay) {
        $automaticDates[$netzjeDay] = true;
    }

    // 3. Von Hand angelegte Spieltage.
    $customMap = [];
    foreach ($store['custom_dates'] as $customDate) {
        $customMap[(string) $customDate] = true;
    }

    $pastCandidates = [];
    foreach (array_keys($automaticDates + $customMap) as $date) {
        if ($date < $todayIso) {
            $pastCandidates[] = $date;
        }
    }

    $visible = [];

    // Der letzte vergangene Spieltag bleibt nur bis zu einer festen Uhrzeit am
    // folgenden Kalendertag sichtbar (Standard: 12 Uhr mittags) — danach
    // verschwindet er, auch wenn noch kein neuerer vergangener Termin
    // nachgerückt ist.
    if ($pastCandidates !== []) {
        rsort($pastCandidates, SORT_STRING);
        $mostRecentPast = $pastCandidates[0];
        $mostRecentPastDate = new DateTimeImmutable($mostRecentPast, $tz);
        $visibleUntil = $mostRecentPastDate
            ->modify('+1 day')
            ->setTime(CALENDAR_PAST_DAY_VISIBLE_UNTIL_HOUR, 0, 0);
        if ($now < $visibleUntil) {
            $visible[$mostRecentPast] = true;
        }
    }

    // Kommende Spieltage aus allen Quellen zusammenführen, dann unabhängig von
    // der Quelle auf die nächsten CALENDAR_UPCOMING_DAYS_LIMIT Tage begrenzen.
    $upcoming = [];
    foreach (array_keys($automaticDates) as $date) {
        if ($date >= $todayIso) {
            $upcoming[$date] = true;
        }
    }
    foreach (array_keys($customMap) as $date) {
        if ($date >= $todayIso) {
            $upcoming[$date] = true;
        }
    }
    $upcomingList = array_keys($upcoming);
    sort($upcomingList, SORT_STRING);
    $upcomingList = array_slice($upcomingList, 0, CALENDAR_UPCOMING_DAYS_LIMIT);
    foreach ($upcomingList as $date) {
        $visible[$date] = true;
    }

    ksort($visible, SORT_STRING);
    $events = [];
    foreach (array_keys($visible) as $date) {
        $events[] = [
            'date' => $date,
            'is_past' => $date < $todayIso,
            'is_today' => $date === $todayIso,
            'is_custom' => isset($customMap[$date]),
            'netzje' => $netzjeDays[$date] ?? null,
        ];
    }
    return $events;
}

/** Kalendertage der geplanten Party: ['YYYY-MM-DD' => ['title' => …]], höchstens 14 Tage. */
function netzjeCalendarDays(array $store): array
{
    $party = normalizeNetzje($store['netzje'] ?? []);
    if ($party['start'] === '' || $party['end'] === '') {
        return [];
    }
    $tz = new DateTimeZone('Europe/Berlin');
    $day = new DateTimeImmutable(substr($party['start'], 0, 10), $tz);
    // Endet die Party um Mitternacht, zählt der Folgetag nicht mehr dazu.
    $last = (new DateTimeImmutable(str_replace('T', ' ', $party['end']), $tz))->modify('-1 minute')->format('Y-m-d');
    $days = [];
    for ($index = 0; $index < 14 && $day->format('Y-m-d') <= $last; $index++, $day = $day->modify('+1 day')) {
        $days[$day->format('Y-m-d')] = ['title' => $party['title'] !== '' ? $party['title'] : 'Netzje'];
    }
    return $days;
}

function visibleDateMap(array $store): array
{
    $map = [];
    foreach (buildEventDates($store) as $event) {
        $map[$event['date']] = true;
    }
    return $map;
}

function effectiveAvailability(array $store, array $eventDates): array
{
    $visible = [];
    foreach ($eventDates as $event) {
        $visible[(string) $event['date']] = $event;
    }

    $availability = [];
    foreach ($store['availability'] as $key => $entry) {
        $playerId = (int) ($entry['player_id'] ?? (int) explode(':', (string) $key, 2)[0]);
        $eventDate = (string) ($entry['event_date'] ?? (explode(':', (string) $key, 2)[1] ?? ''));
        if ($playerId <= 0 || !isset($visible[$eventDate])) {
            continue;
        }
        $availability[$playerId . ':' . $eventDate] = [
            'status' => in_array((string) ($entry['status'] ?? ''), ALLOWED_STATUSES, true) ? (string) ($entry['status'] ?? '') : '',
            'note' => (string) ($entry['note'] ?? ''),
            'game' => (string) ($entry['game'] ?? ''),
            'source' => 'explicit',
        ];
    }

    $todayIso = (new DateTimeImmutable('today', new DateTimeZone('Europe/Berlin')))->format('Y-m-d');
    foreach ($store['users'] as $user) {
        $playerId = (int) ($user['player_id'] ?? 0);
        if ($playerId <= 0) {
            continue;
        }
        $weekdays = normalizeWeekdays($user['default_weekdays'] ?? []);
        if ($weekdays === []) {
            continue;
        }
        $effectiveFrom = validIsoDate((string) ($user['defaults_effective_from'] ?? ''))
            ? (string) $user['defaults_effective_from']
            : $todayIso;
        foreach ($eventDates as $event) {
            $date = (string) $event['date'];
            if ($date < $todayIso || $date < $effectiveFrom) {
                continue;
            }
            $key = $playerId . ':' . $date;
            if (array_key_exists($key, $availability)) {
                continue;
            }
            $weekday = (int) (new DateTimeImmutable($date, new DateTimeZone('Europe/Berlin')))->format('N');
            if (in_array($weekday, $weekdays, true)) {
                $availability[$key] = [
                    'status' => 'online',
                    'note' => '',
                    'game' => '',
                    'source' => 'recurring',
                ];
            }
        }
    }
    return $availability;
}


function gameOptions(array $store): array
{
    $games = [];
    foreach ($store['availability'] as $entry) {
        $game = normalizeSimpleText($entry['game'] ?? '');
        if ($game === '') {
            continue;
        }
        $games[textLower($game)] = $game;
    }
    $values = array_values($games);
    usort($values, static fn(string $a, string $b): int => strcasecmp($a, $b));
    return $values;
}

function replaceAdminName(array &$store, string $oldName, string $newName): void
{
    $oldKey = textLower($oldName);
    $updated = [];
    foreach ($store['settings']['admin_player_names'] as $name) {
        $candidate = textLower((string) $name) === $oldKey ? $newName : (string) $name;
        if ($candidate !== '') {
            $updated[textLower($candidate)] = $candidate;
        }
    }
    $store['settings']['admin_player_names'] = array_values($updated);
}

function removeAdminName(array &$store, string $name): void
{
    $needle = textLower($name);
    $store['settings']['admin_player_names'] = array_values(array_filter(
        $store['settings']['admin_player_names'],
        static fn($candidate): bool => textLower((string) $candidate) !== $needle
    ));
}

function countActiveAdminUsers(array $store): int
{
    $count = 0;
    foreach ($store['users'] as $user) {
        if (isAdminUser($store, $user)) {
            $count++;
        }
    }
    return $count;
}

function ensureNotReservedAdminName(array $store, string $name, ?array $actingUser = null): void
{
    $reserved = isset(adminNameMap($store)[textLower($name)]);
    if (!$reserved) {
        return;
    }
    if ($actingUser !== null && isAdminUser($store, $actingUser)) {
        return;
    }
    respond(['ok' => false, 'error' => 'Dieser Spielername ist als Administrator reserviert.'], 403);
}

function createOrClaimPlayer(array &$store, int $userId, string $name, ?int $currentPlayerId = null, bool $allowAdminName = false): int
{
    $existingIndex = findPlayerIndexByName($store['players'], $name);
    if ($existingIndex !== null) {
        $existing = $store['players'][$existingIndex];
        $ownerId = isset($existing['user_id']) && $existing['user_id'] !== null ? (int) $existing['user_id'] : null;
        if ($ownerId !== null && $ownerId !== $userId) {
            respond(['ok' => false, 'error' => 'Dieser Spieler ist bereits mit einem anderen Account verbunden.'], 409);
        }
        $store['players'][$existingIndex]['user_id'] = $userId;
        if ($currentPlayerId !== null && $currentPlayerId !== (int) $existing['id']) {
            $oldIndex = findPlayerIndex($store['players'], $currentPlayerId);
            if ($oldIndex !== null) {
                $store['players'][$oldIndex]['user_id'] = null;
            }
        }
        return (int) $existing['id'];
    }

    $id = (int) $store['next_player_id'];
    $store['next_player_id'] = $id + 1;
    $store['players'][] = ['id' => $id, 'name' => $name, 'user_id' => $userId];
    if ($currentPlayerId !== null) {
        $oldIndex = findPlayerIndex($store['players'], $currentPlayerId);
        if ($oldIndex !== null) {
            $store['players'][$oldIndex]['user_id'] = null;
        }
    }
    return $id;
}

function renameOrAssignUserPlayer(array &$store, int $userIndex, string $newName, bool $actingAsAdmin): void
{
    $user = $store['users'][$userIndex];
    $currentPlayerId = isset($user['player_id']) && $user['player_id'] !== null ? (int) $user['player_id'] : null;
    $currentPlayerIndex = $currentPlayerId !== null ? findPlayerIndex($store['players'], $currentPlayerId) : null;
    $currentPlayer = $currentPlayerIndex !== null ? $store['players'][$currentPlayerIndex] : null;
    $userIsAdmin = isAdminUser($store, $user);

    if (!$actingAsAdmin && !$userIsAdmin) {
        ensureNotReservedAdminName($store, $newName, null);
    }

    $existingIndex = findPlayerIndexByName($store['players'], $newName);
    if ($existingIndex !== null && ($currentPlayerId === null || (int) $store['players'][$existingIndex]['id'] !== $currentPlayerId)) {
        $ownerId = isset($store['players'][$existingIndex]['user_id']) && $store['players'][$existingIndex]['user_id'] !== null
            ? (int) $store['players'][$existingIndex]['user_id']
            : null;
        if ($ownerId !== null && $ownerId !== (int) $user['id']) {
            respond(['ok' => false, 'error' => 'Dieser Spieler ist bereits mit einem anderen Account verbunden.'], 409);
        }
        if ($currentPlayerIndex !== null) {
            $store['players'][$currentPlayerIndex]['user_id'] = null;
        }
        $store['players'][$existingIndex]['user_id'] = (int) $user['id'];
        $store['users'][$userIndex]['player_id'] = (int) $store['players'][$existingIndex]['id'];
        if ($userIsAdmin && $currentPlayer !== null) {
            replaceAdminName($store, (string) $currentPlayer['name'], (string) $store['players'][$existingIndex]['name']);
        }
        return;
    }

    if ($currentPlayerIndex !== null) {
        $oldName = (string) $store['players'][$currentPlayerIndex]['name'];
        ensureUniqueName($store['players'], $newName, $currentPlayerId);
        $store['players'][$currentPlayerIndex]['name'] = $newName;
        $store['players'][$currentPlayerIndex]['user_id'] = (int) $user['id'];
        if ($userIsAdmin) {
            replaceAdminName($store, $oldName, $newName);
        }
        return;
    }

    ensureUniqueName($store['players'], $newName);
    $playerId = createOrClaimPlayer($store, (int) $user['id'], $newName, null, $actingAsAdmin);
    $store['users'][$userIndex]['player_id'] = $playerId;
}

/**
 * Entfernt Konten, die es nicht mehr gibt, aus der Netzje: Teilnehmer, Anfragen, Absagen, Anmeldungen zu
 * Spielen, Sitzplätze, offene Vorschläge und das Archiv. Gibt die Anzahl der entfernten Einträge zurück.
 */
function pruneNetzjeUsers(array &$store, array $validIds): int
{
    $valid = array_flip($validIds);
    $removed = 0;
    $keep = static function (array $ids) use ($valid, &$removed): array {
        $kept = array_values(array_filter($ids, static fn(int $id): bool => isset($valid[$id])));
        $removed += count($ids) - count($kept);
        return $kept;
    };
    foreach (['attendees', 'requests', 'declined'] as $list) {
        $store['netzje'][$list] = $keep($store['netzje'][$list]);
    }
    $slots = [];
    foreach ($store['netzje']['slots'] as $slot) {
        if ($slot['status'] === 'proposed' && !isset($valid[$slot['proposed_by']])) {
            $removed++;
            continue;
        }
        $slot['signups'] = $keep($slot['signups']);
        $slots[] = $slot;
    }
    $store['netzje']['slots'] = $slots;
    foreach ($store['netzje']['seating']['assign'] as $seat => $userId) {
        if (!isset($valid[$userId])) {
            unset($store['netzje']['seating']['assign'][$seat]);
            $removed++;
        }
    }
    foreach ($store['netzje_history'] as $index => $party) {
        $store['netzje_history'][$index]['attendees'] = $keep($party['attendees']);
    }
    return $removed;
}

/** Dateien eines Ordners, die älter als $minAge Sekunden sind und $isUnused erfüllen: [Pfad => Größe]. */
function cleanupFiles(string $directory, int $minAge, callable $isUnused): array
{
    $found = [];
    if (!is_dir($directory)) {
        return $found;
    }
    foreach (scandir($directory) ?: [] as $name) {
        $path = $directory . DIRECTORY_SEPARATOR . $name;
        if ($name === '.' || $name === '..' || !is_file($path) || time() - (int) filemtime($path) < $minAge) {
            continue;
        }
        if ($isUnused($name)) {
            $found[$path] = (int) filesize($path);
        }
    }
    return $found;
}

/**
 * „Daten aufräumen“ (Admin-Seite): verwaiste Einträge im Store und nicht mehr benutzte Dateien.
 * Mit $apply = false wird nur gezählt (Vorschau), sonst entfernt. Kalenderdaten, Blog, Galerie,
 * Netzje-Archiv und Spielzeit bleiben erhalten – es fällt nur weg, worauf nichts mehr verweist.
 * Rückgabe: Liste aus ['key', 'label', 'count', 'bytes'] (nur Posten mit Treffern).
 */
function cleanupData(array &$store, bool $apply): array
{
    $items = [];
    $note = static function (string $key, string $label, int $count, int $bytes = 0) use (&$items): void {
        if ($count > 0) {
            $items[] = ['key' => $key, 'label' => $label, 'count' => $count, 'bytes' => $bytes];
        }
    };
    $userIds = array_map(static fn(array $user): int => (int) $user['id'], $store['users']);
    $users = array_flip($userIds);
    $players = array_flip(array_map(static fn(array $player): int => (int) $player['id'], $store['players']));
    $today = (new DateTimeImmutable('today', new DateTimeZone('Europe/Berlin')))->format('Y-m-d');
    $monthAgo = (new DateTimeImmutable('today -30 days', new DateTimeZone('Europe/Berlin')))->format('Y-m-d');

    // Kalender: Einträge gelöschter Spieler und leere Einträge („Offen“ ohne Hinweis) vergangener Tage
    $orphans = 0;
    $empty = 0;
    foreach ($store['availability'] as $key => $entry) {
        [$keyPlayer, $keyDate] = array_pad(explode(':', (string) $key, 2), 2, '');
        $playerId = is_array($entry) ? (int) ($entry['player_id'] ?? $keyPlayer) : 0;
        $date = is_array($entry) ? (string) ($entry['event_date'] ?? $keyDate) : '';
        if (!isset($players[$playerId]) || !validIsoDate($date)) {
            $orphans++;
        } elseif ($date < $today && (string) ($entry['status'] ?? '') === '' && trim((string) ($entry['note'] ?? '')) === '' && trim((string) ($entry['game'] ?? '')) === '') {
            $empty++;
        } else {
            continue;
        }
        if ($apply) {
            unset($store['availability'][$key]);
        }
    }
    $note('availability_orphans', 'Kalendereinträge von gelöschten Spielern', $orphans);
    $note('availability_empty', 'Leere Kalendereinträge („Offen“) vergangener Tage', $empty);

    $oldDates = array_values(array_filter($store['custom_dates'], static fn(string $date): bool => $date < $monthAgo));
    if ($apply) {
        $store['custom_dates'] = array_values(array_diff($store['custom_dates'], $oldDates));
    }
    $note('custom_dates', 'Zusätzliche Spieltage, die mehr als 30 Tage vorbei sind', count($oldDates));

    // Verweise auf gelöschte Konten bzw. Spieler
    $copy = $store;
    $netzjeRemoved = pruneNetzjeUsers($copy, $userIds);
    if ($apply) {
        $store = $copy;
    }
    $note('netzje', 'Netzje-Einträge gelöschter Konten (Plätze, Anfragen, Anmeldungen, Archiv)', $netzjeRemoved);

    $links = 0;
    foreach ($store['players'] as $index => $player) {
        if ($player['user_id'] !== null && !isset($users[(int) $player['user_id']])) {
            $links++;
            if ($apply) {
                $store['players'][$index]['user_id'] = null;
            }
        }
    }
    foreach ($store['users'] as $index => $user) {
        if ($user['player_id'] !== null && !isset($players[(int) $user['player_id']])) {
            $links++;
            if ($apply) {
                $store['users'][$index]['player_id'] = null;
            }
        }
    }
    $note('links', 'Verknüpfungen zwischen Spieler und Konto, deren Gegenstück fehlt', $links);

    $tokens = count(array_filter($store['remember_tokens'], static fn(array $token): bool => !isset($users[(int) $token['user_id']])));
    if ($apply) {
        $store['remember_tokens'] = array_values(array_filter($store['remember_tokens'], static fn(array $token): bool => isset($users[(int) $token['user_id']])));
    }
    $note('tokens', '„Angemeldet bleiben“-Schlüssel gelöschter Konten', $tokens);

    $playerNames = [];
    foreach ($store['players'] as $player) {
        $playerNames[textLower((string) $player['name'])] = true;
    }
    $adminNames = array_values(array_filter($store['settings']['admin_player_names'], static fn($name): bool => isset($playerNames[textLower((string) $name)])));
    $note('admin_names', 'Admin-Namen ohne passenden Spieler (geben keine Rechte)', count($store['settings']['admin_player_names']) - count($adminNames));
    if ($apply) {
        $store['settings']['admin_player_names'] = $adminNames;
    }

    // Veraltete Felder früherer Versionen (alles, was die Website nicht mehr liest)
    $fields = 0;
    $known = array_flip(array_merge(array_keys(defaultStore()), ['schema_version']));
    foreach (array_keys($store) as $key) {
        if (!isset($known[$key])) {
            $fields++;
            if ($apply) {
                unset($store[$key]);
            }
        }
    }
    $knownSettings = array_flip(array_keys(defaultStore()['settings']));
    foreach (array_keys($store['settings']) as $key) {
        if (!isset($knownSettings[$key])) {
            $fields++;
            if ($apply) {
                unset($store['settings'][$key]);
            }
        }
    }
    $knownUser = array_flip(['id', 'username', 'password_hash', 'player_id', 'must_change_password', 'is_author', 'session_version',
        'default_weekdays', 'avatar', 'steam_name', 'email', 'reset_token_hash', 'reset_expires_at', 'defaults_effective_from', 'created_at', 'updated_at']);
    foreach ($store['users'] as $index => $user) {
        foreach (array_keys($user) as $key) {
            if (!isset($knownUser[$key])) {
                $fields++;
                if ($apply) {
                    unset($store['users'][$index][$key]);
                }
            }
        }
    }
    $knownPlayer = ['id' => true, 'name' => true, 'user_id' => true];
    foreach ($store['players'] as $index => $player) {
        foreach (array_keys($player) as $key) {
            if (!isset($knownPlayer[$key])) {
                $fields++;
                if ($apply) {
                    unset($store['players'][$index][$key]);
                }
            }
        }
    }
    $note('fields', 'Veraltete Datenfelder früherer Versionen', $fields);

    // Dateien: nur, worauf nichts mehr verweist und was nicht gerade erst entstanden ist
    $files = [];
    $referenced = [];
    foreach ($store['impressions'] as $item) {
        $referenced[$item['file']] = true;
        $referenced[$item['thumb']] = true;
    }
    $files['impressions'] = ['Galerie-Bilder ohne Eintrag', cleanupFiles(impressionsDirectory(), 3600, static fn(string $name): bool => !isset($referenced[$name]))];

    $mediaUsed = [];
    foreach ([gameIconIndex(), discordAvatarIndex()] as $index) {
        foreach ($index as $entry) {
            if (is_array($entry) && is_string($entry['file'] ?? null)) {
                $mediaUsed[$entry['file']] = true;
            }
        }
    }
    $mediaUsed[$store['netzje']['background']] = true;
    $blogText = implode("\n", array_column($store['blog_posts'], 'content_html')) . "\n" . $store['home']['html'];
    $mediaDir = mediaDirectory();
    $files['media'] = ['Bilder ohne Verwendung (Spiele-Symbole, Profilbilder, Netzje-Hintergründe, Blog-Bilder)',
        cleanupFiles($mediaDir, 3600, static function (string $name) use ($mediaUsed, $blogText, $mediaDir): bool {
            if (str_starts_with($name, 'blog-')) {
                // Blog-Bilder: gebraucht, solange ein Beitrag (auch ein Entwurf) sie zeigt; neue erst nach einem Tag
                return !str_contains($blogText, 'media.php?f=' . $name) && time() - (int) filemtime($mediaDir . DIRECTORY_SEPARATOR . $name) > 86400;
            }
            return !isset($mediaUsed[$name]);
        })];

    // Zwischenspeicher: was 30 Tage nicht erneuert wurde, braucht niemand mehr (wird bei Bedarf neu geholt).
    // Die Verzeichnisse der Symbole/Profilbilder und die festen Steam-IDs bleiben.
    $keepCache = static fn(string $name): bool => in_array($name, [GAME_ICON_INDEX_FILE, DISCORD_AVATAR_INDEX_FILE, THROTTLE_FILE], true) || str_starts_with($name, 'steam-id-');
    $files['cache'] = ['Veraltete Zwischenspeicher-Dateien (älter als 30 Tage)',
        cleanupFiles(achievementsCacheDirectory(), 30 * 86400, static fn(string $name): bool => !$keepCache($name))];

    $files['blog'] = ['Blog-Bilder, die in keinem Beitrag (auch keinem Entwurf) vorkommen',
        cleanupFiles(__DIR__ . '/assets/blog', 86400, static fn(string $name): bool => preg_match('/\.(?:png|jpe?g|gif|webp)$/i', $name) === 1 && !str_contains($blogText, 'assets/blog/' . $name))];

    $legacy = storageDirectory() . DIRECTORY_SEPARATOR . 'store-before-accounts-backup.php';
    $files['legacy'] = ['Alte Sicherung von vor der Umstellung auf Benutzerkonten', is_file($legacy) ? [$legacy => (int) filesize($legacy)] : []];

    foreach ($files as $key => [$label, $paths]) {
        if ($apply) {
            foreach (array_keys($paths) as $path) {
                @unlink($path);
            }
        }
        $note('files_' . $key, $label, count($paths), array_sum($paths));
    }
    return $items;
}

/** Vorlage der Startseite „Home“: kurze Beschreibung aller Menüpunkte (ohne Admin). */
function defaultHomeHtml(): string
{
    return <<<'HTML'
<h2>Wer wir sind</h2>
<p>Wir sind eine Truppe, die gerne zusammen zockt – meistens online, ab und zu bei einer LAN-Party. Hier findest du alles rund um unsere Spieleabende.</p>
<h2>Was du hier findest</h2>
<h3>📅 <a href="kalender.php">Kalender</a></h3>
<p>Wer zockt wann? Trag ein, an welchen Tagen du online bist, später kommst, verhindert oder im Urlaub bist – und was du spielen willst. Feste Spieltage hinterlegst du einmal in deinem Konto.</p>
<h3>📝 <a href="blog.php">Blog</a></h3>
<p>Spielerlebnisse, Erfolge und Neuigkeiten aus unserer Community – mit Bildern und Videos.</p>
<h3>📊 <a href="games.php">Spiele-Statistik</a></h3>
<p>Was wir wirklich spielen, automatisch erfasst von unserem Discord-Bot:</p>
<ul>
<li><strong>Spiele</strong> – die meistgespielten Spiele für verschiedene Zeiträume, bis zur kompletten Liste.</li>
<li><strong>Spieler</strong> – die Rangliste nach Spielzeit, mit Profil, Lieblingsspielen und Abzeichen.</li>
<li><strong>Jahresrückblick</strong> – das Spielejahr in Zahlen: Monate, Highlights, Top-Spiele und neue Spiele.</li>
</ul>
<h3>🖥️ <a href="gameserver.php">Server</a></h3>
<p>Unsere eigenen Gameserver mit Adresse und Live-Status: Wer ist gerade drauf? Das Passwort siehst du nach der Anmeldung.</p>
<h3>🔌 <a href="netzje.php">Netzje</a></h3>
<p>Unsere LAN-Party: Termin, Platz anfragen, Sitzplan und Stundenplan mit Spielen zum Anmelden – eigene Spielvorschläge sind willkommen.</p>
<h3>📸 Eindrücke</h3>
<p>Fotos und Clips von unseren Abenden und LAN-Partys – nur für angemeldete Mitglieder sichtbar.</p>
<p>Noch kein Konto? <a href="kalender.php#registrieren">Registriere dich</a> – dann kannst du überall mitmachen.</p>
HTML;
}

/** Antwort für die Startseite „Home“. */
function homeResponse(array $store): array
{
    $userIndex = currentUserIndex($store);
    $user = $userIndex === null ? null : $store['users'][$userIndex];
    $custom = $store['home']['html'] !== '';
    return [
        'ok' => true,
        'csrf_token' => csrfToken(),
        'html' => $custom ? $store['home']['html'] : sanitizeBlogHtml(defaultHomeHtml()),
        'is_template' => !$custom,
        'updated_at' => $store['home']['updated_at'],
        'can_edit' => $user !== null && empty($user['must_change_password']) && isAdminUser($store, $user),
    ];
}

function bootstrapResponse(array $store): array
{
    $eventDates = buildEventDates($store);
    $availability = effectiveAvailability($store, $eventDates);
    $currentIndex = currentUserIndex($store);
    $currentUser = $currentIndex !== null ? $store['users'][$currentIndex] : null;
    $isAdmin = $currentUser !== null && isAdminUser($store, $currentUser);
    $mustChange = $currentUser !== null && !empty($currentUser['must_change_password']);
    $currentPlayer = $currentUser !== null ? playerForUser($store, $currentUser) : null;

    $players = array_values($store['players']);
    usort($players, static fn(array $a, array $b): int => strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? '')));

    $userAvatars = [];
    foreach ($store['users'] as $user) {
        $userAvatars[(int) ($user['id'] ?? 0)] = (string) ($user['avatar'] ?? '');
    }

    $playerPayload = array_map(static function (array $player) use ($currentUser, $isAdmin, $mustChange, $userAvatars): array {
        $ownerId = isset($player['user_id']) && $player['user_id'] !== null ? (int) $player['user_id'] : null;
        $isOwn = $currentUser !== null && $ownerId === (int) $currentUser['id'];
        return [
            'id' => (int) $player['id'],
            'name' => (string) $player['name'],
            'avatar' => $ownerId === null ? '' : ($userAvatars[$ownerId] ?? ''),
            'has_account' => $ownerId !== null,
            'is_own' => $isOwn,
            'can_edit' => !$mustChange && ($isAdmin || $isOwn),
        ];
    }, $players);

    $auth = [
        'logged_in' => $currentUser !== null,
        'setup_required' => count($store['users']) === 0,
        'is_admin' => $isAdmin,
        'is_author' => $currentUser !== null && isAuthorUser($store, $currentUser),
        'must_change_password' => $mustChange,
        'can_write' => $currentUser !== null && !$mustChange,
        'user' => $currentUser === null ? null : [
            'id' => (int) $currentUser['id'],
            'username' => (string) $currentUser['username'],
            'player_id' => $currentPlayer === null ? null : (int) $currentPlayer['id'],
            'player_name' => $currentPlayer === null ? '' : (string) $currentPlayer['name'],
            'avatar' => (string) ($currentUser['avatar'] ?? ''),
            'steam_name' => (string) ($currentUser['steam_name'] ?? ''),
            'steam_profile' => steamProfileUrl((string) ($currentUser['steam_name'] ?? '')),
            'email' => (string) ($currentUser['email'] ?? ''),
            'default_weekdays' => normalizeWeekdays($currentUser['default_weekdays'] ?? []),
        ],
    ];

    $response = [
        'ok' => true,
        'storage_writable' => storageIsWritable(),
        'csrf_token' => csrfToken(),
        'auth' => $auth,
        'players' => $playerPayload,
        'availability' => $availability,
        'game_options' => gameOptions($store),
        'event_dates' => $eventDates,
        'settings' => [
            'theme' => validateThemeValue($store['settings']['theme'] ?? 'default', 'default'),
        ],
    ];

    if ($isAdmin) {
        $adminUsers = [];
        foreach ($store['users'] as $user) {
            $player = playerForUser($store, $user);
            $adminUsers[] = [
                'id' => (int) $user['id'],
                'username' => (string) $user['username'],
                'player_name' => $player === null ? '' : (string) $player['name'],
                'must_change_password' => !empty($user['must_change_password']),
                'is_admin' => isAdminUser($store, $user),
                'is_author' => !empty($user['is_author']),
                'steam_name' => (string) ($user['steam_name'] ?? ''),
                'steam_profile' => steamProfileUrl((string) ($user['steam_name'] ?? '')),
                'email' => (string) ($user['email'] ?? ''),
            ];
        }
        usort($adminUsers, static fn(array $a, array $b): int => strcasecmp($a['username'], $b['username']));
        $response['admin'] = [
            'users' => $adminUsers,
            'admin_player_names' => array_values($store['settings']['admin_player_names']),
            'theme' => validateThemeValue($store['settings']['theme'] ?? 'default', 'default'),
        ];
    }

    return $response;
}

if (defined('BLOG_FEED_ONLY')) {
    // Nur Funktionen bereitstellen, keine Anfrage verarbeiten.
    return;
}

$payload = $_SERVER['REQUEST_METHOD'] === 'POST' ? requestPayload() : $_GET;
$action = (string) ($payload['action'] ?? '');

if ($action === 'bootstrap') {
    respond(bootstrapResponse(readStore()));
}

if ($action === 'achievements') {
    // Setzt keine Session-Werte und kann mehrere Sekunden dauern (externe
    // Raider.IO-Abfragen). Die Session-Sperre sofort freigeben, damit dieser
    // Request nicht alle anderen Anfragen derselben Sitzung blockiert
    // (z. B. einen Admin-Speichervorgang in einem anderen Tab).
    session_write_close();

    $achievementsStore = readStore();
    // WoW-Kasten auf admin.php aus: Raider.IO gar nicht erst fragen
    $wow = normalizeFeatures($achievementsStore['settings']['features'] ?? [])['home']['wow_mplus']
        ? achievementsWowData()
        : ['configured' => false, 'runs' => [], 'updated_at' => null];
    $wowMeta = $achievementsStore['achievements_manual']['wow'] ?? ['title' => null, 'links_title' => null, 'links' => []];
    $wow['title'] = $wowMeta['title'] ?? null;
    $wow['links_title'] = $wowMeta['links_title'] ?? null;
    $wow['links'] = $wowMeta['links'] ?? [];

    respond([
        'ok' => true,
        'wow' => $wow,
        'hots' => achievementsManualData($achievementsStore, 'hots'),
        'diablo4' => achievementsManualData($achievementsStore, 'diablo4'),
        'rocket_league' => achievementsManualData($achievementsStore, 'rocket_league'),
    ]);
}

if ($action === 'blog_posts') {
    session_write_close();
    $blogStore = readStore();
    $requestedTag = textLower(trim((string) ($payload['tag'] ?? '')));

    $tagCounts = [];
    $posts = [];
    foreach ($blogStore['blog_posts'] as $post) {
        if ($post['status'] !== 'published') {
            continue;
        }
        foreach ($post['tags'] as $tag) {
            $key = textLower($tag);
            $tagCounts[$key] = ['label' => $tag, 'count' => ($tagCounts[$key]['count'] ?? 0) + 1];
        }
        if ($requestedTag !== '' && !in_array($requestedTag, array_map('textLower', $post['tags']), true)) {
            continue;
        }
        $posts[] = $post;
    }

    $tags = array_values($tagCounts);
    usort($tags, static fn(array $a, array $b): int => $b['count'] <=> $a['count'] ?: strcasecmp($a['label'], $b['label']));

    respond(['ok' => true, 'posts' => $posts, 'tags' => $tags]);
}

if ($action === 'home_page') {
    // Startseite „Home“: Text für alle; can_edit nur für Admins
    $homeStore = readStore();
    $homeOut = homeResponse($homeStore);
    session_write_close();
    respond($homeOut);
}

if ($action === 'site_settings') {
    // Für die Admin-Seite: Schalter, Style und wer angemeldet ist
    $settingsStore = readStore();
    $settingsOut = siteSettingsResponse($settingsStore);
    session_write_close();
    respond($settingsOut);
}

if ($action === 'netzje') {
    $netzjeStore = readStore();
    $netzjeOut = netzjeResponse($netzjeStore);
    session_write_close();
    respond($netzjeOut);
}

if ($action === 'gameservers') {
    // Öffentlich lesbar. Das Passwort steht nur in der Antwort, wenn jemand angemeldet ist.
    $serverStore = readStore();
    $serverUserIndex = currentUserIndex($serverStore);
    $serverUser = $serverUserIndex === null ? null : $serverStore['users'][$serverUserIndex];
    $serverLoggedIn = $serverUser !== null && empty($serverUser['must_change_password']);
    $serverIsAdmin = $serverUser !== null && isAdminUser($serverStore, $serverUser);
    $serverCsrf = csrfToken();
    session_write_close();

    $statuses = gameserverStatuses();
    $iconIndex = gameIconIndex();
    $gameNames = [];
    $serverRows = [];
    foreach ($serverStore['gameservers'] as $server) {
        $status = $statuses['servers'][$server['id']] ?? ['state' => 'unknown', 'players' => null, 'max' => null, 'names' => []];
        $gameNames[] = $server['game'];
        $serverRows[] = [
            'id' => $server['id'],
            'name' => $server['name'],
            'game' => $server['game'],
            'icon' => gameIconUrl($iconIndex[gameIconKey($server['game'])] ?? null),
            'host' => $server['host'],
            'port' => $server['port'],
            'note' => $server['note'],
            'has_password' => $server['password'] !== '',
            'password' => $serverLoggedIn ? $server['password'] : null,
            'state' => $status['state'],
            'players' => $status['players'],
            'max_players' => $status['max'],
            'player_names' => $serverLoggedIn ? $status['names'] : [],
            'checked_at' => $status['checked_at'] ?? '',
        ];
    }
    $out = [
        'ok' => true,
        'logged_in' => $serverLoggedIn,
        'is_admin' => $serverIsAdmin,
        'csrf_token' => $serverCsrf,
        'status_configured' => botGameserversUrl() !== null,
        'bot_reachable' => $statuses['bot'],
        'servers' => $serverRows,
    ];
    if ($serverIsAdmin) {
        // Für das Bearbeiten: alle Felder inkl. interner Adresse und Abfrageart.
        $out['edit'] = array_map(static fn(array $server): array => $server, $serverStore['gameservers']);
    }
    if (gameIconsMissing($gameNames)) {
        gameIconsInBackground($gameNames, $out);
    }
    respond($out);
}

if ($action === 'gameservers_config') {
    // Wird vom Discord-Bot abgefragt: welche Server er prüfen soll. Ohne Passwörter.
    requireBotToken();
    session_write_close();
    $configStore = readStore();
    respond(['ok' => true, 'servers' => array_map(static fn(array $server): array => [
        'id' => $server['id'],
        'name' => $server['name'],
        'game' => $server['game'],
        'host' => $server['probe_host'] !== '' ? $server['probe_host'] : $server['host'],
        'port' => $server['query_port'] > 0 ? $server['query_port'] : $server['port'],
        'query' => $server['query'],
    ], $configStore['gameservers'])]);
}

if ($action === 'stats_exclusions') {
    // Wird vom Discord-Bot abgefragt, damit /statistik dieselben Spieler ausblendet.
    requireBotToken();
    session_write_close();
    respond(['ok' => true, 'ids' => statsExcludedIds(readStore())]);
}

if ($action === 'steam_diagnose') {
    // Nur für Admins: zeigt je Spieler, was Steam liefert und welche Spiele
    // geprüft wurden. Enthält keine Schlüssel und wird nicht zwischengespeichert.
    $diagStore = readStore();
    requireAdmin($diagStore);
    session_write_close();
    if (!steamApiConfigured()) {
        respond(['ok' => true, 'configured' => false]);
    }
    $report = [];
    foreach ($diagStore['users'] as $diagUser) {
        $steamName = (string) ($diagUser['steam_name'] ?? '');
        if ($steamName === '') {
            continue;
        }
        $diagPlayer = playerForUser($diagStore, $diagUser);
        $steamId = steamResolveId($steamName);
        $row = [
            'player' => $diagPlayer === null ? (string) $diagUser['username'] : (string) $diagPlayer['name'],
            'steam_name' => $steamName,
            'steam_id' => $steamId,
        ];
        if ($steamId !== null) {
            $visibility = steamProfileVisibility([$steamId]);
            $row['profile_public'] = $visibility === null ? null : ($visibility[$steamId] ?? false);
            $trace = null;
            $result = steamLatestAchievement($steamId, $trace);
            $row['result'] = $result['reason'] ?? ('gefunden: ' . ($result['entry']['achievement'] ?? ''));
            $row['library'] = $trace['candidates'];
            $row['checked'] = $trace['checked'];
        }
        $report[] = $row;
    }
    respond(['ok' => true, 'configured' => true, 'transport' => $GLOBALS['steam_transport'] ?? null, 'players' => $report]);
}

if ($action === 'steam_achievements') {
    // Antwortet immer sofort aus dem Zwischenspeicher. Ist er veraltet oder hat jemand
    // seinen Steam-Namen geändert, wird im Hintergrund neu geholt (siehe
    // steamRefreshInBackground); der Besucher sieht bis dahin den letzten Stand.
    session_write_close();
    $steamStore = readStore();
    if (!steamApiConfigured()) {
        respond(['ok' => true, 'configured' => false, 'entries' => [], 'linked_players' => 0]);
    }
    $steamMembers = steamMembers($steamStore);
    if ($steamMembers === []) {
        respond(['ok' => true, 'configured' => true, 'entries' => [], 'missing' => [], 'linked_players' => 0]);
    }
    $steamCached = steamAchievementsCached($steamMembers);
    if ($steamCached !== null && $steamCached['fresh']) {
        respond(['ok' => true] + $steamCached['data']);
    }
    // Veraltet, geändert oder noch nie geholt: Antwort sofort, Aktualisierung im Hintergrund.
    $response = $steamCached === null
        ? ['ok' => true, 'configured' => true, 'pending' => true, 'entries' => [], 'missing' => [], 'linked_players' => count($steamMembers)]
        : ['ok' => true, 'refreshing' => true] + $steamCached['data'];
    steamRefreshInBackground($steamStore, $response);
}

if ($action === 'impressions') {
    // Galerie: nur für Angemeldete
    $galleryStore = readStore();
    [, $galleryUser] = requireUser($galleryStore);
    $galleryOut = impressionsResponse($galleryStore, $galleryUser);
    session_write_close();
    respond($galleryOut);
}

if ($action === 'impression_file') {
    // Ein Bild der Galerie ausliefern – nur an Angemeldete, nie über einen öffentlichen Pfad
    $fileStore = readStore();
    requireUser($fileStore);
    session_write_close();
    $file = (string) ($payload['f'] ?? '');
    $path = impressionsDirectory() . DIRECTORY_SEPARATOR . $file;
    if (!validImpressionFile($file) || !is_file($path)) {
        http_response_code(404);
        exit;
    }
    $types = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    header('Content-Type: ' . $types[pathinfo($file, PATHINFO_EXTENSION)]);
    header('Content-Length: ' . filesize($path));
    // Privat zwischenspeichern (Browser ja, Cloudflare & Co. nein); Dateinamen ändern sich nie
    header('Cache-Control: private, max-age=604800, immutable');
    header_remove('Pragma');
    readfile($path);
    exit;
}

if ($action === 'now_playing') {
    // Widget „Gerade online“: wer spielt gerade was. Ohne Discord-IDs; Profilbilder als lokale Kopie.
    session_write_close();
    $now = botNowPlaying();
    if ($now === null) {
        respond(['ok' => true, 'configured' => botApiUrl('now') !== null, 'players' => []]);
    }
    $avatarIndex = discordAvatarIndex();
    $iconIndex = gameIconIndex();
    $players = [];
    // Veraltete Daten (Bot nicht erreichbar) zeigen niemanden als online
    foreach (!empty($now['stale']) ? [] : (is_array($now['players'] ?? null) ? $now['players'] : []) as $player) {
        if (!is_array($player)) {
            continue;
        }
        $games = [];
        foreach (is_array($player['games'] ?? null) ? $player['games'] : [] as $game) {
            $gameName = mb_substr((string) ($game['name'] ?? ''), 0, 100, 'UTF-8');
            if ($gameName === '') {
                continue;
            }
            $games[] = [
                'name' => $gameName,
                'since' => (string) ($game['since'] ?? ''),
                'icon' => gameIconUrl($iconIndex[gameIconKey($gameName)] ?? null),
            ];
        }
        if ($games === []) {
            continue;
        }
        $players[] = [
            'name' => mb_substr((string) ($player['name'] ?? ''), 0, 60, 'UTF-8'),
            'avatar' => discordAvatarLocal((string) ($player['id'] ?? ''), $avatarIndex),
            'games' => array_slice($games, 0, 3),
        ];
    }
    respond(['ok' => true, 'configured' => true, 'players' => array_slice($players, 0, 30)]);
}

if ($action === 'year_review') {
    // Seite „Jahresrückblick“ (Rubrik Spiele-Statistik)
    $reviewStore = readStore();
    session_write_close();
    $currentYear = (int) date('Y');
    $years = range($currentYear, STATS_FIRST_YEAR);
    $year = (int) ($payload['year'] ?? $currentYear);
    if ($year < STATS_FIRST_YEAR || $year > $currentYear) {
        respond(['ok' => false, 'error' => 'Für dieses Jahr gibt es keinen Rückblick.'], 422);
    }
    if (!defined('BOT_STATS_URL') || BOT_STATS_URL === '') {
        respond(['ok' => true, 'configured' => false, 'year' => $year, 'years' => $years]);
    }
    $data = botYearReview($year, statsExcludedIds($reviewStore));
    if ($data === null) {
        respond(['ok' => false, 'error' => 'Der Jahresrückblick ist gerade nicht erreichbar (Bot ab Version 1.12.0 nötig).'], 502);
    }
    $review = $data['review'];
    $avatarIndex = discordAvatarIndex();
    $iconIndex = gameIconIndex();
    $icon = static fn(string $name): ?string => gameIconUrl($iconIndex[gameIconKey($name)] ?? null);
    $details = is_array($data['details']['players'] ?? null) ? $data['details']['players'] : [];
    $avatarByName = [];
    foreach ($details as $person) {
        $avatarByName[textLower((string) ($person['name'] ?? ''))] = discordAvatarLocal((string) ($person['id'] ?? ''), $avatarIndex);
    }
    $showPersons = is_array($data['players'] ?? null);
    $person = static fn(array $row, string $valueKey = 'minutes'): array => [
        'name' => (string) ($row['name'] ?? ''),
        'avatar' => $avatarByName[textLower((string) ($row['name'] ?? ''))] ?? null,
        'value' => (int) ($row[$valueKey] ?? 0),
    ];
    $games = array_map(static fn(array $game): array => [
        'name' => (string) $game['name'],
        'minutes' => (int) $game['minutes'],
        'players' => (int) ($game['players'] ?? 0),
        'icon' => $icon((string) $game['name']),
    ], array_slice(is_array($data['games'] ?? null) ? $data['games'] : [], 0, 10));
    $parties = [];
    foreach (netzjeAllParties($reviewStore) as $party) {
        if ((int) substr($party['start'], 0, 4) !== $year) {
            continue;
        }
        $names = array_values(array_filter(array_map(static fn(int $id): string => netzjeUserName($reviewStore, $id), $party['attendees'])));
        $parties[] = ['title' => $party['title'] ?: 'Netzje', 'start' => $party['start'], 'attendees' => $names];
    }
    respond([
        'ok' => true,
        'configured' => true,
        'stale' => !empty($data['stale']),
        'year' => $year,
        'years' => $years,
        'total_minutes' => (int) ($data['totalMinutes'] ?? 0),
        'first_tracked_date' => $review['firstTrackedDate'] ?? null,
        'months' => array_map('intval', array_slice(is_array($review['months'] ?? null) ? $review['months'] : [], 0, 12)),
        'active_days' => (int) ($review['activeDays'] ?? 0),
        'busiest_day' => is_array($review['busiestDay'] ?? null) ? ['date' => (string) $review['busiestDay']['date'], 'minutes' => (int) $review['busiestDay']['minutes']] : null,
        'games_count' => (int) ($review['gamesCount'] ?? 0),
        'players_count' => (int) ($review['playersCount'] ?? 0),
        'games' => $games,
        'new_games' => array_map(static fn(array $game): array => [
            'name' => (string) $game['name'],
            'first_played' => (string) ($game['firstPlayed'] ?? ''),
            'minutes' => (int) ($game['minutes'] ?? 0),
            'icon' => $icon((string) $game['name']),
        ], array_slice(is_array($review['newGames'] ?? null) ? $review['newGames'] : [], 0, 12)),
        'new_games_count' => count(is_array($review['newGames'] ?? null) ? $review['newGames'] : []),
        'players' => $showPersons ? array_map(static fn(array $row): array => $person($row) + ['top_game' => (string) ($row['topGame'] ?? '')], array_slice($data['players'], 0, 10)) : [],
        'most_games' => $showPersons ? array_map(static fn(array $row): array => $person($row, 'value'), is_array($review['mostGames'] ?? null) ? $review['mostGames'] : []) : [],
        'most_days' => $showPersons ? array_map(static fn(array $row): array => $person($row, 'value'), is_array($review['mostDays'] ?? null) ? $review['mostDays'] : []) : [],
        'netzje' => $parties,
    ]);
}

if ($action === 'player_profile') {
    // Spielerprofil (Rubrik Spiele-Statistik → Spieler). Name wie in der Discord-Statistik.
    $profileStore = readStore();
    session_write_close();
    $name = trim((string) ($payload['name'] ?? ''));
    if ($name === '' || mb_strlen($name, 'UTF-8') > 80) {
        respond(['ok' => false, 'error' => 'Spieler nicht gefunden.'], 404);
    }
    if (!defined('BOT_STATS_URL') || BOT_STATS_URL === '') {
        respond(['ok' => false, 'error' => 'Die Statistik ist noch nicht eingerichtet.'], 404);
    }
    $excluded = statsExcludedIds($profileStore);
    $all = botStatsData(0, $excluded);
    $recent = botStatsData(30, $excluded);
    if ($all === null || !is_array($all['players'] ?? null)) {
        respond(['ok' => false, 'error' => 'Spielerprofile gibt es nur, wenn die Spielernamen öffentlich sind.'], 404);
    }
    $allDetails = is_array($all['details']['players'] ?? null) ? $all['details']['players'] : [];
    $person = findByName($allDetails, $name);
    if ($person === null) {
        respond(['ok' => false, 'error' => 'Spieler nicht gefunden.'], 404);
    }
    $realName = (string) $person['name'];
    $rank = 1;
    foreach ($all['players'] as $index => $row) {
        if (textLower((string) $row['name']) === textLower($realName)) {
            $rank = $index + 1;
            break;
        }
    }
    $recentPerson = findByName(is_array($recent['details']['players'] ?? null) ? $recent['details']['players'] : [], $realName);
    $iconIndex = gameIconIndex();
    $gameRows = static fn(array $rows): array => array_map(static fn(array $game): array => [
        'name' => (string) ($game['name'] ?? ''),
        'minutes' => (int) ($game['minutes'] ?? 0),
        'icon' => gameIconUrl($iconIndex[gameIconKey((string) ($game['name'] ?? ''))] ?? null),
    ], $rows);

    // Kalender und Netzje: über den gleichnamigen Spieler bzw. Account der Website
    $calendarPlayer = findByName($profileStore['players'], $realName);
    $onlineDays = 0;
    $userId = null;
    if ($calendarPlayer !== null) {
        $userId = $calendarPlayer['user_id'] ?? null;
        foreach ($profileStore['availability'] as $entry) {
            if ((int) ($entry['player_id'] ?? 0) === (int) $calendarPlayer['id'] && in_array($entry['status'] ?? '', ['online', 'late'], true)) {
                $onlineDays++;
            }
        }
    }
    $netzjeParties = [];
    if ($userId !== null) {
        foreach (netzjeAllParties($profileStore) as $party) {
            if (in_array((int) $userId, $party['attendees'], true)) {
                $netzjeParties[] = ['title' => $party['title'] ?: 'Netzje', 'start' => $party['start']];
            }
        }
    }
    $account = null;
    if ($userId !== null) {
        $accountIndex = findUserIndex($profileStore['users'], (int) $userId);
        if ($accountIndex !== null) {
            $accountUser = $profileStore['users'][$accountIndex];
            $account = [
                'steam_profile' => steamProfileUrl((string) ($accountUser['steam_name'] ?? '')),
                'is_admin' => isAdminUser($profileStore, $accountUser),
                'is_author' => !empty($accountUser['is_author']),
            ];
        }
    }

    // Abzeichen aus den vorhandenen Daten
    $badges = [];
    $allGames = 0;
    foreach (is_array($all['details']['games'] ?? null) ? $all['details']['games'] : [] as $topPlayers) {
        if (findByName(is_array($topPlayers) ? $topPlayers : [], $realName) !== null) {
            $allGames++;
        }
    }
    if ($rank === 1) $badges[] = ['icon' => '👑', 'label' => 'Dauerzocker', 'text' => 'Die meiste Spielzeit überhaupt'];
    elseif ($rank <= 3) $badges[] = ['icon' => '🏆', 'label' => 'Top 3', 'text' => "Platz {$rank} der meisten Spielzeit"];
    if ($allGames >= 8) $badges[] = ['icon' => '🎲', 'label' => 'Allrounder', 'text' => "In {$allGames} Spielen unter den Top-Spielern"];
    if (count($netzjeParties) >= 3) $badges[] = ['icon' => '🖥️', 'label' => 'Netzje-Veteran', 'text' => count($netzjeParties) . ' LAN-Partys dabei'];
    elseif (count($netzjeParties) >= 1) $badges[] = ['icon' => '🔌', 'label' => 'Netzje-Gast', 'text' => 'Bei einer LAN-Party dabei'];
    if ($onlineDays >= 50) $badges[] = ['icon' => '📅', 'label' => 'Stammgast', 'text' => "{$onlineDays} Spieltage im Kalender zugesagt"];
    elseif ($onlineDays >= 10) $badges[] = ['icon' => '🗓️', 'label' => 'Verlässlich', 'text' => "{$onlineDays} Spieltage im Kalender zugesagt"];
    if (($account['is_admin'] ?? false) === true) $badges[] = ['icon' => '🛡️', 'label' => 'Admin', 'text' => 'Kümmert sich um die Seite'];
    if (($account['is_author'] ?? false) === true) $badges[] = ['icon' => '✍️', 'label' => 'Autor', 'text' => 'Schreibt im Blog'];

    respond([
        'ok' => true,
        'name' => $realName,
        'avatar' => discordAvatarLocal((string) ($person['id'] ?? ''), discordAvatarIndex()),
        'rank' => $rank,
        'players_count' => count($all['players']),
        'total_minutes' => (int) ($person['minutes'] ?? 0),
        'recent_minutes' => (int) ($recentPerson['minutes'] ?? 0),
        'top_games' => $gameRows(is_array($person['games'] ?? null) ? $person['games'] : []),
        'recent_games' => $gameRows(is_array($recentPerson['games'] ?? null) ? $recentPerson['games'] : []),
        'calendar_days' => $calendarPlayer !== null ? $onlineDays : null,
        'netzje' => $netzjeParties,
        'steam_profile' => $account['steam_profile'] ?? '',
        'badges' => $badges,
    ]);
}

if ($action === 'playtime_stats') {
    $statsStore = readStore();
    $statsUserIndex = currentUserIndex($statsStore);
    $statsIsAdmin = $statsUserIndex !== null && isAdminUser($statsStore, $statsStore['users'][$statsUserIndex]);
    $statsExcluded = $statsStore['settings']['stats_excluded'];
    session_write_close();
    if (!defined('BOT_STATS_URL') || BOT_STATS_URL === '') {
        respond(['ok' => true, 'configured' => false]);
    }
    $statsDays = (int) ($payload['days'] ?? 30);
    if (!in_array($statsDays, BOT_STATS_ALLOWED_DAYS, true)) {
        respond(['ok' => false, 'error' => 'Ungültiger Zeitraum.'], 422);
    }
    $stats = botStatsData($statsDays, statsExcludedIds($statsStore));
    if ($stats === null) {
        respond(['ok' => false, 'error' => 'Die Statistik ist gerade nicht erreichbar.'], 502);
    }
    $out = [
        'ok' => true,
        'configured' => true,
        'stale' => (bool) ($stats['stale'] ?? false),
        'days' => $statsDays,
        'block_minutes' => (int) ($stats['blockMinutes'] ?? 15),
        'total_minutes' => (int) ($stats['totalMinutes'] ?? 0),
        'generated_at' => (string) ($stats['generatedAt'] ?? ''),
        'games' => [],
    ];
    // Einzelwerte (Top-Spieler je Spiel, Top-Spiele je Spieler): öffentlich nur, wenn der
    // Bot Spielernamen freigibt (STATS_PUBLIC_PLAYERS); Admins sehen sie immer.
    $statsDetails = is_array($stats['details'] ?? null) ? $stats['details'] : null;
    $showPersons = $statsDetails !== null && (is_array($stats['players'] ?? null) || $statsIsAdmin);
    // Profilbilder: nur dort, wo auch Namen gezeigt werden. Gezeigt wird immer die heruntergeladene Kopie.
    $avatarSources = [];
    if ($showPersons) {
        foreach (is_array($statsDetails['players'] ?? null) ? $statsDetails['players'] : [] as $person) {
            $personId = (string) ($person['id'] ?? '');
            if (validDiscordAvatarUrl($person['avatar'] ?? null) !== null) {
                $avatarSources[$personId] = $person['avatar'];
            }
        }
    }
    $avatarIndex = discordAvatarIndex();
    $avatarOf = static fn($id): ?string => $showPersons ? discordAvatarLocal((string) $id, $avatarIndex) : null;
    $cleanMinutes = static function (array $rows, string $nameKey, int $limit) use ($avatarOf): array {
        $result = [];
        foreach (array_slice(array_values(array_filter($rows, 'is_array')), 0, $limit) as $row) {
            $entry = [
                'name' => mb_substr((string) ($row[$nameKey] ?? ''), 0, 100, 'UTF-8'),
                'minutes' => (int) ($row['minutes'] ?? 0),
            ];
            if (isset($row['id'])) {
                $entry['avatar'] = $avatarOf($row['id']); // nur bei Spielerzeilen, nicht bei Spielen
            }
            $result[] = $entry;
        }
        return $result;
    };
    $iconIndex = gameIconIndex();
    // „Spiele“ zeigt höchstens die Top 30, „Alle Spiele“ (all=1) bis zu 500; total_games ist die Gesamtzahl.
    $gameLimit = ((string) ($payload['all'] ?? '')) === '1' ? 500 : 30;
    $out['total_games'] = count($stats['games']);
    foreach (array_slice($stats['games'], 0, $gameLimit) as $game) {
        $gameName = mb_substr((string) ($game['name'] ?? ''), 0, 100, 'UTF-8');
        $row = [
            'name' => $gameName,
            'minutes' => (int) ($game['minutes'] ?? 0),
            'players' => (int) ($game['players'] ?? 0),
            'last_played' => (string) ($game['lastPlayed'] ?? ''),
            'icon' => gameIconUrl($iconIndex[gameIconKey($gameName)] ?? null),
        ];
        if ($showPersons && is_array($statsDetails['games'][$gameName] ?? null)) {
            $row['top_players'] = $cleanMinutes($statsDetails['games'][$gameName], 'name', 10);
        }
        $out['games'][] = $row;
    }
    if ($showPersons && is_array($statsDetails['players'] ?? null)) {
        $out['players'] = [];
        foreach (array_slice($statsDetails['players'], 0, 50) as $player) {
            $topGames = is_array($player['games'] ?? null) ? $cleanMinutes($player['games'], 'name', 5) : [];
            $out['players'][] = [
                'name' => mb_substr((string) ($player['name'] ?? ''), 0, 60, 'UTF-8'),
                'minutes' => (int) ($player['minutes'] ?? 0),
                'top_game' => $topGames[0]['name'] ?? null,
                'top_games' => $topGames,
                'avatar' => $avatarOf($player['id'] ?? ''),
            ];
        }
    } elseif (is_array($stats['players'] ?? null)) {
        // Älterer Bot ohne Einzelwerte: nur die einfache Spielerliste.
        $out['players'] = [];
        foreach (array_slice($stats['players'], 0, 50) as $player) {
            $out['players'][] = [
                'name' => mb_substr((string) ($player['name'] ?? ''), 0, 60, 'UTF-8'),
                'minutes' => (int) ($player['minutes'] ?? 0),
                'top_game' => isset($player['topGame']) ? mb_substr((string) $player['topGame'], 0, 100, 'UTF-8') : null,
            ];
        }
    }
    if ($statsIsAdmin) {
        // Nur für Admins: alle Discord-Spieler samt Ausblende-Status zum Bearbeiten.
        $excludedMap = [];
        foreach ($statsExcluded as $entry) {
            $excludedMap[$entry['id']] = $entry['name'];
        }
        $roster = [];
        $avatarStatus = ['players' => 0, 'bot' => 0, 'local' => 0];
        foreach (is_array($stats['roster'] ?? null) ? $stats['roster'] : [] as $member) {
            $memberId = (string) ($member['id'] ?? '');
            if (!preg_match('/^\d{5,25}$/', $memberId)) {
                continue;
            }
            $roster[$memberId] = [
                'id' => $memberId,
                'name' => mb_substr((string) ($member['name'] ?? ''), 0, 60, 'UTF-8'),
                'minutes' => (int) ($member['minutes'] ?? 0),
                'excluded' => isset($excludedMap[$memberId]),
                'avatar' => discordAvatarLocal($memberId, $avatarIndex),
            ];
            if (!isset($excludedMap[$memberId])) {
                $avatarStatus['players']++;
                if (validDiscordAvatarUrl($member['avatar'] ?? null) !== null) {
                    $avatarSources[$memberId] ??= $member['avatar'];
                    $avatarStatus['bot']++;
                }
                if (discordAvatarLocal($memberId, $avatarIndex) !== null) {
                    $avatarStatus['local']++;
                }
            }
        }
        // Ausgeblendete ohne Spielzeit im Zeitraum trotzdem anzeigen, damit man sie wieder einblenden kann.
        foreach ($excludedMap as $memberId => $memberName) {
            $roster[$memberId] ??= ['id' => $memberId, 'name' => $memberName, 'minutes' => 0, 'excluded' => true];
        }
        $roster = array_values($roster);
        usort($roster, static fn(array $a, array $b): int => $b['minutes'] <=> $a['minutes'] ?: strcasecmp($a['name'], $b['name']));
        $out['roster'] = $roster;
        $out['roster_supported'] = is_array($stats['roster'] ?? null);
        // Für die Fehlersuche bei den Profilbildern: Wie viele kennt der Bot, wie viele sind geladen, ist der Ordner beschreibbar?
        $avatarStatus['location'] = runtimeLocation('media'); // 'data', 'temp' oder 'none'
        $avatarStatus['writable'] = $avatarStatus['location'] !== 'none';
        $out['avatar_status'] = $avatarStatus;
    }
    // Fehlende Spiele-Symbole im Hintergrund suchen und herunterladen; die Antwort geht sofort raus.
    $gameNames = array_column($out['games'], 'name');
    if (gameIconsMissing($gameNames) || discordAvatarsMissing($avatarSources)) {
        gameIconsInBackground($gameNames, $out, $avatarSources);
    }
    respond($out);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['ok' => false, 'error' => 'Diese Aktion ist nur per POST verfügbar.'], 405);
}

validateCsrf($payload);

if ($action === 'logout') {
    withWritableStore(function (array &$store): array {
        if (isset($_COOKIE['kellerkinder_remember'])) {
            $logoutParts = explode(':', (string) $_COOKIE['kellerkinder_remember'], 2);
            if (count($logoutParts) === 2 && $logoutParts[0] !== '') {
                revokeRememberToken($store, $logoutParts[0]);
            }
            clearRememberCookie();
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
        session_start();
        return [['ok' => true, 'csrf_token' => csrfToken()], 200, true];
    });
}

if ($action === 'login') {
    // Erst ohne Schreibsperre prüfen: Fehlversuche werden gezählt und gebremst, ohne dass sie
    // andere Anfragen aufhalten. Die eigentliche Anmeldung folgt unten (case 'login').
    $loginName = validateUsername($payload['username'] ?? '');
    if (loginBlocked($loginName)) {
        respond(['ok' => false, 'code' => 'too_many_attempts', 'error' => 'Zu viele Fehlversuche. Bitte warte 15 Minuten und versuche es dann erneut.'], 429);
    }
    $loginStore = readStore();
    $loginIndex = findUserIndexByUsername($loginStore['users'], $loginName);
    $loginPassword = (string) ($payload['password'] ?? '');
    // Auch bei unbekanntem Namen einen Hash prüfen, damit die Antwortzeit nicht verrät, ob es ihn gibt
    $loginHash = $loginIndex === null ? LOGIN_DUMMY_HASH : (string) $loginStore['users'][$loginIndex]['password_hash'];
    if (!password_verify($loginPassword, $loginHash) || $loginIndex === null || $loginPassword === '') {
        loginFailed($loginName);
        session_write_close();
        usleep(350000);
        respond(['ok' => false, 'error' => 'Benutzername oder Passwort ist falsch.'], 401);
    }
    loginSucceeded($loginName);
}

if ($action === 'password_forgot') {
    // Antwort immer gleich, damit niemand herausfinden kann, welche Adressen hinterlegt sind.
    $forgotEmail = validateEmail($payload['email'] ?? '', false);
    if (passwordResetBlocked($forgotEmail)) {
        respond(['ok' => false, 'code' => 'too_many_resets', 'error' => 'Zu viele Anfragen. Bitte warte eine Stunde und versuche es dann erneut.'], 429);
    }
    passwordResetCounted($forgotEmail);
    $pendingResetMail = null;
    // Die Mail geht erst nach der Antwort raus: der Datenspeicher ist dann wieder frei, und die
    // Antwortzeit verrät nicht, ob eine Mail verschickt wurde.
    register_shutdown_function(static function () use (&$pendingResetMail): void {
        if ($pendingResetMail === null) {
            return;
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        sendPasswordResetMail($pendingResetMail['email'], $pendingResetMail['username'], $pendingResetMail['url']);
    });
    withWritableStore(function (array &$store) use ($forgotEmail, &$pendingResetMail): array {
        $response = ['ok' => true, 'message' => 'Wenn die Adresse zu einem Account gehört, ist eine E-Mail mit einem Link unterwegs. Er ist 60 Minuten gültig.'];
        $index = findUserIndexByEmail($store['users'], $forgotEmail);
        if ($index === null) {
            return [$response, 200, false];
        }
        $token = bin2hex(random_bytes(32));
        $store['users'][$index]['reset_token_hash'] = hash('sha256', $token);
        $store['users'][$index]['reset_expires_at'] = gmdate('c', time() + PASSWORD_RESET_SECONDS);
        // Token steht hinter #, damit es nicht in Server-Logs oder als Referrer landet.
        $pendingResetMail = [
            'email' => (string) $store['users'][$index]['email'],
            'username' => (string) $store['users'][$index]['username'],
            'url' => siteBaseUrl() . '/kalender.php#passwort-neu=' . $token,
        ];
        return [$response, 200, true];
    });
}

withWritableStore(function (array &$store) use ($action, $payload): array {
    switch ($action) {
        case 'password_reset':
            $resetToken = strtolower(trim((string) ($payload['token'] ?? '')));
            $resetIndex = null;
            if (preg_match('/^[a-f0-9]{64}$/', $resetToken)) {
                $resetHash = hash('sha256', $resetToken);
                foreach ($store['users'] as $index => $user) {
                    $expires = strtotime((string) ($user['reset_expires_at'] ?? ''));
                    if (hash_equals((string) ($user['reset_token_hash'] ?? ''), $resetHash) && $expires !== false && $expires > time()) {
                        $resetIndex = (int) $index;
                        break;
                    }
                }
            }
            if ($resetIndex === null) {
                return [['ok' => false, 'code' => 'reset_invalid', 'error' => 'Der Link ist ungültig oder abgelaufen. Fordere bitte einen neuen an.'], 410, false];
            }
            $newPassword = validatePassword($payload['password'] ?? '', $payload['password_confirmation'] ?? null);
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            if ($hash === false) {
                return [['ok' => false, 'error' => 'Das Passwort konnte nicht gespeichert werden.'], 500, false];
            }
            $store['users'][$resetIndex]['password_hash'] = $hash;
            $store['users'][$resetIndex]['must_change_password'] = false;
            // Alle bisherigen Anmeldungen (auch "Angemeldet bleiben") werden ungültig
            $store['users'][$resetIndex]['session_version'] = max(1, (int) ($store['users'][$resetIndex]['session_version'] ?? 1)) + 1;
            unset($store['users'][$resetIndex]['reset_token_hash'], $store['users'][$resetIndex]['reset_expires_at']);
            $store['users'][$resetIndex]['updated_at'] = gmdate('c');
            revokeAllRememberTokensForUser($store, (int) $store['users'][$resetIndex]['id']);
            return [['ok' => true, 'username' => (string) $store['users'][$resetIndex]['username'], 'csrf_token' => csrfToken()], 200, true];

        case 'register':
            if (registerBlocked()) {
                return [['ok' => false, 'code' => 'too_many_registrations', 'error' => 'Von hier wurden gerade schon mehrere Konten angelegt. Bitte versuche es später noch einmal.'], 429, false];
            }
            $username = validateUsername($payload['username'] ?? '');
            $playerName = validateName($payload['player_name'] ?? '');
            $password = validatePassword($payload['password'] ?? '', $payload['password_confirmation'] ?? null);
            $email = validateEmail($payload['email'] ?? '');
            ensureUniqueUsername($store['users'], $username);
            if ($email !== '') {
                ensureUniqueEmail($store['users'], $email);
            }

            $firstUser = count($store['users']) === 0;
            if (!$firstUser) {
                ensureNotReservedAdminName($store, $playerName, null);
            }

            $existingPlayerIndex = findPlayerIndexByName($store['players'], $playerName);
            if ($existingPlayerIndex !== null && $store['players'][$existingPlayerIndex]['user_id'] !== null) {
                return [['ok' => false, 'error' => 'Dieser Spieler ist bereits mit einem Account verbunden.'], 409, false];
            }

            $hash = password_hash($password, PASSWORD_DEFAULT);
            if ($hash === false) {
                return [['ok' => false, 'error' => 'Das Passwort konnte nicht gespeichert werden.'], 500, false];
            }

            $userId = (int) $store['next_user_id'];
            $store['next_user_id'] = $userId + 1;
            $now = gmdate('c');
            $store['users'][] = [
                'id' => $userId,
                'username' => $username,
                'password_hash' => $hash,
                'player_id' => null,
                'must_change_password' => false,
                'session_version' => 1,
                'email' => $email,
                'default_weekdays' => [],
                'avatar' => '',
                'defaults_effective_from' => (new DateTimeImmutable('today', new DateTimeZone('Europe/Berlin')))->format('Y-m-d'),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $userIndex = count($store['users']) - 1;
            $playerId = createOrClaimPlayer($store, $userId, $playerName, null, $firstUser);
            $store['users'][$userIndex]['player_id'] = $playerId;

            if ($firstUser) {
                $actualPlayerIndex = findPlayerIndex($store['players'], $playerId);
                $store['settings']['admin_player_names'] = [$actualPlayerIndex === null ? $playerName : (string) $store['players'][$actualPlayerIndex]['name']];
            }

            registerCounted();
            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            $_SESSION['auth_version'] = 1;
            if (!empty($payload['remember'])) {
                issueRememberToken($store, $userId);
            }
            return [bootstrapResponse($store), 201, true];

        case 'login':
            $username = validateUsername($payload['username'] ?? '');
            $password = (string) ($payload['password'] ?? '');
            $index = findUserIndexByUsername($store['users'], $username);
            if ($index === null || $password === '' || !password_verify($password, (string) $store['users'][$index]['password_hash'])) {
                // Nur möglich, wenn sich das Passwort genau zwischen Vorprüfung und hier geändert hat
                return [['ok' => false, 'error' => 'Benutzername oder Passwort ist falsch.'], 401, false];
            }
            if (password_needs_rehash((string) $store['users'][$index]['password_hash'], PASSWORD_DEFAULT)) {
                $newHash = password_hash($password, PASSWORD_DEFAULT);
                if ($newHash !== false) {
                    $store['users'][$index]['password_hash'] = $newHash;
                    $store['users'][$index]['updated_at'] = gmdate('c');
                }
            }
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $store['users'][$index]['id'];
            $_SESSION['auth_version'] = max(1, (int) ($store['users'][$index]['session_version'] ?? 1));
            if (!empty($payload['remember'])) {
                issueRememberToken($store, (int) $store['users'][$index]['id']);
            }
            return [bootstrapResponse($store), 200, true];

        case 'change_password':
            [$userIndex, $user] = requireUser($store, true);
            $newPassword = validatePassword($payload['new_password'] ?? '', $payload['new_password_confirmation'] ?? null);
            if (empty($user['must_change_password'])) {
                $currentPassword = (string) ($payload['current_password'] ?? '');
                if ($currentPassword === '' || !password_verify($currentPassword, (string) $user['password_hash'])) {
                    return [['ok' => false, 'error' => 'Das aktuelle Passwort ist falsch.'], 422, false];
                }
            }
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            if ($hash === false) {
                return [['ok' => false, 'error' => 'Das Passwort konnte nicht gespeichert werden.'], 500, false];
            }
            $store['users'][$userIndex]['password_hash'] = $hash;
            $store['users'][$userIndex]['must_change_password'] = false;
            $store['users'][$userIndex]['session_version'] = max(1, (int) ($store['users'][$userIndex]['session_version'] ?? 1)) + 1;
            $_SESSION['auth_version'] = $store['users'][$userIndex]['session_version'];
            $store['users'][$userIndex]['updated_at'] = gmdate('c');
            $changedUserId = (int) $store['users'][$userIndex]['id'];
            revokeAllRememberTokensForUser($store, $changedUserId);
            if (isset($_COOKIE['kellerkinder_remember'])) {
                issueRememberToken($store, $changedUserId);
            }
            return [bootstrapResponse($store), 200, true];

        case 'update_profile':
            [$userIndex, $user] = requireUser($store);
            $playerName = validateName($payload['player_name'] ?? '');
            $weekdays = normalizeWeekdays($payload['default_weekdays'] ?? []);
            $avatar = validateAvatarData($payload['avatar'] ?? '');
            if (array_key_exists('steam_name', $payload)) {
                $store['users'][$userIndex]['steam_name'] = validateSteamName($payload['steam_name']);
            }
            if (array_key_exists('email', $payload)) {
                $email = validateEmail($payload['email']);
                ensureUniqueEmail($store['users'], $email, (int) $user['id']);
                setUserEmail($store, $userIndex, $email);
            }
            renameOrAssignUserPlayer($store, $userIndex, $playerName, false);
            $store['users'][$userIndex]['default_weekdays'] = $weekdays;
            $store['users'][$userIndex]['avatar'] = $avatar;
            $store['users'][$userIndex]['defaults_effective_from'] = (new DateTimeImmutable('today', new DateTimeZone('Europe/Berlin')))->format('Y-m-d');
            $store['users'][$userIndex]['updated_at'] = gmdate('c');
            return [bootstrapResponse($store), 200, true];

        case 'create_event_date':
            requireUser($store);
            $eventDate = validateDate($payload['event_date'] ?? '');
            if (in_array($eventDate, $store['custom_dates'], true)) {
                return [['ok' => false, 'error' => 'Dieser Spieltag ist bereits vorhanden.'], 409, false];
            }
            $store['custom_dates'][] = $eventDate;
            sort($store['custom_dates'], SORT_STRING);
            return [bootstrapResponse($store), 201, true];

        case 'delete_event_date':
            requireAdmin($store);
            $eventDate = validateDate($payload['event_date'] ?? '');
            $dateIndex = array_search($eventDate, $store['custom_dates'], true);
            if ($dateIndex === false) {
                return [['ok' => false, 'error' => 'Dieser zusätzliche Spieltag wurde nicht gefunden.'], 404, false];
            }
            array_splice($store['custom_dates'], (int) $dateIndex, 1);
            foreach (array_keys($store['availability']) as $key) {
                if (str_ends_with((string) $key, ':' . $eventDate)) {
                    unset($store['availability'][$key]);
                }
            }
            return [bootstrapResponse($store), 200, true];

        case 'create_player':
            requireAdmin($store);
            $name = validateName($payload['name'] ?? '');
            ensureUniqueName($store['players'], $name);
            $id = (int) $store['next_player_id'];
            $store['next_player_id'] = $id + 1;
            $store['players'][] = ['id' => $id, 'name' => $name, 'user_id' => null];
            return [bootstrapResponse($store), 201, true];

        case 'update_player':
            requireAdmin($store);
            $id = validateId($payload['id'] ?? null, 'Spieler-ID');
            $name = validateName($payload['name'] ?? '');
            $index = findPlayerIndex($store['players'], $id);
            if ($index === null) {
                return [['ok' => false, 'error' => 'Der Spieler wurde nicht gefunden.'], 404, false];
            }
            ensureUniqueName($store['players'], $name, $id);
            $oldName = (string) $store['players'][$index]['name'];
            $store['players'][$index]['name'] = $name;
            if (isset(adminNameMap($store)[textLower($oldName)])) {
                replaceAdminName($store, $oldName, $name);
            }
            return [bootstrapResponse($store), 200, true];

        case 'delete_player':
            requireAdmin($store);
            $id = validateId($payload['id'] ?? null, 'Spieler-ID');
            $index = findPlayerIndex($store['players'], $id);
            if ($index === null) {
                return [['ok' => false, 'error' => 'Der Spieler wurde nicht gefunden.'], 404, false];
            }
            $player = $store['players'][$index];
            $wasAdmin = isset(adminNameMap($store)[textLower((string) $player['name'])]);
            if ($wasAdmin && countActiveAdminUsers($store) <= 1) {
                return [['ok' => false, 'error' => 'Der letzte Administrator kann nicht gelöscht werden.'], 409, false];
            }
            foreach ($store['users'] as &$user) {
                if ((int) ($user['player_id'] ?? 0) === $id) {
                    $user['player_id'] = null;
                    $user['updated_at'] = gmdate('c');
                }
            }
            unset($user);
            array_splice($store['players'], $index, 1);
            foreach (array_keys($store['availability']) as $key) {
                if (str_starts_with((string) $key, $id . ':')) {
                    unset($store['availability'][$key]);
                }
            }
            if ($wasAdmin) {
                removeAdminName($store, (string) $player['name']);
            }
            return [bootstrapResponse($store), 200, true];

        case 'set_status':
            [$userIndex, $user] = requireUser($store);
            $playerId = validateId($payload['player_id'] ?? null, 'Spieler-ID');
            $eventDate = validateDate($payload['event_date'] ?? '');
            $status = validateStatus($payload['status'] ?? '');
            $note = validateNote($payload['note'] ?? '');
            $game = validateGame($payload['game'] ?? '');
            $playerIndex = findPlayerIndex($store['players'], $playerId);
            if ($playerIndex === null) {
                return [['ok' => false, 'error' => 'Der Spieler wurde nicht gefunden.'], 404, false];
            }
            $isAdmin = isAdminUser($store, $user);
            $ownerId = isset($store['players'][$playerIndex]['user_id']) && $store['players'][$playerIndex]['user_id'] !== null
                ? (int) $store['players'][$playerIndex]['user_id']
                : null;
            if (!$isAdmin && $ownerId !== (int) $user['id']) {
                return [['ok' => false, 'error' => 'Du kannst nur deinen eigenen Spieler bearbeiten.'], 403, false];
            }
            if (!isset(visibleDateMap($store)[$eventDate])) {
                return [['ok' => false, 'error' => 'Dieser Termin wird aktuell nicht im Kalender angezeigt.'], 422, false];
            }
            $key = $playerId . ':' . $eventDate;
            $store['availability'][$key] = [
                'player_id' => $playerId,
                'event_date' => $eventDate,
                'status' => $status,
                'note' => $note,
                'game' => $game,
                'updated_at' => gmdate('c'),
                'updated_by_user_id' => (int) $user['id'],
            ];
            return [bootstrapResponse($store), 200, true];

        case 'set_vacation_range':
            [, $vacationUser] = requireUser($store);
            $vacationFrom = validateDate($payload['from'] ?? '');
            $vacationTo = validateDate($payload['to'] ?? '');
            if ($vacationTo < $vacationFrom) {
                return [['ok' => false, 'error' => 'Das Enddatum liegt vor dem Startdatum.'], 422, false];
            }
            $vacationPlayer = playerForUser($store, $vacationUser);
            if ($vacationPlayer === null) {
                return [['ok' => false, 'error' => 'Deinem Account ist kein Spieler zugeordnet.'], 422, false];
            }
            $vacationPlayerId = (int) $vacationPlayer['id'];
            // Absichtlich nur die aktuell sichtbaren Spieltage: Urlaub wird
            // nicht auf Termine gelegt, die es im Kalender (noch) nicht gibt.
            $vacationRemove = !empty($payload['remove']);
            $vacationTouched = 0;
            foreach (array_keys(visibleDateMap($store)) as $visibleDate) {
                if ($visibleDate < $vacationFrom || $visibleDate > $vacationTo) {
                    continue;
                }
                $vacationKey = $vacationPlayerId . ':' . $visibleDate;
                if ($vacationRemove) {
                    // Nur eigene Urlaubseinträge zurücknehmen, andere Status
                    // (z. B. „Online“) bleiben unangetastet.
                    if (($store['availability'][$vacationKey]['status'] ?? '') !== 'vacation') {
                        continue;
                    }
                    unset($store['availability'][$vacationKey]);
                    $vacationTouched++;
                    continue;
                }
                $existingEntry = $store['availability'][$vacationKey] ?? null;
                $store['availability'][$vacationKey] = [
                    'player_id' => $vacationPlayerId,
                    'event_date' => $visibleDate,
                    'status' => 'vacation',
                    'note' => (string) ($existingEntry['note'] ?? ''),
                    'game' => (string) ($existingEntry['game'] ?? ''),
                    'updated_at' => gmdate('c'),
                    'updated_by_user_id' => (int) $vacationUser['id'],
                ];
                $vacationTouched++;
            }
            $vacationResponse = bootstrapResponse($store);
            $vacationResponse['vacation_days'] = $vacationTouched;
            return [$vacationResponse, 200, true];

        case 'admin_create_user':
            requireAdmin($store);
            $username = validateUsername($payload['username'] ?? '');
            $playerName = validateName($payload['player_name'] ?? '');
            $password = validatePassword($payload['password'] ?? '', $payload['password_confirmation'] ?? null);
            $email = validateEmail($payload['email'] ?? '');
            ensureUniqueUsername($store['users'], $username);
            if ($email !== '') {
                ensureUniqueEmail($store['users'], $email);
            }
            $existingPlayerIndex = findPlayerIndexByName($store['players'], $playerName);
            if ($existingPlayerIndex !== null && $store['players'][$existingPlayerIndex]['user_id'] !== null) {
                return [['ok' => false, 'error' => 'Dieser Spieler ist bereits mit einem Account verbunden.'], 409, false];
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            if ($hash === false) {
                return [['ok' => false, 'error' => 'Das Passwort konnte nicht gespeichert werden.'], 500, false];
            }
            $userId = (int) $store['next_user_id'];
            $store['next_user_id'] = $userId + 1;
            $now = gmdate('c');
            $store['users'][] = [
                'id' => $userId,
                'username' => $username,
                'password_hash' => $hash,
                'player_id' => null,
                'must_change_password' => true,
                'is_author' => !empty($payload['is_author']),
                'session_version' => 1,
                'email' => $email,
                'default_weekdays' => [],
                'avatar' => '',
                'defaults_effective_from' => (new DateTimeImmutable('today', new DateTimeZone('Europe/Berlin')))->format('Y-m-d'),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $userIndex = count($store['users']) - 1;
            $playerId = createOrClaimPlayer($store, $userId, $playerName);
            $store['users'][$userIndex]['player_id'] = $playerId;
            return [bootstrapResponse($store), 201, true];

        case 'admin_update_user':
            requireAdmin($store);
            $targetId = validateId($payload['user_id'] ?? null, 'Benutzer-ID');
            $targetIndex = findUserIndex($store['users'], $targetId);
            if ($targetIndex === null) {
                return [['ok' => false, 'error' => 'Der Benutzer wurde nicht gefunden.'], 404, false];
            }
            $username = validateUsername($payload['username'] ?? '');
            $playerName = validateName($payload['player_name'] ?? '');
            ensureUniqueUsername($store['users'], $username, $targetId);
            $store['users'][$targetIndex]['username'] = $username;
            renameOrAssignUserPlayer($store, $targetIndex, $playerName, true);
            $store['users'][$targetIndex]['is_author'] = !empty($payload['is_author']);
            if (array_key_exists('steam_name', $payload)) {
                $store['users'][$targetIndex]['steam_name'] = validateSteamName($payload['steam_name']);
            }
            if (array_key_exists('email', $payload)) {
                $email = validateEmail($payload['email']);
                ensureUniqueEmail($store['users'], $email, $targetId);
                setUserEmail($store, $targetIndex, $email);
            }

            $newPassword = (string) ($payload['password'] ?? '');
            if ($newPassword !== '') {
                validateAdminAssignedPassword($newPassword, $payload['password_confirmation'] ?? null);
                $hash = password_hash($newPassword, PASSWORD_DEFAULT);
                if ($hash === false) {
                    return [['ok' => false, 'error' => 'Das Passwort konnte nicht gespeichert werden.'], 500, false];
                }
                $store['users'][$targetIndex]['password_hash'] = $hash;
                $store['users'][$targetIndex]['must_change_password'] = true;
                $store['users'][$targetIndex]['session_version'] = max(1, (int) ($store['users'][$targetIndex]['session_version'] ?? 1)) + 1;
                revokeAllRememberTokensForUser($store, $targetId);
            }
            $store['users'][$targetIndex]['updated_at'] = gmdate('c');
            return [bootstrapResponse($store), 200, true];

        case 'admin_delete_user':
            [, $actingUser] = requireAdmin($store);
            $targetId = validateId($payload['user_id'] ?? null, 'Benutzer-ID');
            $targetIndex = findUserIndex($store['users'], $targetId);
            if ($targetIndex === null) {
                return [['ok' => false, 'error' => 'Der Benutzer wurde nicht gefunden.'], 404, false];
            }
            $target = $store['users'][$targetIndex];
            $targetPlayer = playerForUser($store, $target);
            $targetIsAdmin = isAdminUser($store, $target);
            if ($targetIsAdmin && countActiveAdminUsers($store) <= 1) {
                return [['ok' => false, 'error' => 'Der letzte Administrator kann nicht gelöscht werden.'], 409, false];
            }
            if ($targetPlayer !== null) {
                $playerIndex = findPlayerIndex($store['players'], (int) $targetPlayer['id']);
                if ($playerIndex !== null) {
                    $store['players'][$playerIndex]['user_id'] = null;
                }
                if ($targetIsAdmin) {
                    removeAdminName($store, (string) $targetPlayer['name']);
                }
            }
            array_splice($store['users'], $targetIndex, 1);
            revokeAllRememberTokensForUser($store, $targetId);
            // Sonst belegt das gelöschte Konto weiter einen Netzje-Platz (Zähler „x von y bestätigt“)
            pruneNetzjeUsers($store, array_map(static fn(array $user): int => (int) $user['id'], $store['users']));
            $deletingSelf = (int) $actingUser['id'] === $targetId;
            if ($deletingSelf) {
                unset($_SESSION['user_id']);
                clearRememberCookie();
            }
            $response = bootstrapResponse($store);
            $response['deleted_self'] = $deletingSelf;
            return [$response, 200, true];

        case 'blog_save_post':
            [, $author] = requireAuthor($store);
            $postTitle = trim((string) ($payload['title'] ?? ''));
            if ($postTitle === '') {
                return [['ok' => false, 'error' => 'Bitte gib dem Beitrag einen Titel.'], 422, false];
            }
            if (textLength($postTitle) > BLOG_TITLE_MAX) {
                return [['ok' => false, 'error' => 'Der Titel ist zu lang.'], 422, false];
            }
            $rawContent = (string) ($payload['content_html'] ?? '');
            if (strlen($rawContent) > BLOG_CONTENT_MAX) {
                return [['ok' => false, 'error' => 'Der Beitrag ist zu lang.'], 422, false];
            }
            $safeContent = sanitizeBlogHtml($rawContent);
            if (trim(strip_tags($safeContent)) === '' && !str_contains($safeContent, '<img')) {
                return [['ok' => false, 'error' => 'Der Beitrag hat noch keinen Inhalt.'], 422, false];
            }
            $postTags = normalizeBlogTags($payload['tags'] ?? []);
            $postStatus = ($payload['status'] ?? 'published') === 'draft' ? 'draft' : 'published';
            $postId = (int) ($payload['id'] ?? 0);
            $nowIso = gmdate('c');
            $authorIsAdmin = isAdminUser($store, $author);

            if ($postId > 0) {
                $foundIndex = null;
                foreach ($store['blog_posts'] as $existingIndex => $existingPost) {
                    if ((int) $existingPost['id'] === $postId) {
                        $foundIndex = $existingIndex;
                        break;
                    }
                }
                if ($foundIndex === null) {
                    return [['ok' => false, 'error' => 'Der Beitrag wurde nicht gefunden.'], 404, false];
                }
                $isOwnPost = (int) $store['blog_posts'][$foundIndex]['author_user_id'] === (int) $author['id'];
                if (!$isOwnPost && !$authorIsAdmin) {
                    return [['ok' => false, 'error' => 'Du kannst nur eigene Beiträge bearbeiten.'], 403, false];
                }
                $store['blog_posts'][$foundIndex]['title'] = $postTitle;
                $store['blog_posts'][$foundIndex]['slug'] = blogSlug($postTitle, $postId);
                $store['blog_posts'][$foundIndex]['content_html'] = $safeContent;
                $store['blog_posts'][$foundIndex]['excerpt'] = blogExcerpt($safeContent);
                $store['blog_posts'][$foundIndex]['tags'] = $postTags;
                $store['blog_posts'][$foundIndex]['status'] = $postStatus;
                $store['blog_posts'][$foundIndex]['updated_at'] = $nowIso;
                $savedId = $postId;
            } else {
                $savedId = (int) $store['next_post_id'];
                $store['next_post_id'] = $savedId + 1;
                $authorPlayer = playerForUser($store, $author);
                $store['blog_posts'][] = [
                    'id' => $savedId,
                    'title' => $postTitle,
                    'slug' => blogSlug($postTitle, $savedId),
                    'content_html' => $safeContent,
                    'excerpt' => blogExcerpt($safeContent),
                    'tags' => $postTags,
                    'author_user_id' => (int) $author['id'],
                    'author_name' => $authorPlayer === null ? (string) $author['username'] : (string) $authorPlayer['name'],
                    'status' => $postStatus,
                    'created_at' => $nowIso,
                    'updated_at' => $nowIso,
                ];
            }
            return [['ok' => true, 'id' => $savedId], 200, true];

        case 'blog_delete_post':
            [, $deletingUser] = requireAuthor($store);
            $deleteId = validateId($payload['id'] ?? null, 'Beitrags-ID');
            $deleteIndex = null;
            foreach ($store['blog_posts'] as $existingIndex => $existingPost) {
                if ((int) $existingPost['id'] === $deleteId) {
                    $deleteIndex = $existingIndex;
                    break;
                }
            }
            if ($deleteIndex === null) {
                return [['ok' => false, 'error' => 'Der Beitrag wurde nicht gefunden.'], 404, false];
            }
            $ownsPost = (int) $store['blog_posts'][$deleteIndex]['author_user_id'] === (int) $deletingUser['id'];
            if (!$ownsPost && !isAdminUser($store, $deletingUser)) {
                return [['ok' => false, 'error' => 'Du kannst nur eigene Beiträge löschen.'], 403, false];
            }
            array_splice($store['blog_posts'], $deleteIndex, 1);
            return [['ok' => true], 200, true];

        case 'blog_upload_image':
            requireAuthor($store);
            $imageData = (string) ($payload['image'] ?? '');
            if (!preg_match('#^data:image/(png|jpeg|gif|webp);base64,([A-Za-z0-9+/=]+)$#', $imageData, $imageMatches)) {
                return [['ok' => false, 'error' => 'Es werden nur PNG-, JPEG-, GIF- und WEBP-Bilder unterstützt.'], 422, false];
            }
            $binary = base64_decode($imageMatches[2], true);
            if ($binary === false || $binary === '' || strlen($binary) > BLOG_IMAGE_MAX_BYTES) {
                return [['ok' => false, 'error' => 'Das Bild ist ungültig oder zu groß (max. 6 MB).'], 422, false];
            }
            // Tatsächlichen Bildinhalt prüfen — die angegebene Endung allein
            // ist keine verlässliche Aussage über den echten Dateityp.
            $imageInfo = @getimagesizefromstring($binary);
            if ($imageInfo === false) {
                return [['ok' => false, 'error' => 'Die Datei ist kein gültiges Bild.'], 422, false];
            }
            $extensionByType = [
                IMAGETYPE_PNG => 'png',
                IMAGETYPE_JPEG => 'jpg',
                IMAGETYPE_GIF => 'gif',
                IMAGETYPE_WEBP => 'webp',
            ];
            if (!isset($extensionByType[$imageInfo[2]])) {
                return [['ok' => false, 'error' => 'Dieses Bildformat wird nicht unterstützt.'], 422, false];
            }
            // Ablage in data/media (dort darf PHP immer schreiben; der Programmordner assets/blog gehört auf
            // dem Server oft einem anderen Benutzer). Ausgeliefert über media.php.
            $binary = downscaleBlogImage($binary, $imageInfo);
            $fileName = mediaStore($binary, 'blog-' . date('Ymd') . '-' . bin2hex(random_bytes(8)) . '.' . $extensionByType[$imageInfo[2]]);
            if ($fileName === null) {
                return [['ok' => false, 'error' => 'Das Bild konnte nicht gespeichert werden (Schreibrechte für data/media?).'], 500, false];
            }
            return [['ok' => true, 'url' => 'media.php?f=' . $fileName], 200, false];

        case 'admin_save_achievement':
            requireAdmin($store);
            $achievementGame = validateAchievementGame($payload['game'] ?? '');
            $part = (string) ($payload['part'] ?? '');
            $existingEntry = $store['achievements_manual'][$achievementGame]
                ?? ['title' => null, 'milestones' => [], 'links_title' => null, 'links' => [], 'updated_at' => null];

            if ($part === 'stats') {
                if ($achievementGame === 'wow') {
                    return [['ok' => false, 'error' => 'Die WoW-Statistik kommt automatisch von Raider.IO und kann nicht bearbeitet werden.'], 422, false];
                }
                $existingEntry['title'] = validateAchievementTitle($payload['title'] ?? '');
                $existingEntry['milestones'] = validateAchievementMilestones($payload['milestones'] ?? []);
            } elseif ($part === 'links') {
                $existingEntry['links_title'] = validateAchievementTitle($payload['links_title'] ?? '');
                $existingEntry['links'] = validateAchievementLinks($payload['links'] ?? []);
            } else {
                return [['ok' => false, 'error' => 'Unbekannter Bereich.'], 422, false];
            }

            $existingEntry['updated_at'] = gmdate('c');
            $store['achievements_manual'][$achievementGame] = $existingEntry;
            return [[
                'ok' => true,
                'game' => $achievementGame,
                'part' => $part,
                'entry' => $existingEntry,
            ], 200, true];

        case 'admin_steam_refresh':
            requireAdmin($store);
            if (!steamApiConfigured()) {
                return [['ok' => false, 'error' => 'Der Steam-API-Schlüssel fehlt in der config.php.'], 422, false];
            }
            $refreshed = steamRefreshNow($store);
            return [['ok' => true, 'refreshed' => $refreshed], 200, false];

        case 'admin_netzje_save':
            requireAdmin($store);
            $partyStart = validNetzjeTime($payload['start'] ?? '');
            $partyEnd = validNetzjeTime($payload['end'] ?? '');
            if ($partyStart === null || $partyEnd === null || $partyEnd <= $partyStart) {
                return [['ok' => false, 'error' => 'Bitte Beginn und Ende der Party angeben (Ende nach Beginn).'], 422, false];
            }
            $title = trim((string) ($payload['title'] ?? ''));
            if ($title === '' || mb_strlen($title, 'UTF-8') > 80) {
                return [['ok' => false, 'error' => 'Bitte einen Titel mit höchstens 80 Zeichen eingeben.'], 422, false];
            }
            $streamType = (string) ($payload['stream_type'] ?? 'none');
            if (!in_array($streamType, NETZJE_STREAM_TYPES, true)) {
                return [['ok' => false, 'error' => 'Unbekannte Stream-Art.'], 422, false];
            }
            $stream = validNetzjeStream($streamType, $payload['stream'] ?? '');
            if ($streamType !== 'none' && $stream === '') {
                return [['ok' => false, 'error' => $streamType === 'twitch'
                    ? 'Bitte einen gültigen Twitch-Kanal (Name oder Link) angeben.'
                    : 'Bitte einen gültigen YouTube-Link oder eine Video-ID angeben.'], 422, false];
            }
            $seatsRaw = trim((string) ($payload['seats'] ?? ''));
            if (filter_var($seatsRaw, FILTER_VALIDATE_INT) === false || (int) $seatsRaw < 1 || (int) $seatsRaw > NETZJE_SEATS_MAX) {
                return [['ok' => false, 'error' => 'Bitte die Anzahl der Plätze angeben (1 bis ' . NETZJE_SEATS_MAX . ').'], 422, false];
            }
            $seats = (int) $seatsRaw;
            $confirmed = count($store['netzje']['attendees']);
            if ($seats < $confirmed) {
                return [['ok' => false, 'error' => 'Es sind schon ' . $confirmed . ' Plätze bestätigt. Bitte erst Teilnehmer entfernen oder mehr Plätze angeben.'], 422, false];
            }
            foreach ($store['netzje']['slots'] as $existingSlot) {
                if ($existingSlot['start'] < $partyStart || $existingSlot['end'] > $partyEnd) {
                    return [['ok' => false, 'error' => '„' . $existingSlot['game'] . '“ läge außerhalb der neuen Party-Zeit. Bitte zuerst das Spiel verschieben oder löschen.'], 422, false];
                }
            }
            $store['netzje'] = normalizeNetzje(array_merge($store['netzje'], [
                'title' => $title,
                'description' => $payload['description'] ?? '',
                'location' => $payload['location'] ?? '',
                'info' => $payload['info'] ?? '',
                'start' => $partyStart,
                'end' => $partyEnd,
                'stream_type' => $streamType,
                'stream' => $stream,
                'seats' => $seats,
            ]));
            return [netzjeResponse($store), 200, true];

        case 'admin_netzje_background':
            // Eigenes Hintergrundbild der Party hochladen (image = data-URL) oder entfernen (image leer).
            requireAdmin($store);
            $oldBackground = $store['netzje']['background'];
            $imageData = (string) ($payload['image'] ?? '');
            if ($imageData === '') {
                $store['netzje']['background'] = '';
            } else {
                if (!preg_match('#^data:image/(png|jpeg|webp);base64,([A-Za-z0-9+/=]+)$#', $imageData, $imageMatches)) {
                    return [['ok' => false, 'error' => 'Es werden nur PNG-, JPEG- und WEBP-Bilder unterstützt.'], 422, false];
                }
                $binary = base64_decode($imageMatches[2], true);
                if ($binary === false || $binary === '' || strlen($binary) > NETZJE_BACKGROUND_MAX_BYTES) {
                    return [['ok' => false, 'error' => 'Das Bild ist ungültig oder zu groß (max. 6 MB).'], 422, false];
                }
                $imageInfo = @getimagesizefromstring($binary);
                $extensionByType = [IMAGETYPE_PNG => 'png', IMAGETYPE_JPEG => 'jpg', IMAGETYPE_WEBP => 'webp'];
                if ($imageInfo === false || !isset($extensionByType[$imageInfo[2]])) {
                    return [['ok' => false, 'error' => 'Die Datei ist kein gültiges PNG-, JPEG- oder WEBP-Bild.'], 422, false];
                }
                $binary = downscaleBlogImage($binary, $imageInfo, NETZJE_BACKGROUND_MAX_WIDTH);
                $stored = mediaStore($binary, 'netzje-' . bin2hex(random_bytes(8)) . '.' . $extensionByType[$imageInfo[2]]);
                if ($stored === null) {
                    return [['ok' => false, 'error' => 'Das Bild konnte nicht gespeichert werden (Schreibrechte für data/media?).'], 500, false];
                }
                $store['netzje']['background'] = $stored;
            }
            if ($oldBackground !== '' && $oldBackground !== $store['netzje']['background']) {
                @unlink(mediaDirectory() . DIRECTORY_SEPARATOR . $oldBackground);
            }
            return [netzjeResponse($store), 200, true];

        case 'admin_netzje_new':
            // „Neue Party“: Termin, Stundenplan, Teilnehmer und Hintergrundbild leeren, Texte bleiben als Vorlage stehen.
            requireAdmin($store);
            // Die bisherige Party kommt ins Archiv (für Spielerprofile und Jahresrückblick)
            if ($store['netzje']['start'] !== '') {
                $store['netzje_history'][] = [
                    'title' => $store['netzje']['title'],
                    'start' => $store['netzje']['start'],
                    'end' => $store['netzje']['end'],
                    'attendees' => $store['netzje']['attendees'],
                ];
                $store['netzje_history'] = normalizeNetzjeHistory($store['netzje_history']);
            }
            if ($store['netzje']['background'] !== '') {
                @unlink(mediaDirectory() . DIRECTORY_SEPARATOR . $store['netzje']['background']);
                $store['netzje']['background'] = '';
            }
            $store['netzje']['slots'] = [];
            $store['netzje']['attendees'] = [];
            $store['netzje']['requests'] = [];
            $store['netzje']['declined'] = [];
            $store['netzje']['seating']['assign'] = []; // Raum bleibt als Vorlage, die Plätze werden frei
            $store['netzje']['start'] = '';
            $store['netzje']['end'] = '';
            $store['netzje'] = normalizeNetzje($store['netzje']);
            return [netzjeResponse($store), 200, true];

        case 'netzje_attend':
            [, $attendUser] = requireUser($store);
            if ($store['netzje']['start'] === '') {
                return [['ok' => false, 'error' => 'Es ist gerade keine Party geplant.'], 422, false];
            }
            // Zusagen sind Anfragen: Ein Admin bestätigt sie (admin_netzje_request).
            $attendId = (int) $attendUser['id'];
            $without = static fn(array $ids): array => array_values(array_filter($ids, static fn(int $id): bool => $id !== $attendId));
            if (!empty($payload['attend'])) {
                if (in_array($attendId, $store['netzje']['attendees'], true) || in_array($attendId, $store['netzje']['requests'], true)) {
                    return [netzjeResponse($store), 200, false];
                }
                if (in_array($attendId, $store['netzje']['declined'], true)) {
                    return [['ok' => false, 'error' => 'Deine Anfrage wurde abgelehnt. Sprich bei Fragen einen Admin an.'], 409, false];
                }
                if ($store['netzje']['seats'] <= 0) {
                    return [['ok' => false, 'error' => 'Für diese Party sind noch keine Plätze festgelegt.'], 422, false];
                }
                $store['netzje']['requests'][] = $attendId;
            } else {
                // Absagen bzw. Anfrage zurückziehen; eine Ablehnung bleibt bestehen
                $store['netzje']['attendees'] = $without($store['netzje']['attendees']);
                $store['netzje']['requests'] = $without($store['netzje']['requests']);
            }
            $store['netzje'] = normalizeNetzje($store['netzje']);
            return [netzjeResponse($store), 200, true];

        case 'impression_upload':
            // Bild hochladen (im Browser schon verkleinert: image bis 2400 px, thumb bis 640 px)
            [, $uploadUser] = requireUser($store);
            if (count($store['impressions']) >= IMPRESSIONS_MAX) {
                return [['ok' => false, 'error' => 'Die Galerie ist voll. Bitte erst alte Eindrücke löschen.'], 422, false];
            }
            $base = bin2hex(random_bytes(16));
            [$full, $error] = impressionStoreImage((string) ($payload['image'] ?? ''), $base, IMPRESSION_MAX_WIDTH);
            if ($full === null) {
                return [['ok' => false, 'error' => $error], 422, false];
            }
            [$thumb, $error] = impressionStoreImage((string) ($payload['thumb'] ?? ''), $base . '-t', IMPRESSION_THUMB_WIDTH);
            if ($thumb === null) {
                @unlink(impressionsDirectory() . DIRECTORY_SEPARATOR . $full['file']);
                return [['ok' => false, 'error' => $error], 422, false];
            }
            $store['impressions'][] = [
                'id' => (int) $store['next_impression_id'],
                'type' => 'image',
                'file' => $full['file'],
                'thumb' => $thumb['file'],
                'ref' => '',
                'width' => $full['width'],
                'height' => $full['height'],
                'caption' => mb_substr(trim((string) ($payload['caption'] ?? '')), 0, IMPRESSION_CAPTION_MAX, 'UTF-8'),
                'album' => mb_substr(trim((string) ($payload['album'] ?? '')), 0, IMPRESSION_ALBUM_MAX, 'UTF-8'),
                'user_id' => (int) $uploadUser['id'],
                'created_at' => gmdate('c'),
            ];
            $store['next_impression_id']++;
            return [impressionsResponse($store, $uploadUser), 200, true];

        case 'impression_video':
            // Clip als Link: YouTube-Video oder Twitch-Clip
            [, $videoUser] = requireUser($store);
            $video = impressionVideoRef((string) ($payload['url'] ?? ''));
            if ($video === null) {
                return [['ok' => false, 'error' => 'Bitte einen Link zu einem YouTube-Video oder Twitch-Clip angeben.'], 422, false];
            }
            $store['impressions'][] = [
                'id' => (int) $store['next_impression_id'],
                'type' => $video['type'],
                'ref' => $video['ref'],
                'caption' => mb_substr(trim((string) ($payload['caption'] ?? '')), 0, IMPRESSION_CAPTION_MAX, 'UTF-8'),
                'album' => mb_substr(trim((string) ($payload['album'] ?? '')), 0, IMPRESSION_ALBUM_MAX, 'UTF-8'),
                'user_id' => (int) $videoUser['id'],
                'created_at' => gmdate('c'),
            ];
            $store['next_impression_id']++;
            $store['impressions'] = normalizeImpressions($store['impressions']);
            return [impressionsResponse($store, $videoUser), 200, true];

        case 'impression_delete':
            // Eigene Eindrücke löschen; Admins dürfen alle löschen
            [, $deleteUser] = requireUser($store);
            $deleteId = (int) ($payload['id'] ?? 0);
            foreach ($store['impressions'] as $index => $item) {
                if ($item['id'] !== $deleteId) {
                    continue;
                }
                if ($item['user_id'] !== (int) $deleteUser['id'] && !isAdminUser($store, $deleteUser)) {
                    return [['ok' => false, 'error' => 'Du kannst nur deine eigenen Eindrücke löschen.'], 403, false];
                }
                foreach ([$item['file'], $item['thumb']] as $file) {
                    if ($file !== '' && validImpressionFile($file)) {
                        @unlink(impressionsDirectory() . DIRECTORY_SEPARATOR . $file);
                    }
                }
                array_splice($store['impressions'], $index, 1);
                return [impressionsResponse($store, $deleteUser), 200, true];
            }
            return [['ok' => false, 'error' => 'Diesen Eindruck gibt es nicht mehr.'], 404, false];

        case 'admin_netzje_seating_save':
            // Admin: Raum festlegen (Raster und welche Felder Sitzplätze sind)
            requireAdmin($store);
            $seating = $store['netzje']['seating'];
            $cols = (int) ($payload['cols'] ?? 0);
            $rows = (int) ($payload['rows'] ?? 0);
            if ($cols < 2 || $cols > NETZJE_SEATING_COLS_MAX || $rows < 2 || $rows > NETZJE_SEATING_ROWS_MAX) {
                return [['ok' => false, 'error' => 'Das Raster muss 2–' . NETZJE_SEATING_COLS_MAX . ' Spalten und 2–' . NETZJE_SEATING_ROWS_MAX . ' Reihen haben.'], 422, false];
            }
            $store['netzje']['seating'] = normalizeNetzjeSeating([
                'cols' => $cols,
                'rows' => $rows,
                'seats' => is_array($payload['seats'] ?? null) ? $payload['seats'] : [],
                'assign' => $seating['assign'], // bleibt, solange es den Sitz noch gibt
                'self_select' => !empty($payload['self_select']),
            ], $store['netzje']['attendees']);
            return [netzjeResponse($store), 200, true];

        case 'netzje_seat':
            // Bestätigte Teilnehmer wählen ihren Platz (seat leer = Platz freigeben)
            [, $seatUser] = requireUser($store);
            $seatUserId = (int) $seatUser['id'];
            $seating = $store['netzje']['seating'];
            if (!in_array($seatUserId, $store['netzje']['attendees'], true)) {
                return [['ok' => false, 'error' => 'Einen Platz wählen können nur bestätigte Teilnehmer.'], 403, false];
            }
            if (!$seating['self_select'] && !isAdminUser($store, $seatUser)) {
                return [['ok' => false, 'error' => 'Die Plätze verteilt ein Admin.'], 403, false];
            }
            $seat = (string) ($payload['seat'] ?? '');
            $assign = array_filter($seating['assign'], static fn(int $id): bool => $id !== $seatUserId);
            if ($seat !== '') {
                if (!in_array($seat, $seating['seats'], true)) {
                    return [['ok' => false, 'error' => 'Diesen Platz gibt es nicht.'], 404, false];
                }
                if (isset($assign[$seat])) {
                    return [['ok' => false, 'error' => 'Der Platz ist schon besetzt.'], 409, false];
                }
                $assign[$seat] = $seatUserId;
            }
            $store['netzje']['seating']['assign'] = $assign;
            $store['netzje'] = normalizeNetzje($store['netzje']);
            return [netzjeResponse($store), 200, true];

        case 'admin_netzje_seat_assign':
            // Admin: Platz einem bestätigten Teilnehmer geben (user_id 0 = Platz freigeben)
            requireAdmin($store);
            $seating = $store['netzje']['seating'];
            $seat = (string) ($payload['seat'] ?? '');
            $targetId = (int) ($payload['user_id'] ?? 0);
            if (!in_array($seat, $seating['seats'], true)) {
                return [['ok' => false, 'error' => 'Diesen Platz gibt es nicht.'], 404, false];
            }
            if ($targetId > 0 && !in_array($targetId, $store['netzje']['attendees'], true)) {
                return [['ok' => false, 'error' => 'Plätze gibt es nur für bestätigte Teilnehmer.'], 422, false];
            }
            $assign = $seating['assign'];
            unset($assign[$seat]);
            if ($targetId > 0) {
                $assign = array_filter($assign, static fn(int $id): bool => $id !== $targetId);
                $assign[$seat] = $targetId;
            }
            $store['netzje']['seating']['assign'] = $assign;
            $store['netzje'] = normalizeNetzje($store['netzje']);
            return [netzjeResponse($store), 200, true];

        case 'admin_netzje_request':
            // Admin entscheidet über eine Platz-Anfrage: approve (bestätigen), decline (ablehnen), remove (ganz entfernen)
            requireAdmin($store);
            $targetId = (int) ($payload['user_id'] ?? 0);
            $decision = (string) ($payload['decision'] ?? '');
            if ($targetId <= 0 || findUserIndex($store['users'], $targetId) === null) {
                return [['ok' => false, 'error' => 'Diese Person wurde nicht gefunden.'], 404, false];
            }
            if (!in_array($decision, ['approve', 'decline', 'remove'], true)) {
                return [['ok' => false, 'error' => 'Unbekannte Entscheidung.'], 422, false];
            }
            $party = $store['netzje'];
            $known = array_merge($party['attendees'], $party['requests'], $party['declined']);
            if (!in_array($targetId, $known, true)) {
                return [['ok' => false, 'error' => 'Diese Person hat keinen Platz angefragt.'], 404, false];
            }
            $without = static fn(array $ids): array => array_values(array_filter($ids, static fn(int $id): bool => $id !== $targetId));
            $alreadyIn = in_array($targetId, $party['attendees'], true);
            $party['attendees'] = $without($party['attendees']);
            $party['requests'] = $without($party['requests']);
            $party['declined'] = $without($party['declined']);
            if ($decision === 'approve') {
                if (!$alreadyIn && count($party['attendees']) >= $party['seats']) {
                    return [['ok' => false, 'error' => 'Alle ' . $party['seats'] . ' Plätze sind vergeben. Erhöhe die Platzzahl unter „Party bearbeiten“ oder entferne jemanden.'], 409, false];
                }
                $party['attendees'][] = $targetId;
            } elseif ($decision === 'decline') {
                $party['declined'][] = $targetId;
            }
            $store['netzje'] = normalizeNetzje($party);
            return [netzjeResponse($store), 200, true];

        case 'netzje_signup':
            [, $signupUser] = requireUser($store);
            $slotIndex = netzjeSlotIndex($store['netzje'], (int) ($payload['id'] ?? 0));
            if ($slotIndex === null || $store['netzje']['slots'][$slotIndex]['status'] !== 'planned') {
                return [['ok' => false, 'error' => 'Dieses Spiel gibt es nicht (mehr) im Stundenplan.'], 404, false];
            }
            // Austragen und wieder Eintragen stellt hinten an: Reihenfolge = Anmeldezeit, Warteliste rückt automatisch nach.
            $signups = array_values(array_filter($store['netzje']['slots'][$slotIndex]['signups'], static fn(int $id): bool => $id !== (int) $signupUser['id']));
            if (!empty($payload['join'])) {
                $signups[] = (int) $signupUser['id'];
            }
            $store['netzje']['slots'][$slotIndex]['signups'] = $signups;
            return [netzjeResponse($store), 200, true];

        case 'netzje_propose':
            [, $proposeUser] = requireUser($store);
            if ($store['netzje']['start'] === '') {
                return [['ok' => false, 'error' => 'Es ist gerade keine Party geplant.'], 422, false];
            }
            $ownProposals = count(array_filter($store['netzje']['slots'], static fn(array $slot): bool => $slot['status'] === 'proposed' && $slot['proposed_by'] === (int) $proposeUser['id']));
            if ($ownProposals >= NETZJE_PROPOSALS_PER_USER) {
                return [['ok' => false, 'error' => 'Du hast schon ' . NETZJE_PROPOSALS_PER_USER . ' offene Vorschläge.'], 422, false];
            }
            if (count($store['netzje']['slots']) >= NETZJE_SLOTS_MAX) {
                return [['ok' => false, 'error' => 'Der Stundenplan ist voll.'], 422, false];
            }
            [$slotInput, $slotError] = validateNetzjeSlotInput($payload, $store['netzje']);
            if ($slotError !== null) {
                return [['ok' => false, 'error' => $slotError], 422, false];
            }
            $store['netzje']['slots'][] = ['id' => $store['netzje']['next_slot_id'], 'status' => 'proposed', 'proposed_by' => (int) $proposeUser['id'], 'signups' => []] + $slotInput;
            $store['netzje'] = normalizeNetzje($store['netzje']);
            return [netzjeResponse($store), 200, true];

        case 'netzje_withdraw':
            // Eigenen, noch nicht übernommenen Vorschlag zurückziehen.
            [, $withdrawUser] = requireUser($store);
            $slotIndex = netzjeSlotIndex($store['netzje'], (int) ($payload['id'] ?? 0));
            $slot = $slotIndex === null ? null : $store['netzje']['slots'][$slotIndex];
            if ($slot === null || $slot['status'] !== 'proposed' || $slot['proposed_by'] !== (int) $withdrawUser['id']) {
                return [['ok' => false, 'error' => 'Diesen Vorschlag kannst du nicht zurückziehen.'], 403, false];
            }
            array_splice($store['netzje']['slots'], $slotIndex, 1);
            return [netzjeResponse($store), 200, true];

        case 'admin_netzje_slot_save':
            // Neues Spiel, Spiel ändern oder Vorschlag übernehmen (übernommen = status planned).
            requireAdmin($store);
            if ($store['netzje']['start'] === '') {
                return [['ok' => false, 'error' => 'Bitte zuerst Termin der Party speichern.'], 422, false];
            }
            [$slotInput, $slotError] = validateNetzjeSlotInput($payload, $store['netzje']);
            if ($slotError !== null) {
                return [['ok' => false, 'error' => $slotError], 422, false];
            }
            $slotId = (int) ($payload['id'] ?? 0);
            $slotIndex = $slotId > 0 ? netzjeSlotIndex($store['netzje'], $slotId) : null;
            if ($slotId > 0 && $slotIndex === null) {
                return [['ok' => false, 'error' => 'Dieses Spiel gibt es nicht mehr.'], 404, false];
            }
            if ($slotIndex === null) {
                if (count($store['netzje']['slots']) >= NETZJE_SLOTS_MAX) {
                    return [['ok' => false, 'error' => 'Der Stundenplan ist voll.'], 422, false];
                }
                $store['netzje']['slots'][] = ['id' => $store['netzje']['next_slot_id'], 'status' => 'planned', 'proposed_by' => 0, 'signups' => []] + $slotInput;
            } else {
                $store['netzje']['slots'][$slotIndex] = array_merge($store['netzje']['slots'][$slotIndex], $slotInput, ['status' => 'planned']);
            }
            $store['netzje'] = normalizeNetzje($store['netzje']);
            return [netzjeResponse($store), 200, true];

        case 'admin_netzje_slot_delete':
            requireAdmin($store);
            $slotIndex = netzjeSlotIndex($store['netzje'], (int) ($payload['id'] ?? 0));
            if ($slotIndex === null) {
                return [['ok' => false, 'error' => 'Dieses Spiel gibt es nicht mehr.'], 404, false];
            }
            array_splice($store['netzje']['slots'], $slotIndex, 1);
            return [netzjeResponse($store), 200, true];

        case 'admin_save_gameserver':
            requireAdmin($store);
            [$serverInput, $serverError] = validateGameserverInput($payload);
            if ($serverError !== null) {
                return [['ok' => false, 'error' => $serverError], 422, false];
            }
            $serverId = (int) ($payload['id'] ?? 0);
            $serverPos = null;
            foreach ($store['gameservers'] as $position => $existing) {
                if ($existing['id'] === $serverId) {
                    $serverPos = $position;
                    break;
                }
            }
            if ($serverId > 0 && $serverPos === null) {
                return [['ok' => false, 'error' => 'Dieser Server existiert nicht mehr.'], 404, false];
            }
            if ($serverPos === null) {
                if (count($store['gameservers']) >= GAMESERVER_MAX) {
                    return [['ok' => false, 'error' => 'Es sind höchstens ' . GAMESERVER_MAX . ' Server möglich.'], 422, false];
                }
                $serverId = (int) $store['next_gameserver_id'];
                $store['next_gameserver_id'] = $serverId + 1;
                $store['gameservers'][] = ['id' => $serverId] + $serverInput;
            } else {
                // Leeres Passwortfeld beim Bearbeiten = Passwort bleibt; „Passwort entfernen“ löscht es.
                if ($serverInput['password'] === '' && empty($payload['clear_password'])) {
                    $serverInput['password'] = $store['gameservers'][$serverPos]['password'];
                }
                $store['gameservers'][$serverPos] = ['id' => $serverId] + $serverInput;
            }
            @unlink(achievementsCacheDirectory() . DIRECTORY_SEPARATOR . 'gameservers-status.json');
            return [['ok' => true, 'id' => $serverId], 200, true];

        case 'admin_delete_gameserver':
            requireAdmin($store);
            $deleteId = (int) ($payload['id'] ?? 0);
            $remaining = array_values(array_filter($store['gameservers'], static fn(array $server): bool => $server['id'] !== $deleteId));
            if (count($remaining) === count($store['gameservers'])) {
                return [['ok' => false, 'error' => 'Dieser Server existiert nicht mehr.'], 404, false];
            }
            $store['gameservers'] = $remaining;
            return [['ok' => true], 200, true];

        case 'admin_set_stats_exclusions':
            requireAdmin($store);
            if (!is_array($payload['players'] ?? null)) {
                return [['ok' => false, 'error' => 'Die Liste ist ungültig.'], 422, false];
            }
            $store['settings']['stats_excluded'] = normalizeStatsExcluded($payload['players']);
            return [['ok' => true, 'excluded' => $store['settings']['stats_excluded']], 200, true];

        case 'admin_save_features':
            // Zentrale Admin-Seite: Seitenleiste, Menü, Startseite und Style für alle
            requireAdmin($store);
            if (!is_array($payload['features'] ?? null)) {
                return [['ok' => false, 'error' => 'Die Einstellungen fehlen.'], 422, false];
            }
            $store['settings']['features'] = normalizeFeatures($payload['features']);
            if (isset($payload['theme'])) {
                $store['settings']['theme'] = validateThemeValue($payload['theme']);
            }
            return [siteSettingsResponse($store), 200, true];

        case 'admin_save_settings':
            requireAdmin($store);
            $rawNames = $payload['admin_player_names'] ?? [];
            if (is_string($rawNames)) {
                $rawNames = preg_split('/[,;\n]+/u', $rawNames) ?: [];
            }
            if (!is_array($rawNames)) {
                return [['ok' => false, 'error' => 'Die Admin-Liste ist ungültig.'], 422, false];
            }
            $canonical = [];
            foreach ($rawNames as $rawName) {
                if (trim((string) $rawName) === '') {
                    continue;
                }
                $name = validateName($rawName);
                $playerIndex = findPlayerIndexByName($store['players'], $name);
                if ($playerIndex === null || $store['players'][$playerIndex]['user_id'] === null) {
                    return [[
                        'ok' => false,
                        'error' => 'Admin „' . $name . '“ muss als Spieler existieren und mit einem Account verbunden sein.',
                    ], 422, false];
                }
                $actualName = (string) $store['players'][$playerIndex]['name'];
                $canonical[textLower($actualName)] = $actualName;
            }
            if ($canonical === []) {
                return [['ok' => false, 'error' => 'Mindestens ein Administrator muss eingetragen bleiben.'], 422, false];
            }
            $store['settings']['admin_player_names'] = array_values($canonical);
            if (countActiveAdminUsers($store) === 0) {
                return [['ok' => false, 'error' => 'Die Liste muss mindestens einen vorhandenen Account zum Administrator machen.'], 422, false];
            }
            $store['settings']['theme'] = validateThemeValue($payload['theme'] ?? ($store['settings']['theme'] ?? 'default'));
            return [bootstrapResponse($store), 200, true];

        case 'admin_save_home':
            // Startseite „Home“ bearbeiten (gleiche Filterung wie im Blog); reset = zurück zur Vorlage
            requireAdmin($store);
            if (!empty($payload['reset'])) {
                $store['home'] = ['html' => '', 'updated_at' => gmdate('c')];
                return [homeResponse($store), 200, true];
            }
            $rawHome = (string) ($payload['html'] ?? '');
            if (strlen($rawHome) > BLOG_CONTENT_MAX) {
                return [['ok' => false, 'error' => 'Der Text ist zu lang.'], 422, false];
            }
            $safeHome = sanitizeBlogHtml($rawHome);
            if (trim(strip_tags($safeHome)) === '' && !str_contains($safeHome, '<img')) {
                return [['ok' => false, 'error' => 'Die Startseite braucht etwas Text. Für die Vorlage „Vorlage wiederherstellen“ wählen.'], 422, false];
            }
            $store['home'] = ['html' => $safeHome, 'updated_at' => gmdate('c')];
            return [homeResponse($store), 200, true];

        case 'admin_cleanup':
            // Admin-Seite „Daten aufräumen“: apply = false zeigt nur, was weg könnte; apply = true räumt auf
            requireAdmin($store);
            $apply = !empty($payload['apply']);
            $items = cleanupData($store, $apply);
            return [['ok' => true, 'applied' => $apply, 'items' => $items, 'store_bytes' => is_file(storagePath()) ? (int) filesize(storagePath()) : 0], 200, $apply];

        default:
            return [['ok' => false, 'error' => 'Unbekannte Aktion.'], 400, false];
    }
});
