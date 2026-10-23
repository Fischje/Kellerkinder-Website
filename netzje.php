<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
// Twitch bettet nur ein, wenn die eigene Domain als „parent“ mitgegeben wird.
$streamParent = preg_replace('/:\d+$/', '', (string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" id="themeColorMeta" content="#140b1b">
    <meta name="application-name" content="Kellerkinder">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Kellerkinder">
    <title>Netzje — Kellerkinder</title>
    <meta name="description" content="Netzje, die LAN-Party der Kellerkinder: Termin, Stundenplan, Anmeldung und Live-Stream.">
    <link rel="icon" href="assets/kellerkinder-logo.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800;900&family=Open+Sans:wght@400;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/includes/styles.php'; ?>
    <?php require __DIR__ . '/includes/blog-styles.php'; ?>
    <?php require __DIR__ . '/includes/netzje-styles.php'; ?>
    <?php require __DIR__ . '/includes/arena-styles.php'; ?>
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
$activeNav = 'netzje';
$pageKicker = 'Netzje';
$pageTitle = 'Netzje';
$pageLead = 'Unsere LAN-Party: Termin, Stundenplan, Anmeldung und Live-Stream.';
$showInstall = false;
require __DIR__ . '/includes/site-header.php';
?>

<main class="page-shell">

    <section class="netzje-panel" id="partyPanel">
        <div class="netzje-head">
            <h2 class="section-title" id="partyTitle"><span class="accent">Die</span> Party</h2>
            <div class="netzje-actions">
                <button class="primary-button" id="attendButton" type="button" hidden>Ich komme</button>
                <button class="secondary-button" id="editPartyButton" type="button" hidden>Party bearbeiten</button>
                <button class="danger-button" id="newPartyButton" type="button" hidden>Neue Party</button>
            </div>
        </div>
        <div id="partyBody"><p class="widget-loading">Wird geladen …</p></div>
    </section>

    <section class="netzje-panel" id="streamPanel" hidden>
        <div class="netzje-head">
            <h2 class="section-title"><span class="accent">Live</span> dabei</h2>
        </div>
        <div id="streamBody"></div>
    </section>

    <section class="netzje-panel" id="timetablePanel" hidden>
        <div class="netzje-head">
            <h2 class="section-title"><span class="accent">Der</span> Stundenplan</h2>
            <div class="netzje-actions">
                <button class="secondary-button" id="proposeButton" type="button" hidden>Spiel vorschlagen</button>
                <button class="primary-button" id="addSlotButton" type="button" hidden>＋ Spiel eintragen</button>
            </div>
        </div>
        <p class="netzje-note" id="timetableNote"></p>
        <div class="netzje-timeline-wrap" id="timelineWrap"><div class="netzje-timeline" id="timeline"></div></div>

        <div class="netzje-section" id="plannedBox" hidden>
            <h3 class="stats-section" id="plannedTitle">Eingetragene Spiele</h3>
            <div class="netzje-list" id="timetableList"></div>
        </div>

        <div class="netzje-section" id="proposalsBox">
            <h3 class="stats-section" id="proposalsTitle">Vorgeschlagene Spiele</h3>
            <p class="netzje-note" id="proposalsNote"></p>
            <ul class="netzje-proposals" id="proposalsList"></ul>
        </div>
    </section>

    <footer class="site-footer">Created by Fischje with <span class="heart" aria-label="Love">♥</span> · Made with AI · Version <?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?></footer>
</main>

<dialog id="partyDialog" class="wide">
    <div class="modal-content form-stack">
        <h2 class="modal-title">Party bearbeiten</h2>
        <div class="netzje-form-grid">
            <div>
                <label for="pTitle">Titel</label>
                <input type="text" id="pTitle" maxlength="80" placeholder="z. B. Netzje #7">
            </div>
            <div>
                <label for="pStart">Beginn</label>
                <input type="datetime-local" id="pStart">
            </div>
            <div>
                <label for="pEnd">Ende</label>
                <input type="datetime-local" id="pEnd">
            </div>
        </div>
        <div>
            <label for="pDescription">Beschreibung</label>
            <textarea id="pDescription" maxlength="2000" placeholder="Worum geht es, wer ist eingeladen …"></textarea>
        </div>
        <div>
            <label for="pLocation">Ort / Adresse (nur für Angemeldete sichtbar)</label>
            <textarea id="pLocation" maxlength="300" placeholder="Straße, Ort, Klingel …"></textarea>
        </div>
        <div>
            <label for="pInfo">Mitbringliste / Infos</label>
            <textarea id="pInfo" maxlength="2000" placeholder="Netzwerkkabel, Mehrfachstecker, Kosten, Essen …"></textarea>
        </div>
        <div>
            <label for="pBackgroundFile">Hintergrundbild dieser Party</label>
            <div class="netzje-bg-row">
                <img id="pBackgroundPreview" class="netzje-bg-preview" alt="" hidden>
                <span class="netzje-muted" id="pBackgroundNone">Kein eigenes Bild – es gilt der normale Hintergrund.</span>
                <div class="netzje-actions">
                    <button class="secondary-button" id="pBackgroundPick" type="button">Bild wählen …</button>
                    <button class="danger-button" id="pBackgroundRemove" type="button" hidden>Entfernen</button>
                </div>
            </div>
            <input type="file" id="pBackgroundFile" accept="image/png,image/jpeg,image/webp" hidden>
            <p class="netzje-muted">Wird sofort gespeichert und auf höchstens 1920 Pixel Breite verkleinert. „Neue Party“ entfernt das Bild wieder.</p>
        </div>
        <div class="netzje-form-grid">
            <div>
                <label for="pStreamType">Live-Stream</label>
                <select id="pStreamType">
                    <option value="none">Kein Stream</option>
                    <option value="twitch">Twitch</option>
                    <option value="youtube">YouTube</option>
                </select>
            </div>
            <div>
                <label for="pStream">Kanal bzw. Link</label>
                <input type="text" id="pStream" maxlength="200" placeholder="Twitch-Kanal oder YouTube-Link">
            </div>
        </div>
        <p class="form-error" id="partyError" role="alert" hidden></p>
        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="partyDialog">Abbrechen</button>
            <button class="primary-button" id="savePartyButton" type="button">Speichern</button>
        </div>
    </div>
</dialog>

<dialog id="slotDialog" class="wide">
    <div class="modal-content form-stack">
        <h2 class="modal-title" id="slotDialogTitle">Spiel eintragen</h2>
        <div class="netzje-form-grid">
            <div>
                <label for="sGame">Spiel</label>
                <input type="text" id="sGame" maxlength="100" placeholder="z. B. Counter-Strike 2">
            </div>
            <div>
                <label for="sStart">Beginn</label>
                <input type="datetime-local" id="sStart">
            </div>
            <div>
                <label for="sEnd">Ende</label>
                <input type="datetime-local" id="sEnd">
            </div>
            <div>
                <label for="sMax">Plätze</label>
                <input type="text" inputmode="numeric" id="sMax" maxlength="3" placeholder="leer = ohne Limit">
            </div>
        </div>
        <div>
            <label for="sNote">Hinweis (optional)</label>
            <input type="text" id="sNote" maxlength="200" placeholder="z. B. 5v5, Mods vorher installieren">
        </div>
        <p class="form-error" id="slotError" role="alert" hidden></p>
        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="slotDialog">Abbrechen</button>
            <button class="primary-button" id="saveSlotButton" type="button">Speichern</button>
        </div>
    </div>
</dialog>

<dialog id="detailDialog">
    <div class="modal-content form-stack">
        <h2 class="modal-title" id="detailTitle"></h2>
        <p class="netzje-dialog-meta" id="detailMeta"></p>
        <div id="detailBody"></div>
        <p class="form-error" id="detailError" role="alert" hidden></p>
        <div class="modal-actions" id="detailActions"></div>
    </div>
</dialog>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
    const byId = id => document.getElementById(id);
    const STREAM_PARENT = <?= json_encode($streamParent) ?>;
    const HOUR_PX = 64;          // Breite einer Stunde in der Zeitleiste
    const LANE_PX = 62;          // Höhe einer Spur
    const STREAM_LEAD_MS = 2 * 3600000; // Stream ab 2 Stunden vor Beginn einbetten
    const REFRESH_MS = 60000;
    const state = { csrf: '', data: null, editingSlotId: 0, slotMode: 'admin', openSlotId: 0 };

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
                body: JSON.stringify({ action, ...payload })
            };
        const response = await fetch(payload === null ? `api.php?action=${encodeURIComponent(action)}` : 'api.php', options);
        let data;
        try { data = await response.json(); }
        catch { throw new Error('Der Server hat keine gültige Antwort geliefert.'); }
        if (!response.ok || data.ok === false) throw new Error(data.error || 'Die Aktion ist fehlgeschlagen.');
        return data;
    }

    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }

    function button(label, className, onClick) {
        const node = el('button', className, label);
        node.type = 'button';
        node.addEventListener('click', onClick);
        return node;
    }

    // Lokale Zeit „YYYY-MM-DDTHH:MM“ als Date (Browser-Zeitzone; die Party findet in Deutschland statt).
    function parseTime(text) {
        const m = /^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})$/.exec(text || '');
        return m ? new Date(+m[1], +m[2] - 1, +m[3], +m[4], +m[5]) : null;
    }
    const fmtDay = date => date.toLocaleDateString('de-DE', { weekday: 'short', day: '2-digit', month: '2-digit' });
    const fmtDayLong = date => date.toLocaleDateString('de-DE', { weekday: 'long', day: '2-digit', month: 'long', year: 'numeric' });
    const fmtTime = date => date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
    const sameDay = (a, b) => a.toDateString() === b.toDateString();
    function fmtRange(startText, endText) {
        const start = parseTime(startText);
        const end = parseTime(endText);
        if (!start || !end) return '';
        return sameDay(start, end)
            ? `${fmtDay(start)} ${fmtTime(start)}–${fmtTime(end)}`
            : `${fmtDay(start)} ${fmtTime(start)} – ${fmtDay(end)} ${fmtTime(end)}`;
    }

    function countLabel(slot) {
        return slot.max > 0 ? `${slot.players.length}/${slot.max}` : `${slot.players.length} dabei`;
    }

    function chips(people, extraClass) {
        const list = el('ul', 'netzje-chips');
        for (const person of people) {
            list.appendChild(el('li', `netzje-chip${extraClass ? ' ' + extraClass : ''}`, person.name));
        }
        return list;
    }

    // ===== Hintergrundbild der Party =====
    function renderBackground() {
        const url = state.data.party.background;
        document.body.classList.toggle('netzje-has-bg', Boolean(url));
        if (url) document.body.style.setProperty('--netzje-bg', `url("${url}")`);
        else document.body.style.removeProperty('--netzje-bg');
        const preview = byId('pBackgroundPreview');
        preview.hidden = !url;
        if (url) preview.src = url;
        byId('pBackgroundNone').hidden = Boolean(url);
        byId('pBackgroundRemove').hidden = !url;
    }

    // Im Browser auf höchstens 1920 px verkleinern (spart Upload), als JPEG.
    function shrinkImage(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onerror = () => reject(new Error('Die Datei konnte nicht gelesen werden.'));
            reader.onload = () => {
                const img = new Image();
                img.onerror = () => reject(new Error('Die Datei ist kein gültiges Bild.'));
                img.onload = () => {
                    const scale = Math.min(1, 1920 / img.naturalWidth);
                    const canvas = document.createElement('canvas');
                    canvas.width = Math.round(img.naturalWidth * scale);
                    canvas.height = Math.round(img.naturalHeight * scale);
                    canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
                    resolve(canvas.toDataURL('image/jpeg', 0.85));
                };
                img.src = reader.result;
            };
            reader.readAsDataURL(file);
        });
    }

    byId('pBackgroundPick').addEventListener('click', () => byId('pBackgroundFile').click());
    byId('pBackgroundFile').addEventListener('change', async event => {
        const file = event.target.files[0];
        event.target.value = '';
        if (!file) return;
        const pick = byId('pBackgroundPick');
        pick.disabled = true;
        pick.textContent = 'Wird hochgeladen …';
        try {
            const image = await shrinkImage(file);
            await runAction('admin_netzje_background', { image }, byId('partyError'), 'Hintergrundbild gespeichert.');
        } catch (error) {
            byId('partyError').textContent = error.message;
            byId('partyError').hidden = false;
        } finally {
            pick.disabled = false;
            pick.textContent = 'Bild wählen …';
        }
    });
    byId('pBackgroundRemove').addEventListener('click', async () => {
        if (!confirm('Hintergrundbild dieser Party entfernen?')) return;
        await runAction('admin_netzje_background', { image: '' }, byId('partyError'), 'Hintergrundbild entfernt.');
    });

    // ===== Party =====
    function renderParty() {
        const { party, logged_in: loggedIn, is_admin: isAdmin } = state.data;
        const body = byId('partyBody');
        const planned = Boolean(party.start && party.end);
        byId('editPartyButton').hidden = !isAdmin;
        byId('newPartyButton').hidden = !isAdmin || !planned;
        const attend = byId('attendButton');
        attend.hidden = !loggedIn || !planned;
        attend.textContent = party.attending ? 'Ich komme doch nicht' : 'Ich komme';
        attend.className = party.attending ? 'secondary-button' : 'primary-button';

        const title = byId('partyTitle');
        title.replaceChildren();
        if (party.title) {
            title.textContent = party.title;
        } else {
            title.append(el('span', 'accent', 'Die'), ' Party');
        }

        if (!planned) {
            body.replaceChildren(el('p', 'widget-empty', isAdmin
                ? 'Gerade ist keine Party geplant. Mit „Party bearbeiten“ legst du Termin und Infos fest.'
                : 'Gerade ist keine Party geplant. Sobald ein Termin steht, erscheint er hier.'));
            return;
        }

        const start = parseTime(party.start);
        const end = parseTime(party.end);
        const now = new Date();
        const nodes = [];
        nodes.push(el('p', 'netzje-when', sameDay(start, end)
            ? `${fmtDayLong(start)}, ${fmtTime(start)}–${fmtTime(end)} Uhr`
            : `${fmtDayLong(start)}, ${fmtTime(start)} Uhr bis ${fmtDayLong(end)}, ${fmtTime(end)} Uhr`));
        let countdown;
        if (now >= start && now < end) {
            countdown = el('span', 'netzje-countdown live', '● Läuft gerade');
        } else if (now >= end) {
            countdown = el('span', 'netzje-countdown', 'Vorbei');
        } else {
            const days = Math.floor((start - now) / 86400000);
            const hours = Math.floor(((start - now) % 86400000) / 3600000);
            countdown = el('span', 'netzje-countdown', days > 0 ? `Noch ${days} ${days === 1 ? 'Tag' : 'Tage'}` : `Noch ${hours} Std.`);
        }
        nodes.push(countdown);
        if (party.description) nodes.push(el('p', 'netzje-text', party.description));

        const grid = el('div', 'netzje-grid');
        const people = el('div', 'netzje-box');
        people.append(el('h3', '', `Wer kommt (${party.attendees.length})`));
        if (party.attendees.length > 0) people.append(chips(party.attendees));
        else people.append(el('p', 'netzje-muted', 'Noch niemand eingetragen.'));
        if (!loggedIn) people.append(loginHint('Zum Zusagen bitte anmelden.'));
        grid.append(people);

        if (party.has_location) {
            const where = el('div', 'netzje-box');
            where.append(el('h3', '', 'Ort'));
            where.append(loggedIn ? el('p', 'netzje-text', party.location) : loginHint('Die Adresse siehst du, wenn du angemeldet bist.'));
            grid.append(where);
        }
        if (party.info) {
            const info = el('div', 'netzje-box');
            info.append(el('h3', '', 'Mitbringen & Infos'), el('p', 'netzje-text', party.info));
            grid.append(info);
        }
        nodes.push(grid);
        body.replaceChildren(...nodes);
    }

    function loginHint(text) {
        const hint = el('p', 'netzje-muted');
        hint.append(`${text} `);
        const link = el('a', '', 'Zur Anmeldung');
        link.href = 'index.php';
        hint.append(link);
        return hint;
    }

    // ===== Stream =====
    let streamKey = '';
    function renderStream() {
        const { party } = state.data;
        const panel = byId('streamPanel');
        if (party.stream_type === 'none' || !party.stream || !party.start) { panel.hidden = true; streamKey = ''; return; }
        panel.hidden = false;
        const start = parseTime(party.start);
        const end = parseTime(party.end);
        const now = new Date();
        const live = now >= new Date(start.getTime() - STREAM_LEAD_MS) && now < end;
        const key = `${party.stream_type}:${party.stream}:${live}`;
        if (key === streamKey) return; // Player beim automatischen Neuladen nicht neu starten
        streamKey = key;
        const body = byId('streamBody');
        const pageUrl = party.stream_type === 'twitch' ? `https://www.twitch.tv/${party.stream}` : `https://www.youtube.com/watch?v=${party.stream}`;
        if (!live) {
            const text = el('p', 'netzje-muted');
            text.append(`Der Stream läuft während der Party (ab ${fmtTime(start)} Uhr am ${fmtDay(start)}): `);
            const link = el('a', '', party.stream_type === 'twitch' ? `twitch.tv/${party.stream}` : 'YouTube');
            link.href = pageUrl;
            link.target = '_blank';
            link.rel = 'noopener';
            text.append(link);
            body.replaceChildren(text);
            return;
        }
        const frame = document.createElement('iframe');
        frame.src = party.stream_type === 'twitch'
            ? `https://player.twitch.tv/?channel=${encodeURIComponent(party.stream)}&parent=${encodeURIComponent(STREAM_PARENT)}&muted=true`
            : `https://www.youtube-nocookie.com/embed/${encodeURIComponent(party.stream)}`;
        frame.allow = 'autoplay; fullscreen; picture-in-picture';
        frame.allowFullscreen = true;
        frame.title = 'Live-Stream';
        const box = el('div', 'netzje-stream');
        box.append(frame);
        body.replaceChildren(box);
    }

    // ===== Stundenplan =====
    function plannedSlots() { return state.data.slots.filter(slot => slot.status === 'planned'); }

    // Spuren verteilen: jedes Spiel in die erste Spur, in der es nichts überdeckt.
    function assignLanes(slots) {
        const laneEnds = [];
        return slots.map(slot => {
            let lane = laneEnds.findIndex(endText => endText <= slot.start);
            if (lane === -1) { lane = laneEnds.length; laneEnds.push(slot.end); } else { laneEnds[lane] = slot.end; }
            return { slot, lane };
        });
    }

    function renderTimeline() {
        const { party } = state.data;
        const start = parseTime(party.start);
        const end = parseTime(party.end);
        const timeline = byId('timeline');
        const hours = Math.max(1, (end - start) / 3600000);
        const width = Math.ceil(hours * HOUR_PX);
        const x = date => ((date - start) / 3600000) * HOUR_PX;
        const placed = assignLanes(plannedSlots());
        const lanes = Math.max(1, ...placed.map(item => item.lane + 1));
        timeline.style.width = `${width}px`;
        timeline.style.height = `${44 + lanes * LANE_PX + 8}px`;

        const ruler = el('div', 'netzje-ruler');
        const nodes = [ruler];
        const first = new Date(start);
        first.setMinutes(0, 0, 0);
        if (first < start) first.setHours(first.getHours() + 1);
        for (let t = new Date(first); t <= end; t.setHours(t.getHours() + 1)) {
            const left = x(t);
            if (t.getHours() === 0 || t.getTime() === first.getTime()) {
                const day = el('span', 'netzje-day', fmtDay(t));
                day.style.left = `${left}px`;
                ruler.append(day);
            }
            const tick = el('span', 'netzje-tick', String(t.getHours()).padStart(2, '0'));
            tick.style.left = `${left}px`;
            ruler.append(tick);
            const line = el('span', 'netzje-gridline');
            line.style.left = `${left}px`;
            nodes.push(line);
        }
        for (const { slot, lane } of placed) {
            const block = button('', 'netzje-block' + (slot.signed_up ? ' mine' : '') + (slot.max > 0 && slot.players.length >= slot.max ? ' full' : ''), () => openDetail(slot.id));
            const s = parseTime(slot.start);
            const e = parseTime(slot.end);
            block.style.left = `${x(s) + 2}px`;
            block.style.width = `${Math.max(28, x(e) - x(s) - 4)}px`;
            block.style.top = `${44 + lane * LANE_PX + 6}px`;
            block.style.height = `${LANE_PX - 8}px`;
            block.title = `${slot.game} · ${fmtRange(slot.start, slot.end)} · ${countLabel(slot)}`;
            block.append(el('strong', '', slot.game), el('small', '', `${fmtTime(s)}–${fmtTime(e)} · ${countLabel(slot)}${slot.signed_up ? ' · du bist dabei' : ''}`));
            nodes.push(block);
        }
        const now = new Date();
        if (now >= start && now <= end) {
            const line = el('span', 'netzje-now');
            line.style.left = `${x(now)}px`;
            nodes.push(line);
        }
        timeline.replaceChildren(...nodes);
    }

    function slotRow(slot, onClick) {
        const row = button('', 'netzje-row' + (slot.signed_up ? ' mine' : ''), onClick);
        const s = parseTime(slot.start);
        const e = parseTime(slot.end);
        const game = el('span', 'game', slot.game);
        const sub = [
            sameDay(s, e) ? '' : `bis ${fmtDay(e)}`,
            slot.note,
            slot.status === 'proposed' && slot.proposed_by ? `vorgeschlagen von ${slot.proposed_by}` : '',
            slot.signed_up ? 'du bist dabei' : '',
        ].filter(Boolean).join(' · ');
        if (sub) game.append(el('small', '', sub));
        row.append(
            el('span', 'time', `${fmtTime(s)}–${fmtTime(e)}`),
            game,
            el('span', 'count', slot.status === 'proposed' ? fmtDay(s) : countLabel(slot)),
        );
        return row;
    }

    function renderList() {
        const list = byId('timetableList');
        const groups = new Map();
        for (const slot of plannedSlots()) {
            const key = fmtDayLong(parseTime(slot.start));
            if (!groups.has(key)) groups.set(key, []);
            groups.get(key).push(slot);
        }
        const nodes = [];
        for (const [day, slots] of groups) {
            const ol = el('ol');
            for (const slot of slots) {
                const li = el('li');
                li.append(slotRow(slot, () => openDetail(slot.id)));
                ol.append(li);
            }
            nodes.push(el('h3', '', day), ol);
        }
        list.replaceChildren(...nodes);
    }

    function renderProposals() {
        const { logged_in: loggedIn, is_admin: isAdmin } = state.data;
        const proposals = state.data.slots.filter(slot => slot.status === 'proposed');
        byId('proposalsTitle').textContent = `Vorgeschlagene Spiele (${proposals.length})`;
        let note;
        if (proposals.length === 0) {
            note = loggedIn && !isAdmin ? 'Noch keine Vorschläge. Mit „Spiel vorschlagen“ bringst du eins ins Spiel.' : 'Noch keine Vorschläge.';
        } else if (isAdmin) {
            note = 'Anklicken, um einen Vorschlag zu übernehmen (Zeit und Plätze kannst du dabei anpassen) oder zu löschen.';
        } else {
            note = 'Noch nicht im Stundenplan – ein Admin übernimmt Vorschläge.' + (loggedIn ? ' Deine eigenen kannst du zurückziehen.' : '');
        }
        byId('proposalsNote').textContent = note;
        const list = byId('proposalsList');
        list.replaceChildren(...proposals.map(slot => {
            const li = el('li');
            li.append(slotRow(slot, () => openDetail(slot.id)));
            return li;
        }));
    }

    function renderTimetable() {
        const { party, logged_in: loggedIn, is_admin: isAdmin } = state.data;
        const panel = byId('timetablePanel');
        const planned = Boolean(party.start && party.end);
        panel.hidden = !planned;
        if (!planned) return;
        byId('addSlotButton').hidden = !isAdmin;
        byId('proposeButton').hidden = !loggedIn || isAdmin;
        const count = plannedSlots().length;
        byId('timetableNote').textContent = count === 0
            ? (isAdmin ? 'Noch keine Spiele eingetragen. Mit „Spiel eintragen“ füllst du den Stundenplan.' : 'Noch keine Spiele im Stundenplan.')
            : 'Ein Spiel anklicken, um zu sehen, wer dabei ist, und dich anzumelden.' + (loggedIn ? '' : ' Anmelden geht nur mit Account.');
        byId('timelineWrap').hidden = count === 0;
        byId('plannedBox').hidden = count === 0;
        byId('plannedTitle').textContent = `Eingetragene Spiele (${count})`;
        renderTimeline();
        renderList();
        renderProposals();
    }

    // ===== Details eines Spiels =====
    function openDetail(id) {
        state.openSlotId = id;
        renderDetail();
        byId('detailError').hidden = true;
        if (!byId('detailDialog').open) byId('detailDialog').showModal();
    }

    function renderDetail() {
        const slot = state.data.slots.find(item => item.id === state.openSlotId);
        const dialog = byId('detailDialog');
        if (!slot) { if (dialog.open) dialog.close(); return; }
        const { logged_in: loggedIn, is_admin: isAdmin } = state.data;
        byId('detailTitle').textContent = slot.game;
        byId('detailMeta').textContent = [fmtRange(slot.start, slot.end), slot.max > 0 ? `${slot.max} Plätze` : 'ohne Platzlimit', slot.note].filter(Boolean).join(' · ');
        const body = byId('detailBody');
        const actions = byId('detailActions');
        const nodes = [];
        const buttons = [];
        if (slot.status === 'proposed') {
            nodes.push(el('p', 'netzje-muted', `Vorschlag${slot.proposed_by ? ` von ${slot.proposed_by}` : ''} – noch nicht im Stundenplan.`));
            if (isAdmin) {
                buttons.push(button('Löschen', 'danger-button', () => deleteSlot(slot.id)));
                buttons.push(button('Übernehmen …', 'primary-button', () => { byId('detailDialog').close(); openSlotEditor(slot, 'admin'); }));
            } else if (slot.own_proposal) {
                buttons.push(button('Zurückziehen', 'danger-button', () => withdraw(slot.id)));
            }
        } else {
            nodes.push(el('h3', 'stats-section', `Dabei (${countLabel(slot)})`));
            nodes.push(slot.players.length > 0 ? chips(slot.players) : el('p', 'netzje-muted', 'Noch niemand angemeldet.'));
            if (slot.waitlist.length > 0) {
                nodes.push(el('h3', 'stats-section', `Warteliste (${slot.waitlist.length})`));
                nodes.push(chips(slot.waitlist, 'wait'));
                nodes.push(el('p', 'netzje-muted', 'Sagt jemand ab, rückt die Warteliste der Reihe nach automatisch nach.'));
            }
            if (!loggedIn) nodes.push(loginHint('Anmelden für Spiele geht nur mit Account.'));
            if (isAdmin) {
                buttons.push(button('Löschen', 'danger-button', () => deleteSlot(slot.id)));
                buttons.push(button('Bearbeiten', 'secondary-button', () => { byId('detailDialog').close(); openSlotEditor(slot, 'admin'); }));
            }
            if (loggedIn) {
                const full = slot.max > 0 && slot.players.length >= slot.max;
                const label = slot.signed_up ? 'Abmelden' : (full ? 'Auf die Warteliste' : 'Ich spiele mit');
                buttons.push(button(label, slot.signed_up ? 'secondary-button' : 'primary-button', () => signup(slot.id, !slot.signed_up)));
            }
        }
        buttons.unshift(button('Schließen', 'secondary-button', () => byId('detailDialog').close()));
        body.replaceChildren(...nodes);
        actions.replaceChildren(...buttons);
    }

    async function runAction(action, payload, errorBox, message) {
        try {
            state.data = await api(action, payload);
            state.csrf = state.data.csrf_token || state.csrf;
            renderAll();
            if (message) showToast(message);
            return true;
        } catch (error) {
            if (errorBox) { errorBox.textContent = error.message; errorBox.hidden = false; } else { showToast(error.message); }
            return false;
        }
    }

    async function signup(id, join) {
        const slot = state.data.slots.find(item => item.id === id);
        const waiting = join && slot && slot.max > 0 && slot.players.length >= slot.max;
        await runAction('netzje_signup', { id, join }, byId('detailError'),
            join ? (waiting ? 'Du stehst auf der Warteliste.' : 'Du bist dabei!') : 'Abgemeldet.');
    }

    async function deleteSlot(id) {
        if (!confirm('Dieses Spiel wirklich löschen? Die Anmeldungen gehen dabei verloren.')) return;
        if (await runAction('admin_netzje_slot_delete', { id }, byId('detailError'), 'Gelöscht.')) byId('detailDialog').close();
    }

    async function withdraw(id) {
        if (await runAction('netzje_withdraw', { id }, byId('detailError'), 'Vorschlag zurückgezogen.')) byId('detailDialog').close();
    }

    // ===== Formulare =====
    function openSlotEditor(slot, mode) {
        const { party } = state.data;
        state.slotMode = mode;
        state.editingSlotId = slot ? slot.id : 0;
        const proposalTakeover = slot && slot.status === 'proposed';
        byId('slotDialogTitle').textContent = mode === 'propose' ? 'Spiel vorschlagen'
            : (proposalTakeover ? 'Vorschlag übernehmen' : (slot ? 'Spiel bearbeiten' : 'Spiel eintragen'));
        byId('saveSlotButton').textContent = mode === 'propose' ? 'Vorschlagen' : (proposalTakeover ? 'In den Stundenplan' : 'Speichern');
        byId('sGame').value = slot ? slot.game : '';
        byId('sStart').value = slot ? slot.start : party.start;
        byId('sEnd').value = slot ? slot.end : '';
        byId('sMax').value = slot && slot.max > 0 ? slot.max : '';
        byId('sNote').value = slot ? slot.note : '';
        for (const id of ['sStart', 'sEnd']) { byId(id).min = party.start; byId(id).max = party.end; }
        byId('slotError').hidden = true;
        byId('slotDialog').showModal();
    }

    byId('addSlotButton').addEventListener('click', () => openSlotEditor(null, 'admin'));
    byId('proposeButton').addEventListener('click', () => openSlotEditor(null, 'propose'));

    byId('saveSlotButton').addEventListener('click', async () => {
        const save = byId('saveSlotButton');
        byId('slotError').hidden = true;
        save.disabled = true;
        const payload = {
            id: state.editingSlotId,
            game: byId('sGame').value,
            start: byId('sStart').value,
            end: byId('sEnd').value,
            max: byId('sMax').value,
            note: byId('sNote').value,
        };
        const ok = await runAction(state.slotMode === 'propose' ? 'netzje_propose' : 'admin_netzje_slot_save', payload, byId('slotError'),
            state.slotMode === 'propose' ? 'Vorschlag gespeichert – ein Admin übernimmt ihn.' : 'Gespeichert.');
        save.disabled = false;
        if (ok) byId('slotDialog').close();
    });

    byId('editPartyButton').addEventListener('click', () => {
        const { party } = state.data;
        byId('pTitle').value = party.title;
        byId('pStart').value = party.start;
        byId('pEnd').value = party.end;
        byId('pDescription').value = party.description;
        byId('pLocation').value = party.location || '';
        byId('pInfo').value = party.info;
        byId('pStreamType').value = party.stream_type;
        byId('pStream').value = party.stream;
        byId('partyError').hidden = true;
        byId('partyDialog').showModal();
    });

    byId('savePartyButton').addEventListener('click', async () => {
        const save = byId('savePartyButton');
        byId('partyError').hidden = true;
        save.disabled = true;
        const ok = await runAction('admin_netzje_save', {
            title: byId('pTitle').value,
            start: byId('pStart').value,
            end: byId('pEnd').value,
            description: byId('pDescription').value,
            location: byId('pLocation').value,
            info: byId('pInfo').value,
            stream_type: byId('pStreamType').value,
            stream: byId('pStream').value,
        }, byId('partyError'), 'Party gespeichert.');
        save.disabled = false;
        if (ok) byId('partyDialog').close();
    });

    byId('newPartyButton').addEventListener('click', async () => {
        if (!confirm('Neue Party anlegen? Termin, Stundenplan, Anmeldungen, Teilnehmer und Hintergrundbild werden gelöscht. Titel, Texte und Stream bleiben als Vorlage stehen.')) return;
        await runAction('admin_netzje_new', {}, null, 'Bereit für die nächste Party – jetzt den Termin eintragen.');
    });

    byId('attendButton').addEventListener('click', async () => {
        const attend = !state.data.party.attending;
        await runAction('netzje_attend', { attend }, null, attend ? 'Schön, dass du kommst!' : 'Zusage zurückgenommen.');
    });

    for (const close of document.querySelectorAll('[data-close-dialog]')) {
        close.addEventListener('click', () => byId(close.dataset.closeDialog).close());
    }

    // ===== Laden =====
    let scrolledToNow = false;
    function renderAll() {
        renderBackground();
        renderParty();
        renderStream();
        renderTimetable();
        if (byId('detailDialog').open) renderDetail();
        if (!scrolledToNow) {
            // Während der Party zur aktuellen Uhrzeit scrollen.
            const line = document.querySelector('.netzje-now');
            if (line) { byId('timelineWrap').scrollLeft = Math.max(0, line.offsetLeft - 120); scrolledToNow = true; }
        }
    }

    async function load() {
        try {
            state.data = await api('netzje');
            state.csrf = state.data.csrf_token || state.csrf;
            renderAll();
        } catch (error) {
            byId('partyBody').replaceChildren(el('p', 'widget-empty', error.message));
        }
    }

    load();
    setInterval(() => {
        const busy = [...document.querySelectorAll('dialog')].some(dialog => dialog.open && dialog.id !== 'detailDialog');
        if (!document.hidden && !busy) load();
    }, REFRESH_MS);

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('./service-worker.js').catch(() => {});
        });
    }
</script>
</body>
</html>
