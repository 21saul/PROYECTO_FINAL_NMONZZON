<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/RETRATOS/CONFIGURADOR.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Configurador de Retratos') ?>

<?php
$styles = $styles ?? [];
$sizes  = $sizes ?? [];
$isLoggedIn = (bool) session()->get('isLoggedIn');
$portraitApiAccessToken = '';
if ($isLoggedIn) {
    $uid = (int) session()->get('user_id');
    $userRow = model(\App\Models\UserModel::class)->find($uid);
    if ($userRow !== null && (int) ($userRow['is_active'] ?? 0) === 1) {
        $portraitApiAccessToken = (new \App\Libraries\JWTService())->generateAccessToken($userRow);
    }
}

$fallbackCliente = 'Alba_Méndez.jpg';
$fallbackImg     = base_url('uploads/retratos/clientes/' . rawurlencode($fallbackCliente));

/* Misma foto de héroe que la sección Retratos (índice) */
$configHeroFile = 'Yoli_Rodríguez_.jpg';
$configHeroUrl  = base_url('uploads/retratos/clientes/' . rawurlencode($configHeroFile));
$configHeroPath   = FCPATH . 'uploads/retratos/clientes/' . $configHeroFile;
if (is_file($configHeroPath)) {
    $configHeroUrl .= '?v=' . filemtime($configHeroPath);
}
?>

<?= $this->section('extra_css') ?>
<style>
.wizard-panels-wrap { position: relative; min-height: 240px; }
.wizard-panel {
    display: none;
    animation: portraitWizardIn 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
}
.wizard-panel.is-active { display: block; }
@keyframes portraitWizardIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
#portrait-order-feedback:empty { display: none; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="page-hero page-hero--retratos" style="background-image: url('<?= esc($configHeroUrl, 'attr') ?>');">
    <div class="page-hero-overlay"></div>
    <div class="container page-hero-content text-center py-4 py-md-5">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Retratos', 'url' => base_url('retratos')],
                ['label' => 'Configurador', 'url' => null],
            ],
            'nmzHeroTitle' => 'Personaliza tu retrato',
        ]) ?>
    </div>
</section>

<section class="portrait-wizard-section section-padding">
    <div class="container portrait-wizard-container">
        <div class="wizard-steps portrait-wizard__steps" id="wizard-steps" role="list">
            <?php
            $labels = ['Estilo', 'Tamaño', 'Figuras', 'Opciones', 'Resumen'];
            foreach ($labels as $n => $label) :
                $stepNum = $n + 1;
                ?>
            <div class="wizard-step<?= $stepNum === 1 ? ' active' : '' ?>" data-step="<?= $stepNum ?>" role="listitem">
                <span class="step-number"><?= $stepNum ?></span>
                <span class="step-label"><?= esc($label) ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <form id="portrait-config-form" class="portrait-wizard__form" novalidate>
            <div class="wizard-panels-wrap">
                <div class="wizard-panel is-active" data-panel="1" role="tabpanel">
                    <div class="portrait-wizard-panel-head">
                        <span class="portrait-wizard-panel-head__icon" aria-hidden="true"><i class="bi bi-palette"></i></span>
                        <div>
                            <h2 class="portrait-wizard-panel-head__title font-heading">Elige un estilo</h2>
                            <p class="portrait-wizard-panel-head__desc mb-0">Cada estilo tiene su carácter. Toca una tarjeta para seleccionar.</p>
                        </div>
                    </div>
                    <div class="row g-3 portrait-wizard__style-grid">
                        <?php foreach ($styles as $style) :
                            $sid = (int) $style['id'];
                            $simg = ! empty($style['sample_image'])
                                ? base_url($style['sample_image'])
                                : $fallbackImg;
                            ?>
                        <div class="col-6 col-lg-4">
                            <label class="portrait-wizard-label portrait-wizard-label--choice w-100 mb-0">
                                <input type="radio" name="portrait_style_id" value="<?= $sid ?>" class="portrait-choice-input style-radio">
                                <span class="portrait-style-card style-choice-card h-100">
                                    <span class="portrait-style-card__check" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                                    <span class="portrait-style-card__media">
                                        <img
                                            src="<?= esc($simg, 'attr') ?>"
                                            alt="<?= esc($style['name'] ?? 'Estilo', 'attr') ?>"
                                            class="portrait-style-card__img"
                                            loading="lazy"
                                            decoding="async"
                                            onerror="this.onerror=null;this.src='<?= esc($fallbackImg, 'attr') ?>';"
                                        >
                                    </span>
                                    <span class="portrait-style-card__body">
                                        <span class="portrait-style-card__name"><?= esc($style['name'] ?? '') ?></span>
                                        <span class="portrait-style-card__desc"><?= esc($style['description'] ?? '') ?></span>
                                        <span class="portrait-style-card__price">Desde <?= number_format((float) ($style['base_price'] ?? 0), 2, ',', '.') ?> €</span>
                                    </span>
                                </span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="invalid-feedback d-block portrait-wizard__error" id="error-step-1"></div>
                </div>

                <div class="wizard-panel" data-panel="2" role="tabpanel">
                    <div class="portrait-wizard-panel-head">
                        <span class="portrait-wizard-panel-head__icon" aria-hidden="true"><i class="bi bi-bounding-box"></i></span>
                        <div>
                            <h2 class="portrait-wizard-panel-head__title font-heading">Tamaño del retrato</h2>
                            <p class="portrait-wizard-panel-head__desc mb-0">Impreso o con marco según disponibilidad.</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <?php foreach ($sizes as $size) :
                            $zid = (int) $size['id'];
                            $typeLabel = esc(strtoupper((string) ($size['type'] ?? 'print')));
                            ?>
                        <div class="col-md-6">
                            <label class="portrait-wizard-label portrait-wizard-label--choice w-100 mb-0">
                                <input type="radio" name="portrait_size_id" value="<?= $zid ?>" class="portrait-choice-input size-radio">
                                <span class="portrait-size-card size-choice-card h-100">
                                    <span class="portrait-size-card__check" aria-hidden="true"><i class="bi bi-check-lg"></i></span>
                                    <span class="portrait-size-card__badge"><?= $typeLabel ?></span>
                                    <span class="portrait-size-card__name"><?= esc($size['name'] ?? '') ?></span>
                                    <span class="portrait-size-card__dims"><?= esc($size['dimensions'] ?? '') ?></span>
                                    <span class="portrait-size-card__extra">
                                        +<?= number_format((float) ($size['price_modifier'] ?? 0), 2, ',', '.') ?> €
                                    </span>
                                </span>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="invalid-feedback d-block portrait-wizard__error" id="error-step-2"></div>
                </div>

                <div class="wizard-panel" data-panel="3" role="tabpanel">
                    <div class="portrait-wizard-panel-head">
                        <span class="portrait-wizard-panel-head__icon" aria-hidden="true"><i class="bi bi-people"></i></span>
                        <div>
                            <h2 class="portrait-wizard-panel-head__title font-heading">Número de figuras</h2>
                            <p class="portrait-wizard-panel-head__desc mb-0">Personas u otras figuras principales (1–10).</p>
                        </div>
                    </div>
                    <div class="portrait-figures">
                        <button type="button" class="portrait-figures__btn" id="figures-minus" aria-label="Reducir figuras">
                            <i class="bi bi-dash-lg" aria-hidden="true"></i>
                        </button>
                        <div class="portrait-figures__value-wrap">
                            <input type="number" name="num_figures" id="num_figures" class="portrait-figures__input" value="1" min="1" max="10" inputmode="numeric">
                            <span class="portrait-figures__hint">figuras</span>
                        </div>
                        <button type="button" class="portrait-figures__btn" id="figures-plus" aria-label="Aumentar figuras">
                            <i class="bi bi-plus-lg" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback d-block portrait-wizard__error" id="error-step-3"></div>
                </div>

                <div class="wizard-panel" data-panel="4" role="tabpanel">
                    <div class="portrait-wizard-panel-head">
                        <span class="portrait-wizard-panel-head__icon" aria-hidden="true"><i class="bi bi-frame"></i></span>
                        <div>
                            <h2 class="portrait-wizard-panel-head__title font-heading">Marco</h2>
                            <p class="portrait-wizard-panel-head__desc mb-0">Opcional. Si lo activas, elige el acabado.</p>
                        </div>
                    </div>
                    <div class="portrait-frame-options">
                        <div class="form-check form-switch portrait-frame-switch">
                            <input class="form-check-input" type="checkbox" name="with_frame" id="with_frame" value="1">
                            <label class="form-check-label" for="with_frame">Quiero el retrato con marco</label>
                        </div>
                        <div id="frame-type-wrap" class="d-none mt-3">
                            <label for="frame_type" class="form-label portrait-wizard__field-label">Tipo de marco</label>
                            <select name="frame_type" id="frame_type" class="form-select form-control-nmz">
                                <option value="">Selecciona…</option>
                                <option value="madera_natural">Madera natural</option>
                                <option value="madera_negro">Madera negro mate</option>
                                <option value="madera_blanco">Madera blanco</option>
                                <option value="metal_negro">Metal negro fino</option>
                                <option value="metal_oro">Metal dorado</option>
                            </select>
                        </div>
                    </div>
                    <div class="invalid-feedback d-block portrait-wizard__error" id="error-step-4"></div>
                </div>

                <div class="wizard-panel" data-panel="5" role="tabpanel">
                    <div class="portrait-wizard-panel-head">
                        <span class="portrait-wizard-panel-head__icon" aria-hidden="true"><i class="bi bi-check2-circle"></i></span>
                        <div>
                            <h2 class="portrait-wizard-panel-head__title font-heading">Resumen y notas</h2>
                            <p class="portrait-wizard-panel-head__desc mb-0">Revisa tu pedido y añade detalles para el artista.</p>
                        </div>
                    </div>
                    <div class="portrait-wizard-summary">
                        <ul class="portrait-wizard-summary__list list-unstyled mb-0" id="resumen-list">
                            <li class="portrait-wizard-summary__row">
                                <span class="portrait-wizard-summary__label"><i class="bi bi-palette me-2 text-secondary" aria-hidden="true"></i>Estilo</span>
                                <span id="resumen-estilo" class="portrait-wizard-summary__val">—</span>
                            </li>
                            <li class="portrait-wizard-summary__row">
                                <span class="portrait-wizard-summary__label"><i class="bi bi-bounding-box me-2 text-secondary" aria-hidden="true"></i>Tamaño</span>
                                <span id="resumen-tamano" class="portrait-wizard-summary__val">—</span>
                            </li>
                            <li class="portrait-wizard-summary__row">
                                <span class="portrait-wizard-summary__label"><i class="bi bi-people me-2 text-secondary" aria-hidden="true"></i>Figuras</span>
                                <span id="resumen-figuras" class="portrait-wizard-summary__val">—</span>
                            </li>
                            <li class="portrait-wizard-summary__row">
                                <span class="portrait-wizard-summary__label"><i class="bi bi-frame me-2 text-secondary" aria-hidden="true"></i>Marco</span>
                                <span id="resumen-marco" class="portrait-wizard-summary__val">—</span>
                            </li>
                            <li class="portrait-wizard-summary__total">
                                <span>Total estimado</span>
                                <span id="precio-total" class="price-amount">0,00 €</span>
                            </li>
                        </ul>
                    </div>
                    <label for="client_notes" class="form-label portrait-wizard__field-label mt-4">Notas para el artista</label>
                    <textarea name="client_notes" id="client_notes" class="form-control form-control-nmz" rows="4" maxlength="2000" placeholder="Detalles de la foto, referencias, plazos…"></textarea>
                </div>
            </div>

            <div class="portrait-wizard__nav">
                <button type="button" class="btn btn-nmz-outline" id="wizard-prev">
                    <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>Anterior
                </button>
                <div class="portrait-wizard__nav-next">
                    <button type="button" class="btn btn-nmz" id="wizard-next">
                        Siguiente<i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
                    </button>
                    <button type="submit" class="btn btn-nmz d-none" id="wizard-submit">
                        <i class="bi bi-send me-2" aria-hidden="true"></i>Enviar pedido
                    </button>
                </div>
            </div>

            <div class="alert alert-info mt-4 portrait-wizard__hint<?= $isLoggedIn && $portraitApiAccessToken !== '' ? ' d-none' : '' ?>" id="portrait-login-hint" role="status">
                <?php if (! $isLoggedIn) : ?>
                    Para enviar el pedido necesitas <a href="<?= esc(base_url('login')) ?>">iniciar sesión</a>
                    o <a href="<?= esc(base_url('register')) ?>">crear una cuenta</a>.
                <?php else : ?>
                    No se pudo preparar la sesión segura para el envío. Recarga la página o contacta con soporte.
                <?php endif; ?>
            </div>
            <div id="portrait-order-feedback" class="mt-3" role="alert"></div>
        </form>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
const portraitStyles = <?= json_encode($styles, JSON_UNESCAPED_UNICODE) ?: '[]' ?>;
const portraitSizes = <?= json_encode($sizes, JSON_UNESCAPED_UNICODE) ?: '[]' ?>;
const portraitApiAccessToken = <?= json_encode($portraitApiAccessToken) ?>;
const nmzPortraitApiUrl = <?= json_encode(base_url('api/portrait-orders')) ?>;
const nmzCsrfHeader = <?= json_encode(config('Security')->headerName) ?>;
const nmzCsrfTokenName = <?= json_encode(csrf_token()) ?>;
const nmzCsrfHash = <?= json_encode(csrf_hash()) ?>;
const nmzLoginUrl = <?= json_encode(base_url('login')) ?>;
</script>
<script src="<?= base_url('assets/js/portrait-config.js') ?>"></script>
<?= $this->endSection() ?>