<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/BRANDING/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Branding') ?>

<?= $this->section('content') ?>

<?php
$heroBeeFile = 'Abeja dorada, fondo beige.jpg';
$heroBeePath = FCPATH . 'uploads/branding/' . $heroBeeFile;
$heroBg      = base_url('uploads/branding/' . rawurlencode($heroBeeFile));
if (is_file($heroBeePath)) {
    $heroBg .= '?v=' . filemtime($heroBeePath);
}

$v = static function (string $rel) : string {
    $path = FCPATH . $rel;
    $url  = base_url($rel);
    return is_file($path) ? $url . '?v=' . filemtime($path) : $url;
};

$introImg = $v('uploads/branding/brand-showcase-matcha.png');

/* Casos reales (6 tarjetas) — imágenes en uploads/branding/brand-showcase-*.png */
$brandShowcase = [
    [
        'tag'   => 'Marca personal',
        'title' => 'nmonzzon · identidad',
        'desc'  => 'Logotipo personal con abeja en acabado dorado y entorno editorial. Piezas para papelería y redes, con la misma calidez artesanal del estudio.',
        'img'   => $v('uploads/branding/brand-showcase-nmonzzon.png'),
        'pills' => ['Logotipo', 'Sello circular', 'Textura papel'],
    ],
    [
        'tag'   => 'Packaging',
        'title' => 'Nuba Matcha',
        'desc'  => 'Identidad para marca de matcha: torii, tipografía serif y patrón envolvente en latas. Estética japonesa minimal y premium.',
        'img'   => $v('uploads/branding/brand-showcase-matcha.png'),
        'pills' => ['Naming visual', 'Packaging', 'Patrón'],
    ],
    [
        'tag'   => 'Monograma',
        'title' => 'FG',
        'desc'  => 'Monograma serif enmarcado con ilustración de manos a lápiz / grabado. Versiones en positivo, negativo y sello circular.',
        'img'   => $v('uploads/branding/brand-showcase-fg.jpg'),
        'pills' => ['Monograma', 'Ilustración', 'Variantes'],
    ],
    [
        'tag'   => 'Retail local',
        'title' => 'Valle Fragoso Floristería',
        'desc'  => 'Sello circular con tipografía curva, detalle botánico lineal y paleta verde salvia. Tradición desde 1963, lectura clara en etiquetas y escaparate.',
        'img'   => $v('uploads/branding/brand-showcase-valle-fragoso.png'),
        'pills' => ['Logo circular', 'Retail', 'Clásico'],
    ],
    [
        'tag'   => 'Lifestyle',
        'title' => 'Kairos',
        'desc'  => 'Wordmark serif con detalles orgánicos integrados en las letras. Línea fina y tono tierra sobre fondo oscuro.',
        'img'   => $v('uploads/branding/brand-showcase-kairos.png'),
        'pills' => ['Wordmark', 'Custom type', 'Mood'],
    ],
    [
        'tag'   => 'Aplicaciones',
        'title' => 'Mockups y textil',
        'desc'  => 'Llevo el diseño a prendas, tote bags y presentaciones digitales: mockups realistas para que veas la marca en contexto real.',
        'img'   => $v('uploads/branding/brand-showcase-mockups.png'),
        'pills' => ['Mockups', 'Ropa', 'Presentación'],
    ],
];

/* Tarifas alineadas con la web de referencia (logotipo · manual de identidad) */
$brandPlans = [
    [
        'name'  => 'Opción 1 · Creación de logo',
        'price' => '300',
        'items' => [
            'Briefing y referencias',
            'Análisis de marca y proceso creativo',
            '1 propuesta de diseño',
            'Tres revisiones',
            'Entrega: vectorial, PDF, PNG y JPG',
        ],
    ],
    [
        'name'  => 'Opción 2 · Manual de identidad básico',
        'price' => '700',
        'items' => [
            'Briefing, análisis y propuestas iniciales',
            'Tres revisiones',
            'Logotipo y variantes · archivos finales',
            'Brandbook básico: logo, tipografías, paleta, usos correctos e incorrectos',
        ],
    ],
    [
        'name'  => 'Opción 3 · Manual de identidad completo',
        'price' => '1.000',
        'featured' => true,
        'items' => [
            'Historia, misión, visión y valores',
            'Logotipo, versiones, iconografía y reglas de uso',
            'Paleta, tipografía, espacio de seguridad y fondos',
            'Moodboard y muestra de mockups básicos (desarrollo aparte)',
        ],
    ],
];

$brandPillars = [
    ['icon' => 'bi-chat-text',        'title' => 'Briefing',            'text' => 'Escucho referencias, público y tono para acotar el proyecto.'],
    ['icon' => 'bi-vector-pen',       'title' => 'Propuesta',           'text' => 'Exploraciones alineadas con lo que te diferencia.'],
    ['icon' => 'bi-journal-bookmark', 'title' => 'Manual y entregables', 'text' => 'Normas claras y archivos listos para imprenta y web.'],
];
?>

<section class="page-hero page-hero--tall page-hero--studio-hub page-hero--branding" style="background-image: url('<?= esc($heroBg, 'attr') ?>');">
    <div class="page-hero-overlay page-hero-overlay--branding"></div>
    <div class="container page-hero-content text-center py-5">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Branding', 'url' => null],
            ],
            'nmzHeroTitle'    => 'Branding',
            'nmzHeroSubtitle' => 'Identidad · Logotipos · Manuales',
        ]) ?>
        <div class="brand-hero__cta">
            <a href="#casos" class="btn btn-nmz">Ver casos</a>
            <a href="<?= esc(base_url('contacto')) ?>" class="btn btn-nmz-outline brand-hero__btn-outline">Pedir presupuesto</a>
        </div>
    </div>
</section>

<section class="section-padding brand-section">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-md-5 col-lg-4" data-aos="fade-right">
                <figure class="brand-intro-visual mb-0">
                    <img
                        src="<?= esc($introImg, 'attr') ?>"
                        alt="NUBA Matcha — identidad visual y packaging"
                        width="800"
                        height="800"
                        loading="lazy"
                        decoding="async"
                        class="w-100"
                    >
                </figure>
            </div>
            <div class="col-md-7 col-lg-7" data-aos="fade-left">
                <h2 class="section-title text-start font-heading brand-section--intro__title">Marca con criterio</h2>
                <p class="about-text lead-nmz mb-3">
                    Diseño de logotipo y manuales de identidad pensados para que tu proyecto se reconozca al instante
                    y se mantenga coherente en web, redes e impresión — el mismo enfoque que en la web de referencia de nmonzzon.
                </p>
                <p class="about-text lead-nmz mb-0">
                    Tres niveles de encargo (solo logo, manual básico o manual completo) para adaptarme a la fase en la que estés.
                    Cuéntame tu idea y te indico la opción que mejor encaja.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="brand-showcase-section section-padding" id="casos">
    <div class="container">
        <h2 class="section-title text-center brand-showcase-section__title">Casos y referencias</h2>
        <div class="row g-4">
            <?php foreach ($brandShowcase as $i => $item) : ?>
            <div class="col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="<?= ($i % 6) * 60 ?>">
                <article class="brand-showcase-card h-100">
                    <div class="brand-showcase-card__media">
                        <img src="<?= esc($item['img'], 'attr') ?>" alt="<?= esc($item['title'], 'attr') ?>" loading="lazy" decoding="async">
                    </div>
                    <div class="brand-showcase-card__body">
                        <span class="brand-showcase-card__tag"><?= esc($item['tag']) ?></span>
                        <h3 class="brand-showcase-card__title font-heading"><?= esc($item['title']) ?></h3>
                        <p class="brand-showcase-card__desc"><?= esc($item['desc']) ?></p>
                        <ul class="brand-showcase-card__pills">
                            <?php foreach ($item['pills'] as $pill) : ?>
                            <li><?= esc($pill) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="brand-pricing-section section-padding" id="tarifas">
    <div class="container">
        <h2 class="section-title text-center brand-pricing-section__title">Tarifas orientativas</h2>
        <div class="row g-4 justify-content-center">
            <?php foreach ($brandPlans as $pi => $plan) : ?>
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="<?= $pi * 80 ?>">
                <div class="brand-price-card h-100<?= ! empty($plan['featured']) ? ' brand-price-card--featured' : '' ?>">
                    <?php if (! empty($plan['featured'])) : ?>
                    <span class="brand-price-card__ribbon">Completo</span>
                    <?php endif; ?>
                    <h3 class="brand-price-card__name font-heading"><?= esc($plan['name']) ?></h3>
                    <p class="brand-price-card__price">
                        <span class="brand-price-card__amount"><?= esc($plan['price']) ?></span>
                        <span class="brand-price-card__currency">€</span>
                        <span class="brand-price-card__vat">+ IVA</span>
                    </p>
                    <ul class="brand-price-card__list">
                        <?php foreach ($plan['items'] as $line) : ?>
                        <li><?= esc($line) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?= esc(base_url('contacto')) ?>" class="btn btn-nmz w-100 mt-auto">Consultar esta opción</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-padding bg-body-tertiary brand-section brand-section--pillars">
    <div class="container">
        <h2 class="section-title text-center">Cómo trabajo contigo</h2>
        <div class="row g-4 justify-content-center">
            <?php foreach ($brandPillars as $pi => $pillar) : ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= (int) $pi * 80 ?>">
                <div class="brand-pillar h-100">
                    <div class="brand-pillar__icon" aria-hidden="true">
                        <i class="bi <?= esc($pillar['icon']) ?>"></i>
                    </div>
                    <h3 class="brand-pillar__title"><?= esc($pillar['title']) ?></h3>
                    <p class="brand-pillar__text"><?= esc($pillar['text']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-padding brand-cta-section nmz-cta-panel-section nmz-cta-panel-section--branding">
    <div class="container">
        <div class="nmz-cta-panel nmz-cta-panel--branding">
            <span class="nmz-cta-panel__icon-badge nmz-cta-panel__icon-badge--branding" aria-hidden="true">
                <i class="bi bi-palette2"></i>
            </span>
            <p class="nmz-cta-panel__hint"><?= esc('¿Logo, manual o piezas sueltas? Cuéntame tu marca y te respondo con el siguiente paso.') ?></p>
            <a href="<?= esc(base_url('contacto')) ?>" class="btn btn-nmz btn-lg nmz-cta-panel__btn">
                Contactar
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>