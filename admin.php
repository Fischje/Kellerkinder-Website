<?php
declare(strict_types=1);
// Zentrale Admin-Seite: steuert Seitenleiste (Kästen an/aus, Reihenfolge), Menü
// (Seiten ein-/ausblenden), Startseite und den Style für alle. Außerdem die
// offenen Platz-Anfragen der Netzje und der Sprung zur Benutzerverwaltung.
// Daten: api.php?action=site_settings (lesen), admin_save_features (speichern),
// netzje / admin_netzje_request (Anfragen). Nur Admins sehen die Einstellungen;
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
    <link rel="icon" href="assets/kellerkinder-logo.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png">
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

        <section class="admin-panel">
            <h2 class="section-title"><span class="accent">Benutzer</span> &amp; Admins</h2>
            <p class="admin-help">Konten anlegen, Passwörter zurücksetzen und festlegen, wer Admin ist – das geht weiterhin im Kalender.</p>
            <a class="secondary-button" href="index.php#benutzer">Benutzer verwalten</a>
        </section>
    </div>

</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>

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
                btn.disabled = index + delta < 0 || index + delta >= features.sidebar.length;
                btn.addEventListener('click', () => {
                    const [item] = features.sidebar.splice(index, 1);
                    features.sidebar.splice(index + delta, 0, item);
                    setDirty(true);
                    renderSettings();
                });
                return btn;
            };
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
    }

    load();
</script>
</body>
</html>
