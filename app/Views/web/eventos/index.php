<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/EVENTOS/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Eventos') ?>

<?= $this->section('content') ?>

<?php
$evAsset = static function (string $rel) : string {
    $path = FCPATH . $rel;
    $url  = base_url($rel);
    return is_file($path) ? $url . '?v=' . filemtime($path) : $url;
};

$heroEventosPath = FCPATH . 'uploads/eventos/hero-eventos.png';
$heroEventosUrl  = base_url('uploads/eventos/hero-eventos.png');
if (is_file($heroEventosPath)) {
    $heroEventosUrl .= '?v=' . filemtime($heroEventosPath);
}

// Galería papelería: imagen principal a la izquierda; el resto al pulsar «Ver más»
$papeleriaGalleryItems = [
    ['rel' => 'uploads/eventos/papeleria-01.png', 'alt' => 'Papelería artesanal: papeles, lacres y detalles en tonos crema y rosa'],
    ['rel' => 'uploads/eventos/papeleria-02.png', 'alt' => 'Papeles con borde deckle y texturas para invitaciones'],
    ['rel' => 'uploads/eventos/papeleria-03.png', 'alt' => 'Taco de papel hecho a mano con lazo'],
    ['rel' => 'uploads/eventos/papeleria-04.png', 'alt' => 'Sobres kraft y detalles florales para invitaciones'],
    ['rel' => 'uploads/eventos/papeleria-05.png', 'alt' => 'Sobres con inclusiones botánicas azules'],
    ['rel' => 'uploads/eventos/papeleria-06.png', 'alt' => 'Invitación de boda con acuarela y sobres'],
    ['rel' => 'uploads/eventos/papeleria-07.png', 'alt' => 'Invitación y conjunto de papelería nupcial'],
    ['rel' => 'uploads/eventos/papeleria-08.png', 'alt' => 'Papel texturizado y lazo para evento'],
    ['rel' => 'uploads/eventos/papeleria-09.png', 'alt' => 'Sobres cuadrados con cordel y flores secas'],
];
foreach ($papeleriaGalleryItems as $i => $row) {
    $papeleriaGalleryItems[$i]['url'] = is_file(FCPATH . $row['rel'])
        ? $evAsset($row['rel'])
        : base_url('assets/images/placeholder.webp');
}

$papeleriaHeroRel = 'uploads/eventos/papeleria-hero-principal.png';
$papeleriaHeroUrl = is_file(FCPATH . $papeleriaHeroRel)
    ? $evAsset($papeleriaHeroRel)
    : base_url('assets/images/placeholder.webp');
$papeleriaHeroAlt = 'Papelería artesanal: papeles texturizados, lacres, cintas y detalles en tonos crema y rosa empolvado';
$papeleriaMoreItems = $papeleriaGalleryItems;

$eventosServices = [
    [
        'icon' => 'bi-envelope-heart',
        'title' => 'Invitaciones y recordatorios',
        'text'  => 'Bautizos, comuniones, bodas… desde la invitación hasta el recordatorio, con el mismo cuidado estético en cada pieza.',
    ],
    [
        'icon' => 'bi-cup-straw',
        'title' => 'Minutas, meseros y seating',
        'text'  => 'Papelería de mesa y señalética para que los invitados se ubiquen con claridad y el conjunto luzca armónico.',
    ],
    [
        'icon' => 'bi-palette2',
        'title' => 'Todo personalizable',
        'text'  => 'Tipo de papel, ilustración, flor, sobre, cierre… el presupuesto se adapta a lo que elijas en cada detalle.',
    ],
];
?>

<section class="page-hero page-hero--studio-hub page-hero--bg-img-layer" data-aos="fade-in">
    <img
        class="page-hero-bg-img page-hero-bg-img--hub page-hero-bg-img--eventos"
        src="<?= esc($heroEventosUrl, 'attr') ?>"
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
                ['label' => 'Eventos', 'url' => null],
            ],
            'nmzHeroTitle' => 'Eventos',
        ]) ?>
        <div class="studio-hub__cta">
            <a href="<?= esc(base_url('contacto')) ?>" class="btn btn-nmz"><?= esc('Pedir presupuesto') ?></a>
            <a href="#papeleria-eventos" class="btn btn-nmz-outline brand-hero__btn-outline"><?= esc('Saber más') ?></a>
        </div>
    </div>
</section>

<section class="section-padding border-bottom border-light-subtle" id="papeleria-eventos">
    <div class="container">
        <div class="row g-4 g-lg-5 align-items-start align-items-lg-stretch eventos-papeleria-intro-row">
            <div class="col-lg-5 col-xl-4 order-lg-1 d-flex" data-aos="fade-right">
                <figure class="eventos-papeleria-spotlight mb-0 w-100 d-flex flex-column">
                    <a
                        href="<?= esc($papeleriaHeroUrl, 'attr') ?>"
                        class="glightbox eventos-papeleria-spotlight__link eventos-papeleria-spotlight__link--fill"
                        data-gallery="papeleria-eventos"
                        data-glightbox="title: <?= esc($papeleriaHeroAlt, 'attr') ?>"
                    >
                        <span class="eventos-papeleria-spotlight__mat" aria-hidden="true"></span>
                        <img
                            class="eventos-papeleria-spotlight__img"
                            src="<?= esc($papeleriaHeroUrl, 'attr') ?>"
                            alt="<?= esc($papeleriaHeroAlt, 'attr') ?>"
                            width="900"
                            height="1200"
                            loading="eager"
                            decoding="async"
                            fetchpriority="high"
                        >
                    </a>
                </figure>
            </div>
            <div class="col-lg-7 col-xl-8 order-lg-2" data-aos="fade-left">
                <h2 class="section-title text-start mb-4" style="text-align: left; font-family: var(--nmz-font-heading);"><?= esc('Papelería para tu evento') ?></h2>
                <p class="about-text lead-nmz mb-3">
                    <?= esc(
                        'Desde nmonzzon te ayudo con la papelería de tu evento: una de las partes más visibles y emotivas del día. '
                        . 'Invitaciones, recordatorios, minutas, meseros o seating, con un mismo criterio gráfico de principio a fin.'
                    ) ?>
                </p>
                <p class="about-text lead-nmz mb-3">
                    <?= esc(
                        'Bautizos, comuniones, bodas y otras celebraciones: el presupuesto depende del papel, acabados, sobre, cierre, ilustración o extras que elijas. '
                        . 'Cuéntame tu idea por redes (@nmonzzon), correo o WhatsApp y te respondo con orientación y presupuesto.'
                    ) ?>
                </p>
                <p class="about-text lead-nmz mb-0">
                    <?= esc(
                        'Coordinamos cada pieza para que encaje con el tono de tu fiesta y con la impresión o entrega que necesites.'
                    ) ?>
                </p>
                <div class="mt-4 d-flex flex-wrap align-items-center gap-3">
                    <a href="<?= esc(base_url('contacto')) ?>" class="btn btn-nmz-outline"><?= esc('Pedir presupuesto') ?></a>
                    <?php if ($papeleriaMoreItems !== []) : ?>
                    <button type="button" class="btn btn-outline-nmz" id="eventosPapeleriaGalleryToggle" aria-expanded="false" aria-controls="eventosPapeleriaMore" data-label-more="<?= esc('Ver más fotos abajo', 'attr') ?>" data-label-less="<?= esc('Ocultar fotos extra', 'attr') ?>">
                        <?= esc('Ver más fotos abajo') ?> <span class="text-muted small">(<?= esc((string) count($papeleriaMoreItems)) ?>)</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if ($papeleriaMoreItems !== []) : ?>
        <div class="eventos-papeleria-more" id="eventosPapeleriaMore" hidden aria-hidden="true">
            <div class="eventos-papeleria-more__head">
                <h3 class="eventos-papeleria-more__title"><?= esc('Más inspiración') ?></h3>
                <p class="eventos-papeleria-more__lead mb-0"><?= esc('Detalles, sobres y conjuntos que también puedes combinar para tu celebración.') ?></p>
            </div>
            <div class="eventos-papeleria-more-grid">
                <?php foreach ($papeleriaMoreItems as $item) : ?>
                <a
                    href="<?= esc($item['url'], 'attr') ?>"
                    class="glightbox eventos-papeleria-more__cell"
                    data-gallery="papeleria-eventos"
                    data-glightbox="title: <?= esc($item['alt'], 'attr') ?>"
                >
                    <span class="eventos-papeleria-more__card">
                        <img class="eventos-papeleria-more__img" src="<?= esc($item['url'], 'attr') ?>" alt="<?= esc($item['alt'], 'attr') ?>" loading="lazy" decoding="async" width="800" height="600">
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="section-padding bg-body-tertiary border-top border-bottom border-light-subtle">
    <div class="container">
        <h2 class="section-title text-center mb-5"><?= esc('Qué puedo hacer por tu celebración') ?></h2>
        <div class="row g-4 justify-content-center">
            <?php foreach ($eventosServices as $si => $svc) : ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= (int) $si * 80 ?>">
                <div class="eventos-service-card h-100">
                    <div class="eventos-service-card__icon" aria-hidden="true">
                        <i class="bi <?= esc($svc['icon']) ?>"></i>
                    </div>
                    <h3 class="eventos-service-card__title"><?= esc($svc['title']) ?></h3>
                    <p class="eventos-service-card__text"><?= esc($svc['text']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-padding" id="contacto-eventos">
    <div class="container">
        <div class="eventos-contact-panel text-center" data-aos="fade-up">
            <h2 class="section-title text-center mb-4"><?= esc('Hablemos de tu evento') ?></h2>
            <div class="eventos-contact-strip justify-content-center">
                <a href="https://www.instagram.com/nmonzzon/" class="eventos-contact-strip__link" rel="noopener noreferrer" target="_blank">
                    <i class="bi bi-instagram" aria-hidden="true"></i> @nmonzzon
                </a>
                <a href="mailto:nmonzzon@hotmail.com" class="eventos-contact-strip__link">
                    <i class="bi bi-envelope" aria-hidden="true"></i> nmonzzon@hotmail.com
                </a>
                <a href="https://wa.me/34623964677" class="eventos-contact-strip__link" rel="noopener noreferrer" target="_blank">
                    <i class="bi bi-whatsapp" aria-hidden="true"></i> 623 964 677
                </a>
            </div>
            <div class="text-center mt-4">
                <a href="<?= esc(base_url('contacto')) ?>" class="btn btn-nmz"><?= esc('Formulario de contacto') ?></a>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
(function () {
    var toggleBtn = document.getElementById('eventosPapeleriaGalleryToggle');
    var more = document.getElementById('eventosPapeleriaMore');
    if (!toggleBtn || !more) {
        return;
    }
    var expanded = false;
    var labelMore = toggleBtn.getAttribute('data-label-more') || 'Ver más fotos abajo';
    var labelLess = toggleBtn.getAttribute('data-label-less') || 'Ocultar fotos extra';
    var n = more.querySelectorAll('.eventos-papeleria-more__cell').length;
    toggleBtn.addEventListener('click', function () {
        expanded = !expanded;
        more.hidden = !expanded;
        more.setAttribute('aria-hidden', expanded ? 'false' : 'true');
        toggleBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        if (expanded) {
            toggleBtn.innerHTML = labelLess + ' <span class="text-muted small">(' + n + ')</span>';
            window.requestAnimationFrame(function () {
                more.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        } else {
            toggleBtn.innerHTML = labelMore + ' <span class="text-muted small">(' + n + ')</span>';
        }
    });
})();
</script>
<?= $this->endSection() ?>