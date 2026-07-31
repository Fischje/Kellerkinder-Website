# Upgrade auf Version 2.1.0

Diese Version ändert keine Datenstruktur. Die bestehende Datei `data/store.php` bleibt unverändert erhalten.

## Änderungen

- Neue Passwörter benötigen mindestens 8 Zeichen, einen Buchstaben, eine Zahl und ein Sonderzeichen.
- Alle angemeldeten Benutzer dürfen zusätzliche Spieltage anlegen.
- Nur Administratoren dürfen zusätzliche Spieltage löschen.

## Installation

Vor dem Update empfiehlt sich eine Sicherung:

```bash
cd /srv/kellerkinder-online-kalender
cp data/store.php data/store.php.backup-v2.1
```

Danach die Dateien aus dem Update-Paket in den bestehenden Projektordner kopieren und die vorhandenen Dateien überschreiben. `data/store.php` darf nicht gelöscht oder ersetzt werden.

Bei einer GitHub-Installation können die Änderungen anschließend mit folgendem Befehl veröffentlicht werden:

```bash
git add .
git commit -m "Passwortregeln und Spieltage für Benutzer ergänzt"
git push
```

## Kompatibilität vorhandener Passwörter

Vorhandene Passwörter werden beim Login nicht nachträglich abgewiesen. Die neuen Regeln gelten erst, sobald ein Passwort neu gesetzt oder geändert wird.
