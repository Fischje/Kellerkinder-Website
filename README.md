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
- Automatische Anzeige der nächsten drei Mittwoche und Sonntage
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
- Drei globale Styles: RGB-Gaming als Standard, Sommer mit Sonne/Wasser/Strand und Winter mit Schnee/Weihnachtsmotiven
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
│   ├── backgrounds/
│   │   ├── default/   (mehrere Bilder, eines wird pro Seitenaufruf zufällig gewählt)
│   │   ├── summer/     (genau ein Bild, wird immer verwendet)
│   │   └── winter/     (genau ein Bild, wird immer verwendet)
│   ├── kellerkinder-logo.svg
│   └── smartphone-install.svg
├── data/
│   ├── .htaccess
│   └── index.php
├── .gitignore
├── api.php
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
