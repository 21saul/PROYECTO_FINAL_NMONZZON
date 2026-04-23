/*
 * =============================================================================
 * SCRIPT FRONT: PUBLIC/ASSETS/JS/CART.JS
 * =============================================================================
 * QUÉ HACE: COMPORTAMIENTO EN EL NAVEGADOR (UI, PETICIONES, INTEGRACIONES) PARA ESTA PARTE DEL SITIO.
 * POR QUÍ EN JS: INTERACTIVIDAD SIN RECARGA; SE CARGA DONDE LO PIDE EL LAYOUT O LA VISTA.
 * =============================================================================
 */

(function () {
    'use strict';

    function getCsrf() {
        const n = document.querySelector('meta[name="csrf-token-name"]');
        const h = document.querySelector('meta[name="csrf-token-hash"]');
        if (n && h) return { name: n.content, hash: h.content };
        return { name: 'csrf_test_name', hash: '' };
    }

    function postJSON(url, data) {
        const csrf = getCsrf();
        data[csrf.name] = csrf.hash;
        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        }).then(r => r.json());
    }

    function fmt(n) {
        return n.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function updateSummary(totals) {
        if (!totals) return;
        const s = document.getElementById('cart-subtotal');
        const sh = document.getElementById('cart-shipping');
        const t = document.getElementById('cart-tax');
        const d = document.getElementById('cart-discount');
        const tot = document.getElementById('cart-total');
        if (s) s.textContent = fmt(totals.subtotal) + ' €';
        if (sh) sh.textContent = totals.shipping <= 0 ? 'Gratis' : fmt(totals.shipping) + ' €';
        if (t) t.textContent = fmt(totals.tax) + ' €';
        if (d) d.textContent = fmt(totals.discount);
        if (tot) tot.textContent = fmt(totals.total) + ' €';
    }

    function updateBadge(count) {
        const badge = document.querySelector('.cart-badge');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? '' : 'none';
        }
    }

    // Quantity buttons
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.cart-qty-btn');
        if (!btn) return;
        const group = btn.closest('.cart-qty-group');
        if (!group) return;
        const input = group.querySelector('.cart-qty-input');
        if (!input) return;
        let val = parseInt(input.value) || 1;
        if (btn.dataset.action === 'increase') val++;
        else if (btn.dataset.action === 'decrease' && val > 1) val--;
        input.value = val;

        const key = group.dataset.key;
        if (!key) return;
        postJSON(window.location.origin + '/carrito/update', { key: key, quantity: val })
            .then(data => {
                if (data.success) {
                    updateSummary(data.totals);
                    updateBadge(data.itemCount);
                    const row = document.querySelector('[data-key="' + key + '"]');
                    if (row) {
                        const lineTotals = row.querySelectorAll('.cart-line-total');
                        const price = parseFloat(row.querySelector('td:nth-child(2)')?.textContent?.replace(/[^\d,]/g, '').replace(',', '.')) || 0;
                        lineTotals.forEach(el => {
                            el.textContent = fmt(price * val) + ' €';
                        });
                    }
                }
            })
            .catch(() => {});
    });

    // Quantity input change
    document.addEventListener('change', function (e) {
        if (!e.target.classList.contains('cart-qty-input')) return;
        const group = e.target.closest('.cart-qty-group');
        if (!group) return;
        const key = group.dataset.key;
        const val = Math.max(1, parseInt(e.target.value) || 1);
        e.target.value = val;
        if (!key) return;
        postJSON(window.location.origin + '/carrito/update', { key: key, quantity: val })
            .then(data => {
                if (data.success) {
                    updateSummary(data.totals);
                    updateBadge(data.itemCount);
                }
            })
            .catch(() => {});
    });

    // Remove buttons
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.cart-remove-btn');
        if (!btn) return;
        e.preventDefault();
        const key = btn.dataset.key;
        if (!key) return;
        postJSON(window.location.origin + '/carrito/remove', { key: key })
            .then(data => {
                if (data.success) {
                    document.querySelectorAll('[data-key="' + key + '"]').forEach(el => {
                        el.style.transition = 'opacity 0.3s';
                        el.style.opacity = '0';
                        setTimeout(() => el.remove(), 300);
                    });
                    updateSummary(data.totals);
                    updateBadge(data.itemCount);
                    if (data.itemCount === 0) {
                        setTimeout(() => window.location.reload(), 350);
                    }
                }
            })
            .catch(() => {});
    });

    // Apply coupon
    const applyBtn = document.getElementById('apply-coupon-btn');
    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            const code = document.getElementById('coupon-code')?.value?.trim();
            const msg = document.getElementById('coupon-msg');
            if (!code) { if (msg) msg.textContent = 'Introduce un código.'; return; }
            postJSON(window.location.origin + '/carrito/apply-coupon', { coupon_code: code })
                .then(data => {
                    if (msg) {
                        msg.textContent = data.message || '';
                        msg.className = 'small mt-1 ' + (data.success ? 'text-success' : 'text-danger');
                    }
                    if (data.success) {
                        updateSummary(data.totals);
                        const row = document.getElementById('coupon-row');
                        const wrap = document.getElementById('coupon-form-wrap');
                        const display = document.getElementById('coupon-code-display');
                        if (row) row.classList.remove('d-none');
                        if (wrap) wrap.style.display = 'none';
                        if (display) display.textContent = code.toUpperCase();
                    }
                })
                .catch(() => {});
        });
    }

    // Remove coupon
    document.addEventListener('click', function (e) {
        if (!e.target.closest('#remove-coupon-btn')) return;
        postJSON(window.location.origin + '/carrito/remove-coupon', {})
            .then(data => {
                if (data.success) {
                    updateSummary(data.totals);
                    const row = document.getElementById('coupon-row');
                    const wrap = document.getElementById('coupon-form-wrap');
                    if (row) row.classList.add('d-none');
                    if (wrap) wrap.style.display = '';
                }
            })
            .catch(() => {});
    });

    // Añadir al carrito (grid productos, ficha): envío como formulario real para que CSRF y PHP reciban los campos.
    document.addEventListener('submit', function (e) {
        const form = e.target.closest('.add-to-cart-form');
        if (!form) return;
        e.preventDefault();
        const action = form.getAttribute('action') || (window.location.origin + '/carrito/add');
        fetch(action, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form),
            credentials: 'same-origin',
        })
            .then(function (r) {
                const ct = r.headers.get('content-type') || '';
                if (ct.indexOf('application/json') === -1) {
                    return null;
                }
                return r.json().then(function (body) {
                    return { ok: r.ok, status: r.status, body: body };
                });
            })
            .then(function (wrapped) {
                if (!wrapped) {
                    form.submit();
                    return;
                }
                const res = wrapped.body;
                if (res.success) {
                    updateBadge(res.itemCount);
                    const modalEl = document.getElementById('cart-added-modal');
                    if (modalEl && typeof bootstrap !== 'undefined') {
                        const product = res.product || {};
                        const nameEl  = modalEl.querySelector('#cart-added-name');
                        const priceEl = modalEl.querySelector('#cart-added-price');
                        const countEl = modalEl.querySelector('#cart-added-count');
                        const thumbEl = modalEl.querySelector('#cart-added-thumb');
                        if (nameEl) nameEl.textContent = product.name || 'Producto añadido';
                        if (priceEl && typeof product.price === 'number') {
                            priceEl.textContent = fmt(product.price) + ' €';
                            priceEl.style.display = '';
                        } else if (priceEl) {
                            priceEl.style.display = 'none';
                        }
                        if (countEl) {
                            const items = res.itemCount || 0;
                            countEl.textContent = items === 1
                                ? '1 artículo en tu carrito'
                                : items + ' artículos en tu carrito';
                        }
                        if (thumbEl) {
                            if (product.image) {
                                thumbEl.src = product.image;
                                thumbEl.alt = product.name || '';
                            }
                        }
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    }
                    return;
                }
                let msg = res.message || 'No se pudo añadir al carrito.';
                if (res.errors && typeof res.errors === 'object') {
                    const first = Object.values(res.errors)[0];
                    if (first) {
                        msg = typeof first === 'string' ? first : msg;
                    }
                }
                window.alert(msg);
            })
            .catch(function () {
                form.submit();
            });
    });
})();
