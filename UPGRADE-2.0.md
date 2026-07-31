# Upgrade auf Version 2.0.0

## 1. Datensicherung

Im Website-Ordner:

```bash
cp data/store.php data/store.php.backup-manual
```

## 2. Dateien aktualisieren

Bei Git-Nutzung:

```bash
git status
git pull
```

Oder die Dateien aus dem Update-Paket über die vorhandenen Programmdateien kopieren.

Die bestehende Datei `data/store.php` darf nicht gelöscht oder ersetzt werden.

## 3. Rechte prüfen

```bash
chown -R www-data:www-data data
chmod 750 data
chmod 640 data/store.php
```

## 4. Ersten Administrator anlegen

Nach dem Update die Website sofort öffnen. Solange noch kein Benutzerkonto existiert, wird die Ersteinrichtung angezeigt.

Der erste registrierte Account wird automatisch Administrator. Sein Spielername wird in der Datendatei unter `settings.admin_player_names` gespeichert.

## 5. Bestehende Spieler zuordnen

Vorhandene Spieler und Statusdaten bleiben erhalten. Bei der Registrierung kann ein vorhandener, noch nicht zugeordneter Spielername übernommen werden.

Weitere Benutzer können:

- sich selbst registrieren und einen noch freien Spieler übernehmen oder
- durch einen Administrator im Adminbereich angelegt werden.

## 6. Automatische Sicherung

Beim ersten Speichern mit Version 2.0.0 wird zusätzlich automatisch folgende Datei angelegt:

```text
data/store.php.before-accounts-backup
```

## 7. Prüfung

Temporär aufrufen:

```text
https://deine-domain.example/check.php
```

Nach erfolgreicher Prüfung `check.php` wieder vom Produktivserver entfernen.
