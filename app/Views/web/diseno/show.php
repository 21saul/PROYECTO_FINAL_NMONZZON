<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/DISENO/SHOW.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php
$project = $project ?? [];
$images  = $images ?? [];
$title   = (string) ($project['title'] ?? 'Proyecto');
?>

<?php $this->setVar('pageTitle', $title) ?>

<?= $this->section('content') ?>

<?php
$featured = (string) ($project['featured_image'] ?? '');
$featuredUrl = $featured !== '' && ! preg_match('#^https?://#i', $featured)
    ? base_url($featured)
    : $featured;

$galleryRows = $images;
usort($galleryRows, static function ($a, $b) {
    return ((int) ($a['sort_order'] ?? 0)) <=> ((int) ($b['sort_order'] ?? 0));
});

$galleryItems = [];
foreach ($galleryRows as $row) {
    $p = (string) ($row['image_url'] ?? $row['image_path'] ?? '');
    if ($p === '') {
        continue;
    }
    $galleryItems[] = [
        'url' => preg_match('#^https?://#i', $p) ? $p : base_url($p),
        'alt' => (string) ($row['alt_text'] ?? $title),
    ];
}
if ($galleryItems === [] && $featuredUrl !== '') {
    $galleryItems[] = ['url' => $featuredUrl, 'alt' => $title];
}

$projectTypeRaw = (string) ($project['design_type'] ?? $project['project_type'] ?? '');
$projectType    = $projectTypeRaw !== '' ? mb_convert_case($projectTypeRaw, MB_CASE_TITLE, 'UTF-8') : '';

$coverUrl = $featuredUrl !== '' ? $featuredUrl : ($galleryItems[0]['url'] ?? '');
$coverAlt = $title;

$heroBgRel  = 'assets/images/diseno/diseno-project-page-hero-bg.png';
$heroBgPath = FCPATH . $heroBgRel;
$pageHeroBg = base_url($heroBgRel);
if (is_file($heroBgPath)) {
    $pageHeroBg .= '?v=' . filemtime($heroBgPath);
}

$fullDesc = trim((string) ($project['description'] ?? ''));
$excerptLimit = 2000;
$descExcerpt = '';
$descHasMore = false;
$showBodyProse = false;
if ($fullDesc !== '') {
    $len = mb_strlen($fullDesc, 'UTF-8');
    if ($len <= $excerptLimit) {
        $descExcerpt = $fullDesc;
    } else {
        $descExcerpt = mb_substr($fullDesc, 0, $excerptLimit, 'UTF-8') . '…';
        $descHasMore = true;
        $showBodyProse = true;
    }
}

/* No repetir la portada en la rejilla si hay más imágenes; con una sola, se muestra también en galería */
$galleryForGrid = $galleryItems;
if ($coverUrl !== '' && isset($galleryItems[0]['url']) && $galleryItems[0]['url'] === $coverUrl && count($galleryItems) > 1) {
    $galleryForGrid = array_slice($galleryItems, 1);
}

$projectSlug       = (string) ($project['slug'] ?? '');
$galleryWideLayout = ($projectSlug === 'os-gatos-teatro-enxebre');
$isApalpador       = ($projectSlug === 'libro-apalpador-editorial');
$titleHeadingClass = 'diseno-project-page-title';
if ($isApalpador) {
    $titleHeadingClass .= ' diseno-project-page-title--apalpador';
} elseif (mb_strlen($title, 'UTF-8') > 40) {
    $titleHeadingClass .= ' diseno-project-page-title--long';
}

/* Apalpador: foto izquierda (más estrecha) + texto derecha con más aire útil; separación horizontal mínima */
$mediaColClass = $isApalpador ? 'col-4 col-lg-5' : 'col-lg-5';
$textColClass  = $isApalpador ? 'col-8 col-lg-7' : 'col-lg-7';
$rowMainClass  = 'row align-items-start mt-2 mt-lg-3 diseno-project-page-top__main'
    . ($isApalpador ? ' gx-2 gx-lg-3 gy-3 diseno-project-page-top__main--apalpador' : ' g-4 g-xl-5');
?>

<div class="diseno-page<?= $galleryWideLayout ? ' diseno-page--project-gallery-wide' : '' ?><?= $isApalpador ? ' diseno-page--apalpador' : '' ?>">

<section class="diseno-project-page-top">
    <?php if (is_file($heroBgPath)) : ?>
    <div class="diseno-project-page-top__banner" aria-hidden="true">
        <img
            class="diseno-project-page-top__banner-img"
            src="<?= esc($pageHeroBg, 'attr') ?>"
            alt=""
            width="1920"
            height="480"
            loading="eager"
            decoding="async"
            fetchpriority="high"
        >
        <div class="diseno-project-page-top__banner-scrim diseno-project-page-top__banner-scrim--pastel"></div>
    </div>
    <?php endif; ?>

    <div class="diseno-project-page-top__wrap">
    <div class="container py-4 py-lg-5">
        <div class="diseno-project-page-top__toolbar">
            <div class="diseno-project-page-top__nav">
                <?= view('partials/nmz-hero-heading', [
                    'nmzHeroCrumbs' => [
                        ['label' => 'Inicio', 'url' => base_url('/')],
                        ['label' => 'Diseño', 'url' => base_url('diseno')],
                        ['label' => $title, 'url' => null],
                    ],
                    'nmzHeroTitle' => '',
                    'nmzHeroCrumbsClass' => 'nmz-hero-crumbs--on-light nmz-hero-crumbs--align-start',
                ]) ?>
            </div>
            <a href="<?= esc(base_url('diseno'), 'attr') ?>" class="diseno-project-page__back-link">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                <?= esc('Volver a Diseño') ?>
            </a>
        </div>

        <div class="<?= esc($rowMainClass, 'attr') ?>">
            <div class="<?= esc($mediaColClass, 'attr') ?> diseno-project-page-top__media-col">
                <?php if ($coverUrl !== '') : ?>
                <figure class="diseno-project-page-cover mb-0">
                    <div class="diseno-project-page-cover__frame">
                        <img
                            class="diseno-project-page-cover__img"
                            src="<?= esc($coverUrl, 'attr') ?>"
                            alt="<?= esc($coverAlt, 'attr') ?>"
                            width="800"
                            height="640"
                            loading="eager"
                            decoding="async"
                            fetchpriority="high"
                            sizes="(max-width: 991px) 100vw, 42vw"
                        >
                    </div>
                </figure>
                <?php else : ?>
                <div class="diseno-project-page-cover diseno-project-page-cover--empty" aria-hidden="true">
                    <div class="diseno-project-page-cover__frame diseno-project-page-cover__frame--placeholder"></div>
                </div>
                <?php endif; ?>
            </div>
            <div class="<?= esc($textColClass, 'attr') ?> diseno-project-page-top__text-col">
                <?php if ($projectType !== '') : ?>
                <span class="diseno-project-page-top__badge"><?= esc($projectType) ?></span>
                <?php endif; ?>
                <h1 class="<?= esc($titleHeadingClass, 'attr') ?>"><?= esc($title) ?></h1>
                <?php if (! empty($project['client_name'])) : ?>
                <p class="diseno-project-page-top__client">
                    <i class="bi bi-building" aria-hidden="true"></i>
                    <span><?= esc($project['client_name']) ?></span>
                </p>
                <?php endif; ?>

                <?php if ($fullDesc !== '') : ?>
                <div class="diseno-project-page-top__excerpt">
                    <p class="diseno-project-page-top__excerpt-label">
                        <i class="bi bi-journal-text" aria-hidden="true"></i>
                        <?= esc('Sobre el proyecto') ?>
                    </p>
                    <p class="diseno-project-page-top__excerpt-text"><?= nl2br(esc($descExcerpt)) ?></p>
                    <?php if ($descHasMore) : ?>
                    <a href="#texto-proyecto" class="diseno-project-page-top__read-more">
                        <?= esc('Seguir leyendo') ?>
                        <i class="bi bi-arrow-down-short" aria-hidden="true"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    </div>
</section>

<section class="diseno-project-page-body">
    <div class="container">
        <?php if ($showBodyProse) : ?>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div id="texto-proyecto" class="diseno-project-page-prose" data-aos="fade-up">
                    <?= nl2br(esc($fullDesc)) ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($galleryForGrid !== []) : ?>
        <div class="diseno-project-page-gallery-wrap" data-aos="fade-up">
            <div class="diseno-project-page-gallery-board">
                <header class="diseno-project-page-gallery-head">
                    <h2 class="diseno-project-page-gallery-heading">
                        <i class="bi bi-images" aria-hidden="true"></i>
                        <?= esc('Galería') ?>
                    </h2>
                    <p class="diseno-project-page-gallery-lead">
                        <i class="bi bi-arrows-angle-expand" aria-hidden="true"></i>
                        <?= esc('Pulsa una imagen para ampliarla.') ?>
                    </p>
                </header>
                <div class="diseno-project-page-gallery">
                    <?php foreach ($galleryForGrid as $item) : ?>
                    <a
                        href="<?= esc($item['url'], 'attr') ?>"
                        class="glightbox diseno-project-page-gallery__link"
                        data-gallery="diseno-project"
                        data-glightbox="title: <?= esc($title, 'attr') ?>"
                    >
                        <span class="diseno-project-page-gallery__cell">
                            <img
                                src="<?= esc($item['url'], 'attr') ?>"
                                alt="<?= esc($item['alt'], 'attr') ?>"
                                class="diseno-project-page-gallery__img"
                                loading="lazy"
                                decoding="async"
                                width="900"
                                height="675"
                            >
                            <span class="diseno-project-page-gallery__hint" aria-hidden="true">
                                <i class="bi bi-arrows-angle-expand"></i>
                            </span>
                        </span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <p class="diseno-project-page-footer-nav text-center mb-0 pt-4" data-aos="fade-up">
            <a href="<?= esc(base_url('diseno'), 'attr') ?>" class="diseno-project-page__back-link diseno-project-page__back-link--footer">
                <i class="bi bi-arrow-left" aria-hidden="true"></i>
                <?= esc('Volver al listado de proyectos') ?>
            </a>
        </p>
    </div>
</section>

</div>

<?= $this->endSection() ?>