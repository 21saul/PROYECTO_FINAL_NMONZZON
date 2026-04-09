<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/AUTH/RESET-PASSWORD.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Restablecer Contraseña') ?>

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
                        <h1 class="auth-login-card__title font-heading mb-0"><?= esc('Restablecer contraseña') ?></h1>
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

                        <form action="<?= esc(base_url('reset-password'), 'attr') ?>" method="post" class="auth-form auth-login-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="token" value="<?= esc($token ?? '', 'attr') ?>">

                            <div class="mb-3">
                                <label for="reset_password" class="form-label-nmz"><?= esc('Nueva contraseña') ?></label>
                                <div class="input-group input-group-nmz">
                                    <span class="input-group-text border-end-0 text-secondary"><i class="bi bi-lock" aria-hidden="true"></i></span>
                                    <input
                                        type="password"
                                        class="form-control-nmz border-start-0 ps-0"
                                        id="reset_password"
                                        name="password"
                                        required
                                        minlength="8"
                                        autocomplete="new-password"
                                        placeholder="••••••••"
                                    >
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="reset_password_confirm" class="form-label-nmz"><?= esc('Confirmar contraseña') ?></label>
                                <div class="input-group input-group-nmz">
                                    <span class="input-group-text border-end-0 text-secondary"><i class="bi bi-lock-fill" aria-hidden="true"></i></span>
                                    <input
                                        type="password"
                                        class="form-control-nmz border-start-0 ps-0"
                                        id="reset_password_confirm"
                                        name="password_confirm"
                                        required
                                        minlength="8"
                                        autocomplete="new-password"
                                        placeholder="••••••••"
                                    >
                                </div>
                            </div>
                            <button type="submit" class="btn btn-nmz w-100 py-2 d-inline-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-shield-check" aria-hidden="true"></i>
                                <?= esc('Actualizar contraseña') ?>
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
