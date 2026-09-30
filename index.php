<?php
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" id="themeColorMeta" content="#070914">
    <meta name="application-name" content="Kellerkinder">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Kellerkinder">
    <title>Kellerkinder</title>
    <meta name="description" content="Der gemeinsame Kellerkinder-Online-Kalender für eure Spieltage.">
    <link rel="icon" href="assets/kellerkinder-logo.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="assets/app-icon-180.png">
    <link rel="manifest" href="manifest.webmanifest">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com" nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>"></script>
    <script nonce="<?= htmlspecialchars($cspNonce, ENT_QUOTES, 'UTF-8') ?>">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: '#0a0b0e', panel: '#14151b', panel2: '#1a1c24',
                        line: '#262935', muted: '#8d92a3',
                        primary: '#7c5cff', primaryhi: '#9478ff', gold: '#f2b544',
                        positive: '#45d483', negative: '#ef5b6a',
                    },
                    borderRadius: { card: '16px', control: '10px' },
                }
            }
        };
    </script>
    <?php require __DIR__ . '/includes/styles.php'; ?>
</head>
<body data-theme="default">
<div id="pullRefresh" class="pull-refresh" aria-hidden="true">
    <span class="pull-refresh-badge"><span class="pull-refresh-arrow">↓</span></span>
</div>
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
<main class="page-shell">
    <header class="masthead">
        <div class="masthead-row">
            <span class="brand">
                <img src="assets/kellerkinder-logo.svg" alt="" class="brand-logo">
                <span class="brand-text">
                    <span class="brand-name-row">
                        <span class="brand-name">Kellerkinder</span>
                        <span class="brand-sun" aria-hidden="true">☀️💦</span>
                    </span>
                    <span class="subtitle">Online-Gaming mit Freunden seit <em class="shine">ewig</em></span>
                </span>
            </span>
            <nav class="main-nav" aria-label="Hauptnavigation">
                <a href="#" class="nav-link active">Kalender</a>
                <a href="blog.php" class="nav-link">Blog</a>
                <span class="nav-link disabled">Netzje <small>(folgt)</small></span>
            </nav>
            <button class="install-app-button" id="installAppButton" type="button" title="Als App zum Home-Bildschirm hinzufügen" aria-label="Kellerkinder-Kalender als App zum Home-Bildschirm hinzufügen">
                <img src="assets/smartphone-install.svg" alt="">
            </button>
        </div>
    </header>

    <section class="account-strip" aria-label="Benutzerkonto">
        <div class="account-summary" id="accountSummary">
            <strong>Wird geladen …</strong>
            <small>Die Übersicht ist öffentlich sichtbar. Änderungen erfordern ein Benutzerkonto.</small>
        </div>
        <div class="account-actions">
            <button class="compact-button" id="loginButton" type="button">Anmelden</button>
            <button class="compact-button" id="registerButton" type="button">Account anlegen</button>
            <button class="compact-button" id="profileButton" type="button" hidden>Mein Account</button>
            <button class="compact-button" id="adminButton" type="button" hidden>Adminbereich</button>
            <button class="compact-button" id="logoutButton" type="button" hidden>Abmelden</button>
        </div>
    </section>

    <div id="setupCallout" class="setup-callout" hidden>
        <strong>Ersteinrichtung:</strong> Es existiert noch kein Benutzerkonto. Der erste registrierte Account wird automatisch als Administrator eingetragen.
    </div>

    <div id="passwordCallout" class="password-callout" hidden>
        <strong>Passwortänderung erforderlich:</strong> Ein Administrator hat dein Passwort geändert. Lege jetzt ein eigenes neues Passwort fest.
    </div>

    <div id="storageWarning" class="storage-warning" hidden>
        <strong>Nur-Lese-Modus:</strong> Der Plan ist sichtbar, Änderungen können aber nicht gespeichert werden.
        Gib dem Ordner <code>data</code> Schreibrechte für den PHP-Webserver.
    </div>

    <a class="blog-teaser" id="blogTeaser" href="blog.php" hidden>
        <span class="blog-teaser-label">Neu im Blog</span>
        <span class="blog-teaser-title" id="blogTeaserTitle"></span>
        <span class="blog-teaser-meta" id="blogTeaserMeta"></span>
        <span class="blog-teaser-arrow" aria-hidden="true">→</span>
    </a>

    <section class="board" aria-label="Verfügbarkeitsplan">
        <div class="board-toolbar">
            <h2 class="board-heading">Online-Kalender</h2>
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

    <section class="achievements" aria-label="Erfolge der Kellerkinder">
        <div class="achievements-head">
            <div class="achievements-nav" id="achievementsNav" aria-label="Spielpaar wechseln">
                <button class="nav-arrow" id="achievementsPrev" type="button" aria-label="Vorheriges Spielpaar" disabled>‹</button>
                <span class="achievements-nav-label" id="achievementsNavLabel">World of Warcraft · Diablo IV</span>
                <button class="nav-arrow" id="achievementsNext" type="button" aria-label="Nächstes Spielpaar" disabled>›</button>
            </div>
        </div>

        <div class="achievement-grid" id="achievementGrid"></div>
    </section>

    <div class="info-link-row">
        <button class="info-icon-button" id="infoButton" type="button" title="Was soll das?" aria-label="Was soll das? Erklärung öffnen">?</button>
    </div>

    <footer class="site-footer">Created by Fischje with <span class="heart" aria-label="Love">♥</span> Version <?= htmlspecialchars($appVersion, ENT_QUOTES, 'UTF-8') ?></footer>
</main>

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

<dialog id="achievementEditDialog" class="wide">
    <div class="modal-content form-stack">
        <h2 class="modal-title" id="achievementEditTitle">Widget bearbeiten</h2>
        <p class="modal-subtitle">Überschrift und Inhalt für dieses Kästchen.</p>

        <div>
            <label for="achievementEditTitleInput">Überschrift</label>
            <input type="text" id="achievementEditTitleInput" maxlength="40" placeholder="Standard-Name verwenden">
        </div>

        <div id="achievementEditStatsSection">
            <label>Statistik (bis zu 8 Zeilen, je Bezeichnung + Wert)</label>
            <div id="achievementEditRows" class="achievement-edit-rows"></div>
            <button class="secondary-button" id="achievementEditAddRow" type="button">+ Zeile hinzufügen</button>
        </div>

        <div id="achievementEditLinksSection">
            <label>Links (bis zu 6, je Linktext + URL)</label>
            <div id="achievementEditLinkRows" class="achievement-edit-rows"></div>
            <button class="secondary-button" id="achievementEditAddLinkRow" type="button">+ Link hinzufügen</button>
        </div>

        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="achievementEditDialog">Abbrechen</button>
            <button class="primary-button" id="achievementEditSave" type="button">Speichern</button>
        </div>
    </div>
</dialog>

<dialog id="loginDialog">
    <form class="modal-content form-stack" id="loginForm" method="dialog">
        <div>
            <h2 class="modal-title">Anmelden</h2>
            <p class="modal-subtitle">Melde dich an, um deine eigene Verfügbarkeit zu bearbeiten.</p>
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
            <p class="modal-subtitle" id="registerDialogSubtitle">Lege deinen persönlichen Zugang zum Kalender an.</p>
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

<dialog id="adminDialog" class="wide">
    <div class="modal-content">
        <div class="admin-toolbar">
            <div>
                <h2 class="modal-title">Adminbereich</h2>
                <p class="modal-subtitle">Benutzerkonten, Adminrechte und zentrale Kalenderdaten verwalten.</p>
            </div>
            <button class="primary-button" id="createUserButton" type="button">＋ Benutzer anlegen</button>
        </div>

        <h3 class="section-heading">Benutzerkonten</h3>
        <div class="admin-user-list" id="adminUserList"></div>

        <div class="separator"></div>

        <form id="adminSettingsForm" class="form-stack">
            <div>
                <h3 class="section-heading">Style für alle</h3>
                <p class="field-help">Nur Administratoren können den globalen Style ändern. Die Auswahl gilt nach dem Speichern für alle Besucher und Benutzer.</p>
            </div>
            <div>
                <label for="adminTheme">Style auswählen</label>
                <select id="adminTheme">
                    <option value="default">Standard: RGB-Gaming</option>
                    <option value="summer">Sommer: Sonne, Strand und Wasser</option>
                    <option value="winter">Winter: Schnee und Weihnachten</option>
                </select>
            </div>

            <div class="separator"></div>

            <div>
                <h3 class="section-heading">Administratoren nach Spielername</h3>
                <p class="field-help">Diese Liste wird in der Datendatei unter <code>settings.admin_player_names</code> gespeichert. Jeder Name muss zu einem bestehenden Account gehören. Mehrere Namen mit Komma oder in einzelnen Zeilen eintragen.</p>
            </div>
            <div>
                <label for="adminPlayerNames">Admin-Spielernamen</label>
                <textarea id="adminPlayerNames" required></textarea>
            </div>
            <div class="modal-actions">
                <button class="secondary-button" type="button" data-close-dialog="adminDialog">Schließen</button>
                <button class="primary-button" type="submit">Einstellungen speichern</button>
            </div>
        </form>
    </div>
</dialog>

<dialog id="adminUserDialog">
    <form class="modal-content form-stack" id="adminUserForm" method="dialog">
        <div>
            <h2 class="modal-title" id="adminUserDialogTitle">Benutzer anlegen</h2>
            <p class="modal-subtitle" id="adminUserDialogSubtitle">Der Benutzer muss das vorläufige Passwort nach der ersten Anmeldung ändern.</p>
        </div>
        <input type="hidden" id="adminUserId">
        <div>
            <label for="adminUsername">Benutzername</label>
            <input type="text" id="adminUsername" autocomplete="off" required>
        </div>
        <div>
            <label for="adminUserPlayerName">Spielername</label>
            <input type="text" id="adminUserPlayerName" maxlength="40" autocomplete="off" required>
        </div>
        <div class="form-row">
            <div>
                <label for="adminUserPassword" id="adminUserPasswordLabel">Vorläufiges Passwort</label>
                <input type="password" id="adminUserPassword" minlength="8" autocomplete="new-password">
            </div>
            <div>
                <label for="adminUserPasswordConfirmation">Passwort wiederholen</label>
                <input type="password" id="adminUserPasswordConfirmation" minlength="8" autocomplete="new-password">
            </div>
        </div>
        <p class="field-help" id="adminPasswordHelp">Beim Anlegen ist ein Passwort mit mindestens 8 Zeichen, einem Buchstaben, einer Zahl und einem Sonderzeichen wie !, ?, #, +, -, _, @ oder € erforderlich. Umlaute zählen als Buchstaben. Der Benutzer wird nach der ersten Anmeldung zur Änderung aufgefordert.</p>
        <label class="remember-row">
            <input type="checkbox" id="adminUserIsAuthor">
            <span>Autorenrecht — darf Blogbeiträge schreiben und bearbeiten</span>
        </label>
        <div class="modal-actions">
            <button class="danger-button" id="deleteUserButton" type="button" hidden>Benutzer löschen</button>
            <button class="secondary-button" type="button" data-close-dialog="adminUserDialog">Abbrechen</button>
            <button class="primary-button" type="submit">Speichern</button>
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
        default: '#070914',
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
    const accountSummary = byId('accountSummary');

    const installDialog = byId('installDialog');
    const infoDialog = byId('infoDialog');
    const achievementEditDialog = byId('achievementEditDialog');
    const loginDialog = byId('loginDialog');
    const registerDialog = byId('registerDialog');
    const profileDialog = byId('profileDialog');
    const passwordDialog = byId('passwordDialog');
    const adminDialog = byId('adminDialog');
    const adminUserDialog = byId('adminUserDialog');
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
        renderAchievementGrid();
        if (adminDialog.open) renderAdminPanel();
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

    // Erfolgs-Widgets: jedes Spiel hat GENAU EINE Seite mit zwei Karten —
    // links Statistik, rechts die dazugehörigen Links. Bei WoW kommt die
    // Statistik live von Raider.IO, bei den anderen drei ist sie manuell
    // gepflegt. Titel und Links sind bei allen vier Admin-editierbar.
    const achievementGames = [
        { id: 'wow', label: 'World of Warcraft', icon: '⚔', type: 'wow', source: 'Beste Mythisch-Plus-Läufe, live via Raider.IO' },
        { id: 'hots', label: 'Heroes of the Storm', icon: '🌀', type: 'manual', source: 'Manuell gepflegt' },
        { id: 'diablo4', label: 'Diablo IV', icon: '🔥', type: 'manual', source: 'Manuell gepflegt' },
        { id: 'rocket_league', label: 'Rocket League', icon: '🚀', type: 'manual', source: 'Manuell gepflegt' },
    ];
    const achievementGamesById = Object.fromEntries(achievementGames.map(game => [game.id, game]));
    let achievementPairIndex = 0;
    let achievementsData = null;

    function statsCardTitle(game) {
        if (game.type === 'wow') return 'M+ Wertungen';
        const data = achievementsData ? achievementsData[game.id] : null;
        return (data && data.title) || game.label;
    }

    function linksCardTitle(game) {
        const data = achievementsData ? achievementsData[game.id] : null;
        return (data && data.links_title) || `${game.label} Links`;
    }

    function renderAchievementNav() {
        const game = achievementGames[achievementPairIndex];
        byId('achievementsNavLabel').textContent = game.label;
        byId('achievementsPrev').disabled = achievementGames.length <= 1;
        byId('achievementsNext').disabled = achievementGames.length <= 1;
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

    function buildManualStatsBody(data) {
        const list = document.createElement('ul');
        list.className = 'd4-milestones';

        const milestones = (data && data.milestones) || [];
        if (milestones.length === 0) {
            list.innerHTML = '<li class="widget-empty">Noch keine Einträge hinterlegt.</li>';
        } else {
            for (const milestone of milestones) {
                const item = document.createElement('li');
                const label = document.createElement('span');
                label.textContent = milestone.label;
                const value = document.createElement('span');
                value.textContent = milestone.value;
                item.append(label, value);
                list.appendChild(item);
            }
        }

        const updated = data && data.updated_at
            ? formatUpdatedAt(data.updated_at)
            : 'Noch nicht aktualisiert';

        return { body: list, updated };
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

        const canEdit = cardType === 'links' ? state.admin : (state.admin && game.type === 'manual');
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

    function buildAchievementCard(game, cardType) {
        const card = buildAchievementCardShell(game, cardType);
        const gameData = achievementsData ? achievementsData[game.id] : null;

        if (!achievementsData) {
            const loading = document.createElement('p');
            loading.className = 'widget-loading';
            loading.textContent = 'Wird geladen …';
            card.appendChild(loading);
            card.appendChild(document.createElement('p')).className = 'achievement-updated';
            return card;
        }

        let built;
        if (cardType === 'links') {
            built = buildLinksBody(gameData);
        } else {
            built = game.type === 'wow' ? buildWowBody(gameData) : buildManualStatsBody(gameData);
        }
        card.appendChild(built.body);

        const updated = document.createElement('p');
        updated.className = 'achievement-updated';
        if (cardType === 'stats' && game.type === 'manual' && (!gameData || !gameData.configured)) {
            updated.innerHTML = 'Platzhalter-Daten <span class="mock-tag">Beispiel</span>';
        } else {
            updated.textContent = built.updated;
        }
        card.appendChild(updated);

        return card;
    }

    function renderAchievementGrid() {
        renderAchievementNav();
        const grid = byId('achievementGrid');
        grid.replaceChildren();
        const game = achievementGames[achievementPairIndex];
        grid.appendChild(buildAchievementCard(game, 'stats'));
        grid.appendChild(buildAchievementCard(game, 'links'));
    }

    async function loadAchievements() {
        renderAchievementGrid();
        try {
            const data = await api('achievements');
            achievementsData = {
                wow: data.wow,
                hots: data.hots,
                diablo4: data.diablo4,
                rocket_league: data.rocket_league,
            };
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
                handleApiError(error);
                showToast(error.message);
            } finally {
                saveButton.disabled = false;
            }
        };

        achievementEditDialog.showModal();
    }

    byId('achievementEditAddRow').addEventListener('click', () => addAchievementEditRow());
    byId('achievementEditAddLinkRow').addEventListener('click', () => addAchievementEditLinkRow());

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

        byId('loginButton').hidden = auth.logged_in;
        byId('registerButton').hidden = auth.logged_in;
        byId('profileButton').hidden = !auth.logged_in || auth.must_change_password;
        byId('adminButton').hidden = !auth.is_admin || auth.must_change_password;
        byId('logoutButton').hidden = !auth.logged_in;
        byId('addDateButton').hidden = !auth.can_write;
        byId('addPlayerButton').hidden = !auth.is_admin || auth.must_change_password;

        accountSummary.replaceChildren();
        const avatar = createAvatarElement(auth.user?.avatar || '', auth.user?.player_name || auth.user?.username || '', 'account-avatar');
        const text = document.createElement('div');
        text.className = 'account-summary-text';
        const strong = document.createElement('strong');
        const small = document.createElement('small');

        if (!auth.logged_in) {
            strong.textContent = auth.setup_required ? 'Noch kein Administrator eingerichtet' : 'Nur-Lese-Ansicht';
            small.textContent = auth.setup_required
                ? 'Lege den ersten Account an. Dieser wird automatisch Administrator.'
                : 'Melde dich an oder registriere dich, um deinen eigenen Spieler zu bearbeiten.';
        } else {
            strong.textContent = auth.user?.player_name || 'Spielername noch nicht eingerichtet';
            if (auth.is_admin) {
                const badge = document.createElement('span');
                badge.className = 'account-badge admin';
                badge.textContent = 'Admin';
                strong.appendChild(badge);
            }
            small.textContent = `Angemeldet als ${auth.user?.username || ''}${auth.must_change_password ? ' · Passwortänderung erforderlich' : ''}`;
        }
        text.append(strong, small);
        accountSummary.append(avatar, text);

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
            : 'Lege deinen persönlichen Zugang zum Kalender an.';
        registerDialog.showModal();
        requestAnimationFrame(() => byId('registerUsername').focus());
    }

    function openProfileDialog() {
        if (!state.auth.logged_in || state.auth.must_change_password) return;
        const user = state.auth.user || {};
        byId('profileUsername').value = user.username || '';
        byId('profilePlayerName').value = user.player_name || '';
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

    function renderAdminPanel() {
        if (!state.auth.is_admin || !state.admin) return;
        const list = byId('adminUserList');
        list.replaceChildren();
        for (const user of state.admin.users || []) {
            const card = document.createElement('div');
            card.className = 'admin-user-card';
            const info = document.createElement('div');
            const title = document.createElement('strong');
            title.textContent = user.username;
            if (user.is_admin) {
                const badge = document.createElement('span');
                badge.className = 'account-badge admin';
                badge.textContent = 'Admin';
                title.appendChild(badge);
            }
            if (user.is_author && !user.is_admin) {
                const badge = document.createElement('span');
                badge.className = 'account-badge';
                badge.textContent = 'Autor';
                title.appendChild(badge);
            }
            const details = document.createElement('small');
            details.textContent = `Spieler: ${user.player_name || 'nicht zugeordnet'}${user.must_change_password ? ' · Passwortänderung offen' : ''}`;
            info.append(title, details);
            const edit = document.createElement('button');
            edit.type = 'button';
            edit.className = 'compact-button';
            edit.textContent = 'Bearbeiten';
            edit.addEventListener('click', () => openAdminUserDialog(user));
            card.append(info, edit);
            list.appendChild(card);
        }
        byId('adminPlayerNames').value = (state.admin.admin_player_names || []).join('\n');
        byId('adminTheme').value = state.admin.theme || state.settings.theme || 'default';
    }

    function openAdminDialog() {
        if (!state.auth.is_admin) return;
        renderAdminPanel();
        adminDialog.showModal();
    }

    function openAdminUserDialog(user = null) {
        const editMode = Boolean(user);
        byId('adminUserForm').reset();
        byId('adminUserId').value = user?.id || '';
        byId('adminUsername').value = user?.username || '';
        byId('adminUserPlayerName').value = user?.player_name || '';
        byId('adminUserIsAuthor').checked = Boolean(user?.is_author);
        byId('adminUserDialogTitle').textContent = editMode ? 'Benutzer bearbeiten' : 'Benutzer anlegen';
        byId('adminUserDialogSubtitle').textContent = editMode
            ? 'Benutzername und Spielerzuordnung ändern oder ein vorläufiges neues Passwort setzen.'
            : 'Der Benutzer muss das vorläufige Passwort nach der ersten Anmeldung ändern.';
        byId('adminUserPasswordLabel').textContent = editMode ? 'Neues Passwort (optional)' : 'Vorläufiges Passwort';
        byId('adminPasswordHelp').textContent = editMode
            ? 'Bleibt das Passwortfeld leer, wird das bisherige Passwort beibehalten. Beim Zurücksetzen gelten keine Komplexitätsregeln — der Benutzer muss beim nächsten Login ohnehin ein eigenes, regelkonformes Passwort festlegen.'
            : 'Beim Anlegen ist ein Passwort mit mindestens 8 Zeichen, einem Buchstaben, einer Zahl und einem Sonderzeichen wie !, ?, #, +, -, _, @ oder € erforderlich. Umlaute zählen als Buchstaben. Der Benutzer wird nach der ersten Anmeldung zur Änderung aufgefordert.';
        byId('adminUserPassword').required = !editMode;
        byId('adminUserPasswordConfirmation').required = !editMode;
        byId('adminUserPassword').minLength = editMode ? 0 : 8;
        byId('adminUserPasswordConfirmation').minLength = editMode ? 0 : 8;
        byId('deleteUserButton').hidden = !editMode;
        adminUserDialog.showModal();
        requestAnimationFrame(() => byId('adminUsername').focus());
    }

    byId('installAppButton').addEventListener('click', installApp);
    byId('infoButton').addEventListener('click', () => infoDialog.showModal());
    byId('loginButton').addEventListener('click', openLoginDialog);
    byId('registerButton').addEventListener('click', openRegisterDialog);
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
    byId('profileButton').addEventListener('click', openProfileDialog);
    byId('adminButton').addEventListener('click', openAdminDialog);
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
    byId('createUserButton').addEventListener('click', () => openAdminUserDialog());

    byId('logoutButton').addEventListener('click', async () => {
        try {
            await api('logout', {});
            [installDialog, infoDialog, achievementEditDialog, profileDialog, passwordDialog, adminDialog, adminUserDialog, playerDialog, dateDialog, statusDialog].forEach(dialog => dialog.open && dialog.close());
            await loadPlan();
            showToast('Du wurdest abgemeldet.');
        } catch (error) { handleApiError(error); }
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
                avatar: profileAvatarData
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

    byId('adminUserForm').addEventListener('submit', async event => {
        event.preventDefault();
        const editMode = Boolean(byId('adminUserId').value);
        const password = byId('adminUserPassword').value;
        const passwordConfirmation = byId('adminUserPasswordConfirmation').value;
        if (!editMode) {
            const passwordError = passwordValidationMessage(password, passwordConfirmation);
            if (passwordError) return showToast(passwordError);
        } else if (password !== '' || passwordConfirmation !== '') {
            if (password === '') return showToast('Bitte ein Passwort festlegen.');
            if (password !== passwordConfirmation) return showToast('Die beiden Passwörter stimmen nicht überein.');
        }
        try {
            const data = await api(editMode ? 'admin_update_user' : 'admin_create_user', {
                user_id: byId('adminUserId').value || undefined,
                username: byId('adminUsername').value,
                player_name: byId('adminUserPlayerName').value,
                is_author: byId('adminUserIsAuthor').checked,
                password,
                password_confirmation: passwordConfirmation
            });
            adminUserDialog.close();
            applyData(data);
            renderAdminPanel();
            showToast(editMode ? 'Benutzer wurde geändert.' : 'Benutzer wurde angelegt.');
        } catch (error) { handleApiError(error); }
    });

    byId('deleteUserButton').addEventListener('click', async () => {
        const userId = Number(byId('adminUserId').value);
        const user = state.admin?.users?.find(item => Number(item.id) === userId);
        if (!user || !confirm(`Benutzer „${user.username}“ wirklich löschen? Der Spieler und seine bisherigen Kalenderdaten bleiben erhalten.`)) return;
        try {
            const data = await api('admin_delete_user', { user_id: userId });
            adminUserDialog.close();
            applyData(data);
            if (!state.auth.logged_in) adminDialog.close();
            showToast('Benutzer wurde gelöscht.');
        } catch (error) { handleApiError(error); }
    });

    byId('adminSettingsForm').addEventListener('submit', async event => {
        event.preventDefault();
        try {
            const names = byId('adminPlayerNames').value.split(/[,;\n]+/).map(name => name.trim()).filter(Boolean);
            const data = await api('admin_save_settings', {
                admin_player_names: names,
                theme: byId('adminTheme').value
            });
            applyData(data);
            if (!state.auth.is_admin) adminDialog.close();
            showToast('Einstellungen wurden gespeichert.');
        } catch (error) { handleApiError(error); }
    });

    passwordDialog.addEventListener('cancel', event => {
        if (passwordChangeForced) event.preventDefault();
    });

    [installDialog, infoDialog, achievementEditDialog, loginDialog, registerDialog, profileDialog, passwordDialog, adminDialog, adminUserDialog, playerDialog, dateDialog, statusDialog].forEach(dialog => {
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

    (function setupPullToRefresh() {
        if (!isInstalledApp() || !('ontouchstart' in window)) return;

        const container = byId('pullRefresh');
        const arrow = container.querySelector('.pull-refresh-arrow');
        const THRESHOLD = 72;
        const MAX_PULL = 130;
        let startY = 0;
        let pulling = false;
        let ready = false;
        let refreshing = false;

        document.documentElement.style.overscrollBehaviorY = 'contain';

        const pageScrollTop = () => window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0;

        function reset() {
            pulling = false;
            ready = false;
            container.classList.remove('pull-refresh-visible', 'pull-refresh-ready');
            arrow.style.transform = 'rotate(0deg)';
        }

        window.addEventListener('touchstart', event => {
            if (refreshing || pageScrollTop() > 0 || event.touches.length !== 1) return;
            startY = event.touches[0].clientY;
            pulling = true;
        }, { passive: true });

        window.addEventListener('touchmove', event => {
            if (!pulling || refreshing) return;
            const distance = event.touches[0].clientY - startY;
            if (distance <= 0 || pageScrollTop() > 0) { reset(); return; }
            event.preventDefault();
            const pull = Math.min(MAX_PULL, distance * 0.5);
            ready = pull >= THRESHOLD;
            container.classList.add('pull-refresh-visible');
            container.classList.toggle('pull-refresh-ready', ready);
            arrow.style.transform = `rotate(${Math.min(180, (pull / THRESHOLD) * 180)}deg)`;
        }, { passive: false });

        window.addEventListener('touchend', () => {
            if (!pulling || refreshing) { pulling = false; return; }
            pulling = false;
            if (!ready) { reset(); return; }
            refreshing = true;
            container.classList.add('pull-refresh-loading');
            loadPlan().finally(() => {
                refreshing = false;
                container.classList.remove('pull-refresh-loading');
                reset();
            });
        });

        window.addEventListener('touchcancel', () => {
            refreshing = false;
            reset();
        });
    })();

    byId('achievementsPrev').addEventListener('click', () => {
        achievementPairIndex = (achievementPairIndex - 1 + achievementGames.length) % achievementGames.length;
        renderAchievementGrid();
    });
    byId('achievementsNext').addEventListener('click', () => {
        achievementPairIndex = (achievementPairIndex + 1) % achievementGames.length;
        renderAchievementGrid();
    });

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

    // Zeigt oben über dem Kalender den zuletzt veröffentlichten Blogbeitrag.
    // Schlägt der Abruf fehl oder gibt es noch keinen Beitrag, bleibt das
    // Fenster einfach ausgeblendet — der Kalender funktioniert unabhängig davon.
    async function loadBlogTeaser() {
        try {
            const data = await api('blog_posts');
            const latest = (data.posts || [])[0];
            if (!latest) return;

            byId('blogTeaserTitle').textContent = latest.title;

            const published = new Date(latest.created_at);
            const meta = Number.isNaN(published.getTime())
                ? latest.author_name
                : `${published.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit', year: 'numeric' })} · ${latest.author_name}`;
            byId('blogTeaserMeta').textContent = meta;

            byId('blogTeaser').hidden = false;
        } catch { /* Ohne Blogbeitrag bleibt das Fenster ausgeblendet. */ }
    }

    loadPlan();
    loadAchievements();
    loadBlogTeaser();
</script>
</body>
</html>
