<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/PORTFOLIO/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Portfolio') ?>

<?php
$works      = $works ?? $items ?? [];
$categories = $categories ?? [];
$styles     = $styles ?? [];
$pager      = $pager ?? null;

$req            = service('request');
$filterCategory = $req->getGet('category_id');
$filterStyle    = $req->getGet('style_tag');

$categoryLabel = static function (array $work, array $cats): string {
    if (! empty($work['category_name'])) {
        return (string) $work['category_name'];
    }
    $cid = (int) ($work['category_id'] ?? 0);
    foreach ($cats as $c) {
        if ((int) ($c['id'] ?? 0) === $cid) {
            return (string) ($c['name'] ?? '—');
        }
    }

    return '—';
};
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Portfolio</h1>
    <a href="<?= base_url('admin/portfolio/create') ?>" class="btn btn-admin">
        <i class="bi bi-plus-lg me-1"></i>Añadir obra
    </a>
</div>

<form class="admin-filters" method="get" action="<?= base_url('admin/portfolio') ?>">
    <select name="category_id" class="form-select" aria-label="Filtrar por categoría" onchange="this.form.submit()">
        <option value="">Todas las categorías</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= esc($cat['id'] ?? '', 'attr') ?>"<?= (string) ($cat['id'] ?? '') === (string) $filterCategory ? ' selected' : '' ?>>
                <?= esc($cat['name'] ?? '') ?>
            </option>
        <?php endforeach; ?>
    </select>
    <select name="style_tag" class="form-select" aria-label="Filtrar por estilo" onchange="this.form.submit()">
        <option value="">Todos los estilos</option>
        <?php foreach ($styles as $st): ?>
            <?php
            $val = is_array($st)
                ? (string) ($st['slug'] ?? $st['style_tag'] ?? $st['name'] ?? $st['id'] ?? '')
                : (string) $st;
            $lab = is_array($st)
                ? (string) ($st['name'] ?? $val)
                : (string) $st;
            if ($val === '') {
                continue;
            }
            ?>
            <option value="<?= esc($val, 'attr') ?>"<?= (string) $filterStyle === $val ? ' selected' : '' ?>>
                <?= esc($lab) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if ($filterCategory !== null && $filterCategory !== '' || $filterStyle !== null && $filterStyle !== ''): ?>
        <a href="<?= base_url('admin/portfolio') ?>" class="btn btn-sm btn-outline-secondary">Limpiar</a>
    <?php endif; ?>
</form>

<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Miniatura</th>
                        <th>Título</th>
                        <th>Categoría</th>
                        <th>Estilo</th>
                        <th>Destacado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($works === []): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">No hay obras en el portfolio.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($works as $w): ?>
                            <?php
                            $thumb = $w['thumbnail_url'] ?? $w['image_url'] ?? '';
                            $thumbUrl = $thumb !== '' ? base_url($thumb) : '';
                            $wid = (int) ($w['id'] ?? 0);
                            ?>
                            <tr>
                                <td class="thumb-cell">
                                    <?php if ($thumbUrl !== ''): ?>
                                        <img src="<?= esc($thumbUrl, 'attr') ?>" alt="" width="48" height="48" loading="lazy">
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-medium"><?= esc($w['title'] ?? '') ?></td>
                                <td><?= esc($categoryLabel($w, $categories)) ?></td>
                                <td><?= esc($w['style_tag'] ?? '—') ?></td>
                                <td>
                                    <input type="checkbox"
                                           class="admin-toggle"
                                           role="switch"
                                           aria-label="Destacado"
                                           data-url="<?= esc(base_url('admin/portfolio/' . $wid . '/toggle'), 'attr') ?>"
                                        <?= ! empty($w['is_featured']) ? ' checked' : '' ?>>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= base_url('admin/portfolio/edit/' . $wid) ?>" class="btn btn-sm btn-admin-outline me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?= base_url('admin/portfolio/delete/' . $wid) ?>" method="post" class="d-inline delete-form">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pager !== null): ?>
        <div class="card-body border-top pt-3">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>