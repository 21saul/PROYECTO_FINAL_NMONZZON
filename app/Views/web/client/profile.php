<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/CLIENT/PROFILE.PHP
 * =============================================================================
 */
?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Mi Perfil') ?>

<?php
$user = $user ?? [];
$name = trim((string) ($user['name'] ?? ''));
$parts = $name !== '' ? preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) : [];
$firstName = $parts[0] ?? 'usuario';
$initials = '?';
if ($parts !== []) {
    if (count($parts) >= 2) {
        $a = function_exists('mb_substr') ? mb_substr($parts[0], 0, 1, 'UTF-8') : substr($parts[0], 0, 1);
        $last = $parts[count($parts) - 1];
        $b = function_exists('mb_substr') ? mb_substr($last, 0, 1, 'UTF-8') : substr($last, 0, 1);
        $initials = strtoupper($a . $b);
    } else {
        $w = $parts[0];
        $one = function_exists('mb_substr') ? mb_substr($w, 0, 1, 'UTF-8') : substr($w, 0, 1);
        $initials = strtoupper($one);
    }
}
$email = (string) ($user['email'] ?? '');
$createdRaw = $user['created_at'] ?? null;
$memberSince = '';
if ($createdRaw) {
    $t = strtotime((string) $createdRaw);
    $memberSince = $t ? date('d/m/Y', $t) : '';
}
$isClient = (($user['role'] ?? session('role')) === 'client');
$avatarPath = trim((string) ($user['avatar'] ?? ''));
$avatarPublicUrl = '';
if ($avatarPath !== '') {
    $avatarPublicUrl = preg_match('#^https?://#i', $avatarPath)
        ? $avatarPath
        : base_url(ltrim($avatarPath, '/'));
}
?>

<?= $this->section('extra_css') ?>
<link href="<?= base_url('assets/css/profile-account.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="nmz-page-header py-4 border-bottom bg-white">
    <div class="container">
        <nav class="nmz-hero-crumbs nmz-hero-crumbs--on-light nmz-hero-crumbs--align-start" aria-label="Migas de pan">
            <ol class="nmz-hero-crumbs__list">
                <li class="nmz-hero-crumbs__item"><a href="<?= esc(base_url('/'), 'attr') ?>">Inicio</a></li>
                <li class="nmz-hero-crumbs__item"><a href="<?= esc(base_url('mi-cuenta'), 'attr') ?>">Mi cuenta</a></li>
                <li class="nmz-hero-crumbs__item"><span aria-current="page">Mi perfil</span></li>
            </ol>
        </nav>
        <div class="profile-identity-card profile-identity-card--flat mt-3">
            <div class="profile-identity-card__avatar">
                <?php if ($avatarPublicUrl !== '') : ?>
                <div class="profile-avatar profile-avatar--photo profile-avatar--profile-hero" aria-hidden="true">
                    <img src="<?= esc($avatarPublicUrl, 'attr') ?>" alt="" width="112" height="112" decoding="async">
                </div>
                <?php else : ?>
                <div class="profile-avatar profile-avatar--profile-hero" aria-hidden="true"><?= esc($initials) ?></div>
                <?php endif; ?>
            </div>
            <div class="profile-identity-card__main">
                <h1 class="profile-identity-card__greeting font-heading">Hola, <?= esc($firstName) ?></h1>
                <p class="profile-identity-card__email">
                    <i class="bi bi-envelope" aria-hidden="true"></i>
                    <span class="text-break"><?= esc($email) ?></span>
                </p>
                <?php if ($memberSince !== '') : ?>
                <p class="profile-identity-card__since">
                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                    Cliente desde el <?= esc($memberSince) ?>
                </p>
                <?php endif; ?>
            </div>
            <div class="profile-identity-card__actions">
                <a href="<?= esc(base_url('logout'), 'attr') ?>" class="btn btn-profile-logout">
                    <i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>
                    Cerrar sesión
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section-padding profile-section-nmz">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php
                $flashSuccess = session()->getFlashdata('success');
                $flashError   = session()->getFlashdata('error');
                ?>
                <?php if ($flashSuccess) : ?>
                <div class="alert alert-success border-0 rounded-3 shadow-sm" role="alert"><?= esc($flashSuccess) ?></div>
                <?php endif; ?>
                <?php if ($flashError) : ?>
                <div class="alert alert-danger border-0 rounded-3 shadow-sm" role="alert"><?= esc($flashError) ?></div>
                <?php endif; ?>

                <?php
                $formErrors = session('errors');
                if (is_array($formErrors) && $formErrors !== []) :
                    ?>
                <div class="alert alert-danger border-0 rounded-3" role="alert">
                    <ul class="mb-0 ps-3">
                        <?php foreach ($formErrors as $err) : ?>
                        <li><?= esc(is_array($err) ? implode(' ', $err) : (string) $err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <div class="profile-panel-nmz">
                    <h2 class="profile-panel-nmz__title font-heading mb-0">Datos de contacto</h2>
                    <form action="<?= esc(base_url('mi-cuenta/perfil'), 'attr') ?>" method="post" class="auth-form">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="profile_name" class="form-label-nmz">Nombre</label>
                            <input
                                type="text"
                                class="form-control-nmz"
                                id="profile_name"
                                name="name"
                                value="<?= esc(old('name', $user['name'] ?? '')) ?>"
                                required
                                autocomplete="name"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="profile_email" class="form-label-nmz">Email</label>
                            <input
                                type="email"
                                class="form-control-nmz bg-light"
                                id="profile_email"
                                value="<?= esc($email) ?>"
                                readonly
                                autocomplete="email"
                                aria-describedby="profile_email_help"
                            >
                            <p id="profile_email_help" class="form-text small text-secondary mb-0">El email no se puede cambiar desde aquí.</p>
                        </div>

                        <div class="mb-4">
                            <label for="profile_phone" class="form-label-nmz">Teléfono</label>
                            <input
                                type="tel"
                                class="form-control-nmz"
                                id="profile_phone"
                                name="phone"
                                value="<?= esc(old('phone', $user['phone'] ?? '')) ?>"
                                autocomplete="tel"
                            >
                        </div>

                        <button type="submit" class="btn btn-nmz">
                            <i class="bi bi-check2-circle me-1" aria-hidden="true"></i> Guardar cambios
                        </button>
                    </form>
                </div>

                <div class="profile-panel-nmz">
                    <h2 class="profile-panel-nmz__title font-heading mb-0">Foto de perfil</h2>
                    <p class="text-secondary small mb-3">Opcional. Si subes una imagen, se mostrará en la barra superior (junto al carrito) en lugar del icono de usuario.</p>
                    <form action="<?= esc(base_url('mi-cuenta/perfil/foto'), 'attr') ?>" method="post" enctype="multipart/form-data" class="auth-form">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="profile_photo" class="form-label-nmz">Imagen</label>
                            <input type="file" class="form-control-nmz" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/gif,image/webp" required>
                            <p id="profile_photo_help" class="form-text small text-secondary mb-0">JPEG, PNG, GIF o WebP. Máximo 2 MB.</p>
                        </div>
                        <button type="submit" class="btn btn-outline-nmz">
                            <i class="bi bi-camera me-1" aria-hidden="true"></i> Actualizar foto
                        </button>
                    </form>
                </div>

                <div class="profile-panel-nmz">
                    <h2 class="profile-panel-nmz__title font-heading mb-0">Seguridad</h2>
                    <p class="text-secondary small mb-3">Si no recuerdas tu contraseña, puedes restablecerla desde el enlace de acceso.</p>
                    <a href="<?= esc(base_url('forgot-password'), 'attr') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="bi bi-key me-1" aria-hidden="true"></i> Recuperar contraseña
                    </a>
                </div>

                <?php if ($isClient) : ?>
                <div class="profile-panel-nmz profile-panel-nmz--danger">
                    <h2 class="profile-panel-nmz__title font-heading mb-0">Zona de peligro</h2>
                    <p class="profile-delete-hint mb-3">
                        Eliminar tu cuenta es <strong>permanente</strong> para el acceso: no podrás iniciar sesión de nuevo con este email
                        (los pedidos asociados se conservan en el sistema). Para confirmar, escribe <code>ELIMINAR</code> y tu contraseña actual.
                    </p>
                    <form action="<?= esc(base_url('mi-cuenta/perfil/eliminar-cuenta'), 'attr') ?>" method="post" class="auth-form" id="form-delete-account">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="delete_password" class="form-label text-danger">Contraseña actual</label>
                            <input
                                type="password"
                                class="form-control"
                                id="delete_password"
                                name="delete_password"
                                required
                                autocomplete="current-password"
                            >
                        </div>
                        <div class="mb-3">
                            <label for="delete_confirm_phrase" class="form-label text-danger">Confirmación</label>
                            <input
                                type="text"
                                class="form-control font-monospace"
                                id="delete_confirm_phrase"
                                name="delete_confirm_phrase"
                                required
                                autocomplete="off"
                                placeholder="ELIMINAR"
                            >
                        </div>
                        <button type="submit" class="btn btn-outline-danger rounded-pill" data-confirm-delete="1">
                            <i class="bi bi-trash3 me-1" aria-hidden="true"></i> Eliminar mi cuenta
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
document.getElementById('form-delete-account')?.addEventListener('submit', function (e) {
    if (!confirm('¿Seguro de que quieres eliminar tu cuenta? Esta acción no se puede deshacer desde la web.')) {
        e.preventDefault();
    }
});
</script>
<?= $this->endSection() ?>
