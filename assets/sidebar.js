// Seitenleiste „Kurz & knapp“ (includes/site-footer.php): füllt die Kästen
// Meist gespielt, Nächste Netzje, Neu im Blog und Unsere Server über die
// öffentlichen API-Aktionen. Kästen ohne Daten bleiben versteckt, den Kasten
// der aktuellen Seite (data-skip) laden wir gar nicht erst.
// Texte werden nur per textContent eingesetzt, nie als HTML.
(function () {
    'use strict';

    const sidebar = document.getElementById('siteSidebar');
    if (!sidebar) return;
    const skip = sidebar.dataset.skip || '';

    const box = (name) => sidebar.querySelector(`[data-box="${name}"]`);

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
        const attendees = Array.isArray(party.attendees) ? party.attendees.length : 0;
        const games = Array.isArray(data.slots) ? data.slots.filter((slot) => slot.status !== 'proposed').length : 0;
        section.querySelector('[data-meta]').textContent = `${attendees} ${attendees === 1 ? 'Zusage' : 'Zusagen'} · ${games} ${games === 1 ? 'Spiel' : 'Spiele'} geplant`;
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

    const loaders = { games: loadGames, netzje: loadNetzje, blog: loadBlog, servers: loadServers };
    for (const [name, loader] of Object.entries(loaders)) {
        if (name !== skip) loader();
    }
})();
