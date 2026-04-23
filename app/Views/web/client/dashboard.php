<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/CLIENT/DASHBOARD.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Mi Cuenta') ?>

<?php
$user            = $user ?? [];
$orderCount      = (int) ($orderCount ?? 0);
$portraitCount   = (int) ($portraitCount ?? 0);
$recentOrders    = $recentOrders ?? [];
$recentPortraits = $recentPortraits ?? [];

$welcomeName = esc($user['name'] ?? session('name') ?? '');

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

$portraitStatusBadge = static function (?string $status): string {
    $s = strtolower((string) $status);
    $map = [
        'quote'           => 'badge-quote',
        'accepted'        => 'badge-accepted',
        'photo_received'  => 'badge-in-progress',
        'in_progress'     => 'badge-in-progress',
        'revision'        => 'badge-revision',
        'delivered'       => 'badge-delivered',
        'completed'       => 'badge-completed',
        'cancelled'       => 'badge-cancelled',
    ];
    $cls = $map[$s] ?? 'badge-quote';

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

<section class="nmz-page-header py-4 border-bottom bg-white">
    <div class="container">
        <nav class="nmz-hero-crumbs nmz-hero-crumbs--on-light nmz-hero-crumbs--align-start" aria-label="Migas de pan">
            <ol class="nmz-hero-crumbs__list">
                <li class="nmz-hero-crumbs__item"><a href="<?= esc(base_url('/'), 'attr') ?>">Inicio</a></li>
                <li class="nmz-hero-crumbs__item"><span aria-current="page">Mi cuenta</span></li>
            </ol>
        </nav>
        <h1 class="nmz-page-hero__title nmz-page-hero__title--on-light mt-2">Mi cuenta</h1>
        <?php if ($welcomeName !== '') : ?>
        <p class="lead mb-0 mt-2">Bienvenida, <?= $welcomeName ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-12 col-md-4">
                <div class="dashboard-stat rounded-0 shadow-sm">
                    <h3><?= $orderCount ?></h3>
                    <p>Pedidos</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="dashboard-stat rounded-0 shadow-sm">
                    <h3><?= $portraitCount ?></h3>
                    <p>Retratos</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <a href="<?= esc(base_url('mi-cuenta/perfil')) ?>" class="text-decoration-none text-reset d-block h-100">
                    <div class="dashboard-stat rounded-0 shadow-sm h-100 d-flex flex-column justify-content-center">
                        <h3><i class="bi bi-person-circle" aria-hidden="true"></i></h3>
                        <p>Perfil</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-3 mb-5">
            <a href="<?= esc(base_url('mi-cuenta/pedidos')) ?>" class="btn btn-nmz">Mis Pedidos</a>
            <a href="<?= esc(base_url('mi-cuenta/retratos')) ?>" class="btn btn-nmz-outline">Mis Retratos</a>
            <a href="<?= esc(base_url('mi-cuenta/perfil')) ?>" class="btn btn-nmz-outline">Editar Perfil</a>
        </div>

        <?php if ($recentOrders !== []) : ?>
        <div class="dashboard-card mb-5">
            <h4>Pedidos recientes</h4>
            <div class="table-responsive">
                <table class="table table-dashboard mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Nº pedido</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order) : ?>
                        <tr>
                            <td><?= esc($fmtDate($order['created_at'] ?? null)) ?></td>
                            <td><?= esc($order['order_number'] ?? '') ?></td>
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

        <?php if ($recentPortraits !== []) : ?>
        <div class="dashboard-card">
            <h4>Retratos recientes</h4>
            <div class="table-responsive">
                <table class="table table-dashboard mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Estilo</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentPortraits as $p) : ?>
                        <?php
                        $styleInfo = $p['style_name'] ?? null;
                        if ($styleInfo === null || $styleInfo === '') {
                            $sid = $p['portrait_style_id'] ?? null;
                            $styleInfo = $sid !== null ? 'Estilo #' . $sid : '—';
                        }
                        ?>
                        <tr>
                            <td><?= esc($fmtDate($p['created_at'] ?? null)) ?></td>
                            <td><?= esc((string) $styleInfo) ?></td>
                            <td>
                                <span class="<?= esc($portraitStatusBadge($p['status'] ?? null), 'attr') ?>">
                                    <?= esc($p['status'] ?? '') ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= esc(base_url('mi-cuenta/retratos/' . (int) ($p['id'] ?? 0))) ?>">Ver detalle</a>
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