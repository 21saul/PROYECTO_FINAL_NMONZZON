<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/PORTFOLIO/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Portfolio') ?>

<?= $this->section('content') ?>

<?php
$works                 = $works ?? [];
$categories            = $categories ?? [];
$pager                 = $pager ?? null;
$selected_category_slug = $selected_category_slug ?? null;
$noFilter              = $selected_category_slug === null || $selected_category_slug === '';
?>

<section
    class="page-hero page-hero--tall"
    style="background-color: var(--nmz-black); min-height: 280px;"
    data-aos="fade-in"
>
    <div class="page-hero-overlay" style="background: rgba(26,26,26,0.5);"></div>
    <div class="container page-hero-content py-5 text-center">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Portfolio', 'url' => null],
            ],
            'nmzHeroTitle' => 'Portfolio',
        ]) ?>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="d-flex flex-nowrap flex-md-wrap justify-content-start justify-content-md-center gap-2 mb-5 overflow-x-auto pb-2" data-aos="fade-up" style="-webkit-overflow-scrolling: touch;">
            <a
                href="<?= esc(base_url('portfolio'), 'attr') ?>"
                class="btn btn-sm rounded-pill <?= $noFilter ? 'btn-nmz' : 'btn-nmz-outline' ?>"
            ><?= esc('Todos') ?></a>
            <?php foreach ($categories as $cat) :
                $slug = (string) ($cat['slug'] ?? '');
                if ($slug === '') {
                    continue;
                }
                $label = (string) ($cat['name'] ?? $cat['title'] ?? $slug);
                $isActive = ! $noFilter && (string) $selected_category_slug === $slug;
                ?>
            <a
                href="<?= esc(base_url('portfolio') . '?category=' . rawurlencode($slug), 'attr') ?>"
                class="btn btn-sm rounded-pill <?= $isActive ? 'btn-nmz' : 'btn-nmz-outline' ?>"
            ><?= esc($label) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if ($works === []) : ?>
            <p class="text-center text-secondary mb-0" data-aos="fade-up"><?= esc('No se encontraron obras en esta categoría.') ?></p>
        <?php else : ?>
            <div class="portfolio-masonry">
                <div class="portfolio-sizer"></div>
                <?php foreach ($works as $i => $work) :
                    $imgPath = (string) ($work['image'] ?? $work['image_url'] ?? '');
                    $imgUrl  = $imgPath !== '' && ! preg_match('#^https?://#i', $imgPath)
                        ? base_url($imgPath)
                        : $imgPath;
                    if ($imgUrl === '') {
                        $imgUrl = base_url('assets/images/placeholder.webp');
                    }
                    $slug = (string) ($work['slug'] ?? '');
                    if ($slug === '') {
                        continue;
                    }
                    $title    = (string) ($work['title'] ?? '');
                    $styleTag = (string) ($work['style_tag'] ?? '');
                    $delay    = ($i % 9) * 80;
                    $sizeMod  = (($i + 1) % 3 === 0) ? 'portfolio-item--lg' : 'portfolio-item--sm';
                    ?>
                <div class="portfolio-item <?= esc($sizeMod, 'attr') ?>" data-aos="fade-up" data-aos-delay="<?= (int) $delay ?>">
                    <a
                        href="<?= esc($imgUrl, 'attr') ?>"
                        class="glightbox portfolio-item-inner rounded-3 shadow-sm d-block text-decoration-none"
                        data-gallery="portfolio"
                        data-glightbox="title: <?= esc($title, 'attr') ?>"
                    >
                        <img
                            src="<?= esc($imgUrl, 'attr') ?>"
                            alt="<?= esc($title, 'attr') ?>"
                            loading="lazy" decoding="async"
                            width="800"
                            height="600"
                        >
                        <div class="portfolio-item-overlay">
                            <h4><?= esc($title) ?></h4>
                            <?php if ($styleTag !== '') : ?>
                                <span class="badge rounded-pill bg-white text-dark border shadow-sm fw-normal small mt-2"><?= esc($styleTag) ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <nav class="d-flex justify-content-center mt-5 pt-3" aria-label="<?= esc('Paginación', 'attr') ?>">
            <?= ($pager ?? null)?->links() ?? '' ?>
        </nav>
    </div>
</section>

<?= $this->endSection() ?>