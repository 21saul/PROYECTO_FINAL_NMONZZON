<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/LAYOUTS/MAIN.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?php // LAYOUT PRINCIPAL DEL SITIO PÚBLICO ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO -->
    <title><?= esc($meta_title ?? $pageTitle ?? 'Inicio') ?> | nmonzzon Studio</title>
    <meta name="description" content="<?= esc($meta_description ?? $metaDescription ?? 'nmonzzon Studio — Retratos personalizados, arte en vivo, branding y diseño. Vigo, España.') ?>">
    <meta name="author" content="nmonzzon Studio">
    <link rel="canonical" href="<?= current_url() ?>">

    <!-- OPEN GRAPH -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= esc($meta_title ?? $pageTitle ?? 'nmonzzon Studio') ?>">
    <meta property="og:description" content="<?= esc($meta_description ?? $metaDescription ?? 'Arte, retratos personalizados, arte en vivo y productos artísticos por nmonzzon.') ?>">
    <meta property="og:image" content="<?= esc($og_image ?? base_url('assets/images/logo.png')) ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:site_name" content="nmonzzon Studio">
    <meta property="og:locale" content="es_ES">

    <!-- TWITTER CARD -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($meta_title ?? $pageTitle ?? 'nmonzzon Studio') ?>">
    <meta name="twitter:description" content="<?= esc($meta_description ?? $metaDescription ?? 'Arte, retratos personalizados y productos artísticos.') ?>">
    <meta name="twitter:image" content="<?= esc($og_image ?? base_url('assets/images/logo.png')) ?>">

    <link rel="icon" href="<?= base_url('favicon.ico') ?>">

    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a1a1a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="apple-touch-icon" href="/assets/images/icons/icon-192x192.png">

    <!-- FUENTES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/css/glightbox.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/custom.css') ?>" rel="stylesheet">

    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-hash" content="<?= csrf_hash() ?>">

    <!-- JSON-LD DATOS ESTRUCTURADOS -->
    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"LocalBusiness","name":"nmonzzon Studio","description":"Arte, retratos personalizados, arte en vivo y productos artísticos","url":"<?= base_url() ?>","logo":"<?= base_url('assets/images/logo.png') ?>","address":{"@type":"PostalAddress","addressLocality":"Vigo","addressCountry":"ES"},"email":"hola@nmonzzon.com","sameAs":["https://www.instagram.com/nmonzzon/"],"priceRange":"€€"}
    </script>
    <?php if (isset($product) && is_array($product)): ?>
    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"Product","name":"<?= esc($product['name'] ?? '', 'js') ?>","description":"<?= esc($product['short_description'] ?? $product['description'] ?? '', 'js') ?>","image":"<?= base_url($product['featured_image'] ?? '') ?>","offers":{"@type":"Offer","priceCurrency":"EUR","price":"<?= esc($product['price'] ?? '0', 'js') ?>","availability":"https://schema.org/InStock"}}
    </script>
    <?php endif; ?>

    <?= $this->renderSection('extra_css') ?>
</head>
<body>
    <!-- PANTALLA DE CARGA INICIAL (PRELOADER) -->
    <div id="preloader">
        <div class="preloader-inner">
            <span class="preloader-text">nmonzzon</span>
        </div>
    </div>

    <?= $this->include('partials/navbar') ?>

    <!-- CONTENIDO PRINCIPAL DE LA PÁGINA (SE RELLENA DESDE CADA VISTA) -->
    <main class="pt-nav-offset">
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('partials/footer') ?>

    <!-- ENLACE FLOTANTE A WHATSAPP (LOGO) -->
    <?= $this->include('partials/chatbot') ?>

    <!-- ENLACE VOLVER ARRIBA -->
    <a href="#" class="back-to-top" id="backToTop"><i class="bi bi-chevron-up"></i></a>

    <!-- Toast al añadir al carrito (cart.js): centrado en móvil, esquina en md+ -->
    <div class="toast-container position-fixed bottom-0 start-0 end-0 p-2 p-md-3 d-flex justify-content-center justify-content-md-end align-items-end" style="z-index: 1090; pointer-events: none;" aria-live="polite">
        <div id="cart-toast" class="toast align-items-center text-bg-dark border-0 mw-100" style="max-width: min(100%, 420px); pointer-events: auto;" role="status" data-bs-delay="2800">
            <div class="d-flex">
                <div class="toast-body">Producto añadido al carrito</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Cerrar"></button>
            </div>
        </div>
    </div>

    <!-- LIBRERÍAS JAVASCRIPT Y SCRIPT PRINCIPAL DEL FRONT -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/imagesloaded@5.0.0/imagesloaded.pkgd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/glightbox@3.3.0/dist/js/glightbox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <script src="<?= base_url('assets/js/cart.js') ?>"></script>

    <?= $this->renderSection('extra_js') ?>
</body>
</html>