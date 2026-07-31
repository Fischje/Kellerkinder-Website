# Änderungsprotokoll

## 2.1.0

- Neue Passwörter benötigen mindestens 8 Zeichen
- Mindestens ein Buchstabe, eine Zahl und ein Sonderzeichen werden serverseitig geprüft
- Passwortregeln werden bei Registrierung, Passwortänderung und Admin-Passwortreset angezeigt
- Bestehende Passwörter bleiben bis zur nächsten Änderung weiterhin gültig
- Alle angemeldeten Benutzer dürfen zusätzliche Spieltage anlegen
- Das Löschen zusätzlicher Spieltage bleibt Administratoren vorbehalten

## 2.0.0

- Benutzerregistrierung und Login ergänzt
- Persönliche Zuordnung eines Accounts zu einem Spieler
- Normale Benutzer dürfen ausschließlich den eigenen Spieler und eigene Statusangaben bearbeiten
- Vollständiger Adminbereich für Benutzer, Spieler, Termine und Status
- Adminrechte über `settings.admin_player_names` in der Datendatei
- Erster registrierter Account wird automatisch erster Administrator
- Frei wählbare Passwörter ohne Mindestlänge oder Zeichensatzvorgaben
- Sichere Passwort-Hashes, Sitzungen und CSRF-Schutz
- Admin-Passwortreset erzwingt Passwortwechsel bei der nächsten Anmeldung
- Bestehende Sitzung wird nach einem Admin-Passwortreset ungültig
- Wiederkehrende Online-Wochentage pro Benutzer
- Einzelne Termine können den Wochenstandard überschreiben
- Bestehende Version-1.5-Daten werden automatisch übernommen
- Automatische Sicherung der alten Datendatei vor der Account-Migration
- Öffentliche Kalenderansicht bleibt erhalten
- Discord-API bleibt mit `/kalender` kompatibel

## 1.5

- Automatische Grundtermine für die nächsten drei Mittwoche und Sonntage
- Zusätzliche, frei wählbare Spieltage
- Genau ein vergangener Termin wird angezeigt
- Zusätzliche Spieltage können wieder gelöscht werden
- Discord-Hinweis auf den Befehl `/kalender`
- Status: Online, später, verhindert, Urlaub und offen
