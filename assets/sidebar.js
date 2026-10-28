// Seitenleiste „Kurz & knapp“ (includes/site-footer.php): füllt die Kästen
// Meist gespielt, Neu im Blog, Unsere Server und Nächste Netzje über die
// öffentlichen API-Aktionen. Welche Kästen auf welcher Seite stehen, legt
// site-footer.php fest (Schalter auf admin.php); Kästen ohne Daten bleiben versteckt.
// Außerdem auf allen Seiten: Account-Knopf in der Kopfleiste (Anmelden bzw. Account-Menü
// mit „Mein Account“, „Adminbereich“, „Abmelden“) und für Admins der Menüpunkt „Admin“
// (mit Zahl offener Netzje-Anfragen) sowie Menüpunkte nur für Angemeldete (Eindrücke). Die Dialoge für Anmelden und Account liegen im
// Kalender (kalender.php); dort stellt kalender.php window.kkAccount bereit und öffnet sie direkt,
// auf anderen Seiten führen die Links zu kalender.php#anmelden bzw. #account.
// Texte werden nur per textContent eingesetzt, nie als HTML.
(function () {
    'use strict';

    const sidebar = document.getElementById('siteSidebar');

    const box = (name) => (sidebar ? sidebar.querySelector(`[data-box="${name}"]`) : null);

    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined && text !== null) node.textContent = String(text);
        return node;
    }

    async function getJson(query) {
        try {
            const response = await fetch(`api.php?action=${query}`, { headers: { Accept: 'application/json' }, credentials: 'same-origin' });
            if (!response.ok) return null;
            const data = await response.json();
            return data && data.ok !== false ? data : null;
        } catch (error) {
            return null;
        }
    }

    function show(name) {
        const section = box(name);
        if (section) section.hidden = false;
        sidebar.classList.add('has-content');
    }

    // Eine Listenzeile wie bei NAVIs „Our Channels“: Bild, Titel, kleine Zeile darunter.
    function row({ href, image, placeholder, title, sub, dot }) {
        const li = el('li');
        const link = el(href ? 'a' : 'div', 'sidebar-row');
        if (href) link.href = href;
        if (image) {
            const img = el('img', 'sidebar-row-media');
            img.src = image;
            img.alt = '';
            img.loading = 'lazy';
            img.addEventListener('error', () => img.replaceWith(el('span', 'sidebar-row-media placeholder', placeholder || '🎮')));
            link.appendChild(img);
        } else if (placeholder) {
            link.appendChild(el('span', 'sidebar-row-media placeholder', placeholder));
        }
        const text = el('span', 'sidebar-row-text');
        text.appendChild(el('strong', '', title));
        if (sub) {
            const small = el('small', '', sub);
            if (dot) small.prepend(el('i', `sidebar-dot ${dot}`));
            text.appendChild(small);
        }
        link.appendChild(text);
        li.appendChild(link);
        return li;
    }

    function hours(minutes) {
        const value = Math.round((Number(minutes) || 0) / 60);
        return value.toLocaleString('de-DE');
    }

    function berlinDate(value) {
        // Netzje-Zeiten kommen als „YYYY-MM-DDTHH:MM“ in Berliner Zeit
        const [datePart, timePart = '00:00'] = String(value).split('T');
        const [y, m, d] = datePart.split('-').map(Number);
        const [hh, mm] = timePart.split(':').map(Number);
        return new Date(y, m - 1, d, hh, mm);
    }

    async function loadGames() {
        const data = await getJson('playtime_stats&days=30');
        if (!data || !data.configured || !Array.isArray(data.games) || data.games.length === 0) return;
        const section = box('games');
        const list = section.querySelector('[data-list]');
        for (const game of data.games.slice(0, 5)) {
            list.appendChild(row({
                image: game.icon || '',
                placeholder: '🎮',
                title: game.name,
                sub: `${hours(game.minutes)} Std. · ${game.players} Spieler`,
            }));
        }
        if (data.total_minutes > 0) {
            section.querySelector('[data-stat-value]').textContent = hours(data.total_minutes);
            section.querySelector('[data-stat-label]').textContent = 'Stunden gezockt · 30 Tage';
            section.querySelector('[data-stat]').hidden = false;
        }
        show('games');
    }

    async function loadNetzje() {
        const data = await getJson('netzje');
        const party = data && data.party;
        if (!party || !party.start || !party.end) return;
        const start = berlinDate(party.start);
        const end = berlinDate(party.end);
        const now = new Date();
        if (end < now) return;

        const section = box('netzje');
        let countdown = 'Läuft gerade';
        if (start > now) {
            const days = Math.ceil((start - now) / 86400000);
            countdown = days <= 1 ? 'Morgen' : `Noch ${days} Tage`;
            if (start.toDateString() === now.toDateString()) countdown = 'Heute';
        }
        section.querySelector('[data-countdown]').textContent = countdown;
        section.querySelector('[data-title]').textContent = party.title || 'Netzje';
        section.querySelector('[data-when]').textContent = start.toLocaleDateString('de-DE', { weekday: 'short', day: '2-digit', month: '2-digit', year: 'numeric' })
            + ', ' + start.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' }) + ' Uhr';
        const taken = Number(party.seats_taken) || 0;
        const seats = Number(party.seats) || 0;
        const games = Array.isArray(data.slots) ? data.slots.filter((slot) => slot.status !== 'proposed').length : 0;
        const seatText = seats > 0
            ? (taken >= seats ? `Alle ${seats} Plätze vergeben` : `${taken} von ${seats} Plätzen vergeben`)
            : `${taken} ${taken === 1 ? 'Zusage' : 'Zusagen'}`;
        section.querySelector('[data-meta]').textContent = `${seatText} · ${games} ${games === 1 ? 'Spiel' : 'Spiele'} geplant`;
        if (seats > 0) {
            const bar = section.querySelector('[data-seatbar]');
            bar.firstElementChild.style.width = `${Math.min(100, Math.round((taken / seats) * 100))}%`;
            bar.hidden = false;
        }
        show('netzje');
    }

    async function loadBlog() {
        const data = await getJson('blog_posts');
        if (!data || !Array.isArray(data.posts) || data.posts.length === 0) return;
        const posts = data.posts.slice().sort((a, b) => String(b.created_at).localeCompare(String(a.created_at))).slice(0, 3);
        const list = box('blog').querySelector('[data-list]');
        for (const post of posts) {
            const date = new Date(post.created_at);
            list.appendChild(row({
                href: `blog.php#post-${encodeURIComponent(post.id)}`,
                title: post.title,
                sub: `${Number.isNaN(date.getTime()) ? '' : date.toLocaleDateString('de-DE')} · ${post.author_name || ''}`.replace(/ · $/, ''),
            }));
        }
        show('blog');
    }

    async function loadServers() {
        const data = await getJson('gameservers');
        if (!data || !Array.isArray(data.servers) || data.servers.length === 0) return;
        const list = box('servers').querySelector('[data-list]');
        for (const server of data.servers.slice(0, 5)) {
            let sub = server.game || '';
            let dot = '';
            if (server.state === 'online') {
                dot = 'online';
                sub = server.players !== null && server.players !== undefined
                    ? `Online · ${server.players}${server.max_players ? '/' + server.max_players : ''} Spieler`
                    : 'Online';
            } else if (server.state === 'offline') {
                dot = 'offline';
                sub = 'Offline';
            }
            list.appendChild(row({ href: 'gameserver.php', image: server.icon || '', placeholder: '🖥️', title: server.name, sub, dot }));
        }
        show('servers');
    }

    // ===== Account-Knopf & Menüpunkt „Admin“ =====
    let accountCsrf = '';

    // Auf dem Kalender öffnet kalender.php die Dialoge selbst, sonst zum Kalender springen.
    function openAccountDialog(kind, event) {
        if (window.kkAccount && typeof window.kkAccount.open === 'function' && window.kkAccount.open(kind)) {
            if (event) event.preventDefault();
            closeAccountMenu();
        }
    }

    function closeAccountMenu() {
        const toggle = document.getElementById('topbarAccountToggle');
        const menu = document.getElementById('topbarAccountMenu');
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
        if (menu) menu.hidden = true;
    }

    async function logout() {
        try {
            await fetch('api.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-Token': accountCsrf },
                body: JSON.stringify({ action: 'logout' }),
            });
        } finally {
            window.location.reload();
        }
    }

    function renderAccount(data) {
        const container = document.getElementById('topbarAccount');
        if (!container) return;
        container.replaceChildren();
        const user = data && data.user;

        if (!user) {
            const login = el('a', 'topbar-account-button', 'Anmelden');
            login.id = 'topbarLogin';
            login.href = 'kalender.php#anmelden';
            login.addEventListener('click', (event) => openAccountDialog('login', event));
            container.appendChild(login);
            return;
        }

        const toggle = el('button', 'topbar-account-button is-user');
        toggle.type = 'button';
        toggle.id = 'topbarAccountToggle';
        toggle.setAttribute('aria-haspopup', 'true');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-controls', 'topbarAccountMenu');
        if (user.avatar) {
            const img = el('img', 'topbar-avatar');
            img.src = user.avatar;
            img.alt = '';
            toggle.appendChild(img);
        } else {
            toggle.appendChild(el('span', 'topbar-avatar placeholder', (user.name || '?').slice(0, 1).toUpperCase()));
        }
        toggle.appendChild(el('span', 'topbar-account-name', user.name || 'Account'));
        toggle.appendChild(el('span', 'topbar-caret', '▾'));

        const menu = el('ul', 'topbar-menu');
        menu.id = 'topbarAccountMenu';
        menu.hidden = true;
        const item = (label, href, onClick, badge) => {
            const li = el('li');
            const link = el(href ? 'a' : 'button', 'topbar-menu-item', label);
            if (href) link.href = href; else link.type = 'button';
            if (badge) link.appendChild(el('span', 'nav-badge', badge));
            if (onClick) link.addEventListener('click', onClick);
            li.appendChild(link);
            menu.appendChild(li);
        };
        item(user.must_change_password ? 'Passwort ändern' : 'Mein Account', 'kalender.php#account', (event) => openAccountDialog('profile', event));
        if (data.is_admin) item('Adminbereich', 'admin.php', null, Number(data.netzje_requests) || 0);
        item('Abmelden', null, logout);

        toggle.addEventListener('click', () => {
            const open = menu.hidden;
            menu.hidden = !open;
            toggle.setAttribute('aria-expanded', String(open));
        });
        container.append(toggle, menu);
    }

    // Schließen bei Klick daneben oder Escape
    document.addEventListener('click', (event) => {
        const container = document.getElementById('topbarAccount');
        if (container && !container.contains(event.target)) closeAccountMenu();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeAccountMenu();
    });

    function renderAdminLink(data) {
        const link = document.getElementById('navAdminLink');
        if (!link) return;
        const isAdminPage = link.classList.contains('active');
        link.hidden = !(data && data.is_admin) && !isAdminPage;
        const old = link.querySelector('.nav-badge');
        if (old) old.remove();
        const open = data && data.is_admin ? Number(data.netzje_requests) || 0 : 0;
        if (open > 0) {
            const badge = el('span', 'nav-badge', open);
            badge.title = `${open} offene Netzje-${open === 1 ? 'Anfrage' : 'Anfragen'}`;
            link.appendChild(badge);
        }
    }

    async function loadAccount() {
        const data = await getJson('site_settings');
        if (data && data.csrf_token) accountCsrf = data.csrf_token;
        renderAccount(data);
        renderAdminLink(data);
        // Menüpunkte nur für Angemeldete (z. B. „Eindrücke“): Gäste sehen sie gar nicht
        const member = Boolean(data && data.logged_in);
        for (const link of document.querySelectorAll('[data-members-only]')) {
            link.hidden = !member && !link.classList.contains('active');
        }
    }

    // kalender.php ruft das nach Anmelden/Abmelden auf, damit die Kopfleiste stimmt
    window.kkRefreshAccount = loadAccount;

    // „Gerade online“: nur sichtbar, solange jemand spielt; jede Minute neu abfragen
    function sinceText(iso) {
        const minutes = Math.max(0, Math.round((Date.now() - new Date(iso).getTime()) / 60000));
        if (!Number.isFinite(minutes)) return '';
        if (minutes < 1) return 'gerade eben';
        if (minutes < 60) return `seit ${minutes} Min.`;
        const hours = Math.floor(minutes / 60);
        const rest = minutes % 60;
        return `seit ${hours} Std.${rest ? ` ${rest} Min.` : ''}`;
    }

    async function loadNow() {
        const section = box('now');
        if (!section) return;
        const data = await getJson('now_playing');
        const players = data && Array.isArray(data.players) ? data.players : [];
        const list = section.querySelector('[data-list]');
        list.replaceChildren();
        for (const player of players) {
            const games = player.games.map((game) => game.name).join(', ');
            list.appendChild(row({
                href: `spieler.php?name=${encodeURIComponent(player.name)}`,
                image: player.avatar || '',
                placeholder: (player.name || '?').slice(0, 1).toUpperCase(),
                title: player.name,
                sub: `${games} · ${sinceText(player.games[0].since)}`,
                dot: 'online',
            }));
        }
        section.querySelector('[data-count]').textContent = players.length ? `(${players.length})` : '';
        section.hidden = players.length === 0;
        if (players.length) sidebar.classList.add('has-content');
    }

    if (box('now')) {
        loadNow();
        setInterval(() => { if (!document.hidden) loadNow(); }, 60_000);
    }

    const loaders = { games: loadGames, netzje: loadNetzje, blog: loadBlog, servers: loadServers };
    for (const [name, loader] of Object.entries(loaders)) {
        if (box(name)) loader();
    }
    loadAccount();
})();
