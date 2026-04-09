<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/CART/CHECKOUT.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?php // VISTA DE CHECKOUT: FORMULARIO DE ENVÍO, STRIPE, RESUMEN DEL PEDIDO Y SCRIPT DE PROCESAMIENTO ?>
<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Checkout') ?>

<?php
$items    = $items ?? [];
$totals   = $totals ?? [];
$subtotal = (float) ($totals['subtotal'] ?? 0);
$tax      = (float) ($totals['tax'] ?? 0);
$shipping = (float) ($totals['shipping'] ?? 0);
$discount = (float) ($totals['discount'] ?? 0);
$total    = (float) ($totals['total'] ?? 0);
$coupon   = $coupon ?? null;
$user     = session()->get('user') ?? [];
$stripeKey = $stripePublicKey ?? '';
?>

<?= $this->section('content') ?>

<section
    class="page-hero page-hero--tall"
    style="background-image: url('<?= esc(base_url('uploads/retratos/estilos/estilo_color_sin_caras.jpg'), 'attr') ?>');"
>
    <div class="page-hero-overlay" style="background: linear-gradient(180deg, rgba(26,26,26,.55) 0%, rgba(26,26,26,.82) 100%);"></div>
    <div class="container page-hero-content text-center py-5">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Carrito', 'url' => base_url('carrito')],
                ['label' => 'Checkout', 'url' => null],
            ],
            'nmzHeroTitle' => 'Finalizar pedido',
        ]) ?>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <?php if ($flash = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($flash) ?></div>
        <?php endif; ?>

        <div class="row g-4 align-items-start">
            <!-- FORMULARIO DE ENVÍO Y PAGO CON TARJETA -->
            <div class="col-lg-7 min-w-0 order-2 order-lg-1">
                <div class="dashboard-card p-3 p-md-4 mb-4" id="checkout-card">
                    <h2 class="h5 mb-3">Datos de envío</h2>

                    <div class="mb-3">
                        <label for="shipping_name" class="form-label">Nombre completo *</label>
                        <input type="text" class="form-control" id="shipping_name" name="shipping_name" value="<?= esc(old('shipping_name', $user['name'] ?? session()->get('user_name') ?? '')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">Dirección *</label>
                        <input type="text" class="form-control" id="shipping_address" name="shipping_address" value="<?= esc(old('shipping_address') ?? '') ?>" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="shipping_city" class="form-label">Ciudad *</label>
                            <input type="text" class="form-control" id="shipping_city" name="shipping_city" value="<?= esc(old('shipping_city') ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="shipping_postal_code" class="form-label">Código postal *</label>
                            <input type="text" class="form-control" id="shipping_postal_code" name="shipping_postal_code" value="<?= esc(old('shipping_postal_code') ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="shipping_country" class="form-label">País *</label>
                        <select class="form-select" id="shipping_country" name="shipping_country" required>
                            <?php
                            $countries = ['ES' => 'España', 'PT' => 'Portugal', 'FR' => 'Francia', 'DE' => 'Alemania', 'IT' => 'Italia', 'NL' => 'Países Bajos'];
                            $sel = old('shipping_country', 'ES');
                            foreach ($countries as $code => $label):
                            ?>
                            <option value="<?= $code ?>"<?= $sel === $code ? ' selected' : '' ?>><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="shipping_phone" class="form-label">Teléfono *</label>
                        <input type="tel" class="form-control" id="shipping_phone" name="shipping_phone" value="<?= esc(old('shipping_phone', $user['phone'] ?? '')) ?>" required>
                    </div>

                    <h2 class="h5 mb-3">Pago con tarjeta</h2>
                    <div id="card-element" class="form-control p-3 bg-light" style="min-height:44px;"></div>
                    <div id="card-errors" class="text-danger mt-2 small"></div>

                    <button type="button" class="btn btn-nmz w-100 mt-4" id="submit-payment" disabled>
                        <span class="spinner-border spinner-border-sm d-none me-1" id="pay-spinner"></span>
                        Pagar <?= number_format($total, 2) ?> €
                    </button>
                    <div id="checkout-errors" class="text-danger mt-2 small"></div>
                </div>
            </div>

            <!-- RESUMEN LATERAL DEL PEDIDO (primero en móvil para ver totales antes del formulario) -->
            <div class="col-lg-5 min-w-0 order-1 order-lg-2">
                <div class="card border-0 shadow-sm sticky-lg-top" style="top:1rem;">
                    <div class="card-body p-3 p-md-4">
                        <h2 class="h5 mb-3">Tu pedido</h2>
                        <ul class="list-unstyled mb-3 small">
                            <?php foreach ($items as $item):
                                $qty = (int) ($item['quantity'] ?? 1);
                                $price = (float) ($item['price'] ?? 0);
                            ?>
                            <li class="d-flex justify-content-between gap-2 py-2 border-bottom">
                                <span class="me-2 min-w-0 text-break">
                                    <?= esc($item['name'] ?? '') ?>
                                    <?php if (!empty($item['variant_name'])): ?>
                                    <small class="text-secondary d-block"><?= esc($item['variant_name']) ?></small>
                                    <?php endif; ?>
                                </span>
                                <span class="text-nowrap text-secondary"><?= $qty ?> × <?= number_format($price, 2, ',', '.') ?> €</span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Subtotal</span>
                            <span><?= number_format($subtotal, 2, ',', '.') ?> €</span>
                        </div>
                        <?php if ($discount > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Descuento<?= $coupon ? ' (' . esc($coupon['code']) . ')' : '' ?></span>
                            <span>-<?= number_format($discount, 2, ',', '.') ?> €</span>
                        </div>
                        <?php endif; ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Envío</span>
                            <span><?= $shipping <= 0 ? 'Gratis' : number_format($shipping, 2, ',', '.') . ' €' ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">IVA (21%)</span>
                            <span><?= number_format($tax, 2, ',', '.') ?> €</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-baseline">
                            <span class="fw-semibold">Total</span>
                            <span class="fs-5 fw-bold"><?= number_format($total, 2, ',', '.') ?> €</span>
                        </div>
                        <div class="text-center mt-3">
                            <a href="<?= base_url('carrito') ?>" class="small text-secondary">Volver al carrito</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script src="https://js.stripe.com/v3/"></script>
<script>
(function() {
    // CONFIGURACIÓN INICIAL DE ELEMENTOS DEL FORMULARIO Y STRIPE
    const stripeKey = '<?= esc($stripeKey, 'js') ?>';
    const cardEl = document.getElementById('card-element');
    const errEl = document.getElementById('card-errors');
    const btn = document.getElementById('submit-payment');
    const spinner = document.getElementById('pay-spinner');
    const checkoutErr = document.getElementById('checkout-errors');

    // MODO SIN CLAVE VÁLIDA DE STRIPE: MENSAJE INFORMATIVO Y ENVÍO SIN TARJETA
    if (!stripeKey || stripeKey.indexOf('pk_test_xxx') === 0) {
        cardEl.innerHTML = '<p class="text-muted small mb-0"><i class="bi bi-info-circle"></i> Pago con tarjeta se habilitará cuando se configuren las claves de Stripe. El pedido se creará en estado pendiente.</p>';
        btn.disabled = false;
        btn.addEventListener('click', () => submitOrder(null));
        return;
    }

    const stripe = Stripe(stripeKey);
    const elements = stripe.elements();
    const card = elements.create('card', {
        style: {
            base: { fontSize: '16px', color: '#1a1a1a', fontFamily: '"Inter", sans-serif', '::placeholder': { color: '#999' } },
            invalid: { color: '#dc3545' }
        }
    });
    card.mount('#card-element');

    let cardReady = false;
    card.on('change', function(e) {
        errEl.textContent = e.error ? e.error.message : '';
        cardReady = e.complete;
        btn.disabled = !cardReady;
    });

    btn.addEventListener('click', async () => {
        btn.disabled = true;
        spinner.classList.remove('d-none');
        checkoutErr.textContent = '';
        await submitOrder(stripe, card);
    });

    // ENVÍO AL SERVIDOR, CONFIRMACIÓN DE PAGO Y REDIRECCIÓN A ÉXITO
    async function submitOrder(stripeObj, cardObj) {
        const formData = {
            shipping_name: document.getElementById('shipping_name').value,
            shipping_address: document.getElementById('shipping_address').value,
            shipping_city: document.getElementById('shipping_city').value,
            shipping_postal_code: document.getElementById('shipping_postal_code').value,
            shipping_country: document.getElementById('shipping_country').value,
            shipping_phone: document.getElementById('shipping_phone').value,
        };

        const required = ['shipping_name','shipping_address','shipping_city','shipping_postal_code','shipping_country','shipping_phone'];
        for (const f of required) {
            if (!formData[f] || !formData[f].trim()) {
                checkoutErr.textContent = 'Por favor, completa todos los campos obligatorios.';
                btn.disabled = false;
                spinner.classList.add('d-none');
                return;
            }
        }

        const csrfName = document.querySelector('meta[name="csrf-token-name"]');
        const csrfHash = document.querySelector('meta[name="csrf-token-hash"]');
        if (csrfName && csrfHash) {
            formData[csrfName.content] = csrfHash.content;
        }

        try {
            const res = await fetch('<?= base_url('checkout/process') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(formData)
            });
            const data = await res.json();

            if (!data.success) {
                checkoutErr.textContent = data.message || 'Error al procesar el pedido.';
                btn.disabled = false;
                spinner.classList.add('d-none');
                return;
            }

            if (data.client_secret && stripeObj && cardObj) {
                const { error, paymentIntent } = await stripeObj.confirmCardPayment(data.client_secret, {
                    payment_method: { card: cardObj }
                });
                if (error) {
                    checkoutErr.textContent = error.message;
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                    return;
                }
            }

            window.location.href = '<?= base_url('checkout/success') ?>?order=' + encodeURIComponent(data.order_number);
        } catch (e) {
            checkoutErr.textContent = 'Error de conexión. Intenta de nuevo.';
            btn.disabled = false;
            spinner.classList.add('d-none');
        }
    }
})();
</script>
<?= $this->endSection() ?>