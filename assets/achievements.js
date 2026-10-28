// Erfolge (Rubrik Spiele-Statistik → Spieler): Steam-Erfolge (jüngster Erfolg je Spieler) und – nur wenn
// auf admin.php eingeschaltet – World of Warcraft (M+-Wertungen live von Raider.IO, darunter Links).
// Markup: includes/achievements-section.php ([data-achievements] mit [data-achievements-grid] und dem
// Bearbeiten-Dialog #achievementEditDialog). Holt Anmeldestatus, CSRF und Schalter selbst über site_settings.
// Bis 2.36.0 lag das als Abschnitt „Unsere Erfolge“ im Kalender.
(function () {
    'use strict';

    const root = document.querySelector('[data-achievements]');
    if (!root) return;
    const byId = (id) => document.getElementById(id);
    const ctx = { csrf: '', isAdmin: false, showWow: root.dataset.showWow === '1' };
    const achievementEditDialog = byId('achievementEditDialog');

    async function api(action, payload = null) {
        const options = payload === null
            ? { headers: { Accept: 'application/json' }, credentials: 'same-origin' }
            : {
                method: 'POST',
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-Token': ctx.csrf },
                body: JSON.stringify({ action, ...payload }),
            };
        const response = await fetch(payload === null ? `api.php?action=${encodeURIComponent(action)}` : 'api.php', options);
        let data = null;
        try { data = await response.json(); } catch { data = null; }
        if (!response.ok || !data || data.ok === false) {
            throw new Error((data && data.error) || 'Die Aktion ist fehlgeschlagen.');
        }
        if (data.csrf_token) ctx.csrf = data.csrf_token;
        return data;
    }

    let toastTimer = null;
    function showToast(message) {
        let toast = byId('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'toast';
            toast.id = 'toast';
            toast.setAttribute('role', 'status');
            document.body.append(toast);
        }
        const host = document.querySelector('dialog[open]') || document.body;
        if (toast.parentElement !== host) host.append(toast);
        toast.textContent = message;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 3200);
    }

    const achievementGames = [
        { id: 'steam', label: 'Steam-Erfolge', icon: '🏆', type: 'steam', source: 'Jüngster Erfolg je Spieler' },
        { id: 'wow', label: 'World of Warcraft', icon: '⚔', type: 'wow', source: 'Beste M+-Läufe, live via Raider.IO' },
    ];
    const achievementGamesById = Object.fromEntries(achievementGames.map(game => [game.id, game]));
    let achievementsData = null;
    let steamData = null;

    function statsCardTitle(game) {
        if (game.type === 'wow') return 'M+ Wertungen';
        return game.label;
    }

    function linksCardTitle(game) {
        const data = achievementsData ? achievementsData[game.id] : null;
        return (data && data.links_title) || `${game.label} Links`;
    }

    function formatUpdatedAt(isoString) {
        if (!isoString) return '';
        const date = new Date(isoString);
        if (Number.isNaN(date.getTime())) return '';
        return `Aktualisiert ${date.toLocaleString('de-DE', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })}`;
    }

    function buildWowBody(wow) {
        const container = document.createElement('div');
        container.className = 'mplus-bars';

        if (!wow || !wow.configured) {
            container.innerHTML = '<p class="widget-empty">Noch keine WoW-Charaktere für Raider.IO hinterlegt.</p>';
            return { body: container, updated: '' };
        }
        if (!wow.runs || wow.runs.length === 0) {
            container.innerHTML = '<p class="widget-empty">Für die hinterlegten Charaktere liegen noch keine Season-Läufe vor.</p>';
            return { body: container, updated: formatUpdatedAt(wow.updated_at) };
        }

        const maxScore = Math.max(...wow.runs.map(run => run.score || 0), 1);
        for (const run of wow.runs) {
            const row = document.createElement('div');
            row.className = 'mplus-bar-row';

            const label = document.createElement('div');
            label.className = 'mplus-bar-label';

            const textSpan = document.createElement('span');
            const isSafeProfileLink = typeof run.profile_url === 'string' && /^https:\/\//i.test(run.profile_url);
            const nameNode = document.createElement(isSafeProfileLink ? 'a' : 'span');
            if (isSafeProfileLink) {
                nameNode.href = run.profile_url;
                nameNode.target = '_blank';
                nameNode.rel = 'noopener';
            }
            nameNode.textContent = run.character || '';
            textSpan.appendChild(nameNode);

            if (run.score) {
                const scoreNode = document.createElement('small');
                scoreNode.textContent = ` · Score ${run.score}`;
                textSpan.appendChild(scoreNode);
            }
            textSpan.appendChild(document.createTextNode(` — ${run.dungeon || ''}`));

            const levelNode = document.createElement('b');
            levelNode.textContent = `+${run.level}`;
            levelNode.classList.toggle('top-tier', run.score === maxScore);

            label.append(textSpan, levelNode);

            const track = document.createElement('div');
            track.className = 'mplus-bar-track';
            const fill = document.createElement('div');
            fill.className = 'mplus-bar-fill' + (run.score === maxScore ? ' top-tier' : '');
            fill.style.width = `${Math.max(6, Math.round(((run.score || 0) / maxScore) * 100))}%`;
            track.appendChild(fill);

            row.append(label, track);
            container.appendChild(row);
        }

        return { body: container, updated: formatUpdatedAt(wow.updated_at) };
    }

    function buildLinksBody(data) {
        const list = document.createElement('ul');
        list.className = 'achievement-links';

        const links = (data && data.links) || [];
        if (links.length === 0) {
            list.innerHTML = '<li class="widget-empty">Noch keine Links hinterlegt.</li>';
        } else {
            for (const link of links) {
                const item = document.createElement('li');
                const anchor = document.createElement('a');
                anchor.href = link.url;
                anchor.target = '_blank';
                anchor.rel = 'noopener';
                anchor.textContent = link.label;
                item.appendChild(anchor);
                list.appendChild(item);
            }
        }

        return { body: list, updated: '' };
    }

    function buildAchievementCardShell(game, cardType) {
        const card = document.createElement('article');
        card.className = 'achievement-card';
        card.dataset.widget = `${game.id}-${cardType}`;

        const head = document.createElement('div');
        head.className = 'achievement-card-head';

        const icon = document.createElement('span');
        icon.className = `achievement-icon ${game.id}`;
        icon.setAttribute('aria-hidden', 'true');
        icon.textContent = cardType === 'links' ? '🔗' : game.icon;

        const headText = document.createElement('div');
        headText.className = 'achievement-card-head-text';
        const title = document.createElement('h3');
        title.textContent = cardType === 'links' ? linksCardTitle(game) : statsCardTitle(game);
        const source = document.createElement('p');
        source.className = 'achievement-source';
        source.textContent = cardType === 'links' ? 'Nützliche Links' : game.source;
        headText.append(title, source);

        head.append(icon, headText);

        const canEdit = cardType === 'links' ? ctx.isAdmin : (ctx.isAdmin && game.type === 'manual');
        if (canEdit) {
            const editButton = document.createElement('button');
            editButton.type = 'button';
            editButton.className = 'achievement-edit-button';
            editButton.title = `${game.label} bearbeiten`;
            editButton.setAttribute('aria-label', `${game.label} bearbeiten`);
            editButton.textContent = '✎';
            editButton.addEventListener('click', () => openAchievementEditDialog(game.id, cardType));
            head.appendChild(editButton);
        }

        card.appendChild(head);
        return card;
    }

    function formatSteamTime(isoString) {
        const date = new Date(isoString);
        if (Number.isNaN(date.getTime())) return '';
        const minutes = Math.round((Date.now() - date.getTime()) / 60000);
        if (minutes < 60) return `vor ${Math.max(1, minutes)} Min.`;
        const hours = Math.round(minutes / 60);
        if (hours < 24) return `vor ${hours} Std.`;
        const days = Math.round(hours / 24);
        if (days < 7) return days === 1 ? 'gestern' : `vor ${days} Tagen`;
        return date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }

    function buildSteamBody(data) {
        const list = document.createElement('ul');
        list.className = 'steam-achievements';

        const empty = text => {
            const item = document.createElement('li');
            item.className = 'widget-empty';
            item.textContent = text;
            list.appendChild(item);
        };

        if (!data) {
            empty('Wird geladen …');
            return { body: list, updated: '' };
        }
        if (!data.configured) {
            empty('Die Steam-Erfolge sind noch nicht eingerichtet: Es fehlt der Steam-API-Schlüssel auf dem Server.');
            return { body: list, updated: '' };
        }
        if (data.pending) {
            empty('Die Erfolge werden gerade bei Steam abgeholt – einen Moment …');
            return { body: list, updated: '' };
        }
        if (!data.linked_players) {
            empty('Noch niemand hat einen Steam-Namen hinterlegt. Trag deinen unter „Mein Account“ ein.');
            return { body: list, updated: '' };
        }
        if (!data.entries || data.entries.length === 0) {
            empty('Keine aktuellen Erfolge gefunden.');
        }

        for (const entry of data.entries) {
            const item = document.createElement('li');
            item.className = 'steam-achievement';

            const placeholder = () => {
                const icon = document.createElement('span');
                icon.className = 'steam-achievement-icon placeholder';
                icon.textContent = '🏆';
                return icon;
            };
            if (entry.icon && /^https:\/\//i.test(entry.icon)) {
                const icon = document.createElement('img');
                icon.className = 'steam-achievement-icon';
                icon.src = entry.icon;
                icon.alt = '';
                icon.loading = 'lazy';
                icon.addEventListener('error', () => icon.replaceWith(placeholder()));
                item.appendChild(icon);
            } else {
                item.appendChild(placeholder());
            }

            const text = document.createElement('div');
            text.className = 'steam-achievement-text';
            const title = document.createElement('strong');
            title.textContent = entry.achievement || '';
            const meta = document.createElement('span');
            meta.className = 'steam-achievement-meta';
            const player = document.createElement(entry.profile_url ? 'a' : 'span');
            if (entry.profile_url && /^https:\/\/steamcommunity\.com\//i.test(entry.profile_url)) {
                player.href = entry.profile_url;
                player.target = '_blank';
                player.rel = 'noopener';
            }
            player.textContent = entry.player_name || '';
            meta.append(player, document.createTextNode(` · ${entry.game || ''}`));
            text.append(title, meta);
            if (entry.description) {
                const description = document.createElement('small');
                description.textContent = entry.description;
                text.appendChild(description);
            }

            const time = document.createElement('time');
            time.className = 'steam-achievement-time';
            time.dateTime = entry.unlocked_at || '';
            time.textContent = formatSteamTime(entry.unlocked_at);

            item.append(text, time);
            list.appendChild(item);
        }

        // Wer einen Steam-Namen hat, aber hier fehlt, bekommt den Grund angezeigt.
        const reasons = {
            not_found: 'Steam-Profil nicht gefunden – Namen bzw. Link prüfen',
            profile_private: 'Steam-Profil ist nicht öffentlich',
            games_private: 'Spieldetails sind auf Steam nicht öffentlich',
            achievements_private: 'Spieldetails stehen auf „Nur Freunde“ – für Erfolge auf „Öffentlich“ stellen',
            no_achievements: 'keine Erfolge in den zuletzt gespielten Spielen',
            error: 'Steam hat gerade nicht geantwortet',
        };
        if (Array.isArray(data.missing) && data.missing.length > 0) {
            const note = document.createElement('li');
            note.className = 'steam-missing';
            const heading = document.createElement('strong');
            heading.textContent = 'Ohne Erfolge:';
            note.appendChild(heading);
            for (const member of data.missing) {
                const line = document.createElement('span');
                line.textContent = `${member.player_name} – ${reasons[member.reason] || 'keine Daten'}`;
                note.appendChild(line);
            }
            list.appendChild(note);
        }
        return { body: list, updated: formatUpdatedAt(data.updated_at) };
    }

    // Nur für Admins: Erfolge sofort neu bei Steam holen und die Diagnose öffnen.
    function buildSteamAdminTools() {
        const tools = document.createElement('div');
        tools.className = 'steam-admin-tools';
        const refresh = document.createElement('button');
        refresh.type = 'button';
        refresh.className = 'steam-admin-link';
        refresh.textContent = 'Jetzt neu laden';
        refresh.addEventListener('click', async () => {
            refresh.disabled = true;
            refresh.textContent = 'Wird bei Steam abgeholt …';
            try {
                await api('admin_steam_refresh', {});
                steamPolls = 0;
                await loadSteamAchievements();
            } catch (error) {
                showToast(error.message || 'Aktualisierung fehlgeschlagen.');
                refresh.disabled = false;
                refresh.textContent = 'Jetzt neu laden';
            }
        });
        const diagnose = document.createElement('a');
        diagnose.className = 'steam-admin-link';
        diagnose.href = 'api.php?action=steam_diagnose';
        diagnose.target = '_blank';
        diagnose.rel = 'noopener';
        diagnose.textContent = 'Diagnose';
        tools.append(refresh, diagnose);
        return tools;
    }

    function buildSteamCard(game) {
        const card = buildAchievementCardShell(game, 'stats');
        const built = buildSteamBody(steamData);
        card.appendChild(built.body);
        const updated = document.createElement('p');
        updated.className = 'achievement-updated';
        updated.textContent = built.updated;
        card.appendChild(updated);
        if (ctx.isAdmin) card.appendChild(buildSteamAdminTools());
        return card;
    }

    // WoW: M+-Wertungen und darunter die Links in einer gemeinsamen Karte.
    function buildWowCard(game) {
        const card = buildAchievementCardShell(game, 'stats');
        const gameData = achievementsData ? achievementsData[game.id] : null;

        if (!achievementsData) {
            const loading = document.createElement('p');
            loading.className = 'widget-loading';
            loading.textContent = 'Wird geladen …';
            card.append(loading, Object.assign(document.createElement('p'), { className: 'achievement-updated' }));
            return card;
        }

        const stats = buildWowBody(gameData);
        card.appendChild(stats.body);

        const links = buildLinksBody(gameData);
        const linksHead = document.createElement('div');
        linksHead.className = 'achievement-links-head';
        const linksTitle = document.createElement('span');
        linksTitle.textContent = linksCardTitle(game);
        linksHead.appendChild(linksTitle);
        if (ctx.isAdmin) {
            const edit = document.createElement('button');
            edit.type = 'button';
            edit.className = 'achievement-edit-button';
            edit.title = `${game.label}: Links bearbeiten`;
            edit.setAttribute('aria-label', `${game.label}: Links bearbeiten`);
            edit.textContent = '✎';
            edit.addEventListener('click', () => openAchievementEditDialog(game.id, 'links'));
            linksHead.appendChild(edit);
        }
        card.append(linksHead, links.body);

        const updated = document.createElement('p');
        updated.className = 'achievement-updated';
        updated.textContent = stats.updated;
        card.appendChild(updated);
        return card;
    }

    function renderAchievementGrid() {
        const grid = root.querySelector('[data-achievements-grid]');
        grid.replaceChildren();
        for (const game of achievementGames) {
            if (game.type === 'wow' && !ctx.showWow) continue;
            grid.appendChild(game.type === 'steam' ? buildSteamCard(game) : buildWowCard(game));
        }
    }

    // Die Steam-Daten kommen sofort aus dem Zwischenspeicher des Servers. Holt der Server
    // sie gerade zum ersten Mal bei Steam („pending“), fragen wir alle 6 Sekunden nach
    // (höchstens 12-mal); bei „refreshing“ einmal nach 20 Sekunden, um Neues zu zeigen.
    let steamPolls = 0;
    async function loadSteamAchievements() {
        try {
            steamData = await api('steam_achievements');
        } catch (error) {
            steamData = { configured: true, linked_players: 1, entries: [], missing: [], updated_at: '' };
        }
        renderAchievementGrid();
        if (steamData.pending && steamPolls < 12) {
            steamPolls += 1;
            setTimeout(loadSteamAchievements, 6000);
        } else if (steamData.refreshing && steamPolls < 1) {
            steamPolls += 1;
            setTimeout(loadSteamAchievements, 20000);
        }
    }

    async function loadAchievements() {
        if (!ctx.showWow) return;
        renderAchievementGrid();
        try {
            const data = await api('achievements');
            achievementsData = { wow: data.wow };
        } catch (error) {
            achievementsData = null;
            showToast('Die Erfolge konnten nicht geladen werden.');
        }
        renderAchievementGrid();
    }

    let achievementEditRowId = 0;

    function addAchievementEditRow(label = '', value = '') {
        const rowId = `achievementRow${achievementEditRowId++}`;
        const row = document.createElement('div');
        row.className = 'achievement-edit-row';
        row.dataset.rowId = rowId;

        const labelInput = document.createElement('input');
        labelInput.type = 'text';
        labelInput.placeholder = 'Bezeichnung, z. B. „Rang“';
        labelInput.maxLength = 40;
        labelInput.value = label;
        labelInput.className = 'achievement-edit-label';

        const valueInput = document.createElement('input');
        valueInput.type = 'text';
        valueInput.placeholder = 'Wert, z. B. „Diamant 2“';
        valueInput.maxLength = 60;
        valueInput.value = value;
        valueInput.className = 'achievement-edit-value';

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = '✕';
        removeButton.setAttribute('aria-label', 'Zeile entfernen');
        removeButton.addEventListener('click', () => row.remove());

        row.append(labelInput, valueInput, removeButton);
        byId('achievementEditRows').appendChild(row);
    }

    function addAchievementEditLinkRow(label = '', url = '') {
        const row = document.createElement('div');
        row.className = 'achievement-edit-row';

        const labelInput = document.createElement('input');
        labelInput.type = 'text';
        labelInput.placeholder = 'Linktext, z. B. „Raider.IO Gilde“';
        labelInput.maxLength = 30;
        labelInput.value = label;
        labelInput.className = 'achievement-edit-link-label';

        const urlInput = document.createElement('input');
        urlInput.type = 'url';
        urlInput.placeholder = 'https://…';
        urlInput.maxLength = 200;
        urlInput.value = url;
        urlInput.className = 'achievement-edit-link-url';

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = '✕';
        removeButton.setAttribute('aria-label', 'Link entfernen');
        removeButton.addEventListener('click', () => row.remove());

        row.append(labelInput, urlInput, removeButton);
        byId('achievementEditLinkRows').appendChild(row);
    }

    function openAchievementEditDialog(gameId, part) {
        const game = achievementGamesById[gameId];
        const data = achievementsData ? achievementsData[gameId] : null;

        const statsSection = byId('achievementEditStatsSection');
        const linksSection = byId('achievementEditLinksSection');
        statsSection.hidden = part !== 'stats';
        linksSection.hidden = part !== 'links';

        const titleInput = byId('achievementEditTitleInput');
        if (part === 'stats') {
            byId('achievementEditTitle').textContent = `${statsCardTitle(game)} bearbeiten`;
            titleInput.value = (data && data.title) || '';
            titleInput.placeholder = game.label;

            byId('achievementEditRows').replaceChildren();
            const currentMilestones = (data && data.milestones) || [];
            if (currentMilestones.length === 0) {
                addAchievementEditRow();
            } else {
                for (const milestone of currentMilestones) addAchievementEditRow(milestone.label, milestone.value);
            }
        } else {
            byId('achievementEditTitle').textContent = `${linksCardTitle(game)} bearbeiten`;
            titleInput.value = (data && data.links_title) || '';
            titleInput.placeholder = `${game.label} Links`;

            byId('achievementEditLinkRows').replaceChildren();
            const currentLinks = (data && data.links) || [];
            if (currentLinks.length === 0) {
                addAchievementEditLinkRow();
            } else {
                for (const link of currentLinks) addAchievementEditLinkRow(link.label, link.url);
            }
        }

        const saveButton = byId('achievementEditSave');
        saveButton.onclick = async () => {
            const title = titleInput.value.trim();
            const payload = { game: gameId, part };

            if (part === 'stats') {
                const milestoneRows = [...byId('achievementEditRows').querySelectorAll('.achievement-edit-row')];
                payload.title = title;
                payload.milestones = milestoneRows
                    .map(row => ({
                        label: row.querySelector('.achievement-edit-label').value.trim(),
                        value: row.querySelector('.achievement-edit-value').value.trim(),
                    }))
                    .filter(entry => entry.label !== '' && entry.value !== '');
            } else {
                const linkRows = [...byId('achievementEditLinkRows').querySelectorAll('.achievement-edit-row')];
                const links = linkRows
                    .map(row => ({
                        label: row.querySelector('.achievement-edit-link-label').value.trim(),
                        url: row.querySelector('.achievement-edit-link-url').value.trim(),
                    }))
                    .filter(entry => entry.label !== '' && entry.url !== '');

                for (const link of links) {
                    if (!/^https?:\/\//i.test(link.url)) {
                        showToast(`Der Link „${link.label}“ muss mit http:// oder https:// beginnen.`);
                        return;
                    }
                }
                payload.links_title = title;
                payload.links = links;
            }

            saveButton.disabled = true;
            try {
                await api('admin_save_achievement', payload);
                achievementEditDialog.close();
                showToast('Wurde aktualisiert.');
                await loadAchievements();
            } catch (error) {
                showToast(error.message);
            } finally {
                saveButton.disabled = false;
            }
        };

        achievementEditDialog.showModal();
    }

    byId('achievementEditAddRow').addEventListener('click', () => addAchievementEditRow());
    byId('achievementEditAddLinkRow').addEventListener('click', () => addAchievementEditLinkRow());


    for (const button of document.querySelectorAll('[data-close-dialog="achievementEditDialog"]')) {
        button.addEventListener('click', () => achievementEditDialog.close());
    }

    (async () => {
        renderAchievementGrid();
        try {
            const settings = await api('site_settings');
            ctx.isAdmin = Boolean(settings.is_admin);
        } catch { /* ohne Anmeldung: nur lesen */ }
        loadSteamAchievements();
        loadAchievements();
    })();
})();
