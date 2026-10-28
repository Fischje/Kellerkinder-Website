<?php
// Markup des gemeinsamen Text-Editors (Logik: assets/rich-editor.js, Aufruf kkRichEditor()).
// Vor dem Einbinden optional setzen:
//   $richEditorId           ID des Eingabefelds (für <label for>), Standard 'editorSurface'
//   $richEditorPlaceholder  Platzhalter im leeren Feld
$richEditorId = $richEditorId ?? 'editorSurface';
$richEditorPlaceholder = $richEditorPlaceholder ?? 'Schreib hier deinen Text …';
$richEditorEscape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
?>
<div data-rich-editor>
    <div class="editor-toolbar" role="toolbar" aria-label="Formatierung">
        <button type="button" data-cmd="bold" title="Fett"><b>B</b></button>
        <button type="button" data-cmd="italic" title="Kursiv"><i>I</i></button>
        <button type="button" data-cmd="underline" title="Unterstrichen"><u>U</u></button>
        <button type="button" data-cmd="strikeThrough" title="Durchgestrichen"><s>S</s></button>
        <span class="sep"></span>
        <button type="button" data-block="h2" title="Überschrift">H2</button>
        <button type="button" data-block="h3" title="Zwischenüberschrift">H3</button>
        <button type="button" data-block="p" title="Normaler Text">¶</button>
        <span class="sep"></span>
        <button type="button" data-cmd="insertUnorderedList" title="Aufzählung">• Liste</button>
        <button type="button" data-cmd="insertOrderedList" title="Nummerierte Liste">1. Liste</button>
        <button type="button" data-block="blockquote" title="Zitat">❝</button>
        <span class="sep"></span>
        <button type="button" data-editor-action="link" title="Link einfügen">🔗 Link</button>
        <button type="button" data-editor-action="image" title="Bild hochladen">🖼 Bild</button>
        <button type="button" data-editor-action="youtube" title="YouTube-Video einbetten">▶ YouTube</button>
        <span class="sep"></span>
        <button type="button" data-cmd="removeFormat" title="Formatierung entfernen">✗</button>
    </div>
    <div class="editor-surface" id="<?= $richEditorEscape($richEditorId) ?>" data-editor-surface contenteditable="true"
         data-placeholder="<?= $richEditorEscape($richEditorPlaceholder) ?>"></div>
    <p class="editor-hint">Bilder über „Bild“, per Einfügen (Strg+V) oder Hineinziehen – sie werden automatisch auf maximal 1600 Pixel Breite verkleinert. Über „Bild“ kannst du die Anzeigebreite anpassen.</p>
    <input type="file" data-editor-file accept="image/png,image/jpeg,image/gif,image/webp" hidden>
</div>
