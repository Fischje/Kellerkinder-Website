// Gemeinsamer Text-Editor (Blog-Beiträge, Startseite „Home“).
// Markup: includes/rich-editor.php (Werkzeugleiste, Eingabefeld, verstecktes Datei-Feld).
// Aufruf: const editor = kkRichEditor({ root, api, showToast });
//   root      Element, das includes/rich-editor.php ausgegeben hat ([data-rich-editor])
//   api       (action, payload) => Promise – POST an api.php (mit CSRF), z. B. 'blog_upload_image'
//   showToast (text) => void – kurzer Hinweis
// Rückgabe: { surface, setHtml(html), getHtml(), prepareForSave() }
//   prepareForSave() lädt noch eingebettete Bilder (data-URL) hoch und gibt false zurück, wenn der
//   Nutzer wegen fremder Bild-Adressen abbricht.
// Bilder: über „Bild“, per Einfügen (Strg+V) oder Hineinziehen. Sie werden im Browser auf höchstens
// 1600 px verkleinert und unter ~700 KB gehalten (viele Server nehmen nur 1 MB pro Anfrage an) und
// dann hochgeladen; im Text steht nur die Adresse. FileReader + data-URL, weil die CSP blob: blockiert.
(function () {
    'use strict';

    const IMAGE_MAX_WIDTH = 1600;
    const MAX_DATA_URL_LENGTH = 950000;

    function readAsDataUrl(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(String(reader.result || ''));
            reader.onerror = () => reject(new Error('Das Bild konnte nicht gelesen werden.'));
            reader.readAsDataURL(file);
        });
    }

    function loadImage(src) {
        return new Promise((resolve, reject) => {
            const image = new Image();
            image.onload = () => resolve(image);
            image.onerror = () => reject(new Error('Das Bild konnte nicht geöffnet werden (nur PNG, JPEG, GIF oder WebP).'));
            image.src = src;
        });
    }

    /** Verkleinert ein Bild (data-URL); GIFs bleiben (Animation), kleine PNGs bleiben PNG, sonst JPEG. */
    async function shrinkDataUrl(dataUrl) {
        if (/^data:image\/gif;/.test(dataUrl) && dataUrl.length < 7500000) return dataUrl;
        const image = await loadImage(dataUrl);
        const scale = Math.min(1, IMAGE_MAX_WIDTH / image.naturalWidth);
        if (/^data:image\/png;/.test(dataUrl) && scale === 1 && dataUrl.length < 2000000) return dataUrl;
        // Erst die Qualität senken, dann notfalls die Größe
        let width = Math.max(1, Math.round(image.naturalWidth * scale));
        let result = '';
        for (let attempt = 0; attempt < 6; attempt++) {
            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = Math.max(1, Math.round(image.naturalHeight * (width / image.naturalWidth)));
            const context = canvas.getContext('2d');
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, canvas.width, canvas.height);
            context.drawImage(image, 0, 0, canvas.width, canvas.height);
            for (const quality of [0.86, 0.74, 0.62]) {
                result = canvas.toDataURL('image/jpeg', quality);
                if (result.length <= MAX_DATA_URL_LENGTH) return result;
            }
            width = Math.max(1, Math.round(width * 0.75));
        }
        return result;
    }

    function youtubeId(url) {
        const patterns = [
            /(?:youtube\.com\/watch\?(?:.*&)?v=)([A-Za-z0-9_-]{6,20})/,
            /(?:youtu\.be\/)([A-Za-z0-9_-]{6,20})/,
            /(?:youtube\.com\/(?:embed|shorts)\/)([A-Za-z0-9_-]{6,20})/,
        ];
        for (const pattern of patterns) {
            const match = url.match(pattern);
            if (match) return match[1];
        }
        return null;
    }

    window.kkRichEditor = function kkRichEditor({ root, api, showToast }) {
        const surface = root.querySelector('[data-editor-surface]');
        const fileInput = root.querySelector('[data-editor-file]');

        function runCommand(command) {
            surface.focus();
            document.execCommand(command, false, undefined);
        }

        function applyBlock(tag) {
            surface.focus();
            document.execCommand('formatBlock', false, tag);
        }

        function insertNode(node) {
            surface.focus();
            const selection = window.getSelection();
            if (selection && selection.rangeCount > 0 && surface.contains(selection.anchorNode)) {
                const range = selection.getRangeAt(0);
                range.deleteContents();
                range.insertNode(node);
                range.setStartAfter(node);
                selection.removeAllRanges();
                selection.addRange(range);
            } else {
                surface.appendChild(node);
            }
            surface.appendChild(document.createElement('br'));
        }

        async function uploadDataUrl(dataUrl) {
            const result = await api('blog_upload_image', { image: await shrinkDataUrl(dataUrl) });
            return result.url;
        }

        async function insertImageFile(file, askWidth) {
            if (!/^image\/(png|jpeg|gif|webp)$/.test(file.type)) {
                showToast('Nur PNG-, JPEG-, GIF- oder WebP-Bilder.');
                return;
            }
            if (file.size > 30000000) {
                showToast('Das Bild ist zu groß (über 30 MB).');
                return;
            }
            showToast('Bild wird hochgeladen …');
            try {
                const image = document.createElement('img');
                image.src = await uploadDataUrl(await readAsDataUrl(file));
                image.alt = '';
                insertNode(image);
                showToast('Bild eingefügt.');
                if (askWidth) {
                    const width = window.prompt('Anzeigebreite in Pixeln (leer lassen für volle Breite):', '');
                    if (width && /^\d+$/.test(width)) image.setAttribute('width', width);
                }
            } catch (error) {
                showToast(error.message);
            }
        }

        const imageFiles = (dataTransfer) => [...((dataTransfer && dataTransfer.files) || [])].filter((file) => file.type.startsWith('image/'));

        for (const button of root.querySelectorAll('[data-cmd]')) {
            button.addEventListener('click', () => runCommand(button.dataset.cmd));
        }
        for (const button of root.querySelectorAll('[data-block]')) {
            button.addEventListener('click', () => applyBlock(button.dataset.block));
        }

        root.querySelector('[data-editor-action="link"]').addEventListener('click', () => {
            const url = window.prompt('Link-Adresse (mit https:// beginnen):', 'https://');
            if (!url) return;
            if (!/^https?:\/\//i.test(url)) {
                showToast('Der Link muss mit http:// oder https:// beginnen.');
                return;
            }
            surface.focus();
            document.execCommand('createLink', false, url);
        });

        root.querySelector('[data-editor-action="youtube"]').addEventListener('click', () => {
            const url = window.prompt('YouTube-Link einfügen:', 'https://');
            if (!url) return;
            const id = youtubeId(url);
            if (!id) {
                showToast('Das sieht nicht nach einem YouTube-Link aus.');
                return;
            }
            const anchor = document.createElement('a');
            anchor.className = 'yt-embed';
            anchor.href = `https://www.youtube.com/watch?v=${id}`;
            const thumb = document.createElement('img');
            thumb.src = `https://i.ytimg.com/vi/${id}/hqdefault.jpg`;
            thumb.alt = 'YouTube-Video ansehen';
            const play = document.createElement('span');
            play.className = 'yt-embed-play';
            play.textContent = '▶';
            anchor.append(thumb, play);
            insertNode(anchor);
        });

        root.querySelector('[data-editor-action="image"]').addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', async (event) => {
            const file = event.target.files && event.target.files[0];
            event.target.value = '';
            if (file) await insertImageFile(file, true);
        });

        // Einfügen (Strg+V) bzw. Hineinziehen: hochladen statt als riesige data-URL in den Text
        surface.addEventListener('paste', async (event) => {
            const files = imageFiles(event.clipboardData);
            if (files.length === 0) return;
            event.preventDefault();
            for (const file of files) await insertImageFile(file, false);
        });
        surface.addEventListener('drop', async (event) => {
            const files = imageFiles(event.dataTransfer);
            if (files.length === 0) return;
            event.preventDefault();
            for (const file of files) await insertImageFile(file, false);
        });

        /** Vor dem Speichern: eingebettete Bilder hochladen; fremde Bild-Adressen melden (der Server entfernt sie). */
        async function prepareForSave() {
            let foreign = 0;
            for (const image of [...surface.querySelectorAll('img')]) {
                const src = image.getAttribute('src') || '';
                if (src.startsWith('data:image/')) {
                    showToast('Bild wird hochgeladen …');
                    image.setAttribute('src', await uploadDataUrl(src));
                } else if (/^https?:/i.test(src) && !/^https:\/\/i\.ytimg\.com\//.test(src)) {
                    foreign++;
                }
            }
            if (foreign === 0) return true;
            return window.confirm(`${foreign === 1 ? 'Ein Bild stammt' : `${foreign} Bilder stammen`} von einer anderen Website und ${foreign === 1 ? 'wird' : 'werden'} nicht übernommen. Bitte das Bild speichern und über „Bild“ hochladen. Trotzdem ohne ${foreign === 1 ? 'dieses Bild' : 'diese Bilder'} speichern?`);
        }

        return {
            surface,
            setHtml: (html) => { surface.innerHTML = html; },
            getHtml: () => surface.innerHTML,
            prepareForSave,
        };
    };
})();
