<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/PRODUCTOS/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Tienda') ?>

<?= $this->section('content') ?>

<?php
$products         = $products ?? [];
$categories       = $categories ?? [];
$selectedCategory = $selectedCategory ?? null;
$currentOrder     = $currentOrder ?? 'recientes';
$currentPage      = $currentPage ?? 1;
$totalPages       = $totalPages ?? 1;
$totalProducts    = $totalProducts ?? count($products);

$placeholderSrc = base_url('assets/images/placeholder.svg');

$shopUrl = rtrim(base_url('productos'), '/');

/* Hero TIENDA: retrato editorial alta resolución (colección retratos/estilos) */
$tiendaHeroRel  = 'uploads/retratos/estilos/estilo_color_sin_caras.jpg';
$tiendaHeroPath = FCPATH . $tiendaHeroRel;
$tiendaHeroBg   = base_url($tiendaHeroRel);
if (is_file($tiendaHeroPath)) {
    $tiendaHeroBg .= '?v=' . filemtime($tiendaHeroPath);
}

$buildQuery = static function (?string $catSlug, string $order, ?int $page = null) use ($shopUrl): string {
    $q = array_filter([
        'categoria' => $catSlug,
        'orden'     => $order !== 'recientes' ? $order : null,
        'page'      => ($page !== null && $page > 1) ? $page : null,
    ], static fn ($v) => $v !== null && $v !== '');
    return $q === [] ? $shopUrl : $shopUrl . '?' . http_build_query($q);
};
?>

<!-- HERO TIENDA: img en capa (nitidez); /productos -->
<section class="page-hero page-hero--tall page-hero--tienda">
    <img
        class="page-hero-bg-img page-hero-bg-img--tienda page-hero-bg-img--retratos-shop"
        src="<?= esc($tiendaHeroBg, 'attr') ?>"
        alt=""
        width="3508"
        height="2480"
        sizes="100vw"
        fetchpriority="high"
        decoding="async"
    >
    <div class="page-hero-overlay" style="background: linear-gradient(180deg, rgba(26,26,26,.55) 0%, rgba(26,26,26,.80) 100%);"></div>
    <div class="container page-hero-content text-center py-5">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Tienda', 'url' => null],
            ],
            'nmzHeroTitle'    => 'Tienda',
            'nmzHeroSubtitle' => 'Láminas · Originales · Merchandising',
        ]) ?>
    </div>
</section>

<!-- FILTROS -->
<section class="shop-filters-section sticky-top" aria-label="Filtros de la tienda">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-12 col-md-7">
                <div class="shop-filters-cats d-flex flex-wrap gap-2 justify-content-center justify-content-md-start">
                    <a href="<?= esc($buildQuery(null, $currentOrder), 'attr') ?>"
                       class="shop-filter-chip<?= $selectedCategory === null ? ' is-active' : '' ?>">
                        Todas las categorías
                    </a>
                    <a href="<?= esc($buildQuery('prints', $currentOrder), 'attr') ?>"
                       class="shop-filter-chip<?= $selectedCategory === 'prints' ? ' is-active' : '' ?>">
                        Prints
                    </a>
                    <a href="<?= esc($buildQuery('totebags', $currentOrder), 'attr') ?>"
                       class="shop-filter-chip<?= $selectedCategory === 'totebags' ? ' is-active' : '' ?>">
                        Totebags
                    </a>
                </div>
            </div>
            <div class="col-12 col-md-5">
                <form method="get" action="<?= esc(base_url('productos'), 'attr') ?>" class="shop-filters-sort justify-content-center justify-content-md-end">
                    <?php if ($selectedCategory !== null) : ?>
                    <input type="hidden" name="categoria" value="<?= esc($selectedCategory, 'attr') ?>">
                    <?php endif; ?>
                    <label class="shop-sort-label" for="shop-order-select">Ordenar</label>
                    <select id="shop-order-select" class="form-select shop-sort-select" name="orden" onchange="this.form.submit()">
                        <option value="recientes"  <?= $currentOrder === 'recientes'  ? 'selected' : '' ?>>Más recientes</option>
                        <option value="precio_asc"  <?= $currentOrder === 'precio_asc' ? 'selected' : '' ?>>Precio: menor a mayor</option>
                        <option value="precio_desc" <?= $currentOrder === 'precio_desc'? 'selected' : '' ?>>Precio: mayor a menor</option>
                        <option value="destacados"  <?= $currentOrder === 'destacados' ? 'selected' : '' ?>>Destacados</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- GRID DE PRODUCTOS -->
<section class="shop-catalog-section">
    <div class="container">
        <?php if (empty($products)) : ?>
        <div class="shop-catalog-empty text-center py-5">
            <span class="shop-catalog-empty__icon" aria-hidden="true"><i class="bi bi-bag-heart"></i></span>
            <p class="shop-catalog-empty__text mb-0">No hay productos en esta categoría.</p>
        </div>
        <?php else : ?>

        <div class="shop-catalog-meta" role="status">
            <div class="shop-catalog-pill shop-catalog-pill--count">
                <i class="bi bi-grid-3x3-gap-fill shop-catalog-pill__icon" aria-hidden="true"></i>
                <span class="shop-catalog-pill__main">
                    <span class="shop-catalog-pill__num"><?= (int) $totalProducts ?></span>
                    <span class="shop-catalog-pill__label">producto<?= $totalProducts !== 1 ? 's' : '' ?> en la tienda</span>
                </span>
            </div>
            <?php if ($totalPages > 1) : ?>
            <div class="shop-catalog-pill shop-catalog-pill--pages">
                <i class="bi bi-bookmarks shop-catalog-pill__icon" aria-hidden="true"></i>
                <span class="shop-catalog-pill__label">Página <strong><?= (int) $currentPage ?></strong> de <strong><?= (int) $totalPages ?></strong></span>
            </div>
            <?php endif; ?>
        </div>

        <div class="row g-4 shop-catalog-grid">
            <?php foreach ($products as $i => $product) :
                $productId = (int) ($product['id'] ?? 0);
                $isDisk    = $productId <= 0;
                $slug      = $product['slug'] ?? '';
                $img       = $product['featured_image'] ?? '';
                $imgUrl    = !empty($img) ? base_url($img) : $placeholderSrc;
                // catalog_stock: BD + variantes (CartService); evita WhatsApp cuando el padre tiene stock 0 pero hay variantes
                $catalogStock = (int) ($product['catalog_stock'] ?? $product['stock'] ?? 0);
                $delay        = ($i % 12) * 50;
                $prodName  = $product['card_title'] ?? $product['name'] ?? '';
                $imgRel    = (string) ($product['featured_image'] ?? '');
                $isShopAsset = str_contains($imgRel, 'uploads/productos/prints/')
                    || str_contains($imgRel, 'uploads/productos/totebags/');
                $canAddToCart = $catalogStock > 0
                    && ($productId > 0 || ($productId <= 0 && $isShopAsset));

                if ($isDisk) {
                    $href      = esc($imgUrl, 'attr');
                    $linkClass = 'glightbox';
                    $linkExtra = 'data-gallery="tienda" data-glightbox="title: ' . esc($prodName, 'attr') . '"';
                } else {
                    $href      = esc(base_url('productos/' . $slug), 'attr');
                    $linkClass = '';
                    $linkExtra = '';
                }
            ?>
            <div class="col-6 col-md-4 col-lg-3" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                <div class="product-card-wrap">
                    <a href="<?= $href ?>" class="text-decoration-none text-body d-block h-100 product-card-link <?= $linkClass ?>" <?= $linkExtra ?>>
                        <article class="card h-100 border-0 product-card overflow-hidden">
                            <div class="ratio overflow-hidden" style="--bs-aspect-ratio: 125%;">
                                <img
                                    src="<?= esc($imgUrl, 'attr') ?>"
                                    class="object-fit-cover w-100 h-100"
                                    alt="<?= esc($prodName) ?>"
                                    loading="lazy" decoding="async"
                                    onerror="this.onerror=null;this.src='<?= esc($placeholderSrc, 'attr') ?>';"
                                >
                            </div>
                            <div class="card-body">
                                <h2 class="h6 product-name font-heading mb-1"><?= esc($prodName) ?></h2>
                                <p class="price-amount mb-0">
                                    <?= number_format((float) ($product['price'] ?? 0), 2, ',', '.') ?> €
                                </p>
                                <?php if ($catalogStock <= 0) : ?>
                                <span class="badge bg-secondary mt-2">Agotado</span>
                                <?php endif; ?>
                            </div>
                        </article>
                    </a>
                    <?php if ($canAddToCart) : ?>
                    <form action="<?= esc(base_url('carrito/add'), 'attr') ?>" method="post" class="add-to-cart-form product-card__cart-form">
                        <?= csrf_field() ?>
                        <?php if ($productId > 0) : ?>
                        <input type="hidden" name="product_id" value="<?= $productId ?>">
                        <?php else : ?>
                        <input type="hidden" name="catalog_image" value="<?= esc($imgRel, 'attr') ?>">
                        <input type="hidden" name="product_slug" value="<?= esc($slug, 'attr') ?>">
                        <?php endif; ?>
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="product-card__cart-btn" aria-label="Añadir al carrito" title="Añadir al carrito">
                            <i class="bi bi-cart-plus" aria-hidden="true"></i>
                        </button>
                    </form>
                    <?php else : ?>
                    <a href="https://wa.me/34623964677?text=<?= rawurlencode('Hola, me interesa el producto: ' . $prodName) ?>"
                       target="_blank" rel="noopener noreferrer"
                       class="product-card__cart-form product-card__cart-form--whatsapp"
                       onclick="event.stopPropagation();">
                        <span class="product-card__cart-btn product-card__cart-btn--whatsapp" title="Consultar por WhatsApp">
                            <i class="bi bi-whatsapp" aria-hidden="true"></i>
                        </span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($totalPages > 1) : ?>
        <nav aria-label="Paginación productos" class="mt-5">
            <ul class="pagination pagination-nmz justify-content-center flex-wrap mb-0">
                <?php if ($currentPage > 1) : ?>
                <li class="page-item">
                    <a class="page-link" href="<?= esc($buildQuery($selectedCategory, $currentOrder, $currentPage - 1), 'attr') ?>" aria-label="Anterior">&laquo;</a>
                </li>
                <?php else : ?>
                <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
                <?php endif; ?>

                <?php
                $range = 2;
                $start = max(1, $currentPage - $range);
                $end   = min($totalPages, $currentPage + $range);

                if ($start > 1) : ?>
                <li class="page-item"><a class="page-link" href="<?= esc($buildQuery($selectedCategory, $currentOrder, 1), 'attr') ?>">1</a></li>
                <?php if ($start > 2) : ?><li class="page-item disabled"><span class="page-link">&hellip;</span></li><?php endif; ?>
                <?php endif; ?>

                <?php for ($pg = $start; $pg <= $end; $pg++) : ?>
                <li class="page-item <?= $pg === $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="<?= esc($buildQuery($selectedCategory, $currentOrder, $pg), 'attr') ?>"><?= $pg ?></a>
                </li>
                <?php endfor; ?>

                <?php if ($end < $totalPages) : ?>
                <?php if ($end < $totalPages - 1) : ?><li class="page-item disabled"><span class="page-link">&hellip;</span></li><?php endif; ?>
                <li class="page-item"><a class="page-link" href="<?= esc($buildQuery($selectedCategory, $currentOrder, $totalPages), 'attr') ?>"><?= $totalPages ?></a></li>
                <?php endif; ?>

                <?php if ($currentPage < $totalPages) : ?>
                <li class="page-item">
                    <a class="page-link" href="<?= esc($buildQuery($selectedCategory, $currentOrder, $currentPage + 1), 'attr') ?>" aria-label="Siguiente">&raquo;</a>
                </li>
                <?php else : ?>
                <li class="page-item disabled"><span class="page-link">&raquo;</span></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
document.querySelectorAll('.product-card__cart-form').forEach(function (form) {
    form.addEventListener('submit', function () {
        var btn = form.querySelector('.product-card__cart-btn');
        if (!btn) return;
        btn.classList.add('is-added');
        var icon = btn.querySelector('i');
        if (icon) {
            icon.className = 'bi bi-cart-check-fill';
            setTimeout(function () {
                icon.className = 'bi bi-cart-plus';
                btn.classList.remove('is-added');
            }, 1500);
        }
    });
});
</script>
<?= $this->endSection() ?>