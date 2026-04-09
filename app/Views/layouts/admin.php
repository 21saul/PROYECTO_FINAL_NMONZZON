<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/LAYOUTS/ADMIN.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?php // LAYOUT DEL PANEL DE ADMINISTRACIÓN: BARRA LATERAL, BARRA SUPERIOR, ALERTAS FLASH Y ÁREA DE CONTENIDO CON LIBRERÍAS DE TABLAS Y CALENDARIO ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-hash" content="<?= csrf_hash() ?>">
    <title><?= esc($pageTitle ?? 'Admin') ?> — nmonzzon Studio</title>

    <!-- FUENTES Y HOJAS DE ESTILO DEL PANEL (BOOTSTRAP, DATATABLES, FULLCALENDAR, ADMIN) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
    <?= $this->renderSection('extra_css') ?>
</head>
<body class="admin-body">

<?php
// DEFINICIÓN DE LA URL ACTUAL, MENSAJES NO LEÍDOS Y ENLACES DEL MENÚ LATERAL
$currentUrl = current_url(true)->getPath();
$unreadMessages = 0;
try {
    $contactModel = model(\App\Models\ContactMessageModel::class);
    $unreadMessages = $contactModel->where('is_read', 0)->countAllResults();
} catch (\Throwable $e) {
    $unreadMessages = 0;
}

$adminDisplayName = (string) (session()->get('name') ?? session()->get('user_name') ?? 'Admin');

$navItems = [
    ['section' => 'Principal'],
    ['url' => '/admin/dashboard',         'icon' => 'bi-grid-1x2-fill',    'label' => 'Dashboard'],
    ['section' => 'Contenido'],
    ['url' => '/admin/portfolio',          'icon' => 'bi-images',           'label' => 'Portfolio'],
    ['url' => '/admin/portrait-orders',    'icon' => 'bi-brush',            'label' => 'Pedidos Retratos'],
    ['url' => '/admin/products',           'icon' => 'bi-bag',              'label' => 'Productos'],
    ['url' => '/admin/bookings',           'icon' => 'bi-calendar-event',   'label' => 'Reservas Arte en Vivo'],
    ['section' => 'Proyectos'],
    ['url' => '/admin/branding',           'icon' => 'bi-palette',          'label' => 'Branding'],
    ['url' => '/admin/design',             'icon' => 'bi-vector-pen',       'label' => 'Diseño'],
    ['url' => '/admin/events',             'icon' => 'bi-calendar3',        'label' => 'Eventos'],
    ['section' => 'Gestión'],
    ['url' => '/admin/categories',         'icon' => 'bi-tags',             'label' => 'Categorías'],
    ['url' => '/admin/testimonials',       'icon' => 'bi-chat-quote',       'label' => 'Testimonios'],
    ['url' => '/admin/coupons',            'icon' => 'bi-ticket-perforated','label' => 'Cupones'],
    ['url' => '/admin/messages',           'icon' => 'bi-envelope',         'label' => 'Mensajes', 'badge' => $unreadMessages],
    ['url' => '/admin/users',              'icon' => 'bi-people',           'label' => 'Usuarios'],
    ['url' => '/admin/settings',           'icon' => 'bi-gear',             'label' => 'Configuración'],
];
?>

<!-- CAPA OSCURA SOBRE EL CONTENIDO CUANDO EL MENÚ LATERAL ESTÁ ABIERTO EN MÓVIL -->
<div class="admin-sidebar-overlay" id="sidebarOverlay"></div>

<!-- MENÚ LATERAL FIJO CON MARCA Y ENLACES POR SECCIONES -->
<aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-sidebar-brand">
        <span class="admin-sidebar-brand-text">nmonzzon</span>
    </div>
    <nav class="admin-sidebar-nav">
        <?php foreach ($navItems as $item): ?>
            <?php if (isset($item['section'])): ?>
                <div class="admin-nav-section"><?= esc($item['section']) ?></div>
            <?php else:
                $isActive = str_contains($currentUrl, $item['url']);
            ?>
                <a href="<?= base_url($item['url']) ?>" class="admin-nav-link<?= $isActive ? ' active' : '' ?>">
                    <i class="bi <?= $item['icon'] ?>"></i>
                    <span><?= esc($item['label']) ?></span>
                    <?php if (!empty($item['badge']) && $item['badge'] > 0): ?>
                    <span class="admin-nav-badge"><?= (int)$item['badge'] ?></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
</aside>

<!-- BARRA SUPERIOR: MENÚ HAMBURGUESA, BÚSQUEDA Y MENÚ DE USUARIO -->
<header class="admin-topbar">
    <button class="admin-topbar-toggle" id="sidebarToggle" aria-label="Menú">
        <i class="bi bi-list"></i>
    </button>

    <!-- Búsqueda global no implementada: oculto para no sugerir una función inexistente -->
    <div class="admin-topbar-search position-relative d-none" aria-hidden="true">
        <i class="bi bi-search search-icon"></i>
        <input type="search" class="form-control" placeholder="Buscar…" aria-label="Buscar" tabindex="-1" disabled>
    </div>

    <div class="admin-topbar-actions">
        <a href="<?= base_url('/') ?>" class="btn-icon" title="Ver sitio web" target="_blank">
            <i class="bi bi-box-arrow-up-right"></i>
        </a>
        <a href="<?= base_url('/admin/messages') ?>" class="btn-icon" title="Mensajes">
            <i class="bi bi-bell"></i>
            <?php if ($unreadMessages > 0): ?>
            <span class="notification-dot"></span>
            <?php endif; ?>
        </a>
        <div class="dropdown">
            <div class="admin-user-menu" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="admin-user-avatar"><?= esc(strtoupper(mb_substr($adminDisplayName !== '' ? $adminDisplayName : 'A', 0, 1))) ?></div>
                <span class="d-none d-md-inline small fw-medium"><?= esc($adminDisplayName) ?></span>
                <i class="bi bi-chevron-down small"></i>
            </div>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="<?= base_url('/admin/settings') ?>"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="<?= base_url('/admin/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</header>

<!-- ZONA PRINCIPAL: MENSAJES FLASH Y SECCIÓN DE CONTENIDO DE CADA PANTALLA -->
<main class="admin-main">
    <?php if ($flash = session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-1"></i><?= esc($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    <?php if ($flash = session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-1"></i><?= esc($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</main>

<!-- SCRIPTS DEL PANEL: BOOTSTRAP, GRÁFICOS, CALENDARIO, DATATABLES Y ADMIN.JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@2.1.8/js/dataTables.bootstrap5.min.js"></script>
<script src="<?= base_url('assets/js/admin.js') ?>"></script>
<?= $this->renderSection('extra_js') ?>
</body>
</html>