<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');

date_default_timezone_set('Europe/Berlin');

$dataDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
$storeFile = $dataDir . DIRECTORY_SEPARATOR . 'store.php';
$testFile = $dataDir . DIRECTORY_SEPARATOR . '.write-test-' . bin2hex(random_bytes(4));
$writeTest = false;
$writeMessage = '';

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

function yesNo(bool $value): string
{
    return $value ? '<span class="ok">Ja</span>' : '<span class="bad">Nein</span>';
}
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Systemprüfung – Gildenabend-Planer</title>
    <style>
        body{margin:0;padding:24px;font:16px/1.5 system-ui,sans-serif;background:#17110b;color:#f4e8c5}
        main{max-width:760px;margin:auto;background:#2b1d12;border:1px solid #8a611f;border-radius:12px;padding:22px}
        h1{margin-top:0;color:#f3d27b}.ok{color:#87cf72;font-weight:700}.bad{color:#ff8172;font-weight:700}
        table{width:100%;border-collapse:collapse}td{padding:9px;border-bottom:1px solid #5c4022;vertical-align:top}td:first-child{width:45%;color:#dbc391}
        code{word-break:break-all;color:#fff0bd}.hint{margin-top:18px;padding:12px;background:#4b2c13;border-radius:8px}
        a{color:#f3d27b}
    </style>
</head>
<body><main>
    <h1>Systemprüfung</h1>
    <table>
        <tr><td>PHP-Version</td><td><?= htmlspecialchars(PHP_VERSION) ?></td></tr>
        <tr><td>PHP mindestens 8.1</td><td><?= yesNo(version_compare(PHP_VERSION, '8.1.0', '>=')) ?></td></tr>
        <tr><td>data-Ordner vorhanden</td><td><?= yesNo(is_dir($dataDir)) ?></td></tr>
        <tr><td>data-Ordner beschreibbar</td><td><?= yesNo(is_dir($dataDir) && is_writable($dataDir)) ?></td></tr>
        <tr><td>Praktischer Schreibtest</td><td><?= yesNo($writeTest) ?><?= $writeMessage ? '<br>' . htmlspecialchars($writeMessage) : '' ?></td></tr>
        <tr><td>Datendatei vorhanden</td><td><?= yesNo(is_file($storeFile)) ?></td></tr>
        <tr><td>Datendatei beschreibbar</td><td><?= yesNo(!is_file($storeFile) || is_writable($storeFile)) ?></td></tr>
        <tr><td>Website-Pfad</td><td><code><?= htmlspecialchars(__DIR__) ?></code></td></tr>
        <tr><td>Daten-Pfad</td><td><code><?= htmlspecialchars($dataDir) ?></code></td></tr>
    </table>
    <?php if (!$writeTest): ?>
        <div class="hint"><strong>Erforderliche Korrektur:</strong> Der PHP-Webserver braucht Schreibrechte auf dem angezeigten Daten-Pfad. Beispiel auf Debian/Ubuntu: <code>chown -R www-data:www-data DATA-PFAD</code> und danach <code>chmod 750 DATA-PFAD</code>.</div>
    <?php else: ?>
        <div class="hint"><strong>Die Speicherung funktioniert.</strong> Du kannst diese Datei <code>check.php</code> nach der Prüfung löschen.</div>
    <?php endif; ?>
    <p><a href="index.php">Zurück zum Gildenabend-Planer</a></p>
</main></body></html>
