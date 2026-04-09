<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/DESIGN/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Proyectos de Diseño') ?>

<?php
$projects = $projects ?? [];
$designTypeLabels = [
    'identidad'     => 'Identidad',
    'packaging'     => 'Packaging',
    'editorial'     => 'Editorial',
    'web'           => 'Web',
    'ilustración'   => 'Ilustración',
    'ilustracion'   => 'Ilustración',
];
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Proyectos de Diseño</h1>
    <a href="<?= base_url('admin/design/create') ?>" class="btn btn-admin">
        <i class="bi bi-plus-lg me-1"></i>Añadir proyecto
    </a>
</div>

<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Miniatura</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Destacado</th>
                        <th>Orden</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($projects === []): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay proyectos de diseño.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($projects as $p): ?>
                    <?php
                    $pid   = (int) ($p['id'] ?? 0);
                    $thumb = (string) ($p['featured_image'] ?? '');
                    $thumbSrc = $thumb !== '' && str_starts_with($thumb, 'http') ? $thumb : ($thumb !== '' ? base_url($thumb) : '');
                    $dtype = (string) ($p['design_type'] ?? '');
                    $typeLabel = $dtype !== '' ? ($designTypeLabels[$dtype] ?? $dtype) : '';
                    ?>
                    <tr>
                        <td class="thumb-cell">
                            <?php if ($thumbSrc !== ''): ?>
                            <img src="<?= esc($thumbSrc) ?>" alt="<?= esc($p['title'] ?? '') ?>">
                            <?php else: ?>
                            <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-medium"><?= esc($p['title'] ?? '') ?></td>
                        <td><?= $typeLabel !== '' ? esc($typeLabel) : '—' ?></td>
                        <td>
                            <?php if ($pid > 0): ?>
                            <input type="checkbox" class="admin-toggle" data-url="<?= base_url('admin/design/' . $pid . '/toggle') ?>" <?= ! empty($p['is_featured']) ? 'checked' : '' ?> aria-label="Destacado">
                            <?php else: ?>
                            —
                            <?php endif; ?>
                        </td>
                        <td><?= esc((string) ($p['sort_order'] ?? 0)) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= base_url('admin/design/edit/' . $pid) ?>" class="btn btn-sm btn-outline-secondary me-1" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if ($pid > 0): ?>
                            <form action="<?= base_url('admin/design/delete/' . $pid) ?>" method="post" class="delete-form d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
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