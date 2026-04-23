<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/PORTRAIT-ORDERS/SHOW.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php
$order      = $order ?? [];
$history    = $history ?? [];

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

$flowStates = ['quote', 'accepted', 'photo_received', 'in_progress', 'revision', 'delivered', 'completed'];

$current   = (string) ($order['status'] ?? 'quote');
$cancelled = $current === 'cancelled';

$idx = $cancelled ? -1 : array_search($current, $flowStates, true);
if ($idx === false) {
    $idx = -1;
}

$nextStates = $nextStates ?? null;

if ($nextStates === null) {
    $allowed = [
        'quote'          => ['accepted'],
        'accepted'       => ['photo_received'],
        'photo_received' => ['in_progress'],
        'in_progress'    => ['revision'],
        'revision'       => ['in_progress', 'delivered'],
        'delivered'      => ['completed'],
    ];
    $nextStates = $allowed[$current] ?? [];
    if (! in_array($current, ['completed', 'cancelled'], true)) {
        $nextStates[] = 'cancelled';
    }
}

$nextStates = is_array($nextStates) ? $nextStates : [];

$oid = (int) ($order['id'] ?? 0);
$this->setVar('pageTitle', 'Pedido ' . ($order['order_number'] ?? '#' . $oid));

$refPhoto = (string) ($order['reference_photo'] ?? '');
$refUrl   = $refPhoto !== '' ? base_url($refPhoto) : '';
$sketch   = (string) ($order['sketch_image'] ?? '');
$finalImg = (string) ($order['final_image'] ?? '');
?>

<?= $this->section('extra_css') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <div class="d-flex align-items-center gap-3 flex-wrap">
        <a href="<?= base_url('admin/portrait-orders') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        <h1 class="admin-page-title mb-0">Pedido <?= esc($order['order_number'] ?? '#' . $oid) ?></h1>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="admin-card mb-3">
            <div class="admin-card-header">
                <h6>Datos del pedido</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-sm-4 text-muted">Cliente</dt>
                    <dd class="col-sm-8"><?= esc($order['user_name'] ?? '—') ?></dd>
                    <dt class="col-sm-4 text-muted">Email</dt>
                    <dd class="col-sm-8"><?= esc($order['user_email'] ?? '—') ?></dd>
                    <dt class="col-sm-4 text-muted">Estilo</dt>
                    <dd class="col-sm-8"><?= esc($order['style_name'] ?? '—') ?></dd>
                    <dt class="col-sm-4 text-muted">Tamaño</dt>
                    <dd class="col-sm-8"><?= esc($order['size_name'] ?? '—') ?></dd>
                    <dt class="col-sm-4 text-muted">Figuras</dt>
                    <dd class="col-sm-8"><?= esc((string) ($order['num_figures'] ?? '—')) ?></dd>
                    <dt class="col-sm-4 text-muted">Notas del cliente</dt>
                    <dd class="col-sm-8"><?= esc($order['client_notes'] ?? '—') ?></dd>
                </dl>
            </div>
        </div>

        <?php if ($cancelled): ?>
            <div class="alert alert-warning">
                <i class="bi bi-x-octagon me-1"></i>Este pedido está <strong>cancelado</strong>.
            </div>
        <?php endif; ?>

        <div class="admin-card mb-3">
            <div class="admin-card-header">
                <h6>Flujo del pedido</h6>
            </div>
            <div class="card-body">
                <div class="admin-timeline">
                    <?php foreach ($flowStates as $i => $state): ?>
                        <?php if ($i > 0): ?>
                            <?php $lineCompleted = ! $cancelled && $idx >= 0 && $idx >= $i; ?>
                            <div class="admin-timeline-line<?= $lineCompleted ? ' completed' : '' ?>"></div>
                        <?php endif; ?>
                        <?php
                        $dotClass = '';
                        $labelClass = '';
                        if (! $cancelled && $idx >= 0) {
                            if ($i < $idx) {
                                $dotClass   = ' completed';
                                $labelClass = ' active';
                            } elseif ($i === $idx) {
                                if ($current === 'completed') {
                                    $dotClass   = ' completed';
                                    $labelClass = ' active';
                                } else {
                                    $dotClass   = ' current';
                                    $labelClass = ' active';
                                }
                            }
                        }
                        ?>
                        <div class="admin-timeline-step">
                            <div class="d-flex flex-column align-items-center px-1">
                                <div class="admin-timeline-dot<?= $dotClass ?>"><?= $i + 1 ?></div>
                                <div class="admin-timeline-label<?= $labelClass ?>"><?= esc($statusLabels[$state] ?? $state) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header">
                <h6>Historial de estados</h6>
            </div>
            <div class="card-body">
                <?php if ($history === []): ?>
                    <p class="text-muted mb-0 small">Sin cambios registrados.</p>
                <?php else: ?>
                    <ul class="list-unstyled mb-0 small">
                        <?php foreach ($history as $row): ?>
                            <?php
                            $from = (string) ($row['from_status'] ?? '');
                            $to   = (string) ($row['to_status'] ?? '');
                            $fromL = $from !== '' ? ($statusLabels[$from] ?? $from) : '—';
                            $toL   = $to !== '' ? ($statusLabels[$to] ?? $to) : '—';
                            ?>
                            <li class="border-bottom pb-2 mb-2">
                                <div class="fw-medium"><?= esc($toL) ?></div>
                                <div class="text-muted">
                                    <?= ! empty($row['created_at']) ? esc(date('d/m/Y H:i', strtotime((string) $row['created_at']))) : '' ?>
                                    <?php if ($from !== ''): ?>
                                        <span> · desde <?= esc($fromL) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if (! empty($row['notes'])): ?>
                                    <div class="mt-1"><?= esc((string) $row['notes']) ?></div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card mb-3">
            <div class="admin-card-header">
                <h6>Acciones de estado</h6>
            </div>
            <div class="card-body">
                <?php if ($nextStates === []): ?>
                    <p class="text-muted small mb-0">No hay transiciones disponibles.</p>
                <?php else: ?>
                    <form action="<?= base_url('admin/portrait-orders/' . $oid . '/status') ?>" method="post" class="admin-form">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="status-notes" class="form-label">Notas (opcional)</label>
                            <textarea class="form-control" id="status-notes" name="notes" rows="3" placeholder="Notas para el historial…"></textarea>
                        </div>
                        <div class="d-flex flex-column gap-2">
                            <?php foreach ($nextStates as $ns): ?>
                                <?php
                                $ns = (string) $ns;
                                $lab = $statusLabels[$ns] ?? $ns;
                                $btnClass = $ns === 'cancelled' ? 'btn-outline-danger' : 'btn-admin';
                                ?>
                                <button type="submit" name="status" value="<?= esc($ns, 'attr') ?>" class="btn btn-sm <?= $btnClass ?>">
                                    <?= $ns === 'cancelled' ? '<i class="bi bi-x-circle me-1"></i>' : '<i class="bi bi-arrow-right-circle me-1"></i>' ?>
                                    <?= esc($lab) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($refUrl !== ''): ?>
            <div class="admin-card mb-3">
                <div class="admin-card-header">
                    <h6>Foto de referencia</h6>
                </div>
                <div class="card-body text-center">
                    <a href="<?= esc($refUrl, 'attr') ?>" class="glightbox d-inline-block" data-gallery="order-<?= esc((string) $oid, 'attr') ?>">
                        <img src="<?= esc($refUrl, 'attr') ?>" alt="Referencia" class="img-fluid rounded" style="max-height:220px;object-fit:cover;">
                    </a>
                    <div class="mt-2">
                        <a href="<?= esc($refUrl, 'attr') ?>" class="small text-accent glightbox" data-gallery="order-<?= esc((string) $oid, 'attr') ?>">Ampliar</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($current === 'in_progress'): ?>
            <div class="admin-card mb-3">
                <div class="admin-card-header">
                    <h6>Subir boceto</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/portrait-orders/' . $oid . '/sketch') ?>" method="post" enctype="multipart/form-data" class="admin-form">
                        <?= csrf_field() ?>
                        <input type="file" class="form-control mb-2" name="sketch_image" accept="image/jpeg,image/png,image/gif,image/webp" required>
                        <button type="submit" class="btn btn-sm btn-admin">Enviar boceto</button>
                    </form>
                    <?php if ($sketch !== ''): ?>
                        <p class="small text-muted mb-0 mt-2">Boceto actual: <a href="<?= esc(base_url($sketch), 'attr') ?>" target="_blank" rel="noopener">ver archivo</a></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($current === 'revision' || $current === 'delivered'): ?>
            <div class="admin-card mb-3">
                <div class="admin-card-header">
                    <h6>Subir trabajo final</h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/portrait-orders/' . $oid . '/final') ?>" method="post" enctype="multipart/form-data" class="admin-form">
                        <?= csrf_field() ?>
                        <input type="file" class="form-control mb-2" name="final_image" accept="image/jpeg,image/png,image/gif,image/webp" required>
                        <button type="submit" class="btn btn-sm btn-admin">Enviar imagen final</button>
                    </form>
                    <?php if ($finalImg !== ''): ?>
                        <p class="small text-muted mb-0 mt-2">Archivo actual: <a href="<?= esc(base_url($finalImg), 'attr') ?>" target="_blank" rel="noopener">ver archivo</a></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($order['admin_notes'] ?? ''): ?>
            <div class="admin-card">
                <div class="admin-card-header">
                    <h6>Notas internas</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-0"><?= nl2br(esc((string) $order['admin_notes'])) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof GLightbox !== 'undefined') {
            GLightbox({ selector: '.glightbox' });
        }
    });
</script>
<?= $this->endSection() ?>