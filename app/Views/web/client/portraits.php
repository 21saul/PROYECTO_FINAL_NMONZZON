<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/CLIENT/PORTRAITS.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Mis Retratos') ?>

<?php
$portraits = $portraits ?? [];

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
?>

<?= $this->section('content') ?>

<section class="nmz-page-header py-4 border-bottom bg-white">
    <div class="container">
        <nav class="nmz-hero-crumbs nmz-hero-crumbs--on-light nmz-hero-crumbs--align-start" aria-label="Migas de pan">
            <ol class="nmz-hero-crumbs__list">
                <li class="nmz-hero-crumbs__item"><a href="<?= esc(base_url('/'), 'attr') ?>">Inicio</a></li>
                <li class="nmz-hero-crumbs__item"><a href="<?= esc(base_url('mi-cuenta'), 'attr') ?>">Mi cuenta</a></li>
                <li class="nmz-hero-crumbs__item"><span aria-current="page">Mis retratos</span></li>
            </ol>
        </nav>
        <h1 class="nmz-page-hero__title nmz-page-hero__title--on-light mt-2">Mis retratos</h1>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <?php if ($portraits === []) : ?>
        <div class="dashboard-card text-center py-5">
            <p class="text-secondary mb-4">Aún no tienes encargos de retrato.</p>
            <a href="<?= esc(base_url('retratos/configurador')) ?>" class="btn btn-nmz">Configurar un retrato</a>
        </div>
        <?php else : ?>
        <div class="row g-4">
            <?php foreach ($portraits as $portrait) : ?>
            <?php
            $pid = (int) ($portrait['id'] ?? 0);
            $styleLine = $portrait['style_name'] ?? null;
            if ($styleLine === null || $styleLine === '') {
                $sid = $portrait['portrait_style_id'] ?? null;
                $styleLine = $sid !== null ? 'Estilo #' . $sid : 'Retrato';
            }
            ?>
            <div class="col-md-6 col-xl-4">
                <article class="dashboard-card h-100 d-flex flex-column shadow-sm">
                    <p class="small text-uppercase text-secondary mb-1">Nº encargo</p>
                    <h3 class="h5 mb-3"><?= esc($portrait['order_number'] ?? '') ?></h3>
                    <p class="small text-secondary mb-2"><?= esc($fmtDate($portrait['created_at'] ?? null)) ?></p>
                    <p class="mb-3 text-secondary"><?= esc((string) $styleLine) ?></p>
                    <div class="mb-3">
                        <span class="<?= esc($portraitStatusBadge($portrait['status'] ?? null), 'attr') ?>">
                            <?= esc($portrait['status'] ?? '') ?>
                        </span>
                    </div>
                    <p class="fw-semibold mb-4"><?= esc(number_format((float) ($portrait['total_price'] ?? 0), 2, ',', ' ')) ?> €</p>
                    <div class="mt-auto">
                        <a href="<?= esc(base_url('mi-cuenta/retratos/' . $pid)) ?>" class="btn btn-nmz w-100">Ver detalle</a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>