<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/SETTINGS/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Configuración del sitio') ?>

<?php
$settings = $settings ?? [];
$val = static function (array $s, string $key, ?string $alt = null): string {
    if (array_key_exists($key, $s) && $s[$key] !== null && $s[$key] !== '') {
        return (string) $s[$key];
    }
    if ($alt !== null && array_key_exists($alt, $s) && $s[$alt] !== null && $s[$alt] !== '') {
        return (string) $s[$alt];
    }

    return '';
};
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Configuración del sitio</h1>
</div>

<form action="<?= base_url('admin/settings') ?>" method="post" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-card">
        <div class="card-body">
            <ul class="nav nav-tabs admin-tabs mb-3" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-general" data-bs-toggle="tab" data-bs-target="#pane-general" type="button" role="tab">General</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-images" data-bs-toggle="tab" data-bs-target="#pane-images" type="button" role="tab">Imágenes</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-shop" data-bs-toggle="tab" data-bs-target="#pane-shop" type="button" role="tab">E-commerce</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-portraits" data-bs-toggle="tab" data-bs-target="#pane-portraits" type="button" role="tab">Retratos</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-live" data-bs-toggle="tab" data-bs-target="#pane-live" type="button" role="tab">Arte en Vivo</button>
                </li>
            </ul>

            <div class="tab-content" id="settingsTabContent">
                <div class="tab-pane fade show active" id="pane-general" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="site_name">Nombre del sitio</label>
                            <input type="text" class="form-control" id="site_name" name="settings[site_name]" value="<?= esc($val($settings, 'site_name')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="contact_email">Email de contacto</label>
                            <input type="email" class="form-control" id="contact_email" name="settings[contact_email]" value="<?= esc($val($settings, 'contact_email')) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="site_description">Descripción</label>
                            <textarea class="form-control" id="site_description" name="settings[site_description]" rows="3"><?= esc($val($settings, 'site_description')) ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="contact_phone">Teléfono</label>
                            <input type="text" class="form-control" id="contact_phone" name="settings[contact_phone]" value="<?= esc($val($settings, 'contact_phone')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="address">Dirección</label>
                            <input type="text" class="form-control" id="address" name="settings[address]" value="<?= esc($val($settings, 'address')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="instagram_url">Instagram</label>
                            <input type="url" class="form-control" id="instagram_url" name="settings[instagram_url]" value="<?= esc($val($settings, 'instagram_url')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="facebook_url">Facebook</label>
                            <input type="url" class="form-control" id="facebook_url" name="settings[facebook_url]" value="<?= esc($val($settings, 'facebook_url')) ?>">
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-images" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label" for="path_site_logo">Logo</label>
                            <input type="text" class="form-control mb-2" id="path_site_logo" name="settings[site_logo]" value="<?= esc($val($settings, 'logo', 'site_logo')) ?>" placeholder="Ruta o URL">
                            <div class="upload-zone">
                                <i class="bi bi-image d-block"></i>
                                <span class="small text-muted">Subir imagen (opcional)</span>
                                <input type="file" name="logo" class="upload-zone-input visually-hidden" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="path_site_logo_white">Logo blanco</label>
                            <input type="text" class="form-control mb-2" id="path_site_logo_white" name="settings[site_logo_white]" value="<?= esc($val($settings, 'logo_white', 'site_logo_white')) ?>" placeholder="Ruta o URL">
                            <div class="upload-zone">
                                <i class="bi bi-image d-block"></i>
                                <span class="small text-muted">Subir imagen (opcional)</span>
                                <input type="file" name="logo_white" class="upload-zone-input visually-hidden" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="path_favicon">Favicon</label>
                            <input type="text" class="form-control mb-2" id="path_favicon" name="settings[favicon]" value="<?= esc($val($settings, 'favicon')) ?>" placeholder="Ruta o URL">
                            <div class="upload-zone">
                                <i class="bi bi-image d-block"></i>
                                <span class="small text-muted">Subir .ico / imagen (opcional)</span>
                                <input type="file" name="favicon" class="upload-zone-input visually-hidden" accept="image/*,.ico">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-shop" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="free_shipping_threshold">Compra mínima envío gratis</label>
                            <input type="text" class="form-control" id="free_shipping_threshold" name="settings[free_shipping_threshold]" inputmode="decimal"
                                   value="<?= esc($val($settings, 'free_shipping_min', 'free_shipping_threshold')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="shipping_base_cost">Coste envío estándar</label>
                            <input type="text" class="form-control" id="shipping_base_cost" name="settings[shipping_base_cost]" inputmode="decimal"
                                   value="<?= esc($val($settings, 'standard_shipping_cost', 'shipping_base_cost')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tax_rate">Tipo impositivo (%)</label>
                            <input type="text" class="form-control" id="tax_rate" name="settings[tax_rate]" value="<?= esc($val($settings, 'tax_rate')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="currency">Moneda</label>
                            <input type="text" class="form-control" id="currency" name="settings[currency]" value="<?= esc($val($settings, 'currency')) ?>">
                        </div>
                    </div>
                    <hr class="my-4">
                    <h3 class="h6 text-uppercase text-secondary mb-3">Stripe (pagos con tarjeta)</h3>
                    <p class="small text-muted mb-3">
                        También puedes definir <code class="small">STRIPE_PUBLIC_KEY</code> y <code class="small">STRIPE_SECRET_KEY</code> en el archivo <code class="small">.env</code>;
                        si existen y son válidas, tienen prioridad sobre lo guardado aquí.
                        El <strong>secreto del webhook</strong> solo va en <code class="small">.env</code> como <code class="small">STRIPE_WEBHOOK_SECRET</code>;
                        en Stripe Dashboard crea un endpoint que apunte a <code class="small"><?= esc(base_url('stripe/webhook')) ?></code>.
                    </p>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="stripe_public_key">Clave pública (publishable)</label>
                            <input type="text" class="form-control font-monospace small" id="stripe_public_key" name="settings[stripe_public_key]"
                                   value="<?= esc($val($settings, 'stripe_public_key')) ?>" placeholder="pk_test_…" autocomplete="off">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="stripe_secret_key">Clave secreta (secret)</label>
                            <input type="password" class="form-control font-monospace small" id="stripe_secret_key" name="settings[stripe_secret_key]"
                                   value="" placeholder="<?= $val($settings, 'stripe_secret_key') !== '' ? '•••••••• (dejar vacío para no cambiar)' : 'sk_test_…' ?>" autocomplete="new-password">
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-portraits" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="extra_figure_price">Precio figura extra</label>
                            <input type="text" class="form-control" id="extra_figure_price" name="settings[extra_figure_price]" inputmode="decimal" value="<?= esc($val($settings, 'extra_figure_price')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="standard_frame_price">Marco estándar</label>
                            <input type="text" class="form-control" id="standard_frame_price" name="settings[standard_frame_price]" inputmode="decimal" value="<?= esc($val($settings, 'standard_frame_price')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="premium_frame_price">Marco premium</label>
                            <input type="text" class="form-control" id="premium_frame_price" name="settings[premium_frame_price]" inputmode="decimal" value="<?= esc($val($settings, 'premium_frame_price')) ?>">
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pane-live" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="base_hourly_rate">Tarifa base hora</label>
                            <input type="text" class="form-control" id="base_hourly_rate" name="settings[base_hourly_rate]" inputmode="decimal" value="<?= esc($val($settings, 'base_hourly_rate')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="per_guest_rate">Por invitado</label>
                            <input type="text" class="form-control" id="per_guest_rate" name="settings[per_guest_rate]" inputmode="decimal" value="<?= esc($val($settings, 'per_guest_rate')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="per_km_rate">Por km</label>
                            <input type="text" class="form-control" id="per_km_rate" name="settings[per_km_rate]" inputmode="decimal" value="<?= esc($val($settings, 'per_km_rate')) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="free_radius_km">Radio gratis (km)</label>
                            <input type="text" class="form-control" id="free_radius_km" name="settings[free_radius_km]" inputmode="decimal" value="<?= esc($val($settings, 'free_radius_km')) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-admin">Guardar configuración</button>
            </div>
        </div>
    </div>
</form>

<?= $this->endSection() ?>