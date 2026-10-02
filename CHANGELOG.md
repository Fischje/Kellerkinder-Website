# Änderungsprotokoll

## 2.11.1 — Spielsuche über RAWG (auch Blizzard-Spiele)

- Die Spielsuche nutzt jetzt bevorzugt die Datenbank RAWG, die auch
  Blizzard-, Epic- und Konsolenspiele kennt (Steam kennt diese nicht)
- Aktivierung: kostenlosen Key auf rawg.io/apidocs holen und in der (nicht im
  Repository liegenden) Datei `config.php` eintragen:
  `<?php const RAWG_API_KEY = 'dein-key';`
- Ohne Key bleibt die Steam-Suche als Rückfall aktiv
- Gespeichert wird nur noch die Bild-Adresse; erlaubt sind ausschließlich
  Bilder von RAWG und Steam

## 2.11.0 — Neue Seite „Spiele": Bibliothek der aktuell gespielten Spiele

- Neue Seite `games.php` (Menüpunkt „Spiele") mit einer Bibliothek der Spiele,
  die die Kellerkinder gerade spielen; für alle sichtbar
- Angemeldete Benutzer tragen Spiele über ein Suchfeld ein: Beim Tippen werden
  Treffer aus dem Steam-Store samt Icon vorgeschlagen, nach der Auswahl erscheint
  das Steam-Bild in der Bibliothek
- Spiele ohne Steam-Eintrag lassen sich auch ohne Icon hinzufügen
- Doppelte Einträge werden abgelehnt; entfernen darf, wer das Spiel eingetragen
  hat, sowie Administratoren
- Die Steam-Suche läuft über den Server (`api.php`); die Sicherheits-Header
  erlauben dafür zusätzlich Bilder von den Steam-CDN-Servern

## 2.10.0 — Urlaub für einen Zeitraum eintragen

- Im Bereich „Mein Account" lässt sich jetzt ein Zeitraum (Von–Bis) wählen und
  per Knopfdruck als Urlaub eintragen
- Gesetzt wird der Status „Urlaub" für alle Spieltage im Zeitraum, die aktuell
  im Kalender sichtbar sind — bewusst nicht für Termine, die es dort (noch)
  gar nicht gibt
- „Urlaub entfernen" nimmt im gewählten Zeitraum ausschließlich die eigenen
  Urlaubseinträge zurück; andere Angaben wie „Online" bleiben unangetastet
- Eine Rückmeldung nennt, für wie viele Spieltage der Urlaub gesetzt bzw.
  entfernt wurde, und weist darauf hin, wenn im Zeitraum kein sichtbarer
  Spieltag liegt
- Jeder kann damit nur den eigenen Spieler bearbeiten

## 2.9.2 — Absturz beim Laden behoben (Regression aus 2.9.0)

**Fehler:** Nach dem Blog-Update konnte die Seite auf Geräten mit gesetztem
„Angemeldet bleiben"-Cookie gar nicht mehr laden („Die Anfrage konnte nicht
verarbeitet werden"). Am Computer mit noch aktiver Sitzung fiel es nicht auf,
auf dem Handy nach längerer Pause dagegen sofort.

**Ursache:** Die neuen Blog-Konstanten (`BLOG_TITLE_MAX` und weitere) standen
in `api.php` weiter unten als der Programmteil, der die Anmeldung aus dem
dauerhaften Token wiederherstellt. Dieser Teil liest die Datendatei ein, wobei
die Blogbeiträge geprüft werden — und dabei werden genau diese Konstanten
gebraucht. Anders als Funktionen werden Konstanten in PHP nicht vorgezogen,
sondern erst an ihrer Stelle im Ablauf angelegt. Ergebnis: schwerer Fehler,
sobald die Anmeldung aus dem Token wiederhergestellt werden sollte.

**Behoben:** Die Blog-Konstanten stehen jetzt vor dem gesamten ausführbaren
Teil der Datei. Zusätzlich wurde geprüft, dass keine weitere Konstante
irgendwo zu spät definiert wird.

## 2.9.1 — Blog-Hinweis über dem Kalender

- Über dem Kalender erscheint jetzt ein kompaktes Fenster mit dem Titel des
  zuletzt veröffentlichten Blogbeitrags, inklusive Datum und Autor; ein Klick
  führt direkt zum Blog
- Entwürfe werden dort nicht angezeigt, nur veröffentlichte Beiträge
- Gibt es noch keinen Beitrag oder lässt sich der Blog nicht abrufen, bleibt
  das Fenster ausgeblendet — der Kalender funktioniert unabhängig davon

## 2.9.0 — Blog mit Autorenrecht, Editor, Tags und RSS

Der bisherige Menü-Platzhalter „Tagebuch" ist jetzt ein vollwertiger Blog
unter `blog.php`.

**Neues Recht „Autor"**
- Admins vergeben das Recht im Adminbereich über eine Checkbox im
  Benutzer-Dialog; in der Benutzerliste erscheint ein „Autor"-Abzeichen
- Administratoren haben das Autorenrecht automatisch
- Autoren dürfen eigene Beiträge schreiben, bearbeiten und löschen;
  Administratoren zusätzlich alle fremden Beiträge

**Editor**
- Selbst gebauter WYSIWYG-Editor (bewusst ohne externe Bibliothek: keine
  Abhängigkeit von fremden CDNs, keine Lockerung der Sicherheits-Header,
  funktioniert auch offline in der installierten App)
- Fett, kursiv, unterstrichen, durchgestrichen, Überschriften, Aufzählungen,
  nummerierte Listen, Zitate, Formatierung entfernen
- Links auf externe Inhalte (Pflicht: `http://` oder `https://`)
- Bild-Upload: Server prüft den tatsächlichen Dateiinhalt statt nur der
  Endung, verkleinert automatisch auf maximal 1600 Pixel Breite und speichert
  unter einem Zufallsnamen in `assets/blog/`. Die Anzeigebreite je Bild lässt
  sich beim Einfügen festlegen.
- YouTube-Links werden erkannt und als anklickbares Vorschaubild mit
  Play-Symbol eingebettet
- Beiträge lassen sich als Entwurf speichern (nicht öffentlich sichtbar)

**Tags & Filter**
- Bis zu 8 Tags je Beitrag, kommagetrennt erfassbar
- Filter-Widget rechts neben den Beiträgen mit Anzahl je Thema; im
  Responsive-Design rutscht es unter die Beitragsliste
- Der gewählte Filter steht in der Adresszeile und ist damit teilbar

**RSS**
- Feed unter `feed.php` (RSS 2.0, inkl. Volltext und Kategorien), im Blog
  verlinkt und im Seitenkopf für Reader hinterlegt

**Sicherheit**
- Beitragsinhalte werden serverseitig gegen eine Positivliste erlaubter
  HTML-Elemente und Attribute gefiltert, bevor sie gespeichert werden —
  ohne diesen Filter könnte ein Autor Schadcode einschleusen, der bei allen
  Besuchern im Browser ausgeführt wird
- Bildquellen sind auf eigene Uploads und YouTube-Vorschaubilder beschränkt
- Der Upload-Ordner ist gegen die Ausführung von Programmcode abgesichert

**Aufräumen**
- Gemeinsames Stylesheet und Seiten-Vorspann liegen jetzt in `includes/`,
  damit Kalender und Blog garantiert identisch aussehen

## 2.8.1 — "Angemeldet bleiben" wirklich dauerhaft (Ursache behoben)

**Ursache gefunden:** "Angemeldet bleiben" verlängerte bisher nur den
Browser-Cookie (30 Tage) und versuchte per `ini_set()`, PHPs serverseitige
Sitzungs-Aufräumung zu verzögern. Auf vielen Servern (u. a. Debian/Ubuntu,
vermutlich auch bei dir) räumt aber ein Cron-/Systemd-Job Sitzungsdateien
anhand der echten `php.ini`-Einstellung auf — ein `ini_set()` zur Laufzeit
hat darauf keinen Einfluss. Nach der System-Standardzeit (oft ~24 Minuten
Inaktivität) war die Sitzung serverseitig weg, der Cookie im Browser aber
noch gültig — Ergebnis: ungewollter Logout trotz gesetztem Haken.

**Fix:** "Angemeldet bleiben" läuft jetzt komplett unabhängig von PHPs
Sitzungsverwaltung:

- Beim Login/Registrieren mit gesetztem Haken wird ein eigenes, dauerhaftes
  Token erzeugt und in der normalen Datenspeicherung (`data/store.php`)
  abgelegt — nicht in der von der Server-Aufräumung betroffenen Sitzung
- Das Token besteht aus einem Selektor (Nachschlage-Schlüssel) und einem
  Validator, von dem nur der Hash gespeichert wird (wie bei Passwörtern) —
  ein Leck der Datendatei macht die Tokens nicht direkt nutzbar
- Fehlt beim nächsten Seitenaufruf die Sitzung, aber ein gültiges Token-Cookie
  ist vorhanden, wird die Anmeldung automatisch und unbemerkt wiederhergestellt
- Tokens laufen nach 30 Tagen ab und werden bei jedem Speichervorgang
  automatisch aufgeräumt
- Abmelden, Passwortänderung (eigenständig wie durch Admin) und
  Konto-Löschung widerrufen die zugehörigen Tokens jetzt korrekt

## 2.8.0 — Spieltage entstehen aus den Accounts statt aus festen Wochentagen

**Fehler behoben:** Feste Wochentage aus dem Account (z. B. Freitag) tauchten
gar nicht im Kalender auf. Ursache: Der Kalender erzeugte seine Termine
ausschließlich aus fest einprogrammierten Mittwochen und Sonntagen. Die
persönlichen Wochentage wurden nur auf bereits vorhandene Termine angewendet —
lag ein Wochentag außerhalb von Mi/So, gab es schlicht keine Spalte dafür.

**Neue Logik:** Spieltage entstehen jetzt aus drei Quellen, ohne feste Vorgabetage:

1. den festen Wochentagen, die Spieler in ihrem Account hinterlegt haben
2. Terminen, an denen ein Spieler ausdrücklich einen Status gesetzt hat
3. zusätzlich von Hand angelegten Spieltagen

- Mittwoch und Sonntag sind damit nichts Besonderes mehr — sie erscheinen nur
  noch, wenn sie tatsächlich jemandes fester Tag sind, jemand dort einen Status
  gesetzt hat oder sie von Hand angelegt wurden
- Mittwoche und Sonntage lassen sich jetzt auch von Hand als Spieltag anlegen
  (war vorher blockiert, weil sie automatisch erschienen)
- Wie viele der so ermittelten Tage angezeigt werden, regelt weiterhin die
  Breiten-Automatik (mindestens 4, normal 8, bei mehr Platz mehr)

## 2.7.1 — Status-Zellen wieder zusammengefasst, Legende ohne Badge-Rahmen

- Status-Zellen wieder als eine einzige zusammenhängende Box (Rand + Hintergrund nur einmal um Status, Spiel und Notiz herum) statt drei separater Einzel-Pillen, weiterhin ohne Icon
- Legende oben (Online/Später/Verhindert/Urlaub/Offen) zeigt jetzt nur noch den farbigen Punkt plus Text, ganz ohne umrandete Badge-Fläche drumherum

## 2.7.0 — Kalender-Badges im Vorschau-Stil, Transparenz-Feinabstimmung

- Status-Zellen umgebaut auf saubere, gestapelte Pillen-Badges (passend zur früheren Archon-Vorschau): Hauptstatus ohne Icon als runde Pille, darunter bei Bedarf eine eigene Pille für das Spiel und eine für die Notiz
- Kalender-Tabelle bleibt zu ~90% deckend (gut lesbar), die umgebende Box drumherum ist jetzt zu ~50% transparent und lässt den Seitenhintergrund stärker durchscheinen — themenabhängig berechnet, damit Sommer/Winter ihre Farbstimmung behalten

## 2.6.4 — Stale-Cache-Problem behoben (Updates ohne Strg+F5)

Vermutlich die eigentliche Ursache des Registrierungs-Problems von eben: die
Website wurde vom Browser zwischengespeichert, sodass nach einem Update
weiterhin die alte Version geladen wurde, bis manuell hart neu geladen
wurde (Strg+F5).

- `index.php` sendet jetzt `Cache-Control: no-store` — der Browser darf die
  Seite gar nicht erst zwischenspeichern, jeder Aufruf lädt garantiert die
  aktuelle Version (kleiner Kompromiss: etwas mehr Datenübertragung pro
  Aufruf, für eine kleine Gruppen-Website unproblematisch)
- Service-Worker-Cache-Version war seit Version 2.2.0 nie mehr hochgezählt
  worden — jetzt aktualisiert und wird ab sofort bei jedem Release mitgezogen
- Seite lädt sich jetzt automatisch neu, sobald im Hintergrund ein neuer
  Service Worker aktiv wird — bisher musste man das manuell per Hard-Refresh
  anstoßen, jetzt passiert es von selbst

## 2.6.3 — Deutlichere Passwort-Fehler bei der Registrierung

- Live-Checkliste unter den Passwortfeldern im „Account anlegen"-Dialog: zeigt in Echtzeit beim Tippen, welche Regel (Länge, Buchstabe, Zahl, Sonderzeichen, Übereinstimmung) schon erfüllt ist (grüner Haken) und welche noch fehlt
- Beim Absenden mit fehlerhaftem Passwort werden die offenen Punkte rot markiert, das betroffene Feld bekommt einen roten Rahmen und wird automatisch fokussiert, zusätzlich weiterhin eine Toast-Meldung
- Damit sollte ein Passwortfehler beim Registrieren nicht mehr unbemerkt bleiben

## 2.6.2 — M+-Balken nach Gesamtwertung statt höchstem Key

- Balkenlänge und Gold-Hervorhebung im M+-Widget richten sich jetzt nach der Raider.IO-Gesamtwertung (Score) statt nach dem höchsten geschafften Schlüsselstufen-Level
- Der Schlüsselstufen-Level (`+13` usw.) wird weiterhin wie gehabt angezeigt, ist aber nicht mehr maßgeblich für die Balkenlänge

## 2.6.1 — Statusleiste & Spalten-Regel korrigiert

- Header überlappte auf dem Smartphone mit der Frontkamera/Statusleiste (Uhrzeit, Akku, Empfang) — Seiteninhalt bekommt jetzt automatisch Abstand zur sicheren Zone (`env(safe-area-inset-*)`), passt sich an jedes Gerät individuell an
- Spalten-Regel korrigiert: **mindestens 4**, **normal 8**, bei ausreichend Platz füllt es sich mit mehr Spalten nach rechts auf. Vorher war fälschlich ein Minimum von 7 fest verdrahtet, wodurch auf schmalen Bildschirmen zu viele, zu schmale Spalten erzwungen wurden ("gequetschter" Kalender)

## 2.6.0 — Responsive Design wiederhergestellt

Seit dem Archon-Redesign wurden mehrere neue Bauteile (Header mit Logo/Menü,
kompakte Kalender-Tabelle, Erfolge-Widgets, Hintergrundbilder) ergänzt, ohne
dass die mobile Ansicht mitgezogen wurde. Das ist jetzt nachgeholt:

- **Header komplett neu für Mobile:** Logo + „Kellerkinder" und das Menü
  (Kalender/Tagebuch/Netzje) standen bisher in einer einzigen Zeile
  zusammengequetscht. Jetzt bricht das Menü sauber in eine eigene Zeile um,
  Logo/Schrift-/Menügrößen sind für schmale Bildschirme reduziert.
- **Dynamische Spaltenanzahl korrigiert:** Die Berechnung, wie viele
  Kalendertage angezeigt werden, ging bisher von der Desktop-Spaltenbreite
  aus — auf dem Handy wurden dadurch zu wenige oder unpassend viele Tage
  angefragt. Rechnet jetzt korrekt mit den mobilen Maßen.
- **Hintergrundbild-Performance:** `background-attachment: fixed` ist auf
  kleinen Bildschirmen (≤480px) bekannt für Ruckler/Fehldarstellung in
  mobilen Browsern (v. a. iOS Safari) — dort jetzt auf normales Scrollen
  umgestellt.
- **Überlauf-Schutz ergänzt:** Erfolge-Meilensteinzeilen, M+-Wertungszeilen
  und Admin-Nutzerkarten brechen jetzt sauber um, statt bei langen Texten
  über den Rand hinauszulaufen.
- **Admin-Editor-Dialog** (Erfolge bearbeiten) stapelt seine Eingabezeilen
  auf schmalen Bildschirmen sauber statt sie zusammenzudrücken.
- Verwaiste CSS-Reste vom alten (großen, zentrierten) Header entfernt.

## 2.5.2 — Transparenz/Dunkelheit einzelner Kästchen justiert

- Account-Leiste (Fischje/Admin-Zeile) transparenter, damit der Hintergrund stärker durchscheint
- Kalender-Box und Erfolge-Box etwas dunkler — über eine neue, themenabhängige Variable (`--panel-deep`), damit Sommer/Winter ihre eigene Farbstimmung behalten statt auf ein festes Grau zu wechseln

## 2.5.1 — Hintergrundbilder heller

- CSS-Verdunklungsebene deutlich abgeschwächt (Standard 55%→28%, Sommer/Winter 50%→25%) — wirkt auf alle Bilder, auch künftig hochgeladene
- Sichtbarer Bildbereich vor dem Rand-Verlauf vergrößert (mehr vom Foto bleibt erkennbar)
- Bestehendes WoW-Screenshot-Bild war zusätzlich noch von der ursprünglichen manuellen Bearbeitung vorverdunkelt (doppelte Abdunklung) — durch eine naturbelassenere Version ersetzt, jetzt übernimmt CSS die komplette Verdunklung

## 2.5.0 — Hintergrundbild-Ordner (zufällig / fest je Theme)

- Neue Ordnerstruktur `assets/backgrounds/default|summer|winter/`: Bilder werden unbearbeitet hochgeladen, Abdunkeln und Rand-Verlauf ins Schwarze passiert automatisch per CSS (kein serverseitiges Bildbearbeiten nötig)
- **Standard-Theme:** beliebig viele Bilder ablegen, bei jedem Seitenaufruf wird eines zufällig gewählt
- **Sommer- und Winter-Theme:** genau ein festes Bild pro Ordner, wechselt nicht — liegt zusätzlich unter dem bestehenden Wellen-/Tannenbaum-Muster
- Leere Ordner sind kein Problem: Fällt dann automatisch auf das bisherige Muster ohne Foto zurück
- Bild-Dateien sind über `.gitignore` vom Repository ausgeschlossen (persönlicher Inhalt), README-Dateien in jedem Ordner erklären die Nutzung
- Vorhandenes WoW-Screenshot-Hintergrundbild in `assets/backgrounds/default/` verschoben (bleibt als erstes Bild im Zufallspool erhalten)

## 2.4.11 — Fließtext ebenfalls auf Montserrat

- Fließtext-Schrift von der Systemschrift auf Montserrat umgestellt (bereits geladen für die Überschriften), diese bleiben unverändert im bisherigen Stil (GROSSBUCHSTABEN, Schriftschnitt Black)

## 2.4.10 — Feinjustierung: Hintergrund, Panel-Farben, Tabellenbreite

- Hintergrundgrafik im Standard-Theme 10% heller
- Graue Kästchen-Flächen (Panel-Farben) im Standard-Theme 20% dunkler
- Kalender-Spalten strecken sich jetzt gleichmäßig bis zum rechten Rand der Kästchen-Box (nur die Spielerspalte bleibt fest bei 150px) — schließt die Lücke, die trotz dynamischer Spaltenanzahl auf breiten Bildschirmen noch offen war

## 2.4.9 — Hintergrundbild (Standard-Theme)

- Vom Nutzer bereitgestelltes WoW-Raid-Screenshot als sehr dezente Hintergrundgrafik im Standard-Theme ergänzt: stark abgedunkelt, an allen Rändern (besonders links/rechts) weich ins Schwarze verlaufend, liegt hinter dem bestehenden Grün-Blau-Raster und dem Glow
- Nur im Standard-Theme aktiv, Sommer/Winter bleiben unverändert bei ihrer eigenen Farbfläche

## 2.4.8 — Glanzeffekt, Menü-Reihenfolge, Schriftschnitt

- Dezenter Glanz-Verlauf (wie bei archon.gg) auf allen Kalender-Zellen (Online/Später/Verhindert/Urlaub/Offen) sowie allen Badges (Legende, Du-/Admin-Badge, Standard-Tag, Vergangen-Tag, Heute-Tag, M+-Score-Badge, Beispiel-Tag)
- Menü-Reihenfolge getauscht: Kalender · Tagebuch (folgt) · Netzje (folgt)
- Alle Überschriften (Kellerkinder-Schriftzug, Online-Kalender, Dialog-Titel, Erfolge-Kartentitel) jetzt in Schriftschnitt „Black" (900)

## 2.4.7 — Menü-Bug behoben, dynamische Spaltenanzahl

- **Menü-Fehler gefunden:** Ein CSS-Sonderfall (wird nur `overflow-x` gesetzt, hebt der Browser `overflow-y` automatisch auf „auto") hat einen unsichtbaren vertikalen Scrollbalken mit Auf/Ab-Pfeilen im Menü erzeugt. Jetzt explizit `overflow-y: hidden` gesetzt.
- Server liefert jetzt bis zu 21 kommende Spieltage statt nur 7 (Sicherheitsprüfungen bleiben dadurch konsistent, unabhängig vom Browserfenster)
- Frontend zeigt davon so viele an, wie auf den Bildschirm passen — mindestens aber weiterhin 7 kommende Tage plus ggf. den einen vergangenen Tag; passt sich beim Fenster-Größenändern automatisch an

## 2.4.6 — Schrift getauscht, Spielerspalte breiter

- Fließtext wieder auf die ursprüngliche Systemschrift zurückgestellt
- Überschriften (Kalender-Titel, Dialog-Titel, Erfolge-Kartentitel, „Kellerkinder"-Schriftzug) jetzt in Montserrat, durchgängig in GROSSBUCHSTABEN dargestellt
- JetBrains Mono nicht mehr eingebunden (nicht mehr benötigt)
- Spielerspalte deutlich breiter (150px, mobil 118px) — Name inkl. Avatar wird nicht mehr abgeschnitten; alle anderen Spalten bleiben schmal

## 2.4.5 — Spaltenbreite korrigiert, Spiel/Notiz wieder sichtbar

- **Ursache für die zu breiten Spalten gefunden und behoben:** Die Tabelle wurde per `width: 100%` immer auf die volle Containerbreite gestreckt, unabhängig vom tatsächlichen Platzbedarf. Jetzt bestimmen feste Spaltenbreiten (Datumsspalten 74px, mobil 58px) die tatsächliche Größe — die Tabelle ist nur noch so breit wie nötig.
- Spiel- und Notiz-Angabe sind wieder direkt sichtbar unter Icon/Status (klein und mit „…" gekürzt bei Platzmangel), nicht mehr nur im Tooltip versteckt — das war ein Fehler, danke für den Hinweis
- Die frühere JS-Breitenberechnung entfernt, die teils mit den CSS-Werten kollidierte (v. a. mobil)

## 2.4.4 — Typografie & Header-Feinschliff

- Google Fonts eingebunden: „JetBrains Mono" für Überschriften (Kalender-Titel, Dialog-Titel, Erfolge-Kartentitel, Logo-Schriftzug), „Montserrat" für Fließtext — CSP entsprechend erweitert (fonts.googleapis.com/fonts.gstatic.com)
- „Kellerkinder"-Schriftzug im Header deutlich größer
- Unterüberschrift „Online-Gaming mit Freunden seit ewig" jetzt klein und linksbündig direkt unter Logo/Schriftzug statt als eigene zentrierte Zeile
- Menü-Größenfehler behoben (Flexbox-Bug: Navigation konnte nicht schrumpfen/scrollen und hat das Layout verzerrt)

## 2.4.3 — Tabelle kompakter, Zeitgrenze, Heute-Hervorhebung

- Tabelle nochmal deutlich schmaler: Spielerspalte 118px → 88px, Statuszellen jetzt kompakte Pillen (Icon + Label) statt großer Kästen mit Spiel/Notiz-Text — die Info steht weiterhin als Tooltip beim Antippen/Hovern zur Verfügung
- Spielernamen brechen nicht mehr um, sondern werden bei Platzmangel mit „…" abgeschnitten
- Letzter vergangener Spieltag verschwindet jetzt exakt um 12 Uhr mittags des Folgetages (vorher: ganzer Folgetag)
- Heutige Spalte wird in der Kopfzeile und im Tabellenkörper leicht hervorgehoben, Kopfzeile zeigt zusätzlich ein kleines „Heute"-Label

## 2.4.2 — Theme-Hintergründe (Punkt 5)

- Standard/RGB-Theme: echtes Schwarz als Hintergrund, großes Raster in Grün und Blau, zur Mitte hin betont und an den Rändern ausgeblendet
- Sommer-Theme: Hintergrund auf dunkelblau korrigiert (vorher fälschlich dunkelteal), echte durchgezogene Wellenlinien (zwei Ebenen) statt Halbkreis-Bögen, schwitzende Sonne (☀️💦) neben dem „Kellerkinder"-Schriftzug nur in diesem Theme, sandiger Strand-Streifen am unteren Bildschirmrand statt der bisherigen türkisen Wasserlinie
- Winter-Theme: Hintergrund auf neutrales Dunkelgrau korrigiert (vorher blaustichig), Raster aus gekachelten stilisierten Tannenbäumen (zwei versetzte Ebenen für einen natürlicheren Look) statt Linienraster, Schneefall unverändert beibehalten, Schneehaufen-Streifen am unteren Bildschirmrand statt der bisherigen flachen Verlaufsfläche

## 2.4.1 — Weitere Angleichung an die Design-Vorschau

- Header umgebaut: kleines Logo + „Kellerkinder"-Schriftzug links, statt großem zentriertem Logo/Titel
- Neues Menüband im Header: Kalender · Netzje (folgt) · Tagebuch (folgt)
- „Was soll das?" ist jetzt ein kompakter Icon-Button ganz unten (statt Textbutton)
- Legende zeigt jetzt einfache farbige Punkte statt umrandeter Icon-Kacheln
- M+-Bestwerte (höchste Schlüsselstufe) heben sich jetzt farblich in Gold ab, alle anderen in Violett — sowohl beim Wertungs-Badge als auch beim Balken
- Diverses CSS-Aufräumen (verwaistes Fragment beim Install-Button behoben)

## 2.4.0 — Design-Überarbeitung (Archon-Stil)

- Komplettes visuelles Redesign angelehnt an archon.gg: flache Kästchen mit Haarlinien-Rahmen statt Neon-Verlaufsrahmen, ruhiger dunkler Hintergrund statt pulsierender Farbflecken
- Neue Farbpalette: Violett als Marken-/Link-/Button-Akzent, Gold für Bestleistungen/Scores, in allen drei Themes (Standard/Sommer/Winter) konsistent umgesetzt
- Neue Rundungs-Stufen (16px Kästchen, 10px Buttons/Eingabefelder, 8px kleine Chips) — wirkt automatisch auf alle Dialoge, da diese dasselbe CSS-Grundgerüst teilen
- Kalender-Tabelle spürbar kompakter: schmalere Spielerspalte, geringere Zeilenhöhe, kleinere Status-Kästchen, flacher Chip-Look statt leuchtender Verlaufsbuttons
- Erfolge-Widgets (Statistik- und Link-Kästchen) auf den neuen Kartenstil umgestellt, Scores als Gold-Chip
- Tailwind CSS per CDN eingebunden (für künftige Komponenten), CSP entsprechend angepasst
- Verwaiste, ungenutzte CSS-Reste aus früheren Umbauten entfernt

## 2.3.3 — Sicherheitsüberprüfung

**Kritisch behoben:**
- Die automatisch angelegte Migrations-Sicherung (`data/store.php.before-accounts-backup`) endete nicht auf `.php` und wurde dadurch vom Webserver als Klartext ausgeliefert statt vom eingebauten Schutzvorspann blockiert — inklusive aller Passwort-Hashes. Umbenannt zu `data/store-before-accounts-backup.php`, Schutzvorspann wird jetzt zusätzlich garantiert geschrieben. Dieselbe Falle steckte in den in README.md/GITHUB_SETUP.md dokumentierten manuellen Backup-Befehlen (`cp data/store.php data/store.php.backup-manual`) — dort ebenfalls korrigiert.
- XSS-Lücke im WoW-Erfolge-Widget: Charakter- und Dungeon-Namen von Raider.IO wurden ungefiltert per `innerHTML` eingefügt. Auf sichere DOM-Erstellung umgestellt, Profil-Links werden zusätzlich auf `https://`-Schema geprüft.

**Ergänzt:**
- Security-Header: Content-Security-Policy (mit Nonce für das Inline-Skript), X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy.
- `check.php` bekommt `noindex`-Header und Basis-Härtung (sollte nach der Einrichtung dennoch vom Produktivserver entfernt werden).
- Verzeichnisrechte für `data/` vereinheitlicht (0750 statt 0775).
- Eingabereihenfolge bei der Link-Validierung der Erfolge-Widgets robuster gemacht (Längenprüfung vor Formatprüfung).

**Geprüft und für sicher befunden:** Passwort-Hashing (bcrypt), CSRF-Schutz, Session-Fixation-Schutz, automatische Session-Invalidierung bei Passwortänderung, Zugriffskontrolle auf allen Admin- und Besitzer-geschützten Aktionen (inkl. Schutz vor Admin-Namen-Übernahme durch normale Nutzer, Schutz des letzten Administrator-Kontos vor Löschung), Avatar-Upload-Validierung, keine SQL-Injection-Fläche (Flat-File-Storage), kein `eval`/`exec`, Service-Worker cached keine sensiblen Daten, keine offenen CORS-Header.

**Hinweis für den Betrieb:** `data/.htaccess` wirkt nur unter Apache — falls der Server (wie bei dieser Installation) mit Caddy läuft, hat diese Datei keine Wirkung. Der Schutz für `data/store.php` läuft dann ausschließlich über den PHP-Schutzvorspann; eine zusätzliche Caddy-Regel wird empfohlen (siehe Dokumentation/Chat).

## 2.3.2

- WoW-Erfolge-Widget zuverlässiger gemacht: Charaktere fielen bisher gelegentlich komplett aus der Liste, wenn eine einzelne Raider.IO-Anfrage fehlschlug (z. B. Timeout) — jetzt gibt es pro Charakter einen Retry sowie einen Fallback auf den letzten bekannten Stand, statt den Charakter unsichtbar zu machen
- Timeout je Charakter wieder auf 6 Sekunden angehoben, dazu ein User-Agent-Header für die Raider.IO-Anfragen ergänzt

## 2.3.1

- Hintergrundgrafiken aller drei Themes überarbeitet: cartoonhafte Elemente (Sonnen-Icon mit Strahlen, Emoji-Zeile, Christbäume, geschmückter Kaminofen) durch abstrakte, zur jeweiligen Farbpalette passende Lichteffekte ersetzt (Glow-Flächen, Aurora-Streifen, Horizont-Verläufe)
- Admin-Passwort-Reset für andere Benutzer benötigt keine Komplexitätsregeln mehr (nur noch: nicht leer, beide Felder identisch); bei Neuanlage eines Kontos sowie bei der eigenen Passwortänderung gelten die Regeln unverändert weiter
- Versionsnummer wird ab sofort bei jeder Auslieferung mitgeführt (Datei- und Footer-Version)

## 2.3.0

- Erfolge-Bereich unter dem Kalender ergänzt: pro Spiel (WoW, Heroes of the Storm, Diablo IV, Rocket League) ein Statistik- und ein Link-Kästchen, per Pfeilen durchblätterbar
- WoW-Mythisch-Plus-Statistik läuft automatisch über die Raider.IO-API (Charakterliste konfigurierbar)
- Statistik- und Link-Kästchen (Überschrift, Inhalte) sind für die anderen drei Spiele sowie die Links aller Spiele über die Adminoberfläche einzeln bearbeitbar
- Ausführlicher Erklärtext („So funktioniert der Kalender“) aus der Hauptseite in einen eigenen „Was soll das?“-Dialog ausgelagert
- „Angemeldet bleiben“-Option beim Anmelden/Registrieren (30 Tage, gerätebezogen)
- PHP-Session-Sperre wird beim Erfolge-Abruf sofort freigegeben, damit lang laufende externe Anfragen nicht mehr andere Anfragen (z. B. Admin-Speichern) blockieren
- Ladeanzeigen im gesamten Kalender vereinheitlicht

## 2.2.0

- Spielwunsch je Spieler und Termin ergänzt
- Bereits genannte Spiele werden als gemeinsame Dropdown-Vorschläge angeboten
- Spielwünsche werden direkt in den Kalenderfeldern angezeigt
- PWA-Manifest, Service Worker und App-Symbole ergänzt
- Smartphone-Schaltfläche oben rechts startet die Installation oder zeigt gerätespezifische Hinweise
- Dynamischer Footer mit „Created by Fischje with ♥ Version 2.2.0“ ergänzt
- Bestehende Datendateien werden ohne Migration weiterverwendet

## 2.1.1

- Öffnen des Spieltag-Dialogs für normale Benutzer repariert
- Erkennung sichtbarer Sonderzeichen bei neuen Passwörtern robuster umgesetzt
- Umlaute werden korrekt als Buchstaben bewertet

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
