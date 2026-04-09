<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/ARTE-EN-VIVO/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Arte en Vivo') ?>

<?= $this->section('content') ?>

<?php
$featuredImages = $featured_live_art_images ?? null;

$liveArtGalleryImages = $liveArtGalleryImages ?? [];
$galleryTotal         = count($liveArtGalleryImages);

/**
 * Mismo mosaico que Retratos (2×2 simétrico por cada 4 fotos).
 */
$liveMosaicCell = static function (int $idx, int $total): array {
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

$introImg = 'uploads/live-art/Las_bodas_de_Sabela.png';
if (! empty($featuredImages) && is_array($featuredImages)) {
    $first = $featuredImages[0];
    if (is_array($first)) {
        $introImg = $first['image'] ?? $first['url'] ?? $first['path'] ?? $introImg;
    } elseif (is_string($first) && $first !== '') {
        $introImg = $first;
    }
}
$heroBg = base_url('uploads/live-art/live art.JPG');
$introImgUrl = base_url($introImg);
?>

<!-- 1. Hero (60vh, título en MAYÚSCULAS) -->
<section class="page-hero page-hero--tall page-hero--liveart" style="background-image: url('<?= esc($heroBg, 'attr') ?>');">
    <div class="page-hero-overlay" style="background: linear-gradient(180deg, rgba(26,26,26,.45) 0%, rgba(26,26,26,.75) 100%);"></div>
    <div class="container page-hero-content text-center py-5">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Arte en vivo', 'url' => null],
            ],
            'nmzHeroTitle' => 'Arte en vivo',
        ]) ?>
        <div class="hero-cta">
            <a href="#reservar" class="btn btn-nmz">Reserva tu evento</a>
            <a href="#galeria" class="btn btn-nmz-outline" style="color: #fff; border-color: rgba(255,255,255,.6);">Ver galería</a>
        </div>
    </div>
</section>

<!-- 2. ¿Qué es el Arte en Vivo? — Card glassmorphism -->
<section class="section-padding" id="que-es">
    <div class="container">
        <div class="row align-items-stretch g-5 justify-content-center">
            <div class="col-lg-5 col-xl-4" data-aos="fade-right">
                <div class="glass-card glass-card--liveart">
                    <div class="glass-card__img-wrapper">
                        <img
                            src="<?= esc($introImgUrl, 'attr') ?>"
                            alt="Arte en vivo — retrato en directo"
                            class="glass-card__img"
                            loading="lazy" decoding="async"
                        >
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-xl-7 d-flex align-items-center" data-aos="fade-left">
                <div>
                    <h2 class="section-title text-start mb-4" style="text-align: left; font-family: var(--nmz-font-heading);">¿Qué es el Arte en Vivo?</h2>
                    <p class="about-text lead-nmz mb-3">
                        El arte en vivo consiste en pintar retratos durante tu celebración: invitados posan unos minutos
                        y se llevan un recuerdo original hecho en el momento.
                    </p>
                    <p class="about-text lead-nmz mb-4">
                        Es ideal para crear un punto de encuentro elegante y divertido en bodas, fiestas corporativas y
                        eventos especiales. Trabajo con materiales adaptados al espacio y al ritmo del evento, cuidando
                        la iluminación y la comodidad de cada persona.
                    </p>
                    <blockquote class="blockquote-nmz">
                        Una experiencia memorable y piezas únicas que reflejan la energía del día.
                    </blockquote>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. Cómo funciona — Cards glassmorphism suaves -->
<section class="section-padding bg-body-tertiary" id="proceso">
    <div class="container">
        <h2 class="section-title text-center">Cómo funciona</h2>
        <div class="row g-4 g-lg-5 justify-content-center">
            <?php
            $steps = [
                ['num' => '01', 'title' => 'Contacto',    'text' => 'Cuéntame fecha, lugar y tipo de celebración. Te respondo con disponibilidad y una propuesta adaptada.', 'icon' => 'bi-chat-dots'],
                ['num' => '02', 'title' => 'Preparación', 'text' => 'Adapto materiales, formato y estilo al espacio. Coordino horarios para que todo fluya con naturalidad.', 'icon' => 'bi-palette2'],
                ['num' => '03', 'title' => 'El evento',   'text' => 'Pinto retratos en directo mientras los invitados disfrutan. Un espectáculo artístico que atrapa miradas.', 'icon' => 'bi-brush'],
                ['num' => '04', 'title' => 'Recuerdos',   'text' => 'Cada invitado se lleva su retrato: una pieza única, personal y hecha con cariño en el momento.', 'icon' => 'bi-gift'],
            ];
            foreach ($steps as $si => $step) :
            ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?= $si * 100 ?>">
                <div class="glass-step-card">
                    <div class="glass-step-card__icon">
                        <i class="bi <?= $step['icon'] ?>" aria-hidden="true"></i>
                    </div>
                    <div class="glass-step-card__number"><?= $step['num'] ?></div>
                    <h3 class="glass-step-card__title"><?= esc($step['title']) ?></h3>
                    <p class="glass-step-card__text"><?= esc($step['text']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 4. Tipos de Evento — con particles.js de fondo -->
<section class="section-padding liveart-types-section" id="tipos">
    <div id="liveart-particles" class="liveart-types-section__particles"></div>
    <div class="container position-relative" style="z-index: 2;">
        <h2 class="section-title text-center" style="color: var(--nmz-white);">Tipos de evento</h2>
        <div class="row g-4 justify-content-center">
            <?php
            $eventTypes = [
                ['icon' => 'bi-heart-fill',         'title' => 'Bodas',            'desc' => 'Retratos en vivo para invitados y momentos únicos en tu gran día.'],
                ['icon' => 'bi-briefcase-fill',     'title' => 'Corporativos',     'desc' => 'Activaciones de marca, cenas de empresa y networking con arte.'],
                ['icon' => 'bi-balloon-fill',       'title' => 'Cumpleaños',       'desc' => 'Recuerdos pintados al instante que sorprenden a todos.'],
                ['icon' => 'bi-music-note-beamed',  'title' => 'Festivales',       'desc' => 'Ambiente creativo en festivales y ferias al aire libre.'],
                ['icon' => 'bi-house-heart-fill',   'title' => 'Eventos privados', 'desc' => 'Fiestas, comuniones y encuentros a medida.'],
            ];
            foreach ($eventTypes as $ei => $et) :
            ?>
            <div class="col-6 col-lg" data-aos="fade-up" data-aos-delay="<?= $ei * 80 ?>">
                <div class="glass-type-card">
                    <div class="glass-type-card__icon">
                        <i class="bi <?= $et['icon'] ?>" aria-hidden="true"></i>
                    </div>
                    <h3 class="glass-type-card__title"><?= esc($et['title']) ?></h3>
                    <p class="glass-type-card__text"><?= esc($et['desc']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 5. Galería — mismo mosaico que Retratos (carpeta uploads/live-art/fotosartenvivo) -->
<section class="section-padding ret-section--gallery" id="galeria">
    <div class="container">
        <h2 class="section-title text-center">Galería</h2>

        <?php if (empty($liveArtGalleryImages)) : ?>
        <p class="text-center text-muted mb-0">
            Las fotos se cargan desde <code>public/uploads/live-art/fotosartenvivo</code>
            (copia aquí el contenido de tu carpeta de imágenes).
        </p>
        <?php else : ?>
        <div class="ret-grid-gallery" id="liveArtGallery">
            <?php foreach ($liveArtGalleryImages as $idx => $gi) :
                $src    = base_url('uploads/live-art/fotosartenvivo/' . rawurlencode($gi['file']));
                $hidden = $idx >= $galleryVisible ? ' ret-grid-gallery__item--hidden' : '';
                $cell   = $liveMosaicCell($idx, $galleryTotal);
            ?>
            <a href="<?= esc($src, 'attr') ?>" class="glightbox ret-grid-gallery__item <?= esc($cell['mod']) ?><?= $hidden ?>" style="<?= esc($cell['style'], 'attr') ?>" data-gallery="arte-en-vivo" data-glightbox="title: <?= esc($gi['label'], 'attr') ?>">
                <img src="<?= esc($src, 'attr') ?>" alt="<?= esc($gi['label'], 'attr') ?>" loading="lazy" decoding="async">
            </a>
            <?php endforeach; ?>
        </div>

        <?php if (count($liveArtGalleryImages) > $galleryVisible) : ?>
        <div class="text-center mt-4">
            <button type="button" class="btn btn-outline-nmz" id="liveArtGalleryToggle" data-label-more="Ver todas las fotos" data-label-less="Ver menos">
                Ver todas las fotos <span class="ret-gallery-count">(<?= count($liveArtGalleryImages) ?>)</span>
            </button>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- 6. Testimonios — cards limpios -->
<section class="section-padding bg-body-tertiary" id="opiniones">
    <div class="container">
        <h2 class="section-title text-center mb-4">Lo que dicen</h2>
        <?php
        $liveartTestimonials = [
            ['name' => 'Carlos y Marta',         'text' => 'Contratar a Nahir para el arte en vivo de nuestra boda fue mágico. Los invitados quedaron fascinados viéndola pintar en directo mientras disfrutaban de la celebración. Un recuerdo que atesoraremos para siempre.', 'event' => 'Boda', 'initials' => 'CM'],
            ['name' => 'Restaurante Atlántico',   'text' => 'Nahir realizó arte en vivo durante nuestro evento de inauguración. Aportó un toque único y elegante que nuestros clientes todavía recuerdan semanas después.', 'event' => 'Evento corporativo', 'initials' => 'RA'],
            ['name' => 'Diego Fernández',          'text' => 'La experiencia fue increíble. Ver cómo Nahir captaba la esencia de cada invitado en minutos fue un espectáculo en sí mismo. Los retratos son pequeñas obras de arte.', 'event' => 'Cumpleaños', 'initials' => 'DF'],
        ];
        ?>
        <div class="row g-4 justify-content-center">
            <?php foreach ($liveartTestimonials as $ti => $t) : ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= $ti * 100 ?>">
                <div class="liveart-review-card">
                    <div class="liveart-review-card__header">
                        <div class="liveart-review-card__avatar"><?= esc($t['initials']) ?></div>
                        <div>
                            <cite class="liveart-review-card__name"><?= esc($t['name']) ?></cite>
                            <span class="liveart-review-card__event"><?= esc($t['event']) ?></span>
                        </div>
                    </div>
                    <div class="liveart-review-card__stars">★★★★★</div>
                    <blockquote class="liveart-review-card__text">"<?= esc($t['text']) ?>"</blockquote>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 7. Formulario de Reserva — fondo claro, limpio -->
<section class="section-padding" id="reservar">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <h2 class="section-title text-center mb-4">Reserva tu evento</h2>

                <div class="liveart-form-features mb-4">
                    <span class="liveart-form-feature"><i class="bi bi-check2-circle" aria-hidden="true"></i> Presupuesto sin compromiso</span>
                    <span class="liveart-form-feature"><i class="bi bi-check2-circle" aria-hidden="true"></i> Toda España</span>
                    <span class="liveart-form-feature"><i class="bi bi-check2-circle" aria-hidden="true"></i> Materiales incluidos</span>
                </div>

                <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success" role="alert"><?= esc(session()->getFlashdata('success')) ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
                <?php endif; ?>

                <form action="<?= esc(base_url('arte-en-vivo/reservar'), 'attr') ?>" method="post" class="liveart-form-clean">
                    <?= csrf_field() ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="contact_name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="contact_name" name="contact_name" value="<?= esc(old('contact_name') ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?= esc(old('contact_email') ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_phone" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="contact_phone" name="contact_phone" value="<?= esc(old('contact_phone') ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="event_type" class="form-label">Tipo de evento</label>
                            <select class="form-select" id="event_type" name="event_type" required>
                                <option value="" disabled <?= old('event_type') === null || old('event_type') === '' ? 'selected' : '' ?>>Selecciona…</option>
                                <option value="wedding" <?= old('event_type') === 'wedding' ? 'selected' : '' ?>>Boda</option>
                                <option value="corporate" <?= old('event_type') === 'corporate' ? 'selected' : '' ?>>Corporativo</option>
                                <option value="birthday" <?= old('event_type') === 'birthday' ? 'selected' : '' ?>>Cumpleaños</option>
                                <option value="festival" <?= old('event_type') === 'festival' ? 'selected' : '' ?>>Festival</option>
                                <option value="private" <?= old('event_type') === 'private' ? 'selected' : '' ?>>Evento privado</option>
                                <option value="other" <?= old('event_type') === 'other' ? 'selected' : '' ?>>Otro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="event_date" class="form-label">Fecha del evento</label>
                            <input type="date" class="form-control" id="event_date" name="event_date" value="<?= esc(old('event_date') ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="event_location" class="form-label">Lugar / espacio</label>
                            <input type="text" class="form-control" id="event_location" name="event_location" value="<?= esc(old('event_location') ?? '') ?>" placeholder="Nombre del venue">
                        </div>
                        <div class="col-md-6">
                            <label for="event_city" class="form-label">Ciudad</label>
                            <input type="text" class="form-control" id="event_city" name="event_city" value="<?= esc(old('event_city') ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="num_guests" class="form-label">Nº aprox. de invitados</label>
                            <input type="number" class="form-control" id="num_guests" name="num_guests" min="1" value="<?= esc(old('num_guests') ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label for="special_requirements" class="form-label">Requisitos o notas</label>
                            <textarea class="form-control" id="special_requirements" name="special_requirements" rows="3" placeholder="Horarios, estilo deseado…"><?= esc(old('special_requirements') ?? '') ?></textarea>
                        </div>
                        <div class="col-12 text-center pt-3">
                            <button type="submit" class="btn btn-nmz btn-lg px-5">
                                <i class="bi bi-send me-2" aria-hidden="true"></i>Enviar solicitud
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_css') ?>
<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
(function () {
    /* Hero GSAP */
    if (typeof gsap !== 'undefined') {
        var tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
        tl.from('.page-hero--liveart .nmz-page-hero__title', { opacity: 0, y: 40, duration: 0.9 })
          .from('.page-hero--liveart .hero-cta', { opacity: 0, y: 16, duration: 0.55 }, '-=0.35');
    }

    /* Particles.js en la sección de tipos de evento */
    if (typeof particlesJS !== 'undefined' && document.getElementById('liveart-particles') && window.nmzParticlesDefaultConfig) {
        var cfg = JSON.parse(JSON.stringify(window.nmzParticlesDefaultConfig));
        cfg.particles.number.value = 60;
        cfg.particles.color.value = '#c9a96e';
        cfg.particles.line_linked.color = '#c9a96e';
        cfg.particles.line_linked.opacity = 0.15;
        cfg.particles.opacity.value = 0.4;
        cfg.particles.move.speed = 1.8;
        particlesJS('liveart-particles', cfg);
    }

    /* Galería mosaico — ver más / menos + lightbox */
    var toggleBtn = document.getElementById('liveArtGalleryToggle');
    var gallery = document.getElementById('liveArtGallery');
    if (toggleBtn && gallery) {
        var expanded = false;
        var labelMore = toggleBtn.getAttribute('data-label-more') || 'Ver todas las fotos';
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

    if (typeof GLightbox !== 'undefined') {
        GLightbox({ selector: '.glightbox' });
    }
})();
</script>
<?= $this->endSection() ?>