<?php
declare(strict_types=1);
// Admin-Unterseite „Heroes of the Storm – Builds“: Helden verwalten und je Held eigene Builds
// hinterlegen (Titel, Notiz, Import-Text aus dem Spiel). Der Discord-Bot gibt sie mit /build aus.
// Daten: api.php?action=hots_builds (lesen, öffentlich), admin_hots_save_hero / admin_hots_delete_hero /
// admin_hots_save_build / admin_hots_delete_build / admin_hots_move_build (speichern, nur Admins).
// Nur Admins sehen die Bedienung; die API prüft die Rechte bei jedem Speichern selbst.
require __DIR__ . '/includes/bootstrap.php';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1c1f25">
    <meta name="robots" content="noindex">
    <title>Heroes-Builds (Admin) — Kellerkinder</title>
    <link rel="icon" href="assets/kellerkinder-logo.svg?v=<?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png?v=<?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Montserrat:wght@500;600;700;800;900&family=Open+Sans:wght@400;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/includes/styles.php'; ?>
    <?php require __DIR__ . '/includes/arena-styles.php'; ?>
    <?php require __DIR__ . '/includes/navi-styles.php'; ?>
    <?php require __DIR__ . '/includes/hots-admin-styles.php'; ?>
</head>
<body data-theme="default">

<?php
$activeNav = 'admin';
$pageKicker = 'Admin';
$pageTitle = 'Heroes of the Storm – Builds';
$pageLead = 'Deine eigenen Builds je Held. Der Discord-Bot gibt sie mit /build aus, wenn jemand nach dem Helden fragt.';
$showInstall = false;
require __DIR__ . '/includes/site-header.php';
?>

<main class="page-shell">

    <div id="adminGate" class="admin-panel">
        <p class="widget-loading">Wird geladen …</p>
    </div>

    <div id="adminContent" hidden>
        <p class="hots-back"><a href="admin.php">← Zurück zum Adminbereich</a></p>

        <div class="hots-layout">
            <section class="admin-panel" aria-label="Helden">
                <div class="admin-panel-head">
                    <h2 class="section-title"><span class="accent">Helden</span> <span class="admin-muted" id="heroCount"></span></h2>
                    <button class="secondary-button" id="addHeroButton" type="button">＋ Held</button>
                </div>
                <div class="hots-search">
                    <label for="heroSearch" class="admin-muted">Suchen (deutsch oder englisch)</label>
                    <input type="search" id="heroSearch" autocomplete="off" placeholder="z. B. Kerrigan, Graumähne, etc">
                    <label class="admin-switch"><input type="checkbox" id="onlyWithBuilds"><span>Nur Helden mit Builds</span></label>
                </div>
                <ul class="hots-hero-list" id="heroList"></ul>
            </section>

            <section class="admin-panel" id="detailPanel" aria-live="polite"></section>
        </div>
    </div>

</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>

<dialog id="buildDialog">
    <form class="modal-content form-stack" id="buildForm" method="dialog">
        <div>
            <h2 class="modal-title" id="buildDialogTitle">Build hinzufügen</h2>
            <p class="modal-subtitle" id="buildDialogSubtitle"></p>
        </div>
        <input type="hidden" id="buildId">
        <div>
            <label for="buildTitle">Name des Builds</label>
            <input type="text" id="buildTitle" maxlength="60" autocomplete="off" placeholder="z. B. Allround" required>
        </div>
        <div>
            <label for="buildCode">Import-Text aus dem Spiel</label>
            <input type="text" id="buildCode" maxlength="60" autocomplete="off" spellcheck="false" placeholder="[T2222324,Kerrigan]" required>
            <p class="field-help">Im Spiel den Build kopieren und hier einfügen. Anführungszeichen drumherum sind egal.</p>
        </div>
        <div>
            <label for="buildNote">Notiz (optional)</label>
            <textarea id="buildNote" maxlength="200" rows="3" placeholder="z. B. Gut gegen viele Gegner mit Betäubung"></textarea>
        </div>
        <p class="form-error" id="buildError" role="alert" hidden></p>
        <div class="modal-actions">
            <button class="secondary-button" type="button" id="cancelBuildButton">Abbrechen</button>
            <button class="primary-button" type="submit">Speichern</button>
        </div>
    </form>
</dialog>

<dialog id="heroDialog">
    <form class="modal-content form-stack" id="heroForm" method="dialog">
        <div>
            <h2 class="modal-title" id="heroDialogTitle">Held hinzufügen</h2>
            <p class="modal-subtitle">Zum Ergänzen neuer Helden oder zum Pflegen von Namen und Suchbegriffen.</p>
        </div>
        <input type="hidden" id="heroId">
        <div>
            <label for="heroName">Name (englisch, wie im Spiel)</label>
            <input type="text" id="heroName" maxlength="40" autocomplete="off" required>
        </div>
        <div>
            <label for="heroNameDe">Deutscher Name (optional)</label>
            <input type="text" id="heroNameDe" maxlength="40" autocomplete="off">
            <p class="field-help">Erscheint im Discord statt des englischen Namens. Leer lassen, wenn er gleich ist.</p>
        </div>
        <div>
            <label for="heroImportName">Name im Import-Text</label>
            <input type="text" id="heroImportName" maxlength="30" autocomplete="off" spellcheck="false">
            <p class="field-help">Nur Buchstaben und Ziffern, ohne Leerzeichen, so wie im kopierten Text, z. B. <code>ETC</code>, <code>Lucio</code>, <code>LostVikings</code>. Leer = aus dem Namen abgeleitet.</p>
        </div>
        <div>
            <label for="heroAliases">Weitere Suchbegriffe (optional)</label>
            <textarea id="heroAliases" rows="3" placeholder="Mit Komma oder in einzelnen Zeilen, z. B. Graumähne"></textarea>
            <p class="field-help">Damit findet auch ein anderer Name den Helden, in der Suche und im Discord.</p>
        </div>
        <p class="form-error" id="heroError" role="alert" hidden></p>
        <div class="modal-actions">
            <button class="danger-button" id="deleteHeroButton" type="button" hidden>Held löschen</button>
            <button class="secondary-button" type="button" id="cancelHeroButton">Abbrechen</button>
            <button class="primary-button" type="submit">Speichern</button>
        </div>
    </form>
</dialog>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
    const byId = (id) => document.getElementById(id);
    const state = { csrf: '', heroes: [], selectedId: null, filter: '', onlyWithBuilds: false };
    const CODE_PATTERN = /^\[T[0-9]{7},[A-Za-z0-9]{1,30}\]$/;

    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined && text !== null) node.textContent = String(text);
        return node;
    }

    let toastTimer = null;
    function showToast(message, duration = 3200) {
        const toast = byId('toast');
        // Ein offenes Dialogfenster liegt über allem: den Hinweis dort hinein, sonst bleibt er unsichtbar
        const host = document.querySelector('dialog[open]') || document.body;
        if (toast.parentElement !== host) host.append(toast);
        toast.textContent = message;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), duration);
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

    // Suche ohne Groß-/Kleinschreibung, Akzente und Satzzeichen ("E.T.C." = "etc", "Graumähne" = "graumahne")
    function fold(text) {
        return String(text || '').toLowerCase().replace(/ß/g, 'ss').normalize('NFKD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9]+/g, '');
    }

    const heroById = (id) => state.heroes.find((hero) => hero.id === id) || null;
    const heroTitle = (hero) => hero.name_de || hero.name;

    function applyData(data) {
        state.heroes = [...data.heroes].sort((a, b) => heroTitle(a).localeCompare(heroTitle(b), 'de'));
        if (state.selectedId !== null && !heroById(state.selectedId)) state.selectedId = null;
        render();
    }

    // ===== Heldenliste =====
    function visibleHeroes() {
        const needle = fold(state.filter);
        return state.heroes.filter((hero) => {
            if (state.onlyWithBuilds && hero.builds.length === 0) return false;
            if (needle === '') return true;
            return [hero.name, hero.name_de, hero.import_name, ...hero.aliases].some((text) => fold(text).includes(needle));
        });
    }

    function renderList() {
        const list = byId('heroList');
        list.replaceChildren();
        const heroes = visibleHeroes();
        const withBuilds = state.heroes.filter((hero) => hero.builds.length > 0).length;
        byId('heroCount').textContent = `${heroes.length} von ${state.heroes.length} · ${withBuilds} mit Builds`;
        if (heroes.length === 0) {
            list.append(el('li', 'hots-empty', 'Kein Held gefunden.'));
            return;
        }
        for (const hero of heroes) {
            const item = el('li');
            const button = el('button', 'hots-hero');
            button.type = 'button';
            if (hero.id === state.selectedId) button.setAttribute('aria-current', 'true');
            const name = el('span', 'hots-hero-name', heroTitle(hero));
            if (hero.name_de && hero.name_de !== hero.name) name.append(el('small', null, hero.name));
            button.append(name, el('span', `hots-count${hero.builds.length > 0 ? ' has' : ''}`, hero.builds.length));
            button.addEventListener('click', () => { state.selectedId = hero.id; render(); });
            item.append(button);
            list.append(item);
        }
    }

    // ===== Builds des gewählten Helden =====
    async function copyText(text) {
        try {
            await navigator.clipboard.writeText(text);
            showToast('Kopiert.');
        } catch (error) {
            showToast('Kopieren nicht möglich, bitte den Text markieren.');
        }
    }

    async function run(action, payload, success) {
        try {
            const data = await api(action, payload);
            applyData(data);
            if (success) showToast(success);
            return data;
        } catch (error) {
            showToast(error.message, 4500);
            return null;
        }
    }

    function renderDetail() {
        const panel = byId('detailPanel');
        panel.replaceChildren();
        const hero = heroById(state.selectedId);
        if (!hero) {
            panel.append(el('h2', 'section-title', 'Builds'));
            panel.append(el('p', 'hots-empty', 'Wähle links einen Helden, um seine Builds zu sehen und neue anzulegen.'));
            return;
        }

        const head = el('div', 'hots-detail-head');
        const title = el('h2', 'section-title');
        title.append(el('span', 'accent', heroTitle(hero)));
        const actions = el('div', 'hots-detail-actions');
        const addButton = el('button', 'primary-button', '＋ Build hinzufügen');
        addButton.type = 'button';
        addButton.addEventListener('click', () => openBuildDialog(hero, null));
        const editButton = el('button', 'secondary-button', 'Heldendaten');
        editButton.type = 'button';
        editButton.addEventListener('click', () => openHeroDialog(hero));
        actions.append(addButton, editButton);
        head.append(title, actions);
        panel.append(head);

        const meta = [];
        if (hero.name_de && hero.name_de !== hero.name) meta.push(`Englisch: ${hero.name}`);
        meta.push(`Import-Name: ${hero.import_name}`);
        panel.append(el('p', 'hots-meta', meta.join(' · ')));

        if (hero.builds.length === 0) {
            panel.append(el('p', 'hots-empty', 'Für diesen Helden ist noch kein Build hinterlegt. Der Bot antwortet dann „noch keine Builds“.'));
            return;
        }

        const list = el('ol', 'hots-builds');
        hero.builds.forEach((build, index) => {
            const item = el('li', 'hots-build');
            item.append(el('h3', 'hots-build-title', build.title));
            if (build.note) item.append(el('p', 'hots-build-note', build.note));

            const codeRow = el('div', 'hots-code-row');
            codeRow.append(el('code', 'hots-code', build.code));
            const copy = el('button', 'secondary-button', 'Kopieren');
            copy.type = 'button';
            copy.addEventListener('click', () => copyText(build.code));
            codeRow.append(copy);
            item.append(codeRow);

            const controls = el('div', 'hots-build-actions');
            const mk = (label, aria, handler, disabled = false, className = 'secondary-button') => {
                const button = el('button', className, label);
                button.type = 'button';
                button.setAttribute('aria-label', aria);
                button.disabled = disabled;
                button.addEventListener('click', handler);
                return button;
            };
            controls.append(
                mk('↑', `${build.title} nach oben`, () => run('admin_hots_move_build', { hero_id: hero.id, id: build.id, direction: 'up' }), index === 0),
                mk('↓', `${build.title} nach unten`, () => run('admin_hots_move_build', { hero_id: hero.id, id: build.id, direction: 'down' }), index === hero.builds.length - 1),
                mk('Bearbeiten', `${build.title} bearbeiten`, () => openBuildDialog(hero, build)),
                mk('Löschen', `${build.title} löschen`, async () => {
                    if (!window.confirm(`Build „${build.title}“ von ${heroTitle(hero)} löschen?`)) return;
                    await run('admin_hots_delete_build', { hero_id: hero.id, id: build.id }, 'Build gelöscht.');
                }, false, 'danger-button'),
            );
            item.append(controls);
            list.append(item);
        });
        panel.append(list);
    }

    function render() {
        renderList();
        renderDetail();
    }

    // ===== Dialog: Build =====
    const buildDialog = byId('buildDialog');
    let buildHeroId = null;

    function setError(id, message) {
        const node = byId(id);
        node.textContent = message || '';
        node.hidden = !message;
    }

    function openBuildDialog(hero, build) {
        buildHeroId = hero.id;
        byId('buildDialogTitle').textContent = build ? 'Build bearbeiten' : 'Build hinzufügen';
        byId('buildDialogSubtitle').textContent = heroTitle(hero);
        byId('buildId').value = build ? build.id : '';
        byId('buildTitle').value = build ? build.title : '';
        byId('buildCode').value = build ? build.code : '';
        byId('buildNote').value = build ? build.note : '';
        setError('buildError', '');
        buildDialog.showModal();
        byId(build ? 'buildTitle' : 'buildCode').focus();
    }

    byId('cancelBuildButton').addEventListener('click', () => buildDialog.close());
    byId('buildForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        const code = byId('buildCode').value.trim().replace(/^["'`„“”‚‘’\s]+|["'`„“”‚‘’\s]+$/g, '');
        if (!CODE_PATTERN.test(code)) {
            setError('buildError', 'Der Import-Text sieht nicht richtig aus. Er muss so aussehen wie im Spiel kopiert, z. B. [T2222324,Kerrigan].');
            return;
        }
        const submit = event.submitter || byId('buildForm').querySelector('button[type="submit"]');
        submit.disabled = true;
        try {
            const data = await api('admin_hots_save_build', {
                hero_id: buildHeroId,
                id: Number(byId('buildId').value) || 0,
                title: byId('buildTitle').value,
                code,
                note: byId('buildNote').value,
            });
            buildDialog.close();
            applyData(data);
            showToast(data.warning ? `Gespeichert. Hinweis: ${data.warning}` : 'Build gespeichert.', data.warning ? 9000 : 3200);
        } catch (error) {
            setError('buildError', error.message);
        } finally {
            submit.disabled = false;
        }
    });

    // ===== Dialog: Held =====
    const heroDialog = byId('heroDialog');

    function openHeroDialog(hero) {
        byId('heroDialogTitle').textContent = hero ? 'Heldendaten bearbeiten' : 'Held hinzufügen';
        byId('heroId').value = hero ? hero.id : '';
        byId('heroName').value = hero ? hero.name : '';
        byId('heroNameDe').value = hero ? hero.name_de : '';
        byId('heroImportName').value = hero ? hero.import_name : '';
        byId('heroAliases').value = hero ? hero.aliases.join(', ') : '';
        byId('deleteHeroButton').hidden = !hero;
        setError('heroError', '');
        heroDialog.showModal();
        byId('heroName').focus();
    }

    byId('addHeroButton').addEventListener('click', () => openHeroDialog(null));
    byId('cancelHeroButton').addEventListener('click', () => heroDialog.close());
    byId('heroForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        const submit = event.submitter || byId('heroForm').querySelector('button[type="submit"]');
        submit.disabled = true;
        try {
            const data = await api('admin_hots_save_hero', {
                id: Number(byId('heroId').value) || 0,
                name: byId('heroName').value,
                name_de: byId('heroNameDe').value,
                import_name: byId('heroImportName').value,
                aliases: byId('heroAliases').value,
            });
            state.selectedId = data.id;
            heroDialog.close();
            applyData(data);
            showToast('Held gespeichert.');
        } catch (error) {
            setError('heroError', error.message);
        } finally {
            submit.disabled = false;
        }
    });
    byId('deleteHeroButton').addEventListener('click', async () => {
        const hero = heroById(Number(byId('heroId').value));
        if (!hero) return;
        const extra = hero.builds.length > 0 ? ` Seine ${hero.builds.length} Builds gehen dabei verloren.` : '';
        if (!window.confirm(`Held „${heroTitle(hero)}“ löschen?${extra}`)) return;
        heroDialog.close();
        state.selectedId = null;
        await run('admin_hots_delete_hero', { id: hero.id }, 'Held gelöscht.');
    });

    for (const dialog of [buildDialog, heroDialog]) {
        // Klick auf den abgedunkelten Rand schließt den Dialog
        dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
    }

    byId('heroSearch').addEventListener('input', (event) => { state.filter = event.target.value; renderList(); });
    byId('onlyWithBuilds').addEventListener('change', (event) => { state.onlyWithBuilds = event.target.checked; renderList(); });

    // ===== Laden =====
    async function load() {
        const gate = byId('adminGate');
        let settings;
        try {
            settings = await api('site_settings');
        } catch (failure) {
            gate.replaceChildren(el('p', 'form-error', failure.message));
            return;
        }
        if (!settings.is_admin) {
            gate.replaceChildren();
            gate.append(el('h2', 'section-title', 'Nur für Admins'));
            gate.append(el('p', 'admin-help', settings.logged_in
                ? 'Dein Konto hat keine Adminrechte.'
                : 'Bitte melde dich im Kalender mit einem Admin-Konto an und öffne diese Seite dann erneut.'));
            const link = el('a', 'primary-button', 'Zum Kalender');
            link.href = 'kalender.php#anmelden';
            gate.append(link);
            return;
        }
        gate.hidden = true;
        byId('adminContent').hidden = false;
        try {
            applyData(await api('hots_builds'));
        } catch (failure) {
            byId('detailPanel').replaceChildren(el('p', 'form-error', failure.message));
        }
    }

    load();
</script>
</body>
</html>
