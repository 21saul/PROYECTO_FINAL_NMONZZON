/*
 * =============================================================================
 * SCRIPT FRONT: PUBLIC/ASSETS/JS/CAPTCHA-PUZZLE.JS
 * =============================================================================
 * QUÉ HACE: Controla el captcha de puzzle deslizante (mouse + touch + teclado),
 *           normaliza la X a píxeles del servidor y gestiona el botón "Nuevo
 *           desafío" con refresh AJAX (mantiene el CSRF).
 * POR QUÉ EN JS: Interacción drag/slide sin recargas, con fallback accesible.
 * =============================================================================
 */
(function () {
    'use strict';

    function initPuzzle(root) {
        if (!root || root.dataset.nmzCaptchaReady === '1') {
            return;
        }
        root.dataset.nmzCaptchaReady = '1';

        var stage    = root.querySelector('[data-captcha-stage]');
        var bgImg    = root.querySelector('[data-captcha-bg]');
        var pieceImg = root.querySelector('[data-captcha-piece]');
        var status   = root.querySelector('[data-captcha-status]');
        var track    = root.querySelector('[data-captcha-track]');
        var progress = root.querySelector('[data-captcha-progress]');
        var handle   = root.querySelector('[data-captcha-handle]');
        var hint     = root.querySelector('[data-captcha-hint]');
        var answer   = root.querySelector('[data-captcha-answer]');
        var tokenEl  = root.querySelector('[data-captcha-token]');
        var refresh  = root.querySelector('[data-captcha-refresh]');

        if (!stage || !pieceImg || !track || !handle || !answer || !tokenEl) {
            return;
        }

        var state = {
            width:     parseInt(stage.dataset.width, 10) || 320,
            height:    parseInt(stage.dataset.height, 10) || 180,
            pieceSize: parseInt(stage.dataset.pieceSize, 10) || 48,
            pieceY:    parseInt(stage.dataset.pieceY, 10) || 0,
            dragging:  false,
            startPtr:  0,
            currentPx: 0, // posición actual del slider/pieza en píxeles del stage renderizado
            hintText:  hint ? hint.textContent : ''
        };

        function stageRect() {
            return stage.getBoundingClientRect();
        }

        function maxStagePx() {
            // La pieza puede moverse desde 0 hasta (stageRenderedWidth - pieceRenderedWidth).
            return Math.max(1, stage.clientWidth - pieceImg.clientWidth);
        }

        function scaleFactor() {
            // Proporción entre píxeles del servidor y píxeles renderizados.
            return state.width / Math.max(1, stage.clientWidth);
        }

        function clampPx(px) {
            return Math.max(0, Math.min(maxStagePx(), px));
        }

        function setCurrentPx(px, options) {
            state.currentPx = clampPx(px);

            pieceImg.style.transform = 'translate(' + state.currentPx + 'px, 0)';

            var maxHandle = Math.max(1, track.clientWidth - handle.clientWidth);
            var handleX = (state.currentPx / maxStagePx()) * maxHandle;
            handle.style.transform = 'translate(' + handleX + 'px, 0)';

            if (progress) {
                progress.style.width = (handleX + handle.clientWidth / 2) + 'px';
            }

            var pct = Math.round((state.currentPx / maxStagePx()) * 100);
            handle.setAttribute('aria-valuenow', String(pct));

            // X normalizada a píxeles del servidor (lo que espera verify).
            var serverX = Math.round(state.currentPx * scaleFactor());
            answer.value = String(serverX);

            if (options && options.clearStatus) {
                root.classList.remove('nmz-captcha-puzzle--ok', 'nmz-captcha-puzzle--err');
                if (status) { status.textContent = ''; }
            }
        }

        function pointerX(e) {
            if (e.touches && e.touches.length) { return e.touches[0].clientX; }
            if (e.changedTouches && e.changedTouches.length) { return e.changedTouches[0].clientX; }
            return e.clientX;
        }

        function onStart(e) {
            state.dragging = true;
            state.startPtr = pointerX(e) - state.currentPx;
            root.classList.add('nmz-captcha-puzzle--active');
            if (hint) { hint.textContent = 'Suelta cuando la pieza encaje.'; }
            if (e.cancelable) { e.preventDefault(); }
        }

        function onMove(e) {
            if (!state.dragging) { return; }
            setCurrentPx(pointerX(e) - state.startPtr, { clearStatus: true });
            if (e.cancelable) { e.preventDefault(); }
        }

        function onEnd() {
            if (!state.dragging) { return; }
            state.dragging = false;
            root.classList.remove('nmz-captcha-puzzle--active');
            if (hint) { hint.textContent = state.hintText; }
        }

        handle.addEventListener('mousedown', onStart);
        handle.addEventListener('touchstart', onStart, { passive: false });
        document.addEventListener('mousemove', onMove);
        document.addEventListener('touchmove', onMove, { passive: false });
        document.addEventListener('mouseup', onEnd);
        document.addEventListener('mouseleave', onEnd);
        document.addEventListener('touchend', onEnd);
        document.addEventListener('touchcancel', onEnd);

        handle.addEventListener('keydown', function (e) {
            var step = (e.shiftKey ? 10 : 2);
            if (e.key === 'ArrowRight' || e.key === 'Right') {
                setCurrentPx(state.currentPx + step, { clearStatus: true });
                e.preventDefault();
            } else if (e.key === 'ArrowLeft' || e.key === 'Left') {
                setCurrentPx(state.currentPx - step, { clearStatus: true });
                e.preventDefault();
            } else if (e.key === 'Home') {
                setCurrentPx(0, { clearStatus: true });
                e.preventDefault();
            } else if (e.key === 'End') {
                setCurrentPx(maxStagePx(), { clearStatus: true });
                e.preventDefault();
            }
        });

        window.addEventListener('resize', function () {
            // Recalcular para mantener la X de servidor coherente tras resize.
            setCurrentPx(state.currentPx);
        });

        if (refresh) {
            refresh.addEventListener('click', function () {
                var url = root.dataset.refreshUrl;
                if (!url) { return; }

                refresh.disabled = true;
                refresh.classList.add('is-loading');

                var prev = encodeURIComponent(tokenEl.value || '');
                var sep  = url.indexOf('?') === -1 ? '?' : '&';
                var full = url + sep + 'previous=' + prev + '&t=' + Date.now();

                fetch(full, {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function (r) {
                        if (!r.ok) { throw new Error('refresh_failed'); }
                        return r.json();
                    })
                    .then(function (data) {
                        if (!data || !data.token) { throw new Error('refresh_bad_payload'); }

                        tokenEl.value = data.token;

                        var w  = parseInt(data.width, 10)      || state.width;
                        var h  = parseInt(data.height, 10)     || state.height;
                        var ps = parseInt(data.piece_size, 10) || state.pieceSize;
                        var py = parseInt(data.piece_y, 10);
                        if (isNaN(py)) { py = state.pieceY; }

                        state.width = w;
                        state.height = h;
                        state.pieceSize = ps;
                        state.pieceY = py;

                        stage.dataset.width     = String(w);
                        stage.dataset.height    = String(h);
                        stage.dataset.pieceSize = String(ps);
                        stage.dataset.pieceY    = String(py);

                        stage.style.setProperty('--captcha-w', w + 'px');
                        stage.style.setProperty('--captcha-ratio-w', String(w));
                        stage.style.setProperty('--captcha-ratio-h', String(h));

                        pieceImg.style.top   = (100 * py / Math.max(1, h)).toFixed(3) + '%';
                        pieceImg.style.width = (100 * ps / Math.max(1, w)).toFixed(3) + '%';

                        // Evita cache del navegador sobre la URL antigua
                        var bust = '?t=' + Date.now();
                        bgImg.src    = data.bg_url + bust;
                        pieceImg.src = data.piece_url + bust;

                        setCurrentPx(0, { clearStatus: true });
                    })
                    .catch(function () {
                        if (status) { status.textContent = 'No se pudo refrescar el desafío. Inténtalo de nuevo.'; }
                    })
                    .finally(function () {
                        refresh.disabled = false;
                        refresh.classList.remove('is-loading');
                    });
            });
        }

        // Inicializar valor de respuesta = 0 por si el usuario envía sin tocar.
        setCurrentPx(0);
    }

    function bootstrap() {
        document.querySelectorAll('[data-nmz-captcha-puzzle]').forEach(initPuzzle);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootstrap);
    } else {
        bootstrap();
    }
})();
