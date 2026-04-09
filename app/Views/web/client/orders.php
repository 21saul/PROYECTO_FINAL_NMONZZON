<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/CLIENT/ORDERS.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Mis Pedidos') ?>

<?php
$orders = $orders ?? [];

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

$fmtDate = static function ($dt): string {
    if ($dt === null || $dt === '') {
        return '—';
    }
    $t = strtotime((string) $dt);

    return $t ? date('d/m/Y H:i', $t) : '—';
};
?>

<?= $this->section('content') ?>

<section
    class="page-hero"
    style="background-image: url('<?= esc(nmz_mi_cuenta_hero_bg_url(), 'attr') ?>');"
>
    <div class="page-hero-overlay"></div>
    <div class="container page-hero-content py-4 text-center">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Mi cuenta', 'url' => base_url('mi-cuenta')],
                ['label' => 'Mis pedidos', 'url' => null],
            ],
            'nmzHeroTitle' => 'Mis pedidos',
        ]) ?>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <?php if ($orders === []) : ?>
        <div class="dashboard-card text-center py-5">
            <p class="text-secondary mb-4">Aún no tienes pedidos en la tienda.</p>
            <a href="<?= esc(base_url('productos')) ?>" class="btn btn-nmz">Ver productos</a>
        </div>
        <?php else : ?>
        <div class="dashboard-card">
            <div class="table-responsive">
                <table class="table table-dashboard mb-0">
                    <thead>
                        <tr>
                            <th>Nº pedido</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order) : ?>
                        <tr>
                            <td><?= esc($order['order_number'] ?? '') ?></td>
                            <td><?= esc($fmtDate($order['created_at'] ?? null)) ?></td>
                            <td><?= esc(number_format((float) ($order['total'] ?? 0), 2, ',', ' ')) ?> €</td>
                            <td>
                                <span class="<?= esc($orderStatusBadge($order['status'] ?? null), 'attr') ?>">
                                    <?= esc($order['status'] ?? '') ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= esc(base_url('mi-cuenta/pedidos/' . (int) ($order['id'] ?? 0))) ?>">Ver detalle</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>