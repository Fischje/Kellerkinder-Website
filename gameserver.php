<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
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
    <title>Server — Kellerkinder</title>
    <meta name="description" content="Die Gameserver der Kellerkinder: Adresse, Passwort und ob sie gerade laufen.">
    <link rel="icon" href="assets/kellerkinder-logo.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Montserrat:wght@500;600;700;800;900&family=Open+Sans:wght@400;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/includes/styles.php'; ?>
    <?php require __DIR__ . '/includes/blog-styles.php'; ?>
    <?php require __DIR__ . '/includes/gameserver-styles.php'; ?>
    <?php require __DIR__ . '/includes/arena-styles.php'; ?>
    <?php require __DIR__ . '/includes/navi-styles.php'; ?>
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
$activeNav = 'servers';
$pageKicker = 'Server';
$pageTitle = 'Unsere Server';
$pageLead = 'Die Gameserver der Kellerkinder: Adresse, Passwort und ob sie gerade laufen.';
$showInstall = false;
require __DIR__ . '/includes/site-header.php';
?>

<main class="page-shell">

    <section class="server-panel">
        <div class="server-head">
            <h2 class="section-title"><span class="accent">Unsere</span> Gameserver</h2>
            <button class="primary-button" id="addServerButton" type="button" hidden>＋ Server hinzufügen</button>
        </div>
        <p class="stats-note" id="serverNote" hidden></p>
        <div id="serverGrid" class="server-grid"><p class="widget-loading">Wird geladen …</p></div>
    </section>

    <footer class="site-footer">Created by Fischje with <span class="heart" aria-label="Love">♥</span> · Made with AI · Version <?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?></footer>
</main>

<dialog id="serverDialog" class="wide">
    <div class="modal-content form-stack">
        <h2 class="modal-title" id="serverDialogTitle">Server hinzufügen</h2>

        <div class="server-form-grid">
            <div>
                <label for="srvName">Name</label>
                <input type="text" id="srvName" maxlength="60" placeholder="z. B. Keller-Welt">
            </div>
            <div>
                <label for="srvGame">Spiel (für das Logo)</label>
                <input type="text" id="srvGame" maxlength="100" placeholder="z. B. Valheim">
            </div>
            <div>
                <label for="srvHost">IP-Adresse oder Hostname</label>
                <input type="text" id="srvHost" maxlength="100" placeholder="z. B. play.example.org">
            </div>
            <div>
                <label for="srvPort">Port</label>
                <input type="text" inputmode="numeric" id="srvPort" maxlength="5" placeholder="z. B. 2456">
            </div>
            <div>
                <label for="srvPassword">Passwort</label>
                <input type="text" id="srvPassword" maxlength="100" autocomplete="off" placeholder="leer = keines">
                <p class="server-form-hint" id="srvPasswordHint" hidden>Leer lassen, um das bisherige Passwort zu behalten.</p>
            </div>
            <div>
                <label for="srvNote">Hinweis (optional)</label>
                <input type="text" id="srvNote" maxlength="120" placeholder="z. B. Mods: siehe Discord">
            </div>
        </div>

        <details>
            <summary>Statusabfrage durch den Discord-Bot</summary>
            <div class="server-form-grid" style="margin-top:12px">
                <div>
                    <label for="srvQuery">Abfrageart</label>
                    <select id="srvQuery">
                        <option value="tcp">Nur Erreichbarkeit (TCP-Port)</option>
                        <option value="a2s">Steam/Source-Abfrage (UDP, mit Spielernamen)</option>
                        <option value="minecraft">Minecraft (TCP, mit Spielernamen)</option>
                    </select>
                    <p class="server-form-hint">Meldungen „Spieler beigetreten“ im Discord gibt es nur bei den Arten mit Spielernamen.</p>
                </div>
                <div>
                    <label for="srvQueryPort">Abfrage-Port (optional)</label>
                    <input type="text" inputmode="numeric" id="srvQueryPort" maxlength="5" placeholder="leer = wie Port">
                    <p class="server-form-hint">Viele Steam-Server antworten auf einem anderen Port (z. B. Valheim: Port + 1).</p>
                </div>
                <div>
                    <label for="srvProbeHost">Interne Adresse (optional)</label>
                    <input type="text" id="srvProbeHost" maxlength="100" placeholder="z. B. 192.168.1.50">
                    <p class="server-form-hint">Wenn der Bot den Server unter der öffentlichen Adresse nicht erreicht.</p>
                </div>
            </div>
        </details>

        <p class="form-error" id="serverError" role="alert" hidden></p>

        <div class="modal-actions">
            <button class="danger-button" id="deleteServerButton" type="button" hidden>Löschen</button>
            <button class="secondary-button" type="button" data-close-dialog="serverDialog">Abbrechen</button>
            <button class="primary-button" id="saveServerButton" type="button">Speichern</button>
        </div>
    </div>
</dialog>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
    const byId = id => document.getElementById(id);
    const REFRESH_MS = 30000;
    const state = { csrf: '', loggedIn: false, isAdmin: false, servers: [], edit: [], editingId: 0 };
    const revealed = new Set();

    let toastTimer = null;
    function showToast(message) {
        const toast = byId('toast');
        toast.textContent = message;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 3200);
    }

    async function api(action, payload = null) {
        const options = payload === null
            ? { headers: { Accept: 'application/json' }, credentials: 'same-origin' }
            : {
                method: 'POST',
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-Token': state.csrf },
                body: JSON.stringify({ action, ...payload })
            };
        const response = await fetch(payload === null ? `api.php?action=${encodeURIComponent(action)}` : 'api.php', options);
        let data;
        try { data = await response.json(); }
        catch { throw new Error('Der Server hat keine gültige Antwort geliefert.'); }
        if (!response.ok || data.ok === false) throw new Error(data.error || 'Die Aktion ist fehlgeschlagen.');
        return data;
    }

    async function copyText(text, label) {
        try {
            await navigator.clipboard.writeText(text);
            showToast(`${label} kopiert.`);
        } catch {
            showToast('Kopieren wird von diesem Browser nicht erlaubt.');
        }
    }

    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }

    function miniButton(label, onClick) {
        const button = el('button', 'server-mini', label);
        button.type = 'button';
        button.addEventListener('click', onClick);
        return button;
    }

    // Logo des Spiels; fehlt es oder lädt nicht, erscheint ein Platzhalter.
    function logoElement(src) {
        const placeholder = () => {
            const box = el('span', 'server-logo placeholder', '🎮');
            box.setAttribute('aria-hidden', 'true');
            return box;
        };
        if (!src) return placeholder();
        const img = el('img', 'server-logo');
        img.src = src;
        img.alt = '';
        img.loading = 'lazy';
        img.addEventListener('error', () => img.replaceWith(placeholder()));
        return img;
    }

    function statusBadge(server) {
        const labels = { online: 'Online', offline: 'Offline', unknown: 'Unbekannt' };
        const badge = el('span', `server-status ${server.state}`, labels[server.state] || labels.unknown);
        if (server.state === 'online' && server.players !== null) {
            badge.textContent += server.max_players ? ` · ${server.players}/${server.max_players}` : ` · ${server.players}`;
        }
        return badge;
    }

    function row(label, value, buttons) {
        const wrap = document.createDocumentFragment();
        wrap.append(el('dt', '', label), value, buttons || el('span'));
        return wrap;
    }

    function renderCard(server) {
        const card = el('article', 'server-card' + (server.state === 'online' ? ' is-online' : ''));
        const top = el('div', 'server-top');
        const title = el('div', 'server-title');
        title.append(el('h3', '', server.name), el('small', '', server.game));
        top.append(logoElement(server.icon), title);
        card.append(top, statusBadge(server));

        const rows = el('dl', 'server-rows');
        const address = `${server.host}:${server.port}`;
        const addressButtons = el('span');
        addressButtons.append(miniButton('Kopieren', () => copyText(address, 'Adresse')));
        rows.append(row('IP', el('dd', '', address), addressButtons));

        if (server.has_password) {
            const shown = state.loggedIn && revealed.has(server.id);
            const passwordText = el('dd', '', shown ? server.password : '••••••••');
            const passwordButtons = el('span');
            if (state.loggedIn) {
                passwordButtons.append(miniButton(shown ? 'Verbergen' : 'Anzeigen', () => {
                    if (revealed.has(server.id)) revealed.delete(server.id); else revealed.add(server.id);
                    render();
                }));
                if (shown) passwordButtons.append(' ', miniButton('Kopieren', () => copyText(server.password, 'Passwort')));
            }
            rows.append(row('Passwort', passwordText, passwordButtons));
        } else {
            rows.append(row('Passwort', el('dd', '', 'keines')));
        }
        card.append(rows);

        if (server.has_password && !state.loggedIn) {
            const hint = el('p', 'server-hint');
            hint.append(document.createTextNode('Das Passwort siehst du, wenn du angemeldet bist – '));
            const link = el('a', '', 'zur Anmeldung');
            link.href = 'index.php';
            hint.append(link, '.');
            card.append(hint);
        }
        if (server.note) card.append(el('p', 'server-meta', server.note));
        if (server.player_names.length > 0) card.append(el('p', 'server-meta', `Gerade online: ${server.player_names.join(', ')}`));

        if (state.isAdmin) {
            const actions = el('div', 'server-card-actions');
            actions.append(miniButton('Bearbeiten', () => openEditor(server.id)));
            card.append(actions);
        }
        return card;
    }

    function render() {
        const grid = byId('serverGrid');
        if (state.servers.length === 0) {
            grid.replaceChildren(el('p', 'widget-empty', state.isAdmin
                ? 'Noch kein Server eingetragen. Mit „Server hinzufügen“ geht es los.'
                : 'Noch kein Server eingetragen.'));
            return;
        }
        grid.replaceChildren(...state.servers.map(renderCard));
    }

    async function load() {
        try {
            const data = await api('gameservers');
            state.csrf = data.csrf_token || state.csrf;
            state.loggedIn = data.logged_in;
            state.isAdmin = data.is_admin;
            state.servers = data.servers;
            state.edit = data.edit || [];
            byId('addServerButton').hidden = !state.isAdmin;
            const note = byId('serverNote');
            if (!data.status_configured) {
                note.textContent = 'Der Online-Status ist noch nicht eingerichtet (Discord-Bot-Anbindung fehlt).';
            } else if (!data.bot_reachable) {
                note.textContent = 'Der Discord-Bot ist gerade nicht erreichbar – der Status ist unbekannt.';
            }
            note.hidden = !note.textContent || (data.status_configured && data.bot_reachable);
            render();
        } catch (error) {
            byId('serverGrid').replaceChildren(el('p', 'widget-empty', error.message));
        }
    }

    // ===== Admin: Server anlegen, ändern, löschen =====
    const dialog = byId('serverDialog');

    function openEditor(id) {
        const server = state.edit.find(item => item.id === id) || null;
        state.editingId = server ? server.id : 0;
        byId('serverDialogTitle').textContent = server ? 'Server bearbeiten' : 'Server hinzufügen';
        byId('srvName').value = server ? server.name : '';
        byId('srvGame').value = server ? server.game : '';
        byId('srvHost').value = server ? server.host : '';
        byId('srvPort').value = server ? server.port : '';
        byId('srvPassword').value = '';
        byId('srvPasswordHint').hidden = !(server && server.password !== '');
        byId('srvNote').value = server ? server.note : '';
        byId('srvQuery').value = server ? server.query : 'tcp';
        byId('srvQueryPort').value = server && server.query_port ? server.query_port : '';
        byId('srvProbeHost').value = server ? server.probe_host : '';
        byId('deleteServerButton').hidden = !server;
        byId('serverError').hidden = true;
        dialog.showModal();
    }

    byId('addServerButton').addEventListener('click', () => openEditor(0));

    byId('saveServerButton').addEventListener('click', async () => {
        const button = byId('saveServerButton');
        const error = byId('serverError');
        error.hidden = true;
        button.disabled = true;
        try {
            await api('admin_save_gameserver', {
                id: state.editingId,
                name: byId('srvName').value,
                game: byId('srvGame').value,
                host: byId('srvHost').value,
                port: byId('srvPort').value,
                password: byId('srvPassword').value,
                note: byId('srvNote').value,
                query: byId('srvQuery').value,
                query_port: byId('srvQueryPort').value,
                probe_host: byId('srvProbeHost').value,
            });
            dialog.close();
            showToast('Server gespeichert.');
            await load();
        } catch (failure) {
            error.textContent = failure.message;
            error.hidden = false;
        } finally {
            button.disabled = false;
        }
    });

    byId('deleteServerButton').addEventListener('click', async () => {
        if (!state.editingId || !confirm('Diesen Server wirklich löschen?')) return;
        try {
            await api('admin_delete_gameserver', { id: state.editingId });
            dialog.close();
            showToast('Server gelöscht.');
            await load();
        } catch (failure) {
            byId('serverError').textContent = failure.message;
            byId('serverError').hidden = false;
        }
    });

    for (const button of document.querySelectorAll('[data-close-dialog]')) {
        button.addEventListener('click', () => byId(button.dataset.closeDialog).close());
    }

    load();
    setInterval(() => { if (!document.hidden && !dialog.open) load(); }, REFRESH_MS);

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('./service-worker.js').catch(() => {});
        });
    }
</script>
</body>
</html>
