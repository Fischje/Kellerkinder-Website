// Pfeile am Hauptmenü: zeigt „›“, wenn rechts noch Menüpunkte versteckt sind, und „‹“ nach dem Scrollen.
// Ein Tipp auf den Pfeil schiebt das Menü um etwa eine Bildschirmbreite weiter.
(function () {
    'use strict';
    const wrap = document.querySelector('.main-nav-wrap');
    if (!wrap) return;
    const nav = wrap.querySelector('.main-nav');
    const left = wrap.querySelector('.nav-arrow-left');
    const right = wrap.querySelector('.nav-arrow-right');

    function update() {
        const max = nav.scrollWidth - nav.clientWidth;
        left.hidden = nav.scrollLeft <= 2;
        right.hidden = max <= 2 || nav.scrollLeft >= max - 2;
    }
    function scrollBy(direction) {
        nav.scrollBy({ left: direction * Math.max(120, nav.clientWidth * 0.7), behavior: 'smooth' });
    }

    left.addEventListener('click', () => scrollBy(-1));
    right.addEventListener('click', () => scrollBy(1));
    nav.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update);
    if ('ResizeObserver' in window) new ResizeObserver(update).observe(nav);
    // Menüpunkte werden nachträglich ein-/ausgeblendet (Admin, Eindrücke)
    new MutationObserver(update).observe(nav, { attributes: true, attributeFilter: ['hidden'], subtree: true });
    // Aktive Seite ins Sichtfeld holen
    const active = nav.querySelector('.nav-link.active');
    if (active && active.offsetLeft + active.offsetWidth > nav.clientWidth) nav.scrollLeft = active.offsetLeft - 16;
    update();
})();
