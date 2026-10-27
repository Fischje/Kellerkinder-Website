<?php
declare(strict_types=1);
// Seite „Jahresrückblick“ (Rubrik Spiele-Statistik): das Spielejahr der Kellerkinder.
// Jahresauswahl ab 2026 (STATS_FIRST_YEAR in api.php), Daten über api.php?action=year_review
// (der Discord-Bot rechnet aus seiner Spielzeit-Erfassung, ab Bot 1.12.0).
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
    <title>Jahresrückblick — Kellerkinder</title>
    <meta name="description" content="Das Spielejahr der Kellerkinder: Spielzeit, Top-Spiele, fleißigste Spieler und neue Spiele.">
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
$activeSub = 'review';
$pageKicker = 'Spiele-Statistik';
$pageTitle = 'Jahresrückblick';
$pageLead = 'Unser Spielejahr in Zahlen: wie viel wir gezockt haben, was am meisten lief und wer am häufigsten dabei war.';
$showInstall = false;
require __DIR__ . '/includes/site-header.php';
?>

<main class="page-shell">
    <div class="filter-row" id="yearTabs" role="group" aria-label="Jahr wählen"></div>
    <div id="reviewBody"><p class="widget-loading">Wird geladen …</p></div>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>

<script nonce="<?= $h($cspNonce) ?>">
    const byId = (id) => document.getElementById(id);
    const MONTHS = ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'];
    const MONTHS_LONG = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
    let requestSeq = 0;

    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined && text !== null) node.textContent = String(text);
        return node;
    }

    const hours = (minutes) => Math.round((Number(minutes) || 0) / 60).toLocaleString('de-DE');
    const formatDate = (iso) => {
        const date = new Date(`${String(iso).slice(0, 10)}T12:00:00`);
        return Number.isNaN(date.getTime()) ? '' : date.toLocaleDateString('de-DE', { day: 'numeric', month: 'long', year: 'numeric' });
    };

    function avatar(src, name, className = 'review-avatar') {
        if (src) {
            const img = el('img', className);
            img.src = src;
            img.alt = '';
            img.loading = 'lazy';
            img.addEventListener('error', () => img.replaceWith(el('span', `${className} placeholder`, (name || '?').slice(0, 1).toUpperCase())));
            return img;
        }
        return el('span', `${className} placeholder`, (name || '?').slice(0, 1).toUpperCase());
    }

    function gameIcon(src) {
        if (src) {
            const img = el('img', 'review-icon');
            img.src = src;
            img.alt = '';
            img.loading = 'lazy';
            img.addEventListener('error', () => img.replaceWith(el('span', 'review-icon placeholder', '🎮')));
            return img;
        }
        return el('span', 'review-icon placeholder', '🎮');
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

    // Monatsbalken: eine Reihe, ein Grün; Werte beim Überfahren/Antippen, als Tabelle für Screenreader
    function monthChart(months, year) {
        const wrap = el('figure', 'review-chart');
        const max = Math.max(1, ...months);
        const bars = el('div', 'review-bars');
        bars.setAttribute('role', 'img');
        bars.setAttribute('aria-label', `Spielstunden je Monat ${year}`);
        const tip = el('div', 'review-tip');
        tip.hidden = true;
        const best = months.indexOf(Math.max(...months));
        months.forEach((minutes, index) => {
            const column = el('button', 'review-bar-col');
            column.type = 'button';
            column.setAttribute('aria-label', `${MONTHS_LONG[index]}: ${hours(minutes)} Stunden`);
            const bar = el('span', 'review-bar');
            bar.style.height = `${minutes > 0 ? Math.max(2, (minutes / max) * 100) : 0}%`;
            const value = el('span', 'review-bar-value', minutes > 0 && index === best ? `${hours(minutes)} Std.` : '');
            const plot = el('span', 'review-bar-plot');
            plot.append(value, bar);
            column.append(plot, el('span', 'review-bar-label', MONTHS[index]));
            const show = () => {
                tip.textContent = `${MONTHS_LONG[index]} ${year}: ${hours(minutes)} Std.`;
                tip.hidden = false;
                bars.querySelectorAll('.review-bar-col').forEach((other) => other.classList.toggle('is-active', other === column));
            };
            column.addEventListener('mouseenter', show);
            column.addEventListener('focus', show);
            column.addEventListener('click', show);
            bars.append(column);
        });
        bars.addEventListener('mouseleave', () => {
            tip.hidden = true;
            bars.querySelectorAll('.review-bar-col').forEach((other) => other.classList.remove('is-active'));
        });
        const tableWrap = el('div', 'visually-hidden');
        const table = el('table');
        tableWrap.append(table);
        const caption = el('caption', '', `Spielstunden je Monat ${year}`);
        table.append(caption);
        months.forEach((minutes, index) => {
            const row = table.insertRow();
            row.insertCell().textContent = MONTHS_LONG[index];
            row.insertCell().textContent = `${hours(minutes)} Stunden`;
        });
        const cap = el('figcaption', 'review-chart-caption', 'Gezockte Stunden je Monat');
        wrap.append(cap, tip, bars, tableWrap);
        return wrap;
    }

    function rankList(rows, { valueText, link }) {
        const ol = el('ol', 'review-list');
        rows.forEach((row, index) => {
            const li = el('li');
            const item = el(link ? 'a' : 'div', 'review-list-item');
            if (link) item.href = link(row);
            item.append(el('span', 'review-rank', `${index + 1}.`));
            item.append(row.icon !== undefined ? gameIcon(row.icon) : avatar(row.avatar, row.name));
            const text = el('span', 'review-list-text');
            text.append(el('strong', '', row.name));
            if (row.sub) text.append(el('small', '', row.sub));
            item.append(text, el('span', 'review-list-value', valueText(row)));
            li.append(item);
            ol.append(li);
        });
        return ol;
    }

    function award(icon, title, main, sub, avatarSrc) {
        const card = el('div', 'review-award');
        const head = el('div', 'review-award-head');
        head.append(el('span', 'review-award-icon', icon), el('span', 'review-award-title', title));
        card.append(head);
        const body = el('div', 'review-award-body');
        if (avatarSrc !== undefined) body.append(avatar(avatarSrc, main));
        const text = el('div');
        text.append(el('strong', '', main), el('small', '', sub));
        body.append(text);
        card.append(body);
        return card;
    }

    const profileLink = (row) => `spieler.php?name=${encodeURIComponent(row.name)}`;

    function render(data) {
        const body = byId('reviewBody');
        renderYears(data.years || [data.year], data.year);
        if (!data.configured) {
            body.replaceChildren(el('p', 'widget-empty', 'Die Statistik ist noch nicht eingerichtet.'));
            return;
        }
        if (!data.total_minutes) {
            body.replaceChildren(el('p', 'widget-empty', `Für ${data.year} liegen noch keine Spielzeiten vor.`));
            return;
        }
        const nodes = [];
        if (data.stale) nodes.push(el('p', 'stats-note', 'Der Bot ist gerade nicht erreichbar. Gezeigt wird der letzte bekannte Stand.'));

        const summary = el('div', 'stats-summary review-summary');
        summary.append(
            tile(`${hours(data.total_minutes)} Std.`, 'Gemeinsam gezockt'),
            tile(String(data.active_days), data.active_days === 1 ? 'Spieltag' : 'Spieltage'),
            tile(String(data.games_count), data.games_count === 1 ? 'Spiel' : 'Spiele'),
            tile(String(data.players_count), 'Kellerkinder'),
        );
        nodes.push(summary);
        nodes.push(section(`${data.year} in Monaten`, 'Das Jahr', monthChart(data.months, data.year)));

        // Auszeichnungen
        const awards = el('div', 'review-awards');
        if (data.busiest_day) awards.append(award('🔥', 'Aktivster Tag', formatDate(data.busiest_day.date), `${hours(data.busiest_day.minutes)} Std. zusammen gezockt`));
        if (data.players[0]) awards.append(award('👑', 'Meiste Spielzeit', data.players[0].name, `${hours(data.players[0].value)} Std. im Jahr`, data.players[0].avatar));
        if (data.most_days[0]) awards.append(award('📅', 'Stammgast', data.most_days[0].name, `an ${data.most_days[0].value} Tagen gezockt`, data.most_days[0].avatar));
        if (data.most_games[0]) awards.append(award('🎲', 'Allrounder', data.most_games[0].name, `${data.most_games[0].value} verschiedene Spiele`, data.most_games[0].avatar));
        if (data.games[0]) awards.append(award('🎮', 'Spiel des Jahres', data.games[0].name, `${hours(data.games[0].minutes)} Std. von ${data.games[0].players} Spielern`));
        if (awards.children.length) nodes.push(section('des Jahres', 'Highlights', awards));

        const columns = el('div', 'review-columns');
        columns.append(section('Spiele', 'Top', rankList(data.games.map((game) => ({ ...game, sub: `${game.players} Spieler` })), {
            valueText: (row) => `${hours(row.minutes)} Std.`,
        })));
        if (data.players.length > 0) {
            columns.append(section('Kellerkinder', 'Fleißigste', rankList(data.players.map((player) => ({ ...player, sub: player.top_game ? `meist ${player.top_game}` : '' })), {
                valueText: (row) => `${hours(row.value)} Std.`,
                link: profileLink,
            })));
        }
        nodes.push(columns);

        if (data.new_games.length > 0) {
            const chips = el('div', 'review-chips');
            for (const game of data.new_games) {
                const chip = el('span', 'review-chip');
                chip.append(gameIcon(game.icon), el('span', '', game.name));
                chip.title = `Zum ersten Mal am ${formatDate(game.first_played)}`;
                chips.append(chip);
            }
            const more = data.new_games_count > data.new_games.length ? el('p', 'stats-note', `… und ${data.new_games_count - data.new_games.length} weitere.`) : null;
            nodes.push(section(`dazugekommen (${data.new_games_count})`, 'Neu', chips, ...(more ? [more] : [])));
        }

        if (data.netzje.length > 0) {
            const list = el('ul', 'review-netzje');
            for (const party of data.netzje) {
                const li = el('li');
                li.append(el('strong', '', party.title), el('small', '', `${formatDate(party.start)} · ${party.attendees.length} dabei${party.attendees.length ? ': ' + party.attendees.join(', ') : ''}`));
                list.append(li);
            }
            nodes.push(section(`${data.year}`, 'Netzje', list));
        }

        const note = el('p', 'stats-note review-note', `Grundlage ist die Spielzeit-Erfassung unseres Discord-Bots${data.first_tracked_date ? ` (seit ${formatDate(data.first_tracked_date)})` : ''}. Er zählt volle 15 Minuten pro Spiel und speichert keine Uhrzeiten.`);
        nodes.push(note);
        body.replaceChildren(...nodes);
    }

    function renderYears(years, active) {
        const tabs = byId('yearTabs');
        tabs.replaceChildren();
        for (const year of years) {
            const button = el('button', 'tag-chip' + (year === active ? ' active' : ''), String(year));
            button.type = 'button';
            button.setAttribute('aria-pressed', String(year === active));
            button.addEventListener('click', () => load(year));
            tabs.append(button);
        }
    }

    async function load(year) {
        const seq = ++requestSeq;
        const url = new URL(window.location.href);
        if (year) url.searchParams.set('jahr', year); else url.searchParams.delete('jahr');
        history.replaceState(null, '', url);
        try {
            const response = await fetch(`api.php?action=year_review${year ? `&year=${encodeURIComponent(year)}` : ''}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            const data = await response.json();
            if (seq !== requestSeq) return;
            if (!response.ok || data.ok === false) throw new Error(data.error || 'Der Jahresrückblick konnte nicht geladen werden.');
            render(data);
        } catch (error) {
            if (seq !== requestSeq) return;
            byId('reviewBody').replaceChildren(el('p', 'widget-empty', error.message));
        }
    }

    const requested = Number(new URLSearchParams(window.location.search).get('jahr')) || 0;
    load(requested || 0);
</script>
</body>
</html>
