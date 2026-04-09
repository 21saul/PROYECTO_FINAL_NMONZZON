<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/AUTH/FORGOT-PASSWORD.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Recuperar Contraseña') ?>

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
            <div class="col-11 col-sm-10 col-md-8 col-lg-5 col-xl-4">
                <div class="auth-login-card shadow-lg">
                    <div class="auth-login-card__header text-center">
                        <div class="auth-login-card__brand-wrap mx-auto mb-3" aria-hidden="true">
                            <img src="<?= esc($abejaUrl, 'attr') ?>" alt="" class="auth-login-card__bee" width="88" height="88" decoding="async">
                        </div>
                        <h1 class="auth-login-card__title font-heading mb-2"><?= esc('Recuperar contraseña') ?></h1>
                        <p class="small text-secondary mb-0 px-1">
                            <?= esc('Introduce tu email y te enviaremos instrucciones para restablecer tu contraseña.') ?>
                        </p>
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

                        <form action="<?= esc(base_url('forgot-password'), 'attr') ?>" method="post" class="auth-form auth-login-form">
                            <?= csrf_field() ?>

                            <div class="mb-4">
                                <label for="forgot_email" class="form-label-nmz"><?= esc('Email') ?></label>
                                <div class="input-group input-group-nmz">
                                    <span class="input-group-text border-end-0 text-secondary"><i class="bi bi-envelope" aria-hidden="true"></i></span>
                                    <input
                                        type="email"
                                        class="form-control-nmz border-start-0 ps-0"
                                        id="forgot_email"
                                        name="email"
                                        value="<?= esc(old('email') ?? '') ?>"
                                        required
                                        autocomplete="email"
                                        placeholder="tu@email.com"
                                    >
                                </div>
                            </div>
                            <button type="submit" class="btn btn-nmz w-100 py-2 d-inline-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-send" aria-hidden="true"></i>
                                <?= esc('Enviar instrucciones') ?>
                            </button>
                        </form>
                    </div>

                    <div class="auth-login-card__footer text-center small">
                        <p class="mb-0">
                            <a href="<?= esc(base_url('login'), 'attr') ?>" class="auth-login-link"><?= esc('Volver al inicio de sesión') ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
