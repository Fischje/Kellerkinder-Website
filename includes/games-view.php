<?php
// Gemeinsame Ansicht für die Seiten „Spiele“ (games.php, Top 10/30) und „Alle Spiele“
// (alle-spiele.php). Erwartet $gamesView = 'top' | 'all'.
declare(strict_types=1);
$gamesView = ($gamesView ?? 'top') === 'all' ? 'all' : 'top';
require __DIR__ . '/bootstrap.php';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" id="themeColorMeta" content="#140b1b">
    <meta name="application-name" content="Kellerkinder">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Kellerkinder">
    <title><?= $gamesView === 'all' ? 'Alle Spiele' : 'Spiele' ?> — Kellerkinder</title>
    <meta name="description" content="Welche Spiele die Kellerkinder spielen und wie lange.">
    <link rel="icon" href="assets/kellerkinder-logo.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800;900&family=Open+Sans:wght@400;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/styles.php'; ?>
    <?php require __DIR__ . '/blog-styles.php'; ?>
    <?php require __DIR__ . '/stats-styles.php'; ?>
    <?php require __DIR__ . '/arena-styles.php'; ?>
</head>
<body data-theme="default">
<div class="season-scene" aria-hidden="true">
    <div class="summer-sun"></div>
    <div class="summer-bubbles"></div>
    <div class="summer-waves"></div>
    <div class="winter-snow"></div>
    <div class="winter-aurora left"></div>
    <div class="winter-aurora right"></div>
    <div class="winter-ember-glow"></div>
    <div class="winter-snowbank"></div>
</div>

<?php
$activeNav = 'games';
if ($gamesView === 'all') {
    $pageKicker = 'Spiele / Alle Spiele';
    $pageTitle = 'Alle Spiele';
    $pageLead = 'Die komplette Rangliste aller Spiele, die bei uns auf Discord gespielt wurden. Der Bot zählt dafür nur volle 15 Minuten pro Spiel.';
} else {
    $pageKicker = 'Spiele';
    $pageTitle = 'Das spielen wir';
    $pageLead = 'Welche Spiele bei uns auf Discord laufen und wie lange. Der Bot zählt dafür nur volle 15 Minuten pro Spiel.';
}
$showInstall = false;
require __DIR__ . '/site-header.php';
?>

<main class="page-shell">

    <section class="games-panel">
        <div class="games-head">
            <?php if ($gamesView === 'all'): ?>
                <h2 class="section-title"><span class="accent">Alle</span> Spiele</h2>
            <?php else: ?>
                <h2 class="section-title"><span class="accent">Meist</span> gespielt</h2>
            <?php endif; ?>
        </div>

        <div class="stats-periods" id="statsPeriods" role="group" aria-label="Zeitraum">
            <button type="button" class="stats-period" data-days="7">Letzte 7 Tage</button>
            <button type="button" class="stats-period active" data-days="30">Letzte 30 Tage</button>
            <button type="button" class="stats-period" data-days="365">Ein Jahr</button>
            <button type="button" class="stats-period" data-days="0">Immer</button>
        </div>

        <div id="statsBody"><p class="widget-loading">Wird geladen …</p></div>
    </section>

    <?php if ($gamesView === 'top'): ?>
    <section class="games-panel stats-admin" id="statsAdmin" hidden>
        <h2 class="section-title"><span class="accent">Spieler</span> ausblenden</h2>
        <p class="stats-note">Nur für Admins sichtbar. Angehakte Discord-Mitglieder zählen nicht zur Statistik –
            weder hier noch beim Discord-Befehl <code>/statistik</code>. Ihre Spielzeit wird weiter erfasst, sodass du sie
            jederzeit wieder einblenden kannst.</p>
        <div id="statsAdminList" class="stats-admin-list"></div>
        <p class="stats-note" id="statsAvatarStatus"></p>
        <div class="stats-admin-actions">
            <button class="primary-button" id="statsAdminSave" type="button">Speichern</button>
            <span class="stats-note" id="statsAdminStatus" role="status" aria-live="polite"></span>
        </div>
    </section>
    <?php endif; ?>

    <footer class="site-footer">Created by Fischje with <span class="heart" aria-label="Love">♥</span> · Made with AI · Version <?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?></footer>
</main>

<script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
    const body = document.getElementById('statsBody');
    const VIEW = <?= json_encode($gamesView) ?>;       // 'top' oder 'all'
    const TOP_VISIBLE = 10;                              // so viele Spiele sind sofort zu sehen
    const TOP_EXPANDED = 30;                             // bis hierhin lässt sich aufklappen
    const ALLOWED_DAYS = [7, 30, 365, 0];
    let requestSeq = 0;

    function formatMinutes(minutes) {
        const hours = Math.floor(minutes / 60);
        const rest = minutes % 60;
        if (hours === 0) return `${rest} Min.`;
        return rest === 0 ? `${hours} Std.` : `${hours} Std. ${rest} Min.`;
    }

    function formatDate(iso) {
        const date = new Date(`${iso}T00:00:00`);
        return Number.isNaN(date.getTime()) ? '' : date.toLocaleDateString('de-DE', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function message(text) {
        const p = document.createElement('p');
        p.className = 'widget-empty';
        p.textContent = text;
        body.replaceChildren(p);
    }

    function tile(value, label) {
        const el = document.createElement('div');
        el.className = 'stats-tile';
        const strong = document.createElement('strong');
        strong.textContent = value;
        const span = document.createElement('span');
        span.textContent = label;
        el.append(strong, span);
        return el;
    }

    // Kleine Liste im aufgeklappten Bereich (Top-Spieler eines Spiels bzw. Top-Spiele eines Spielers).
    function detailList(title, rows) {
        const box = document.createElement('div');
        box.className = 'stats-detail';
        const heading = document.createElement('p');
        heading.className = 'stats-detail-title';
        heading.textContent = title;
        const ol = document.createElement('ol');
        ol.className = 'stats-detail-list';
        const max = Math.max(1, ...rows.map(row => row.minutes));
        rows.forEach((row, index) => {
            const li = document.createElement('li');
            const bar = document.createElement('span');
            bar.className = 'stats-bar';
            bar.style.width = `${Math.max(2, Math.round((row.minutes / max) * 100))}%`;
            const rank = document.createElement('span');
            rank.className = 'stats-rank';
            rank.textContent = `${index + 1}.`;
            const name = document.createElement('span');
            name.className = 'stats-name';
            name.textContent = row.name;
            const time = document.createElement('span');
            time.className = 'stats-time';
            time.textContent = formatMinutes(row.minutes);
            if ('avatar' in row) {
                li.classList.add('with-avatar');
                li.append(bar, rank, avatarElement(row.avatar, row.name), name, time);
            } else {
                li.append(bar, rank, name, time);
            }
            ol.appendChild(li);
        });
        box.append(heading, ol);
        return box;
    }

    let detailSeq = 0;

    // Profilbild eines Spielers (heruntergeladene Kopie) bzw. Anfangsbuchstabe als Platzhalter.
    function avatarElement(src, name) {
        const placeholder = () => {
            const box = document.createElement('span');
            box.className = 'stats-avatar placeholder';
            box.setAttribute('aria-hidden', 'true');
            box.textContent = (String(name || '?').trim()[0] || '?').toUpperCase();
            return box;
        };
        if (!src) return placeholder();
        const img = document.createElement('img');
        img.className = 'stats-avatar';
        img.src = src;
        img.alt = '';
        img.loading = 'lazy';
        img.addEventListener('error', () => img.replaceWith(placeholder()));
        return img;
    }

    // Spiele-Symbol: das heruntergeladene Bild, sonst ein Platzhalter (auch wenn das Bild nicht lädt).
    function gameIcon(src) {
        const placeholder = () => {
            const box = document.createElement('span');
            box.className = 'stats-icon placeholder';
            box.setAttribute('aria-hidden', 'true');
            box.textContent = '🎮';
            return box;
        };
        if (!src) return placeholder();
        const img = document.createElement('img');
        img.className = 'stats-icon';
        img.src = src;
        img.alt = '';
        img.loading = 'lazy';
        img.addEventListener('error', () => img.replaceWith(placeholder()));
        return img;
    }

    // rows: { name, minutes, sub, detail?: { title, rows } } – mit `detail` lässt sich die Zeile aufklappen.
    function list(title, rows, options = {}) {
        const wrap = document.createDocumentFragment();
        const heading = document.createElement('h2');
        heading.className = 'stats-section';
        heading.textContent = title;
        const ul = document.createElement('ol');
        ul.className = 'stats-list';
        const max = Math.max(1, ...rows.map(row => row.minutes));
        const visible = options.visible || rows.length;
        const extraItems = [];
        rows.forEach((row, index) => {
            const li = document.createElement('li');
            li.className = 'stats-item';
            if (index >= visible) {
                li.hidden = true;
                extraItems.push(li);
            }
            const expandable = Boolean(row.detail && row.detail.rows.length > 0);
            const main = document.createElement(expandable ? 'button' : 'div');
            main.className = 'stats-row' + (expandable ? ' expandable' : '');
            if (expandable) main.type = 'button';
            const bar = document.createElement('span');
            bar.className = 'stats-bar';
            bar.style.width = `${Math.max(2, Math.round((row.minutes / max) * 100))}%`;
            const rank = document.createElement('span');
            rank.className = 'stats-rank';
            rank.textContent = `${index + 1}.`;
            const icon = row.hasIcon ? gameIcon(row.icon) : (row.hasAvatar ? avatarElement(row.avatar, row.name) : null);
            const name = document.createElement('span');
            name.className = 'stats-name';
            name.textContent = row.name;
            if (row.sub) {
                const small = document.createElement('small');
                small.textContent = row.sub;
                name.appendChild(small);
            }
            const time = document.createElement('span');
            time.className = 'stats-time';
            time.textContent = formatMinutes(row.minutes);
            if (icon) {
                main.classList.add(row.hasAvatar ? 'with-avatar' : 'with-icon');
                main.append(bar, rank, icon, name, time);
            } else {
                main.append(bar, rank, name, time);
            }
            if (expandable) {
                const caret = document.createElement('span');
                caret.className = 'stats-caret';
                caret.setAttribute('aria-hidden', 'true');
                caret.textContent = '▾';
                main.appendChild(caret);
            }
            li.appendChild(main);

            if (expandable) {
                const panel = detailList(row.detail.title, row.detail.rows);
                panel.id = `statsDetail${detailSeq++}`;
                panel.hidden = true;
                main.setAttribute('aria-expanded', 'false');
                main.setAttribute('aria-controls', panel.id);
                main.addEventListener('click', () => {
                    const open = panel.hidden;
                    panel.hidden = !open;
                    main.setAttribute('aria-expanded', String(open));
                    li.classList.toggle('open', open);
                });
                li.appendChild(panel);
            }
            ul.appendChild(li);
        });
        wrap.append(heading, ul);
        if (extraItems.length > 0) {
            // Plätze ab „visible“ liegen versteckt in derselben Liste und werden per Knopf gezeigt.
            const toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'secondary-button stats-more-toggle';
            toggle.setAttribute('aria-expanded', 'false');
            const label = `Platz ${visible + 1}–${rows.length} anzeigen`;
            toggle.textContent = label;
            toggle.addEventListener('click', () => {
                const open = toggle.getAttribute('aria-expanded') !== 'true';
                extraItems.forEach(item => { item.hidden = !open; });
                toggle.setAttribute('aria-expanded', String(open));
                toggle.textContent = open ? 'Weniger anzeigen' : label;
            });
            wrap.append(toggle);
        }
        return wrap;
    }

    // ===== Admin: Spieler aus der Statistik ausblenden =====
    let csrfToken = '';
    let currentDays = 30;

    function renderAdmin(data) {
        const panel = document.getElementById('statsAdmin');
        if (!panel) return; // „Alle Spiele“ hat keinen Admin-Bereich
        if (!Array.isArray(data.roster)) { panel.hidden = true; return; }
        panel.hidden = false;
        const container = document.getElementById('statsAdminList');
        container.replaceChildren();
        if (data.roster_supported === false) {
            const note = document.createElement('p');
            note.className = 'widget-empty';
            note.textContent = 'Der Discord-Bot liefert noch keine Spielerliste. Dafür ist Bot-Version 1.6.0 nötig.';
            container.appendChild(note);
        }
        if (data.roster.length === 0 && data.roster_supported !== false) {
            const note = document.createElement('p');
            note.className = 'widget-empty';
            note.textContent = 'Im gewählten Zeitraum hat noch niemand gespielt.';
            container.appendChild(note);
        }
        // Hinweis zu den Discord-Profilbildern (nur für Admins): wo hängt es, falls keine erscheinen?
        const avatarNote = document.getElementById('statsAvatarStatus');
        const status = data.avatar_status;
        if (avatarNote && status) {
            if (status.bot === 0) {
                avatarNote.textContent = 'Profilbilder: Der Discord-Bot liefert noch keine Bild-Adressen. Das braucht Bot-Version 1.8.0; der Bot fragt sie beim Start und danach täglich bei Discord ab.';
            } else {
                avatarNote.textContent = `Profilbilder: ${status.local} von ${status.players} Spielern geladen (der Bot kennt ${status.bot}).`
                    + (status.writable ? '' : ' Der Ordner data/media ist für PHP nicht beschreibbar – bitte Schreibrechte geben.');
            }
        }
        for (const member of data.roster) {
            const label = document.createElement('label');
            label.className = 'stats-admin-item';
            const box = document.createElement('input');
            box.type = 'checkbox';
            box.checked = member.excluded;
            box.dataset.id = member.id;
            box.dataset.name = member.name;
            const name = document.createElement('span');
            name.textContent = member.name;
            const time = document.createElement('small');
            time.textContent = member.minutes > 0 ? formatMinutes(member.minutes) : '–';
            label.append(box, avatarElement(member.avatar, member.name), name, time);
            container.appendChild(label);
        }
    }

    document.getElementById('statsAdminSave')?.addEventListener('click', async () => {
        const button = document.getElementById('statsAdminSave');
        const status = document.getElementById('statsAdminStatus');
        const players = [...document.querySelectorAll('#statsAdminList input:checked')]
            .map(box => ({ id: box.dataset.id, name: box.dataset.name }));
        button.disabled = true;
        status.textContent = 'Wird gespeichert …';
        try {
            if (!csrfToken) {
                const boot = await (await fetch('api.php?action=bootstrap', { headers: { Accept: 'application/json' }, credentials: 'same-origin' })).json();
                csrfToken = boot.csrf_token || '';
            }
            const response = await fetch('api.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                body: JSON.stringify({ action: 'admin_set_stats_exclusions', players }),
            });
            const data = await response.json();
            if (!response.ok || data.ok === false) throw new Error(data.error || 'Speichern fehlgeschlagen.');
            status.textContent = players.length === 0
                ? 'Gespeichert. Alle Spieler zählen zur Statistik.'
                : `Gespeichert. ${players.length} Spieler ausgeblendet.`;
            load(currentDays);
        } catch (error) {
            status.textContent = error.message;
        } finally {
            button.disabled = false;
        }
    });

    function render(data) {
        renderAdmin(data);
        if (!data.configured) { message('Die Statistik ist noch nicht eingerichtet.'); return; }
        if (data.games.length === 0) { message('Für diesen Zeitraum liegen noch keine Spielzeiten vor.'); return; }

        const totalGames = Number(data.total_games) || data.games.length;
        const summary = document.createElement('div');
        summary.className = 'stats-summary';
        summary.append(
            tile(formatMinutes(data.total_minutes), 'Gesamte Spielzeit'),
            tile(String(totalGames), totalGames === 1 ? 'Spiel' : 'Spiele'),
            tile(data.games[0].name, 'Meistgespielt'),
        );

        const nodes = [summary];
        if (data.stale) {
            const note = document.createElement('p');
            note.className = 'stats-note';
            note.textContent = 'Der Bot ist gerade nicht erreichbar. Gezeigt wird der letzte bekannte Stand.';
            nodes.push(note);
        }
        const hasDetails = Array.isArray(data.players) && data.players.some(player => Array.isArray(player.top_games));
        if (VIEW === 'all') {
            const back = document.createElement('a');
            back.className = 'secondary-button stats-link-button';
            back.href = `games.php?tage=${data.days}`;
            back.textContent = '← Zurück zu den Top-Spielen';
            nodes.push(back);
        }
        nodes.push(list('Spiele', data.games.map(game => ({
            name: game.name,
            minutes: game.minutes,
            sub: `${game.players} Spieler · zuletzt ${formatDate(game.last_played)}`,
            detail: Array.isArray(game.top_players) ? { title: 'Top-Spieler', rows: game.top_players } : null,
            hasIcon: true,
            icon: game.icon || '',
        })), VIEW === 'top' ? { visible: TOP_VISIBLE } : {}));
        if (VIEW === 'top' && totalGames > data.games.length) {
            // Mehr als die Top 30: Rest steht auf der eigenen Seite mit allen Spielen.
            const all = document.createElement('a');
            all.className = 'primary-button stats-link-button';
            all.href = `alle-spiele.php?tage=${data.days}`;
            all.textContent = `Alle ${totalGames} Spiele ansehen →`;
            nodes.push(all);
        }
        if (VIEW === 'top' && Array.isArray(data.players) && data.players.length > 0) {
            nodes.push(list('Kellerkinder', data.players.map(player => ({
                name: player.name,
                minutes: player.minutes,
                sub: player.top_game ? `am meisten ${player.top_game}` : '',
                detail: Array.isArray(player.top_games) ? { title: 'Top-5-Spiele', rows: player.top_games } : null,
                hasAvatar: true,
                avatar: player.avatar || '',
            }))));
        }
        if (hasDetails || data.games.some(game => Array.isArray(game.top_players))) {
            const hint = document.createElement('p');
            hint.className = 'stats-note';
            hint.textContent = 'Tipp: Ein Spiel oder einen Spieler anklicken, um die Top-Spieler bzw. Top-5-Spiele zu sehen.';
            nodes.splice(nodes.indexOf(summary) + 1, 0, hint);
        }
        body.replaceChildren(...nodes);
    }

    async function load(days) {
        currentDays = days;
        const seq = ++requestSeq;
        try {
            const response = await fetch(`api.php?action=playtime_stats&days=${days}${VIEW === 'all' ? '&all=1' : ''}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            const data = await response.json();
            if (seq !== requestSeq) return;
            if (!response.ok || data.ok === false) throw new Error(data.error || 'Die Statistik konnte nicht geladen werden.');
            render(data);
        } catch (error) {
            if (seq !== requestSeq) return;
            message(error.message || 'Die Statistik konnte nicht geladen werden.');
        }
    }

    document.getElementById('statsPeriods').addEventListener('click', event => {
        const button = event.target.closest('.stats-period');
        if (!button) return;
        document.querySelectorAll('.stats-period').forEach(el => el.classList.toggle('active', el === button));
        load(Number(button.dataset.days));
    });

    // Zeitraum aus der Adresse (?tage=…), sonst die letzten 30 Tage.
    const requestedDays = Number(new URLSearchParams(window.location.search).get('tage'));
    const startDays = ALLOWED_DAYS.includes(requestedDays) && new URLSearchParams(window.location.search).has('tage') ? requestedDays : 30;
    document.querySelectorAll('.stats-period').forEach(el => el.classList.toggle('active', Number(el.dataset.days) === startDays));
    load(startDays);

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('./service-worker.js').catch(() => {});
        });
    }
</script>
</body>
</html>
