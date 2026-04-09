<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/DASHBOARD.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Dashboard') ?>

<?php
$revenue = $revenue ?? 0;
$revenueChange = $revenueChange ?? 0;
$activeOrders = $activeOrders ?? 0;
$upcomingBookings = $upcomingBookings ?? 0;
$unreadMessages = $unreadMessages ?? 0;
$recentOrders = $recentOrders ?? [];
$nextBookings = $nextBookings ?? [];
$monthLabels = $monthLabels ?? [];
$revenueData = $revenueData ?? [];
$styleLabels = $styleLabels ?? [];
$styleData = $styleData ?? [];
$dashboardDateLabel = $dashboardDateLabel ?? '';
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Dashboard</h1>
    <span class="text-muted small"><?= esc($dashboardDateLabel !== '' ? $dashboardDateLabel : date('d/m/Y')) ?></span>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon revenue"><i class="bi bi-currency-euro"></i></div>
            <div>
                <div class="kpi-label">Ingresos del mes</div>
                <div class="kpi-value"><?= number_format((float)$revenue, 2, ',', '.') ?> €</div>
                <div class="kpi-change <?= $revenueChange >= 0 ? 'up' : 'down' ?>">
                    <i class="bi bi-arrow-<?= $revenueChange >= 0 ? 'up' : 'down' ?>"></i>
                    <?= abs($revenueChange) ?>% vs mes anterior
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon orders"><i class="bi bi-box-seam"></i></div>
            <div>
                <div class="kpi-label">Pedidos activos</div>
                <div class="kpi-value"><?= (int)$activeOrders ?></div>
                <div class="text-muted" style="font-size:0.75rem;">Retratos + productos en proceso</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon bookings"><i class="bi bi-calendar-check"></i></div>
            <div>
                <div class="kpi-label">Reservas próximas</div>
                <div class="kpi-value"><?= (int)$upcomingBookings ?></div>
                <div class="text-muted" style="font-size:0.75rem;">Próximos 30 días</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon messages"><i class="bi bi-envelope"></i></div>
            <div>
                <div class="kpi-label">Mensajes sin leer</div>
                <div class="kpi-value"><?= (int)$unreadMessages ?></div>
                <a href="<?= base_url('admin/messages') ?>" class="text-accent" style="font-size:0.75rem;text-decoration:none;">Ver mensajes →</a>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h6>Ingresos — Últimos 12 meses</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h6>Pedidos por estilo</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="max-width:260px;width:100%;">
                    <canvas id="styleChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders + Upcoming Bookings -->
<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="admin-card">
            <div class="admin-card-header">
                <h6 class="mb-0">Actividad reciente</h6>
                <div class="d-flex flex-wrap gap-1 justify-content-end">
                    <a href="<?= base_url('admin/portrait-orders') ?>" class="btn btn-sm btn-admin-outline">Pedidos retrato</a>
                    <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-admin-outline">Clientes (tienda)</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cliente</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentOrders)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">No hay pedidos recientes (tienda ni retratos)</td></tr>
                            <?php else: ?>
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td class="fw-medium">
                                    <?php
                                    $ordRef = $order['order_number'] ?? '#' . $order['id'];
                                    $ordHref = $order['admin_href'] ?? null;
                                    ?>
                                    <?php if (! empty($ordHref)): ?>
                                    <a href="<?= esc($ordHref, 'attr') ?>" class="text-reset text-decoration-none"><?= esc($ordRef) ?></a>
                                    <?php else: ?>
                                    <?= esc($ordRef) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($order['customer_name'] ?? $order['user_name'] ?? '—') ?></td>
                                <td><span class="badge bg-light text-dark"><?= esc($order['type'] ?? 'Producto') ?></span></td>
                                <td><span class="badge-status badge-<?= esc($order['status'] ?? 'pending') ?>"><?= esc(ucfirst($order['status'] ?? 'pending')) ?></span></td>
                                <td><?= number_format((float)($order['total'] ?? 0), 2, ',', '.') ?> €</td>
                                <td class="text-muted small"><?= date('d/m/Y', strtotime($order['created_at'] ?? 'now')) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="admin-card">
            <div class="admin-card-header">
                <h6>Próximas reservas</h6>
                <a href="<?= base_url('admin/bookings/calendar') ?>" class="btn btn-sm btn-admin-outline">Calendario</a>
            </div>
            <div class="card-body">
                <?php if (empty($nextBookings)): ?>
                <p class="text-muted text-center py-3 mb-0">No hay reservas próximas</p>
                <?php else: ?>
                <?php
                $nextBookingsList = array_values($nextBookings);
                $nextBookingsLast = count($nextBookingsList) - 1;
                foreach ($nextBookingsList as $nbIdx => $booking):
                ?>
                <div class="d-flex align-items-start gap-3 <?= $nbIdx !== $nextBookingsLast ? 'mb-3 pb-3 border-bottom' : '' ?>">
                    <div class="kpi-icon bookings flex-shrink-0" style="width:40px;height:40px;font-size:1rem;">
                        <i class="bi bi-calendar-event"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-medium small"><?= esc($booking['event_type'] ?? '') ?></div>
                        <div class="text-muted" style="font-size:0.75rem;">
                            <?= !empty($booking['event_date']) ? date('d/m/Y', strtotime($booking['event_date'])) : '—' ?>
                            · <?= esc($booking['event_city'] ?? $booking['event_location'] ?? '') ?>
                        </div>
                        <span class="badge-status badge-<?= esc($booking['status'] ?? 'pending') ?>" style="font-size:0.6rem;"><?= esc(ucfirst($booking['status'] ?? 'pending')) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
(function() {
    const monthLabels = <?= json_encode($monthLabels) ?>;
    const revenueData = <?= json_encode($revenueData) ?>;
    const styleLabels = <?= json_encode($styleLabels) ?>;
    const styleData = <?= json_encode($styleData) ?>;

    if (document.getElementById('revenueChart')) {
        new Chart(document.getElementById('revenueChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: monthLabels.length ? monthLabels : ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
                datasets: [{
                    label: 'Ingresos (€)',
                    data: revenueData.length ? revenueData : [0,0,0,0,0,0,0,0,0,0,0,0],
                    borderColor: '#c9a96e',
                    backgroundColor: 'rgba(201, 169, 110, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#c9a96e',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { callback: v => v + ' €' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    if (document.getElementById('styleChart')) {
        new Chart(document.getElementById('styleChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: styleLabels.length ? styleLabels : ['Color', 'B/N', 'Figurín', 'Sin Caras', 'A Línea'],
                datasets: [{
                    data: styleData.length ? styleData : [0,0,0,0,0],
                    backgroundColor: ['#c9a96e','#1a1a1a','#6b6b6b','#e8e8e8','#b08d4f'],
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, font: { size: 11 } } }
                },
                cutout: '65%',
            }
        });
    }
})();
</script>
<?= $this->endSection() ?>