<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" id="themeColorMeta" content="#1b1f27">
    <meta name="application-name" content="Kellerkinder">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Kellerkinder">
    <title>Blog — Kellerkinder</title>
    <meta name="description" content="Spielerlebnisse und News der Kellerkinder.">
    <link rel="icon" href="assets/kellerkinder-logo.svg?v=<?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png?v=<?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="alternate" type="application/rss+xml" title="Kellerkinder Blog" href="feed.php">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Montserrat:wght@500;600;700;800;900&family=Open+Sans:wght@400;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/includes/styles.php'; ?>
    <?php require __DIR__ . '/includes/blog-styles.php'; ?>
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
$activeNav = 'blog';
$pageKicker = 'Blog';
$pageTitle = 'Aus dem Keller';
$pageLead = 'Spielerlebnisse, Erfolge und News der Kellerkinder.';
$showInstall = false;
require __DIR__ . '/includes/site-header.php';
?>

<main class="page-shell">

    <div id="storageWarning" class="storage-warning" hidden></div>

    <div class="blog-layout">
        <section class="blog-panel">
            <div class="blog-head">
                <h2 class="section-title"><span class="accent">Neueste</span> Beiträge</h2>
                <div class="blog-head-actions">
                    <a class="secondary-button" href="feed.php" title="Blog als RSS abonnieren">RSS abonnieren</a>
                    <button class="primary-button" id="newPostButton" type="button" hidden>＋ Neuer Beitrag</button>
                </div>
            </div>

            <div class="filter-row post-tags" id="tagFilter" aria-label="Nach Thema filtern">
                <span class="widget-loading">Wird geladen …</span>
            </div>

            <p id="activeFilter" class="post-meta" hidden></p>
            <div id="postList" class="post-list">
                <p class="widget-loading">Wird geladen …</p>
            </div>
        </section>
    </div>

</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>

<dialog id="editorDialog" class="wide">
    <div class="modal-content form-stack">
        <h2 class="modal-title" id="editorTitle">Neuer Beitrag</h2>

        <div>
            <label for="postTitle">Titel</label>
            <input type="text" id="postTitle" maxlength="120" placeholder="Worum geht es?">
        </div>

        <div>
            <label for="postTags">Tags (mit Komma trennen, max. 8)</label>
            <input type="text" id="postTags" placeholder="z. B. Mythic+, Raid, Season 3">
        </div>

        <div>
            <label for="editorSurface">Inhalt</label>
            <div class="editor-toolbar" role="toolbar" aria-label="Formatierung">
                <button type="button" data-cmd="bold" title="Fett"><b>B</b></button>
                <button type="button" data-cmd="italic" title="Kursiv"><i>I</i></button>
                <button type="button" data-cmd="underline" title="Unterstrichen"><u>U</u></button>
                <button type="button" data-cmd="strikeThrough" title="Durchgestrichen"><s>S</s></button>
                <span class="sep"></span>
                <button type="button" data-block="h2" title="Überschrift">H2</button>
                <button type="button" data-block="h3" title="Zwischenüberschrift">H3</button>
                <button type="button" data-block="p" title="Normaler Text">¶</button>
                <span class="sep"></span>
                <button type="button" data-cmd="insertUnorderedList" title="Aufzählung">• Liste</button>
                <button type="button" data-cmd="insertOrderedList" title="Nummerierte Liste">1. Liste</button>
                <button type="button" data-block="blockquote" title="Zitat">❝</button>
                <span class="sep"></span>
                <button type="button" id="btnLink" title="Link einfügen">🔗 Link</button>
                <button type="button" id="btnImage" title="Bild hochladen">🖼 Bild</button>
                <button type="button" id="btnYoutube" title="YouTube-Video einbetten">▶ YouTube</button>
                <span class="sep"></span>
                <button type="button" data-cmd="removeFormat" title="Formatierung entfernen">✗</button>
            </div>
            <div class="editor-surface" id="editorSurface" contenteditable="true"
                 data-placeholder="Schreib hier deinen Beitrag …"></div>
            <p class="editor-hint">Bilder über „Bild“, per Einfügen (Strg+V) oder Hineinziehen – sie werden automatisch auf maximal 1600 Pixel Breite verkleinert. Über „Bild“ kannst du die Anzeigebreite anpassen.</p>
        </div>

        <label class="remember-row">
            <input type="checkbox" id="postDraft">
            <span>Als Entwurf speichern (noch nicht öffentlich sichtbar)</span>
        </label>

        <div class="modal-actions">
            <button class="danger-button" id="deletePostButton" type="button" hidden>Löschen</button>
            <button class="secondary-button" type="button" data-close-dialog="editorDialog">Abbrechen</button>
            <button class="primary-button" id="savePostButton" type="button">Veröffentlichen</button>
        </div>
    </div>
</dialog>

<input type="file" id="imageInput" accept="image/png,image/jpeg,image/gif,image/webp" hidden>
<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
    const byId = id => document.getElementById(id);
    const state = { csrf: '', auth: { logged_in: false, is_author: false, is_admin: false, user: null }, posts: [], tags: [], activeTag: '', editingId: 0 };

    let toastTimer = null;
    function showToast(message) {
        const toast = byId('toast');
        // Ein offenes Dialogfenster liegt über allem: den Hinweis dort hinein, sonst bleibt er unsichtbar
        const host = document.querySelector('dialog[open]') || document.body;
        if (toast.parentElement !== host) host.append(toast);
        toast.textContent = message;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 4200);
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
            const error = new Error(data.error || 'Die Aktion ist fehlgeschlagen.');
            error.status = response.status;
            throw error;
        }
        return data;
    }

    function formatDate(iso) {
        const date = new Date(iso);
        if (Number.isNaN(date.getTime())) return '';
        return date.toLocaleDateString('de-DE', { day: '2-digit', month: 'long', year: 'numeric' });
    }

    function renderTags() {
        const container = byId('tagFilter');
        container.replaceChildren();
        // Ohne Themen bleibt die Filterreihe ganz weg
        container.hidden = state.tags.length === 0;
        if (state.tags.length === 0) return;

        const all = document.createElement('button');
        all.type = 'button';
        all.className = 'tag-chip' + (state.activeTag === '' ? ' active' : '');
        all.textContent = 'Alle';
        all.addEventListener('click', () => selectTag(''));
        container.appendChild(all);

        for (const tag of state.tags) {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'tag-chip' + (state.activeTag.toLowerCase() === tag.label.toLowerCase() ? ' active' : '');
            chip.textContent = tag.label;
            const count = document.createElement('span');
            count.className = 'tag-count';
            count.textContent = tag.count;
            chip.appendChild(count);
            chip.addEventListener('click', () => selectTag(tag.label));
            container.appendChild(chip);
        }
    }

    function renderPosts() {
        const list = byId('postList');
        list.replaceChildren();

        const filterLine = byId('activeFilter');
        if (state.activeTag) {
            filterLine.hidden = false;
            filterLine.textContent = `Gefiltert nach „${state.activeTag}“ — ${state.posts.length} Beitrag${state.posts.length === 1 ? '' : 'e'}`;
        } else {
            filterLine.hidden = true;
        }

        if (state.posts.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'widget-empty';
            empty.textContent = state.activeTag
                ? 'Zu diesem Thema gibt es noch keine Beiträge.'
                : 'Es wurde noch kein Beitrag geschrieben.';
            list.appendChild(empty);
            return;
        }

        for (const post of state.posts) {
            const card = document.createElement('article');
            card.className = 'post-card';
            card.id = `post-${post.id}`;

            const heading = document.createElement('h2');
            heading.textContent = post.title;
            card.appendChild(heading);

            const meta = document.createElement('p');
            meta.className = 'post-meta';
            meta.textContent = `${formatDate(post.created_at)} · ${post.author_name}`;
            if (post.status === 'draft') {
                const flag = document.createElement('span');
                flag.className = 'draft-flag';
                flag.textContent = 'Entwurf';
                meta.appendChild(flag);
            }
            card.appendChild(meta);

            const body = document.createElement('div');
            body.className = 'post-body';
            // Inhalt wurde serverseitig gefiltert (Whitelist), siehe sanitizeBlogHtml().
            body.innerHTML = post.content_html;
            card.appendChild(body);

            if (post.tags.length > 0) {
                const tagRow = document.createElement('div');
                tagRow.className = 'post-tags';
                for (const tag of post.tags) {
                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = 'tag-chip';
                    chip.textContent = tag;
                    chip.addEventListener('click', () => selectTag(tag));
                    tagRow.appendChild(chip);
                }
                card.appendChild(tagRow);
            }

            const canEdit = state.auth.is_admin
                || (state.auth.user && post.author_user_id === state.auth.user.id);
            if (canEdit) {
                const actions = document.createElement('div');
                actions.className = 'post-tags';
                const edit = document.createElement('button');
                edit.type = 'button';
                edit.className = 'secondary-button';
                edit.textContent = 'Bearbeiten';
                edit.addEventListener('click', () => openEditor(post));
                actions.appendChild(edit);
                card.appendChild(actions);
            }

            list.appendChild(card);
        }

        // Sprungmarke aus der Seitenleiste („Neu im Blog“): erst nach dem Laden vorhanden
        if (window.location.hash.startsWith('#post-')) {
            const target = document.getElementById(window.location.hash.slice(1));
            if (target) target.scrollIntoView({ block: 'start' });
        }
    }

    function selectTag(tag) {
        state.activeTag = tag;
        const url = new URL(window.location.href);
        if (tag) url.searchParams.set('tag', tag); else url.searchParams.delete('tag');
        window.history.replaceState({}, '', url);
        loadPosts();
    }

    async function loadPosts() {
        try {
            const data = await api('blog_posts', null, state.activeTag ? { tag: state.activeTag } : null);
            state.posts = data.posts || [];
            state.tags = data.tags || [];
            renderPosts();
            renderTags();
        } catch (error) {
            byId('postList').innerHTML = '<p class="widget-empty">Die Beiträge konnten nicht geladen werden.</p>';
            showToast(error.message);
        }
    }

    async function loadAuth() {
        try {
            const data = await api('bootstrap');
            state.csrf = data.csrf_token || '';
            state.auth = data.auth || state.auth;
            byId('newPostButton').hidden = !state.auth.is_author;
        } catch { /* Blog bleibt auch ohne Anmeldung lesbar. */ }
    }

    // ===== Editor =====
    const editorDialog = byId('editorDialog');
    const surface = byId('editorSurface');

    function openEditor(post = null) {
        state.editingId = post ? post.id : 0;
        byId('editorTitle').textContent = post ? 'Beitrag bearbeiten' : 'Neuer Beitrag';
        byId('postTitle').value = post ? post.title : '';
        byId('postTags').value = post ? post.tags.join(', ') : '';
        byId('postDraft').checked = post ? post.status === 'draft' : false;
        surface.innerHTML = post ? post.content_html : '';
        byId('deletePostButton').hidden = !post;
        editorDialog.showModal();
        requestAnimationFrame(() => byId('postTitle').focus());
    }

    function runCommand(command) {
        surface.focus();
        document.execCommand(command, false, undefined);
    }

    function applyBlock(tag) {
        surface.focus();
        document.execCommand('formatBlock', false, tag);
    }

    for (const button of document.querySelectorAll('.editor-toolbar [data-cmd]')) {
        button.addEventListener('click', () => runCommand(button.dataset.cmd));
    }
    for (const button of document.querySelectorAll('.editor-toolbar [data-block]')) {
        button.addEventListener('click', () => applyBlock(button.dataset.block));
    }

    byId('btnLink').addEventListener('click', () => {
        const url = window.prompt('Link-Adresse (mit https:// beginnen):', 'https://');
        if (!url) return;
        if (!/^https?:\/\//i.test(url)) {
            showToast('Der Link muss mit http:// oder https:// beginnen.');
            return;
        }
        surface.focus();
        document.execCommand('createLink', false, url);
    });

    function youtubeId(url) {
        const patterns = [
            /(?:youtube\.com\/watch\?(?:.*&)?v=)([A-Za-z0-9_-]{6,20})/,
            /(?:youtu\.be\/)([A-Za-z0-9_-]{6,20})/,
            /(?:youtube\.com\/(?:embed|shorts)\/)([A-Za-z0-9_-]{6,20})/
        ];
        for (const pattern of patterns) {
            const match = url.match(pattern);
            if (match) return match[1];
        }
        return null;
    }

    byId('btnYoutube').addEventListener('click', () => {
        const url = window.prompt('YouTube-Link einfügen:', 'https://');
        if (!url) return;
        const id = youtubeId(url);
        if (!id) {
            showToast('Das sieht nicht nach einem YouTube-Link aus.');
            return;
        }
        const anchor = document.createElement('a');
        anchor.className = 'yt-embed';
        anchor.href = `https://www.youtube.com/watch?v=${id}`;
        const thumb = document.createElement('img');
        thumb.src = `https://i.ytimg.com/vi/${id}/hqdefault.jpg`;
        thumb.alt = 'YouTube-Video ansehen';
        const play = document.createElement('span');
        play.className = 'yt-embed-play';
        play.textContent = '▶';
        anchor.append(thumb, play);
        insertNode(anchor);
    });

    function insertNode(node) {
        surface.focus();
        const selection = window.getSelection();
        if (selection && selection.rangeCount > 0 && surface.contains(selection.anchorNode)) {
            const range = selection.getRangeAt(0);
            range.deleteContents();
            range.insertNode(node);
            range.setStartAfter(node);
            selection.removeAllRanges();
            selection.addRange(range);
        } else {
            surface.appendChild(node);
        }
        surface.appendChild(document.createElement('br'));
    }

    // ===== Bilder: Bild-Knopf, Einfügen aus der Zwischenablage, Hineinziehen =====
    // Bilder werden im Browser auf höchstens 1600 px Breite verkleinert (Handyfotos sind sonst zu groß
    // für den Upload) und dann hochgeladen; im Beitrag steht nur noch die Adresse.
    // Über FileReader + data-URL, weil die Content-Security-Policy blob:-Bilder blockiert.
    const BLOG_IMAGE_MAX_WIDTH = 1600;

    function readAsDataUrl(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(String(reader.result || ''));
            reader.onerror = () => reject(new Error('Das Bild konnte nicht gelesen werden.'));
            reader.readAsDataURL(file);
        });
    }

    function loadImage(src) {
        return new Promise((resolve, reject) => {
            const image = new Image();
            image.onload = () => resolve(image);
            image.onerror = () => reject(new Error('Das Bild konnte nicht geöffnet werden (nur PNG, JPEG, GIF oder WebP).'));
            image.src = src;
        });
    }

    /** Verkleinert ein Bild (data-URL) und gibt eine neue data-URL zurück; GIFs bleiben, damit sie animiert bleiben. */
    async function shrinkDataUrl(dataUrl) {
        if (/^data:image\/gif;/.test(dataUrl) && dataUrl.length < 7_500_000) return dataUrl;
        const image = await loadImage(dataUrl);
        const scale = Math.min(1, BLOG_IMAGE_MAX_WIDTH / image.naturalWidth);
        // Kleine PNGs (z. B. mit Transparenz) bleiben PNG; alles andere wird JPEG
        if (/^data:image\/png;/.test(dataUrl) && scale === 1 && dataUrl.length < 2_000_000) return dataUrl;
        // Unter ~700 KB bleiben: Viele Server nehmen standardmäßig nur 1 MB pro Anfrage an.
        // Erst die Qualität senken, dann notfalls die Größe.
        const MAX_LENGTH = 950_000;
        let width = Math.max(1, Math.round(image.naturalWidth * scale));
        let result = '';
        for (let attempt = 0; attempt < 6; attempt++) {
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = Math.max(1, Math.round(image.naturalHeight * (width / image.naturalWidth)));
            const context = canvas.getContext('2d');
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, canvas.width, canvas.height);
            context.drawImage(image, 0, 0, canvas.width, canvas.height);
            for (const quality of [0.86, 0.74, 0.62]) {
                result = canvas.toDataURL('image/jpeg', quality);
                if (result.length <= MAX_LENGTH) return result;
            }
            width = Math.max(1, Math.round(width * 0.75));
        }
        return result;
    }

    async function uploadDataUrl(dataUrl) {
        const result = await api('blog_upload_image', { image: await shrinkDataUrl(dataUrl) });
        return result.url;
    }

    async function insertImageFile(file, askWidth) {
        if (!/^image\/(png|jpeg|gif|webp)$/.test(file.type)) {
            showToast('Nur PNG-, JPEG-, GIF- oder WebP-Bilder.');
            return;
        }
        if (file.size > 30_000_000) {
            showToast('Das Bild ist zu groß (über 30 MB).');
            return;
        }
        showToast('Bild wird hochgeladen …');
        try {
            const url = await uploadDataUrl(await readAsDataUrl(file));
            const image = document.createElement('img');
            image.src = url;
            image.alt = '';
            insertNode(image);
            showToast('Bild eingefügt.');
            if (askWidth) {
                const width = window.prompt('Anzeigebreite in Pixeln (leer lassen für volle Breite):', '');
                if (width && /^\d+$/.test(width)) image.setAttribute('width', width);
            }
        } catch (error) {
            showToast(error.message);
        }
    }

    function imageFiles(dataTransfer) {
        return [...((dataTransfer && dataTransfer.files) || [])].filter(file => file.type.startsWith('image/'));
    }

    byId('btnImage').addEventListener('click', () => byId('imageInput').click());

    byId('imageInput').addEventListener('change', async event => {
        const file = event.target.files && event.target.files[0];
        event.target.value = '';
        if (file) await insertImageFile(file, true);
    });

    // Einfügen (Strg+V) bzw. Hineinziehen von Bildern: hochladen statt als riesige data-URL in den Text
    surface.addEventListener('paste', async event => {
        const files = imageFiles(event.clipboardData);
        if (files.length === 0) return;
        event.preventDefault();
        for (const file of files) await insertImageFile(file, false);
    });
    surface.addEventListener('drop', async event => {
        const files = imageFiles(event.dataTransfer);
        if (files.length === 0) return;
        event.preventDefault();
        for (const file of files) await insertImageFile(file, false);
    });

    /** Vor dem Speichern: noch eingebettete Bilder (data-URL) hochladen; fremde Bild-Adressen melden. */
    async function uploadEmbeddedImages() {
        let foreign = 0;
        for (const image of [...surface.querySelectorAll('img')]) {
            const src = image.getAttribute('src') || '';
            if (src.startsWith('data:image/')) {
                showToast('Bild wird hochgeladen …');
                image.setAttribute('src', await uploadDataUrl(src));
            } else if (/^https?:/i.test(src) && !/^https:\/\/i\.ytimg\.com\//.test(src)) {
                foreign++;
            }
        }
        return foreign;
    }

    byId('savePostButton').addEventListener('click', async () => {
        const title = byId('postTitle').value.trim();
        if (!title) {
            showToast('Bitte gib dem Beitrag einen Titel.');
            byId('postTitle').focus();
            return;
        }
        const tags = byId('postTags').value.split(',').map(tag => tag.trim()).filter(Boolean);
        const button = byId('savePostButton');
        button.disabled = true;
        try {
            const foreign = await uploadEmbeddedImages();
            if (foreign > 0 && !window.confirm(`${foreign === 1 ? 'Ein Bild stammt' : `${foreign} Bilder stammen`} von einer anderen Website und ${foreign === 1 ? 'wird' : 'werden'} nicht übernommen. Bitte das Bild speichern und über „Bild“ hochladen. Trotzdem ohne ${foreign === 1 ? 'dieses Bild' : 'diese Bilder'} speichern?`)) return;
            await api('blog_save_post', {
                id: state.editingId,
                title,
                content_html: surface.innerHTML,
                tags,
                status: byId('postDraft').checked ? 'draft' : 'published'
            });
            editorDialog.close();
            showToast('Beitrag gespeichert.');
            await loadPosts();
        } catch (error) {
            showToast(error.message);
        } finally {
            button.disabled = false;
        }
    });

    byId('deletePostButton').addEventListener('click', async () => {
        if (!state.editingId) return;
        if (!window.confirm('Diesen Beitrag wirklich löschen?')) return;
        try {
            await api('blog_delete_post', { id: state.editingId });
            editorDialog.close();
            showToast('Beitrag gelöscht.');
            await loadPosts();
        } catch (error) {
            showToast(error.message);
        }
    });

    byId('newPostButton').addEventListener('click', () => openEditor(null));

    for (const button of document.querySelectorAll('[data-close-dialog]')) {
        button.addEventListener('click', () => byId(button.dataset.closeDialog).close());
    }
    editorDialog.addEventListener('click', event => {
        if (event.target === editorDialog) editorDialog.close();
    });

    state.activeTag = new URL(window.location.href).searchParams.get('tag') || '';

    (async () => {
        await loadAuth();
        await loadPosts();
    })();

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('./service-worker.js').catch(() => {});
        });
    }
</script>
</body>
</html>
