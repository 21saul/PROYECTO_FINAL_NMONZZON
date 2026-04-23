<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/ARTE-EN-VIVO/RESERVAR.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Solicitar Reserva') ?>

<?= $this->section('content') ?>

<?php $event_types = $event_types ?? []; ?>

<!-- 1. Page Hero (compact) -->
<section class="page-hero page-hero--compact" style="background-image: url('<?= esc(base_url('uploads/live-art/live art.JPG'), 'attr') ?>'); min-height: 38vh;">
    <div class="page-hero-overlay"></div>
    <div class="container page-hero-content py-4 text-center">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Arte en vivo', 'url' => base_url('arte-en-vivo')],
                ['label' => 'Reserva', 'url' => null],
            ],
            'nmzHeroTitle' => 'Solicitar reserva',
        ]) ?>
    </div>
</section>

<!-- 2. Form -->
<section class="section-padding">
    <div class="container">
        <div class="col-lg-8 mx-auto">
            <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success" role="alert"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger" role="alert"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <?php if (session()->has('errors')) : ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach (session('errors') as $e) : ?>
                    <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form action="<?= esc(base_url('arte-en-vivo/reservar'), 'attr') ?>" method="post" class="card border-0 shadow-sm p-4 p-lg-5">
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
                            <?php if (! empty($event_types)) : ?>
                                <?php
                                $typeValues = ['wedding', 'corporate', 'birthday', 'festival', 'private', 'other'];
                                foreach ($event_types as $optValue => $optLabel) :
                                    if (is_int($optValue)) {
                                        $optValue = $typeValues[$optValue] ?? 'other';
                                    }
                                    ?>
                            <option value="<?= esc((string) $optValue, 'attr') ?>" <?= (string) old('event_type') === (string) $optValue ? 'selected' : '' ?>><?= esc((string) $optLabel) ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <option value="wedding" <?= old('event_type') === 'wedding' ? 'selected' : '' ?>>Boda</option>
                            <option value="corporate" <?= old('event_type') === 'corporate' ? 'selected' : '' ?>>Corporativo</option>
                            <option value="birthday" <?= old('event_type') === 'birthday' ? 'selected' : '' ?>>Cumpleaños</option>
                            <option value="festival" <?= old('event_type') === 'festival' ? 'selected' : '' ?>>Festival</option>
                            <option value="private" <?= old('event_type') === 'private' ? 'selected' : '' ?>>Evento privado</option>
                            <option value="other" <?= old('event_type') === 'other' ? 'selected' : '' ?>>Otro</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="event_date" class="form-label">Fecha del evento</label>
                        <input type="date" class="form-control" id="event_date" name="event_date" value="<?= esc(old('event_date') ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="event_location" class="form-label">Lugar / espacio</label>
                        <input type="text" class="form-control" id="event_location" name="event_location" value="<?= esc(old('event_location') ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="event_city" class="form-label">Ciudad</label>
                        <input type="text" class="form-control" id="event_city" name="event_city" value="<?= esc(old('event_city') ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="num_guests" class="form-label">Número aproximado de invitados</label>
                        <input type="number" class="form-control" id="num_guests" name="num_guests" min="1" value="<?= esc(old('num_guests') ?? '') ?>">
                    </div>
                    <div class="col-12">
                        <label for="special_requirements" class="form-label">Requisitos o notas</label>
                        <textarea class="form-control" id="special_requirements" name="special_requirements" rows="4"><?= esc(old('special_requirements') ?? '') ?></textarea>
                    </div>
                    <div class="col-12">
                        <?= view('partials/captcha') ?>
                    </div>
                    <div class="col-12 text-center pt-2">
                        <button type="submit" class="btn btn-nmz btn-lg">Enviar solicitud</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<?= $this->endSection() ?>