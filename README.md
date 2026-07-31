# Kellerkinder-Online-Kalender

Mobiloptimierter PHP-Kalender zur Planung gemeinsamer Spielabende mit Benutzerkonten, Rollen und Discord-Abfrage.

## Funktionen

- Öffentliche Kalenderansicht
- Registrierung und Anmeldung mit Benutzername und Passwort
- Persönlicher Spielername je Account
- Normale Benutzer bearbeiten ausschließlich den eigenen Spieler und die eigenen Statusangaben
- Administratoren verwalten Benutzer, Spieler, Termine und sämtliche Statusangaben
- Adminrechte werden in `settings.admin_player_names` anhand der Spielernamen gespeichert
- Automatische Anzeige der nächsten drei Mittwoche und Sonntage
- Zusätzliche, frei wählbare Spieltage durch alle angemeldeten Benutzer
- Anzeige von genau einem vergangenen Termin
- Wiederkehrende Standardtage: Ein Benutzer kann festlegen, an welchen Wochentagen er normalerweise online ist
- Status: Online, später, verhindert, Urlaub oder offen
- Optionaler Hinweis, beispielsweise „ab 21:00 Uhr“
- Passwortänderung ohne E-Mail-Funktion
- Setzt ein Admin ein neues Passwort, wird die bestehende Sitzung ungültig und der Benutzer muss das vorläufige Passwort nach der nächsten Anmeldung erneut ändern
- Discord-Abfrage über `/kalender`
- Dunkles RGB-Gaming-Design
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
cp data/store.php data/store.php.backup-manual
```

Danach die neuen Programmdateien einspielen oder aus GitHub aktualisieren:

```bash
git pull
chown -R www-data:www-data data
chmod 750 data
```

Beim ersten Speichervorgang mit der neuen Account-Version wird automatisch eine zusätzliche Sicherung angelegt:

```text
data/store.php.before-accounts-backup
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
- eigene regelmäßige Online-Wochentage festlegen,
- zusätzliche Spieltage anlegen,
- eigenen Status und eigenen Hinweis je Termin ändern,
- eigenes Passwort ändern.

Zusätzliche Spieltage können von allen angemeldeten Benutzern angelegt werden. Das Löschen zusätzlicher Spieltage bleibt Administratoren vorbehalten, weil dabei die zugehörigen Statusangaben aller Spieler entfernt werden.

### Administrator

- Benutzerkonten anlegen, ändern und löschen,
- vorläufige Benutzerpasswörter setzen,
- Admin-Spielernamen verwalten,
- Spieler anlegen, umbenennen und löschen,
- zusätzliche Spieltage anlegen und löschen,
- Status und Hinweise aller Spieler ändern.

Beim Löschen eines Benutzerkontos bleibt der zugehörige Spieler samt Kalenderhistorie bestehen und wird lediglich vom Account getrennt. Ein Spieler kann anschließend separat gelöscht oder erneut einem Account zugeordnet werden.

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
- wiederkehrende Wochentage,
- Termine,
- Statusangaben und Hinweise.

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
│   └── kellerkinder-logo.svg
├── data/
│   ├── .htaccess
│   └── index.php
├── .gitignore
├── api.php
├── check.php
├── index.php
├── CHANGELOG.md
├── GITHUB_SETUP.md
├── README.md
└── VERSION
```

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
