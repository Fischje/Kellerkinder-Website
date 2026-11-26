<?php
declare(strict_types=1);
// Seite „Spieler“ (Rubrik Spiele-Statistik).
//   spieler.php              Rangliste der Kellerkinder (gemeinsame Ansicht includes/games-view.php)
//   spieler.php?name=Nenok   Spielerprofil: Spielzeit, Spiele im gewählten Zeitraum, Kalender, Netzje, Abzeichen
//   …&tage=7|30|365|0         Zeitraum der Spieleliste (wie auf der Spiele-Seite), Standard 30 Tage
// Daten des Profils: api.php?action=player_profile. Der Name ist der aus der Discord-Statistik;
// Kalender- und Netzje-Daten gehören zum gleichnamigen Spieler auf der Website.
$profileName = trim((string) ($_GET['name'] ?? ''));
if ($profileName === '') {
    $gamesView = 'players';
    require __DIR__ . '/includes/games-view.php';
    return;
}
require __DIR__ . '/includes/bootstrap.php';
$h = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1b1f27">
    <meta name="application-name" content="Kellerkinder">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Kellerkinder">
    <title><?= $h(mb_substr($profileName, 0, 60, 'UTF-8')) ?> — Kellerkinder</title>
    <link rel="icon" href="assets/kellerkinder-logo.svg?v=<?= $h($appVersion) ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png?v=<?= $h($appVersion) ?>">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Montserrat:wght@500;600;700;800;900&family=Open+Sans:wght@400;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/includes/styles.php'; ?>
    <?php require __DIR__ . '/includes/blog-styles.php'; ?>
    <?php require __DIR__ . '/includes/stats-styles.php'; ?>
    <?php require __DIR__ . '/includes/arena-styles.php'; ?>
    <?php require __DIR__ . '/includes/navi-styles.php'; ?>
    <?php require __DIR__ . '/includes/review-styles.php'; ?>
</head>
<body data-theme="default">

<?php
$activeNav = 'games';
$activeSub = 'players';
$pageKicker = 'Spiele-Statistik · Spieler';
$pageTitle = mb_substr($profileName, 0, 60, 'UTF-8');
$pageLead = '';
$showInstall = false;
require __DIR__ . '/includes/site-header.php';
?>

<main class="page-shell">
    <a class="secondary-button stats-link-button" href="spieler.php">← Alle Spieler</a>
    <div id="profileBody"><p class="widget-loading">Wird geladen …</p></div>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>

<script nonce="<?= $h($cspNonce) ?>">
    const NAME = <?= json_encode($profileName, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
    const byId = (id) => document.getElementById(id);
    const ALLOWED_DAYS = [7, 30, 365, 0];
    const PERIODS = [[7, 'Letzte 7 Tage'], [30, 'Letzte 30 Tage'], [365, 'Ein Jahr'], [0, 'Immer']];
    const periodLabel = (days) => (PERIODS.find(([value]) => value === days) || PERIODS[1])[1];
    let requestSeq = 0;

    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined && text !== null) node.textContent = String(text);
        return node;
    }

    const hours = (minutes) => {
        const value = (Number(minutes) || 0) / 60;
        return value < 10 ? value.toLocaleString('de-DE', { maximumFractionDigits: 1 }) : Math.round(value).toLocaleString('de-DE');
    };
    const formatDate = (iso) => {
        const date = new Date(`${String(iso).slice(0, 10)}T12:00:00`);
        return Number.isNaN(date.getTime()) ? '' : date.toLocaleDateString('de-DE', { day: 'numeric', month: 'long', year: 'numeric' });
    };

    function avatar(src, name, className) {
        const placeholder = () => el('span', `${className} placeholder`, (name || '?').slice(0, 1).toUpperCase());
        if (!src) return placeholder();
        const img = el('img', className);
        img.src = src;
        img.alt = '';
        img.addEventListener('error', () => img.replaceWith(placeholder()));
        return img;
    }

    function gameIcon(src) {
        if (!src) return el('span', 'review-icon placeholder', '🎮');
        const img = el('img', 'review-icon');
        img.src = src;
        img.alt = '';
        img.loading = 'lazy';
        img.addEventListener('error', () => img.replaceWith(el('span', 'review-icon placeholder', '🎮')));
        return img;
    }

    function section(title, accent, ...children) {
        const box = el('section', 'review-section');
        const heading = el('h2', 'section-title');
        heading.append(el('span', 'accent', accent), ` ${title}`);
        box.append(heading, ...children);
        return box;
    }

    function tile(value, label) {
        const box = el('div', 'stats-tile');
        box.append(el('strong', '', value), el('span', '', label));
        return box;
    }

    function gameList(rows) {
        if (rows.length === 0) return el('p', 'widget-empty', 'Keine Spielzeit in diesem Zeitraum.');
        const ol = el('ol', 'review-list');
        const max = Math.max(1, ...rows.map((row) => row.minutes));
        rows.forEach((row, index) => {
            const li = el('li');
            const item = el('div', 'review-list-item has-bar');
            const bar = el('span', 'review-list-bar');
            bar.style.width = `${Math.max(3, Math.round((row.minutes / max) * 100))}%`;
            const text = el('span', 'review-list-text');
            text.append(el('strong', '', row.name));
            item.append(bar, el('span', 'review-rank', `${index + 1}.`), gameIcon(row.icon), text, el('span', 'review-list-value', `${hours(row.minutes)} Std.`));
            li.append(item);
            ol.append(li);
        });
        return ol;
    }

    function render(data) {
        document.querySelector('.page-hero-title').textContent = data.name;
        document.title = `${data.name} — Kellerkinder`;

        const card = el('section', 'profile-card');
        card.append(avatar(data.avatar, data.name, 'profile-avatar'));
        const info = el('div', 'profile-info');
        info.append(el('p', 'profile-rank', `Platz ${data.rank} von ${data.players_count} · Spielzeit gesamt`));
        const badges = el('ul', 'profile-badges');
        for (const badge of data.badges) {
            const li = el('li', 'profile-badge');
            li.title = badge.text;
            li.append(el('span', 'profile-badge-icon', badge.icon), el('span', '', badge.label));
            badges.append(li);
        }
        if (data.badges.length) info.append(badges);
        if (data.steam_profile) {
            const steam = el('a', 'sidebar-more', 'Steam-Profil');
            steam.href = data.steam_profile;
            steam.target = '_blank';
            steam.rel = 'noopener';
            info.append(steam);
        }
        card.append(info);

        const summary = el('div', 'stats-summary review-summary');
        summary.append(
            tile(`${hours(data.total_minutes)} Std.`, 'Gesamt gezockt'),
            tile(data.calendar_days === null ? '–' : String(data.calendar_days), 'Spieltage zugesagt'),
            tile(String(data.netzje.length), data.netzje.length === 1 ? 'Netzje' : 'Netzjes'),
        );

        // Spiele im gewählten Zeitraum: Knöpfe wie auf der Spiele-Seite, Inhalt in #profilePeriod
        const periods = el('div', 'stats-periods');
        periods.setAttribute('role', 'group');
        periods.setAttribute('aria-label', 'Zeitraum');
        for (const [days, label] of PERIODS) {
            const button = el('button', 'stats-period' + (days === data.period_days ? ' active' : ''), label);
            button.type = 'button';
            button.dataset.days = String(days);
            periods.append(button);
        }
        periods.addEventListener('click', (event) => {
            const button = event.target.closest('.stats-period');
            if (!button) return;
            const days = Number(button.dataset.days);
            periods.querySelectorAll('.stats-period').forEach((node) => node.classList.toggle('active', node === button));
            // Zeitraum in der Adresse merken (Neuladen, Teilen, Zurück zur Rangliste)
            const url = new URL(window.location.href);
            url.searchParams.set('tage', String(days));
            history.replaceState(null, '', url);
            backLink.href = `spieler.php?tage=${days}`;
            loadPeriod(days);
        });
        const periodBody = el('div');
        periodBody.id = 'profilePeriod';
        periodBody.append(renderPeriod(data));
        const games = section('Spiele', 'Gezockte', periods, periodBody);

        const nodes = [card, summary, games];
        if (data.stale) nodes.splice(2, 0, el('p', 'stats-note', 'Der Bot ist gerade nicht erreichbar. Gezeigt wird der letzte bekannte Stand.'));
        if (data.netzje.length > 0) {
            const list = el('ul', 'review-netzje');
            for (const party of data.netzje) {
                const li = el('li');
                li.append(el('strong', '', party.title), el('small', '', formatDate(party.start)));
                list.append(li);
            }
            nodes.push(section('dabei', 'Netzje', list));
        }
        if (data.calendar_days === null) {
            nodes.push(el('p', 'stats-note', 'Im Kalender gibt es keinen Spieler mit genau diesem Namen, deshalb fehlen Spieltage und Netzje.'));
        }
        byId('profileBody').replaceChildren(...nodes);
    }

    // Spielzeit und Spiele des Spielers im gewählten Zeitraum
    function renderPeriod(data) {
        const box = document.createDocumentFragment();
        const label = periodLabel(data.period_days);
        const line = data.period_minutes > 0
            ? `${label}: ${hours(data.period_minutes)} Std.` + (data.period_rank ? ` · Platz ${data.period_rank} von ${data.period_players_count}` : '')
            : `${label}: nicht gezockt`;
        box.append(el('p', 'stats-note', line), gameList(data.period_games));
        return box;
    }

    async function fetchProfile(days) {
        const response = await fetch(`api.php?action=player_profile&name=${encodeURIComponent(NAME)}&days=${days}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
        let data = null;
        try { data = await response.json(); } catch { data = null; }
        if (!response.ok || !data || data.ok === false) throw new Error((data && data.error) || 'Das Profil konnte nicht geladen werden.');
        return data;
    }

    async function loadPeriod(days) {
        const seq = ++requestSeq;
        const target = byId('profilePeriod');
        target.replaceChildren(el('p', 'widget-loading', 'Wird geladen …'));
        try {
            const data = await fetchProfile(days);
            if (seq !== requestSeq) return;
            target.replaceChildren(renderPeriod(data));
        } catch (error) {
            if (seq !== requestSeq) return;
            target.replaceChildren(el('p', 'widget-empty', error.message));
        }
    }

    // Zeitraum aus der Adresse (?tage=…, kommt aus der Rangliste mit), sonst die letzten 30 Tage
    const params = new URLSearchParams(window.location.search);
    const requestedDays = Number(params.get('tage'));
    const startDays = params.has('tage') && ALLOWED_DAYS.includes(requestedDays) ? requestedDays : 30;
    const backLink = document.querySelector('.stats-link-button');
    backLink.href = `spieler.php?tage=${startDays}`;

    (async () => {
        try {
            render(await fetchProfile(startDays));
        } catch (error) {
            byId('profileBody').replaceChildren(el('p', 'widget-empty', error.message));
        }
    })();
</script>
</body>
</html>
