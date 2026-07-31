# GitHub-Ersteinrichtung

## 1. Leeres Repository auf GitHub anlegen

Empfohlener Name:

```text
kellerkinder-online-kalender
```

Beim Anlegen **keine** README-, `.gitignore`- oder Lizenzdatei erzeugen, da diese Dateien bereits im Projekt vorhanden sind.

## 2. Projekt lokal oder auf dem Webserver initialisieren

Im Projektordner ausführen:

```bash
git init
git branch -M main
git add .
git commit -m "Kellerkinder-Online-Kalender v2.1.0"
git remote add origin https://github.com/Fischje/kellerkinder-online-kalender.git
git push -u origin main
```

Wenn das Remote `origin` bereits existiert:

```bash
git remote set-url origin https://github.com/Fischje/kellerkinder-online-kalender.git
git push -u origin main
```

Bei HTTPS wird beim Kennwortfeld ein GitHub-Token verwendet, nicht das normale GitHub-Passwort.

## 3. Spätere Änderungen veröffentlichen

```bash
git status
git add .
git commit -m "Beschreibung der Änderung"
git push
```

## 4. Website aus GitHub auf einem Server installieren

```bash
git clone https://github.com/Fischje/kellerkinder-online-kalender.git
cd kellerkinder-online-kalender
```

Danach dem PHP-Webserver Schreibrechte für `data` geben, zum Beispiel:

```bash
chown -R www-data:www-data data
chmod 750 data
```

Die Datei `data/store.php` wird beim ersten Speichern automatisch erstellt und wird nicht in Git aufgenommen. Sie enthält ab Version 2.0.0 auch Benutzerkonten und Passwort-Hashes.

## 5. Bereits vorhandene Daten behalten

Vor einem Wechsel auf die Git-Version die bestehende Datei sichern:

```bash
cp data/store.php /root/kellerkinder-store-backup.php
```

Nach dem Klonen kann sie wieder zurückkopiert werden:

```bash
cp /root/kellerkinder-store-backup.php data/store.php
chown www-data:www-data data/store.php
chmod 640 data/store.php
```


## 6. Upgrade auf die Account-Version

Vor dem ersten `git pull` eine Sicherung anlegen:

```bash
cp data/store.php data/store.php.backup-manual
```

Nach dem Update die Website öffnen und sofort den ersten Account registrieren. Der erste Account wird automatisch Administrator. Die vorhandenen Spieler, Termine und Statusangaben bleiben erhalten.
