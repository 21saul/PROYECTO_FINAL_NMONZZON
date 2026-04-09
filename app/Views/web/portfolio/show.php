<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/PORTFOLIO/SHOW.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php
$work          = $work ?? [];
$relatedWorks  = $relatedWorks ?? [];
$title         = (string) ($work['title'] ?? 'Obra');
$slug          = (string) ($work['slug'] ?? '');
$imgPath       = (string) ($work['image'] ?? $work['image_url'] ?? '');
$imgUrl        = $imgPath !== '' && ! preg_match('#^https?://#i', $imgPath)
    ? base_url($imgPath)
    : $imgPath;
if ($imgUrl === '') {
    $imgUrl = base_url('assets/images/placeholder.webp');
}
$styleTag   = (string) ($work['style_tag'] ?? '');
$desc       = (string) ($work['description'] ?? '');
$technique  = (string) ($work['technique'] ?? '');
$dimensions = (string) ($work['dimensions'] ?? '');
$year       = isset($work['year']) && $work['year'] !== '' && $work['year'] !== null
    ? (string) $work['year']
    : '';
?>

<?php $this->setVar('pageTitle', $title) ?>

<?= $this->section('content') ?>

<section
    class="page-hero page-hero--tall"
    style="background-image: url('<?= esc($imgUrl, 'attr') ?>');"
>
    <div class="page-hero-overlay" style="background: linear-gradient(180deg, rgba(26,26,26,.55) 0%, rgba(26,26,26,.85) 100%);"></div>
    <div class="container page-hero-content text-center py-5">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Portfolio', 'url' => base_url('portfolio')],
                ['label' => $title, 'url' => null],
            ],
            'nmzHeroTitle' => $title,
        ]) ?>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row g-5 align-items-start">
            <div class="col-lg-7" data-aos="fade-right">
                <a
                    href="<?= esc($imgUrl, 'attr') ?>"
                    class="glightbox d-block rounded-3 overflow-hidden shadow-sm"
                    data-gallery="portfolio-detail"
                    data-glightbox="title: <?= esc($title, 'attr') ?>"
                >
                    <img
                        src="<?= esc($imgUrl, 'attr') ?>"
                        alt="<?= esc($title, 'attr') ?>"
                        class="w-100"
                        loading="lazy" decoding="async"
                        width="1200"
                        height="900"
                    >
                </a>
            </div>
            <div class="col-lg-5" data-aos="fade-left">
                <header class="mb-4">
                    <h1 class="font-heading display-5 mb-3"><?= esc($title) ?></h1>
                    <?php if ($styleTag !== '') : ?>
                        <span class="badge rounded-pill bg-light text-dark border px-3 py-2 fw-normal"><?= esc($styleTag) ?></span>
                    <?php endif; ?>
                </header>
                <?php if ($desc !== '') : ?>
                    <div class="prose-nmz text-secondary mb-4"><?= nl2br(esc($desc)) ?></div>
                <?php endif; ?>
                <?php if ($technique !== '' || $dimensions !== '' || $year !== '') : ?>
                    <dl class="row small text-secondary mb-0">
                        <?php if ($technique !== '') : ?>
                            <dt class="col-sm-4 fw-semibold text-dark"><?= esc('Técnica') ?></dt>
                            <dd class="col-sm-8"><?= esc($technique) ?></dd>
                        <?php endif; ?>
                        <?php if ($dimensions !== '') : ?>
                            <dt class="col-sm-4 fw-semibold text-dark"><?= esc('Dimensiones') ?></dt>
                            <dd class="col-sm-8"><?= esc($dimensions) ?></dd>
                        <?php endif; ?>
                        <?php if ($year !== '') : ?>
                            <dt class="col-sm-4 fw-semibold text-dark"><?= esc('Año') ?></dt>
                            <dd class="col-sm-8"><?= esc($year) ?></dd>
                        <?php endif; ?>
                    </dl>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($relatedWorks !== []) : ?>
        <section class="mt-5 pt-5 border-top">
            <h2 class="font-heading h3 mb-4" data-aos="fade-up"><?= esc('Obras relacionadas') ?></h2>
            <div class="row g-4">
                <?php foreach ($relatedWorks as $j => $rel) :
                    $relSlug = (string) ($rel['slug'] ?? '');
                    if ($relSlug === '' || $relSlug === $slug) {
                        continue;
                    }
                    $relTitle = (string) ($rel['title'] ?? '');
                    $relPath  = (string) ($rel['image'] ?? $rel['image_url'] ?? '');
                    $relUrl   = $relPath !== '' && ! preg_match('#^https?://#i', $relPath)
                        ? base_url($relPath)
                        : $relPath;
                    if ($relUrl === '') {
                        $relUrl = base_url('assets/images/placeholder.webp');
                    }
                    $rDelay = ($j % 6) * 80;
                    ?>
                <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="<?= (int) $rDelay ?>">
                    <a href="<?= esc(base_url('portfolio/' . $relSlug), 'attr') ?>" class="text-decoration-none d-block">
                        <div class="ratio ratio-1x1 rounded-3 overflow-hidden shadow-sm mb-2">
                            <img
                                src="<?= esc($relUrl, 'attr') ?>"
                                alt="<?= esc($relTitle, 'attr') ?>"
                                class="object-fit-cover w-100 h-100"
                                loading="lazy" decoding="async"
                                width="400"
                                height="400"
                            >
                        </div>
                        <span class="text-dark small fw-medium"><?= esc($relTitle) ?></span>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <div class="mt-5">
            <a href="<?= esc(base_url('portfolio'), 'attr') ?>" class="btn btn-nmz-outline">
                <i class="bi bi-arrow-left me-2" aria-hidden="true"></i><?= esc('Volver al portfolio') ?>
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>