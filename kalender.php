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
    <title>Kellerkinder</title>
    <meta name="description" content="Der gemeinsame Kellerkinder-Online-Kalender für eure Spieltage.">
    <link rel="icon" href="assets/kellerkinder-logo.svg?v=<?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?>" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png?v=<?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?>">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Montserrat:wght@500;600;700;800;900&family=Open+Sans:wght@400;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <?php require __DIR__ . '/includes/styles.php'; ?>
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
$activeNav = 'calendar';
$pageKicker = 'Online-Kalender';
$pageTitle = 'Wer zockt wann?';
$pageLead = 'Trag ein, wann du online bist – und was du spielen willst.';
$showInstall = true;
require __DIR__ . '/includes/site-header.php';
?>

<main class="page-shell">

    <div id="setupCallout" class="setup-callout" hidden>
        <strong>Ersteinrichtung:</strong> Es existiert noch kein Benutzerkonto. Der erste registrierte Account wird automatisch als Administrator eingetragen.
        <button class="primary-button setup-callout-button" id="setupRegisterButton" type="button">Ersten Account anlegen</button>
    </div>

    <div id="passwordCallout" class="password-callout" hidden>
        <strong>Passwortänderung erforderlich:</strong> Ein Administrator hat dein Passwort geändert. Lege jetzt ein eigenes neues Passwort fest.
    </div>

    <div id="storageWarning" class="storage-warning" hidden>
        <strong>Nur-Lese-Modus:</strong> Der Plan ist sichtbar, Änderungen können aber nicht gespeichert werden.
        Gib dem Ordner <code>data</code> Schreibrechte für den PHP-Webserver.
    </div>

    <section class="board" aria-label="Verfügbarkeitsplan">
        <div class="board-toolbar">
            <h2 class="board-heading section-title"><span class="accent">Nächste</span> Spieltage</h2>
            <div class="toolbar" aria-label="Steuerung und Legende">
                <div class="legend" aria-label="Status-Legende">
                    <span class="legend-item"><span class="legend-icon online"></span> Online</span>
                    <span class="legend-item"><span class="legend-icon late"></span> Später</span>
                    <span class="legend-item"><span class="legend-icon absent"></span> Verhindert</span>
                    <span class="legend-item"><span class="legend-icon vacation"></span> Urlaub</span>
                    <span class="legend-item"><span class="legend-icon open"></span> Offen</span>
                </div>
                <div class="toolbar-actions">
                    <button class="secondary-button" id="addDateButton" type="button" hidden>＋ Spieltag hinzufügen</button>
                    <button class="primary-button" id="addPlayerButton" type="button" hidden>＋ Spieler hinzufügen</button>
                </div>
            </div>
        </div>
        <div id="loading" class="loading">Wird geladen …</div>
        <div id="tableScroll" class="table-scroll" hidden>
            <table>
                <thead>
                <tr id="dateHeaderRow">
                    <th scope="col" class="player-heading">Spieler</th>
                </tr>
                </thead>
                <tbody id="planBody"></tbody>
            </table>
        </div>
        <div id="emptyState" class="empty-state">
            <strong>Noch keine Helden eingetragen.</strong>
            Füge den ersten Spieler hinzu und trage die Verfügbarkeit ein.
        </div>
    </section>

    <div class="info-link-row">
        <button class="info-icon-button" id="infoButton" type="button" title="Was soll das?" aria-label="Was soll das? Erklärung öffnen">?</button>
    </div>

</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>

<dialog id="installDialog">
    <div class="modal-content">
        <h2 class="modal-title">Kalender als App speichern</h2>
        <p class="modal-subtitle" id="installDialogSubtitle">Lege den Kellerkinder-Kalender als Symbol auf deinem Home-Bildschirm ab.</p>
        <ol class="install-steps" id="installSteps"></ol>
        <div class="modal-actions">
            <button class="primary-button" type="button" data-close-dialog="installDialog">Verstanden</button>
        </div>
    </div>
</dialog>

<dialog id="infoDialog" class="wide">
    <div class="modal-content">
        <h2 class="modal-title">So funktioniert der Online-Kalender</h2>
        <div class="instruction-grid">
            <div class="instruction-item">
                <span class="instruction-number">1</span>
                <p><strong>Account anlegen:</strong> Registriere dich mit einem Benutzernamen, einem sicheren Passwort und deinem Spieler- oder Charakternamen. Das Passwort benötigt mindestens acht Zeichen, einen Buchstaben, eine Zahl und ein Sonderzeichen.</p>
            </div>
            <div class="instruction-item">
                <span class="instruction-number">2</span>
                <p><strong>Eigene Verfügbarkeit pflegen:</strong> Nach der Anmeldung kannst du zusätzliche Spieltage anlegen und ausschließlich deine eigene Spielerzeile bearbeiten. Für jeden Termin stehen „Online“, „Später“, „Verhindert“, „Urlaub“ und „Offen“ zur Auswahl. Zusätzlich kannst du angeben, welches Spiel du spielen möchtest und einen kurzen Hinweis ergänzen.</p>
            </div>
            <div class="instruction-item">
                <span class="instruction-number">3</span>
                <p><strong>Feste Wochentage einstellen:</strong> In „Mein Account“ kannst du Wochentage markieren, an denen du normalerweise online bist. Künftige Termine an diesen Tagen werden automatisch als „Online“ vorbelegt und lassen sich einzeln überschreiben.</p>
            </div>
            <div class="instruction-item">
                <span class="instruction-number">4</span>
                <p><strong>Administration:</strong> Administratoren verwalten Benutzerkonten, Spieler, zusätzliche Spieltage und sämtliche Statusangaben. Alle angemeldeten Benutzer dürfen neue Spieltage anlegen; löschen kann sie weiterhin nur ein Administrator. Wird ein Passwort durch einen Admin geändert, muss der Benutzer nach der nächsten Anmeldung ein eigenes neues Passwort festlegen.</p>
            </div>
        </div>
        <p class="editing-note"><strong>Geschützte Bearbeitung:</strong> Die Kalenderübersicht bleibt für alle Besucher sichtbar. Angemeldete Benutzer dürfen zusätzliche Spieltage anlegen und ausschließlich den eigenen Spieler bearbeiten. Administratoren können außerdem Termine löschen und besitzen vollständige Verwaltungsrechte.</p>
        <div class="discord-note">
            <h3>💬 Auch in Discord nutzbar</h3>
            <p>Im Kellerkinder-Discord könnt ihr den aktuellen Kalender jederzeit mit dem Befehl <code>/kalender</code> aufrufen und anzeigen lassen.</p>
        </div>
        <div class="modal-actions">
            <button class="primary-button" type="button" data-close-dialog="infoDialog">Verstanden</button>
        </div>
    </div>
</dialog>

<dialog id="loginDialog">
    <form class="modal-content form-stack" id="loginForm" method="dialog">
        <div>
            <h2 class="modal-title">Anmelden</h2>
            <p class="modal-subtitle">Melde dich an, um die Kellerkinder-Seite voll zu nutzen: Kalender, Netzje, Eindrücke und alles, was nur für Mitglieder ist.</p>
        </div>
        <div>
            <label for="loginUsername">Benutzername</label>
            <input type="text" id="loginUsername" autocomplete="username" required>
        </div>
        <div>
            <label for="loginPassword">Passwort</label>
            <input type="password" id="loginPassword" autocomplete="current-password" required>
        </div>
        <label class="remember-row">
            <input type="checkbox" id="loginRemember">
            <span>Angemeldet bleiben auf diesem Gerät</span>
        </label>
        <p class="login-register-hint">Noch keinen Account? <button class="text-button" id="loginToRegisterButton" type="button">Account anlegen</button></p>
        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="loginDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Anmelden</button>
        </div>
    </form>
</dialog>

<dialog id="registerDialog">
    <form class="modal-content form-stack" id="registerForm" method="dialog">
        <div>
            <h2 class="modal-title" id="registerDialogTitle">Account anlegen</h2>
            <p class="modal-subtitle" id="registerDialogSubtitle">Lege deinen persönlichen Zugang zur Kellerkinder-Seite an.</p>
        </div>
        <div>
            <label for="registerUsername">Benutzername</label>
            <input type="text" id="registerUsername" autocomplete="username" required>
        </div>
        <div>
            <label for="registerPlayerName">Spielername</label>
            <input type="text" id="registerPlayerName" maxlength="40" autocomplete="nickname" required>
            <p class="field-help">Existiert dieser Spieler bereits ohne Account, wird er mit deinem neuen Account verbunden.</p>
        </div>
        <div class="form-row">
            <div>
                <label for="registerPassword">Passwort</label>
                <input type="password" id="registerPassword" minlength="8" autocomplete="new-password" required>
            </div>
            <div>
                <label for="registerPasswordConfirmation">Passwort wiederholen</label>
                <input type="password" id="registerPasswordConfirmation" minlength="8" autocomplete="new-password" required>
            </div>
        </div>
        <ul class="password-checklist" id="registerPasswordChecklist">
            <li data-rule="length">Mindestens 8 Zeichen</li>
            <li data-rule="letter">Mindestens ein Buchstabe</li>
            <li data-rule="number">Mindestens eine Zahl</li>
            <li data-rule="special">Mindestens ein Sonderzeichen (z. B. <code>! ? # + - _ @ €</code>)</li>
            <li data-rule="match">Beide Passwörter stimmen überein</li>
        </ul>
        <label class="remember-row">
            <input type="checkbox" id="registerRemember" checked>
            <span>Angemeldet bleiben auf diesem Gerät</span>
        </label>
        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="registerDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Account anlegen</button>
        </div>
    </form>
</dialog>

<dialog id="profileDialog">
    <form class="modal-content form-stack" id="profileForm" method="dialog">
        <div>
            <h2 class="modal-title">Mein Account</h2>
            <p class="modal-subtitle">Verwalte deinen Spielernamen und deine üblichen Online-Tage.</p>
        </div>
        <div>
            <label>Benutzername</label>
            <input type="text" id="profileUsername" disabled>
        </div>
        <div>
            <label for="profilePlayerName">Spielername</label>
            <input type="text" id="profilePlayerName" maxlength="40" autocomplete="nickname" required>
        </div>
        <div>
            <label for="profileSteamName">Steam-Name (optional)</label>
            <input type="text" id="profileSteamName" maxlength="120" autocomplete="off" placeholder="z. B. fischje oder steamcommunity.com/id/fischje">
            <p class="field-help">Profilname, SteamID oder Link zum Steam-Profil. Für die Steam-Erfolge müssen Profil und Spieldetails auf Steam öffentlich sein (Steam → Profil bearbeiten → Privatsphäre).</p>
        </div>
        <div>
            <label for="profileAvatarInput">Profilbild / Avatar</label>
            <div class="avatar-upload-row">
                <span class="avatar-placeholder avatar-preview" id="profileAvatarPreview" aria-hidden="true">?</span>
                <div>
                    <input type="file" id="profileAvatarInput" accept="image/png,image/jpeg,image/gif,image/webp">
                    <p class="field-help">PNG, JPG, GIF oder WebP. Wird automatisch auf maximal 50 × 50 Pixel verkleinert und in der Tabelle klein neben deinem Namen angezeigt.</p>
                    <button class="secondary-button" id="removeAvatarButton" type="button">Avatar entfernen</button>
                </div>
            </div>
        </div>
        <div>
            <label>Normalerweise online an</label>
            <div class="weekday-grid" id="profileWeekdays">
                <label class="weekday-choice"><input type="checkbox" value="1"><span>Mo</span></label>
                <label class="weekday-choice"><input type="checkbox" value="2"><span>Di</span></label>
                <label class="weekday-choice"><input type="checkbox" value="3"><span>Mi</span></label>
                <label class="weekday-choice"><input type="checkbox" value="4"><span>Do</span></label>
                <label class="weekday-choice"><input type="checkbox" value="5"><span>Fr</span></label>
                <label class="weekday-choice"><input type="checkbox" value="6"><span>Sa</span></label>
                <label class="weekday-choice"><input type="checkbox" value="7"><span>So</span></label>
            </div>
            <p class="field-help">Künftige Spieltage an diesen Wochentagen werden automatisch als „Online“ angezeigt. Ein einzelner Termin kann jederzeit überschrieben werden.</p>
        </div>
        <div>
            <label>Urlaub eintragen</label>
            <div class="form-row">
                <div>
                    <label for="vacationFrom" class="field-sublabel">Von</label>
                    <input type="date" id="vacationFrom">
                </div>
                <div>
                    <label for="vacationTo" class="field-sublabel">Bis</label>
                    <input type="date" id="vacationTo">
                </div>
            </div>
            <div class="vacation-actions">
                <button class="secondary-button" id="setVacationButton" type="button">Urlaub eintragen</button>
                <button class="secondary-button" id="clearVacationButton" type="button">Urlaub entfernen</button>
            </div>
            <p class="field-help">Setzt alle Spieltage im gewählten Zeitraum auf „Urlaub“ — aber nur die, die aktuell im Kalender sichtbar sind. „Urlaub entfernen“ nimmt im selben Zeitraum nur deine Urlaubseinträge zurück; andere Angaben bleiben bestehen.</p>
        </div>
        <div class="modal-actions">
            <button class="secondary-button" id="openPasswordButton" type="button">Passwort ändern</button>
            <button class="secondary-button" type="button" data-close-dialog="profileDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Profil speichern</button>
        </div>
    </form>
</dialog>

<dialog id="passwordDialog">
    <form class="modal-content form-stack" id="passwordForm" method="dialog">
        <div>
            <h2 class="modal-title">Passwort ändern</h2>
            <p class="modal-subtitle" id="passwordDialogSubtitle">Lege ein neues Passwort fest.</p>
        </div>
        <div id="forcedPasswordNote" class="forced-password-note" hidden>
            Ein Administrator hat dein Passwort geändert. Bevor du den Kalender wieder bearbeiten kannst, musst du ein eigenes neues Passwort festlegen.
        </div>
        <div id="currentPasswordField">
            <label for="currentPassword">Aktuelles Passwort</label>
            <input type="password" id="currentPassword" autocomplete="current-password">
        </div>
        <div class="form-row">
            <div>
                <label for="newPassword">Neues Passwort</label>
                <input type="password" id="newPassword" minlength="8" autocomplete="new-password" required>
            </div>
            <div>
                <label for="newPasswordConfirmation">Neues Passwort wiederholen</label>
                <input type="password" id="newPasswordConfirmation" minlength="8" autocomplete="new-password" required>
            </div>
        </div>
        <p class="field-help"><strong>Passwortregel:</strong> Mindestens 8 Zeichen sowie mindestens ein Buchstabe, eine Zahl und ein Sonderzeichen, zum Beispiel <code>! ? # + - _ @ €</code>. Umlaute wie ä, ö und ü zählen als Buchstaben.</p>
        <div class="modal-actions">
            <button class="secondary-button" id="cancelPasswordButton" type="button" data-close-dialog="passwordDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Passwort speichern</button>
        </div>
    </form>
</dialog>

<dialog id="playerDialog">
    <form class="modal-content" id="playerForm" method="dialog">
        <h2 class="modal-title" id="playerDialogTitle">Spieler hinzufügen</h2>
        <p class="modal-subtitle" id="playerDialogSubtitle">Trage den Charakternamen ein.</p>
        <input type="hidden" id="playerId">
        <label for="playerName">Spielername</label>
        <input type="text" id="playerName" maxlength="40" autocomplete="off" required>
        <div class="modal-actions">
            <button class="danger-button" id="deletePlayerButton" type="button" hidden>Spieler löschen</button>
            <button class="secondary-button" type="button" data-close-dialog="playerDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Speichern</button>
        </div>
    </form>
</dialog>

<dialog id="dateDialog">
    <form class="modal-content" id="dateForm" method="dialog">
        <h2 class="modal-title">Spieltag hinzufügen</h2>
        <p class="modal-subtitle">Wähle einen zusätzlichen Spieltag. Tage, an denen jemand einen festen Wochentag hinterlegt oder bereits einen Status gesetzt hat, erscheinen ohnehin automatisch.</p>
        <label for="eventDateInput">Datum</label>
        <input type="date" id="eventDateInput" required>
        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="dateDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Spieltag anlegen</button>
        </div>
    </form>
</dialog>

<dialog id="statusDialog">
    <form class="modal-content" id="statusForm" method="dialog">
        <h2 class="modal-title" id="statusDialogTitle">Verfügbarkeit</h2>
        <p class="modal-subtitle" id="statusDialogSubtitle"></p>
        <input type="hidden" id="statusPlayerId">
        <input type="hidden" id="statusDate">
        <input type="hidden" id="selectedStatus" value="">

        <div class="status-grid" role="group" aria-label="Status auswählen">
            <button class="status-choice" type="button" data-status="online"><span>⚔</span>Online</button>
            <button class="status-choice" type="button" data-status="late"><span>◷</span>Später</button>
            <button class="status-choice" type="button" data-status="absent"><span>✕</span>Verhindert</button>
            <button class="status-choice" type="button" data-status="vacation"><span>☀</span>Urlaub</button>
            <button class="status-choice" type="button" data-status=""><span>?</span>Offen</button>
        </div>

        <div>
            <label for="statusGame">Was möchtest du spielen? <small>(optional)</small></label>
            <input type="text" id="statusGame" list="gameOptions" maxlength="60" placeholder="z. B. WoW, Diablo 4 oder Factorio" autocomplete="off">
            <datalist id="gameOptions"></datalist>
            <p class="field-help">Du kannst ein neues Spiel eintragen oder einen bereits genannten Titel aus der Liste auswählen.</p>
        </div>

        <label for="statusNote">Hinweis <small>(optional)</small></label>
        <input type="text" id="statusNote" maxlength="60" placeholder="z. B. ab 21:00 Uhr" autocomplete="off">

        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="statusDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Speichern</button>
        </div>
    </form>
</dialog>

<div class="toast" id="toast" role="status" aria-live="polite"></div>

<script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
    const STATUS_META = {
        online: { icon: '⚔', label: 'Online', className: 'online' },
        late: { icon: '◷', label: 'Später', className: 'late' },
        absent: { icon: '✕', label: 'Verhindert', className: 'absent' },
        vacation: { icon: '☀', label: 'Urlaub', className: 'vacation' },
        '': { icon: '?', label: 'Offen', className: 'open' }
    };

    const THEME_COLORS = {
        default: '#1b1f27',
        summer: '#06383d',
        winter: '#07182b'
    };

    const state = {
        players: [],
        availability: {},
        gameOptions: [],
        eventDates: [],
        auth: { logged_in: false, setup_required: false, is_admin: false, must_change_password: false, can_write: false, user: null },
        admin: null,
        settings: { theme: 'default' },
        csrf: '',
        storageWritable: true
    };

    const byId = id => document.getElementById(id);
    const loading = byId('loading');
    const tableScroll = byId('tableScroll');
    const planBody = byId('planBody');
    const dateHeaderRow = byId('dateHeaderRow');
    const emptyState = byId('emptyState');
    const toast = byId('toast');
    const storageWarning = byId('storageWarning');
    const setupCallout = byId('setupCallout');
    const passwordCallout = byId('passwordCallout');

    const installDialog = byId('installDialog');
    const infoDialog = byId('infoDialog');
    const loginDialog = byId('loginDialog');
    const registerDialog = byId('registerDialog');
    const profileDialog = byId('profileDialog');
    const passwordDialog = byId('passwordDialog');
    const playerDialog = byId('playerDialog');
    const dateDialog = byId('dateDialog');
    const statusDialog = byId('statusDialog');

    const playerForm = byId('playerForm');
    const playerId = byId('playerId');
    const playerName = byId('playerName');
    const deletePlayerButton = byId('deletePlayerButton');
    const dateForm = byId('dateForm');
    const eventDateInput = byId('eventDateInput');
    const statusForm = byId('statusForm');
    const statusGame = byId('statusGame');
    const gameOptionsList = byId('gameOptions');
    const statusNote = byId('statusNote');
    const selectedStatus = byId('selectedStatus');
    let toastTimer;
    let passwordChangeForced = false;
    let deferredInstallPrompt = null;
    let profileAvatarData = '';

    function playerInitial(name) {
        const clean = String(name || '').trim();
        return clean ? clean.slice(0, 1).toUpperCase() : '?';
    }

    function createAvatarElement(src, name, className = '') {
        if (src) {
            const image = document.createElement('img');
            image.className = `avatar ${className}`.trim();
            image.src = src;
            image.alt = `${name || 'Spieler'} Avatar`;
            image.loading = 'lazy';
            image.decoding = 'async';
            return image;
        }

        const placeholder = document.createElement('span');
        placeholder.className = `avatar-placeholder ${className}`.trim();
        placeholder.textContent = playerInitial(name);
        placeholder.setAttribute('aria-hidden', 'true');
        return placeholder;
    }

    function setAvatarPreview(src, name) {
        const preview = byId('profileAvatarPreview');
        const replacement = createAvatarElement(src, name || byId('profilePlayerName').value, 'avatar-preview');
        replacement.id = 'profileAvatarPreview';
        preview.replaceWith(replacement);
    }

    function readImageFile(file) {
        return new Promise((resolve, reject) => {
            if (!file || !file.type.startsWith('image/')) {
                reject(new Error('Bitte wähle eine Bilddatei aus.'));
                return;
            }

            const reader = new FileReader();
            reader.onerror = () => reject(new Error('Das Bild konnte nicht gelesen werden.'));
            reader.onload = () => {
                const image = new Image();
                image.onerror = () => reject(new Error('Das Bild konnte nicht verarbeitet werden.'));
                image.onload = () => {
                    const scale = Math.min(1, 50 / image.naturalWidth, 50 / image.naturalHeight);
                    const width = Math.max(1, Math.round(image.naturalWidth * scale));
                    const height = Math.max(1, Math.round(image.naturalHeight * scale));
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    const context = canvas.getContext('2d');
                    context.clearRect(0, 0, width, height);
                    context.drawImage(image, 0, 0, width, height);
                    resolve(canvas.toDataURL('image/png'));
                };
                image.src = String(reader.result || '');
            };
            reader.readAsDataURL(file);
        });
    }

    function showToast(message) {
        // Ein offenes Dialogfenster liegt über allem: den Hinweis dort hinein, sonst bleibt er unsichtbar
        const host = document.querySelector('dialog[open]') || document.body;
        if (toast.parentElement !== host) host.append(toast);
        toast.textContent = message;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 3200);
    }

    async function api(action, payload = null, extraQuery = null) {
        const options = payload === null
            ? { headers: { Accept: 'application/json' }, credentials: 'same-origin' }
            : {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': state.csrf
                },
                body: JSON.stringify({ action, ...payload })
            };

        let url = payload === null ? `api.php?action=${encodeURIComponent(action)}` : 'api.php';
        if (payload === null && extraQuery) {
            for (const [key, value] of Object.entries(extraQuery)) {
                url += `&${encodeURIComponent(key)}=${encodeURIComponent(value)}`;
            }
        }
        const response = await fetch(url, options);
        const raw = await response.text();
        let data;

        try {
            data = raw ? JSON.parse(raw) : {};
        } catch (error) {
            throw new Error(`Der Server liefert keine gültige PHP-Antwort (HTTP ${response.status}). Prüfe, ob PHP für diese Website aktiviert ist.`);
        }

        if (!response.ok || data.ok === false) {
            const apiError = new Error(data.error || 'Die Anfrage konnte nicht verarbeitet werden.');
            apiError.code = data.code || '';
            apiError.status = response.status;
            throw apiError;
        }

        return data;
    }

    function applyData(data) {
        state.players = data.players || [];
        state.availability = data.availability || {};
        state.gameOptions = data.game_options || [];
        state.eventDates = data.event_dates || [];
        state.settings = data.settings || state.settings;
        applyTheme(state.settings.theme);
        renderGameOptions();
        state.auth = data.auth || state.auth;
        state.admin = data.admin || null;
        state.csrf = data.csrf_token || state.csrf;
        state.storageWritable = data.storage_writable !== false;
        storageWarning.hidden = state.storageWritable;
        loading.hidden = true;
        renderAuth();
        renderPlan();
        // Kopfleiste (Account-Knopf, Menüpunkt „Admin“) nachziehen, wenn sich die Anmeldung geändert hat
        if (window.kkRefreshAccount && state.lastLoggedIn !== state.auth.logged_in) window.kkRefreshAccount();
        state.lastLoggedIn = state.auth.logged_in;
        openDialogFromHash();
    }

    function applyTheme(theme) {
        const normalized = Object.prototype.hasOwnProperty.call(THEME_COLORS, theme) ? theme : 'default';
        document.body.dataset.theme = normalized;
        byId('themeColorMeta')?.setAttribute('content', THEME_COLORS[normalized]);
    }

    function renderGameOptions() {
        gameOptionsList.replaceChildren();
        for (const game of state.gameOptions) {
            const option = document.createElement('option');
            option.value = game;
            gameOptionsList.appendChild(option);
        }
    }

    function isInstalledApp() {
        return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    }

    function showInstallInstructions() {
        const isIos = /iphone|ipad|ipod/i.test(navigator.userAgent);
        const steps = isIos
            ? [
                'Öffne diese Seite in Safari.',
                'Tippe in Safari auf „Teilen“.',
                'Wähle „Zum Home-Bildschirm hinzufügen“, aktiviere „Als Web-App öffnen“ und tippe auf „Hinzufügen“.'
            ]
            : [
                'Öffne das Browsermenü oben rechts.',
                'Wähle „App installieren“ oder „Zum Startbildschirm hinzufügen“.',
                'Bestätige die Installation. Danach erscheint das Kellerkinder-Symbol wie eine App auf deinem Home-Bildschirm.'
            ];
        byId('installDialogSubtitle').textContent = isIos
            ? 'Auf dem iPhone wird die Web-App über das Teilen-Menü in Safari installiert.'
            : 'Falls dein Browser keinen direkten Installationsdialog anbietet, nutze diese Schritte.';
        const list = byId('installSteps');
        list.replaceChildren();
        for (const text of steps) {
            const item = document.createElement('li');
            item.textContent = text;
            list.appendChild(item);
        }
        installDialog.showModal();
    }

    async function installApp() {
        if (isInstalledApp()) {
            showToast('Der Kalender ist bereits als App geöffnet.');
            return;
        }
        if (deferredInstallPrompt) {
            deferredInstallPrompt.prompt();
            const choice = await deferredInstallPrompt.userChoice;
            deferredInstallPrompt = null;
            if (choice.outcome === 'accepted') {
                showToast('Der Kellerkinder-Kalender wurde installiert.');
            }
            return;
        }
        showInstallInstructions();
    }

    function computeDesiredDayCount() {
        const boardEl = document.querySelector('.board');
        const minDays = 4;
        const normalDays = 8;
        if (!boardEl) return normalDays;
        const isMobile = window.innerWidth <= 680;
        const boardPadding = isMobile ? 28 : 44;
        const playerColWidth = isMobile ? 118 : 150;
        const dateColWidth = isMobile ? 58 : 75;
        const available = boardEl.clientWidth - boardPadding - playerColWidth;
        const fitting = Math.floor(available / dateColWidth);
        // Genug Platz für mehr als die normalen 8 Spalten? Dann bis zum Rand auffüllen.
        if (fitting >= normalDays) return fitting;
        // Sonst so viele wie reinpassen, aber nie unter dem Minimum von 4.
        return Math.max(minDays, fitting);
    }

    function getVisibleEventDates() {
        const pastDate = state.eventDates.find(d => d.is_past);
        const futureDates = state.eventDates.filter(d => !d.is_past);
        const desiredFuture = computeDesiredDayCount();
        const futureToShow = futureDates.slice(0, Math.max(4, Math.min(desiredFuture, futureDates.length)));
        return pastDate ? [pastDate, ...futureToShow] : futureToShow;
    }

    async function loadPlan() {
        try {
            applyData(await api('bootstrap'));
        } catch (error) {
            loading.textContent = 'Der Plan konnte nicht geladen werden.';
            showToast(error.message);
        }
    }

    function handleApiError(error) {
        if (error.code === 'storage_not_writable') storageWarning.hidden = false;
        if (error.code === 'password_change_required') openPasswordDialog(true);
        if (error.status === 419) loadPlan();
        showToast(error.message);
    }

    function parseLocalDate(isoDate) {
        return new Date(`${isoDate}T12:00:00`);
    }

    function formatDateParts(isoDate) {
        const date = parseLocalDate(isoDate);
        return {
            day: new Intl.DateTimeFormat('de-DE', { weekday: 'short' }).format(date).replace('.', ''),
            value: new Intl.DateTimeFormat('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' }).format(date),
            label: new Intl.DateTimeFormat('de-DE', { weekday: 'short', day: '2-digit', month: '2-digit', year: 'numeric' }).format(date)
        };
    }

    function evaluatePasswordRules(password, confirmation) {
        const characters = [...password];
        return {
            length: characters.length >= 8,
            letter: characters.some(character => /^\p{L}$/u.test(character)),
            number: characters.some(character => /^\p{N}$/u.test(character)),
            special: characters.some(character =>
                !/^\p{L}$/u.test(character)
                && !/^\p{N}$/u.test(character)
                && !/^\s$/u.test(character)
            ),
            match: password !== '' && password === confirmation,
        };
    }

    function updatePasswordChecklist(prefix, markUnmet = false) {
        const password = byId(`${prefix}Password`).value;
        const confirmation = byId(`${prefix}PasswordConfirmation`).value;
        const rules = evaluatePasswordRules(password, confirmation);
        const list = byId(`${prefix}PasswordChecklist`);
        if (list) {
            for (const [rule, passed] of Object.entries(rules)) {
                const item = list.querySelector(`[data-rule="${rule}"]`);
                if (!item) continue;
                item.classList.toggle('met', passed);
                item.classList.toggle('unmet', markUnmet && !passed);
            }
        }
        return rules;
    }

    function resetPasswordChecklist(prefix) {
        const list = byId(`${prefix}PasswordChecklist`);
        if (!list) return;
        list.querySelectorAll('li').forEach(item => item.classList.remove('met', 'unmet'));
        byId(`${prefix}Password`)?.classList.remove('field-invalid');
        byId(`${prefix}PasswordConfirmation`)?.classList.remove('field-invalid');
    }

    function passwordValidationMessage(password, confirmation = null) {
        const characters = [...password];
        const hasLetter = characters.some(character => /^\p{L}$/u.test(character));
        const hasNumber = characters.some(character => /^\p{N}$/u.test(character));
        const hasSpecial = characters.some(character =>
            !/^\p{L}$/u.test(character)
            && !/^\p{N}$/u.test(character)
            && !/^\s$/u.test(character)
        );

        if (characters.length < 8) return 'Das Passwort muss mindestens 8 Zeichen lang sein.';
        if (!hasLetter) return 'Das Passwort muss mindestens einen Buchstaben enthalten.';
        if (!hasNumber) return 'Das Passwort muss mindestens eine Zahl enthalten.';
        if (!hasSpecial) return 'Das Passwort muss mindestens ein Sonderzeichen wie !, ?, #, +, -, _, @ oder € enthalten. Umlaute gelten als Buchstaben.';
        if (confirmation !== null && password !== confirmation) return 'Die beiden Passwörter stimmen nicht überein.';
        return '';
    }

    function renderAuth() {
        const auth = state.auth;
        setupCallout.hidden = !auth.setup_required;
        passwordCallout.hidden = !auth.must_change_password;

        byId('addDateButton').hidden = !auth.can_write;
        byId('addPlayerButton').hidden = !auth.is_admin || auth.must_change_password;

        if (auth.must_change_password && !passwordDialog.open) {
            setTimeout(() => openPasswordDialog(true), 50);
        }
    }

    function renderDateHeaders() {
        while (dateHeaderRow.children.length > 1) dateHeaderRow.lastElementChild.remove();

        for (const eventDate of getVisibleEventDates()) {
            const parts = formatDateParts(eventDate.date);
            const heading = document.createElement('th');
            heading.scope = 'col';
            heading.dataset.date = eventDate.date;
            if (eventDate.is_today) heading.classList.add('is-today');

            const day = document.createElement('span');
            day.className = 'date-day';
            day.textContent = parts.day;
            const value = document.createElement('span');
            value.className = 'date-value';
            value.textContent = parts.value;
            heading.append(day, value);

            const today = document.createElement('span');
            today.className = 'today-tag';
            today.textContent = 'Heute';
            heading.appendChild(today);

            if (eventDate.netzje) {
                // LAN-Party: Tag rot markiert, Link zur Seite „Netzje“.
                heading.classList.add('is-netzje');
                const netzje = document.createElement('a');
                netzje.className = 'netzje-tag';
                netzje.href = 'netzje.php';
                netzje.textContent = '🎉 Netzje';
                netzje.title = `${eventDate.netzje.title} – zur Netzje-Seite`;
                heading.appendChild(netzje);
            }

            if (eventDate.is_past) {
                const past = document.createElement('span');
                past.className = 'past-tag';
                past.textContent = 'Vergangen';
                heading.appendChild(past);
            }

            if (eventDate.is_custom && state.auth.is_admin && !state.auth.must_change_password) {
                const removeButton = document.createElement('button');
                removeButton.type = 'button';
                removeButton.className = 'date-remove';
                removeButton.textContent = '✕';
                removeButton.title = `${parts.label} entfernen`;
                removeButton.setAttribute('aria-label', `${parts.label} als Spieltag entfernen`);
                removeButton.addEventListener('click', () => deleteEventDate(eventDate.date, parts.label));
                heading.appendChild(removeButton);
            }

            dateHeaderRow.appendChild(heading);
        }
    }

    function renderPlan() {
        planBody.replaceChildren();
        renderDateHeaders();

        if (state.players.length === 0) {
            tableScroll.hidden = true;
            emptyState.style.display = 'block';
            emptyState.querySelector('strong').textContent = state.auth.is_admin ? 'Noch keine Spieler eingetragen.' : 'Noch keine Spieler eingetragen.';
            return;
        }

        emptyState.style.display = 'none';
        tableScroll.hidden = false;

        for (const player of state.players) {
            const row = document.createElement('tr');
            const playerCell = document.createElement('th');
            playerCell.scope = 'row';
            playerCell.className = 'player-cell';

            const playerButton = document.createElement('button');
            playerButton.type = 'button';
            playerButton.className = 'player-button';
            const title = document.createElement('span');
            title.className = 'player-title';
            title.appendChild(createAvatarElement(player.avatar || '', player.name));
            const nameText = document.createElement('span');
            nameText.className = 'player-name';
            nameText.textContent = player.name;
            title.appendChild(nameText);
            if (player.is_own) {
                const badge = document.createElement('span');
                badge.className = 'player-badge';
                badge.textContent = 'Du';
                title.appendChild(badge);
            }
            playerButton.appendChild(title);
            const playerHint = document.createElement('small');
            if (player.can_edit) {
                playerHint.textContent = state.auth.is_admin ? 'Antippen zum Bearbeiten' : 'Dein Spieler · Account öffnen';
                playerButton.addEventListener('click', () => state.auth.is_admin ? openPlayerDialog(player) : openProfileDialog());
            } else {
                playerHint.textContent = player.has_account ? 'Durch Account geschützt' : 'Noch keinem Account zugeordnet';
                playerButton.classList.add('readonly');
                playerButton.disabled = true;
            }
            playerButton.appendChild(playerHint);
            playerCell.appendChild(playerButton);
            row.appendChild(playerCell);

            for (const eventDate of getVisibleEventDates()) {
                const date = eventDate.date;
                const dateLabel = formatDateParts(date).label;
                const cell = document.createElement('td');
                cell.className = 'status-cell';
                if (eventDate.is_today) cell.classList.add('is-today');
                if (eventDate.netzje) cell.classList.add('is-netzje');
                const entry = state.availability[`${player.id}:${date}`] || { status: '', note: '', game: '', source: '' };
                const meta = STATUS_META[entry.status] || STATUS_META[''];

                const button = document.createElement('button');
                button.type = 'button';
                button.className = `status-button ${meta.className}`;
                const entryDetails = [entry.game ? `Spiel: ${entry.game}` : '', entry.note || ''].filter(Boolean).join(', ');
                button.setAttribute('aria-label', `${player.name}, ${dateLabel}: ${meta.label}${entryDetails ? ', ' + entryDetails : ''}`);
                button.title = player.can_edit ? (entryDetails || `${meta.label} · Antippen zum Ändern`) : (entryDetails || meta.label);

                const mainBadge = document.createElement('span');
                mainBadge.className = 'status-badge status-badge-main';
                mainBadge.textContent = meta.label;
                button.appendChild(mainBadge);

                if (entry.source === 'recurring') {
                    const recurring = document.createElement('span');
                    recurring.className = 'recurring-tag';
                    recurring.textContent = 'Standard';
                    button.appendChild(recurring);
                }
                if (entry.game) {
                    const game = document.createElement('span');
                    game.className = 'status-badge status-badge-game';
                    game.textContent = entry.game;
                    button.appendChild(game);
                }
                if (entry.note) {
                    const note = document.createElement('span');
                    note.className = 'status-badge status-badge-note';
                    note.textContent = entry.note;
                    button.appendChild(note);
                }

                button.disabled = !player.can_edit || !state.storageWritable;
                if (!button.disabled) button.addEventListener('click', () => openStatusDialog(player, date, entry));
                cell.appendChild(button);
                row.appendChild(cell);
            }
            planBody.appendChild(row);
        }
    }

    function openDateDialog() {
        if (!state.auth.can_write || !state.storageWritable) return;
        eventDateInput.value = '';
        dateDialog.showModal();
        requestAnimationFrame(() => {
            eventDateInput.focus();
            if (typeof eventDateInput.showPicker === 'function') {
                try { eventDateInput.showPicker(); } catch (error) { /* Fokus reicht als Rückfall. */ }
            }
        });
    }

    async function deleteEventDate(date, label) {
        if (!confirm(`Den zusätzlichen Spieltag „${label}“ samt aller Einträge wirklich löschen?`)) return;
        try {
            applyData(await api('delete_event_date', { event_date: date }));
            showToast('Spieltag wurde gelöscht.');
        } catch (error) { handleApiError(error); }
    }

    function openPlayerDialog(player = null) {
        if (!state.auth.is_admin) return;
        const isEdit = Boolean(player);
        byId('playerDialogTitle').textContent = isEdit ? 'Spieler bearbeiten' : 'Spieler hinzufügen';
        byId('playerDialogSubtitle').textContent = isEdit
            ? 'Name ändern oder den Spieler vollständig löschen. Verknüpfte Accounts bleiben bestehen, wenn ein Spieler gelöscht wird.'
            : 'Lege einen zusätzlichen Spieler ohne Benutzerkonto an.';
        playerId.value = player?.id || '';
        playerName.value = player?.name || '';
        deletePlayerButton.hidden = !isEdit;
        playerDialog.showModal();
        requestAnimationFrame(() => playerName.focus());
    }

    function openStatusDialog(player, date, entry) {
        if (!player.can_edit) return;
        byId('statusDialogTitle').textContent = player.name;
        byId('statusDialogSubtitle').textContent = formatDateParts(date).label + (entry.source === 'recurring' ? ' · automatisch aus deinem Wochenstandard' : '');
        byId('statusPlayerId').value = player.id;
        byId('statusDate').value = date;
        selectedStatus.value = entry.status || '';
        statusGame.value = entry.game || '';
        statusNote.value = entry.note || '';
        updateStatusChoices();
        statusDialog.showModal();
    }

    function updateStatusChoices() {
        document.querySelectorAll('.status-choice').forEach(button => {
            button.classList.toggle('selected', button.dataset.status === selectedStatus.value);
        });
    }

    // Account-Knopf in der Kopfleiste (assets/sidebar.js): öffnet hier direkt die Dialoge,
    // von anderen Seiten kommt man über kalender.php#anmelden bzw. kalender.php#account.
    function openAccountKind(kind) {
        if (kind === 'login') {
            if (state.auth.logged_in) return false;
            openLoginDialog();
            return true;
        }
        if (kind === 'register') {
            if (state.auth.logged_in) return false;
            openRegisterDialog();
            return true;
        }
        if (kind === 'profile') {
            if (!state.auth.logged_in) { openLoginDialog(); return true; }
            if (state.auth.must_change_password) openPasswordDialog(true); else openProfileDialog();
            return true;
        }
        return false;
    }

    window.kkAccount = { open: openAccountKind };

    function openDialogFromHash() {
        const kind = { '#anmelden': 'login', '#registrieren': 'register', '#account': 'profile' }[window.location.hash];
        if (!kind || document.querySelector('dialog[open]')) return;
        history.replaceState(null, '', window.location.pathname + window.location.search);
        openAccountKind(kind);
    }

    window.addEventListener('hashchange', openDialogFromHash);

    function openLoginDialog() {
        byId('loginForm').reset();
        loginDialog.showModal();
        requestAnimationFrame(() => byId('loginUsername').focus());
    }

    function openRegisterDialog() {
        byId('registerForm').reset();
        resetPasswordChecklist('register');
        byId('registerDialogTitle').textContent = state.auth.setup_required ? 'Ersten Administrator anlegen' : 'Account anlegen';
        byId('registerDialogSubtitle').textContent = state.auth.setup_required
            ? 'Der erste Account erhält automatisch vollständige Administratorrechte.'
            : 'Lege deinen persönlichen Zugang zur Kellerkinder-Seite an.';
        registerDialog.showModal();
        requestAnimationFrame(() => byId('registerUsername').focus());
    }

    function openProfileDialog() {
        if (!state.auth.logged_in || state.auth.must_change_password) return;
        const user = state.auth.user || {};
        byId('profileUsername').value = user.username || '';
        byId('profilePlayerName').value = user.player_name || '';
        byId('profileSteamName').value = user.steam_profile || '';
        byId('profileAvatarInput').value = '';
        profileAvatarData = user.avatar || '';
        setAvatarPreview(profileAvatarData, user.player_name || user.username || '');
        const days = new Set((user.default_weekdays || []).map(Number));
        document.querySelectorAll('#profileWeekdays input').forEach(input => input.checked = days.has(Number(input.value)));
        const todayIso = new Date().toLocaleDateString('sv-SE');
        byId('vacationFrom').value = todayIso;
        byId('vacationTo').value = todayIso;
        byId('vacationFrom').min = todayIso;
        byId('vacationTo').min = todayIso;
        profileDialog.showModal();
        requestAnimationFrame(() => byId('profilePlayerName').focus());
    }

    function openPasswordDialog(forced = false) {
        if (!state.auth.logged_in) return;
        passwordChangeForced = forced || state.auth.must_change_password;
        byId('passwordForm').reset();
        byId('forcedPasswordNote').hidden = !passwordChangeForced;
        byId('currentPasswordField').hidden = passwordChangeForced;
        byId('currentPassword').required = !passwordChangeForced;
        byId('cancelPasswordButton').hidden = passwordChangeForced;
        byId('passwordDialogSubtitle').textContent = passwordChangeForced
            ? 'Lege jetzt ein eigenes neues Passwort fest, um den Kalender wieder bearbeiten zu können.'
            : 'Bestätige dein aktuelles Passwort und lege danach ein neues fest.';
        if (!passwordDialog.open) passwordDialog.showModal();
        requestAnimationFrame(() => (passwordChangeForced ? byId('newPassword') : byId('currentPassword')).focus());
    }

    byId('installAppButton').addEventListener('click', installApp);
    byId('infoButton').addEventListener('click', () => infoDialog.showModal());
    // Anmelden/Abmelden/Account laufen über den Account-Knopf in der Kopfleiste (assets/sidebar.js)
    byId('setupRegisterButton').addEventListener('click', openRegisterDialog);
    byId('loginToRegisterButton').addEventListener('click', () => {
        loginDialog.close();
        openRegisterDialog();
    });
    byId('registerPassword').addEventListener('input', () => {
        const rules = updatePasswordChecklist('register');
        if (rules.length && rules.letter && rules.number && rules.special) {
            byId('registerPassword').classList.remove('field-invalid');
        }
    });
    byId('registerPasswordConfirmation').addEventListener('input', () => {
        const rules = updatePasswordChecklist('register');
        if (rules.match) byId('registerPasswordConfirmation').classList.remove('field-invalid');
    });
    byId('addDateButton').addEventListener('click', openDateDialog);
    byId('addPlayerButton').addEventListener('click', () => openPlayerDialog());
    async function submitVacationRange(remove) {
        const from = byId('vacationFrom').value;
        const to = byId('vacationTo').value;
        if (!from || !to) {
            showToast('Bitte wähle Start- und Enddatum aus.');
            byId(from ? 'vacationTo' : 'vacationFrom').focus();
            return;
        }
        if (to < from) {
            showToast('Das Enddatum liegt vor dem Startdatum.');
            byId('vacationTo').focus();
            return;
        }
        const buttons = [byId('setVacationButton'), byId('clearVacationButton')];
        buttons.forEach(button => { button.disabled = true; });
        try {
            const data = await api('set_vacation_range', { from, to, remove });
            applyData(data);
            const days = data.vacation_days || 0;
            if (days === 0) {
                showToast(remove
                    ? 'In diesem Zeitraum war kein Urlaub eingetragen.'
                    : 'In diesem Zeitraum liegt aktuell kein sichtbarer Spieltag.');
            } else {
                const label = days === 1 ? 'ein Spieltag' : `${days} Spieltage`;
                showToast(remove
                    ? `Urlaub für ${label} entfernt.`
                    : `Urlaub für ${label} eingetragen.`);
            }
        } catch (error) {
            handleApiError(error);
        } finally {
            buttons.forEach(button => { button.disabled = false; });
        }
    }

    byId('setVacationButton').addEventListener('click', () => submitVacationRange(false));
    byId('clearVacationButton').addEventListener('click', () => submitVacationRange(true));

    byId('openPasswordButton').addEventListener('click', () => {
        profileDialog.close();
        openPasswordDialog(false);
    });
    byId('removeAvatarButton').addEventListener('click', () => {
        profileAvatarData = '';
        byId('profileAvatarInput').value = '';
        setAvatarPreview('', byId('profilePlayerName').value);
    });
    byId('profileAvatarInput').addEventListener('change', async event => {
        const file = event.target.files?.[0];
        if (!file) return;
        try {
            profileAvatarData = await readImageFile(file);
            setAvatarPreview(profileAvatarData, byId('profilePlayerName').value);
            showToast('Avatar wurde vorbereitet.');
        } catch (error) {
            profileAvatarData = state.auth.user?.avatar || '';
            setAvatarPreview(profileAvatarData, byId('profilePlayerName').value);
            byId('profileAvatarInput').value = '';
            showToast(error.message || 'Das Bild konnte nicht verarbeitet werden.');
        }
    });
    byId('profilePlayerName').addEventListener('input', () => {
        if (!profileAvatarData) setAvatarPreview('', byId('profilePlayerName').value);
    });

    document.querySelectorAll('[data-close-dialog]').forEach(button => {
        button.addEventListener('click', () => {
            const dialog = byId(button.dataset.closeDialog);
            if (dialog === passwordDialog && passwordChangeForced) return;
            dialog.close();
        });
    });

    document.querySelectorAll('.status-choice').forEach(button => {
        button.addEventListener('click', () => {
            selectedStatus.value = button.dataset.status;
            updateStatusChoices();
        });
    });

    byId('loginForm').addEventListener('submit', async event => {
        event.preventDefault();
        try {
            const data = await api('login', {
                username: byId('loginUsername').value,
                password: byId('loginPassword').value,
                remember: byId('loginRemember').checked
            });
            loginDialog.close();
            applyData(data);
            showToast('Anmeldung erfolgreich.');
        } catch (error) { handleApiError(error); }
    });

    byId('registerForm').addEventListener('submit', async event => {
        event.preventDefault();
        const password = byId('registerPassword').value;
        const passwordConfirmation = byId('registerPasswordConfirmation').value;
        const rules = updatePasswordChecklist('register', true);
        const complexityOk = rules.length && rules.letter && rules.number && rules.special;

        byId('registerPassword').classList.toggle('field-invalid', !complexityOk);
        byId('registerPasswordConfirmation').classList.toggle('field-invalid', !rules.match);

        if (!complexityOk || !rules.match) {
            showToast('Bitte prüfe die rot markierten Passwort-Anforderungen unten im Formular.');
            (complexityOk ? byId('registerPasswordConfirmation') : byId('registerPassword')).focus();
            return;
        }

        try {
            const data = await api('register', {
                username: byId('registerUsername').value,
                player_name: byId('registerPlayerName').value,
                password,
                password_confirmation: passwordConfirmation,
                remember: byId('registerRemember').checked
            });
            registerDialog.close();
            applyData(data);
            showToast(state.auth.is_admin ? 'Erster Administrator wurde eingerichtet.' : 'Account wurde angelegt.');
        } catch (error) { handleApiError(error); }
    });

    byId('profileForm').addEventListener('submit', async event => {
        event.preventDefault();
        const weekdays = [...document.querySelectorAll('#profileWeekdays input:checked')].map(input => Number(input.value));
        try {
            const data = await api('update_profile', {
                player_name: byId('profilePlayerName').value,
                default_weekdays: weekdays,
                avatar: profileAvatarData,
                steam_name: byId('profileSteamName').value
            });
            profileDialog.close();
            applyData(data);
            showToast('Dein Account wurde gespeichert.');
        } catch (error) { handleApiError(error); }
    });

    byId('passwordForm').addEventListener('submit', async event => {
        event.preventDefault();
        const newPassword = byId('newPassword').value;
        const newPasswordConfirmation = byId('newPasswordConfirmation').value;
        const passwordError = passwordValidationMessage(newPassword, newPasswordConfirmation);
        if (passwordError) return showToast(passwordError);
        try {
            const data = await api('change_password', {
                current_password: byId('currentPassword').value,
                new_password: newPassword,
                new_password_confirmation: newPasswordConfirmation
            });
            passwordChangeForced = false;
            passwordDialog.close();
            applyData(data);
            showToast('Dein Passwort wurde geändert.');
        } catch (error) { handleApiError(error); }
    });

    dateForm.addEventListener('submit', async event => {
        event.preventDefault();
        const eventDate = eventDateInput.value;
        if (!eventDate) return eventDateInput.focus();
        try {
            applyData(await api('create_event_date', { event_date: eventDate }));
            dateDialog.close();
            showToast('Spieltag wurde hinzugefügt.');
        } catch (error) { handleApiError(error); }
    });

    playerForm.addEventListener('submit', async event => {
        event.preventDefault();
        const name = playerName.value.trim();
        if (!name) return playerName.focus();
        try {
            const action = playerId.value ? 'update_player' : 'create_player';
            const data = await api(action, { id: playerId.value || undefined, name });
            playerDialog.close();
            applyData(data);
            showToast(playerId.value ? 'Spieler wurde geändert.' : 'Spieler wurde hinzugefügt.');
        } catch (error) { handleApiError(error); }
    });

    deletePlayerButton.addEventListener('click', async () => {
        const player = state.players.find(item => String(item.id) === String(playerId.value));
        if (!player || !confirm(`„${player.name}“ samt aller Termineinträge wirklich löschen? Ein verknüpfter Account bleibt bestehen, besitzt danach aber zunächst keinen Spieler.`)) return;
        try {
            const data = await api('delete_player', { id: player.id });
            playerDialog.close();
            applyData(data);
            showToast('Spieler wurde gelöscht.');
        } catch (error) { handleApiError(error); }
    });

    statusForm.addEventListener('submit', async event => {
        event.preventDefault();
        try {
            const data = await api('set_status', {
                player_id: Number(byId('statusPlayerId').value),
                event_date: byId('statusDate').value,
                status: selectedStatus.value,
                game: statusGame.value.trim(),
                note: statusNote.value.trim()
            });
            statusDialog.close();
            applyData(data);
            showToast('Verfügbarkeit wurde gespeichert.');
        } catch (error) { handleApiError(error); }
    });

    passwordDialog.addEventListener('cancel', event => {
        if (passwordChangeForced) event.preventDefault();
    });

    [installDialog, infoDialog, loginDialog, registerDialog, profileDialog, passwordDialog, playerDialog, dateDialog, statusDialog].forEach(dialog => {
        dialog.addEventListener('click', event => {
            if (event.target !== dialog) return;
            if (dialog === passwordDialog && passwordChangeForced) return;
            dialog.close();
        });
    });

    window.addEventListener('beforeinstallprompt', event => {
        event.preventDefault();
        deferredInstallPrompt = event;
    });

    window.addEventListener('appinstalled', () => {
        deferredInstallPrompt = null;
        showToast('Der Kellerkinder-Kalender ist jetzt auf deinem Home-Bildschirm.');
    });

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('./service-worker.js').catch(() => {
                /* Die Website funktioniert auch ohne Service Worker weiter. */
            });
        });

        // Sobald ein neuer Service Worker die Kontrolle übernimmt (also nach
        // einem Update), Seite automatisch neu laden — sonst bekommen
        // Nutzer:innen ohne manuellen Hard-Refresh (Strg+F5) weiterhin die
        // alten Inhalte zu sehen.
        let reloadedForUpdate = false;
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (reloadedForUpdate) return;
            reloadedForUpdate = true;
            window.location.reload();
        });
    }

    // Herunterziehen zum Neuladen (assets/pull-refresh.js): im Kalender nur die Daten neu laden
    window.kkPullRefresh = () => loadPlan();


    let lastRequestedDayCount = computeDesiredDayCount();
    let resizeReloadTimer = null;
    window.addEventListener('resize', () => {
        clearTimeout(resizeReloadTimer);
        resizeReloadTimer = setTimeout(() => {
            const desired = computeDesiredDayCount();
            if (desired !== lastRequestedDayCount) {
                lastRequestedDayCount = desired;
                loadPlan();
            }
        }, 400);
    });

    loadPlan();
</script>
</body>
</html>
