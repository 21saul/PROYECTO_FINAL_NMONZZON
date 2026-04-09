<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/USERS/SHOW.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php
$user           = $user ?? [];
$orders         = $orders ?? $orderHistory ?? [];
$portraitOrders = $portraitOrders ?? [];
$uid            = (int) ($user['id'] ?? 0);
$isActive       = ! empty($user['is_active']);
$this->setVar('pageTitle', $user['name'] ?? 'Usuario');
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <a href="<?= base_url('admin/users') ?>" class="btn btn-admin-outline btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Volver
        </a>
        <h1 class="admin-page-title mb-0"><?= esc($user['name'] ?? 'Usuario') ?></h1>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="admin-card h-100">
            <div class="admin-card-header">
                <h6>Datos del usuario</h6>
            </div>
            <div class="card-body">
                <dl class="row small mb-0 g-2">
                    <dt class="col-12 col-sm-5 text-muted">Nombre</dt>
                    <dd class="col-12 col-sm-7"><?= esc($user['name'] ?? '') ?></dd>
                    <dt class="col-12 col-sm-5 text-muted">Email</dt>
                    <dd class="col-12 col-sm-7 text-break"><a href="mailto:<?= esc($user['email'] ?? '') ?>"><?= esc($user['email'] ?? '') ?></a></dd>
                    <dt class="col-12 col-sm-5 text-muted">Rol</dt>
                    <dd class="col-12 col-sm-7">
                        <?php $role = (string) ($user['role'] ?? ''); ?>
                        <span class="badge <?= $role === 'admin' ? 'bg-dark' : 'bg-secondary' ?>"><?= $role === 'admin' ? 'Admin' : 'Cliente' ?></span>
                    </dd>
                    <dt class="col-12 col-sm-5 text-muted">Registro</dt>
                    <dd class="col-12 col-sm-7 text-muted"><?= ! empty($user['created_at']) ? esc(date('d/m/Y H:i', strtotime((string) $user['created_at']))) : '—' ?></dd>
                    <dt class="col-12 col-sm-5 text-muted">Último login</dt>
                    <dd class="col-12 col-sm-7 text-muted"><?= ! empty($user['last_login_at']) ? esc(date('d/m/Y H:i', strtotime((string) $user['last_login_at']))) : '—' ?></dd>
                    <dt class="col-12 col-sm-5 text-muted">Estado</dt>
                    <dd class="col-12 col-sm-7"><?= $isActive ? '<span class="text-success">Activo</span>' : '<span class="text-danger">Inactivo</span>' ?></dd>
                </dl>
                <form action="<?= base_url('admin/users/' . $uid . '/toggle') ?>" method="post" class="mt-3">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm <?= $isActive ? 'btn-outline-danger' : 'btn-admin' ?> w-100">
                        <?= $isActive ? 'Desactivar cuenta' : 'Activar cuenta' ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="admin-card mb-3">
            <div class="admin-card-header">
                <h6>Pedidos recientes (tienda)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Sin pedidos</td></tr>
                            <?php else: ?>
                            <?php foreach (array_slice($orders, 0, 10) as $o): ?>
                            <tr>
                                <td class="fw-medium"><?= esc($o['order_number'] ?? '#' . ($o['id'] ?? '')) ?></td>
                                <td><span class="badge-status badge-<?= esc($o['status'] ?? 'pending') ?>"><?= esc(ucfirst((string) ($o['status'] ?? 'pending'))) ?></span></td>
                                <td><?= number_format((float) ($o['total'] ?? 0), 2, ',', '.') ?> €</td>
                                <td class="text-muted small"><?= ! empty($o['created_at']) ? esc(date('d/m/Y', strtotime((string) $o['created_at']))) : '—' ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h6>Pedidos de retratos recientes</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($portraitOrders)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Sin pedidos de retratos</td></tr>
                            <?php else: ?>
                            <?php foreach (array_slice($portraitOrders, 0, 10) as $po): ?>
                            <tr>
                                <td class="fw-medium"><?= esc($po['order_number'] ?? '#' . ($po['id'] ?? '')) ?></td>
                                <td><span class="badge-status badge-<?= esc($po['status'] ?? 'pending') ?>"><?= esc(ucfirst((string) ($po['status'] ?? 'pending'))) ?></span></td>
                                <td><?= number_format((float) ($po['total_price'] ?? 0), 2, ',', '.') ?> €</td>
                                <td class="text-muted small"><?= ! empty($po['created_at']) ? esc(date('d/m/Y', strtotime((string) $po['created_at']))) : '—' ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>