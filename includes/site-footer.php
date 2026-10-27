<?php
// Gemeinsames Seitenende für alle Seiten: Seitenleiste und Fußzeile.
// Schließt den Seitenrahmen, den includes/site-header.php öffnet
// (<div class="page-frame"><div class="page-main">), und wird direkt nach
// </main> eingebunden.
// Die Seitenleiste ist hier nur ein Gerüst; die Inhalte lädt assets/sidebar.js
// über die vorhandenen öffentlichen API-Aktionen. Kästen ohne Daten bleiben
// unsichtbar. Den Kasten zur aktuellen Seite zeigen wir nicht (data-skip).
// Welche Kästen in welcher Reihenfolge erscheinen, stellt ein Admin auf
// admin.php ein (settings.features.sidebar, siehe includes/features.php).
// Ist eine Seite im Menü ausgeblendet, fehlt auch ihr Kasten.
// Am Computer steht die Leiste rechts (weiß, volle Höhe, wie bei navi.gg),
// auf Tablet und Handy rutscht sie als Karten unter den Inhalt.
// Erwartet: $activeNav (aus site-header.php) und $appVersion (aus bootstrap.php).
// Optional: $showSidebar = false vor dem Einbinden blendet die Seitenleiste ganz aus
// (der Inhalt nutzt dann die volle Breite), z. B. auf der Netzje-Seite.
require_once __DIR__ . '/features.php';
$sidebarSkip = [
    'calendar' => 'blog',      // Der Kalender hat schon den Hinweis „Neu im Blog“
    'blog' => 'blog',
    'games' => 'games',
    'servers' => 'servers',
    'netzje' => 'netzje',
][$activeNav ?? ''] ?? '';
$footerVersion = htmlspecialchars((string) ($appVersion ?? ''), ENT_QUOTES, 'UTF-8');

$sidebarBoxes = [];
foreach (($showSidebar ?? true) ? siteFeatures()['sidebar'] : [] as $entry) {
    if ($entry['enabled'] && featurePageEnabled($entry['box'])) {
        $sidebarBoxes[] = $entry['box'];
    }
}
?>
</div><!-- /.page-main -->

<aside class="site-sidebar" id="siteSidebar" data-skip="<?= htmlspecialchars($sidebarSkip, ENT_QUOTES, 'UTF-8') ?>" aria-label="Kurz & knapp"<?= $sidebarBoxes === [] ? ' hidden' : '' ?>>
<?php foreach ($sidebarBoxes as $sidebarBox): ?>
<?php if ($sidebarBox === 'now'): ?>
    <?php /* Erscheint nur, wenn gerade jemand spielt; steht immer ganz oben */ ?>
    <section class="sidebar-section sidebar-now" data-box="now" hidden aria-live="polite">
        <h2 class="sidebar-title"><span class="live-dot" aria-hidden="true"></span>Gerade online <span class="sidebar-count" data-count></span></h2>
        <ul class="sidebar-list" data-list></ul>
    </section>
<?php elseif ($sidebarBox === 'games'): ?>
    <section class="sidebar-section" data-box="games" hidden>
        <h2 class="sidebar-title">Meist gespielt</h2>
        <ul class="sidebar-list" data-list></ul>
        <div class="sidebar-stat" data-stat hidden>
            <strong data-stat-value></strong>
            <span data-stat-label></span>
        </div>
        <a class="sidebar-more" href="games.php">Alle Spiele</a>
    </section>
<?php elseif ($sidebarBox === 'blog'): ?>
    <section class="sidebar-section" data-box="blog" hidden>
        <h2 class="sidebar-title">Neu im Blog</h2>
        <ul class="sidebar-list" data-list></ul>
        <a class="sidebar-more" href="blog.php">Zum Blog</a>
    </section>
<?php elseif ($sidebarBox === 'servers'): ?>
    <section class="sidebar-section" data-box="servers" hidden>
        <h2 class="sidebar-title">Unsere Server</h2>
        <ul class="sidebar-list" data-list></ul>
        <a class="sidebar-more" href="gameserver.php">Alle Server</a>
    </section>
<?php elseif ($sidebarBox === 'netzje'): ?>
    <section class="sidebar-section" data-box="netzje" hidden>
        <h2 class="sidebar-title">Nächste Netzje</h2>
        <a class="sidebar-feature" href="netzje.php">
            <span class="sidebar-feature-tag" data-countdown></span>
            <strong data-title></strong>
            <span data-when></span>
            <span class="sidebar-seatbar" data-seatbar hidden><span></span></span>
            <span class="sidebar-feature-meta" data-meta></span>
        </a>
    </section>
<?php endif; ?>
<?php endforeach; ?>
</aside>
</div><!-- /.page-frame -->

<footer class="site-footer">Created by Fischje with <span class="heart" aria-label="Love">♥</span> · Made with AI · Version <?= $footerVersion ?></footer>
<script src="assets/sidebar.js?v=<?= $footerVersion ?>" defer></script>
<?php /* Pfeile am Hauptmenü, wenn es breiter ist als der Platz */ ?>
<script src="assets/nav-scroll.js?v=<?= $footerVersion ?>" defer></script>
<?php /* Herunterziehen zum Neuladen in der installierten App, auf allen Seiten */ ?>
<script src="assets/pull-refresh.js?v=<?= $footerVersion ?>" defer></script>
