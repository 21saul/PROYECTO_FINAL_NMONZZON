<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/TESTIMONIALS/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Testimonios') ?>

<?php $testimonials = $testimonials ?? []; ?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Testimonios</h1>
    <a href="<?= base_url('admin/testimonials/new') ?>" class="btn btn-admin">
        <i class="bi bi-plus-lg me-1"></i>Nuevo testimonio
    </a>
</div>

<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre cliente</th>
                        <th>Tipo servicio</th>
                        <th>Rating</th>
                        <th>Destacado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($testimonials)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No hay testimonios</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($testimonials as $t): ?>
                    <?php $tid = (int) ($t['id'] ?? 0); ?>
                    <tr>
                        <td class="thumb-cell">
                            <?php if (! empty($t['client_image'])): ?>
                            <img src="<?= esc(base_url($t['client_image']), 'attr') ?>" alt="">
                            <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width:48px;height:48px;">
                                <i class="bi bi-person text-muted"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-medium"><?= esc($t['client_name'] ?? '') ?></td>
                        <td><span class="badge bg-light text-dark"><?= esc($t['service_type'] ?? '—') ?></span></td>
                        <td>
                            <div class="star-rating" aria-label="Valoración <?= (int) ($t['rating'] ?? 0) ?> de 5">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                <i class="bi bi-star-fill<?= $s <= (int) ($t['rating'] ?? 0) ? ' filled' : '' ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td>
                            <input type="checkbox" class="admin-toggle" role="switch" aria-label="Destacado"
                                   data-url="<?= base_url('admin/testimonials/' . $tid . '/toggle-featured') ?>"
                                <?= ! empty($t['is_featured']) ? 'checked' : '' ?>>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= base_url('admin/testimonials/edit/' . $tid) ?>" class="btn btn-sm btn-admin-outline me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?= base_url('admin/testimonials/delete/' . $tid) ?>" method="post" class="delete-form d-inline">
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