<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/BRANDING/SHOW.PHP
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

$year = $project['year'] ?? null;
$yearLabel = $year !== null && $year !== '' ? (string) $year : '';

$serviceTags = branding_parse_service_tags($project['services'] ?? $project['services_provided'] ?? null);

$hasHero = $featuredUrl !== '';
?>

<?php if ($hasHero) : ?>
<section class="page-hero page-hero--tall page-hero--studio-hub page-hero--branding-detail" style="background-image: url('<?= esc($featuredUrl, 'attr') ?>');">
    <div class="page-hero-overlay page-hero-overlay--branding"></div>
    <div class="container page-hero-content text-center py-5">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Branding', 'url' => base_url('branding')],
                ['label' => $title, 'url' => null],
            ],
            'nmzHeroTitle' => $title,
        ]) ?>
        <div class="brand-detail-hero__meta text-white-75">
            <?php if (! empty($project['client_name'])) : ?>
                <span class="text-uppercase small letter-spacing-wide"><?= esc($project['client_name']) ?></span>
            <?php endif; ?>
            <?php if ($yearLabel !== '') : ?>
                <?php if (! empty($project['client_name'])) : ?>
                    <span class="mx-2" aria-hidden="true">·</span>
                <?php endif; ?>
                <span class="small"><?= esc($yearLabel) ?></span>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php else : ?>
<section class="brand-detail-topbar studio-hub-detail-bar studio-hub-detail-bar--branding">
    <div class="container py-3">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Branding', 'url' => base_url('branding')],
                ['label' => $title, 'url' => null],
            ],
            'nmzHeroTitle' => '',
            'nmzHeroCrumbsClass' => 'nmz-hero-crumbs--on-light',
        ]) ?>
    </div>
</section>
<?php endif; ?>

<section class="section-padding brand-detail-body">
    <div class="container">
        <a href="<?= esc(base_url('branding'), 'attr') ?>" class="brand-detail-back">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            <span><?= esc('Volver a Branding') ?></span>
        </a>

        <?php if (! $hasHero) : ?>
        <header class="brand-detail-header mb-4 text-center" data-aos="fade-up">
            <h1 class="nmz-page-hero__title nmz-page-hero__title--on-light mb-2"><?= esc($title) ?></h1>
            <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 text-secondary">
                <?php if (! empty($project['client_name'])) : ?>
                    <span class="text-uppercase small letter-spacing-wide"><?= esc($project['client_name']) ?></span>
                <?php endif; ?>
                <?php if ($yearLabel !== '') : ?>
                    <?php if (! empty($project['client_name'])) : ?>
                        <span class="text-muted" aria-hidden="true">·</span>
                    <?php endif; ?>
                    <span class="small"><?= esc($yearLabel) ?></span>
                <?php endif; ?>
            </div>
        </header>
        <?php endif; ?>

        <?php if (! empty($project['description'])) : ?>
            <div class="prose-nmz text-secondary mx-auto brand-detail-prose mb-5" data-aos="fade-up"><?= nl2br(esc((string) $project['description'])) ?></div>
        <?php endif; ?>

        <?php if ($serviceTags !== []) : ?>
            <div class="mb-5" data-aos="fade-up">
                <h2 class="brand-detail-label"><?= esc('Servicios en este proyecto') ?></h2>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach ($serviceTags as $tag) : ?>
                        <span class="brand-detail-tag"><?= esc($tag) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($galleryItems !== []) : ?>
            <h2 class="section-title text-center mb-4" data-aos="fade-up"><?= esc('Galería') ?></h2>
            <div class="brand-detail-gallery" data-aos="fade-up">
                <?php foreach ($galleryItems as $item) : ?>
                    <a
                        href="<?= esc($item['url'], 'attr') ?>"
                        class="glightbox brand-detail-gallery__item"
                        data-gallery="branding-project"
                        data-glightbox="title: <?= esc($title, 'attr') ?>"
                    >
                        <img
                            src="<?= esc($item['url'], 'attr') ?>"
                            alt="<?= esc($item['alt'], 'attr') ?>"
                            loading="lazy" decoding="async"
                            width="800"
                            height="600"
                        >
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-5 pt-3" data-aos="fade-up">
            <a href="<?= esc(base_url('contacto')) ?>" class="btn btn-nmz"><?= esc('Consultar un proyecto similar') ?></a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>