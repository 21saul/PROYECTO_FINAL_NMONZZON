<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/CLIENT/ORDER-DETAIL.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php
$order = $order ?? [];
$items = $items ?? [];
$on    = (string) ($order['order_number'] ?? '');
?>

<?php $this->setVar('pageTitle', 'Pedido #' . $on) ?>

<?php
$orderStatusBadge = static function (?string $status): string {
    $s = strtolower((string) $status);
    $map = [
        'pending'    => 'badge-pending',
        'processing' => 'badge-in-progress',
        'shipped'    => 'badge-accepted',
        'delivered'  => 'badge-delivered',
        'cancelled'  => 'badge-cancelled',
        'refunded'   => 'badge-cancelled',
    ];
    $cls = $map[$s] ?? 'badge-pending';

    return 'badge-status ' . $cls;
};

$paymentBadge = static function (?string $ps): string {
    $s = strtolower((string) $ps);
    if ($s === 'paid' || $s === 'pagado') {
        return 'badge-status badge-completed';
    }
    if ($s === 'failed' || $s === 'fallido') {
        return 'badge-status badge-cancelled';
    }

    return 'badge-status badge-pending';
};

$fmtDate = static function ($dt): string {
    if ($dt === null || $dt === '') {
        return '—';
    }
    $t = strtotime((string) $dt);

    return $t ? date('d/m/Y H:i', $t) : '—';
};

$money = static function ($v): string {
    return number_format((float) $v, 2, ',', ' ') . ' €';
};

$showInvoice = ! empty($order['invoice_path'])
    || in_array(strtolower((string) ($order['payment_status'] ?? '')), ['paid', 'pagado'], true);
$orderId = (int) ($order['id'] ?? 0);
?>

<?= $this->section('content') ?>

<section class="nmz-page-header py-4 border-bottom bg-white">
    <div class="container">
        <nav class="nmz-hero-crumbs nmz-hero-crumbs--on-light nmz-hero-crumbs--align-start" aria-label="Migas de pan">
            <ol class="nmz-hero-crumbs__list">
                <li class="nmz-hero-crumbs__item"><a href="<?= esc(base_url('/'), 'attr') ?>">Inicio</a></li>
                <li class="nmz-hero-crumbs__item"><a href="<?= esc(base_url('mi-cuenta'), 'attr') ?>">Mi cuenta</a></li>
                <li class="nmz-hero-crumbs__item"><a href="<?= esc(base_url('mi-cuenta/pedidos'), 'attr') ?>">Mis pedidos</a></li>
                <li class="nmz-hero-crumbs__item"><span aria-current="page">Pedido <?= esc($on) ?></span></li>
            </ol>
        </nav>
        <h1 class="nmz-page-hero__title nmz-page-hero__title--on-light mt-2">Pedido <?= esc($on) ?></h1>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="dashboard-card mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <p class="small text-uppercase text-secondary mb-1">Nº pedido</p>
                    <p class="mb-0 fw-semibold"><?= esc($on) ?></p>
                </div>
                <div class="col-md-4">
                    <p class="small text-uppercase text-secondary mb-1">Fecha</p>
                    <p class="mb-0"><?= esc($fmtDate($order['created_at'] ?? null)) ?></p>
                </div>
                <div class="col-md-4">
                    <p class="small text-uppercase text-secondary mb-1">Estado</p>
                    <p class="mb-0">
                        <span class="<?= esc($orderStatusBadge($order['status'] ?? null), 'attr') ?> me-2">
                            <?= esc($order['status'] ?? '') ?>
                        </span>
                        <span class="<?= esc($paymentBadge($order['payment_status'] ?? null), 'attr') ?>">
                            <?= esc($order['payment_status'] ?? '') ?>
                        </span>
                    </p>
                </div>
            </div>
            <?php if ($showInvoice) : ?>
            <div class="mt-4 pt-3 border-top">
                <a href="<?= esc(base_url('mi-cuenta/pedidos/' . $orderId . '/invoice')) ?>" class="btn btn-nmz-outline btn-sm">
                    Descargar factura
                </a>
            </div>
            <?php endif; ?>
        </div>

        <div class="dashboard-card mb-4">
            <h4 class="mb-3">Productos</h4>
            <div class="table-responsive">
                <table class="table table-dashboard mb-0">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Producto</th>
                            <th>Variante</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-end">Precio unit.</th>
                            <th class="text-end">Total línea</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item) : ?>
                        <?php
                        $thumb = $item['image_url'] ?? $item['product_image'] ?? $item['thumbnail'] ?? null;
                        $variant = $item['variant_name'] ?? $item['variant_label'] ?? $item['variant'] ?? '';
                        $variant = $variant !== '' ? (string) $variant : '—';
                        ?>
                        <tr>
                            <td style="width: 72px;">
                                <?php if ($thumb) : ?>
                                <img
                                    src="<?= esc(base_url(ltrim((string) $thumb, '/')), 'attr') ?>"
                                    alt=""
                                    class="rounded"
                                    width="56"
                                    height="56"
                                    style="object-fit: cover;"
                                >
                                <?php else : ?>
                                <span class="d-inline-flex align-items-center justify-content-center bg-light rounded" style="width:56px;height:56px;">
                                    <i class="bi bi-image text-secondary" aria-hidden="true"></i>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($item['product_name'] ?? '') ?></td>
                            <td><?= esc($variant) ?></td>
                            <td class="text-center"><?= esc((string) (int) ($item['quantity'] ?? 0)) ?></td>
                            <td class="text-end"><?= esc($money($item['unit_price'] ?? 0)) ?></td>
                            <td class="text-end"><?= esc($money($item['total_price'] ?? 0)) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="dashboard-card h-100">
                    <h4 class="mb-3">Resumen</h4>
                    <div class="d-flex justify-content-between py-2 border-bottom border-light">
                        <span>Subtotal</span>
                        <span><?= esc($money($order['subtotal'] ?? 0)) ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom border-light">
                        <span>Descuento</span>
                        <span><?= esc($money($order['discount'] ?? 0)) ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom border-light">
                        <span>Impuestos</span>
                        <span><?= esc($money($order['tax'] ?? 0)) ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom border-light">
                        <span>Envío</span>
                        <span><?= esc($money($order['shipping_cost'] ?? 0)) ?></span>
                    </div>
                    <div class="d-flex justify-content-between pt-3 fw-semibold">
                        <span>Total</span>
                        <span><?= esc($money($order['total'] ?? 0)) ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="dashboard-card h-100">
                    <h4 class="mb-3">Envío</h4>
                    <p class="mb-1"><?= esc($order['shipping_name'] ?? '') ?></p>
                    <p class="mb-1 text-secondary"><?= esc($order['shipping_address'] ?? '') ?></p>
                    <p class="mb-0">
                        <?= esc($order['shipping_postal_code'] ?? '') ?>
                        <?= esc($order['shipping_city'] ?? '') ?>
                    </p>
                    <?php if (! empty($order['shipping_country'])) : ?>
                    <p class="mb-0 mt-2 text-secondary"><?= esc($order['shipping_country']) ?></p>
                    <?php endif; ?>
                    <?php if (! empty($order['shipping_phone'])) : ?>
                    <p class="mb-0 mt-2"><i class="bi bi-telephone me-1" aria-hidden="true"></i><?= esc($order['shipping_phone']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>