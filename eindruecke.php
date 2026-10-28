<?php
declare(strict_types=1);
// Seite „Eindrücke“: Galerie mit Fotos und Clips (YouTube, Twitch) – nur für Angemeldete.
// Gäste sehen den Menüpunkt nicht (assets/sidebar.js blendet ihn nur nach dem Anmelden ein)
// und bekommen hier nur einen Hinweis. Bilder liefert ausschließlich api.php?action=impression_file
// an angemeldete Nutzer aus. Hochladen: Bilder werden im Browser verkleinert (2400 px, Vorschau 640 px).
require __DIR__ . '/includes/bootstrap.php';
$h = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1b1f27">
    <meta name="robots" content="noindex">
    <meta name="application-name" content="Kellerkinder">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Kellerkinder">
    <title>Eindrücke — Kellerkinder</title>
    <link rel="icon" href="assets/kellerkinder-logo.svg?v=<?= $h($appVersion) ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png?v=<?= $h($appVersion) ?>">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Montserrat:wght@500;600;700;800;900&family=Open+Sans:wght@400;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/includes/styles.php'; ?>
    <?php require __DIR__ . '/includes/blog-styles.php'; ?>
    <?php require __DIR__ . '/includes/arena-styles.php'; ?>
    <?php require __DIR__ . '/includes/navi-styles.php'; ?>
    <?php require __DIR__ . '/includes/gallery-styles.php'; ?>
</head>
<body data-theme="default">

<?php
$activeNav = 'impressions';
$pageKicker = 'Nur für Kellerkinder';
$pageTitle = 'Eindrücke';
$pageLead = 'Fotos und Clips von Netzje, Spieleabenden und allem dazwischen.';
$showInstall = false;
require __DIR__ . '/includes/site-header.php';
?>

<main class="page-shell">
    <div id="galleryGate" class="gallery-gate"><p class="widget-loading">Wird geladen …</p></div>

    <section id="galleryContent" hidden>
        <div class="gallery-head">
            <h2 class="section-title"><span class="accent">Unsere</span> Eindrücke</h2>
            <div class="gallery-actions">
                <button class="secondary-button" id="addClipButton" type="button">Clip-Link</button>
                <button class="primary-button" id="addPhotosButton" type="button">＋ Fotos hochladen</button>
            </div>
        </div>
        <div class="filter-row" id="albumFilter" aria-label="Nach Album filtern" hidden></div>
        <div class="gallery-grid" id="galleryGrid"></div>
    </section>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>

<datalist id="albumList"></datalist>

<dialog id="uploadDialog" class="wide">
    <form class="modal-content form-stack" id="uploadForm" method="dialog">
        <h2 class="modal-title">Fotos hochladen</h2>
        <div>
            <label for="uploadFiles">Fotos (mehrere möglich)</label>
            <input type="file" id="uploadFiles" accept="image/png,image/jpeg,image/webp" multiple required>
            <p class="field-help">Große Fotos werden vor dem Hochladen verkleinert.</p>
        </div>
        <div class="form-row">
            <div>
                <label for="uploadAlbum">Album (optional)</label>
                <input type="text" id="uploadAlbum" maxlength="60" list="albumList" placeholder="z. B. Netzje #7">
            </div>
            <div>
                <label for="uploadCaption">Beschreibung (optional)</label>
                <input type="text" id="uploadCaption" maxlength="300" placeholder="gilt für alle gewählten Fotos">
            </div>
        </div>
        <p class="gallery-progress" id="uploadProgress" hidden></p>
        <p class="form-error" id="uploadError" role="alert" hidden></p>
        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="uploadDialog">Abbrechen</button>
            <button class="primary-button" type="submit" id="uploadSubmit">Hochladen</button>
        </div>
    </form>
</dialog>

<dialog id="clipDialog">
    <form class="modal-content form-stack" id="clipForm" method="dialog">
        <h2 class="modal-title">Clip hinzufügen</h2>
        <div>
            <label for="clipUrl">Link zum Clip</label>
            <input type="text" id="clipUrl" maxlength="300" placeholder="YouTube-Link oder Twitch-Clip" required>
        </div>
        <div>
            <label for="clipAlbum">Album (optional)</label>
            <input type="text" id="clipAlbum" maxlength="60" list="albumList">
        </div>
        <div>
            <label for="clipCaption">Beschreibung (optional)</label>
            <input type="text" id="clipCaption" maxlength="300">
        </div>
        <p class="form-error" id="clipError" role="alert" hidden></p>
        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="clipDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Hinzufügen</button>
        </div>
    </form>
</dialog>

<dialog id="viewerDialog" class="gallery-viewer">
    <div class="viewer-stage" id="viewerStage"></div>
    <div class="viewer-bar">
        <div class="viewer-info">
            <strong id="viewerCaption"></strong>
            <small id="viewerMeta"></small>
        </div>
        <div class="viewer-actions">
            <button class="danger-button" id="viewerDelete" type="button" hidden>Löschen</button>
            <button class="secondary-button" id="viewerPrev" type="button" aria-label="Vorheriger Eindruck">‹</button>
            <button class="secondary-button" id="viewerNext" type="button" aria-label="Nächster Eindruck">›</button>
            <button class="primary-button" type="button" data-close-dialog="viewerDialog">Schließen</button>
        </div>
    </div>
</dialog>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script nonce="<?= $h($cspNonce) ?>">
    const byId = (id) => document.getElementById(id);
    const state = { csrf: '', items: [], albums: [], album: '', viewIndex: -1 };

    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined && text !== null) node.textContent = String(text);
        return node;
    }

    let toastTimer = null;
    function showToast(message) {
        const toast = byId('toast');
        // Ein offenes Dialogfenster liegt über allem: den Hinweis dort hinein, sonst bleibt er unsichtbar
        const host = document.querySelector('dialog[open]') || document.body;
        if (toast.parentElement !== host) host.append(toast);
        toast.textContent = message;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 3200);
    }

    async function api(action, payload = null) {
        const options = payload === null
            ? { headers: { Accept: 'application/json' }, credentials: 'same-origin' }
            : { method: 'POST', credentials: 'same-origin', headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-Token': state.csrf }, body: JSON.stringify({ action, ...payload }) };
        const response = await fetch(payload === null ? `api.php?action=${encodeURIComponent(action)}` : 'api.php', options);
        let data = null;
        try { data = await response.json(); } catch (error) { data = null; }
        if (!response.ok || !data || data.ok === false) {
            const failure = new Error((data && data.error) || 'Die Anfrage ist fehlgeschlagen.');
            failure.status = response.status;
            throw failure;
        }
        if (data.csrf_token) state.csrf = data.csrf_token;
        return data;
    }

    const visibleItems = () => state.album ? state.items.filter((item) => item.album === state.album) : state.items;
    const formatDate = (iso) => { const d = new Date(iso); return Number.isNaN(d.getTime()) ? '' : d.toLocaleDateString('de-DE', { day: 'numeric', month: 'long', year: 'numeric' }); };

    function applyData(data) {
        state.items = data.items || [];
        state.albums = data.albums || [];
        if (state.album && !state.albums.some((album) => album.name === state.album)) state.album = '';
        render();
    }

    function render() {
        const filter = byId('albumFilter');
        filter.replaceChildren();
        filter.hidden = state.albums.length === 0;
        const chip = (label, value, count) => {
            const button = el('button', 'tag-chip' + (state.album === value ? ' active' : ''), label);
            button.type = 'button';
            if (count !== undefined) button.append(el('span', 'tag-count', count));
            button.addEventListener('click', () => { state.album = value; render(); });
            return button;
        };
        filter.append(chip('Alle', '', state.items.length));
        for (const album of state.albums) filter.append(chip(album.name, album.name, album.count));
        byId('albumList').replaceChildren(...state.albums.map((album) => { const o = el('option'); o.value = album.name; return o; }));

        const grid = byId('galleryGrid');
        grid.replaceChildren();
        const items = visibleItems();
        if (items.length === 0) {
            grid.append(el('p', 'widget-empty', 'Noch keine Eindrücke. Lade die ersten Fotos hoch!'));
            return;
        }
        items.forEach((item, index) => {
            const tile = el('button', `gallery-tile ${item.type}`);
            tile.type = 'button';
            tile.setAttribute('aria-label', item.caption || (item.type === 'image' ? 'Foto ansehen' : 'Clip ansehen'));
            if (item.type === 'image') {
                const img = el('img');
                img.src = item.thumb;
                img.alt = item.caption || '';
                img.loading = 'lazy';
                tile.append(img);
            } else if (item.type === 'youtube') {
                const img = el('img');
                img.src = `https://i.ytimg.com/vi/${encodeURIComponent(item.ref)}/hqdefault.jpg`;
                img.alt = '';
                img.loading = 'lazy';
                // Vorschaubild nicht erreichbar: schlichte Kachel mit „YouTube“
                img.addEventListener('error', () => img.replaceWith(el('span', 'gallery-clip-label', 'YouTube')));
                tile.append(img, el('span', 'gallery-play', '▶'));
            } else {
                tile.append(el('span', 'gallery-clip-label', 'Twitch-Clip'), el('span', 'gallery-play', '▶'));
            }
            if (item.caption || item.album) tile.append(el('span', 'gallery-caption', item.caption || item.album));
            tile.addEventListener('click', () => openViewer(index));
            grid.append(tile);
        });
    }

    // ===== Großansicht =====
    function openViewer(index) {
        const items = visibleItems();
        if (index < 0 || index >= items.length) return;
        state.viewIndex = index;
        const item = items[index];
        const stage = byId('viewerStage');
        stage.replaceChildren();
        if (item.type === 'image') {
            const img = el('img');
            img.src = item.src;
            img.alt = item.caption || '';
            stage.append(img);
        } else {
            const frame = el('iframe');
            frame.src = item.type === 'youtube'
                ? `https://www.youtube-nocookie.com/embed/${encodeURIComponent(item.ref)}`
                : `https://clips.twitch.tv/embed?clip=${encodeURIComponent(item.ref)}&parent=${encodeURIComponent(location.hostname)}&autoplay=false`;
            frame.allow = 'fullscreen; picture-in-picture';
            frame.allowFullscreen = true;
            frame.title = item.caption || 'Clip';
            stage.append(frame);
        }
        byId('viewerCaption').textContent = item.caption || (item.album || '');
        byId('viewerMeta').textContent = [item.album && item.caption ? item.album : '', item.author, formatDate(item.created_at)].filter(Boolean).join(' · ');
        byId('viewerDelete').hidden = !item.can_delete;
        byId('viewerPrev').disabled = index === 0;
        byId('viewerNext').disabled = index === items.length - 1;
        if (!byId('viewerDialog').open) byId('viewerDialog').showModal();
    }

    byId('viewerPrev').addEventListener('click', () => openViewer(state.viewIndex - 1));
    byId('viewerNext').addEventListener('click', () => openViewer(state.viewIndex + 1));
    byId('viewerDialog').addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') openViewer(state.viewIndex - 1);
        if (event.key === 'ArrowRight') openViewer(state.viewIndex + 1);
    });
    byId('viewerDialog').addEventListener('close', () => byId('viewerStage').replaceChildren());
    // Wischen auf dem Handy
    let touchX = null;
    byId('viewerStage').addEventListener('touchstart', (event) => { touchX = event.touches[0].clientX; }, { passive: true });
    byId('viewerStage').addEventListener('touchend', (event) => {
        if (touchX === null) return;
        const dx = event.changedTouches[0].clientX - touchX;
        touchX = null;
        if (Math.abs(dx) > 50) openViewer(state.viewIndex + (dx < 0 ? 1 : -1));
    });

    byId('viewerDelete').addEventListener('click', async () => {
        const item = visibleItems()[state.viewIndex];
        if (!item || !confirm('Diesen Eindruck wirklich löschen?')) return;
        try {
            applyData(await api('impression_delete', { id: item.id }));
            byId('viewerDialog').close();
            showToast('Gelöscht.');
        } catch (failure) { showToast(failure.message); }
    });

    // ===== Hochladen =====
    // Bild im Browser verkleinern; liefert eine data-URL (JPEG, PNG bleibt PNG)
    // Die Datei als data:-Adresse lesen (blob:-Adressen erlaubt die Content-Security-Policy nicht)
    function readFile(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = () => reject(new Error(`„${file.name}“ konnte nicht gelesen werden.`));
            reader.readAsDataURL(file);
        });
    }

    function shrink(dataUrl, file, maxWidth) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => {
                const scale = Math.min(1, maxWidth / img.naturalWidth);
                const canvas = document.createElement('canvas');
                canvas.width = Math.round(img.naturalWidth * scale);
                canvas.height = Math.round(img.naturalHeight * scale);
                canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
                resolve(canvas.toDataURL(file.type === 'image/png' ? 'image/png' : 'image/jpeg', 0.85));
            };
            img.onerror = () => reject(new Error(`„${file.name}“ ist kein lesbares Bild.`));
            img.src = dataUrl;
        });
    }

    byId('addPhotosButton').addEventListener('click', () => {
        byId('uploadForm').reset();
        byId('uploadAlbum').value = state.album;
        byId('uploadError').hidden = true;
        byId('uploadProgress').hidden = true;
        byId('uploadDialog').showModal();
    });

    byId('uploadForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        const files = [...byId('uploadFiles').files];
        if (files.length === 0) return;
        const submit = byId('uploadSubmit');
        const progress = byId('uploadProgress');
        const error = byId('uploadError');
        submit.disabled = true;
        error.hidden = true;
        progress.hidden = false;
        let done = 0;
        try {
            for (const file of files) {
                progress.textContent = `Lade Foto ${done + 1} von ${files.length} hoch …`;
                const original = await readFile(file);
                const [image, thumb] = await Promise.all([shrink(original, file, 2400), shrink(original, file, 640)]);
                applyData(await api('impression_upload', { image, thumb, album: byId('uploadAlbum').value, caption: byId('uploadCaption').value }));
                done++;
            }
            byId('uploadDialog').close();
            showToast(done === 1 ? 'Foto hochgeladen.' : `${done} Fotos hochgeladen.`);
        } catch (failure) {
            error.textContent = done > 0 ? `${done} hochgeladen, dann: ${failure.message}` : failure.message;
            error.hidden = false;
        } finally {
            submit.disabled = false;
            progress.hidden = true;
        }
    });

    byId('addClipButton').addEventListener('click', () => {
        byId('clipForm').reset();
        byId('clipAlbum').value = state.album;
        byId('clipError').hidden = true;
        byId('clipDialog').showModal();
    });

    byId('clipForm').addEventListener('submit', async (event) => {
        event.preventDefault();
        try {
            applyData(await api('impression_video', { url: byId('clipUrl').value, album: byId('clipAlbum').value, caption: byId('clipCaption').value }));
            byId('clipDialog').close();
            showToast('Clip hinzugefügt.');
        } catch (failure) {
            byId('clipError').textContent = failure.message;
            byId('clipError').hidden = false;
        }
    });

    for (const button of document.querySelectorAll('[data-close-dialog]')) {
        button.addEventListener('click', () => byId(button.dataset.closeDialog).close());
    }

    // ===== Laden =====
    (async () => {
        const gate = byId('galleryGate');
        try {
            applyData(await api('impressions'));
            gate.hidden = true;
            byId('galleryContent').hidden = false;
        } catch (failure) {
            gate.replaceChildren();
            gate.append(el('h2', 'section-title', 'Nur für Kellerkinder'));
            gate.append(el('p', 'admin-help', failure.status === 401
                ? 'Die Eindrücke sehen nur angemeldete Mitglieder. Melde dich an, dann geht’s los.'
                : failure.message));
            const link = el('a', 'primary-button', 'Anmelden');
            link.href = 'index.php#anmelden';
            gate.append(link);
        }
    })();
</script>
</body>
</html>
