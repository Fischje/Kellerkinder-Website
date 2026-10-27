<?php
declare(strict_types=1);
// Zentrale Admin-Seite: steuert Seitenleiste (Kästen an/aus, Reihenfolge), Menü
// (Seiten ein-/ausblenden), Startseite und den Style für alle. Außerdem die
// offenen Platz-Anfragen der Netzje und die Benutzerverwaltung (Konten, Admins) –
// der frühere Adminbereich im Kalender ist seit 2.29.0 hier aufgegangen.
// Daten: api.php?action=site_settings (lesen), admin_save_features (speichern),
// netzje / admin_netzje_request (Anfragen), bootstrap (Benutzerliste),
// admin_create_user / admin_update_user / admin_delete_user / admin_save_settings. Nur Admins sehen die Einstellungen;
// die API prüft die Rechte bei jedem Speichern selbst.
require __DIR__ . '/includes/bootstrap.php';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1c1f25">
    <meta name="robots" content="noindex">
    <title>Admin — Kellerkinder</title>
    <link rel="icon" href="assets/kellerkinder-logo.svg?v=<?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png?v=<?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Montserrat:wght@500;600;700;800;900&family=Open+Sans:wght@400;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/includes/styles.php'; ?>
    <?php require __DIR__ . '/includes/arena-styles.php'; ?>
    <?php require __DIR__ . '/includes/navi-styles.php'; ?>
</head>
<body data-theme="default">

<?php
$activeNav = 'admin';
$pageKicker = 'Admin';
$pageTitle = 'Adminbereich';
$pageLead = 'Zentrale Schalter für Seitenleiste, Menü und Startseite – und die offenen Netzje-Anfragen.';
$showInstall = false;
require __DIR__ . '/includes/site-header.php';
?>

<main class="page-shell">

    <div id="adminGate" class="admin-panel">
        <p class="widget-loading">Wird geladen …</p>
    </div>

    <div id="adminContent" hidden>
        <section class="admin-panel" id="netzjePanel">
            <div class="admin-panel-head">
                <h2 class="section-title"><span class="accent">Netzje</span> Anfragen</h2>
                <a class="secondary-button" href="netzje.php">Zur Netzje</a>
            </div>
            <div id="netzjeRequests"><p class="widget-loading">Wird geladen …</p></div>
        </section>

        <form id="featuresForm" class="admin-settings">
            <section class="admin-panel">
                <h2 class="section-title"><span class="accent">Seitenleiste</span></h2>
                <p class="admin-help">Welche Kästen rechts (auf dem Handy unter dem Inhalt) erscheinen und in welcher Reihenfolge. Ist eine Seite im Menü ausgeblendet, fehlt auch ihr Kasten.</p>
                <ol class="admin-order" id="sidebarList"></ol>
            </section>

            <section class="admin-panel">
                <h2 class="section-title"><span class="accent">Menü</span> &amp; Seiten</h2>
                <p class="admin-help">Ausgeblendete Seiten verschwinden aus dem Menü und der Seitenleiste. Per Link bleiben sie erreichbar (mit Hinweis), damit du sie vorbereiten kannst. Der Kalender ist immer da.</p>
                <div class="admin-switches" id="pageSwitches"></div>
            </section>

            <section class="admin-panel">
                <h2 class="section-title"><span class="accent">Startseite</span></h2>
                <div class="admin-switches" id="homeSwitches"></div>
            </section>

            <section class="admin-panel">
                <h2 class="section-title"><span class="accent">Style</span> für alle</h2>
                <label for="themeSelect">Style auswählen</label>
                <select id="themeSelect">
                    <option value="default">Standard: NAVI (Graublau / Grün)</option>
                    <option value="summer">Sommer: Sonne, Strand und Wasser</option>
                    <option value="winter">Winter: Schnee und Weihnachten</option>
                </select>
            </section>

            <div class="admin-savebar">
                <p class="form-error" id="saveError" role="alert" hidden></p>
                <span class="admin-dirty" id="dirtyNote" hidden>Ungespeicherte Änderungen</span>
                <button class="primary-button" id="saveButton" type="submit">Einstellungen speichern</button>
            </div>
        </form>

        <section class="admin-panel" id="usersPanel">
            <div class="admin-panel-head">
                <h2 class="section-title"><span class="accent">Benutzer</span> &amp; Admins</h2>
                <button class="primary-button" id="createUserButton" type="button">＋ Benutzer anlegen</button>
            </div>
            <p class="admin-help">Konten anlegen, bearbeiten, Passwörter zurücksetzen und Autorenrechte vergeben.</p>
            <div class="admin-user-list" id="adminUserList"><p class="widget-loading">Wird geladen …</p></div>

            <form id="adminNamesForm" class="admin-names">
                <label for="adminPlayerNames">Admins (Spielernamen)</label>
                <p class="admin-help">Jeder Name muss zu einem Spieler mit Account gehören. Mehrere Namen mit Komma oder in einzelnen Zeilen.</p>
                <textarea id="adminPlayerNames" required></textarea>
                <div class="admin-names-actions">
                    <button class="secondary-button" type="submit">Admins speichern</button>
                </div>
            </form>
        </section>
    </div>

</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>

<dialog id="adminUserDialog">
    <form class="modal-content form-stack" id="adminUserForm" method="dialog">
        <div>
            <h2 class="modal-title" id="adminUserDialogTitle">Benutzer anlegen</h2>
            <p class="modal-subtitle" id="adminUserDialogSubtitle">Der Benutzer muss das vorläufige Passwort nach der ersten Anmeldung ändern.</p>
        </div>
        <input type="hidden" id="adminUserId">
        <div>
            <label for="adminUsername">Benutzername</label>
            <input type="text" id="adminUsername" autocomplete="off" required>
        </div>
        <div>
            <label for="adminUserPlayerName">Spielername</label>
            <input type="text" id="adminUserPlayerName" maxlength="40" autocomplete="off" required>
        </div>
        <div id="adminUserSteamRow">
            <label for="adminUserSteamName">Steam-Name (optional)</label>
            <input type="text" id="adminUserSteamName" maxlength="120" autocomplete="off" placeholder="Profilname, SteamID oder Profil-Link">
        </div>
        <div class="form-row">
            <div>
                <label for="adminUserPassword" id="adminUserPasswordLabel">Vorläufiges Passwort</label>
                <input type="password" id="adminUserPassword" minlength="8" autocomplete="new-password">
            </div>
            <div>
                <label for="adminUserPasswordConfirmation">Passwort wiederholen</label>
                <input type="password" id="adminUserPasswordConfirmation" minlength="8" autocomplete="new-password">
            </div>
        </div>
        <p class="field-help" id="adminPasswordHelp"></p>
        <label class="remember-row">
            <input type="checkbox" id="adminUserIsAuthor">
            <span>Autorenrecht — darf Blogbeiträge schreiben und bearbeiten</span>
        </label>
        <p class="form-error" id="adminUserError" role="alert" hidden></p>
        <div class="modal-actions">
            <button class="danger-button" id="deleteUserButton" type="button" hidden>Benutzer löschen</button>
            <button class="secondary-button" type="button" id="cancelUserButton">Abbrechen</button>
            <button class="primary-button" type="submit">Speichern</button>
        </div>
    </form>
</dialog>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
    const byId = (id) => document.getElementById(id);
    const state = { csrf: '', settings: null, features: null, netzje: null, dirty: false };

    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined && text !== null) node.textContent = String(text);
        return node;
    }

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
                body: JSON.stringify({ action, ...payload }),
            };
        const response = await fetch(payload === null ? `api.php?action=${encodeURIComponent(action)}` : 'api.php', options);
        let data = null;
        try { data = await response.json(); } catch (error) { data = null; }
        if (!response.ok || !data || data.ok === false) {
            throw new Error((data && data.error) || 'Die Anfrage ist fehlgeschlagen.');
        }
        if (data.csrf_token) state.csrf = data.csrf_token;
        return data;
    }

    function setDirty(value) {
        state.dirty = value;
        byId('dirtyNote').hidden = !value;
    }

    // ===== Einstellungen =====
    function renderSettings() {
        const { labels } = state.settings;
        const features = state.features;

        const list = byId('sidebarList');
        list.replaceChildren();
        features.sidebar.forEach((entry, index) => {
            const li = el('li', 'admin-order-item');
            const label = el('label', 'admin-switch');
            const input = el('input');
            input.type = 'checkbox';
            input.checked = entry.enabled;
            input.addEventListener('change', () => { entry.enabled = input.checked; setDirty(true); });
            label.append(input, el('span', '', labels.sidebar[entry.box] || entry.box));
            const pageOff = features.pages[entry.box] === false;
            if (pageOff) label.append(el('small', 'admin-muted', 'Seite ist ausgeblendet'));
            const moves = el('div', 'admin-moves');
            const move = (delta, text, aria) => {
                const btn = el('button', 'secondary-button admin-move', text);
                btn.type = 'button';
                btn.setAttribute('aria-label', `${labels.sidebar[entry.box]} ${aria}`);
                // „Gerade online“ bleibt immer oben: weder verschiebbar noch überholbar
                const pinnedTop = features.sidebar[0] && features.sidebar[0].box === 'now' ? 1 : 0;
                btn.disabled = entry.box === 'now' || index + delta < pinnedTop || index + delta >= features.sidebar.length;
                btn.addEventListener('click', () => {
                    const [item] = features.sidebar.splice(index, 1);
                    features.sidebar.splice(index + delta, 0, item);
                    setDirty(true);
                    renderSettings();
                });
                return btn;
            };
            if (entry.box === 'now') label.append(el('small', 'admin-muted', 'immer oben, nur wenn jemand spielt'));
            moves.append(move(-1, '↑', 'nach oben'), move(1, '↓', 'nach unten'));
            li.append(el('span', 'admin-order-number', index + 1), label, moves);
            list.append(li);
        });

        const switches = (containerId, group) => {
            const container = byId(containerId);
            container.replaceChildren();
            for (const [key, text] of Object.entries(labels[group])) {
                const label = el('label', 'admin-switch');
                const input = el('input');
                input.type = 'checkbox';
                input.checked = features[group][key] !== false;
                input.addEventListener('change', () => {
                    features[group][key] = input.checked;
                    setDirty(true);
                    if (group === 'pages') renderSettings();
                });
                label.append(input, el('span', '', group === 'pages' ? `${text} im Menü zeigen` : text));
                container.append(label);
            }
        };
        switches('pageSwitches', 'pages');
        switches('homeSwitches', 'home');
    }

    byId('themeSelect').addEventListener('change', () => setDirty(true));

    byId('featuresForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        const save = byId('saveButton');
        const error = byId('saveError');
        error.hidden = true;
        save.disabled = true;
        try {
            state.settings = await api('admin_save_features', { features: state.features, theme: byId('themeSelect').value });
            state.features = structuredClone(state.settings.features);
            renderSettings();
            setDirty(false);
            showToast('Gespeichert. Menü und Seitenleiste ändern sich beim nächsten Seitenaufruf.');
        } catch (failure) {
            error.textContent = failure.message;
            error.hidden = false;
        } finally {
            save.disabled = false;
        }
    });

    window.addEventListener('beforeunload', (event) => {
        if (state.dirty) { event.preventDefault(); event.returnValue = ''; }
    });

    // ===== Netzje-Anfragen =====
    function renderNetzje() {
        const box = byId('netzjeRequests');
        const data = state.netzje;
        const party = data && data.party;
        box.replaceChildren();
        if (!party || !party.start) {
            box.append(el('p', 'admin-help', 'Gerade ist keine Netzje geplant.'));
            return;
        }
        const summary = el('p', 'admin-netzje-summary');
        summary.append(el('strong', '', party.title || 'Netzje'), ' · ');
        summary.append(party.seats > 0 ? `${party.seats_taken} von ${party.seats} Plätzen bestätigt` : 'Plätze noch nicht festgelegt (unter „Party bearbeiten“)');
        box.append(summary);

        if (party.requests.length === 0) {
            box.append(el('p', 'admin-help', 'Keine offenen Anfragen.'));
            return;
        }
        const list = el('ul', 'admin-request-list');
        for (const person of party.requests) {
            const li = el('li', 'admin-request');
            li.append(el('strong', '', person.name));
            const actions = el('div', 'admin-request-actions');
            const decide = (label, className, decision, message) => {
                const btn = el('button', className, label);
                btn.type = 'button';
                btn.addEventListener('click', async () => {
                    btn.disabled = true;
                    try {
                        state.netzje = await api('admin_netzje_request', { user_id: person.id, decision });
                        renderNetzje();
                        showToast(message);
                    } catch (failure) {
                        showToast(failure.message);
                        btn.disabled = false;
                    }
                });
                return btn;
            };
            actions.append(
                decide('Bestätigen', 'primary-button', 'approve', `${person.name} ist bestätigt.`),
                decide('Ablehnen', 'danger-button', 'decline', `Anfrage von ${person.name} abgelehnt.`),
            );
            li.append(actions);
            list.append(li);
        }
        box.append(list);
    }

    // ===== Benutzer & Admins =====
    const userDialog = byId('adminUserDialog');

    // Gleiche Regeln wie beim Registrieren (index.php); der Server prüft ohnehin selbst.
    function passwordValidationMessage(password, confirmation = null) {
        const characters = [...password];
        const hasLetter = characters.some((c) => /^\p{L}$/u.test(c));
        const hasNumber = characters.some((c) => /^\p{N}$/u.test(c));
        const hasSpecial = characters.some((c) => !/^\p{L}$/u.test(c) && !/^\p{N}$/u.test(c) && !/^\s$/u.test(c));
        if (characters.length < 8) return 'Das Passwort muss mindestens 8 Zeichen lang sein.';
        if (!hasLetter) return 'Das Passwort muss mindestens einen Buchstaben enthalten.';
        if (!hasNumber) return 'Das Passwort muss mindestens eine Zahl enthalten.';
        if (!hasSpecial) return 'Das Passwort muss mindestens ein Sonderzeichen wie !, ?, #, +, -, _, @ oder € enthalten. Umlaute gelten als Buchstaben.';
        if (confirmation !== null && password !== confirmation) return 'Die beiden Passwörter stimmen nicht überein.';
        return '';
    }

    function applyUsers(data) {
        state.admin = data.admin || null;
        if (!data.auth || !data.auth.is_admin) {
            // Eigene Adminrechte entzogen: Seite neu laden zeigt „Nur für Admins“
            window.location.reload();
            return;
        }
        renderUsers();
    }

    function renderUsers() {
        const list = byId('adminUserList');
        list.replaceChildren();
        const users = (state.admin && state.admin.users) || [];
        if (users.length === 0) list.append(el('p', 'admin-help', 'Noch keine Benutzer.'));
        for (const user of users) {
            const card = el('div', 'admin-user-card');
            const info = el('div');
            const title = el('strong', '', user.username);
            if (user.is_admin) title.append(el('span', 'account-badge admin', 'Admin'));
            else if (user.is_author) title.append(el('span', 'account-badge', 'Autor'));
            info.append(title, el('small', '', `Spieler: ${user.player_name || 'nicht zugeordnet'}${user.must_change_password ? ' · Passwortänderung offen' : ''}`));
            const edit = el('button', 'secondary-button', 'Bearbeiten');
            edit.type = 'button';
            edit.addEventListener('click', () => openUserDialog(user));
            card.append(info, edit);
            list.append(card);
        }
        byId('adminPlayerNames').value = ((state.admin && state.admin.admin_player_names) || []).join('\n');
    }

    function openUserDialog(user = null) {
        const editMode = Boolean(user);
        byId('adminUserForm').reset();
        byId('adminUserError').hidden = true;
        byId('adminUserId').value = user ? user.id : '';
        byId('adminUsername').value = user ? user.username : '';
        byId('adminUserPlayerName').value = user ? user.player_name : '';
        byId('adminUserIsAuthor').checked = Boolean(user && user.is_author);
        byId('adminUserSteamName').value = user ? user.steam_profile || '' : '';
        // Den Steam-Namen trägt beim Anlegen der Spieler selbst ein
        byId('adminUserSteamRow').hidden = !editMode;
        byId('adminUserDialogTitle').textContent = editMode ? 'Benutzer bearbeiten' : 'Benutzer anlegen';
        byId('adminUserDialogSubtitle').textContent = editMode
            ? 'Benutzername und Spielerzuordnung ändern oder ein vorläufiges neues Passwort setzen.'
            : 'Der Benutzer muss das vorläufige Passwort nach der ersten Anmeldung ändern.';
        byId('adminUserPasswordLabel').textContent = editMode ? 'Neues Passwort (optional)' : 'Vorläufiges Passwort';
        byId('adminPasswordHelp').textContent = editMode
            ? 'Bleibt das Passwortfeld leer, bleibt das bisherige Passwort. Beim Zurücksetzen gelten keine Komplexitätsregeln – der Benutzer muss beim nächsten Login ein eigenes, regelkonformes Passwort festlegen.'
            : 'Mindestens 8 Zeichen mit Buchstabe, Zahl und Sonderzeichen (z. B. !, ?, #, +, -, _, @, €). Der Benutzer ändert es nach der ersten Anmeldung.';
        for (const id of ['adminUserPassword', 'adminUserPasswordConfirmation']) {
            byId(id).required = !editMode;
            byId(id).minLength = editMode ? 0 : 8;
        }
        byId('deleteUserButton').hidden = !editMode;
        userDialog.showModal();
        requestAnimationFrame(() => byId('adminUsername').focus());
    }

    function userError(message) {
        byId('adminUserError').textContent = message;
        byId('adminUserError').hidden = false;
    }

    byId('createUserButton').addEventListener('click', () => openUserDialog());
    byId('cancelUserButton').addEventListener('click', () => userDialog.close());

    byId('adminUserForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        const editMode = Boolean(byId('adminUserId').value);
        const password = byId('adminUserPassword').value;
        const confirmation = byId('adminUserPasswordConfirmation').value;
        if (!editMode) {
            const problem = passwordValidationMessage(password, confirmation);
            if (problem) return userError(problem);
        } else if (password !== '' || confirmation !== '') {
            if (password === '') return userError('Bitte ein Passwort festlegen.');
            if (password !== confirmation) return userError('Die beiden Passwörter stimmen nicht überein.');
        }
        try {
            const data = await api(editMode ? 'admin_update_user' : 'admin_create_user', {
                user_id: byId('adminUserId').value || undefined,
                username: byId('adminUsername').value,
                player_name: byId('adminUserPlayerName').value,
                is_author: byId('adminUserIsAuthor').checked,
                steam_name: editMode ? byId('adminUserSteamName').value : undefined,
                password,
                password_confirmation: confirmation,
            });
            userDialog.close();
            applyUsers(data);
            showToast(editMode ? 'Benutzer wurde geändert.' : 'Benutzer wurde angelegt.');
        } catch (failure) {
            userError(failure.message);
        }
    });

    byId('deleteUserButton').addEventListener('click', async () => {
        const userId = Number(byId('adminUserId').value);
        const user = ((state.admin && state.admin.users) || []).find((item) => Number(item.id) === userId);
        if (!user || !confirm(`Benutzer „${user.username}“ wirklich löschen? Der Spieler und seine bisherigen Kalenderdaten bleiben erhalten.`)) return;
        try {
            const data = await api('admin_delete_user', { user_id: userId });
            userDialog.close();
            applyUsers(data);
            showToast('Benutzer wurde gelöscht.');
        } catch (failure) {
            userError(failure.message);
        }
    });

    byId('adminNamesForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        const names = byId('adminPlayerNames').value.split(/[,;\n]+/).map((name) => name.trim()).filter(Boolean);
        try {
            // Style unverändert lassen: admin_save_settings verlangt ihn mit
            const data = await api('admin_save_settings', { admin_player_names: names, theme: state.settings.theme });
            applyUsers(data);
            showToast('Admins gespeichert.');
        } catch (failure) {
            showToast(failure.message);
        }
    });

    // ===== Laden =====
    async function load() {
        const gate = byId('adminGate');
        try {
            state.settings = await api('site_settings');
        } catch (failure) {
            gate.replaceChildren(el('p', 'form-error', failure.message));
            return;
        }
        if (!state.settings.is_admin) {
            gate.replaceChildren();
            gate.append(el('h2', 'section-title', 'Nur für Admins'));
            const text = el('p', 'admin-help', state.settings.logged_in
                ? 'Dein Konto hat keine Adminrechte.'
                : 'Bitte melde dich im Kalender mit einem Admin-Konto an und öffne diese Seite dann erneut.');
            gate.append(text);
            const link = el('a', 'primary-button', 'Zum Kalender');
            link.href = 'index.php';
            gate.append(link);
            return;
        }
        gate.hidden = true;
        byId('adminContent').hidden = false;
        state.features = structuredClone(state.settings.features);
        byId('themeSelect').value = state.settings.theme;
        renderSettings();
        try {
            state.netzje = await api('netzje');
        } catch (failure) {
            state.netzje = null;
        }
        renderNetzje();
        try {
            applyUsers(await api('bootstrap'));
        } catch (failure) {
            byId('adminUserList').replaceChildren(el('p', 'form-error', failure.message));
        }
    }

    load();
</script>
</body>
</html>
