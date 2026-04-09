<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/RETRATOS/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Retratos Personalizados') ?>

<?= $this->section('content') ?>

<?php
$styles = $styles ?? [];
$sizes  = $sizes ?? [];

$heroBg = base_url('uploads/retratos/clientes/' . rawurlencode('Yoli_Rodríguez_.jpg'));

$styleImageOverrides = [
    'blanco-y-negro-todo-detalle' => 'uploads/retratos/estilos/sandra_maceira.png',
    'figurin'                     => 'uploads/retratos/estilos/figurin_ejemplo.jpg',
    'a-linea'                     => 'uploads/retratos/estilos/elisa_goris_a_linea.jpg',
];

$sizesByType = ['print' => [], 'frame' => []];
foreach ($sizes as $sz) {
    $t = strtolower((string) ($sz['type'] ?? 'print'));
    if (! isset($sizesByType[$t])) {
        $sizesByType[$t] = [];
    }
    $sizesByType[$t][] = $sz;
}
$hasAnySizes = ! empty($sizesByType['print']) || ! empty($sizesByType['frame']);

$galleryImages = $galleryImages ?? [];
$galleryTotal  = count($galleryImages);

/**
 * Mosaico simétrico 2 columnas × 3 filas por cada 4 fotos (ancha | larga / larga | ancha) sin huecos.
 * Cola 1–3 fotos: también rellena el ancho sin dejar celdas vacías.
 */
$retMosaicCell = static function (int $idx, int $total): array {
    if ($total < 1) {
        return ['style' => '', 'mod' => 'ret-mosaic--wide'];
    }
    $rem       = $total % 4;
    $tailStart = $rem === 0 ? $total : ($total - $rem);

    if ($idx >= $tailStart && $rem > 0) {
        $local   = $idx - $tailStart;
        $rowBase = (int) (floor($tailStart / 4) * 3) + 1;

        if ($rem === 1) {
            return [
                'style' => 'grid-column:1 / span 2; grid-row:' . $rowBase . ';',
                'mod'   => 'ret-mosaic--wide',
            ];
        }
        if ($rem === 2) {
            $col = $local === 0 ? 1 : 2;

            return [
                'style' => 'grid-column:' . $col . '; grid-row:' . $rowBase . ';',
                'mod'   => 'ret-mosaic--wide',
            ];
        }
        // $rem === 3
        if ($local === 0) {
            return [
                'style' => 'grid-column:1 / span 2; grid-row:' . $rowBase . ';',
                'mod'   => 'ret-mosaic--wide',
            ];
        }

        return [
            'style' => 'grid-column:' . ($local === 1 ? 1 : 2) . '; grid-row:' . ($rowBase + 1) . ';',
            'mod'   => 'ret-mosaic--half',
        ];
    }

    $pos  = $idx % 4;
    $row0 = (int) (floor($idx / 4) * 3) + 1;

    if ($pos === 0) {
        return ['style' => 'grid-column:1; grid-row:' . $row0 . ';', 'mod' => 'ret-mosaic--wide'];
    }
    if ($pos === 1) {
        return ['style' => 'grid-column:2; grid-row:' . $row0 . ' / span 2;', 'mod' => 'ret-mosaic--tall'];
    }
    if ($pos === 2) {
        return ['style' => 'grid-column:1; grid-row:' . ($row0 + 1) . ' / span 2;', 'mod' => 'ret-mosaic--tall'];
    }

    return ['style' => 'grid-column:2; grid-row:' . ($row0 + 2) . ';', 'mod' => 'ret-mosaic--wide'];
};

$galleryVisible = 8;
?>

<!-- 1. Hero — editorial: solo título + CTA, sin subtítulo redundante -->
<section class="page-hero page-hero--retratos" style="background-image: url('<?= esc($heroBg, 'attr') ?>');">
    <div class="page-hero-overlay"></div>
    <div class="container page-hero-content text-center">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Retratos', 'url' => null],
            ],
            'nmzHeroTitle' => 'Retratos personalizados',
        ]) ?>
        <a href="<?= esc(base_url('retratos/configurador')) ?>" class="btn btn-nmz btn-lg ret-hero__cta">
            Configura tu retrato
        </a>
    </div>
</section>

<!-- 2. Cómo funciona — proceso en 3 pasos, horizontal en desktop -->
<section class="ret-section ret-section--process">
    <div class="container">
        <h2 class="section-title text-center">Cómo funciona</h2>
        <div class="ret-steps">
            <div class="ret-step" data-aos="fade-up">
                <span class="ret-step__num">01</span>
                <div class="ret-step__icon"><i class="bi bi-sliders" aria-hidden="true"></i></div>
                <h3 class="ret-step__title">Elige estilo y opciones</h3>
                <p class="ret-step__desc">Selecciona el estilo artístico, tamaño y si quieres marco. Ves el precio al instante.</p>
            </div>
            <div class="ret-step" data-aos="fade-up" data-aos-delay="100">
                <span class="ret-step__num">02</span>
                <div class="ret-step__icon"><i class="bi bi-camera" aria-hidden="true"></i></div>
                <h3 class="ret-step__title">Envía tu foto</h3>
                <p class="ret-step__desc">Sube la foto de referencia y las indicaciones desde tu área de cliente o por correo.</p>
            </div>
            <div class="ret-step" data-aos="fade-up" data-aos-delay="200">
                <span class="ret-step__num">03</span>
                <div class="ret-step__icon"><i class="bi bi-gift" aria-hidden="true"></i></div>
                <h3 class="ret-step__title">Recibe tu retrato</h3>
                <p class="ret-step__desc">Cada pieza lleva dedicación. Lo interpreto con calma para cuidar cada detalle.</p>
            </div>
        </div>
    </div>
</section>

<!-- 3. Estilos — carrusel en panel editorial (marquee suave, pausa al hover) -->
<section class="ret-section ret-section--styles" id="estilos">
    <div class="container">
        <h2 class="section-title text-center ret-styles-heading">Estilos disponibles</h2>
        <div class="ret-styles-showcase">
        <div class="ret-styles-carousel-wrap" aria-label="Estilos de retrato — carrusel automático">
        <div class="ret-styles-carousel" id="stylesCarousel">
            <?php foreach ($styles as $i => $style) :
                $slug = strtolower((string) ($style['slug'] ?? ''));
                if (isset($styleImageOverrides[$slug])) {
                    $img = base_url($styleImageOverrides[$slug]);
                } elseif (! empty($style['sample_image'])) {
                    $img = base_url($style['sample_image']);
                } else {
                    $img = base_url('uploads/retratos/clientes/Alba_Méndez.jpg');
                }
            ?>
            <article class="ret-style-card">
                <div class="ret-style-card__img">
                    <img src="<?= esc($img, 'attr') ?>" alt="<?= esc($style['name'] ?? 'Estilo', 'attr') ?>" loading="lazy" decoding="async">
                </div>
                <div class="ret-style-card__body">
                    <h3 class="ret-style-card__name"><?= esc($style['name'] ?? '') ?></h3>
                    <p class="ret-style-card__desc"><?= esc($style['description'] ?? '') ?></p>
                    <div class="ret-style-card__footer">
                        <span class="ret-style-card__price">Desde <?= number_format((float) ($style['base_price'] ?? 0), 2, ',', '.') ?> €</span>
                        <a href="<?= esc(base_url('retratos/configurador?estilo=' . (int) $style['id'])) ?>" class="btn btn-nmz-outline btn-sm ret-style-card__cta">Configurar</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        </div>
        </div>
    </div>
</section>

<!-- 4. Galería — rejilla bento + "ver más" (fotos en uploads/retratos/fotosretratos) -->
<section class="ret-section ret-section--gallery" id="galeria">
    <div class="container">
        <h2 class="section-title text-center">Retratos realizados</h2>

        <?php if (empty($galleryImages)) : ?>
        <p class="text-center text-muted mb-0">Las fotos de la galería se cargan desde la carpeta <code>public/uploads/retratos/fotosretratos</code> (copia aquí el contenido de tu carpeta de retratos).</p>
        <?php else : ?>
        <div class="ret-grid-gallery" id="retratosGallery">
            <?php foreach ($galleryImages as $idx => $gi) :
                $src    = base_url('uploads/retratos/fotosretratos/' . rawurlencode($gi['file']));
                $hidden = $idx >= $galleryVisible ? ' ret-grid-gallery__item--hidden' : '';
                $cell   = $retMosaicCell($idx, $galleryTotal);
            ?>
            <a href="<?= esc($src, 'attr') ?>" class="glightbox ret-grid-gallery__item <?= esc($cell['mod']) ?><?= $hidden ?>" style="<?= esc($cell['style'], 'attr') ?>" data-gallery="retratos" data-glightbox="title: <?= esc($gi['label'], 'attr') ?>">
                <img src="<?= esc($src, 'attr') ?>" alt="Retrato de <?= esc($gi['label'], 'attr') ?>" loading="lazy" decoding="async">
            </a>
            <?php endforeach; ?>
        </div>

        <?php if (count($galleryImages) > $galleryVisible) : ?>
        <div class="text-center mt-4">
            <button type="button" class="btn btn-outline-nmz" id="galleryToggle" data-label-more="Ver todos los retratos" data-label-less="Ver menos">
                Ver todos los retratos <span class="ret-gallery-count">(<?= count($galleryImages) ?>)</span>
            </button>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- 5. Precios y tamaños -->
<?php if ($hasAnySizes) : ?>
<section class="ret-section ret-section--prices" id="precios">
    <div class="container">
        <h2 class="section-title text-center">Tamaños y suplementos</h2>

        <div class="row g-4 justify-content-center">
            <?php
            $tableTitles = ['print' => 'Impresión', 'frame' => 'Marcos y enmarcado'];
            $tableIcons  = ['print' => 'bi-printer', 'frame' => 'bi-columns-gap'];
            foreach ($tableTitles as $typeKey => $typeLabel) :
                if (empty($sizesByType[$typeKey])) continue;
            ?>
            <div class="col-lg-6" data-aos="fade-up">
                <div class="ret-price-table">
                    <div class="ret-price-table__header">
                        <i class="bi <?= $tableIcons[$typeKey] ?>" aria-hidden="true"></i>
                        <h3><?= esc($typeLabel) ?></h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Dimensiones</th>
                                <th>Suplemento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sizesByType[$typeKey] as $row) : ?>
                            <tr>
                                <td><?= esc($row['name'] ?? '') ?></td>
                                <td><?= esc($row['dimensions'] ?? '') ?></td>
                                <td class="ret-price-table__amount">+<?= number_format((float) ($row['price_modifier'] ?? 0), 2, ',', '.') ?> €</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 6. FAQ + Contacto directo -->
<section class="ret-section ret-section--faq" id="faq">
    <div class="container">
        <div class="row g-5 justify-content-center">
            <div class="col-lg-7">
                <h2 class="section-title">Preguntas frecuentes</h2>
                <div class="accordion ret-faq" id="retFaq">
                    <div class="accordion-item">
                        <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">¿Cuánto tarda un encargo?</button></h3>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#retFaq"><div class="accordion-body">Depende del estilo y la complejidad, pero normalmente entre 2 y 4 semanas desde que recibo la foto de referencia. Siempre te informo del plazo estimado antes de empezar.</div></div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">¿Qué tipo de foto necesito enviar?</button></h3>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#retFaq"><div class="accordion-body">Una foto nítida con buena iluminación donde se vean bien los rasgos. Si tienes dudas, envíame varias opciones y te aconsejo cuál funciona mejor.</div></div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">¿Puedo pedir modificaciones?</button></h3>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#retFaq"><div class="accordion-body">Sí. Antes de dar el retrato por terminado te envío una vista previa para que puedas comentar ajustes. Pequeñas correcciones están incluidas en el precio.</div></div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">¿Cómo recibo el retrato?</button></h3>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#retFaq"><div class="accordion-body">Recibes el archivo digital en alta resolución por email. Si has elegido impresión o enmarcado, te lo envío por correo certificado protegido.</div></div>
                    </div>
                    <div class="accordion-item">
                        <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">¿Qué métodos de pago aceptáis?</button></h3>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#retFaq"><div class="accordion-body">Tarjeta de crédito/débito y transferencia bancaria a través de la pasarela segura de pago integrada en la web.</div></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-8">
                <div class="ret-contact-block">
                    <h3 class="ret-contact-block__title">¿Tienes dudas?</h3>
                    <p class="ret-contact-block__text">Escríbeme sin compromiso y te cuento todo lo que necesites saber.</p>

                    <div class="ret-contact-block__links">
                        <a href="https://wa.me/34623964677?text=Hola%20Nahir%2C%20me%20interesa%20un%20retrato%20personalizado" class="ret-contact-link ret-contact-link--wa" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-whatsapp" aria-hidden="true"></i>
                            <span>WhatsApp</span>
                        </a>
                        <a href="mailto:nmonzzon@hotmail.com?subject=Consulta%20retrato%20personalizado" class="ret-contact-link ret-contact-link--mail">
                            <i class="bi bi-envelope" aria-hidden="true"></i>
                            <span>nmonzzon@hotmail.com</span>
                        </a>
                        <a href="tel:+34623964677" class="ret-contact-link ret-contact-link--phone">
                            <i class="bi bi-telephone" aria-hidden="true"></i>
                            <span>623 964 677</span>
                        </a>
                        <a href="https://www.instagram.com/nmonzzon/" class="ret-contact-link ret-contact-link--ig" target="_blank" rel="noopener noreferrer">
                            <i class="bi bi-instagram" aria-hidden="true"></i>
                            <span>@nmonzzon</span>
                        </a>
                    </div>

                    <a href="<?= esc(base_url('contacto')) ?>" class="btn btn-nmz w-100 mt-3">
                        Ir al formulario de contacto
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_css') ?>
<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script src="<?= base_url('assets/js/retratos-carousel.js') ?>"></script>
<script>
(function () {
    /* Hero: from() deja opacity:0 hasta animar; fromTo + reduced motion evita CTA “invisible” */
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (typeof gsap !== 'undefined' && !reduceMotion) {
        gsap.fromTo(
            '.nmz-page-hero__title',
            { opacity: 0, y: 30 },
            { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out' }
        );
        gsap.fromTo(
            '.ret-hero__cta',
            { opacity: 0, y: 15 },
            { opacity: 1, y: 0, duration: 0.5, delay: 0.4, ease: 'power3.out' }
        );
    }

    /* Gallery — toggle hidden items */
    var toggleBtn = document.getElementById('galleryToggle');
    var gallery = document.getElementById('retratosGallery');
    if (toggleBtn && gallery) {
        var expanded = false;
        var labelMore = toggleBtn.getAttribute('data-label-more') || 'Ver todos los retratos';
        var labelLess = toggleBtn.getAttribute('data-label-less') || 'Ver menos';
        var total = gallery.querySelectorAll('.ret-grid-gallery__item').length;
        toggleBtn.addEventListener('click', function () {
            expanded = !expanded;
            gallery.classList.toggle('ret-grid-gallery--expanded', expanded);
            if (expanded) {
                toggleBtn.textContent = labelLess;
            } else {
                toggleBtn.innerHTML = labelMore + ' <span class="ret-gallery-count">(' + total + ')</span>';
            }
        });
    }

    /* GLightbox */
    if (typeof GLightbox !== 'undefined') {
        GLightbox({ selector: '.glightbox' });
    }
})();
</script>
<?= $this->endSection() ?>