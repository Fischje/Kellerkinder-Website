<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

date_default_timezone_set('Europe/Berlin');

const PLAYER_NAME_MAX = 40;
const NOTE_MAX = 60;
const ALLOWED_STATUSES = ['', 'online', 'late', 'absent', 'vacation'];
const STORE_PREFIX = "<?php http_response_code(403); exit; ?>\n";

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

    return @mkdir($directory, 0775, true) || is_dir($directory);
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
        'next_player_id' => 1,
        'players' => [],
        'availability' => [],
        'custom_dates' => [],
    ];
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

    $store['next_player_id'] = max(1, (int) ($store['next_player_id'] ?? 1));
    $store['players'] = is_array($store['players'] ?? null) ? $store['players'] : [];
    $store['availability'] = is_array($store['availability'] ?? null) ? $store['availability'] : [];
    $store['custom_dates'] = is_array($store['custom_dates'] ?? null) ? $store['custom_dates'] : [];

    $validCustomDates = [];
    foreach ($store['custom_dates'] as $customDate) {
        $date = trim((string) $customDate);
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date, new DateTimeZone('Europe/Berlin'));
        $errors = DateTimeImmutable::getLastErrors();
        if ($parsed && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0)) && $parsed->format('Y-m-d') === $date) {
            $validCustomDates[$date] = $date;
        }
    }
    ksort($validCustomDates);
    $store['custom_dates'] = array_values($validCustomDates);

    return $store;
}

/**
 * Lädt die Daten zum Anzeigen nur lesend. Dadurch funktioniert die Seite auch,
 * wenn der Webserver noch keine Schreibrechte besitzt.
 */
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

/**
 * Führt eine Änderung unter exklusiver Dateisperre aus.
 * Die Callback-Funktion erhält den Speicher per Referenz und gibt
 * [Antwortdaten, HTTP-Status, speichern?] zurück.
 */
function withWritableStore(callable $callback): void
{
    if (!ensureStorageDirectory()) {
        respond([
            'ok' => false,
            'code' => 'storage_not_writable',
            'error' => 'Der Datenordner konnte nicht erstellt werden. Bitte gib dem Webserver Schreibrechte für den Website-Ordner.',
        ], 500);
    }

    $path = storagePath();
    if ((is_file($path) && !is_writable($path)) || (!is_file($path) && !is_writable(storageDirectory()))) {
        respond([
            'ok' => false,
            'code' => 'storage_not_writable',
            'error' => 'Der Plan kann angezeigt, aber nicht gespeichert werden. Der Webserver hat keine Schreibrechte für den Ordner „data“.',
        ], 500);
    }

    $handle = @fopen($path, 'c+');
    if ($handle === false) {
        respond([
            'ok' => false,
            'code' => 'storage_not_writable',
            'error' => 'Die Datendatei konnte nicht zum Speichern geöffnet werden. Prüfe die Schreibrechte des Ordners „data“.',
        ], 500);
    }

    try {
        if (!flock($handle, LOCK_EX)) {
            respond(['ok' => false, 'error' => 'Die Datendatei ist momentan gesperrt.'], 503);
        }

        rewind($handle);
        $contents = stream_get_contents($handle);
        $store = decodeStore($contents === false ? '' : $contents);

        list($response, $status, $shouldSave) = $callback($store);

        if ($shouldSave) {
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

function validateName($value): string
{
    $name = trim((string) $value);
    $name = preg_replace('/\s+/u', ' ', $name) ?? $name;

    if ($name === '') {
        respond(['ok' => false, 'error' => 'Bitte einen Spielernamen eintragen.'], 422);
    }
    if (textLength($name) > PLAYER_NAME_MAX) {
        respond(['ok' => false, 'error' => 'Der Spielername ist zu lang.'], 422);
    }

    return $name;
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
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date, new DateTimeZone('Europe/Berlin'));
    $errors = DateTimeImmutable::getLastErrors();

    if (!$parsed || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0)) || $parsed->format('Y-m-d') !== $date) {
        respond(['ok' => false, 'error' => 'Das Datum ist ungültig.'], 422);
    }

    return $date;
}


function isAutomaticWeekday(string $date): bool
{
    $parsed = new DateTimeImmutable($date, new DateTimeZone('Europe/Berlin'));
    return in_array((int) $parsed->format('N'), [3, 7], true);
}

/**
 * Erstellt die sichtbaren Spalten:
 * - genau den letzten vergangenen Termin aus automatischen und zusätzlichen Tagen
 * - die jeweils nächsten drei Mittwoche und Sonntage
 * - alle zusätzlichen Tage ab heute
 */
function buildEventDates(array $customDates): array
{
    $tz = new DateTimeZone('Europe/Berlin');
    $today = new DateTimeImmutable('today', $tz);
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
    if ($pastCandidates !== []) {
        rsort($pastCandidates, SORT_STRING);
        $visible[$pastCandidates[0]] = true;
    }

    foreach (array_keys($automaticDates) as $date) {
        if ($date >= $todayIso) {
            $visible[$date] = true;
        }
    }
    foreach (array_keys($customMap) as $date) {
        if ($date >= $todayIso) {
            $visible[$date] = true;
        }
    }

    ksort($visible, SORT_STRING);
    $events = [];
    foreach (array_keys($visible) as $date) {
        $events[] = [
            'date' => $date,
            'is_past' => $date < $todayIso,
            'is_custom' => isset($customMap[$date]),
        ];
    }

    return $events;
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
    $note = trim((string) $value);
    $note = preg_replace('/\s+/u', ' ', $note) ?? $note;
    if (textLength($note) > NOTE_MAX) {
        respond(['ok' => false, 'error' => 'Der Hinweis ist zu lang.'], 422);
    }
    return $note;
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

function bootstrapResponse(array $store): array
{
    $players = array_values($store['players']);
    usort($players, static function (array $a, array $b): int {
        return strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
    });

    $availability = [];
    foreach ($store['availability'] as $key => $entry) {
        $availability[$key] = [
            'status' => (string) ($entry['status'] ?? ''),
            'note' => (string) ($entry['note'] ?? ''),
        ];
    }

    return [
        'ok' => true,
        'storage_writable' => storageIsWritable(),
        'players' => array_map(static function (array $player): array {
            return [
                'id' => (int) $player['id'],
                'name' => (string) $player['name'],
            ];
        }, $players),
        'availability' => $availability,
        'event_dates' => buildEventDates($store['custom_dates']),
    ];
}

$payload = $_SERVER['REQUEST_METHOD'] === 'POST' ? requestPayload() : $_GET;
$action = (string) ($payload['action'] ?? '');

if ($action === 'bootstrap') {
    respond(bootstrapResponse(readStore()));
}

withWritableStore(function (array &$store) use ($action, $payload): array {
    switch ($action) {
        case 'create_event_date':
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
            return [['ok' => true], 201, true];

        case 'delete_event_date':
            $eventDate = validateDate($payload['event_date'] ?? '');
            $dateIndex = array_search($eventDate, $store['custom_dates'], true);
            if ($dateIndex === false) {
                return [['ok' => false, 'error' => 'Dieser zusätzliche Spieltag wurde nicht gefunden.'], 404, false];
            }
            array_splice($store['custom_dates'], (int) $dateIndex, 1);
            foreach (array_keys($store['availability']) as $key) {
                if (str_ends_with($key, ':' . $eventDate)) {
                    unset($store['availability'][$key]);
                }
            }
            return [['ok' => true], 200, true];

        case 'create_player':
            $name = validateName($payload['name'] ?? '');
            ensureUniqueName($store['players'], $name);
            $id = (int) $store['next_player_id'];
            $store['next_player_id'] = $id + 1;
            $store['players'][] = ['id' => $id, 'name' => $name];
            return [['ok' => true, 'id' => $id], 201, true];

        case 'update_player':
            $id = validateId($payload['id'] ?? null, 'Spieler-ID');
            $name = validateName($payload['name'] ?? '');
            $index = findPlayerIndex($store['players'], $id);
            if ($index === null) {
                return [['ok' => false, 'error' => 'Der Spieler wurde nicht gefunden.'], 404, false];
            }
            ensureUniqueName($store['players'], $name, $id);
            $store['players'][$index]['name'] = $name;
            return [['ok' => true], 200, true];

        case 'delete_player':
            $id = validateId($payload['id'] ?? null, 'Spieler-ID');
            $index = findPlayerIndex($store['players'], $id);
            if ($index === null) {
                return [['ok' => false, 'error' => 'Der Spieler wurde nicht gefunden.'], 404, false];
            }
            array_splice($store['players'], $index, 1);
            foreach (array_keys($store['availability']) as $key) {
                if (strpos($key, $id . ':') === 0) {
                    unset($store['availability'][$key]);
                }
            }
            return [['ok' => true], 200, true];

        case 'set_status':
            $playerId = validateId($payload['player_id'] ?? null, 'Spieler-ID');
            $eventDate = validateDate($payload['event_date'] ?? '');
            $status = validateStatus($payload['status'] ?? '');
            $note = validateNote($payload['note'] ?? '');

            if (findPlayerIndex($store['players'], $playerId) === null) {
                return [['ok' => false, 'error' => 'Der Spieler wurde nicht gefunden.'], 404, false];
            }

            $key = $playerId . ':' . $eventDate;
            if ($status === '') {
                unset($store['availability'][$key]);
            } else {
                $store['availability'][$key] = [
                    'player_id' => $playerId,
                    'event_date' => $eventDate,
                    'status' => $status,
                    'note' => $note,
                    'updated_at' => gmdate('c'),
                ];
            }
            return [['ok' => true], 200, true];

        default:
            return [['ok' => false, 'error' => 'Unbekannte Aktion.'], 400, false];
    }
});
