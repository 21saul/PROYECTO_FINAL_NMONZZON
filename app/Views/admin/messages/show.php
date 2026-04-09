<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/MESSAGES/SHOW.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Mensaje') ?>

<?php $message = $message ?? []; ?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <div>
        <a href="<?= base_url('admin/messages') ?>" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Volver a mensajes</a>
        <h1 class="admin-page-title mt-1"><?= esc($message['subject'] ?? 'Sin asunto') ?></h1>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Nombre</small>
                        <span class="fw-medium"><?= esc($message['name'] ?? '') ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Email</small>
                        <a href="mailto:<?= esc($message['email'] ?? '') ?>"><?= esc($message['email'] ?? '') ?></a>
                    </div>
                    <?php if (!empty($message['phone'])): ?>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Teléfono</small>
                        <span><?= esc($message['phone']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Categoría</small>
                        <span class="badge bg-light text-dark"><?= esc($message['category'] ?? $message['service_type'] ?? '—') ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Fecha</small>
                        <span><?= date('d/m/Y H:i', strtotime($message['created_at'] ?? 'now')) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Estado</small>
                        <?php if (!empty($message['is_read']) && $message['is_read']): ?>
                        <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Leído</span>
                        <?php else: ?>
                        <span class="text-warning"><i class="bi bi-circle-fill me-1" style="font-size:0.6rem;"></i>No leído</span>
                        <?php endif; ?>
                    </div>
                </div>

                <h6 class="mb-2">Mensaje</h6>
                <div class="bg-light p-3 rounded" style="white-space:pre-wrap;"><?= esc($message['message'] ?? '') ?></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card mb-3">
            <div class="card-body d-flex flex-column gap-2">
                <?php if (empty($message['is_read']) || !$message['is_read']): ?>
                <button type="button" class="btn btn-admin w-100 mark-read-btn" data-url="<?= base_url('admin/messages/' . ($message['id'] ?? 0) . '/read') ?>">
                    <i class="bi bi-check2 me-1"></i>Marcar como leído
                </button>
                <?php endif; ?>

                <a href="mailto:<?= esc($message['email'] ?? '') ?>?subject=Re: <?= esc($message['subject'] ?? '') ?>" class="btn btn-admin-outline w-100">
                    <i class="bi bi-reply me-1"></i>Responder por email
                </a>

                <form action="<?= base_url('admin/messages/delete/' . ($message['id'] ?? 0)) ?>" method="post" class="delete-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>