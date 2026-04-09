<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/CLIENT/PORTRAIT-DETAIL.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php
$portrait = $portrait ?? [];
$history  = $history ?? [];
$on       = (string) ($portrait['order_number'] ?? '');
?>

<?php $this->setVar('pageTitle', 'Retrato #' . $on) ?>

<?= $this->section('extra_css') ?>
<style>
.timeline-item.timeline-complete::before {
    background-color: var(--nmz-success);
    border-color: var(--nmz-white);
    box-shadow: 0 0 0 2px var(--nmz-off-white);
}
</style>
<?= $this->endSection() ?>

<?php
$portraitStatusBadge = static function (?string $status): string {
    $s = strtolower((string) $status);
    $map = [
        'quote'           => 'badge-quote',
        'accepted'        => 'badge-accepted',
        'photo_received'  => 'badge-in-progress',
        'in_progress'     => 'badge-in-progress',
        'revision'        => 'badge-revision',
        'delivered'       => 'badge-delivered',
        'completed'       => 'badge-completed',
        'cancelled'       => 'badge-cancelled',
    ];
    $cls = $map[$s] ?? 'badge-quote';

    return 'badge-status ' . $cls;
};

$fmtDate = static function ($dt): string {
    if ($dt === null || $dt === '') {
        return '—';
    }
    $t = strtotime((string) $dt);

    return $t ? date('d/m/Y H:i', $t) : '—';
};

$flowSteps = [
    'quote'           => 'Presupuesto',
    'accepted'        => 'Aceptado',
    'photo_received'  => 'Foto recibida',
    'in_progress'     => 'En proceso',
    'revision'        => 'Revisión',
    'delivered'       => 'Entregado',
    'completed'       => 'Completado',
];

$currentStatus = strtolower((string) ($portrait['status'] ?? 'quote'));
$isCancelled   = $currentStatus === 'cancelled';
$flowKeys      = array_keys($flowSteps);
$currentIdx    = $isCancelled ? -1 : array_search($currentStatus, $flowKeys, true);
if ($currentIdx === false) {
    $currentIdx = 0;
}

$pid = (int) ($portrait['id'] ?? 0);
$sketchUrl = $portrait['sketch_image'] ?? null;
$finalUrl  = $portrait['final_image'] ?? null;
$refPhoto  = $portrait['reference_photo'] ?? null;

$showPhotoUpload = ($currentStatus === 'accepted')
    && ($refPhoto === null || $refPhoto === '');
?>

<?= $this->section('content') ?>

<section
    class="page-hero"
    style="background-image: url('<?= esc(nmz_mi_cuenta_hero_bg_url(), 'attr') ?>');"
>
    <div class="page-hero-overlay"></div>
    <div class="container page-hero-content py-4 text-center">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Mi cuenta', 'url' => base_url('mi-cuenta')],
                ['label' => 'Mis retratos', 'url' => base_url('mi-cuenta/retratos')],
                ['label' => 'Retrato ' . $on, 'url' => null],
            ],
            'nmzHeroTitle' => 'Retrato ' . $on,
        ]) ?>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="dashboard-card mb-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <p class="small text-uppercase text-secondary mb-1">Nº encargo</p>
                    <p class="mb-0 fw-semibold"><?= esc($on) ?></p>
                </div>
                <div class="col-md-4">
                    <p class="small text-uppercase text-secondary mb-1">Fecha</p>
                    <p class="mb-0"><?= esc($fmtDate($portrait['created_at'] ?? null)) ?></p>
                </div>
                <div class="col-md-4">
                    <p class="small text-uppercase text-secondary mb-1">Estado</p>
                    <p class="mb-0">
                        <span class="<?= esc($portraitStatusBadge($portrait['status'] ?? null), 'attr') ?>">
                            <?= esc($portrait['status'] ?? '') ?>
                        </span>
                    </p>
                </div>
                <div class="col-md-4">
                    <p class="small text-uppercase text-secondary mb-1">Estilo</p>
                    <p class="mb-0"><?= esc($portrait['style_name'] ?? '—') ?></p>
                </div>
                <div class="col-md-4">
                    <p class="small text-uppercase text-secondary mb-1">Tamaño</p>
                    <p class="mb-0"><?= esc($portrait['size_name'] ?? '—') ?></p>
                </div>
                <div class="col-md-4">
                    <p class="small text-uppercase text-secondary mb-1">Figuras / marco</p>
                    <p class="mb-0">
                        <?= esc((string) (int) ($portrait['num_figures'] ?? 0)) ?>
                        ·
                        <?= ! empty($portrait['with_frame']) ? 'Con marco' : 'Sin marco' ?>
                    </p>
                </div>
                <div class="col-md-4">
                    <p class="small text-uppercase text-secondary mb-1">Total</p>
                    <p class="mb-0 fw-semibold"><?= esc(number_format((float) ($portrait['total_price'] ?? 0), 2, ',', ' ')) ?> €</p>
                </div>
            </div>
        </div>

        <?php if ($isCancelled) : ?>
        <div class="alert alert-secondary" role="status">Este encargo ha sido cancelado.</div>
        <?php endif; ?>

        <div class="dashboard-card mb-4">
            <h4 class="mb-4">Proceso del retrato</h4>
            <div class="timeline">
                <?php
                $i = 0;
                foreach ($flowSteps as $key => $label) :
                    $classes = ['timeline-item'];
                    if (! $isCancelled) {
                        if ($i < $currentIdx) {
                            $classes[] = 'timeline-complete';
                        } elseif ($i === $currentIdx) {
                            $classes[] = 'active';
                        }
                    }
                    $i++;
                    ?>
                <div class="<?= esc(implode(' ', $classes), 'attr') ?>">
                    <h5><?= esc($label) ?></h5>
                    <p class="small text-uppercase text-secondary mb-0"><?= esc($key) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($showPhotoUpload) : ?>
        <div class="dashboard-card mb-4">
            <h4 class="mb-3">Subir foto de referencia</h4>
            <p class="text-secondary small mb-3">Adjunta una imagen clara para trabajar el retrato. Formatos habituales: JPG, PNG.</p>
            <form
                action="<?= esc(base_url('mi-cuenta/retratos/' . $pid . '/referencia'), 'attr') ?>"
                method="post"
                enctype="multipart/form-data"
                class="row g-3 align-items-end"
            >
                <?= csrf_field() ?>
                <div class="col-md-8">
                    <label for="reference_photo" class="form-label-nmz">Archivo</label>
                    <input type="file" class="form-control" name="reference_photo" id="reference_photo" accept="image/*" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-nmz w-100">Enviar foto</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <?php if (! empty($sketchUrl)) : ?>
        <div class="dashboard-card mb-4">
            <h4 class="mb-3">Boceto</h4>
            <a
                href="<?= esc(base_url(ltrim((string) $sketchUrl, '/')), 'attr') ?>"
                class="glightbox d-inline-block rounded overflow-hidden shadow-sm"
                data-gallery="portrait-<?= esc((string) $pid, 'attr') ?>"
                data-glightbox="title: <?= esc('Boceto — ' . $on, 'attr') ?>"
            >
                <img
                    src="<?= esc(base_url(ltrim((string) $sketchUrl, '/')), 'attr') ?>"
                    alt="Boceto del retrato"
                    class="img-fluid"
                    style="max-height: 420px; object-fit: contain;"
                >
            </a>
        </div>
        <?php endif; ?>

        <?php if (! empty($finalUrl)) : ?>
        <div class="dashboard-card mb-4">
            <h4 class="mb-3">Imagen final</h4>
            <a
                href="<?= esc(base_url(ltrim((string) $finalUrl, '/')), 'attr') ?>"
                class="glightbox d-inline-block rounded overflow-hidden shadow-sm"
                data-gallery="portrait-<?= esc((string) $pid, 'attr') ?>"
                data-glightbox="title: <?= esc('Imagen final — ' . $on, 'attr') ?>"
            >
                <img
                    src="<?= esc(base_url(ltrim((string) $finalUrl, '/')), 'attr') ?>"
                    alt="Imagen final del retrato"
                    class="img-fluid"
                    style="max-height: 420px; object-fit: contain;"
                >
            </a>
        </div>
        <?php endif; ?>

        <?php if ($history !== []) : ?>
        <div class="dashboard-card">
            <h4 class="mb-3">Historial de estado</h4>
            <div class="table-responsive">
                <table class="table table-dashboard mb-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Desde</th>
                            <th>Hasta</th>
                            <th>Notas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $row) : ?>
                        <tr>
                            <td><?= esc($fmtDate($row['created_at'] ?? null)) ?></td>
                            <td><?= esc($row['from_status'] ?? '—') ?></td>
                            <td><?= esc($row['to_status'] ?? '') ?></td>
                            <td><?= esc($row['notes'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>