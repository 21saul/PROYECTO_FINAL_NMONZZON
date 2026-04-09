<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/PRODUCTOS/SHOW.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', $product['name'] ?? 'Producto') ?>

<?= $this->section('content') ?>

<?php
$product = $product ?? [];
$images = $images ?? [];
$variants = $variants ?? [];
$relatedProducts = $relatedProducts ?? $related ?? [];

$name = $product['name'] ?? '';
$slug = $product['slug'] ?? '';
$description = $product['description'] ?? '';
$price = (float) ($product['price'] ?? 0);
$pid = (int) ($product['id'] ?? 0);
$stock = $pid > 0 ? \App\Libraries\CartService::catalogQuickAddStock($pid) : 0;
$mainImage = $product['featured_image'] ?? $product['image'] ?? null;

$gallery = [];
if ($mainImage) {
    $gallery[] = $mainImage;
}
foreach ($images as $imgRow) {
    $path = is_array($imgRow) ? ($imgRow['path'] ?? $imgRow['image'] ?? $imgRow['url'] ?? null) : $imgRow;
    if ($path && $path !== $mainImage) {
        $gallery[] = $path;
    }
}
$gallery = array_values(array_unique(array_filter($gallery)));
$primarySrc = base_url($gallery[0] ?? 'assets/images/placeholder.webp');

$relatedSlice = array_slice($relatedProducts, 0, 4);

$detailHeroBg = $primarySrc;
?>

<section
    class="page-hero page-hero--tall page-hero--product-detail"
    style="background-image: url('<?= esc($detailHeroBg, 'attr') ?>');"
>
    <div class="page-hero-overlay" style="background: linear-gradient(180deg, rgba(26,26,26,.55) 0%, rgba(26,26,26,.82) 100%);"></div>
    <div class="container page-hero-content text-center py-5">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Tienda', 'url' => base_url('productos')],
                ['label' => $name, 'url' => null],
            ],
            'nmzHeroTitle' => $name,
        ]) ?>
    </div>
</section>

<!-- Product detail -->
<section class="section-padding">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-6">
                <div class="ratio ratio-1x1 bg-light rounded-3 overflow-hidden mb-3">
                    <a href="<?= esc($primarySrc, 'attr') ?>" class="glightbox d-block w-100 h-100" data-gallery="product-detail" data-glightbox="title: <?= esc($name, 'attr') ?>">
                        <img src="<?= esc($primarySrc, 'attr') ?>" alt="<?= esc($name) ?>" class="object-fit-cover w-100 h-100" loading="eager" decoding="async">
                    </a>
                </div>
                <?php if (count($gallery) > 1) : ?>
                <div class="row g-2">
                    <?php foreach ($gallery as $gi => $path) :
                        $thumbUrl = base_url($path);
                        ?>
                    <div class="col-6 col-sm-4 col-md-3">
                        <a href="<?= esc($thumbUrl, 'attr') ?>" class="glightbox ratio ratio-1x1 d-block rounded-2 overflow-hidden border<?= $gi === 0 ? ' border-dark' : '' ?>" data-gallery="product-detail" data-glightbox="title: <?= esc($name, 'attr') ?>">
                            <img src="<?= esc($thumbUrl, 'attr') ?>" alt="" class="object-fit-cover w-100 h-100" loading="lazy" decoding="async">
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <h1 class="font-heading display-5 mb-3"><?= esc($name) ?></h1>
                <p class="price-amount display-6 fw-semibold mb-4"><?= number_format($price, 2, ',', '.') ?> €</p>

                <?php if ($description !== '') : ?>
                <div class="prose-nmz text-secondary mb-4"><?= nl2br(esc($description)) ?></div>
                <?php endif; ?>

                <?php if ($stock <= 0) : ?>
                <p class="text-danger fw-semibold mb-4">Agotado</p>
                <?php else : ?>
                <p class="text-success small mb-4">En stock (<?= $stock ?> disponibles)</p>
                <?php endif; ?>

                <form action="<?= esc(base_url('carrito/add'), 'attr') ?>" method="post" class="add-to-cart-form card border-0 shadow-sm p-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= esc((string) ($product['id'] ?? ''), 'attr') ?>">

                    <?php if (! empty($variants)) : ?>
                    <div class="mb-3">
                        <label for="variant_id" class="form-label">Opción</label>
                        <select class="form-select" id="variant_id" name="variant_id" required>
                            <?php foreach ($variants as $v) :
                                $vid = $v['id'] ?? null;
                                if ($vid === null) {
                                    continue;
                                }
                                $vlabel = $v['name'] ?? $v['label'] ?? $v['title'] ?? ('#' . $vid);
                                ?>
                            <option value="<?= esc((string) $vid, 'attr') ?>"><?= esc($vlabel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else : ?>
                    <input type="hidden" name="variant_id" value="">
                    <?php endif; ?>

                    <div class="mb-4">
                        <label for="quantity" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="<?= min(10, max(1, $stock)) ?>" value="1"<?= $stock <= 0 ? ' disabled' : '' ?> required>
                    </div>

                    <button type="submit" class="btn btn-nmz btn-lg w-100"<?= $stock <= 0 ? ' disabled' : '' ?>><i class="bi bi-cart-plus me-2" aria-hidden="true"></i>Añadir al carrito</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Related -->
<?php if ($relatedSlice !== []) : ?>
<section class="section-padding bg-body-tertiary border-top">
    <div class="container">
        <h2 class="section-title text-center mb-5">También te puede interesar</h2>
        <div class="row g-4">
            <?php foreach ($relatedSlice as $ri => $rp) :
                $rslug = $rp['slug'] ?? '';
                $rhref = base_url('productos/' . $rslug);
                $rimg = $rp['featured_image'] ?? $rp['image'] ?? null;
                $rst = isset($rp['stock']) ? (int) $rp['stock'] : 1;
                ?>
            <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="<?= ($ri % 4) * 80 ?>">
                <a href="<?= esc($rhref, 'attr') ?>" class="text-decoration-none text-body d-block h-100">
                    <article class="card h-100 border-0 shadow-sm product-card overflow-hidden rounded-3">
                        <img
                            src="<?= esc(base_url($rimg ?? 'assets/images/placeholder.webp'), 'attr') ?>"
                            class="card-img-top"
                            alt="<?= esc($rp['name'] ?? '') ?>"
                            loading="lazy" decoding="async"
                        >
                        <div class="card-body">
                            <h3 class="h6 product-name font-heading mb-2"><?= esc($rp['name'] ?? '') ?></h3>
                            <p class="price-amount fw-semibold mb-0"><?= number_format((float) ($rp['price'] ?? 0), 2, ',', '.') ?> €</p>
                            <?php if ($rst <= 0) : ?>
                            <span class="badge bg-secondary mt-2">Agotado</span>
                            <?php endif; ?>
                        </div>
                    </article>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>