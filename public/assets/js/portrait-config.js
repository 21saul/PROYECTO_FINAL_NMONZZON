/*
 * =============================================================================
 * SCRIPT FRONT: PUBLIC/ASSETS/JS/PORTRAIT-CONFIG.JS
 * =============================================================================
 * QUÉ HACE: COMPORTAMIENTO EN EL NAVEGADOR (UI, PETICIONES, INTEGRACIONES) PARA ESTA PARTE DEL SITIO.
 * POR QUÍ EN JS: INTERACTIVIDAD SIN RECARGA; SE CARGA DONDE LO PIDE EL LAYOUT O LA VISTA.
 * =============================================================================
 */

/*
 * =============================================================================
 * SCRIPT FRONT: PUBLIC/ASSETS/JS/PORTRAIT-CONFIG.JS
 * =============================================================================
 * QUÉ HACE: COMPORTAMIENTO EN EL NAVEGADOR (UI, PETICIONES, INTEGRACIONES) PARA ESTA PARTE DEL SITIO.
 * POR QUÍ EN JS: INTERACTIVIDAD SIN RECARGA; SE CARGA DONDE LO PIDE EL LAYOUT O LA VISTA.
 * =============================================================================
 */

/**
 * CONFIGURADOR DE RETRATOS (ASISTENTE POR PASOS).
 * DEPENDE DE portraitStyles, portraitSizes Y VARIABLES GLOBALES (TOKEN API, CSRF).
 * GESTIONA ESTILO, TAMAÑO, FIGURAS, MARCO, PRECIO Y ENVÍO DEL PEDIDO POR FETCH.
 */
document.addEventListener('DOMContentLoaded', () => {
    if (typeof portraitStyles === 'undefined' || typeof portraitSizes === 'undefined') {
        return;
    }

    let currentStep = 1;
    const totalSteps = 5;
    let selectedStyleId = null;
    let selectedSizeId = null;
    let numFigures = 1;
    let withFrame = false;

    const form = document.getElementById('portrait-config-form');
    const btnPrev = document.getElementById('wizard-prev');
    const btnNext = document.getElementById('wizard-next');
    const btnSubmit = document.getElementById('wizard-submit');
    const stepsEl = document.getElementById('wizard-steps');
    const withFrameCb = document.getElementById('with_frame');
    const frameTypeWrap = document.getElementById('frame-type-wrap');
    const frameTypeSelect = document.getElementById('frame_type');
    const figuresInput = document.getElementById('num_figures');
    const loginHint = document.getElementById('portrait-login-hint');
    const feedbackEl = document.getElementById('portrait-order-feedback');

    /* LIMPIA LOS TEXTOS DE ERROR DE TODOS LOS PASOS */
    function clearStepErrors() {
        for (let s = 1; s <= 5; s++) {
            const el = document.getElementById(`error-step-${s}`);
            if (el) {
                el.textContent = '';
            }
        }
    }

    /* ASIGNA MENSAJE DE ERROR A UN PASO CONCRETO */
    function setStepError(step, msg) {
        const el = document.getElementById(`error-step-${step}`);
        if (el) el.textContent = msg;
    }

    /* MUESTRA EL PANEL ACTIVO, ACTUALIZA INDICADORES Y BOTONES ANTERIOR/SIGUIENTE/ENVIAR */
    function showStep(step) {
        currentStep = Math.max(1, Math.min(totalSteps, step));

        document.querySelectorAll('.wizard-panel').forEach((panel) => {
            const n = parseInt(panel.getAttribute('data-panel'), 10);
            panel.classList.toggle('is-active', n === currentStep);
        });

        if (stepsEl) {
            stepsEl.querySelectorAll('.wizard-step').forEach((el) => {
                const n = parseInt(el.getAttribute('data-step'), 10);
                el.classList.remove('active', 'completed');
                if (n < currentStep) el.classList.add('completed');
                if (n === currentStep) el.classList.add('active');
            });
        }

        if (btnPrev) btnPrev.disabled = currentStep === 1;
        if (btnNext) btnNext.classList.toggle('d-none', currentStep === totalSteps);
        if (btnSubmit) btnSubmit.classList.toggle('d-none', currentStep !== totalSteps);

        if (currentStep === totalSteps) {
            updateResumen();
            updatePrecio();
        }
    }

    /* LEE DEL DOM ESTILO, TAMAÑO, NÚMERO DE FIGURAS Y SI LLEVA MARCO */
    function readSelectionFromDom() {
        const sr = document.querySelector('input[name="portrait_style_id"]:checked');
        const zr = document.querySelector('input[name="portrait_size_id"]:checked');
        selectedStyleId = sr ? sr.value : null;
        selectedSizeId = zr ? zr.value : null;
        if (figuresInput) {
            numFigures = Math.min(10, Math.max(1, parseInt(figuresInput.value, 10) || 1));
            figuresInput.value = String(numFigures);
        }
        withFrame = !!(withFrameCb && withFrameCb.checked);
    }

    /* VALIDACIÓN AL AVANZAR DE PASO (1 A 4) */
    function validateStep(step) {
        readSelectionFromDom();
        clearStepErrors();

        if (step === 1) {
            if (!selectedStyleId) {
                setStepError(1, 'Selecciona un estilo para continuar.');
                return false;
            }
        }
        if (step === 2) {
            if (!selectedSizeId) {
                setStepError(2, 'Selecciona un tamaño para continuar.');
                return false;
            }
        }
        if (step === 3) {
            if (numFigures < 1 || numFigures > 10) {
                setStepError(3, 'El número de figuras debe estar entre 1 y 10.');
                return false;
            }
        }
        if (step === 4) {
            if (withFrame && frameTypeSelect && (!frameTypeSelect.value || frameTypeSelect.value === '')) {
                setStepError(4, 'Selecciona un tipo de marco o desactiva la opción de marco.');
                return false;
            }
        }
        return true;
    }

    /* VALIDACIÓN COMPLETA ANTES DE ENVIAR (PASO 5); VUELVE AL PRIMER PASO CON ERROR */
    function validateAllSteps() {
        readSelectionFromDom();
        clearStepErrors();
        let ok = true;
        if (!selectedStyleId) {
            setStepError(1, 'Selecciona un estilo.');
            ok = false;
        }
        if (!selectedSizeId) {
            setStepError(2, 'Selecciona un tamaño.');
            ok = false;
        }
        if (numFigures < 1 || numFigures > 10) {
            setStepError(3, 'El número de figuras debe estar entre 1 y 10.');
            ok = false;
        }
        if (withFrame && frameTypeSelect && (!frameTypeSelect.value || frameTypeSelect.value === '')) {
            setStepError(4, 'Selecciona un tipo de marco o desactiva la opción de marco.');
            ok = false;
        }
        if (!ok) {
            const firstBad = !selectedStyleId ? 1 : !selectedSizeId ? 2 : numFigures < 1 || numFigures > 10 ? 3 : 4;
            showStep(firstBad);
        }
        return ok;
    }

    /* AVANZA AL SIGUIENTE PASO SI LA VALIDACIÓN ACTUAL ES CORRECTA */
    function nextStep() {
        if (!validateStep(currentStep)) return;
        if (currentStep < totalSteps) {
            showStep(currentStep + 1);
        }
    }

    /* RETROCEDE UN PASO EN EL ASISTENTE */
    function prevStep() {
        if (currentStep > 1) {
            showStep(currentStep - 1);
        }
    }

    /* SINCRONIZA CLASE VISUAL DE LAS TARJETAS DE ESTILO CON EL RADIO MARCADO */
    function syncStyleCards() {
        document.querySelectorAll('.style-choice-card').forEach((card) => {
            const input = card.closest('label')?.querySelector('.style-radio');
            card.classList.toggle('is-selected', !!(input && input.checked));
        });
    }

    /* SINCRONIZA CLASE VISUAL DE LAS TARJETAS DE TAMAÑO */
    function syncSizeCards() {
        document.querySelectorAll('.size-choice-card').forEach((card) => {
            const input = card.closest('label')?.querySelector('.size-radio');
            card.classList.toggle('is-selected', !!(input && input.checked));
        });
    }

    /* CONSULTA AL SERVIDOR PARA OBTENER EL PRECIO REAL CALCULADO POR PortraitPricingService */
    let precioAbortController = null;

    async function updatePrecio() {
        const el = document.getElementById('precio-total');
        if (!el) return;

        if (!selectedStyleId || !selectedSizeId) {
            el.textContent = '—';
            return;
        }

        el.textContent = 'Calculando...';

        if (precioAbortController) {
            precioAbortController.abort();
        }
        precioAbortController = new AbortController();

        try {
            const csrf = (function () {
                const n = document.querySelector('meta[name="csrf-token-name"]');
                const h = document.querySelector('meta[name="csrf-token-hash"]');
                return n && h ? { name: n.content, hash: h.content } : { name: 'csrf_test_name', hash: '' };
            })();

            const formData = new FormData();
            formData.append('style_id', selectedStyleId);
            formData.append('size_id', selectedSizeId);
            formData.append('num_figures', String(numFigures));
            formData.append('with_frame', withFrame ? '1' : '0');
            formData.append(csrf.name, csrf.hash);

            const res = await fetch('/retratos/calcular-precio', {
                method: 'POST',
                body: formData,
                signal: precioAbortController.signal,
            });

            const data = await res.json();
            if (data.success) {
                el.textContent = new Intl.NumberFormat('es-ES', {
                    style: 'currency',
                    currency: 'EUR',
                }).format(data.price);
            } else {
                el.textContent = '—';
            }
        } catch (err) {
            if (err.name !== 'AbortError') {
                el.textContent = '—';
            }
        }
    }

    /* TEXTO LEGIBLE DEL TIPO DE MARCO SELECCIONADO EN EL SELECT */
    function labelForFrameType(val) {
        if (!val) return 'Sin especificar';
        if (!frameTypeSelect) return val;
        const opt = Array.from(frameTypeSelect.options).find((o) => o.value === val);
        return opt ? opt.textContent : val;
    }

    /* RELLENA EL RESUMEN DEL ÚLTIMO PASO CON LAS ELECCIONES ACTUALES */
    function updateResumen() {
        readSelectionFromDom();
        const style = portraitStyles.find((s) => String(s.id) === String(selectedStyleId));
        const size = portraitSizes.find((s) => String(s.id) === String(selectedSizeId));

        const elEstilo = document.getElementById('resumen-estilo');
        const elTam = document.getElementById('resumen-tamano');
        const elFig = document.getElementById('resumen-figuras');
        const elMarco = document.getElementById('resumen-marco');

        if (elEstilo) elEstilo.textContent = style ? style.name : '—';
        if (elTam) {
            elTam.textContent = size
                ? `${size.name} (${size.dimensions || ''})`
                : '—';
        }
        if (elFig) elFig.textContent = String(numFigures);
        if (elMarco) {
            if (!withFrame) {
                elMarco.textContent = 'Sin marco';
            } else {
                elMarco.textContent = `Con marco — ${labelForFrameType(frameTypeSelect?.value || '')}`;
            }
        }
    }

    /* PRECARGA ESTILO DESDE ?ESTILO= EN LA URL */
    function applyUrlPreselect() {
        const params = new URLSearchParams(window.location.search);
        const raw = params.get('estilo');
        if (!raw) return;
        const estilo = String(raw).replace(/\D/g, '');
        if (!estilo) return;
        const input = document.querySelector(`input[name="portrait_style_id"][value="${estilo}"]`);
        if (input) {
            input.checked = true;
            syncStyleCards();
            selectedStyleId = estilo;
        }
    }

    /* MUESTRA/OCULTA SELECT DE TIPO DE MARCO Y RECALCULA PRECIO */
    function toggleFrameUi() {
        withFrame = !!(withFrameCb && withFrameCb.checked);
        if (frameTypeWrap) {
            frameTypeWrap.classList.toggle('d-none', !withFrame);
        }
        if (!withFrame && frameTypeSelect) {
            frameTypeSelect.value = '';
        }
        updatePrecio();
    }

    document.querySelectorAll('.style-radio').forEach((radio) => {
        radio.addEventListener('change', () => {
            syncStyleCards();
            readSelectionFromDom();
            updatePrecio();
        });
    });

    document.querySelectorAll('.size-radio').forEach((radio) => {
        radio.addEventListener('change', () => {
            syncSizeCards();
            readSelectionFromDom();
            updatePrecio();
        });
    });

    /* La selección va por <label> + input en overlay (.portrait-choice-input); no hace falta capturar clic en la tarjeta. */

    /* BOTÓN MENOS: DECREMENTA FIGURAS (MÍNIMO 1) */
    document.getElementById('figures-minus')?.addEventListener('click', () => {
        if (figuresInput) {
            const v = Math.max(1, (parseInt(figuresInput.value, 10) || 1) - 1);
            figuresInput.value = String(v);
            readSelectionFromDom();
            updatePrecio();
        }
    });

    /* BOTÓN MÁS: INCREMENTA FIGURAS (MÁXIMO 10) */
    document.getElementById('figures-plus')?.addEventListener('click', () => {
        if (figuresInput) {
            const v = Math.min(10, (parseInt(figuresInput.value, 10) || 1) + 1);
            figuresInput.value = String(v);
            readSelectionFromDom();
            updatePrecio();
        }
    });

    figuresInput?.addEventListener('input', () => {
        readSelectionFromDom();
        updatePrecio();
    });

    withFrameCb?.addEventListener('change', toggleFrameUi);
    frameTypeSelect?.addEventListener('change', () => {
        readSelectionFromDom();
        updatePrecio();
    });

    btnPrev?.addEventListener('click', prevStep);
    btnNext?.addEventListener('click', nextStep);

    /* ENVÍO DEL PEDIDO: EXIGE TOKEN; POST JSON A LA API DE RETRATOS */
    form?.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!validateAllSteps()) {
            return;
        }

        if (typeof portraitApiAccessToken === 'undefined' || !portraitApiAccessToken) {
            if (feedbackEl) {
                const loginHref = typeof nmzLoginUrl !== 'undefined' ? nmzLoginUrl : '/login';
                feedbackEl.innerHTML =
                    '<div class="alert alert-warning mb-0">Debes iniciar sesión para enviar el pedido. <a href="' +
                    escapeHtml(loginHref) +
                    '">Acceder</a></div>';
            }
            loginHint?.classList.remove('d-none');
            return;
        }

        readSelectionFromDom();
        const body = {
            portrait_style_id: parseInt(selectedStyleId, 10),
            portrait_size_id: parseInt(selectedSizeId, 10),
            num_figures: numFigures,
            with_frame: withFrame,
            frame_type: withFrame && frameTypeSelect ? frameTypeSelect.value : '',
            client_notes: document.getElementById('client_notes')?.value || '',
        };
        body[nmzCsrfTokenName] = nmzCsrfHash;

        if (feedbackEl) feedbackEl.innerHTML = '';

        try {
            const headers = {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                Authorization: `Bearer ${portraitApiAccessToken}`,
            };
            if (typeof nmzCsrfHeader !== 'undefined' && nmzCsrfHash) {
                headers[nmzCsrfHeader] = nmzCsrfHash;
            }

            const res = await fetch(nmzPortraitApiUrl, {
                method: 'POST',
                headers,
                body: JSON.stringify(body),
                credentials: 'same-origin',
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                const msg =
                    data.message ||
                    data.error ||
                    (data.errors && (typeof data.errors === 'object' ? JSON.stringify(data.errors) : String(data.errors))) ||
                    `Error ${res.status}`;
                if (feedbackEl) {
                    feedbackEl.innerHTML = `<div class="alert alert-danger mb-0">${escapeHtml(String(msg))}</div>`;
                }
                return;
            }

            if (feedbackEl) {
                feedbackEl.innerHTML =
                    '<div class="alert alert-success mb-0">Pedido registrado correctamente. Puedes seguirlo desde tu cuenta.</div>';
            }
            if (form) form.reset();
            selectedStyleId = null;
            selectedSizeId = null;
            numFigures = 1;
            if (figuresInput) figuresInput.value = '1';
            withFrame = false;
            if (withFrameCb) withFrameCb.checked = false;
            toggleFrameUi();
            syncStyleCards();
            syncSizeCards();
            showStep(1);
        } catch (err) {
            if (feedbackEl) {
                feedbackEl.innerHTML = `<div class="alert alert-danger mb-0">No se pudo enviar. ${escapeHtml(err.message || '')}</div>`;
            }
        }
    });

    /* UTILIDAD: ESCAPA CARACTERES HTML PARA INSERTAR EN INNERHTML DE FORMA SEGURA */
    function escapeHtml(s) {
        const d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }

    syncStyleCards();
    syncSizeCards();
    toggleFrameUi();
    applyUrlPreselect();
    readSelectionFromDom();
    updatePrecio();
    showStep(1);
});
