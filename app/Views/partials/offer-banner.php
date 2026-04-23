<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/PARTIALS/OFFER-BANNER.PHP
 * =============================================================================
 * QUÉ HACE: MODAL DE BIENVENIDA QUE PROMOCIONA UN PRODUCTO DESTACADO Y ENLAZA A SU FICHA EN TIENDA.
 * POR QUÉ AQUÍ: SE INCLUYE GLOBAL DESDE EL LAYOUT; AUTO-OPEN UNA VEZ POR SESIÓN VÍA SESSIONSTORAGE EN JS.
 * =============================================================================
 */

$offerProduct = null;
try {
    $productModel = model(\App\Models\ProductModel::class);
    $offerProduct = $productModel
        ->where('is_active', 1)
        ->where('is_featured', 1)
        ->orderBy('id', 'DESC')
        ->first();
    if ($offerProduct === null) {
        $offerProduct = $productModel
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->first();
    }
} catch (\Throwable $e) {
    log_message('warning', 'OfferBanner product lookup failed: ' . $e->getMessage());
    $offerProduct = null;
}

if (! is_array($offerProduct) || empty($offerProduct['slug'])) {
    return;
}

$offerSlug  = (string) $offerProduct['slug'];
$offerName  = (string) ($offerProduct['name'] ?? 'Pieza destacada');
$offerPrice = (float)  ($offerProduct['price'] ?? 0);
$offerCompare = isset($offerProduct['compare_price']) && (float) $offerProduct['compare_price'] > $offerPrice
    ? (float) $offerProduct['compare_price']
    : null;
$offerImageRaw = (string) ($offerProduct['featured_image'] ?? '');
$offerImage    = $offerImageRaw !== ''
    ? (preg_match('#^https?://#i', $offerImageRaw) ? $offerImageRaw : base_url(ltrim($offerImageRaw, '/')))
    : base_url('uploads/site/carousel-productos.png');
$offerShortDesc = trim((string) ($offerProduct['short_description'] ?? ''));
if ($offerShortDesc === '') {
    $offerShortDesc = 'Una pieza destacada del estudio. Edición limitada disponible en la tienda.';
}
$offerProductUrl = base_url('tienda/' . rawurlencode($offerSlug));
?>
<div
    class="modal fade nmz-offer-modal"
    id="nmzOfferModal"
    tabindex="-1"
    aria-labelledby="nmzOfferModalTitle"
    aria-hidden="true"
    data-nmz-offer-modal
>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= esc('Cerrar', 'attr') ?>"></button>
            <div class="modal-body">
                <div class="nmz-offer-modal__grid">
                    <div class="nmz-offer-modal__media">
                        <img src="<?= esc($offerImage, 'attr') ?>" alt="<?= esc($offerName, 'attr') ?>" loading="lazy" decoding="async">
                    </div>
                    <div class="nmz-offer-modal__body">
                        <span class="nmz-offer-modal__eyebrow"><?= esc('Oferta destacada') ?></span>
                        <h2 class="nmz-offer-modal__title" id="nmzOfferModalTitle"><?= esc($offerName) ?></h2>
                        <p class="nmz-offer-modal__text"><?= esc($offerShortDesc) ?></p>
                        <p class="nmz-offer-modal__price">
                            <?php if ($offerCompare !== null) : ?>
                                <span class="text-decoration-line-through text-secondary me-2 fs-6 fw-normal"><?= number_format($offerCompare, 2, ',', '.') ?> €</span>
                            <?php endif; ?>
                            <?= number_format($offerPrice, 2, ',', '.') ?> €
                        </p>
                        <div class="nmz-offer-modal__actions">
                            <a href="<?= esc($offerProductUrl, 'attr') ?>" class="btn btn-nmz">
                                <i class="bi bi-bag-heart me-2" aria-hidden="true"></i><?= esc('Ver la oferta') ?>
                            </a>
                            <button type="button" class="btn btn-nmz-outline" data-bs-dismiss="modal">
                                <?= esc('Seguir mirando') ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
