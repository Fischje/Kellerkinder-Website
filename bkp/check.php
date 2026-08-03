<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set('Europe/Berlin');

$dataDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
$storeFile = $dataDir . DIRECTORY_SEPARATOR . 'store.php';
$backupFile = $dataDir . DIRECTORY_SEPARATOR . 'store.php.before-accounts-backup';
$testFile = $dataDir . DIRECTORY_SEPARATOR . '.write-test-' . bin2hex(random_bytes(4));
$writeTest = false;
$writeMessage = '';
$schemaVersion = null;
$userCount = null;
$playerCount = null;

if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0775, true);
}

if (is_dir($dataDir)) {
    $bytes = @file_put_contents($testFile, 'test', LOCK_EX);
    if ($bytes !== false) {
        $writeTest = true;
        @unlink($testFile);
    } else {
        $writeMessage = 'Der PHP-Prozess kann im data-Ordner keine Datei anlegen.';
    }
} else {
    $writeMessage = 'Der data-Ordner fehlt und konnte nicht erstellt werden.';
}

if (is_file($storeFile) && is_readable($storeFile)) {
    $contents = @file_get_contents($storeFile);
    if (is_string($contents)) {
        $newline = strpos($contents, "\n");
        $json = $newline === false ? '' : substr($contents, $newline + 1);
        $data = json_decode($json, true);
        if (is_array($data)) {
            $schemaVersion = (int) ($data['schema_version'] ?? 1);
            $userCount = is_array($data['users'] ?? null) ? count($data['users']) : 0;
            $playerCount = is_array($data['players'] ?? null) ? count($data['players']) : 0;
        }
    }
}

function yesNo(bool $value): string
{
    return $value ? '<span class="ok">Ja</span>' : '<span class="bad">Nein</span>';
}

function valueOrDash($value): string
{
    return $value === null ? '–' : htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Systemprüfung – Kellerkinder-Online-Kalender</title>
    <style>
        body{margin:0;padding:24px;font:16px/1.5 system-ui,sans-serif;background:#05060b;color:#f4f6ff}
        main{max-width:820px;margin:auto;background:#0d1220;border:1px solid #405078;border-radius:15px;padding:22px;box-shadow:0 22px 65px rgba(0,0,0,.5)}
        h1{margin-top:0;color:#a7efff}.ok{color:#63eca4;font-weight:700}.bad{color:#ff8190;font-weight:700}
        table{width:100%;border-collapse:collapse}td{padding:9px;border-bottom:1px solid #29334f;vertical-align:top}td:first-child{width:46%;color:#b9c2da}
        code{word-break:break-all;color:#fff}.hint{margin-top:18px;padding:12px;background:#17213a;border-radius:9px;line-height:1.55}
        a{color:#7fe9ff}
    </style>
</head>
<body><main>
    <h1>Systemprüfung</h1>
    <table>
        <tr><td>PHP-Version</td><td><?= htmlspecialchars(PHP_VERSION) ?></td></tr>
        <tr><td>PHP mindestens 8.1</td><td><?= yesNo(version_compare(PHP_VERSION, '8.1.0', '>=')) ?></td></tr>
        <tr><td>Passwort-Hashing verfügbar</td><td><?= yesNo(function_exists('password_hash') && function_exists('password_verify')) ?></td></tr>
        <tr><td>PHP-Sitzungen verfügbar</td><td><?= yesNo(function_exists('session_start')) ?></td></tr>
        <tr><td>data-Ordner vorhanden</td><td><?= yesNo(is_dir($dataDir)) ?></td></tr>
        <tr><td>data-Ordner beschreibbar</td><td><?= yesNo(is_dir($dataDir) && is_writable($dataDir)) ?></td></tr>
        <tr><td>Praktischer Schreibtest</td><td><?= yesNo($writeTest) ?><?= $writeMessage ? '<br>' . htmlspecialchars($writeMessage) : '' ?></td></tr>
        <tr><td>Datendatei vorhanden</td><td><?= yesNo(is_file($storeFile)) ?></td></tr>
        <tr><td>Datendatei beschreibbar</td><td><?= yesNo(!is_file($storeFile) || is_writable($storeFile)) ?></td></tr>
        <tr><td>Daten-Schema</td><td><?= valueOrDash($schemaVersion) ?></td></tr>
        <tr><td>Benutzerkonten</td><td><?= valueOrDash($userCount) ?></td></tr>
        <tr><td>Spieler</td><td><?= valueOrDash($playerCount) ?></td></tr>
        <tr><td>Automatische Upgrade-Sicherung vorhanden</td><td><?= yesNo(is_file($backupFile)) ?></td></tr>
        <tr><td>Website-Pfad</td><td><code><?= htmlspecialchars(__DIR__) ?></code></td></tr>
        <tr><td>Daten-Pfad</td><td><code><?= htmlspecialchars($dataDir) ?></code></td></tr>
    </table>
    <?php if (!$writeTest): ?>
        <div class="hint"><strong>Erforderliche Korrektur:</strong> Der PHP-Webserver braucht Schreibrechte auf dem angezeigten Daten-Pfad. Beispiel: <code>chown -R www-data:www-data <?= htmlspecialchars($dataDir) ?></code> und danach <code>chmod 750 <?= htmlspecialchars($dataDir) ?></code>.</div>
    <?php else: ?>
        <div class="hint"><strong>Die Speicherung funktioniert.</strong> Die Account- und Passwortfunktionen können verwendet werden. Entferne <code>check.php</code> nach der Prüfung vom Produktivserver.</div>
    <?php endif; ?>
    <p><a href="index.php">Zurück zum Kellerkinder-Online-Kalender</a></p>
</main></body></html>
