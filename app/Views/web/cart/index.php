<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/CART/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?php // VISTA DEL CARRITO DE COMPRA: LISTADO DE ARTÍCULOS (TABLA Y TARJETAS MÓVIL), CUPONES Y RESUMEN CON TOTALES ?>
<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Carrito') ?>

<?php
$items     = $items ?? [];
$totals    = $totals ?? [];
$subtotal  = (float) ($totals['subtotal'] ?? 0);
$tax       = (float) ($totals['tax'] ?? 0);
$shipping  = (float) ($totals['shipping'] ?? 0);
$discount  = (float) ($totals['discount'] ?? 0);
$total     = (float) ($totals['total'] ?? 0);
$coupon    = $coupon ?? null;
$itemCount = 0;
foreach ($items as $item) { $itemCount += (int) ($item['quantity'] ?? 1); }
?>

<?= $this->section('content') ?>

<section class="nmz-page-header py-4 border-bottom bg-white">
    <div class="container">
        <nav class="nmz-hero-crumbs nmz-hero-crumbs--on-light nmz-hero-crumbs--align-start" aria-label="Migas de pan">
            <ol class="nmz-hero-crumbs__list">
                <li class="nmz-hero-crumbs__item"><a href="<?= esc(base_url('/'), 'attr') ?>">Inicio</a></li>
                <li class="nmz-hero-crumbs__item"><span aria-current="page">Carrito</span></li>
            </ol>
        </nav>
        <h1 class="nmz-page-hero__title nmz-page-hero__title--on-light mt-2">Carrito</h1>
        <?php if ($itemCount > 0) : ?>
        <p class="text-secondary small text-uppercase mb-0 mt-2" style="letter-spacing: 0.12em;">
            <?= (int) $itemCount ?> <?= $itemCount === 1 ? 'artículo' : 'artículos' ?>
        </p>
        <?php endif; ?>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <?php if ($flash = session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc($flash) ?></div>
        <?php endif; ?>

        <?php if (empty($items)): ?>
        <!-- ESTADO VACÍO DEL CARRITO -->
        <div class="text-center py-5">
            <i class="bi bi-cart3 display-1 text-secondary mb-3 d-block"></i>
            <p class="text-secondary mb-4">Tu carrito está vacío</p>
            <a href="<?= base_url('productos') ?>" class="btn btn-nmz">Ver productos</a>
        </div>
        <?php else: ?>

        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <!-- LISTADO EN TABLA (ESCRITORIO) -->
                <div class="d-none d-md-block table-responsive border rounded-3 overflow-hidden bg-white shadow-sm">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th class="text-end pe-3">Subtotal</th>
                                <th class="text-end pe-3" style="width:3rem;"><span class="visually-hidden">Eliminar</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $key => $item):
                                $pname = $item['name'] ?? '';
                                $vname = trim((string) ($item['variant_name'] ?? ''));
                                $price = (float) ($item['price'] ?? 0);
                                $qty   = (int) ($item['quantity'] ?? 1);
                                $img   = $item['image'] ?? '';
                                $imgUrl = $img ? base_url($img) : base_url('assets/images/placeholder.webp');
                                $lineTotal = $price * $qty;
                            ?>
                            <tr data-key="<?= esc($key) ?>">
                                <td class="ps-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="flex-shrink-0 rounded-2 overflow-hidden bg-light" style="width:80px;height:80px;">
                                            <img src="<?= esc($imgUrl) ?>" alt="" class="w-100 h-100 object-fit-cover" loading="lazy">
                                        </div>
                                        <div>
                                            <div class="fw-semibold"><?= esc($pname) ?></div>
                                            <?php if ($vname): ?>
                                            <div class="small text-secondary"><?= esc($vname) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= number_format($price, 2, ',', '.') ?> €</td>
                                <td>
                                    <div class="d-inline-flex align-items-center gap-1 cart-qty-group" data-key="<?= esc($key) ?>">
                                        <button type="button" class="btn btn-sm btn-outline-secondary cart-qty-btn" data-action="decrease">−</button>
                                        <input type="number" class="form-control form-control-sm text-center cart-qty-input" style="width:4rem;" min="1" value="<?= $qty ?>">
                                        <button type="button" class="btn btn-sm btn-outline-secondary cart-qty-btn" data-action="increase">+</button>
                                    </div>
                                </td>
                                <td class="text-end pe-3 cart-line-total"><?= number_format($lineTotal, 2, ',', '.') ?> €</td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-link text-danger p-0 cart-remove-btn" data-key="<?= esc($key) ?>" aria-label="Eliminar">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- LISTADO EN TARJETAS (MÓVIL) -->
                <div class="d-md-none d-flex flex-column gap-3">
                    <?php foreach ($items as $key => $item):
                        $pname = $item['name'] ?? '';
                        $vname = trim((string) ($item['variant_name'] ?? ''));
                        $price = (float) ($item['price'] ?? 0);
                        $qty   = (int) ($item['quantity'] ?? 1);
                        $img   = $item['image'] ?? '';
                        $imgUrl = $img ? base_url($img) : base_url('assets/images/placeholder.webp');
                        $lineTotal = $price * $qty;
                    ?>
                    <div class="card border-0 shadow-sm" data-key="<?= esc($key) ?>">
                        <div class="card-body">
                            <div class="d-flex gap-3 mb-3">
                                <div class="flex-shrink-0 rounded-2 overflow-hidden bg-light" style="width:80px;height:80px;">
                                    <img src="<?= esc($imgUrl) ?>" alt="" class="w-100 h-100 object-fit-cover" loading="lazy">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fw-semibold"><?= esc($pname) ?></div>
                                    <?php if ($vname): ?>
                                    <div class="small text-secondary"><?= esc($vname) ?></div>
                                    <?php endif; ?>
                                    <div class="mt-1"><?= number_format($price, 2, ',', '.') ?> €</div>
                                </div>
                                <button type="button" class="btn btn-link text-danger p-0 align-self-start cart-remove-btn" data-key="<?= esc($key) ?>" aria-label="Eliminar">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-inline-flex align-items-center gap-1 cart-qty-group" data-key="<?= esc($key) ?>">
                                    <button type="button" class="btn btn-sm btn-outline-secondary cart-qty-btn" data-action="decrease">−</button>
                                    <input type="number" class="form-control form-control-sm text-center cart-qty-input" style="width:4rem;" min="1" value="<?= $qty ?>">
                                    <button type="button" class="btn btn-sm btn-outline-secondary cart-qty-btn" data-action="increase">+</button>
                                </div>
                                <span class="fw-semibold cart-line-total"><?= number_format($lineTotal, 2, ',', '.') ?> €</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- COLUMNA LATERAL: RESUMEN, CUPÓN Y BOTÓN AL CHECKOUT -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-lg-top" style="top:1rem;">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Resumen</h2>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Subtotal</span>
                            <span id="cart-subtotal"><?= number_format($subtotal, 2, ',', '.') ?> €</span>
                        </div>

                        <?php if ($coupon): ?>
                        <div class="d-flex justify-content-between mb-2 text-success" id="coupon-row">
                            <span>Descuento (<?= esc($coupon['code']) ?>)
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-1" id="remove-coupon-btn" aria-label="Eliminar cupón"><i class="bi bi-x-circle"></i></button>
                            </span>
                            <span>-<span id="cart-discount"><?= number_format($discount, 2, ',', '.') ?></span> €</span>
                        </div>
                        <?php else: ?>
                        <div id="coupon-row" class="d-none d-flex justify-content-between mb-2 text-success">
                            <span>Descuento (<span id="coupon-code-display"></span>)
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-1" id="remove-coupon-btn" aria-label="Eliminar cupón"><i class="bi bi-x-circle"></i></button>
                            </span>
                            <span>-<span id="cart-discount">0,00</span> €</span>
                        </div>
                        <?php endif; ?>

                        <div class="mb-3" id="coupon-form-wrap" <?= $coupon ? 'style="display:none"' : '' ?>>
                            <label for="coupon-code" class="form-label small text-secondary mb-1">Código promocional</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="coupon-code" placeholder="Código" autocomplete="off">
                                <button type="button" class="btn btn-outline-secondary" id="apply-coupon-btn">Aplicar</button>
                            </div>
                            <div id="coupon-msg" class="small mt-1"></div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Envío</span>
                            <span id="cart-shipping"><?= $shipping <= 0 ? 'Gratis' : number_format($shipping, 2, ',', '.') . ' €' ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-secondary">IVA (21%)</span>
                            <span id="cart-tax"><?= number_format($tax, 2, ',', '.') ?> €</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between align-items-baseline mb-4">
                            <span class="fw-semibold">Total</span>
                            <span class="fs-4 fw-bold" id="cart-total"><?= number_format($total, 2, ',', '.') ?> €</span>
                        </div>

                        <?php if ($subtotal < 50): ?>
                        <div class="alert alert-info small py-2 mb-3">
                            <i class="bi bi-truck"></i> Añade <?= number_format(50 - $subtotal, 2, ',', '.') ?> € más para envío gratis.
                        </div>
                        <?php endif; ?>

                        <a href="<?= base_url('checkout') ?>" class="btn btn-nmz w-100 mb-2">Proceder al pago</a>
                        <div class="text-center">
                            <a href="<?= base_url('productos') ?>" class="small text-secondary">Seguir comprando</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<?php // cart.js se carga en layouts/main.php ?>
<?= $this->endSection() ?>