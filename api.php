<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

date_default_timezone_set('Europe/Berlin');

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
const ACHIEVEMENT_MANUAL_GAMES = ['hots', 'diablo4', 'rocket_league']; // Spiele mit manuell gepflegter Statistik
const ACHIEVEMENT_GAMES = ['wow', 'hots', 'diablo4', 'rocket_league']; // Alle Spiele (inkl. WoW, das Titel/Links aber nicht Stats manuell hat)
const ACHIEVEMENT_LABEL_MAX = 40;
const ACHIEVEMENT_VALUE_MAX = 60;
const ACHIEVEMENT_MILESTONES_MAX = 8;
const ACHIEVEMENT_TITLE_MAX = 40;
const ACHIEVEMENT_LINK_LABEL_MAX = 30;
const ACHIEVEMENT_LINK_URL_MAX = 200;
const ACHIEVEMENT_LINKS_MAX = 6;
const REMEMBER_ME_SECONDS = 60 * 60 * 24 * 30; // 30 Tage
const CALENDAR_UPCOMING_DAYS_LIMIT = 21; // Server liefert bis zu so viele kommende Spieltage; wie viele angezeigt werden, entscheidet das Frontend anhand der verfügbaren Breite (mindestens 7)
const CALENDAR_PAST_DAY_VISIBLE_UNTIL_HOUR = 12; // Letzter vergangener Spieltag ist nur bis zu dieser Uhrzeit (Folgetag) sichtbar

function isHttpsRequest(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    return strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
}

session_name('kellerkinder_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => isHttpsRequest(),
    'httponly' => true,
    'samesite' => 'Lax',
]);
// Ohne diese Anhebung räumt PHP serverseitige Sitzungsdateien standardmäßig
// schon nach recht kurzer Inaktivität weg (oft ~24 Minuten) – dann würde
// "Angemeldet bleiben" trotz gültigem Cookie nach kurzer Pause nicht mehr
// funktionieren, weil die Sitzungsdaten auf dem Server bereits gelöscht sind.
$configuredGcLifetime = (int) ini_get('session.gc_maxlifetime');
if ($configuredGcLifetime < REMEMBER_ME_SECONDS) {
    ini_set('session.gc_maxlifetime', (string) REMEMBER_ME_SECONDS);
}
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function applyRememberMeCookie(bool $remember): void
{
    if (!$remember) {
        return;
    }
    // Überschreibt den zuvor von session_start()/session_regenerate_id()
    // gesendeten Session-Cookie (der beim Schließen des Browsers ausläuft)
    // mit einer lang laufenden Variante, ohne die Sitzungsdaten selbst
    // anzufassen.
    setcookie(session_name(), session_id(), [
        'expires' => time() + REMEMBER_ME_SECONDS,
        'path' => '/',
        'secure' => isHttpsRequest(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
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

function achievementsCacheDirectory(): string
{
    return storageDirectory() . DIRECTORY_SEPARATOR . 'cache';
}

function achievementsCacheRead(string $file): ?array
{
    $path = achievementsCacheDirectory() . DIRECTORY_SEPARATOR . $file;
    if (!is_file($path) || (time() - filemtime($path)) >= ACHIEVEMENTS_CACHE_TTL_SECONDS) {
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
            'profile_url' => (string) ($data['profile_url'] ?? ''),
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
        'settings' => [
            'admin_player_names' => [],
            'theme' => 'default',
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
    $store['settings'] = is_array($store['settings'] ?? null) ? $store['settings'] : $defaults['settings'];
    $store['settings']['admin_player_names'] = is_array($store['settings']['admin_player_names'] ?? null)
        ? array_values($store['settings']['admin_player_names'])
        : [];
    $store['settings']['theme'] = validateThemeValue($store['settings']['theme'] ?? 'default', 'default');

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
        $user['defaults_effective_from'] = validIsoDate((string) ($user['defaults_effective_from'] ?? ''))
            ? (string) $user['defaults_effective_from']
            : (new DateTimeImmutable('today', new DateTimeZone('Europe/Berlin')))->format('Y-m-d');
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

function requireAdmin(array $store): array
{
    [$index, $user] = requireUser($store);
    if (!isAdminUser($store, $user)) {
        respond(['ok' => false, 'error' => 'Diese Funktion ist nur für Administratoren verfügbar.'], 403);
    }
    return [$index, $user];
}

function isAutomaticWeekday(string $date): bool
{
    $parsed = new DateTimeImmutable($date, new DateTimeZone('Europe/Berlin'));
    return in_array((int) $parsed->format('N'), [3, 7], true);
}

function buildEventDates(array $customDates): array
{
    $tz = new DateTimeZone('Europe/Berlin');
    $now = new DateTimeImmutable('now', $tz);
    $today = $now->setTime(0, 0, 0);
    $todayIso = $today->format('Y-m-d');
    $automaticDates = [];

    $lastAutomatic = $today->modify('-1 day');
    while (!in_array((int) $lastAutomatic->format('N'), [3, 7], true)) {
        $lastAutomatic = $lastAutomatic->modify('-1 day');
    }
    $automaticDates[$lastAutomatic->format('Y-m-d')] = true;

    foreach ([3, 7] as $targetWeekday) {
        $daysUntil = ($targetWeekday - (int) $today->format('N') + 7) % 7;
        $first = $today->modify('+' . $daysUntil . ' days');
        for ($index = 0; $index < 3; $index++) {
            $automaticDates[$first->modify('+' . ($index * 7) . ' days')->format('Y-m-d')] = true;
        }
    }

    $customMap = [];
    foreach ($customDates as $customDate) {
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

    // Kommende Spieltage aus automatischen und zusätzlichen Terminen
    // zusammenführen, dann unabhängig von der Quelle auf die nächsten
    // CALENDAR_UPCOMING_DAYS_LIMIT Tage begrenzen.
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
        ];
    }
    return $events;
}

function visibleDateMap(array $store): array
{
    $map = [];
    foreach (buildEventDates($store['custom_dates']) as $event) {
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

function bootstrapResponse(array $store): array
{
    $eventDates = buildEventDates($store['custom_dates']);
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
        'must_change_password' => $mustChange,
        'can_write' => $currentUser !== null && !$mustChange,
        'user' => $currentUser === null ? null : [
            'id' => (int) $currentUser['id'],
            'username' => (string) $currentUser['username'],
            'player_id' => $currentPlayer === null ? null : (int) $currentPlayer['id'],
            'player_name' => $currentPlayer === null ? '' : (string) $currentPlayer['name'],
            'avatar' => (string) ($currentUser['avatar'] ?? ''),
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
    $wow = achievementsWowData();
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['ok' => false, 'error' => 'Diese Aktion ist nur per POST verfügbar.'], 405);
}

validateCsrf($payload);

if ($action === 'logout') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
    session_start();
    respond(['ok' => true, 'csrf_token' => csrfToken()]);
}

withWritableStore(function (array &$store) use ($action, $payload): array {
    switch ($action) {
        case 'register':
            $username = validateUsername($payload['username'] ?? '');
            $playerName = validateName($payload['player_name'] ?? '');
            $password = validatePassword($payload['password'] ?? '', $payload['password_confirmation'] ?? null);
            ensureUniqueUsername($store['users'], $username);

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

            session_regenerate_id(true);
            $_SESSION['user_id'] = $userId;
            $_SESSION['auth_version'] = 1;
            applyRememberMeCookie(!empty($payload['remember']));
            return [bootstrapResponse($store), 201, true];

        case 'login':
            $username = validateUsername($payload['username'] ?? '');
            $password = (string) ($payload['password'] ?? '');
            $index = findUserIndexByUsername($store['users'], $username);
            if ($index === null || $password === '' || !password_verify($password, (string) $store['users'][$index]['password_hash'])) {
                usleep(350000);
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
            applyRememberMeCookie(!empty($payload['remember']));
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
            return [bootstrapResponse($store), 200, true];

        case 'update_profile':
            [$userIndex, $user] = requireUser($store);
            $playerName = validateName($payload['player_name'] ?? '');
            $weekdays = normalizeWeekdays($payload['default_weekdays'] ?? []);
            $avatar = validateAvatarData($payload['avatar'] ?? '');
            renameOrAssignUserPlayer($store, $userIndex, $playerName, false);
            $store['users'][$userIndex]['default_weekdays'] = $weekdays;
            $store['users'][$userIndex]['avatar'] = $avatar;
            $store['users'][$userIndex]['defaults_effective_from'] = (new DateTimeImmutable('today', new DateTimeZone('Europe/Berlin')))->format('Y-m-d');
            $store['users'][$userIndex]['updated_at'] = gmdate('c');
            return [bootstrapResponse($store), 200, true];

        case 'create_event_date':
            requireUser($store);
            $eventDate = validateDate($payload['event_date'] ?? '');
            if (isAutomaticWeekday($eventDate)) {
                return [[
                    'ok' => false,
                    'error' => 'Mittwoche und Sonntage werden automatisch angezeigt. Bitte wähle einen zusätzlichen Spieltag.',
                ], 422, false];
            }
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

        case 'admin_create_user':
            requireAdmin($store);
            $username = validateUsername($payload['username'] ?? '');
            $playerName = validateName($payload['player_name'] ?? '');
            $password = validatePassword($payload['password'] ?? '', $payload['password_confirmation'] ?? null);
            ensureUniqueUsername($store['users'], $username);
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
                'session_version' => 1,
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
            $deletingSelf = (int) $actingUser['id'] === $targetId;
            if ($deletingSelf) {
                unset($_SESSION['user_id']);
            }
            $response = bootstrapResponse($store);
            $response['deleted_self'] = $deletingSelf;
            return [$response, 200, true];

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

        default:
            return [['ok' => false, 'error' => 'Unbekannte Aktion.'], 400, false];
    }
});
