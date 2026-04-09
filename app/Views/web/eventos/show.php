<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/EVENTOS/SHOW.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php
$event = $event ?? [];
$images = $images ?? [];
$title = (string) ($event['title'] ?? 'Evento');
?>

<?php $this->setVar('pageTitle', $title) ?>

<?= $this->section('content') ?>

<?php
$featured = (string) ($event['featured_image'] ?? '');
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

$meses = [
    1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
    7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
];

$eventDate = isset($event['event_date']) ? (string) $event['event_date'] : '';
$dateLabel = '';
if ($eventDate !== '') {
    $ts = strtotime($eventDate);
    if ($ts !== false) {
        $n = (int) date('n', $ts);
        $mes = $meses[$n] ?? date('F', $ts);
        $dateLabel = date('j', $ts) . ' de ' . $mes . ' de ' . date('Y', $ts);
    } else {
        $dateLabel = $eventDate;
    }
}
$location = (string) ($event['location'] ?? '');

$eventHeroUrl = $featuredUrl !== '' ? $featuredUrl : base_url('uploads/eventos/hero-eventos.png');
?>

<section class="page-hero page-hero--studio-hub page-hero--bg-img-layer">
    <img
        class="page-hero-bg-img page-hero-bg-img--hub page-hero-bg-img--eventos"
        src="<?= esc($eventHeroUrl, 'attr') ?>"
        alt=""
        width="1920"
        height="1080"
        sizes="100vw"
        fetchpriority="high"
        decoding="async"
    >
    <div class="page-hero-overlay page-hero-overlay--studio-eventos"></div>
    <div class="container page-hero-content py-5 text-center">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Eventos', 'url' => base_url('eventos')],
                ['label' => $title, 'url' => null],
            ],
            'nmzHeroTitle' => $title,
        ]) ?>
        <div class="d-flex flex-wrap flex-column flex-sm-row gap-sm-3 justify-content-center text-white-75 mt-3">
            <?php if ($dateLabel !== '') : ?>
                <span><i class="bi bi-calendar-event me-2" aria-hidden="true"></i><?= esc($dateLabel) ?></span>
            <?php endif; ?>
            <?php if ($location !== '') : ?>
                <span><i class="bi bi-geo-alt me-2" aria-hidden="true"></i><?= esc($location) ?></span>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <a href="<?= esc(base_url('eventos'), 'attr') ?>" class="btn btn-nmz-outline mb-4">
            <i class="bi bi-arrow-left me-2" aria-hidden="true"></i><?= esc('Volver a Eventos') ?>
        </a>

        <?php if (! empty($event['description'])) : ?>
            <div class="prose-nmz text-secondary mb-5" data-aos="fade-up"><?= nl2br(esc((string) $event['description'])) ?></div>
        <?php endif; ?>

        <?php if ($galleryItems !== []) : ?>
            <div class="gallery-grid" data-aos="fade-up">
                <?php foreach ($galleryItems as $item) : ?>
                    <a
                        href="<?= esc($item['url'], 'attr') ?>"
                        class="glightbox rounded-3 overflow-hidden shadow-sm"
                        data-gallery="evento-detail"
                        data-glightbox="title: <?= esc($title, 'attr') ?>"
                    >
                        <img
                            src="<?= esc($item['url'], 'attr') ?>"
                            alt="<?= esc($item['alt'], 'attr') ?>"
                            class="rounded-3"
                            loading="lazy" decoding="async"
                            width="800"
                            height="600"
                        >
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>