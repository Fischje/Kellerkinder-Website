<?php
// Abschnitt „Erfolge“ (Seite Spieler der Spiele-Statistik): Steam-Erfolge und – wenn auf admin.php
// eingeschaltet – World of Warcraft (M+-Wertungen). Inhalt und Logik: assets/achievements.js.
// Erwartet $appVersion (bootstrap.php). Der Bearbeiten-Dialog (Links des WoW-Kastens, nur Admins) gehört dazu.
require_once __DIR__ . '/features.php';
$achievementsShowWow = siteFeatures()['home']['wow_mplus'];
?>
<section class="games-panel players-achievements" data-achievements data-show-wow="<?= $achievementsShowWow ? '1' : '0' ?>" aria-labelledby="achievementsTitle">
    <div class="games-head">
        <h2 class="section-title" id="achievementsTitle"><span class="accent">Unsere</span> Erfolge</h2>
    </div>
    <div class="achievement-stack" data-achievements-grid></div>
</section>

<dialog id="achievementEditDialog" class="wide">
    <div class="modal-content form-stack">
        <h2 class="modal-title" id="achievementEditTitle">Widget bearbeiten</h2>
        <p class="modal-subtitle">Überschrift und Inhalt für dieses Kästchen.</p>

        <div>
            <label for="achievementEditTitleInput">Überschrift</label>
            <input type="text" id="achievementEditTitleInput" maxlength="40" placeholder="Standard-Name verwenden">
        </div>

        <div id="achievementEditStatsSection">
            <label>Statistik (bis zu 8 Zeilen, je Bezeichnung + Wert)</label>
            <div id="achievementEditRows" class="achievement-edit-rows"></div>
            <button class="secondary-button" id="achievementEditAddRow" type="button">+ Zeile hinzufügen</button>
        </div>

        <div id="achievementEditLinksSection">
            <label>Links (bis zu 6, je Linktext + URL)</label>
            <div id="achievementEditLinkRows" class="achievement-edit-rows"></div>
            <button class="secondary-button" id="achievementEditAddLinkRow" type="button">+ Link hinzufügen</button>
        </div>

        <div class="modal-actions">
            <button class="secondary-button" type="button" data-close-dialog="achievementEditDialog">Abbrechen</button>
            <button class="primary-button" id="achievementEditSave" type="button">Speichern</button>
        </div>
    </div>
</dialog>
<script src="assets/achievements.js?v=<?= htmlspecialchars((string) $appVersion, ENT_QUOTES, 'UTF-8') ?>" defer></script>
