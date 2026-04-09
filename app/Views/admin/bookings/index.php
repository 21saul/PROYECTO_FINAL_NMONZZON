<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/BOOKINGS/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Reservas Arte en Vivo') ?>

<?php
$bookings      = $bookings ?? [];
$statusFilter  = (string)(service('request')->getGet('status') ?? '');
$statusOptions = [
    ''            => 'Todos los estados',
    'pending'     => 'Pendiente',
    'confirmed'   => 'Confirmada',
    'quote'       => 'Presupuesto',
    'cancelled'   => 'Cancelada',
    'completed'   => 'Completada',
];
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Reservas Arte en Vivo</h1>
    <a href="<?= base_url('admin/bookings/calendar') ?>" class="btn btn-admin-outline">
        <i class="bi bi-calendar3 me-1"></i>Vista calendario
    </a>
</div>

<div class="admin-filters">
    <form method="get" action="<?= base_url('admin/bookings') ?>" class="d-flex align-items-center gap-2 flex-wrap">
        <label for="filter-status" class="small text-muted mb-0">Estado</label>
        <select class="form-select" id="filter-status" name="status" onchange="this.form.submit()">
            <?php foreach ($statusOptions as $val => $label): ?>
            <option value="<?= esc($val, 'attr') ?>" <?= ($statusFilter === $val) ? 'selected' : '' ?>>
                <?= esc($label) ?>
            </option>
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
                        <th># Reserva</th>
                        <th>Contacto</th>
                        <th>Tipo evento</th>
                        <th>Fecha</th>
                        <th>Ubicación</th>
                        <th>Invitados</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bookings)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">No hay reservas</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($bookings as $booking): ?>
                    <?php
                        $bid = (int)($booking['id'] ?? 0);
                        $ref = $booking['reference'] ?? $booking['booking_number'] ?? '#' . $bid;
                        $cName  = trim((string)($booking['contact_name'] ?? $booking['name'] ?? ''));
                        $cEmail = trim((string)($booking['email'] ?? ''));
                        $contact = $cName !== '' && $cEmail !== '' ? $cName . ' · ' . $cEmail : ($cName !== '' ? $cName : ($cEmail !== '' ? $cEmail : '—'));
                        $eventDate = $booking['event_date'] ?? $booking['date'] ?? null;
                        $location  = $booking['event_location'] ?? $booking['event_city'] ?? $booking['location'] ?? '—';
                        $guests    = $booking['guests'] ?? $booking['guest_count'] ?? '—';
                        $status    = $booking['status'] ?? 'pending';
                    ?>
                    <tr>
                        <td class="fw-medium"><?= esc($ref) ?></td>
                        <td><?= esc($contact) ?></td>
                        <td><?= esc($booking['event_type'] ?? '—') ?></td>
                        <td class="text-muted small">
                            <?= $eventDate ? esc(date('d/m/Y', strtotime((string)$eventDate))) : '—' ?>
                        </td>
                        <td><?= esc($location) ?></td>
                        <td><?= esc((string)$guests) ?></td>
                        <td>
                            <span class="badge-status badge-<?= esc(preg_replace('/\s+/', '_', strtolower((string)$status)), 'attr') ?>"><?= esc(ucfirst(str_replace('_', ' ', (string)$status))) ?></span>
                        </td>
                        <td class="text-end">
                            <a href="<?= base_url('admin/bookings/' . $bid) ?>" class="btn btn-sm btn-admin-outline">
                                <i class="bi bi-eye me-1"></i>Ver
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