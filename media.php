<?php
declare(strict_types=1);

// Liefert die heruntergeladenen Spiele-Symbole und Discord-Profilbilder aus data/media aus
// (Aufruf: media.php?f=<dateiname>). Die Dateinamen enthalten einen Hash der Bild-Adresse;
// ändert sich ein Bild, ändert sich also auch der Name, und Browser dürfen es lange behalten.

$file = (string) ($_GET['f'] ?? '');
$types = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp', 'gif' => 'image/gif'];

if (preg_match('/^[a-z0-9][a-z0-9._-]{0,120}\.(png|jpe?g|webp|gif)$/', $file, $match) !== 1) {
    http_response_code(404);
    exit;
}

$path = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . $file;
if (!is_file($path)) {
    http_response_code(404);
    exit;
}

$etag = '"' . md5($file . filemtime($path) . filesize($path)) . '"';
header('Content-Type: ' . $types[$match[1]]);
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=31536000, immutable');
header('ETag: ' . $etag);

if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
    http_response_code(304);
    exit;
}

header('Content-Length: ' . (string) filesize($path));
readfile($path);
