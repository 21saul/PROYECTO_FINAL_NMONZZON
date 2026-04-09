<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/EVENTS/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Eventos') ?>

<?php
$events = $events ?? [];
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Eventos</h1>
    <a href="<?= base_url('admin/events/create') ?>" class="btn btn-admin">
        <i class="bi bi-plus-lg me-1"></i>Añadir evento
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
                        <th>Fecha</th>
                        <th>Ubicación</th>
                        <th>Destacado</th>
                        <th>Orden</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($events === []): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay eventos.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($events as $ev): ?>
                    <?php
                    $eid   = (int) ($ev['id'] ?? 0);
                    $thumb = (string) ($ev['featured_image'] ?? '');
                    $thumbSrc = $thumb !== '' && str_starts_with($thumb, 'http') ? $thumb : ($thumb !== '' ? base_url($thumb) : '');
                    $eventDate = $ev['event_date'] ?? '';
                    $dateOut = '';
                    if ($eventDate !== '') {
                        $ts = strtotime((string) $eventDate);
                        $dateOut = $ts !== false ? date('d/m/Y', $ts) : (string) $eventDate;
                    }
                    ?>
                    <tr>
                        <td class="thumb-cell">
                            <?php if ($thumbSrc !== ''): ?>
                            <img src="<?= esc($thumbSrc) ?>" alt="<?= esc($ev['title'] ?? '') ?>">
                            <?php else: ?>
                            <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-medium"><?= esc($ev['title'] ?? '') ?></td>
                        <td><?= $dateOut !== '' ? esc($dateOut) : '—' ?></td>
                        <td><?= esc($ev['location'] ?? '—') ?></td>
                        <td>
                            <?php if ($eid > 0): ?>
                            <input type="checkbox" class="admin-toggle" data-url="<?= base_url('admin/events/' . $eid . '/toggle') ?>" <?= ! empty($ev['is_featured']) ? 'checked' : '' ?> aria-label="Destacado">
                            <?php else: ?>
                            —
                            <?php endif; ?>
                        </td>
                        <td><?= esc((string) ($ev['sort_order'] ?? 0)) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= base_url('admin/events/edit/' . $eid) ?>" class="btn btn-sm btn-outline-secondary me-1" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if ($eid > 0): ?>
                            <form action="<?= base_url('admin/events/delete/' . $eid) ?>" method="post" class="delete-form d-inline">
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