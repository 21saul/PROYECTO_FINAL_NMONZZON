<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/DISENO/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Diseño') ?>

<?= $this->section('content') ?>

<?php
$projects = $projects ?? [];

$placeholderUrl = base_url('assets/images/placeholder.svg');

/**
 * URL pública a un archivo bajo public/ (uploads/...). Codifica segmentos (espacios, tildes).
 */
$resolveUpload = static function (string $rel) use ($placeholderUrl): string {
    $rel = str_replace('\\', '/', ltrim($rel, '/'));
    if ($rel === '' || str_contains($rel, '..') || ! str_starts_with($rel, 'uploads/')) {
        return $placeholderUrl;
    }
    $path = FCPATH . $rel;
    if (! is_file($path)) {
        return $placeholderUrl;
    }
    $parts = explode('/', $rel);
    $enc   = implode('/', array_map(static fn (string $p): string => rawurlencode($p), $parts));

    return base_url($enc) . '?v=' . filemtime($path);
};

$heroBgRel = 'assets/images/diseno/diseno-landing-hero-bg.png';
$heroBgPath = FCPATH . $heroBgRel;
$heroBg = base_url($heroBgRel);
if (is_file($heroBgPath)) {
    $heroBg .= '?v=' . filemtime($heroBgPath);
}

$projectsWithSlug = [];
foreach ($projects as $p) {
    if (! empty($p['slug'])) {
        $projectsWithSlug[] = $p;
    }
}

$disenoScope = [
    [
        'n'     => '01',
        'icon'  => 'bi-easel2',
        'title' => 'Cartelería',
        'line'  => 'Carteles, flyers y piezas para conciertos, teatro o campañas que necesiten un mensaje visual claro y legible a distancia.',
    ],
    [
        'n'     => '02',
        'icon'  => 'bi-bag-heart',
        'title' => 'Diseño textil',
        'line'  => 'Gráficos para camisetas, tote bags y merchandising: archivos listos para imprenta o proveedor, con mimo en tipografía y color.',
    ],
    [
        'n'     => '03',
        'icon'  => 'bi-disc',
        'title' => 'Cover art',
        'line'  => 'Portadas y arte para discos o singles: coherencia con el sonido, el nombre del proyecto y el uso en digital e impreso.',
    ],
];
?>

<div class="diseno-page">

<section class="page-hero page-hero--diseno-landing" style="background-image: url('<?= esc($heroBg, 'attr') ?>');">
    <div class="page-hero-overlay"></div>
    <div class="container page-hero-content text-center">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Diseño', 'url' => null],
            ],
            'nmzHeroTitle' => 'Diseño',
        ]) ?>
        <?php if ($projectsWithSlug !== []) : ?>
        <a href="#proyectos" class="btn btn-nmz btn-lg ret-hero__cta"><?= esc('Ver proyectos') ?></a>
        <?php else : ?>
        <a href="<?= esc(base_url('contacto')) ?>" class="btn btn-nmz btn-lg ret-hero__cta"><?= esc('Encargar diseño') ?></a>
        <?php endif; ?>
    </div>
</section>

<section class="diseno-section diseno-section--editorial" id="sobre-diseno">
    <div class="container container--diseno-narrow">
        <div class="diseno-editorial-panel" data-aos="fade-up">
        <header class="diseno-section-head diseno-section-head--left diseno-editorial__masthead mb-0 pb-4 border-0">
            <span class="diseno-section-head__label"><?= esc('Cómo trabajo') ?></span>
            <h2 class="diseno-editorial__headline"><?= esc('Ideas con forma') ?></h2>
            <div class="diseno-editorial__text-block">
                <p class="diseno-editorial__lede mb-0">
                    <?= esc(
                        'Música, teatro, marcas y encargos personales: mensaje ordenado, archivos listos para impresión o pantalla, y presupuesto claro desde el principio.'
                    ) ?>
                </p>
                <p class="diseno-editorial__intro diseno-editorial__intro--with-lede mb-0" data-aos="fade-up">
                    <?= esc(
                        'Las fichas de proyecto reúnen el contexto y la galería. Si aún no hay uno que encaje con lo que buscas, escríbeme y lo vemos.'
                    ) ?>
                </p>
            </div>
        </header>

        <div class="diseno-editorial__body">
            <div class="row diseno-services g-4 g-lg-4" data-aos="fade-up" data-aos-delay="40">
                <?php foreach ($disenoScope as $block) : ?>
                <div class="col-md-4">
                    <article class="diseno-service-card h-100">
                        <div class="diseno-service-card__head">
                            <span class="diseno-service-card__n" aria-hidden="true"><?= esc($block['n']) ?></span>
                            <span class="diseno-service-card__icon" aria-hidden="true">
                                <i class="bi <?= esc($block['icon']) ?>"></i>
                            </span>
                        </div>
                        <h3 class="diseno-service-card__title"><?= esc($block['title']) ?></h3>
                        <p class="diseno-service-card__text mb-0"><?= esc($block['line']) ?></p>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="diseno-editorial__actions" data-aos="fade-up" data-aos-delay="80">
                <?php if ($projectsWithSlug !== []) : ?>
                <a href="#proyectos" class="btn btn-nmz"><?= esc('Ver proyectos') ?></a>
                <?php endif; ?>
                <a href="<?= esc(base_url('contacto')) ?>" class="btn btn-nmz-outline"><?= esc('Contacto') ?></a>
            </div>
        </div>
        </div>
    </div>
</section>

<section class="diseno-section diseno-section--projects" id="proyectos">
    <div class="container">
        <header class="diseno-section-head diseno-section-head--projects-tight" data-aos="fade-up">
            <span class="diseno-section-head__label"><?= esc('Portfolio') ?></span>
            <h2 class="diseno-section-head__title diseno-section-head__title--section"><?= esc('Proyectos realizados') ?></h2>
        </header>

        <?php if ($projectsWithSlug === []) : ?>
        <div class="diseno-projects-empty text-center py-5 px-3" data-aos="fade-up">
            <p class="diseno-projects-empty__text mb-4"><?= esc('Pronto publicaré nuevas fichas de proyecto. Mientras tanto, puedes escribirme con tu idea.') ?></p>
            <a href="<?= esc(base_url('contacto')) ?>" class="btn btn-nmz"><?= esc('Ir a contacto') ?></a>
        </div>
        <?php else : ?>
        <div class="diseno-projects-board" data-aos="fade-up" data-aos-delay="40">
            <div class="row g-4 g-lg-4">
                <?php foreach ($projectsWithSlug as $i => $project) :
                    $feat = (string) ($project['featured_image'] ?? '');
                    $img  = $feat !== '' && ! preg_match('#^https?://#i', $feat)
                        ? base_url($feat)
                        : ($feat !== '' ? $feat : $placeholderUrl);
                    $slug = (string) $project['slug'];
                    $delay       = ($i % 9) * 80;
                    $projectTypeRaw = (string) ($project['design_type'] ?? $project['project_type'] ?? '');
                    $projectType    = $projectTypeRaw !== '' ? mb_convert_case($projectTypeRaw, MB_CASE_TITLE, 'UTF-8') : '';
                    ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= (int) $delay ?>">
                    <a href="<?= esc(base_url('diseno/' . $slug), 'attr') ?>" class="diseno-project-card">
                        <div class="diseno-project-card__media ratio ratio-4x3">
                            <img
                                class="diseno-project-card__img"
                                src="<?= esc($img, 'attr') ?>"
                                alt="<?= esc($project['title'] ?? '', 'attr') ?>"
                                loading="lazy"
                                decoding="async"
                                width="800"
                                height="600"
                            >
                        </div>
                        <div class="diseno-project-card__body">
                            <?php if ($projectType !== '') : ?>
                            <span class="diseno-project-card__type"><?= esc($projectType) ?></span>
                            <?php endif; ?>
                            <h3 class="diseno-project-card__title"><?= esc($project['title'] ?? '') ?></h3>
                            <?php if (! empty($project['client_name'])) : ?>
                            <p class="diseno-project-card__client"><?= esc($project['client_name']) ?></p>
                            <?php endif; ?>
                            <span class="diseno-project-card__cta">
                                <?= esc('Ver proyecto') ?>
                                <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
                            </span>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="diseno-section diseno-section--cta diseno-cta-band" id="contacto-diseno">
    <div class="container">
        <div class="diseno-cta-band__panel" data-aos="fade-up">
            <div class="diseno-cta-band__text">
                <h2 class="diseno-cta-band__title"><?= esc('¿Siguiente proyecto?') ?></h2>
                <p class="diseno-cta-band__lead">
                    <?= esc('Escríbeme por Instagram, correo o WhatsApp y te respondo con orientación y presupuesto.') ?>
                </p>
            </div>
            <div class="diseno-cta-band__links">
                <a href="https://www.instagram.com/nmonzzon/" class="diseno-cta-band__contact" rel="noopener noreferrer" target="_blank">
                    <i class="bi bi-instagram" aria-hidden="true"></i> @nmonzzon
                </a>
                <a href="mailto:nmonzzon@hotmail.com" class="diseno-cta-band__contact">
                    <i class="bi bi-envelope" aria-hidden="true"></i> nmonzzon@hotmail.com
                </a>
                <a href="https://wa.me/34623964677" class="diseno-cta-band__contact" rel="noopener noreferrer" target="_blank">
                    <i class="bi bi-whatsapp" aria-hidden="true"></i> 623 964 677
                </a>
            </div>
            <div class="diseno-cta-band__action">
                <a href="<?= esc(base_url('contacto')) ?>" class="btn btn-nmz"><?= esc('Formulario de contacto') ?></a>
            </div>
        </div>
    </div>
</section>

</div>

<?= $this->endSection() ?>