<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/BOOKINGS/SHOW.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php
$booking = is_array($booking ?? null) ? $booking : [];
$bid     = (int)($booking['id'] ?? 0);
$ref     = $booking['reference'] ?? $booking['booking_number'] ?? '#' . $bid;
$this->setVar('pageTitle', 'Reserva ' . $ref);

$logPower    = !empty($booking['logistics_power']) || !empty($booking['power_outlet']);
$logTable    = !empty($booking['logistics_table']) || !empty($booking['needs_table']);
$logShade    = !empty($booking['logistics_shade']) || !empty($booking['needs_shade']);
$logVehicle  = !empty($booking['logistics_vehicle']) || !empty($booking['vehicle_access']);
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <a href="<?= base_url('admin/bookings') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="admin-page-title mb-0">Reserva <?= esc($ref) ?></h1>
        <span class="badge-status badge-<?= esc(preg_replace('/\s+/', '_', strtolower((string)($booking['status'] ?? 'pending'))), 'attr') ?>">
            <?= esc(ucfirst(str_replace('_', ' ', (string)($booking['status'] ?? 'pending')))) ?>
        </span>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <h6>Datos de la reserva</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-sm-4 text-muted">Contacto</dt>
                    <dd class="col-sm-8"><?= esc($booking['contact_name'] ?? $booking['name'] ?? '—') ?></dd>

                    <dt class="col-sm-4 text-muted">Email</dt>
                    <dd class="col-sm-8"><a href="mailto:<?= esc($booking['email'] ?? '', 'attr') ?>"><?= esc($booking['email'] ?? '—') ?></a></dd>

                    <dt class="col-sm-4 text-muted">Teléfono</dt>
                    <dd class="col-sm-8"><?= esc($booking['phone'] ?? $booking['telephone'] ?? '—') ?></dd>

                    <dt class="col-sm-4 text-muted">Tipo de evento</dt>
                    <dd class="col-sm-8"><?= esc($booking['event_type'] ?? '—') ?></dd>

                    <dt class="col-sm-4 text-muted">Fecha</dt>
                    <dd class="col-sm-8">
                        <?php $ed = $booking['event_date'] ?? $booking['date'] ?? null; ?>
                        <?= $ed ? esc(date('d/m/Y H:i', strtotime((string)$ed))) : '—' ?>
                    </dd>

                    <dt class="col-sm-4 text-muted">Ubicación</dt>
                    <dd class="col-sm-8"><?= esc($booking['event_location'] ?? $booking['event_city'] ?? $booking['location'] ?? '—') ?></dd>

                    <dt class="col-sm-4 text-muted">Invitados</dt>
                    <dd class="col-sm-8"><?= esc((string)($booking['guests'] ?? $booking['guest_count'] ?? '—')) ?></dd>

                    <dt class="col-sm-4 text-muted">Mensaje</dt>
                    <dd class="col-sm-8">
                        <?php
                        $msg = (string)($booking['message'] ?? $booking['notes_public'] ?? '');
                        echo $msg !== '' ? nl2br(esc($msg)) : '—';
                        ?>
                    </dd>

                    <dt class="col-sm-4 text-muted">Estado</dt>
                    <dd class="col-sm-8">
                        <span class="badge-status badge-<?= esc(preg_replace('/\s+/', '_', strtolower((string)($booking['status'] ?? 'pending'))), 'attr') ?>">
                            <?= esc(ucfirst(str_replace('_', ' ', (string)($booking['status'] ?? 'pending')))) ?>
                        </span>
                    </dd>
                </dl>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h6>Logística en el lugar</h6>
            </div>
            <div class="card-body admin-form">
                <form method="post" action="<?= base_url('admin/bookings/' . $bid . '/logistics') ?>">
                    <?= csrf_field() ?>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="logistics[power]" value="1" id="log-power"
                                    <?= $logPower ? 'checked' : '' ?>>
                                <label class="form-check-label" for="log-power">Toma de corriente</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="logistics[table]" value="1" id="log-table"
                                    <?= $logTable ? 'checked' : '' ?>>
                                <label class="form-check-label" for="log-table">Mesa</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="logistics[shade]" value="1" id="log-shade"
                                    <?= $logShade ? 'checked' : '' ?>>
                                <label class="form-check-label" for="log-shade">Sombra</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="logistics[vehicle]" value="1" id="log-vehicle"
                                    <?= $logVehicle ? 'checked' : '' ?>>
                                <label class="form-check-label" for="log-vehicle">Acceso vehículo</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-admin mt-3">Guardar checklist</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <h6>Cambiar estado</h6>
            </div>
            <div class="card-body admin-form d-grid gap-2">
                <form method="post" action="<?= base_url('admin/bookings/' . $bid . '/status') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="pending">
                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Marcar pendiente</button>
                </form>
                <form method="post" action="<?= base_url('admin/bookings/' . $bid . '/status') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit" class="btn btn-sm btn-admin-outline w-100">Confirmar</button>
                </form>
                <form method="post" action="<?= base_url('admin/bookings/' . $bid . '/status') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="quote">
                    <button type="submit" class="btn btn-sm btn-outline-secondary w-100">Enviar presupuesto</button>
                </form>
                <form method="post" action="<?= base_url('admin/bookings/' . $bid . '/status') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="btn btn-sm btn-outline-success w-100">Completada</button>
                </form>
                <form method="post" action="<?= base_url('admin/bookings/' . $bid . '/status') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="cancelled">
                    <button type="submit" class="btn btn-sm btn-outline-danger w-100">Cancelar reserva</button>
                </form>
            </div>
        </div>

        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <h6>Presupuesto PDF</h6>
            </div>
            <div class="card-body">
                <form method="post" action="<?= base_url('admin/bookings/' . $bid . '/quote') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-admin w-100">
                        <i class="bi bi-file-earmark-pdf me-1"></i>Generar presupuesto PDF
                    </button>
                </form>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h6>Notas internas</h6>
            </div>
            <div class="card-body admin-form">
                <form method="post" action="<?= base_url('admin/bookings/' . $bid . '/notes') ?>">
                    <?= csrf_field() ?>
                    <label for="internal_notes" class="form-label visually-hidden">Notas</label>
                    <textarea class="form-control" id="internal_notes" name="notes" rows="6" placeholder="Notas solo para el equipo…"><?= esc($booking['internal_notes'] ?? $booking['admin_notes'] ?? '') ?></textarea>
                    <button type="submit" class="btn btn-sm btn-admin mt-2 w-100">Guardar notas</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>