/*
 * =============================================================================
 * SCRIPT FRONT: PUBLIC/ASSETS/JS/GALLERY-CAROUSEL.JS
 * =============================================================================
 * QUÉ HACE: Controla los carruseles horizontales masonry de "Arte en Vivo" y
 *           "Retratos". Un único botón "Ver más" desplaza el track hacia la
 *           derecha; al llegar al final, vuelve al inicio.
 * POR QUÍ EN JS: Scroll controlado por el usuario sin recargas; teclado accesible.
 * =============================================================================
 */
(function () {
    'use strict';

    function bindMasonry(root) {
        var track = root.querySelector('.ret-hmasonry__track');
        var btn = root.querySelector('[data-hmasonry-more]');

        if (!track) {
            return;
        }

        function scrollRight() {
            var step = Math.max(240, Math.round(track.clientWidth * 0.85));
            var maxScroll = track.scrollWidth - track.clientWidth - 4;

            if (track.scrollLeft >= maxScroll) {
                track.scrollTo({ left: 0, behavior: 'smooth' });
                return;
            }

            track.scrollBy({ left: step, behavior: 'smooth' });
        }

        if (btn) {
            btn.addEventListener('click', scrollRight);
        }

        track.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                track.scrollBy({ left: 220, behavior: 'smooth' });
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                track.scrollBy({ left: -220, behavior: 'smooth' });
            }
        });
    }

    document.querySelectorAll('[data-hmasonry]').forEach(bindMasonry);

    if (typeof GLightbox !== 'undefined') {
        GLightbox({ selector: '.glightbox' });
    }
})();
