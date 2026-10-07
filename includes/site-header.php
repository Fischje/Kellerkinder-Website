<?php
// Gemeinsamer Seitenkopf für Kalender, Blog und Spiele: schmale Infoleiste,
// Kopfband mit Logo und Navigation sowie der Seitentitel-Bereich.
// Erwartet vor dem Einbinden:
//   $activeNav     'calendar' | 'blog' | 'games'
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
];
$h = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<div class="topbar">
    <div class="topbar-inner">
        <span class="topbar-tagline subtitle">Online-Gaming mit <em class="shine">Freunden</em></span>
        <?php if ($showInstall): ?>
            <button class="install-app-button" id="installAppButton" type="button" title="Als App zum Home-Bildschirm hinzufügen" aria-label="Kellerkinder-Kalender als App zum Home-Bildschirm hinzufügen">
                <img src="assets/smartphone-install.svg" alt="">
                <span>App</span>
            </button>
        <?php endif; ?>
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
            <span class="nav-link disabled">Netzje <small>(folgt)</small></span>
        </nav>
    </div>
</header>

<?php if (!empty($pageTitle)): ?>
<section class="page-hero">
    <div class="page-hero-inner">
        <p class="page-hero-crumb">Kellerkinder <span aria-hidden="true">/</span> <?= $h((string) ($pageKicker ?? $pageTitle)) ?></p>
        <h1 class="page-hero-title"><?= $h((string) $pageTitle) ?></h1>
        <?php if (!empty($pageLead)): ?>
            <p class="page-hero-lead"><?= $h((string) $pageLead) ?></p>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>
