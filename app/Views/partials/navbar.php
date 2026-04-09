<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/PARTIALS/NAVBAR.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?php // BARRA DE NAVEGACIÓN PÚBLICA ?>
<nav class="navbar navbar-expand-lg fixed-top navbar-nmz" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/') ?>">
            <img src="<?= base_url('uploads/logos/logo-nmonzzon-estudio.png') ?>"
                 alt="nmonzzon Studio"
                 class="navbar-logo"
                 height="60">
        </a>

        <button class="navbar-toggler border-0 shadow-none"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#nmzNavbarOffcanvas"
                aria-controls="nmzNavbarOffcanvas"
                aria-expanded="false"
                aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="offcanvas offcanvas-end offcanvas-lg offcanvas-nmz"
             tabindex="-1"
             id="nmzNavbarOffcanvas"
             aria-labelledby="nmzNavbarOffcanvasLabel">

            <div class="offcanvas-header d-lg-none">
                <h5 class="offcanvas-title" id="nmzNavbarOffcanvasLabel">Menú</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
            </div>

            <div class="offcanvas-body d-lg-flex flex-lg-grow-1 align-items-lg-center px-lg-0">

                <ul class="navbar-nav mx-lg-auto gap-lg-4">
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('productos*') ? 'active' : '' ?>"
                           href="<?= base_url('productos') ?>">Productos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('retratos*') ? 'active' : '' ?>"
                           href="<?= base_url('retratos') ?>">Retratos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('arte-en-vivo*') ? 'active' : '' ?>"
                           href="<?= base_url('arte-en-vivo') ?>">Arte en vivo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('branding*') ? 'active' : '' ?>"
                           href="<?= base_url('branding') ?>">Branding</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('eventos*') ? 'active' : '' ?>"
                           href="<?= base_url('eventos') ?>">Eventos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('diseno*') ? 'active' : '' ?>"
                           href="<?= base_url('diseno') ?>">Diseño</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= url_is('contacto*') ? 'active' : '' ?>"
                           href="<?= base_url('contacto') ?>">Contacto</a>
                    </li>
                </ul>

                <!-- Grupo 3: Iconos (carrito + usuario); sin nombre en línea para no desplazar el navbar -->
                <ul class="navbar-nav ms-lg-4 flex-row align-items-center gap-3">
                    <li class="nav-item">
                        <a href="<?= base_url('carrito') ?>" class="nav-link position-relative" aria-label="Carrito">
                            <i class="bi bi-bag" style="font-size: 1.15rem;"></i>
                            <?php
                            $cart = session()->get('cart') ?? [];
                            $cartQty = 0;
                            foreach ($cart as $line) {
                                $cartQty += (int) ($line['quantity'] ?? 0);
                            }
                            ?>
                            <span class="cart-badge" <?= $cartQty > 0 ? '' : 'style="display:none"' ?>><?= $cartQty ?></span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <?php if (session()->get('isLoggedIn')) :
                            $navAvatarRaw = trim((string) (session('avatar') ?? ''));
                            $navAvatarUrl = '';
                            if ($navAvatarRaw !== '') {
                                $navAvatarUrl = preg_match('#^https?://#i', $navAvatarRaw)
                                    ? $navAvatarRaw
                                    : base_url(ltrim($navAvatarRaw, '/'));
                            }
                            $navUserLabel = 'Menú de cuenta';
                            $navName = trim((string) (session('name') ?? ''));
                            if ($navName !== '') {
                                $navUserLabel .= ': ' . $navName;
                            }
                            ?>
                            <a class="nav-link dropdown-toggle d-flex align-items-center justify-content-center py-lg-2 nmz-nav-user-trigger"
                               href="#"
                               id="nmzUserNavDropdown"
                               role="button"
                               data-bs-toggle="dropdown"
                               data-bs-display="static"
                               aria-expanded="false"
                               aria-label="<?= esc($navUserLabel, 'attr') ?>">
                                <?php if ($navAvatarUrl !== '') : ?>
                                    <img src="<?= esc($navAvatarUrl, 'attr') ?>"
                                         alt=""
                                         class="nmz-nav-user-avatar"
                                         width="20"
                                         height="20"
                                         decoding="async">
                                <?php else : ?>
                                    <i class="bi bi-person-circle nmz-nav-user-icon" aria-hidden="true"></i>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 py-2 nmz-user-dropdown" aria-labelledby="nmzUserNavDropdown">
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('mi-cuenta') ?>">
                                        <i class="bi bi-grid-1x2 me-2 text-secondary"></i> Mi cuenta
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('mi-cuenta/perfil') ?>">
                                        <i class="bi bi-person-gear me-2 text-secondary"></i> Mi perfil
                                    </a>
                                </li>
                                <?php if (session()->get('role') === 'admin') : ?>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('admin/dashboard') ?>">
                                        <i class="bi bi-speedometer2 me-2 text-secondary"></i> Admin
                                    </a>
                                </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                                    </a>
                                </li>
                            </ul>
                        <?php else : ?>
                            <a href="<?= base_url('login') ?>" class="nav-link d-flex align-items-center" aria-label="Acceder">
                                <i class="bi bi-person" style="font-size: 1.2rem;"></i>
                            </a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>