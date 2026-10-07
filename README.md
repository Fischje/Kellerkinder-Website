# Kellerkinder-Online-Kalender

Mobiloptimierter PHP-Kalender zur Planung gemeinsamer Spielabende mit Benutzerkonten, Rollen und Discord-Abfrage.

## Funktionen

- Öffentliche Kalenderansicht
- Registrierung und Anmeldung mit Benutzername und Passwort
- Persönlicher Spielername je Account
- Persönlicher Avatar je Account, automatisch auf maximal 50 × 50 Pixel verkleinert
- Normale Benutzer bearbeiten ausschließlich den eigenen Spieler und die eigenen Statusangaben
- Administratoren verwalten Benutzer, Spieler, Termine und sämtliche Statusangaben
- Adminrechte werden in `settings.admin_player_names` anhand der Spielernamen gespeichert
- Administratoren ändern den globalen Style für alle Besucher und Benutzer
- Spieltage entstehen automatisch aus den festen Wochentagen der Accounts und aus Terminen, an denen jemand einen Status gesetzt hat (keine fest vorgegebenen Mittwoche/Sonntage mehr)
- Zusätzliche, frei wählbare Spieltage durch alle angemeldeten Benutzer
- Anzeige von genau einem vergangenen Termin
- Wiederkehrende Standardtage: Ein Benutzer kann festlegen, an welchen Wochentagen er normalerweise online ist
- Status: Online, später, verhindert, Urlaub oder offen
- Spielwunsch je Spieler und Termin mit lernender Auswahlliste bereits genannter Spiele
- Optionaler Hinweis, beispielsweise „ab 21:00 Uhr“
- Als Progressive Web App auf dem Home-Bildschirm von iPhone und Android installierbar
- Automatische Versionsanzeige im Footer
- Passwortänderung ohne E-Mail-Funktion
- Setzt ein Admin ein neues Passwort, wird die bestehende Sitzung ungültig und der Benutzer muss das vorläufige Passwort nach der nächsten Anmeldung erneut ändern
- Discord-Abfrage über `/kalender`
- Drei globale Styles: „Arena“ (Violett/Rot im Esports-Look) als Standard, Sommer mit Sonne/Wasser/Strand und Winter mit Schnee/Weihnachtsmotiven
- Keine externe Datenbank und keine externen Bibliotheken erforderlich

## Passwortregeln

Neue Passwörter müssen folgende Anforderungen erfüllen:

- mindestens 8 Zeichen,
- mindestens einen Buchstaben,
- mindestens eine Zahl,
- mindestens ein Sonderzeichen, beispielsweise `!`, `?`, `#`, `+`, `-` oder `_`.

Die Regeln gelten bei der Registrierung, bei eigenen Passwortänderungen sowie bei vorläufigen Passwörtern, die ein Administrator setzt. Bereits vorhandene schwächere Passwörter bleiben für die Anmeldung gültig, bis sie geändert werden. Passwörter werden ausschließlich als sichere PHP-Passworthashes gespeichert.

## Voraussetzungen

- Webserver mit PHP 8.1 oder neuer
- PHP-Sitzungen aktiviert
- Schreibrechte für den Ordner `data`
- Für den Produktivbetrieb dringend HTTPS

## Installation

```bash
git clone https://github.com/Fischje/kellerkinder-online-kalender.git
cd kellerkinder-online-kalender
chown -R www-data:www-data data
chmod 750 data
```

Anschließend den Ordner über den Webserver bereitstellen und `index.php` aufrufen.

## Ersteinrichtung

Existiert noch kein Benutzerkonto, zeigt die Website einen Hinweis zur Ersteinrichtung. Der **erste registrierte Account wird automatisch Administrator**. Sein Spielername wird in der Datendatei unter folgendem Schlüssel gespeichert:

```text
settings.admin_player_names
```

Registriere den ersten Account unmittelbar nach dem Update, damit kein anderer Besucher die Ersteinrichtung übernehmen kann.

Weitere Administratoren werden im Adminbereich über ihren Spielername ergänzt. Der Spieler muss bereits existieren und mit einem Benutzerkonto verbunden sein.

## Upgrade von Version 1.5

Die bisherige Datei `data/store.php` kann unverändert weiterverwendet werden. Spieler, zusätzliche Termine und Statusangaben bleiben erhalten.

Vor dem Update empfiehlt sich trotzdem eine manuelle Sicherung:

```bash
cp data/store.php data/store-backup-manual.php
```

**Wichtig:** Der Dateiname muss auf `.php` enden, sonst liefert der Webserver die Sicherung bei direktem Aufruf als reinen Text aus — inklusive aller Passwort-Hashes. `data/store.php` selbst beginnt mit einer Schutzzeile, die nur wirkt, wenn die Datei vom Webserver als PHP ausgeführt wird.

Danach die neuen Programmdateien einspielen oder aus GitHub aktualisieren:

```bash
git pull
chown -R www-data:www-data data
chmod 750 data
```

Beim ersten Speichervorgang mit der neuen Account-Version wird automatisch eine zusätzliche Sicherung angelegt:

```text
data/store-before-accounts-backup.php
```

Diese Sicherung und die aktive Datendatei werden durch `.gitignore` nicht zu GitHub hochgeladen.

Nach dem Upgrade:

1. Website öffnen.
2. Den ersten Account registrieren.
3. Mit dem gewünschten Admin-Spielernamen anmelden.
4. Im Adminbereich vorhandene Spieler den weiteren Benutzerkonten zuordnen oder neue Benutzer anlegen.

## Benutzer- und Adminrechte

### Normaler Benutzer

- eigenen Spielernamen ändern,
- eigenes Profilbild/Avatar hochladen oder entfernen,
- eigene regelmäßige Online-Wochentage festlegen,
- zusätzliche Spieltage anlegen,
- eigenen Status, eigenen Spielwunsch und eigenen Hinweis je Termin ändern,
- eigenes Passwort ändern.

Zusätzliche Spieltage können von allen angemeldeten Benutzern angelegt werden. Das Löschen zusätzlicher Spieltage bleibt Administratoren vorbehalten, weil dabei die zugehörigen Statusangaben aller Spieler entfernt werden.

### Administrator

- Benutzerkonten anlegen, ändern und löschen,
- vorläufige Benutzerpasswörter setzen,
- Admin-Spielernamen verwalten,
- Spieler anlegen, umbenennen und löschen,
- zusätzliche Spieltage anlegen und löschen,
- Status, Spielwünsche und Hinweise aller Spieler ändern.

Beim Löschen eines Benutzerkontos bleibt der zugehörige Spieler samt Kalenderhistorie bestehen und wird lediglich vom Account getrennt. Ein Spieler kann anschließend separat gelöscht oder erneut einem Account zugeordnet werden.

## Spielwünsche

Im Bearbeitungsfenster eines Termins kann jeder berechtigte Spieler neben Status und Hinweis ein gewünschtes Spiel eintragen. Bereits einmal gespeicherte Spielnamen werden allen Benutzern als Vorschläge in einer Auswahlliste angeboten. Neue Titel können weiterhin frei eingetragen werden.

## Installation auf dem Home-Bildschirm

Das kleine Smartphone-Symbol oben rechts öffnet die Installation:

- Unterstützte Android-Browser zeigen direkt den Installationsdialog.
- Auf dem iPhone werden die Schritte für Safari angezeigt: Teilen → Zum Home-Bildschirm hinzufügen → Als Web-App öffnen → Hinzufügen.

Für die Installation muss die Website über HTTPS erreichbar sein. Die PWA-Dateien speichern ausschließlich statische Logos und Symbole zwischen; die Kalender- und Accountdaten werden weiterhin aktuell über `api.php` geladen.

## Datenspeicherung

Alle Laufzeitdaten werden in folgender geschützter PHP-Datendatei gespeichert:

```text
data/store.php
```

Enthalten sind unter anderem:

- Benutzerkonten,
- Passwort-Hashes,
- Spielerzuordnungen,
- Admin-Spielernamen,
- globaler Style,
- wiederkehrende Wochentage,
- Termine,
- Statusangaben, Spielwünsche und Hinweise.

Die Datei beginnt mit einer PHP-Sperre und liefert bei einem direkten Aufruf HTTP 403. Sie darf trotzdem niemals in das öffentliche GitHub-Repository eingecheckt werden.

## Prüfung

Über `check.php` lässt sich prüfen, ob:

- PHP in ausreichender Version läuft,
- Passwort-Hashing und Sitzungen verfügbar sind,
- der Ordner `data` beschreibbar ist,
- die Datendatei und das neue Datenschema erkannt werden.

`check.php` sollte nach erfolgreicher Prüfung vom Produktivserver entfernt werden.

## Aktualisieren

```bash
git status
git pull
```

Die Laufzeitdateien unter `data/store.php*` werden von Git ignoriert und dadurch nicht überschrieben.

## Dateistruktur

```text
.
├── assets/
│   ├── app-icon-180.png
│   ├── app-icon-192.png
│   ├── app-icon-512.png
│   ├── blog/           (im Blog hochgeladene Bilder)
│   ├── backgrounds/
│   │   ├── default/   (mehrere Bilder, eines wird pro Seitenaufruf zufällig gewählt)
│   │   ├── summer/     (genau ein Bild, wird immer verwendet)
│   │   └── winter/     (genau ein Bild, wird immer verwendet)
│   ├── kellerkinder-logo.svg
│   └── smartphone-install.svg
├── data/
│   ├── .htaccess
│   └── index.php
├── includes/
│   ├── bootstrap.php   (Version, Hintergrundbilder, Sicherheits-Header)
│   ├── styles.php      (gemeinsames Stylesheet für alle Seiten)
│   ├── site-header.php (gemeinsamer Seitenkopf)
│   ├── games-view.php  (gemeinsame Ansicht von games.php und alle-spiele.php)
│   ├── arena-styles.php (Design „Arena“, über styles.php gelegt)
│   ├── blog-styles.php
│   └── stats-styles.php
├── .gitignore
├── api.php
├── blog.php
├── games.php           (Spiele: Top 10 / aufklappbar bis 30)
├── alle-spiele.php     (Alle Spiele: komplette Rangliste)
├── statistik.php       (leitet auf games.php um)
├── steam-refresh.php   (Cron: Steam-Erfolge, Spiele-Symbole, Profilbilder holen)
├── feed.php
├── check.php
├── index.php
├── manifest.webmanifest
├── service-worker.js
├── CHANGELOG.md
├── GITHUB_SETUP.md
├── README.md
└── VERSION
```

## Hintergrundbilder

Für jedes der drei Themes (Standard/RGB, Sommer, Winter) gibt es einen eigenen
Ordner unter `assets/backgrounds/`. Bilder dort können unbearbeitet (JPG,
JPEG, PNG oder WEBP) hochgeladen werden — Abdunkeln und der Verlauf ins
Schwarze an den Rändern übernimmt die Website automatisch per CSS, keine
Bildbearbeitung nötig.

- **`assets/backgrounds/default/`**: beliebig viele Bilder ablegen. Bei jedem
  Seitenaufruf wird eines zufällig gewählt.
- **`assets/backgrounds/summer/`** und **`assets/backgrounds/winter/`**:
  jeweils genau ein Bild ablegen. Es wird immer verwendet und nicht
  gewechselt. Liegen mehrere Dateien im Ordner, wird die alphabetisch erste
  genommen.

Ist ein Ordner leer, wird kein Foto angezeigt — beim Sommer- und
Winter-Theme bleibt dann nur das bestehende Wellen-/Sonnen- bzw.
Tannenbaum-/Schnee-Muster sichtbar, beim Standard-Theme nur das Raster.

Die Bilder selbst sind nicht Teil des Git-Repositories (siehe `.gitignore`)
und müssen direkt auf den Server hochgeladen werden (z. B. per FTP/SCP oder
über den Datei-Manager des Hosters).

## Spiele

Die Seite `games.php` (Menüpunkt „Spiele“, früher „Statistik“) zeigt, welche Spiele auf dem
Kellerkinder-Discord gespielt werden und wie lange. Die Zahlen liefert der Discord-Bot
(siehe Abschnitt „Spielzeit-Statistik“ weiter unten). Die frühere, von Hand gepflegte
Spiele-Bibliothek gibt es nicht mehr.

**Zeiträume:** Letzte 7 Tage, Letzte 30 Tage (Standard), Ein Jahr, Immer.

**Umfang:** Sichtbar sind die Top 10, mit „Platz 11–30 anzeigen“ klappt die Liste bis Platz 30 auf.
Alles darüber steht auf der eigenen Seite „Alle Spiele“ (`alle-spiele.php`, Knopf „Alle N Spiele ansehen“).

**Spiele-Symbole:** Zu jedem Spiel der Rangliste sucht die Website einmal ein Bild und lädt es
nach `assets/game-icons` herunter (dort liegen nur automatisch erzeugte Dateien, sie sind nicht
im Repository). Die Suche läuft im Hintergrund, die Seite wartet nicht darauf. Für Spiele
außerhalb von Steam (z. B. Blizzard) wird [RAWG](https://rawg.io/apidocs) verwendet. Dafür einen
kostenlosen API-Key holen und in `config.php` im Hauptordner eintragen (die Datei ist in
`.gitignore` und wird nicht hochgeladen):

```php
<?php
const RAWG_API_KEY = 'dein-key';
```

Ohne Key sucht die Seite im Steam-Store (dort fehlen z. B. Blizzard-Spiele, sie zeigen dann
ein Platzhalter-Symbol). Der Server braucht ausgehenden Zugriff auf `api.rawg.io` bzw.
`store.steampowered.com`. Nicht gefundene Spiele werden nach 7 Tagen erneut gesucht; der
Cron-Job (siehe Steam-Erfolge) mit `--force` sucht sofort erneut.

## Steam-Erfolge

Das Widget „Steam-Erfolge“ bei „Unsere Erfolge“ zeigt für bis zu 7 Spieler den jüngsten
Steam-Erfolg, neueste zuerst. Den Steam-Namen trägt jeder unter „Mein Account“
ein, ein Admin kann ihn im Benutzer-Dialog setzen (Profilname, SteamID oder
Link zum Steam-Profil).

**Einrichtung:** kostenlosen Schlüssel unter https://steamcommunity.com/dev/apikey
holen und in `config.php` eintragen (die Datei liegt nicht im Repository):

```php
const STEAM_API_KEY = 'dein-schluessel';
```

Der Server braucht ausgehenden Zugriff auf `api.steampowered.com`.

**So wird geladen:** Die Seite liefert immer den Zwischenspeicher aus (`data/cache`) und
wartet nie auf Steam. Neu bei Steam geholt wird höchstens stündlich, im Hintergrund, und
sofort, wenn sich ein Steam-Name ändert. Damit die Daten auch ohne Besucher aktuell bleiben,
empfiehlt sich ein stündlicher Cron-Job:

```cron
0 * * * * php /srv/kellerkinder-website/steam-refresh.php --force
```

Das Skript holt außerdem die Symbole für die Seite „Spiele“.

Ohne Cron holt die Website die Daten bei einem Besuch nach Ablauf der Stunde selbst
(beim nächsten Besuch sind sie dann neu). Admins sehen unter dem Widget „Jetzt neu laden“
und „Diagnose“.

**Wichtig:** Erfolge gibt Steam nur heraus, wenn **Profil und Spieldetails auf „Öffentlich“**
stehen – „Nur Freunde“ reicht nicht, auch wenn die Spieleliste sichtbar ist. Fehlt jemand,
nennt das Widget den Grund.

## Spielzeit-Statistik (Discord-Bot)

Die Spielzeiten auf der Seite „Spiele“ (`games.php`) liefert der Discord-Bot
(Zeiträume 7, 30, 365 Tage und gesamt). Der Discord-Bot (Repository `kk-discord-bot`, ab Version 1.5.0); gezählt
werden nur volle 15-Minuten-Blöcke.

Der Server ruft die Daten selbst beim Bot ab, der Browser spricht nie direkt mit
dem Bot. Die Adresse kommt in `config.php` (neben dem RAWG-Key):

```php
<?php
const BOT_STATS_URL = 'http://127.0.0.1:3100/api/stats';
```

Die Antwort wird 5 Minuten zwischengespeichert. Ist der Bot kurz nicht
erreichbar, zeigt die Seite den letzten bekannten Stand mit Hinweis. Ohne
`BOT_STATS_URL` meldet die Seite, dass die Statistik noch nicht eingerichtet ist.

**Spieler ausblenden:** Admins sehen auf der Seite „Spiele“ unten eine Liste
aller Discord-Mitglieder mit Spielzeit und können einzelne ausblenden. Diese
zählen dann weder auf der Website noch bei `/statistik` im Discord (der Bot holt
die Liste über `api.php?action=stats_exclusions`, höchstens 5 Minuten verzögert).
Dafür ist Bot-Version 1.6.0 nötig.

**Profilbilder:** Neben den Spielern steht ihr Discord-Profilbild (Bot-Version 1.8.0). Die Website lädt
es nach `assets/avatars` herunter (nicht im Repository) und prüft es einmal pro Woche auf Änderung;
Besucher laden nichts von Discord. Ohne eigenes Bild erscheint ein Platzhalter. Der Ordner muss für PHP
beschreibbar sein.

**Aufklappen:** Ein Spiel anklicken zeigt die Top-Spieler, ein Spieler seine Top-5-Spiele
(Bot-Version 1.7.0). Öffentlich nur, wenn der Bot Spielernamen freigibt
(`STATS_PUBLIC_PLAYERS=true`), Admins sehen sie immer.

Spielernamen erscheinen nur, wenn im Bot `STATS_PUBLIC_PLAYERS=true` gesetzt ist.

## Blog

Unter `blog.php` steht ein Blog für Spielerlebnisse und News bereit.

**Autorenrecht:** Nur Benutzer mit dem Recht „Autor" dürfen Beiträge
schreiben. Ein Administrator vergibt es im Adminbereich über die Checkbox
„Autorenrecht" im Benutzer-Dialog. Administratoren sind automatisch Autoren
und dürfen zusätzlich fremde Beiträge bearbeiten und löschen.

**Editor:** Formatierung (fett, kursiv, Überschriften, Listen, Zitate),
Links, Bild-Upload und YouTube-Einbettung. Hochgeladene Bilder werden
serverseitig geprüft, auf maximal 1600 Pixel Breite verkleinert und unter
einem Zufallsnamen in `assets/blog/` abgelegt. Beiträge lassen sich als
Entwurf speichern, dann sind sie noch nicht öffentlich sichtbar.

**Tags:** Bis zu acht Schlagworte je Beitrag, kommagetrennt. Über das
Themen-Widget lässt sich die Liste filtern; der Filter steht in der
Adresszeile und ist damit teilbar.

**RSS:** Der Feed liegt unter `feed.php` und ist im Blog verlinkt sowie im
Seitenkopf für Reader hinterlegt.

**Hinweis zur Sicherheit:** Beitragsinhalte werden vor dem Speichern gegen
eine Positivliste erlaubter HTML-Elemente gefiltert. Vergib das Autorenrecht
trotzdem nur an Personen, denen du vertraust.

## Sicherheit

Die Anwendung verwendet:

- `password_hash()` und `password_verify()` für Passwörter,
- serverseitige PHP-Sitzungen,
- HTTP-only-Sitzungscookies,
- SameSite-Cookies,
- CSRF-Schutz für alle schreibenden Aktionen,
- serverseitige Rechteprüfungen für jeden Änderungsaufruf,
- Sitzungsinvalidierung nach einem Admin-Passwortreset.

Da die Registrierung offen ist, kann jeder Besucher mit Kenntnis der URL einen normalen Account anlegen. Dieser Account kann jedoch ausschließlich den eigenen zugeordneten Spieler bearbeiten.

GitHub-Tokens, Serverpasswörter und sämtliche Dateien `data/store.php*` dürfen nicht ins Repository eingecheckt werden.

## Lizenz

Aktuell ist keine Open-Source-Lizenz hinterlegt. Bei einem öffentlichen Repository bedeutet das, dass andere den Quelltext ansehen, aber nicht automatisch weiterverwenden dürfen.
