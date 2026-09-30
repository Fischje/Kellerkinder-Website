<?php
declare(strict_types=1);

date_default_timezone_set('Europe/Berlin');

header('Content-Type: application/rss+xml; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Nur die Lese-Hilfsfunktionen aus der API nutzen: BLOG_FEED_ONLY sorgt
// dafür, dass api.php nur seine Funktionen definiert und keine Anfrage
// verarbeitet oder Sitzungs-Header sendet.
define('BLOG_FEED_ONLY', true);
require __DIR__ . '/api.php';

$store = readStore();

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https' ? 'https' : 'http';
$host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
$basePath = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
$baseUrl = $scheme . '://' . $host . $basePath;

$posts = array_values(array_filter(
    $store['blog_posts'],
    static fn(array $post): bool => $post['status'] === 'published'
));
$posts = array_slice($posts, 0, 30);

$lastBuild = $posts === []
    ? gmdate('D, d M Y H:i:s') . ' GMT'
    : gmdate('D, d M Y H:i:s', (int) strtotime($posts[0]['created_at'])) . ' GMT';

function feedEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
  <channel>
    <title>Kellerkinder Blog</title>
    <link><?= feedEscape($baseUrl . '/blog.php') ?></link>
    <description>Spielerlebnisse und News der Kellerkinder.</description>
    <language>de-DE</language>
    <lastBuildDate><?= feedEscape($lastBuild) ?></lastBuildDate>
    <atom:link href="<?= feedEscape($baseUrl . '/feed.php') ?>" rel="self" type="application/rss+xml" />
<?php foreach ($posts as $post): ?>
    <item>
      <title><?= feedEscape($post['title']) ?></title>
      <link><?= feedEscape($baseUrl . '/blog.php#post-' . $post['id']) ?></link>
      <guid isPermaLink="false"><?= feedEscape($baseUrl . '/blog.php?post=' . $post['id']) ?></guid>
      <pubDate><?= feedEscape(gmdate('D, d M Y H:i:s', (int) strtotime($post['created_at'])) . ' GMT') ?></pubDate>
      <author><?= feedEscape($post['author_name']) ?></author>
<?php foreach ($post['tags'] as $tag): ?>
      <category><?= feedEscape($tag) ?></category>
<?php endforeach; ?>
      <description><?= feedEscape($post['excerpt']) ?></description>
      <content:encoded xmlns:content="http://purl.org/rss/1.0/modules/content/"><![CDATA[<?= str_replace(']]>', ']]&gt;', $post['content_html']) ?>]]></content:encoded>
    </item>
<?php endforeach; ?>
  </channel>
</rss>
