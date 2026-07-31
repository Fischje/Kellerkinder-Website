# Kellerkinder-Online-Kalender

Mobiloptimierter PHP-Kalender zur Planung gemeinsamer Spielabende.

## Funktionen

- Automatische Anzeige der nächsten drei Mittwoche und Sonntage
- Zusätzliche, frei wählbare Spieltage
- Anzeige von genau einem vergangenen Termin
- Beliebig viele Spieler
- Status: Online, später, verhindert, Urlaub oder offen
- Optionaler Hinweis, beispielsweise „ab 21:00 Uhr“
- Offene Bearbeitung durch alle Besucher
- Gemeinsame Datenspeicherung auf dem Webserver
- Discord-Abfrage über `/kalender`
- Dunkles RGB-Gaming-Design
- Keine Datenbank und keine externen Bibliotheken erforderlich

## Voraussetzungen

- Webserver mit PHP 8.1 oder neuer
- Schreibrechte für den Ordner `data`

## Installation

```bash
git clone https://github.com/Fischje/kellerkinder-online-kalender.git
cd kellerkinder-online-kalender
chown -R www-data:www-data data
chmod 750 data
```

Anschließend den Ordner über den Webserver bereitstellen und `index.php` aufrufen.

Die Kalenderdaten werden zur Laufzeit in `data/store.php` gespeichert. Diese Datei ist absichtlich in `.gitignore` eingetragen und wird nicht auf GitHub hochgeladen.

## Prüfung

Über `check.php` lässt sich prüfen, ob PHP aktiv ist und der Ordner `data` beschrieben werden kann. Die Datei kann nach erfolgreicher Einrichtung vom Produktivserver entfernt werden.

## Aktualisieren

Vor einem Update empfiehlt sich eine Sicherung der Datendatei:

```bash
cp data/store.php data/store.php.backup
```

Danach:

```bash
git pull
```

Falls Git wegen lokaler Änderungen an Programmdateien abbricht, zuerst `git status` prüfen. Die Laufzeitdatei `data/store.php` wird von Git nicht verändert.

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

Die Anwendung besitzt bewusst keine Benutzeranmeldung. Jeder Besucher mit Zugriff auf die URL kann Spieler, Termine und Einträge verändern oder löschen. Die URL sollte daher nur innerhalb des gewünschten Personenkreises geteilt werden.

GitHub-Tokens, Serverpasswörter und die Datei `data/store.php` dürfen nicht ins Repository eingecheckt werden.

## Lizenz

Aktuell ist keine Open-Source-Lizenz hinterlegt. Bei einem öffentlichen Repository bedeutet das, dass andere den Quelltext ansehen, aber nicht automatisch weiterverwenden dürfen.
