<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/COUPONS/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Cupones') ?>

<?php $coupons = $coupons ?? []; ?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Cupones</h1>
    <a href="<?= base_url('admin/coupons/new') ?>" class="btn btn-admin">
        <i class="bi bi-plus-lg me-1"></i>Nuevo cupón
    </a>
</div>

<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Compra mínima</th>
                        <th>Usos</th>
                        <th>Válido hasta</th>
                        <th>Activo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($coupons)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No hay cupones</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($coupons as $c): ?>
                    <?php
                    $cid  = (int) ($c['id'] ?? 0);
                    $type = (string) ($c['type'] ?? '');
                    $val  = $c['value'] ?? '0';
                    ?>
                    <tr>
                        <td class="fw-medium font-monospace"><?= esc(strtoupper((string) ($c['code'] ?? ''))) ?></td>
                        <td><?= $type === 'percentage' ? 'Porcentaje' : ($type === 'fixed' ? 'Fijo' : esc($type)) ?></td>
                        <td><?= $type === 'percentage' ? esc($val) . ' %' : number_format((float) $val, 2, ',', '.') . ' €' ?></td>
                        <td><?= isset($c['min_purchase']) && $c['min_purchase'] !== '' && $c['min_purchase'] !== null
                            ? number_format((float) $c['min_purchase'], 2, ',', '.') . ' €'
                            : '—' ?></td>
                        <td class="small">
                            <?= (int) ($c['used_count'] ?? 0) ?>
                            /
                            <?= isset($c['max_uses']) && $c['max_uses'] !== null && $c['max_uses'] !== '' ? (int) $c['max_uses'] : '∞' ?>
                        </td>
                        <td class="text-muted small">
                            <?= ! empty($c['valid_until']) ? esc(date('d/m/Y', strtotime((string) $c['valid_until']))) : '—' ?>
                        </td>
                        <td>
                            <input type="checkbox" class="admin-toggle" role="switch" aria-label="Activo"
                                   data-url="<?= base_url('admin/coupons/' . $cid . '/toggle-active') ?>"
                                <?= ! empty($c['is_active']) ? 'checked' : '' ?>>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= base_url('admin/coupons/edit/' . $cid) ?>" class="btn btn-sm btn-admin-outline me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?= base_url('admin/coupons/delete/' . $cid) ?>" method="post" class="delete-form d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
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