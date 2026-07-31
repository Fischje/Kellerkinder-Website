# Update auf Version 2.2.0

Dieses Update benötigt keine Migration der Datendatei. Bestehende Accounts, Spieler, Termine, Status und Hinweise bleiben erhalten.

## Neu

- Spielwunsch je Spieler und Termin
- Gemeinsame Vorschlagsliste bereits gespeicherter Spiele
- Installation als Home-Screen-Web-App auf iPhone und Android
- Smartphone-Symbol oben rechts
- Automatische Versionsanzeige im Footer

## Installation

1. `data/store.php` sichern.
2. Dateien aus dem Update-Paket über die bestehende Installation kopieren.
3. `data/store.php` nicht löschen oder ersetzen.
4. Prüfen, dass auch `manifest.webmanifest`, `service-worker.js` und die neuen Dateien unter `assets/` übertragen wurden.
5. Website einmal mit Strg+F5 neu laden.

Beispiel:

```bash
cd /srv/kellerkinder-online-kalender
cp data/store.php data/store.php.backup-v2.2.0
```

## Hinweis zur App-Installation

Die Website muss über HTTPS erreichbar sein. Auf Android kann das Smartphone-Symbol den Browser-Installationsdialog öffnen. Auf dem iPhone zeigt es die nötigen Safari-Schritte an.
