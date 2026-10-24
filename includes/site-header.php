<?php
// Gemeinsamer Seitenkopf für Kalender, Blog, Spiele und Server: schmale Infoleiste,
// Kopfband mit Logo und Navigation sowie der Seitentitel-Bereich.
// Öffnet außerdem den Seitenrahmen (<div class="page-frame"><div class="page-main">),
// den includes/site-footer.php wieder schließt – beide Dateien gehören zusammen.
// Erwartet vor dem Einbinden:
//   $activeNav     'calendar' | 'blog' | 'games' | 'servers' | 'netzje' | 'admin'
//   $pageKicker    kleine Zeile über dem Titel (Brotkrumen)
//   $pageTitle     großer Seitentitel
//   $pageLead      optionaler Untertitel
//   $showInstall   true: Knopf „Als App speichern“ anzeigen (nur Kalender)
$activeNav = $activeNav ?? '';
$showInstall = $showInstall ?? false;
$navItems = [
    'calendar' => ['Kalender', 'index.php'],
    'blog' => ['Blog', 'blog.php'],
    'games' => ['Spiele', 'games.php'],
    'servers' => ['Server', 'gameserver.php'],
    'netzje' => ['Netzje', 'netzje.php'],
];
$h = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

// Seiten, die ein Admin auf admin.php ausgeblendet hat, fehlen im Menü (per Link bleiben sie erreichbar)
require_once __DIR__ . '/features.php';
$navItems = array_filter($navItems, static fn(string $key): bool => featurePageEnabled($key), ARRAY_FILTER_USE_KEY);
$currentPageHidden = !featurePageEnabled($activeNav);
?>
<div class="topbar">
    <div class="topbar-inner">
        <span class="topbar-tagline subtitle">Online-Gaming mit <em class="shine">Freunden</em></span>
        <div class="topbar-actions">
            <?php if ($showInstall): ?>
                <button class="install-app-button" id="installAppButton" type="button" title="Als App zum Home-Bildschirm hinzufügen" aria-label="Kellerkinder-Kalender als App zum Home-Bildschirm hinzufügen">
                    <img src="assets/smartphone-install.svg" alt="">
                    <span>App</span>
                </button>
            <?php endif; ?>
            <?php /* Account-Knopf auf allen Seiten: assets/sidebar.js macht daraus „Account“ mit Menü,
                     sobald jemand angemeldet ist. Ohne JavaScript bleibt der Link zur Anmeldung. */ ?>
            <div class="topbar-account" id="topbarAccount">
                <a class="topbar-account-button" id="topbarLogin" href="index.php#anmelden">Anmelden</a>
            </div>
        </div>
    </div>
</div>

<header class="masthead">
    <div class="masthead-row">
        <a class="brand" href="index.php">
            <img src="assets/kellerkinder-logo.svg" alt="" class="brand-logo">
            <span class="brand-text">
                <span class="brand-name-row">
                    <span class="brand-name">Keller<span class="brand-accent">kinder</span></span>
                    <span class="brand-sun" aria-hidden="true">☀️💦</span>
                </span>
            </span>
        </a>
        <nav class="main-nav" aria-label="Hauptnavigation">
            <?php foreach ($navItems as $key => [$label, $href]): ?>
                <a href="<?= $h($href) ?>" class="nav-link<?= $key === $activeNav ? ' active' : '' ?>"<?= $key === $activeNav ? ' aria-current="page"' : '' ?>><?= $h($label) ?></a>
            <?php endforeach; ?>
            <?php /* Nur für Admins sichtbar; assets/sidebar.js blendet den Link ein */ ?>
            <a href="admin.php" class="nav-link nav-admin<?= $activeNav === 'admin' ? ' active' : '' ?>" id="navAdminLink"<?= $activeNav === 'admin' ? ' aria-current="page"' : ' hidden' ?>>Admin</a>
        </nav>
    </div>
</header>

<div class="page-frame">
<div class="page-main">
<?php if (!empty($pageTitle)): ?>
<section class="page-hero">
    <div class="page-hero-inner">
        <p class="page-hero-crumb"><?= $h((string) ($pageKicker ?? $pageTitle)) ?></p>
        <h1 class="page-hero-title"><?= $h((string) $pageTitle) ?></h1>
        <?php if (!empty($pageLead)): ?>
            <p class="page-hero-lead"><?= $h((string) $pageLead) ?></p>
        <?php endif; ?>
        <?php if ($currentPageHidden): ?>
            <p class="page-hidden-note">Diese Seite ist im Menü ausgeblendet und nur per Link erreichbar. Einschalten unter <a href="admin.php">Admin</a>.</p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
