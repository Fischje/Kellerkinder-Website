<?php
declare(strict_types=1);
// Startseite „Home“: Text des Admins (bearbeitbar mit dem gemeinsamen Editor, Vorlage: Überblick über
// die Menüpunkte) und darunter „Neu im Blog“ mit den drei neuesten Beiträgen.
// Daten: api.php?action=home_page (lesen), admin_save_home (speichern, nur Admins), blog_posts.
// Der Kalender liegt seit 2.36.0 auf kalender.php; alte Links wie index.php#anmelden leiten dorthin.
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/features.php';
$h = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
$showBlogTeasers = siteFeatures()['home']['blog_teaser'] && featurePageEnabled('blog');
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
    <title>Kellerkinder — Online-Gaming mit Freu(n)den</title>
    <meta name="description" content="Die Kellerkinder: Kalender, Blog, Spiele-Statistik, Gameserver und LAN-Partys.">
    <link rel="icon" href="assets/kellerkinder-logo.svg?v=<?= $h($appVersion) ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png?v=<?= $h($appVersion) ?>">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="alternate" type="application/rss+xml" title="Kellerkinder Blog" href="feed.php">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Montserrat:wght@500;600;700;800;900&family=Open+Sans:wght@400;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/includes/styles.php'; ?>
    <?php require __DIR__ . '/includes/blog-styles.php'; ?>
    <?php require __DIR__ . '/includes/arena-styles.php'; ?>
    <?php require __DIR__ . '/includes/navi-styles.php'; ?>
    <style>
        .home-text { margin: 0; }
        .home-text > :first-child { margin-top: 0; }
        .home-admin { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin: 22px 0 0; }
        .home-admin[hidden] { display: none; }
        .home-admin small { color: var(--muted); }
        .home-blog { margin-top: 44px; }
        .home-blog[hidden] { display: none; }
        .home-blog-list { display: grid; gap: 10px; }
        .home-blog-list .blog-teaser { margin: 0; }
    </style>
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
<script nonce="<?= $h($cspNonce) ?>">
    // Früher lag hier der Kalender: alte Links und Lesezeichen (Anmelden, Konto, …) dorthin weiterleiten
    if (/^#(anmelden|registrieren|account|benutzer|konto)$/.test(window.location.hash)) {
        window.location.replace('kalender.php' + window.location.hash);
    }
</script>

<?php
$activeNav = 'home';
$pageKicker = 'Home';
$pageTitle = 'Willkommen im Keller';
$pageLead = 'Online-Gaming mit Freu(n)den – schön, dass du da bist.';
$showInstall = false;
require __DIR__ . '/includes/site-header.php';
?>

<main class="page-shell">
    <article class="home-text post-body" id="homeText"><p class="widget-loading">Wird geladen …</p></article>

    <div class="home-admin" id="homeAdmin" hidden>
        <button class="secondary-button" id="editHomeButton" type="button">Text bearbeiten</button>
        <small id="homeTemplateNote" hidden>Das ist noch die Vorlage – passe sie gern an.</small>
    </div>

    <?php if ($showBlogTeasers): ?>
    <section class="home-blog" id="homeBlog" hidden aria-labelledby="homeBlogTitle">
        <h2 class="section-title" id="homeBlogTitle"><span class="accent">Neu</span> im Blog</h2>
        <div class="home-blog-list" id="homeBlogList"></div>
    </section>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>

<dialog id="homeEditorDialog" class="wide">
    <div class="modal-content form-stack">
        <h2 class="modal-title">Startseite bearbeiten</h2>
        <div>
            <label for="homeEditorSurface">Text</label>
            <?php $richEditorId = 'homeEditorSurface'; $richEditorPlaceholder = 'Schreib hier den Text der Startseite …'; require __DIR__ . '/includes/rich-editor.php'; ?>
        </div>
        <div class="modal-actions">
            <button class="danger-button" id="resetHomeButton" type="button">Vorlage wiederherstellen</button>
            <button class="secondary-button" type="button" id="cancelHomeButton">Abbrechen</button>
            <button class="primary-button" id="saveHomeButton" type="button">Speichern</button>
        </div>
    </div>
</dialog>

<div class="toast" id="toast" role="status" aria-live="polite"></div>
<script src="assets/rich-editor.js?v=<?= $h($appVersion) ?>"></script>

<script nonce="<?= $h($cspNonce) ?>">
    const byId = (id) => document.getElementById(id);
    const state = { csrf: '', home: null };

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
        try { data = await response.json(); } catch { data = null; }
        if (!response.ok || !data || data.ok === false) {
            throw new Error((data && data.error) || 'Die Aktion ist fehlgeschlagen.');
        }
        if (data.csrf_token) state.csrf = data.csrf_token;
        return data;
    }

    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined && text !== null) node.textContent = String(text);
        return node;
    }

    // ===== Text =====
    function renderHome() {
        const home = state.home;
        // Inhalt wurde serverseitig gefiltert (gleiche Whitelist wie im Blog, sanitizeBlogHtml())
        byId('homeText').innerHTML = home.html;
        byId('homeAdmin').hidden = !home.can_edit;
        byId('homeTemplateNote').hidden = !home.is_template;
    }

    async function loadHome() {
        try {
            state.home = await api('home_page');
            renderHome();
        } catch (error) {
            byId('homeText').replaceChildren(el('p', 'widget-empty', error.message));
        }
    }

    // ===== Bearbeiten (nur Admins) =====
    const dialog = byId('homeEditorDialog');
    const editor = kkRichEditor({ root: dialog.querySelector('[data-rich-editor]'), api, showToast });

    byId('editHomeButton').addEventListener('click', () => {
        editor.setHtml(state.home.html);
        dialog.showModal();
        requestAnimationFrame(() => editor.surface.focus());
    });
    byId('cancelHomeButton').addEventListener('click', () => dialog.close());

    byId('saveHomeButton').addEventListener('click', async () => {
        const button = byId('saveHomeButton');
        button.disabled = true;
        try {
            if (!await editor.prepareForSave()) return;
            state.home = await api('admin_save_home', { html: editor.getHtml() });
            renderHome();
            dialog.close();
            showToast('Startseite gespeichert.');
        } catch (error) {
            showToast(error.message);
        } finally {
            button.disabled = false;
        }
    });

    byId('resetHomeButton').addEventListener('click', async () => {
        if (!confirm('Den eigenen Text verwerfen und wieder die Vorlage (Überblick über die Menüpunkte) zeigen?')) return;
        try {
            state.home = await api('admin_save_home', { reset: true });
            renderHome();
            dialog.close();
            showToast('Vorlage wiederhergestellt.');
        } catch (error) {
            showToast(error.message);
        }
    });

    // ===== Neu im Blog: die drei neuesten Beiträge, Klick springt zum Beitrag im Blog =====
    async function loadBlogTeasers() {
        const list = byId('homeBlogList');
        if (!list) return;
        try {
            const data = await api('blog_posts');
            const posts = (data.posts || []).slice(0, 3);
            if (posts.length === 0) return;
            list.replaceChildren(...posts.map((post, index) => {
                const link = el('a', 'blog-teaser');
                link.href = `blog.php#post-${encodeURIComponent(post.id)}`;
                const published = new Date(post.created_at);
                const date = Number.isNaN(published.getTime()) ? '' : `${published.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' })} · `;
                link.append(
                    el('span', 'blog-teaser-label', index === 0 ? 'Neu im Blog' : 'Auch im Blog'),
                    el('span', 'blog-teaser-title', post.title),
                    el('span', 'blog-teaser-meta', date + post.author_name),
                    el('span', 'blog-teaser-arrow', '→'),
                );
                link.lastChild.setAttribute('aria-hidden', 'true');
                return link;
            }));
            byId('homeBlog').hidden = false;
        } catch { /* Ohne Blog bleibt der Abschnitt ausgeblendet. */ }
    }

    loadHome();
    loadBlogTeasers();

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('./service-worker.js').catch(() => {});
        });
    }
</script>
</body>
</html>
