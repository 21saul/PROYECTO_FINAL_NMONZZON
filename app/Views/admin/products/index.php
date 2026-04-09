<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/PRODUCTS/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Productos') ?>

<?php
$products   = $products ?? [];
$categories = $categories ?? [];
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Productos</h1>
    <a href="<?= base_url('admin/products/create') ?>" class="btn btn-admin">
        <i class="bi bi-plus-lg me-1"></i>Añadir producto
    </a>
</div>

<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Activo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">No hay productos</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <?php
                        $pid        = (int)($product['id'] ?? 0);
                        $stock      = (int)($product['stock'] ?? 0);
                        $thumbRaw   = $product['featured_image'] ?? $product['primary_gallery_image'] ?? $product['image_thumb'] ?? $product['thumbnail_url'] ?? $product['image_url'] ?? '';
                        $thumbRaw   = is_string($thumbRaw) ? trim($thumbRaw) : '';
                        $thumbUrl   = '';
                        if ($thumbRaw !== '') {
                            $thumbUrl = preg_match('#^https?://#i', $thumbRaw) === 1
                                ? $thumbRaw
                                : base_url(ltrim($thumbRaw, '/'));
                        }
                        $catName    = $product['category_name'] ?? '—';
                        $isActive   = !empty($product['active']) || (isset($product['is_active']) && (int)$product['is_active'] === 1);
                        $priceFrom  = (float) ($product['admin_price_from'] ?? $product['price'] ?? 0);
                        $priceTo    = (float) ($product['admin_price_to'] ?? $product['price'] ?? 0);
                        $priceRange = abs($priceTo - $priceFrom) > 0.009;
                    ?>
                    <tr>
                        <td class="thumb-cell">
                            <?php if ($thumbUrl !== ''): ?>
                            <img src="<?= esc($thumbUrl, 'attr') ?>" alt="">
                            <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width:48px;height:48px;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-medium"><?= esc($product['name'] ?? '') ?></td>
                        <td><?= esc($catName) ?></td>
                        <td>
                            <?php if ($priceRange): ?>
                            <span class="text-nowrap">Desde <?= number_format($priceFrom, 2, ',', '.') ?> €</span>
                            <div class="text-muted small text-nowrap">hasta <?= number_format($priceTo, 2, ',', '.') ?> €</div>
                            <div class="text-muted small" style="font-size:0.7rem;">Base <?= number_format((float)($product['price'] ?? 0), 2, ',', '.') ?> €</div>
                            <?php else: ?>
                            <?= number_format($priceFrom, 2, ',', '.') ?> €
                            <?php endif; ?>
                        </td>
                        <td class="<?= $stock < 5 ? 'text-danger fw-semibold' : '' ?>"><?= $stock ?></td>
                        <td>
                            <input type="checkbox"
                                   class="admin-toggle"
                                   role="switch"
                                   aria-label="Activo"
                                   data-url="<?= esc(base_url('admin/products/' . $pid . '/toggle'), 'attr') ?>"
                                   <?= $isActive ? 'checked' : '' ?>>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="<?= base_url('admin/products/' . $pid . '/edit') ?>" class="btn btn-sm btn-outline-secondary" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form class="d-inline delete-form" method="post" action="<?= base_url('admin/products/' . $pid . '/delete') ?>">
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
    <?php if (! empty($pager)): ?>
    <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3">
        <?= $pager->links('default', 'default_full') ?>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>