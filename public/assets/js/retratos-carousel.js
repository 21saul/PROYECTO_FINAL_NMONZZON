/*
 * =============================================================================
 * SCRIPT FRONT: PUBLIC/ASSETS/JS/RETRATOS-CAROUSEL.JS
 * =============================================================================
 * QUÉ HACE: COMPORTAMIENTO EN EL NAVEGADOR (UI, PETICIONES, INTEGRACIONES) PARA ESTA PARTE DEL SITIO.
 * POR QUÍ EN JS: INTERACTIVIDAD SIN RECARGA; SE CARGA DONDE LO PIDE EL LAYOUT O LA VISTA.
 * =============================================================================
 */

/*
 * =============================================================================
 * SCRIPT FRONT: PUBLIC/ASSETS/JS/RETRATOS-CAROUSEL.JS
 * =============================================================================
 * QUÉ HACE: COMPORTAMIENTO EN EL NAVEGADOR (UI, PETICIONES, INTEGRACIONES) PARA ESTA PARTE DEL SITIO.
 * POR QUÍ EN JS: INTERACTIVIDAD SIN RECARGA; SE CARGA DONDE LO PIDE EL LAYOUT O LA VISTA.
 * =============================================================================
 */

/**
 * Carrusel de estilos (Retratos): movimiento horizontal continuo y suave, en bucle.
 * Duplica el contenido para un cierre invisible; segunda copia con aria-hidden.
 */
(function () {
    'use strict';

    var carousel = document.getElementById('stylesCarousel');

    if (!carousel) {
        return;
    }

    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var originalHtml = carousel.innerHTML.trim();

    if (!originalHtml) {
        return;
    }

    var rafId = null;
    var running = false;
    var hoverPause = false;

    /** Velocidad suave (marquee editorial, no “cinta” rápida) */
    var SPEED_PX = 0.22;

    function markCloneAriaHidden() {
        var cards = carousel.querySelectorAll('.ret-style-card');
        var half = cards.length / 2;
        var i;
        for (i = half; i < cards.length; i++) {
            cards[i].setAttribute('aria-hidden', 'true');
        }
    }

    function buildMarqueeStrip() {
        carousel.innerHTML = originalHtml + originalHtml;
        markCloneAriaHidden();
    }

    function getLoopWidth() {
        return carousel.scrollWidth / 2;
    }

    function tick() {
        if (!running) {
            return;
        }

        if (document.hidden) {
            stop();
            return;
        }

        var loopW = getLoopWidth();
        if (loopW > 4) {
            carousel.scrollLeft += SPEED_PX;
            if (carousel.scrollLeft >= loopW - 0.5) {
                carousel.scrollLeft -= loopW;
            }
        }

        rafId = window.requestAnimationFrame(tick);
    }

    function start() {
        if (reducedMotion) {
            return;
        }
        if (running) {
            return;
        }
        running = true;
        rafId = window.requestAnimationFrame(tick);
    }

    function stop() {
        running = false;
        if (rafId !== null) {
            window.cancelAnimationFrame(rafId);
            rafId = null;
        }
    }

    buildMarqueeStrip();

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            stop();
        } else if (!reducedMotion && !hoverPause) {
            start();
        }
    });

    carousel.addEventListener('mouseenter', function () {
        if (reducedMotion) {
            return;
        }
        hoverPause = true;
        stop();
    });

    carousel.addEventListener('mouseleave', function () {
        if (reducedMotion) {
            return;
        }
        hoverPause = false;
        if (!document.hidden) {
            start();
        }
    });

    var resizeTimer;
    window.addEventListener('resize', function () {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(function () {
            stop();
            carousel.innerHTML = originalHtml;
            buildMarqueeStrip();
            carousel.scrollLeft = 0;
            if (!reducedMotion && !hoverPause) {
                start();
            }
        }, 200);
    });

    if (!reducedMotion) {
        start();
    }
})();
