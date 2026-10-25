// Herunterziehen zum Neuladen – auf allen Seiten (eingebunden über includes/site-footer.php).
// Nur in der installierten App (Startbildschirm, display-mode: standalone) mit Touch:
// Im normalen Browser übernimmt das der Browser selbst.
// Am oberen Rand nach unten ziehen, bis der Pfeil grün wird, dann loslassen.
// Stellt eine Seite window.kkPullRefresh bereit (eine Funktion, die ein Promise liefert),
// lädt sie damit nur ihre Daten neu (z. B. der Kalender); sonst wird die Seite neu geladen.
(function () {
    'use strict';

    const installed = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
    if (!installed || !('ontouchstart' in window)) return;

    // Anzeige oben in der Mitte (Styles: .pull-refresh in includes/styles.php)
    let container = document.getElementById('pullRefresh');
    if (!container) {
        container = document.createElement('div');
        container.id = 'pullRefresh';
        container.className = 'pull-refresh';
        container.setAttribute('aria-hidden', 'true');
        const badge = document.createElement('span');
        badge.className = 'pull-refresh-badge';
        const arrowSpan = document.createElement('span');
        arrowSpan.className = 'pull-refresh-arrow';
        arrowSpan.textContent = '↓';
        badge.appendChild(arrowSpan);
        container.appendChild(badge);
        document.body.prepend(container);
    }
    const arrow = container.querySelector('.pull-refresh-arrow');

    const THRESHOLD = 72;
    const MAX_PULL = 130;
    let startY = 0;
    let pulling = false;
    let ready = false;
    let refreshing = false;

    // Verhindert das „Gummiband“ des Systems, solange wir selbst ziehen
    document.documentElement.style.overscrollBehaviorY = 'contain';

    const pageScrollTop = () => window.scrollY || document.documentElement.scrollTop || document.body.scrollTop || 0;
    const dialogOpen = () => Boolean(document.querySelector('dialog[open]'));

    function reset() {
        pulling = false;
        ready = false;
        container.classList.remove('pull-refresh-visible', 'pull-refresh-ready');
        arrow.style.transform = 'rotate(0deg)';
    }

    window.addEventListener('touchstart', (event) => {
        if (refreshing || dialogOpen() || pageScrollTop() > 0 || event.touches.length !== 1) return;
        startY = event.touches[0].clientY;
        pulling = true;
    }, { passive: true });

    window.addEventListener('touchmove', (event) => {
        if (!pulling || refreshing) return;
        const distance = event.touches[0].clientY - startY;
        if (distance <= 0 || pageScrollTop() > 0) { reset(); return; }
        event.preventDefault();
        const pull = Math.min(MAX_PULL, distance * 0.5);
        ready = pull >= THRESHOLD;
        container.classList.add('pull-refresh-visible');
        container.classList.toggle('pull-refresh-ready', ready);
        arrow.style.transform = `rotate(${Math.min(180, (pull / THRESHOLD) * 180)}deg)`;
    }, { passive: false });

    window.addEventListener('touchend', () => {
        if (!pulling || refreshing) { pulling = false; return; }
        pulling = false;
        if (!ready) { reset(); return; }
        refreshing = true;
        container.classList.add('pull-refresh-loading');
        if (typeof window.kkPullRefresh !== 'function') {
            window.location.reload();
            return;
        }
        Promise.resolve()
            .then(() => window.kkPullRefresh())
            .catch(() => {})
            .finally(() => {
                refreshing = false;
                container.classList.remove('pull-refresh-loading');
                reset();
            });
    });

    window.addEventListener('touchcancel', () => {
        refreshing = false;
        reset();
    });
})();
