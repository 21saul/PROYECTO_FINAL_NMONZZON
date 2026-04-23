<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/HOME.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- 1. Hero Section -->
<section class="hero-section" id="hero"
         style="background-image: url('<?= base_url('uploads/site/hero-inicio.jpg') ?>');
                background-size: cover;
                background-position: center center;
                background-repeat: no-repeat;">
    <div class="hero-overlay"></div>
    <div class="hero-content min-w-0 px-3">
        <h1 class="nmz-page-hero__title">nmonzzon studio</h1>
        <div class="hero-cta">
            <a href="#servicios" class="btn btn-nmz">Descubre mis servicios</a>
        </div>
    </div>
</section>

<!-- 2. Escaparate de Servicios -->
<section class="section-padding" id="servicios">
    <div class="container">
        <h2 class="section-title text-center">Servicios</h2>

        <div class="svc-grid">
            <?php
            $services = [
                ['title' => 'Retratos',     'tag' => 'Personalizado', 'image' => 'uploads/site/carousel-retratos.png',  'href' => '/retratos',
                 'desc' => 'Retratos de personas y mascotas hechos a mano que capturan miradas y emociones.',
                 'pills' => ['Personas y familias', 'Mascotas', 'A partir de foto']],
                ['title' => 'Arte en Vivo',  'tag' => 'Experiencia',  'image' => 'uploads/site/carousel-liveart.jpg',   'href' => '/arte-en-vivo',
                 'card_class' => 'svc-card--liveart',
                 'desc' => 'Pintura en directo en bodas y eventos: un espectáculo que deja un cuadro irrepetible.',
                 'pills' => ['Bodas', 'Eventos corporativos', 'Celebraciones']],
                ['title' => 'Branding',      'tag' => 'Identidad',   'image' => 'uploads/site/carousel-branding.jpg',  'href' => '/branding',
                 'desc' => 'Identidad visual para tu marca: logotipo, paleta y piezas gráficas a medida.',
                 'pills' => ['Logotipo', 'Paleta y tipografía', 'Manual de marca']],
                ['title' => 'Eventos',       'tag' => 'Celebración',  'image' => 'uploads/site/carousel-eventos.jpg',   'href' => '/eventos',
                 'desc' => 'Papelería y detalles artísticos para celebraciones: invitaciones, meseros y más.',
                 'pills' => ['Papelería nupcial', 'Detalles de mesa', 'Cartelería']],
                ['title' => 'Diseño',        'tag' => 'Ilustración',  'image' => 'uploads/site/carousel-diseno.png',    'href' => '/diseno',
                 'desc' => 'Ilustración y diseño gráfico a medida: láminas, editorial y arte con estilo propio.',
                 'pills' => ['Ilustración digital', 'Láminas', 'Editorial']],
                ['title' => 'Productos',     'tag' => 'Shop',         'image' => 'uploads/site/carousel-productos.png', 'href' => '/productos',
                 'desc' => 'Láminas, tazas, velas y merchandising artístico para regalar o disfrutar.',
                 'pills' => ['Láminas', 'Tazas y velas', 'Regalos']],
            ];
            foreach ($services as $si => $svc) :
                $svcImgUrl  = base_url($svc['image']);
                $svcImgPath = FCPATH . $svc['image'];
                if (is_file($svcImgPath)) {
                    $svcImgUrl .= '?v=' . filemtime($svcImgPath);
                }
            ?>
            <a href="<?= esc(base_url(ltrim($svc['href'], '/'))) ?>" class="svc-card<?= ! empty($svc['card_class']) ? ' ' . esc($svc['card_class'], 'attr') : '' ?>" data-aos="fade-up" data-aos-delay="<?= ($si % 3) * 80 ?>">
                <div class="svc-card__img">
                    <img src="<?= esc($svcImgUrl, 'attr') ?>" alt="<?= esc($svc['title']) ?>" loading="lazy" decoding="async">
                </div>
                <div class="svc-card__body">
                    <h3 class="svc-card__title"><?= esc($svc['title']) ?></h3>
                    <span class="svc-card__tag"><?= esc($svc['tag']) ?></span>
                    <p class="svc-card__desc"><?= esc($svc['desc']) ?></p>
                    <ul class="svc-card__pills">
                        <?php foreach ($svc['pills'] as $pill) : ?>
                        <li><?= esc($pill) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <span class="svc-card__link">Ver más <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 3. About Section -->
<?php
$aboutInicioPath = FCPATH . 'uploads/site/about-inicio-sobre-mi.jpg';
$aboutInicioUrl  = base_url('uploads/site/about-inicio-sobre-mi.jpg');
if (is_file($aboutInicioPath)) {
    $aboutInicioUrl .= '?v=' . filemtime($aboutInicioPath);
}
?>
<section class="section-padding" id="sobre-mi">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 col-xl-4 about-photo-col" data-aos="fade-right">
                <div class="about-photo rounded-3 overflow-hidden shadow">
                    <img src="<?= esc($aboutInicioUrl, 'attr') ?>"
                         alt="Nahir Álvarez Monzón en su mesa de trabajo al aire libre con rotuladores y acuarelas — nmonzzon studio"
                         class="img-fluid w-100 about-photo__img"
                         loading="lazy" decoding="async"
                         width="1019" height="1024">
                </div>
            </div>
            <div class="col-lg-7 col-xl-8 min-w-0" data-aos="fade-left">
                <h2 class="section-title text-start mb-4" style="text-align: left; font-family: var(--nmz-font-heading);">Nahir Álvarez Monzón</h2>
                <?php if (! empty($settings['about_text'])) : ?>
                    <div class="about-text lead-nmz"><?= nl2br(esc((string) $settings['about_text'])) ?></div>
                <?php else : ?>
                    <p class="about-text lead-nmz">
                        Soy Nahir Álvarez Monzón, artista visual afincada en Vigo, especializada en
                        retratos personalizados, arte en vivo y diseño gráfico. Mi trabajo nace de la
                        pasión por capturar emociones y transformar momentos en piezas de arte únicas
                        que cuentan una historia.
                    </p>
                    <p class="about-text lead-nmz">
                        Desde que tengo memoria, el arte ha sido mi forma de conectar con el mundo.
                        Estudié Bellas Artes y me formé en ilustración digital, lo que me permite
                        combinar técnica tradicional con herramientas contemporáneas. Cada proyecto
                        es un reto creativo que afronto con dedicación y mimo.
                    </p>
                    <p class="about-text lead-nmz">
                        Colaboro con marcas, parejas, familias y espacios culturales para crear piezas
                        que perduran: desde retratos de mascotas y familiares hasta identidades visuales
                        completas y arte en vivo en bodas y eventos. Llevo el arte a celebraciones y
                        hogares de toda España.
                    </p>
                <?php endif; ?>
                <p class="about-text lead-nmz mt-3 mb-3">
                    Trabajo desde Galicia con proyectos de toda la península: me importa escuchar tu historia,
                    entender el tono que quieres transmitir y traducirlo en una imagen coherente, sea un retrato,
                    un cartel o el logotipo de tu negocio. Cada encargo lo trato con calma, cercanía y atención al detalle.
                </p>
                <p class="about-text lead-nmz mb-0">
                    Si buscas algo hecho a mano, con carácter propio y listo para enmarcar, compartir o aplicar en
                    tus redes, estaré encantada de acompañarte en el proceso — desde la primera idea hasta la entrega final.
                </p>
                <div class="mt-4">
                    <a href="<?= base_url('contacto') ?>" class="btn btn-nmz-outline">Contactar</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. Testimonios (carousel con reseñas reales de Google) -->
<section class="section-padding bg-body-tertiary" id="testimonios">
    <div class="container">
        <div class="text-center mb-4">
            <h2 class="section-title">Reseñas</h2>
        </div>

        <?php
        $googleReviews = [
            ['name' => 'Raquel Fernández Graña', 'initials' => 'RF', 'rating' => 5,
             'text' => 'Es una artista increíble que trabaja con muchísimo cariño y profesionalidad en todo lo que hace. Cuida cada detalle. Sin duda es una gran oportunidad hacerse con alguna obra de ella!'],
            ['name' => 'Laura CZ', 'initials' => 'LC', 'rating' => 5,
             'text' => 'Nahir es, sencillamente, increíble. Sus ilustraciones son una auténtica maravilla y no se parecen a nada que haya visto antes. En la boda, la gente estaba fascinada viéndola pintar en directo mientras disfrutaban de la celebración. Un recuerdo para siempre.'],
            ['name' => 'Ariadna Gil', 'initials' => 'AG', 'rating' => 5,
             'text' => 'Hace todo al detalle tal cual le pides, 10 de 10 y súper rápida, eso muy importante.'],
            ['name' => 'Iago Martínez Vila', 'initials' => 'IM', 'rating' => 5,
             'text' => 'Me ha realizado una ilustración de mi perra y ha quedado hiperrealista, parece una foto real, muy recomendable.'],
        ];
        ?>

        <div class="review-carousel" aria-label="Carrusel de reseñas">
            <div class="review-carousel__viewport">
                <div class="review-carousel__track" id="reviewTrack">
                    <?php foreach ($googleReviews as $ri => $rev) : ?>
                    <div class="review-carousel__slide">
                        <div class="review-card">
                            <div class="review-card__header">
                                <div class="review-card__avatar"><?= esc($rev['initials']) ?></div>
                                <div>
                                    <cite class="review-card__name"><?= esc($rev['name']) ?></cite>
                                    <span class="review-card__source">
                                        <i class="bi bi-google" aria-hidden="true"></i>
                                        Reseña de Google
                                    </span>
                                </div>
                                <div class="review-card__stars ms-auto" aria-label="5 de 5 estrellas">★★★★★</div>
                            </div>
                            <blockquote class="review-card__text">"<?= esc($rev['text']) ?>"</blockquote>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="review-carousel__btn review-carousel__btn--prev" id="reviewPrev" type="button" aria-label="Anterior">
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </button>
            <button class="review-carousel__btn review-carousel__btn--next" id="reviewNext" type="button" aria-label="Siguiente">
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </button>
            <div class="review-carousel__dots" id="reviewDots">
                <?php foreach ($googleReviews as $ri => $rev) : ?>
                <button type="button" class="review-carousel__dot<?= $ri === 0 ? ' is-active' : '' ?>" aria-label="Ir a reseña <?= $ri + 1 ?>" data-index="<?= $ri ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- 5. Formulario de Contacto -->
<section class="section-padding" id="contacto">
    <div class="container">
        <div class="row g-5 align-items-start justify-content-center">
            <div class="col-lg-5" data-aos="fade-right">
                <h2 class="section-title text-start mb-4" style="text-align: left;">Hablemos</h2>
                <p class="lead-nmz mb-4">
                    ¿Tienes un proyecto en mente? Cuéntame qué necesitas y te responderé lo antes posible.
                </p>
                <ul class="list-unstyled home-contact-info">
                    <li>
                        <i class="bi bi-envelope" aria-hidden="true"></i>
                        <a href="mailto:nmonzzon@hotmail.com">nmonzzon@hotmail.com</a>
                    </li>
                    <li>
                        <i class="bi bi-telephone" aria-hidden="true"></i>
                        <a href="tel:+34623964677">623 964 677</a>
                    </li>
                    <li>
                        <i class="bi bi-geo-alt" aria-hidden="true"></i>
                        <span>Vigo, España</span>
                    </li>
                    <li>
                        <i class="bi bi-instagram" aria-hidden="true"></i>
                        <a href="https://www.instagram.com/nmonzzon/" rel="noopener noreferrer" target="_blank">@nmonzzon</a>
                    </li>
                    <li>
                        <i class="bi bi-tiktok" aria-hidden="true"></i>
                        <a href="https://www.tiktok.com/@nmonzzon" rel="noopener noreferrer" target="_blank">@nmonzzon</a>
                    </li>
                    <li>
                        <i class="bi bi-whatsapp" aria-hidden="true"></i>
                        <a href="https://wa.me/34623964677" rel="noopener noreferrer" target="_blank">WhatsApp</a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <?php
                $homeFlashOk = session()->getFlashdata('success');
                $homeFlashErr = session()->getFlashdata('error');
                ?>
                <?php if ($homeFlashOk) : ?>
                <div class="alert alert-success" role="alert"><?= esc($homeFlashOk) ?></div>
                <?php endif; ?>
                <?php if ($homeFlashErr) : ?>
                <div class="alert alert-danger" role="alert"><?= esc($homeFlashErr) ?></div>
                <?php endif; ?>

                <form action="<?= esc(base_url('contacto'), 'attr') ?>" method="post" class="home-contact-form">
                    <?= csrf_field() ?>
                    <div style="position:absolute;left:-9999px;">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="home_name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="home_name" name="name" value="<?= esc(old('name') ?? '') ?>" required autocomplete="name">
                        </div>
                        <div class="col-md-6">
                            <label for="home_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="home_email" name="email" value="<?= esc(old('email') ?? '') ?>" required autocomplete="email">
                        </div>
                        <div class="col-md-6">
                            <label for="home_phone" class="form-label">Teléfono <span class="text-secondary fw-normal">(opcional)</span></label>
                            <input type="tel" class="form-control" id="home_phone" name="phone" value="<?= esc(old('phone') ?? '') ?>" autocomplete="tel">
                        </div>
                        <div class="col-md-6">
                            <label for="home_category" class="form-label">Categoría</label>
                            <select class="form-select" id="home_category" name="category" required>
                                <option value="" disabled selected>Selecciona…</option>
                                <option value="general">Consulta general</option>
                                <option value="portrait">Retratos</option>
                                <option value="live_art">Arte en vivo</option>
                                <option value="branding">Branding</option>
                                <option value="design">Diseño</option>
                                <option value="products">Productos</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="home_subject" class="form-label">Asunto</label>
                            <input type="text" class="form-control" id="home_subject" name="subject" value="<?= esc(old('subject') ?? '') ?>" required>
                        </div>
                        <div class="col-12">
                            <label for="home_message" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="home_message" name="message" rows="4" required><?= esc(old('message') ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <?= view('partials/captcha') ?>
                        </div>
                        <div class="col-12 pt-2">
                            <button type="submit" class="btn btn-nmz btn-lg">
                                <i class="bi bi-send me-2" aria-hidden="true"></i>Enviar mensaje
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
(function () {
    /* GSAP hero animation */
    if (typeof gsap !== 'undefined') {
        var tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
        tl.from('.hero-title', { opacity: 0, y: 40, duration: 0.9 })
          .from('.hero-cta', { opacity: 0, y: 16, duration: 0.55 }, '-=0.35');
    }

    /* ---- Reviews Carousel ---- */
    var track = document.getElementById('reviewTrack');
    var btnPrev = document.getElementById('reviewPrev');
    var btnNext = document.getElementById('reviewNext');
    var dotsWrap = document.getElementById('reviewDots');
    if (!track) return;

    var slides = track.querySelectorAll('.review-carousel__slide');
    var dots   = dotsWrap ? dotsWrap.querySelectorAll('.review-carousel__dot') : [];
    var total  = slides.length;
    var current = 0;
    var autoTimer;

    function goTo(idx) {
        current = ((idx % total) + total) % total;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';
        for (var i = 0; i < dots.length; i++) {
            dots[i].classList.toggle('is-active', i === current);
        }
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    function startAuto() {
        stopAuto();
        autoTimer = setInterval(next, 5500);
    }
    function stopAuto() { clearInterval(autoTimer); }

    btnNext.addEventListener('click', function () { next(); startAuto(); });
    btnPrev.addEventListener('click', function () { prev(); startAuto(); });

    for (var d = 0; d < dots.length; d++) {
        dots[d].addEventListener('click', function () {
            goTo(parseInt(this.getAttribute('data-index'), 10));
            startAuto();
        });
    }

    /* Touch swipe */
    var touchStartX = 0;
    track.addEventListener('touchstart', function (e) {
        touchStartX = e.touches[0].clientX;
        stopAuto();
    }, { passive: true });
    track.addEventListener('touchend', function (e) {
        var diff = touchStartX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) {
            diff > 0 ? next() : prev();
        }
        startAuto();
    }, { passive: true });

    startAuto();
})();
</script>
<?= $this->endSection() ?>