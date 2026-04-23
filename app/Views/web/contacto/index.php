<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/CONTACTO/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', $title ?? 'Contacto') ?>

<?= $this->section('content') ?>

<?php
$categoryOptions = [
    'general'   => 'Consulta general',
    'portrait'  => 'Retratos',
    'live_art'  => 'Arte en vivo',
    'branding'  => 'Branding',
    'design'    => 'Diseño',
    'products'  => 'Productos',
    'other'     => 'Otro',
];

/* Retrato editorial (no reutilizado como héroe principal de Retratos / Diseño / Tienda) */
$contactHeroRel  = 'uploads/retratos/clientes/Martu.jpg';
$contactHeroPath = FCPATH . $contactHeroRel;
$contactHeroUrl  = base_url($contactHeroRel);
if (is_file($contactHeroPath)) {
    $contactHeroUrl .= '?v=' . filemtime($contactHeroPath);
}
?>

<section class="page-hero page-hero--studio-hub page-hero--bg-img-layer" data-aos="fade-in">
    <img
        class="page-hero-bg-img page-hero-bg-img--hub page-hero-bg-img--contacto"
        src="<?= esc($contactHeroUrl, 'attr') ?>"
        alt="<?= esc('Retrato personalizado — nmonzzon studio', 'attr') ?>"
        width="3508"
        height="4961"
        sizes="100vw"
        fetchpriority="high"
        decoding="async"
    >
    <div class="page-hero-overlay page-hero-overlay--studio-contacto"></div>
    <div class="container page-hero-content py-5 text-center">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Contacto', 'url' => null],
            ],
            'nmzHeroTitle' => 'Contacto',
        ]) ?>
    </div>
</section>

<section class="section-padding contact-page-section">
    <div class="container">
        <div class="row g-4 g-lg-4 align-items-stretch contact-page__row">
            <div class="col-lg-7 d-flex min-w-0">
                <div class="liveart-form-clean contact-page__panel contact-page__panel--form w-100 d-flex flex-column">
                    <?php
                    $flashSuccess = session()->getFlashdata('success');
                    $flashError   = session()->getFlashdata('error');
                    ?>
                    <?php if ($flashSuccess) : ?>
                    <div class="alert alert-success py-2 small mb-3" role="alert"><?= esc($flashSuccess) ?></div>
                    <?php endif; ?>
                    <?php if ($flashError) : ?>
                    <div class="alert alert-danger py-2 small mb-3" role="alert"><?= esc($flashError) ?></div>
                    <?php endif; ?>

                    <?php
                    $formErrors = session('errors');
                    if (is_array($formErrors) && $formErrors !== []) :
                        ?>
                    <div class="alert alert-danger py-2 small mb-3" role="alert">
                        <ul class="mb-0 ps-3">
                            <?php foreach ($formErrors as $err) : ?>
                            <li><?= esc(is_array($err) ? implode(' ', $err) : (string) $err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <form action="<?= esc(base_url('contacto'), 'attr') ?>" method="post" class="contact-page__form d-flex flex-column flex-grow-1">
                        <?= csrf_field() ?>

                        <div class="visually-hidden" aria-hidden="true">
                            <input type="text" name="website" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contact_name" class="form-label"><?= esc('Nombre') ?></label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="contact_name"
                                    name="name"
                                    value="<?= esc(old('name') ?? '') ?>"
                                    required
                                    autocomplete="name"
                                >
                            </div>
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label"><?= esc('Email') ?></label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="contact_email"
                                    name="email"
                                    value="<?= esc(old('email') ?? '') ?>"
                                    required
                                    autocomplete="email"
                                >
                            </div>
                            <div class="col-md-6">
                                <label for="contact_phone" class="form-label"><?= esc('Teléfono') ?></label>
                                <input
                                    type="tel"
                                    class="form-control"
                                    id="contact_phone"
                                    name="phone"
                                    value="<?= esc(old('phone') ?? '') ?>"
                                    autocomplete="tel"
                                >
                            </div>
                            <div class="col-md-6">
                                <label for="contact_category" class="form-label"><?= esc('Categoría') ?></label>
                                <select class="form-select" id="contact_category" name="category" required>
                                    <option value="" disabled <?= old('category') === null || old('category') === '' ? 'selected' : '' ?>><?= esc('Selecciona…') ?></option>
                                    <?php foreach ($categoryOptions as $value => $label) : ?>
                                    <option value="<?= esc($value, 'attr') ?>" <?= (string) old('category') === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="contact_subject" class="form-label"><?= esc('Asunto') ?></label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="contact_subject"
                                    name="subject"
                                    value="<?= esc(old('subject') ?? '') ?>"
                                    required
                                >
                            </div>
                        </div>

                        <div class="contact-page__message-block mt-3 d-flex flex-column flex-grow-1">
                            <label for="contact_message" class="form-label"><?= esc('Mensaje') ?></label>
                            <textarea
                                class="form-control contact-page__textarea flex-grow-1"
                                id="contact_message"
                                name="message"
                                rows="6"
                                required
                            ><?= esc(old('message') ?? '') ?></textarea>
                        </div>

                        <div class="mt-3">
                            <?= view('partials/captcha') ?>
                        </div>

                        <div class="text-center pt-3 mt-auto">
                            <button type="submit" class="btn btn-nmz px-4">
                                <i class="bi bi-send me-2" aria-hidden="true"></i><?= esc('Enviar mensaje') ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-5 d-flex min-w-0">
                <div class="liveart-form-clean contact-page__panel contact-page__panel--aside w-100 d-flex flex-column" data-aos="fade-left">
                    <h2 class="contact-info-card__title font-heading"><?= esc('Información de contacto') ?></h2>
                    <ul class="contact-info-card__list list-unstyled mb-0">
                        <li class="contact-info-card__item">
                            <span class="contact-info-card__label"><?= esc('Email') ?></span>
                            <a href="mailto:nmonzzon@hotmail.com" class="text-break"><?= esc('nmonzzon@hotmail.com') ?></a>
                        </li>
                        <li class="contact-info-card__item">
                            <span class="contact-info-card__label"><?= esc('Teléfono') ?></span>
                            <a href="tel:+34623964677"><?= esc('623 964 677') ?></a>
                        </li>
                        <li class="contact-info-card__item">
                            <span class="contact-info-card__label"><?= esc('Ubicación') ?></span>
                            <span class="text-secondary"><?= esc('Vigo, España') ?></span>
                        </li>
                        <li class="contact-info-card__item contact-info-card__item--last">
                            <span class="contact-info-card__label"><?= esc('Instagram') ?></span>
                            <a href="https://www.instagram.com/nmonzzon/" rel="noopener noreferrer" target="_blank">@nmonzzon</a>
                        </li>
                    </ul>
                    <div class="contact-sidebar-logo-wrap text-center border-top border-light">
                        <img
                            src="<?= esc(base_url('uploads/site/contacto-logo-abeja.png'), 'attr') ?>"
                            alt="<?= esc('Marca nmonzzon — abeja', 'attr') ?>"
                            class="img-fluid contact-sidebar-logo contact-sidebar-logo--abeja"
                            width="200"
                            height="200"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>