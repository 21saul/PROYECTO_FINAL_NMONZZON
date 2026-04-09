<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/AUTH/REGISTER.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Crear Cuenta') ?>

<?php
$abejaPath = FCPATH . 'assets/images/auth-login-abeja.png';
$abejaUrl  = base_url('assets/images/auth-login-abeja.png');
if (is_file($abejaPath)) {
    $abejaUrl .= '?v=' . filemtime($abejaPath);
}
?>

<?= $this->section('extra_css') ?>
<link href="<?= base_url('assets/css/auth-login.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="auth-login-page">
    <div class="container auth-login-inner py-0">
        <div class="row justify-content-center align-items-center auth-login-min-height">
            <div class="col-11 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <div class="auth-login-card shadow-lg">
                    <div class="auth-login-card__header text-center">
                        <div class="auth-login-card__brand-wrap mx-auto mb-3" aria-hidden="true">
                            <img src="<?= esc($abejaUrl, 'attr') ?>" alt="" class="auth-login-card__bee" width="88" height="88" decoding="async">
                        </div>
                        <h1 class="auth-login-card__title font-heading mb-0"><?= esc('Crear cuenta') ?></h1>
                    </div>

                    <div class="auth-login-card__body">
                        <?php
                        $flashSuccess = session()->getFlashdata('success');
                        $flashError   = session()->getFlashdata('error');
                        ?>
                        <?php if ($flashSuccess) : ?>
                        <div class="alert alert-success border-0 rounded-3 small" role="alert"><?= esc($flashSuccess) ?></div>
                        <?php endif; ?>
                        <?php if ($flashError) : ?>
                        <div class="alert alert-danger border-0 rounded-3 small" role="alert"><?= esc($flashError) ?></div>
                        <?php endif; ?>

                        <?php
                        $formErrors = session('errors');
                        if (is_array($formErrors) && $formErrors !== []) :
                            ?>
                        <div class="alert alert-danger border-0 rounded-3 small" role="alert">
                            <ul class="mb-0 ps-3">
                                <?php foreach ($formErrors as $err) : ?>
                                <li><?= esc(is_array($err) ? implode(' ', $err) : (string) $err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>

                        <form action="<?= esc(base_url('register'), 'attr') ?>" method="post" class="auth-form auth-login-form">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="register_name" class="form-label-nmz"><?= esc('Nombre') ?></label>
                                <div class="input-group input-group-nmz">
                                    <span class="input-group-text border-end-0 text-secondary"><i class="bi bi-person" aria-hidden="true"></i></span>
                                    <input
                                        type="text"
                                        class="form-control-nmz border-start-0 ps-0"
                                        id="register_name"
                                        name="name"
                                        value="<?= esc(old('name') ?? '') ?>"
                                        required
                                        autocomplete="name"
                                    >
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="register_email" class="form-label-nmz"><?= esc('Email') ?></label>
                                <div class="input-group input-group-nmz">
                                    <span class="input-group-text border-end-0 text-secondary"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                                    <input
                                        type="email"
                                        class="form-control-nmz border-start-0 ps-0"
                                        id="register_email"
                                        name="email"
                                        value="<?= esc(old('email') ?? '') ?>"
                                        required
                                        autocomplete="email"
                                        placeholder="tu@email.com"
                                    >
                                </div>
                            </div>
                            <div class="mb-2">
                                <label for="register_password" class="form-label-nmz"><?= esc('Contraseña') ?></label>
                                <div class="input-group input-group-nmz">
                                    <span class="input-group-text border-end-0 text-secondary"><i class="bi bi-lock" aria-hidden="true"></i></span>
                                    <input
                                        type="password"
                                        class="form-control-nmz border-start-0 ps-0"
                                        id="register_password"
                                        name="password"
                                        required
                                        minlength="8"
                                        autocomplete="new-password"
                                        aria-describedby="password_strength_hint"
                                        placeholder="••••••••"
                                    >
                                </div>
                                <p class="small text-secondary mb-0 mt-1"><?= esc('Mínimo 8 caracteres (elige una contraseña que recuerdes con facilidad).') ?></p>
                            </div>
                            <div class="mb-3" id="password_strength_hint">
                                <div class="progress rounded-0" style="height: 4px;" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="password_strength_progress_wrap">
                                    <div class="progress-bar rounded-0" id="password_strength_bar" style="width: 0%;"></div>
                                </div>
                                <p class="small mb-0 mt-2 text-secondary" id="password_strength_label"><?= esc('Seguridad de la contraseña') ?></p>
                            </div>
                            <div class="mb-3">
                                <label for="register_password_confirm" class="form-label-nmz"><?= esc('Confirmar contraseña') ?></label>
                                <div class="input-group input-group-nmz">
                                    <span class="input-group-text border-end-0 text-secondary"><i class="bi bi-lock-fill" aria-hidden="true"></i></span>
                                    <input
                                        type="password"
                                        class="form-control-nmz border-start-0 ps-0"
                                        id="register_password_confirm"
                                        name="password_confirm"
                                        required
                                        minlength="8"
                                        autocomplete="new-password"
                                        placeholder="••••••••"
                                    >
                                </div>
                            </div>
                            <div class="mb-4 form-check">
                                <input
                                    type="checkbox"
                                    class="form-check-input rounded-0"
                                    id="register_accept"
                                    name="accept_terms"
                                    value="1"
                                    required
                                    <?= old('accept_terms') ? 'checked' : '' ?>
                                >
                                <label class="form-check-label small" for="register_accept">
                                    <?= esc('Acepto la política de privacidad y los términos de uso') ?>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-nmz w-100 py-2 d-inline-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-person-plus" aria-hidden="true"></i>
                                <?= esc('Crear cuenta') ?>
                            </button>
                        </form>
                    </div>

                    <div class="auth-login-card__footer text-center small">
                        <p class="mb-0 text-secondary">
                            <?= esc('¿Ya tienes cuenta?') ?>
                            <a href="<?= esc(base_url('login'), 'attr') ?>" class="auth-login-link fw-semibold"><?= esc('Inicia sesión') ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
(function () {
    'use strict';
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('register_password');
        var bar = document.getElementById('password_strength_bar');
        var wrap = document.getElementById('password_strength_progress_wrap');
        var label = document.getElementById('password_strength_label');
        if (!input || !bar || !label) {
            return;
        }

        function scorePassword(value) {
            var score = 0;
            if (value.length >= 8) {
                score++;
            }
            if (/[A-Z]/.test(value)) {
                score++;
            }
            if (/[0-9]/.test(value)) {
                score++;
            }
            if (/[^A-Za-z0-9]/.test(value)) {
                score++;
            }
            return score;
        }

        function updateStrength() {
            var v = input.value || '';
            var s = scorePassword(v);
            var pct = (s / 4) * 100;
            bar.style.width = pct + '%';
            if (wrap) {
                wrap.setAttribute('aria-valuenow', String(Math.round(pct)));
            }
            bar.classList.remove('bg-danger', 'bg-warning', 'bg-success');
            if (v.length === 0) {
                label.textContent = 'Seguridad de la contraseña';
                label.className = 'small mb-0 mt-2 text-secondary';
                bar.style.width = '0%';
                return;
            }
            if (s <= 1) {
                label.textContent = 'Débil';
                label.className = 'small mb-0 mt-2 text-danger';
                bar.classList.add('bg-danger');
            } else if (s <= 3) {
                label.textContent = 'Media';
                label.className = 'small mb-0 mt-2 text-warning';
                bar.classList.add('bg-warning');
            } else {
                label.textContent = 'Fuerte';
                label.className = 'small mb-0 mt-2 text-success';
                bar.classList.add('bg-success');
            }
        }

        input.addEventListener('input', updateStrength);
        input.addEventListener('change', updateStrength);
        updateStrength();
    });
})();
</script>
<?= $this->endSection() ?>
