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
$pageTitle = 'Neues von den Kellerkindern';
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
            <?php $richEditorId = 'editorSurface'; $richEditorPlaceholder = 'Schreib hier deinen Beitrag …'; require __DIR__ . '/includes/rich-editor.php'; ?>
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

<script src="assets/rich-editor.js?v=<?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?>"></script>
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

    function openEditor(post = null) {
        state.editingId = post ? post.id : 0;
        byId('editorTitle').textContent = post ? 'Beitrag bearbeiten' : 'Neuer Beitrag';
        byId('postTitle').value = post ? post.title : '';
        byId('postTags').value = post ? post.tags.join(', ') : '';
        byId('postDraft').checked = post ? post.status === 'draft' : false;
        editor.setHtml(post ? post.content_html : '');
        byId('deletePostButton').hidden = !post;
        editorDialog.showModal();
        requestAnimationFrame(() => byId('postTitle').focus());
    }

    const editor = kkRichEditor({ root: editorDialog.querySelector('[data-rich-editor]'), api, showToast });

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
            if (!await editor.prepareForSave()) return;
            await api('blog_save_post', {
                id: state.editingId,
                title,
                content_html: editor.getHtml(),
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
