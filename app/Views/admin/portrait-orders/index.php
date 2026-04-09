<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/PORTRAIT-ORDERS/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Pedidos de Retratos') ?>

<?php
$orders  = $orders ?? [];
$styles  = $styles ?? [];
$filters = $filters ?? [];

$statusLabels = [
    'quote'          => 'Presupuesto',
    'accepted'       => 'Aceptado',
    'photo_received' => 'Foto recibida',
    'in_progress'    => 'En proceso',
    'revision'       => 'Revisión',
    'delivered'      => 'Entregado',
    'completed'      => 'Completado',
    'cancelled'      => 'Cancelado',
];

$req = service('request');

$filterStatus = $filters['status'] ?? $req->getGet('status');
$filterFrom   = $filters['date_from'] ?? $req->getGet('date_from');
$filterTo     = $filters['date_to'] ?? $req->getGet('date_to');
$filterStyle  = $req->getGet('portrait_style_id');
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Pedidos de Retratos</h1>
</div>

<form class="admin-filters" method="get" action="<?= base_url('admin/portrait-orders') ?>">
    <select name="status" class="form-select" aria-label="Estado" onchange="this.form.submit()">
        <option value="">Todos los estados</option>
        <?php foreach ($statusLabels as $key => $label): ?>
            <option value="<?= esc($key, 'attr') ?>"<?= (string) $filterStatus === $key ? ' selected' : '' ?>>
                <?= esc($label) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <select name="portrait_style_id" class="form-select" aria-label="Estilo" onchange="this.form.submit()">
        <option value="">Todos los estilos</option>
        <?php foreach ($styles as $st): ?>
            <?php
            $sid = (string) ($st['id'] ?? '');
            $sname = (string) ($st['name'] ?? $sid);
            if ($sid === '') {
                continue;
            }
            ?>
            <option value="<?= esc($sid, 'attr') ?>"<?= (string) $filterStyle === $sid ? ' selected' : '' ?>>
                <?= esc($sname) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <input type="date" name="date_from" class="form-control" value="<?= esc($filterFrom ?? '', 'attr') ?>" aria-label="Desde" title="Desde">
    <input type="date" name="date_to" class="form-control" value="<?= esc($filterTo ?? '', 'attr') ?>" aria-label="Hasta" title="Hasta">
    <button type="submit" class="btn btn-sm btn-admin">Filtrar</button>
    <?php if ($filterStatus !== null && $filterStatus !== '' || $filterFrom || $filterTo || $filterStyle): ?>
        <a href="<?= base_url('admin/portrait-orders') ?>" class="btn btn-sm btn-outline-secondary">Limpiar</a>
    <?php endif; ?>
</form>

<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th># Pedido</th>
                        <th>Cliente</th>
                        <th>Estilo</th>
                        <th>Tamaño</th>
                        <th>Figuras</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders === []): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">No hay pedidos.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <?php
                            $oid    = (int) ($order['id'] ?? 0);
                            $st     = (string) ($order['status'] ?? 'quote');
                            $badge  = 'badge-' . preg_replace('/[^a-z0-9_]/', '', $st);
                            $stLab  = $statusLabels[$st] ?? $st;
                            ?>
                            <tr>
                                <td class="fw-medium"><?= esc($order['order_number'] ?? '#' . $oid) ?></td>
                                <td><?= esc($order['user_name'] ?? $order['customer_name'] ?? '—') ?></td>
                                <td><?= esc($order['style_name'] ?? '—') ?></td>
                                <td><?= esc($order['size_name'] ?? '—') ?></td>
                                <td><?= esc((string) ($order['num_figures'] ?? '—')) ?></td>
                                <td>
                                    <span class="badge-status <?= esc($badge, 'attr') ?>"><?= esc($stLab) ?></span>
                                </td>
                                <td><?= number_format((float) ($order['total_price'] ?? 0), 2, ',', '.') ?> €</td>
                                <td class="text-muted small">
                                    <?= ! empty($order['created_at']) ? esc(date('d/m/Y', strtotime((string) $order['created_at']))) : '—' ?>
                                </td>
                                <td class="text-end">
                                    <a href="<?= base_url('admin/portrait-orders/' . $oid) ?>" class="btn btn-sm btn-admin-outline">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>