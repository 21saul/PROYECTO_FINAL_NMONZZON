<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/MESSAGES/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Mensajes de contacto') ?>

<?php
$messages = $messages ?? [];
$filters  = $filters ?? ['is_read' => null, 'category' => null];
$catLabels = [
    'general'   => 'General',
    'portrait'  => 'Retrato',
    'live_art'  => 'Arte en vivo',
    'branding'  => 'Branding',
    'design'    => 'Diseño',
    'products'  => 'Productos',
    'other'     => 'Otro',
];
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Mensajes de contacto</h1>
</div>

<div class="admin-filters">
    <form method="get" action="<?= base_url('admin/messages') ?>" class="d-flex flex-wrap align-items-center gap-2">
        <select class="form-select" name="is_read" aria-label="Estado lectura" onchange="this.form.submit()">
            <option value="" <?= ($filters['is_read'] ?? '') === null || ($filters['is_read'] ?? '') === '' ? 'selected' : '' ?>>Todos</option>
            <option value="0" <?= ($filters['is_read'] ?? '') === '0' ? 'selected' : '' ?>>No leídos</option>
            <option value="1" <?= ($filters['is_read'] ?? '') === '1' ? 'selected' : '' ?>>Leídos</option>
        </select>
        <select class="form-select" name="category" aria-label="Categoría" onchange="this.form.submit()">
            <option value="">Todas las categorías</option>
            <?php foreach ($catLabels as $key => $lbl): ?>
            <option value="<?= esc($key) ?>" <?= (string) ($filters['category'] ?? '') === $key ? 'selected' : '' ?>><?= esc($lbl) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width:2rem;"></th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Categoría</th>
                        <th>Asunto</th>
                        <th>Fecha</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay mensajes</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                    <?php
                    $mid   = (int) ($msg['id'] ?? 0);
                    $isRead = ! empty($msg['is_read']);
                    ?>
                    <tr class="<?= $isRead ? '' : 'fw-semibold table-light' ?>">
                        <td>
                            <span class="read-indicator bi bi-circle-fill <?= $isRead ? 'text-success' : 'text-secondary' ?>" title="<?= $isRead ? 'Leído' : 'No leído' ?>"></span>
                        </td>
                        <td><?= esc($msg['name'] ?? '') ?></td>
                        <td class="small"><a href="mailto:<?= esc($msg['email'] ?? '') ?>"><?= esc($msg['email'] ?? '') ?></a></td>
                        <td><span class="badge bg-light text-dark"><?= esc($catLabels[$msg['category'] ?? ''] ?? ($msg['category'] ?? '—')) ?></span></td>
                        <td class="small"><?= esc($msg['subject'] ?? '') ?></td>
                        <td class="text-muted small"><?= ! empty($msg['created_at']) ? esc(date('d/m/Y H:i', strtotime((string) $msg['created_at']))) : '—' ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= base_url('admin/messages/' . $mid) ?>" class="btn btn-sm btn-admin-outline me-1">Ver</a>
                            <?php if (! $isRead): ?>
                            <button type="button" class="btn btn-sm btn-admin mark-read-btn" data-url="<?= base_url('admin/messages/' . $mid . '/read') ?>">Marcar leído</button>
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