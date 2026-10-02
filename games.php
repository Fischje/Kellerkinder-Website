<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" id="themeColorMeta" content="#060606">
    <meta name="application-name" content="Kellerkinder">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Kellerkinder">
    <title>Spiele — Kellerkinder</title>
    <meta name="description" content="Was die Kellerkinder aktuell spielen.">
    <link rel="icon" href="assets/kellerkinder-logo.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/includes/styles.php'; ?>
    <?php require __DIR__ . '/includes/blog-styles.php'; ?>
    <?php require __DIR__ . '/includes/games-styles.php'; ?>
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

<main class="page-shell">
    <header class="masthead">
        <div class="masthead-row">
            <span class="brand">
                <img src="assets/kellerkinder-logo.svg" alt="" class="brand-logo">
                <span class="brand-text">
                    <span class="brand-name-row">
                        <span class="brand-name">Kellerkinder</span>
                        <span class="brand-sun" aria-hidden="true">☀️💦</span>
                    </span>
                    <span class="subtitle">Online-Gaming mit Freunden seit <em class="shine">ewig</em></span>
                </span>
            </span>
            <nav class="main-nav" aria-label="Hauptnavigation">
                <a href="index.php" class="nav-link">Kalender</a>
                <a href="blog.php" class="nav-link">Blog</a>
                <a href="games.php" class="nav-link active">Spiele</a>
                <a href="statistik.php" class="nav-link">Statistik</a>
                <span class="nav-link disabled">Netzje <small>(folgt)</small></span>
            </nav>
        </div>
    </header>

    <section class="games-panel">
        <div class="games-head">
            <h1>Das spielen wir gerade</h1>
            <p>Unsere kleine Bibliothek der aktuell gespielten Spiele. Das Bild kommt automatisch aus einer Spiele-Datenbank.</p>
        </div>

        <div class="game-search" id="gameSearchBox" hidden>
            <label for="gameSearch">Spiel hinzufügen</label>
            <input type="text" id="gameSearch" maxlength="100" autocomplete="off" placeholder="Spielname eingeben, z. B. Diablo IV">
            <ul class="game-results" id="gameResults" hidden></ul>
        </div>
        <p class="post-meta" id="loginHint" hidden>Zum Eintragen von Spielen bitte zuerst <a href="index.php">im Kalender anmelden</a>.</p>

        <div class="game-grid" id="gameGrid">
            <p class="widget-loading">Wird geladen …</p>
        </div>
    </section>

    <footer class="site-footer">Created by Fischje with <span class="heart" aria-label="Love">♥</span> · Made with AI · Version <?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?></footer>
</main>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
    const byId = id => document.getElementById(id);
    const state = { csrf: '', auth: { logged_in: false, is_admin: false, user: null }, games: [] };

    let toastTimer = null;
    function showToast(message) {
        const toast = byId('toast');
        toast.textContent = message;
        toast.classList.add('visible');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('visible'), 4200);
    }

    async function api(action, payload = null, query = null) {
        const options = payload === null
            ? { headers: { Accept: 'application/json' }, credentials: 'same-origin' }
            : {
                method: 'POST',
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-Token': state.csrf },
                body: JSON.stringify({ action, ...payload })
            };
        let url = payload === null ? `api.php?action=${encodeURIComponent(action)}` : 'api.php';
        if (payload === null && query) {
            for (const [key, value] of Object.entries(query)) {
                url += `&${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
            }
        }
        const response = await fetch(url, options);
        let data;
        try { data = await response.json(); }
        catch { throw new Error('Der Server hat keine gültige Antwort geliefert.'); }
        if (!response.ok || data.ok === false) {
            throw new Error(data.error || 'Die Aktion ist fehlgeschlagen.');
        }
        return data;
    }

    function thumb(src, className) {
        const placeholder = () => {
            const empty = document.createElement('div');
            empty.className = 'game-thumb-empty' + (className ? ' ' + className : '');
            empty.textContent = '🎮';
            return empty;
        };
        if (!src) return placeholder();
        const img = document.createElement('img');
        img.src = src;
        img.alt = '';
        img.loading = 'lazy';
        img.addEventListener('error', () => img.replaceWith(placeholder()));
        return img;
    }

    function formatDate(iso) {
        const date = new Date(iso);
        return Number.isNaN(date.getTime()) ? '' : date.toLocaleDateString('de-DE', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function renderGames() {
        const grid = byId('gameGrid');
        grid.replaceChildren();
        if (state.games.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'widget-empty';
            empty.textContent = 'Noch kein Spiel eingetragen.';
            grid.appendChild(empty);
            return;
        }
        for (const game of state.games) {
            const card = document.createElement('article');
            card.className = 'game-card';
            card.appendChild(thumb(game.image));

            const body = document.createElement('div');
            body.className = 'game-card-body';
            const title = document.createElement('h2');
            title.textContent = game.name;
            const meta = document.createElement('p');
            meta.className = 'post-meta';
            meta.textContent = `${game.added_by_name} · ${formatDate(game.added_at)}`;
            body.append(title, meta);
            card.appendChild(body);

            const canRemove = state.auth.is_admin
                || (state.auth.user && game.added_by_user_id === state.auth.user.id);
            if (canRemove) {
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'game-remove';
                remove.title = 'Aus der Bibliothek entfernen';
                remove.setAttribute('aria-label', `${game.name} entfernen`);
                remove.textContent = '✕';
                remove.addEventListener('click', () => removeGame(game));
                card.appendChild(remove);
            }
            grid.appendChild(card);
        }
    }

    async function loadGames() {
        try {
            state.games = (await api('games')).games || [];
            renderGames();
        } catch (error) {
            byId('gameGrid').innerHTML = '<p class="widget-empty">Die Spiele konnten nicht geladen werden.</p>';
            showToast(error.message);
        }
    }

    async function addGame(name, image, appid) {
        hideResults();
        byId('gameSearch').value = '';
        try {
            await api('game_add', { name, image: image || '', steam_appid: appid || 0 });
            showToast(`„${name}“ wurde hinzugefügt.`);
            await loadGames();
        } catch (error) {
            showToast(error.message);
        }
    }

    async function removeGame(game) {
        if (!window.confirm(`„${game.name}“ aus der Bibliothek entfernen?`)) return;
        try {
            await api('game_remove', { id: game.id });
            await loadGames();
        } catch (error) {
            showToast(error.message);
        }
    }

    // ===== Steam-Suche =====
    const resultsBox = byId('gameResults');
    const searchInput = byId('gameSearch');
    let searchTimer = null;
    let searchSeq = 0;

    function hideResults() {
        resultsBox.hidden = true;
        resultsBox.replaceChildren();
    }

    function resultRow(content) {
        const item = document.createElement('li');
        item.appendChild(content);
        resultsBox.appendChild(item);
    }

    function hint(text) {
        const p = document.createElement('div');
        p.className = 'game-result-hint';
        p.textContent = text;
        return p;
    }

    function manualRow(name) {
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'game-result';
        button.append(thumb(''), document.createTextNode(`„${name}“ ohne Bild hinzufügen`));
        button.addEventListener('click', () => addGame(name, '', 0));
        return button;
    }

    async function runSearch() {
        const term = searchInput.value.trim();
        const seq = ++searchSeq;
        if (term.length < 2) { hideResults(); return; }
        resultsBox.hidden = false;
        resultsBox.replaceChildren();
        resultRow(hint('Suche …'));
        try {
            const data = await api('game_search', null, { term });
            if (seq !== searchSeq) return;
            resultsBox.replaceChildren();
            for (const entry of data.results) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'game-result';
                const label = document.createElement('span');
                label.textContent = entry.name;
                button.append(thumb(entry.image), label);
                button.addEventListener('click', () => addGame(entry.name, entry.image, entry.steam_appid));
                resultRow(button);
            }
            if (data.results.length === 0) resultRow(hint('Kein Treffer.'));
            resultRow(manualRow(term));
        } catch (error) {
            if (seq !== searchSeq) return;
            resultsBox.replaceChildren();
            resultRow(hint(error.message));
            resultRow(manualRow(term));
        }
    }

    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(runSearch, 350);
    });
    searchInput.addEventListener('keydown', event => { if (event.key === 'Escape') hideResults(); });
    document.addEventListener('click', event => {
        if (!byId('gameSearchBox').contains(event.target)) hideResults();
    });

    async function loadAuth() {
        try {
            const data = await api('bootstrap');
            state.csrf = data.csrf_token || '';
            state.auth = data.auth || state.auth;
        } catch { /* Die Liste bleibt auch ohne Anmeldung lesbar. */ }
        const canWrite = !!(state.auth.logged_in && state.auth.can_write);
        byId('gameSearchBox').hidden = !canWrite;
        byId('loginHint').hidden = canWrite;
    }

    (async () => {
        await loadAuth();
        await loadGames();
    })();

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('./service-worker.js').catch(() => {});
        });
    }
</script>
</body>
</html>
