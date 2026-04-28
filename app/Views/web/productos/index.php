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

        <!-- Toolbar: buscador + toggle vista (grid/lista). Reemplaza el meta original. -->
        <div class="toolbar">
            <div class="search-wrapper">
                <i class="bi bi-search" aria-hidden="true"></i>
                <input
                    type="search"
                    id="product-search"
                    placeholder="Buscar productos…"
                    autocomplete="off"
                    spellcheck="false"
                    aria-label="Buscar productos en la tienda">
                <span id="product-count" class="search-count" aria-live="polite">
                    <strong><?= (int) $totalProducts ?></strong>&nbsp;producto<?= $totalProducts !== 1 ? 's' : '' ?>
                </span>
            </div>
            <div class="view-toggle" role="group" aria-label="Cambiar vista de productos">
                <button type="button"
                        class="view-toggle__btn active"
                        data-view="grid"
                        aria-pressed="true"
                        aria-label="Vista en cuadrícula"
                        title="Cuadrícula">
                    <i class="bi bi-grid-fill" aria-hidden="true"></i>
                </button>
                <button type="button"
                        class="view-toggle__btn"
                        data-view="list"
                        aria-pressed="false"
                        aria-label="Vista en lista"
                        title="Lista">
                    <i class="bi bi-list-ul" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <?php if ($totalPages > 1) : ?>
        <p class="shop-catalog-pages-info text-muted small mb-3" role="status">
            Página <strong><?= (int) $currentPage ?></strong> de <strong><?= (int) $totalPages ?></strong>
        </p>
        <?php endif; ?>

        <div id="products-grid" class="grid-view">
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
            <div class="product-card-wrap"
                 data-name="<?= esc(mb_strtolower($prodName, 'UTF-8'), 'attr') ?>"
                 data-aos="fade-up" data-aos-delay="<?= $delay ?>">
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
            <?php endforeach; ?>
        </div>

        <!-- Estado "sin resultados" para el buscador (oculto por defecto) -->
        <div id="no-results" data-aos="fade-up" style="display: none;">
            <i class="bi bi-emoji-frown" aria-hidden="true"></i>
            <p>Ningún producto coincide con tu búsqueda.</p>
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

<?= $this->section('extra_css') ?>
<style>
/* =============================================================================
   /productos — Toolbar (buscador + toggle vista) + grid/list view
   Estilos contenidos en la propia vista para no depender del bundle global.
   ============================================================================= */

/* --- Toolbar contenedor: buscador a la izquierda, toggle a la derecha. ------ */
.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 2rem;
}

/* --- Buscador con icono integrado y badge contador. ------------------------ */
.search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    flex: 1 1 280px;
    max-width: 520px;
}

.search-wrapper > .bi-search {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    pointer-events: none;
    font-size: 0.95rem;
    transition: color 0.3s ease;
    z-index: 2;
}

#product-search {
    width: 100%;
    padding: 0.625rem 1rem 0.625rem 40px;
    font-family: inherit;
    font-size: 0.92rem;
    color: #2d2d2d;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 50px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    outline: none;
    transition: box-shadow 0.3s ease, border-color 0.3s ease;
}

#product-search::placeholder {
    color: #9aa0a6;
    transition: opacity 0.3s ease;
}

#product-search:focus {
    border-color: #adb5bd;
    box-shadow: 0 0 0 3px rgba(108, 117, 125, 0.3);
}

#product-search:focus::placeholder { opacity: 0.5; }
#product-search:not(:placeholder-shown)::placeholder { opacity: 0; }

.search-wrapper:focus-within > .bi-search { color: #212529; }

/* Badge contador junto al input. */
.search-count {
    margin-left: 0.75rem;
    padding: 0.32rem 0.85rem;
    font-size: 0.78rem;
    font-weight: 500;
    color: #495057;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 50px;
    white-space: nowrap;
    transition: transform 0.25s ease;
}
.search-count strong { color: #212529; font-weight: 600; }
.search-count.is-pulse { animation: nmzCountPulse 0.45s ease; }
@keyframes nmzCountPulse {
    0%   { transform: scale(1); }
    50%  { transform: scale(1.08); }
    100% { transform: scale(1); }
}

/* Quita la cruz nativa del search en webkit. */
#product-search::-webkit-search-cancel-button {
    -webkit-appearance: none;
    appearance: none;
}

/* --- Toggle pill grid/lista: dos botones segmentados. ----------------------- */
.view-toggle {
    display: flex;
    gap: 0;
    border: 1px solid #dee2e6;
    border-radius: 50px;
    overflow: hidden;
    background: #fff;
}

.view-toggle__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 3rem;
    padding: 0.5rem 1.1rem;
    background: transparent;
    color: #495057;
    border: 0;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.3s ease, color 0.3s ease;
}

.view-toggle__btn:hover { color: #212529; }
.view-toggle__btn.active { background: #212529; color: #fff; }
.view-toggle__btn:focus-visible { outline: 2px solid #c9a96e; outline-offset: -2px; }

/* --- Grid container: dos modos (grid/list) controlados por clase. ----------- */
#products-grid { width: 100%; }

#products-grid.grid-view {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.5rem;
}

#products-grid.list-view {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* Transición suave al alternar modos. */
#products-grid > .product-card-wrap { transition: all 0.3s ease; }

/* En modo lista la card se reorganiza horizontalmente y la imagen se acota. */
#products-grid.list-view .product-card-wrap {
    border-bottom: 1px solid rgba(201, 169, 110, 0.18);
    padding-bottom: 1rem;
}

#products-grid.list-view .product-card-link {
    display: flex;
    flex-direction: row;
    align-items: stretch;
    gap: 1.5rem;
}

#products-grid.list-view .product-card {
    flex-direction: row;
    align-items: stretch;
    gap: 1.5rem;
    width: 100%;
}

#products-grid.list-view .product-card .ratio {
    flex: 0 0 200px;
    max-width: 200px;
    width: 200px;
    flex-shrink: 0;
}

#products-grid.list-view .product-card .card-body {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding-right: 4rem; /* hueco para el botón de carrito */
}

#products-grid.list-view .product-card-wrap .product-card__cart-form {
    top: 50%;
    right: 0.75rem;
    transform: translateY(-50%);
}

/* --- Mobile-first: en <768px la lista se apila como cuadrícula visual. ------ */
@media (max-width: 767.98px) {
    .toolbar {
        flex-direction: column;
        align-items: stretch;
    }
    .search-wrapper {
        max-width: 100%;
    }
    .view-toggle {
        align-self: center;
    }
    #products-grid.list-view .product-card,
    #products-grid.list-view .product-card-link {
        flex-direction: column;
        gap: 0;
    }
    #products-grid.list-view .product-card .ratio {
        flex: 0 0 auto;
        width: 100%;
        max-width: 100%;
    }
    #products-grid.list-view .product-card .card-body {
        padding-right: 1rem;
    }
}

/* --- Empty state cuando el buscador no encuentra coincidencias. ------------- */
#no-results {
    text-align: center;
    padding: 3rem 1rem;
}
#no-results .bi {
    display: inline-block;
    font-size: 3rem;
    color: #adb5bd;
    margin-bottom: 1rem;
}
#no-results p {
    color: #6c757d;
    font-size: 1rem;
    margin: 0;
}

/* Página x de y, info opcional. */
.shop-catalog-pages-info { letter-spacing: 0.02em; }

/* Respeta a quien pide menos animación. */
@media (prefers-reduced-motion: reduce) {
    .search-count, #products-grid > .product-card-wrap, .view-toggle__btn,
    .search-wrapper > .bi-search, #product-search {
        transition: none;
        animation: none;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
/* Animación del botón "añadir al carrito" tras submit (no toca cart.js). */
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

/* =============================================================================
   /productos — Buscador en vivo + Toggle vista (grid/lista)
   Inline en la propia vista para evitar problemas de cache del bundle global.
   ============================================================================= */
(function nmzShopCatalog() {
    var grid = document.getElementById('products-grid');
    if (!grid) return; // si no hay catálogo (página vacía), no hacemos nada

    var input      = document.getElementById('product-search');
    var countEl    = document.getElementById('product-count');
    var emptyEl    = document.getElementById('no-results');
    var toggleBtns = document.querySelectorAll('[data-view]');
    // Filtramos el contenedor exterior (incluye link + botón de carrito).
    var cards      = Array.prototype.slice.call(grid.querySelectorAll('.product-card-wrap'));

    var useGsap = (typeof window.gsap !== 'undefined');
    var reduceMotion = (typeof window.matchMedia === 'function')
        && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---------- Helpers ---------- */

    function debounce(fn, ms) {
        var t = null;
        return function () {
            var args = arguments, ctx = this;
            clearTimeout(t);
            t = setTimeout(function () { fn.apply(ctx, args); }, ms);
        };
    }

    function updateCount(n) {
        if (!countEl) return;
        countEl.innerHTML = '<strong>' + n + '</strong>&nbsp;producto' + (n === 1 ? '' : 's');
        countEl.classList.remove('is-pulse');
        // Forzar reflow para reiniciar la animación CSS
        void countEl.offsetWidth;
        countEl.classList.add('is-pulse');
    }

    function setEmptyVisible(show) {
        if (!emptyEl) return;
        if (show) {
            emptyEl.style.display = 'block';
            // Refrescar AOS si ya pasó el observer inicial
            if (typeof window.AOS !== 'undefined' && typeof window.AOS.refreshHard === 'function') {
                window.AOS.refreshHard();
            }
            emptyEl.classList.add('aos-animate');
        } else {
            emptyEl.style.display = 'none';
            emptyEl.classList.remove('aos-animate');
        }
    }

    /* ---------- Filtrado ---------- */

    function applyFilter(rawQuery) {
        var q = (rawQuery || '').trim().toLowerCase();
        var visibleCount = 0;

        cards.forEach(function (card) {
            var name = card.getAttribute('data-name') || '';
            var isMatch = (q === '' || name.indexOf(q) !== -1);

            // Cancela tweens en curso (evita carreras fade-out → fade-in)
            if (useGsap) window.gsap.killTweensOf(card);

            if (isMatch) {
                if (useGsap && !reduceMotion) {
                    window.gsap.to(card, {
                        opacity: 1,
                        scale: 1,
                        display: 'block',
                        duration: 0.2
                    });
                } else {
                    card.style.display = '';
                    card.style.opacity = '';
                    card.style.transform = '';
                }
                visibleCount++;
            } else {
                if (useGsap && !reduceMotion) {
                    window.gsap.to(card, {
                        opacity: 0,
                        scale: 0.95,
                        duration: 0.2,
                        onComplete: function () { card.style.display = 'none'; }
                    });
                } else {
                    card.style.display = 'none';
                }
            }
        });

        updateCount(visibleCount);
        setEmptyVisible(visibleCount === 0);
    }

    if (input) {
        var debouncedFilter = debounce(function (val) { applyFilter(val); }, 250);
        input.addEventListener('input', function (e) {
            debouncedFilter(e.target.value);
        });
        // Tecla Escape limpia la búsqueda
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && input.value !== '') {
                input.value = '';
                applyFilter('');
            }
        });
    }

    /* ---------- Toggle Grid / Lista ---------- */

    var STORAGE_KEY = 'nmzShopView';

    function applyView(mode) {
        if (mode !== 'grid' && mode !== 'list') mode = 'grid';
        grid.classList.toggle('grid-view', mode === 'grid');
        grid.classList.toggle('list-view', mode === 'list');
        for (var i = 0; i < toggleBtns.length; i++) {
            var btn = toggleBtns[i];
            var active = (btn.getAttribute('data-view') === mode);
            btn.classList.toggle('active', active);
            btn.setAttribute('aria-pressed', active ? 'true' : 'false');
        }
        try { localStorage.setItem(STORAGE_KEY, mode); } catch (e) { /* storage bloqueado */ }
    }

    for (var i = 0; i < toggleBtns.length; i++) {
        (function (btn) {
            btn.addEventListener('click', function () {
                applyView(btn.getAttribute('data-view'));
                // Micro-fade del catálogo para suavizar el cambio de layout
                if (useGsap && !reduceMotion) {
                    window.gsap.fromTo(grid,
                        { opacity: 0.45 },
                        { opacity: 1, duration: 0.35, ease: 'power2.out', clearProps: 'opacity' }
                    );
                }
            });
        })(toggleBtns[i]);
    }

    // Restaurar preferencia previa al cargar la página
    var saved = 'grid';
    try { saved = localStorage.getItem(STORAGE_KEY) || 'grid'; } catch (e) { /* storage bloqueado */ }
    applyView(saved);
})();
</script>
<?= $this->endSection() ?>