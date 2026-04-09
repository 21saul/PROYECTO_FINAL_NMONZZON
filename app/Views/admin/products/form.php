<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/PRODUCTS/FORM.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php
$product    = $product ?? null;
$isEdit     = is_array($product) && !empty($product['id']);
$categories = $categories ?? [];
$images     = $images ?? [];
$variants   = $variants ?? [];

$pageTitle = $isEdit ? 'Editar producto' : 'Nuevo producto';
$this->setVar('pageTitle', $pageTitle);

$activeChecked = $isEdit
    ? (!empty($product['active']) || (!empty($product['is_active']) && (int)$product['is_active'] === 1))
    : true;

$formAction = $isEdit
    ? base_url('admin/products/' . (int)$product['id'])
    : base_url('admin/products');
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title"><?= esc($isEdit ? 'Editar producto' : 'Nuevo producto') ?></h1>
    <a href="<?= base_url('admin/products') ?>" class="btn btn-admin-outline">Volver al listado</a>
</div>

<form class="admin-form" method="post" action="<?= esc($formAction, 'attr') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="admin-card mb-3">
        <div class="card-body">
            <ul class="nav nav-tabs admin-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-general-link" data-bs-toggle="tab" data-bs-target="#tab-general" type="button" role="tab">General</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-images-link" data-bs-toggle="tab" data-bs-target="#tab-images" type="button" role="tab">Imágenes</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-variants-link" data-bs-toggle="tab" data-bs-target="#tab-variants" type="button" role="tab">Variantes</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-seo-link" data-bs-toggle="tab" data-bs-target="#tab-seo" type="button" role="tab">SEO</button>
                </li>
            </ul>

            <div class="tab-content pt-4">
                <div class="tab-pane fade show active" id="tab-general" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   value="<?= esc($product['name'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="slug" name="slug"
                                   value="<?= esc($product['slug'] ?? '') ?>" placeholder="auto-generado">
                        </div>
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Categoría</label>
                            <select class="form-select" id="category_id" name="category_id">
                                <option value="">— Seleccionar —</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int)($cat['id'] ?? 0) ?>"
                                    <?= (isset($product['category_id']) && (int)$product['category_id'] === (int)($cat['id'] ?? 0)) ? 'selected' : '' ?>>
                                    <?= esc($cat['name'] ?? '') ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="price" class="form-label">Precio (€)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0"
                                   value="<?= esc($product['price'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="compare_price" class="form-label">Precio comparación (€)</label>
                            <input type="number" class="form-control" id="compare_price" name="compare_price" step="0.01" min="0"
                                   value="<?= esc($product['compare_price'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="5"><?= esc($product['description'] ?? '') ?></textarea>
                        </div>
                        <div class="col-12">
                            <label for="short_description" class="form-label">Descripción corta</label>
                            <textarea class="form-control" id="short_description" name="short_description" rows="2"><?= esc($product['short_description'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-4">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control" id="sku" name="sku"
                                   value="<?= esc($product['sku'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0"
                                   value="<?= esc($product['stock'] ?? '0') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="low_stock_alert" class="form-label">Alerta stock bajo</label>
                            <input type="number" class="form-control" id="low_stock_alert" name="low_stock_alert" min="0"
                                   value="<?= esc($product['low_stock_alert'] ?? '5') ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="weight" class="form-label">Peso (kg)</label>
                            <input type="number" class="form-control" id="weight" name="weight" step="0.001" min="0"
                                   value="<?= esc($product['weight'] ?? '') ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="featured" name="featured" value="1"
                                    <?= !empty($product['featured']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="featured">Destacado</label>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1"
                                    <?= $activeChecked ? 'checked' : '' ?>>
                                <label class="form-check-label" for="active">Activo</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-images" role="tabpanel">
                    <label class="form-label">Subir imágenes</label>
                    <div class="upload-zone">
                        <i class="bi bi-cloud-arrow-up d-block"></i>
                        <p class="mb-0 small text-muted">Arrastra imágenes aquí o haz clic para seleccionar</p>
                        <p class="mb-0 small text-muted">Varias imágenes a la vez</p>
                    </div>
                    <input type="file" class="upload-zone-input d-none" name="images[]" accept="image/*" multiple>

                    <p class="form-label mt-4 mb-2">Imágenes actuales</p>
                    <div class="image-preview-grid">
                        <?php foreach ($images as $img): ?>
                        <?php
                            $imgId   = (int)($img['id'] ?? 0);
                            $imgUrl  = $img['url'] ?? $img['path'] ?? $img['image_url'] ?? '';
                            $primary = !empty($img['is_primary']);
                            $prodId  = $isEdit ? (int)$product['id'] : 0;
                        ?>
                        <div class="image-preview-item">
                            <img src="<?= esc($imgUrl) ?>" alt="">
                            <div class="position-absolute bottom-0 start-0 end-0 p-1 bg-dark bg-opacity-75 d-flex align-items-center justify-content-between gap-1">
                                <div class="form-check form-check-inline m-0">
                                    <input class="form-check-input" type="radio" name="primary_image_id" id="primary_<?= $imgId ?>" value="<?= $imgId ?>"
                                           <?= $primary ? 'checked' : '' ?>>
                                    <label class="form-check-label text-white small" for="primary_<?= $imgId ?>">Principal</label>
                                </div>
                            </div>
                            <?php if ($isEdit && $prodId > 0 && $imgId > 0): ?>
                            <button type="submit" class="remove-btn" title="Eliminar" aria-label="Eliminar"
                                    form="delete-product-image-<?= $imgId ?>">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-variants" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="form-label mb-0">Variantes del producto</span>
                        <button type="button" class="btn btn-sm btn-admin-outline" id="add-variant-btn">
                            <i class="bi bi-plus-lg me-1"></i>Añadir variante
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Nombre variante</th>
                                    <th>Valor</th>
                                    <th>Mod. precio</th>
                                    <th>Stock</th>
                                    <th>SKU</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="variant-table-body">
                                <?php foreach ($variants as $idx => $v): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($v['id'])): ?>
                                        <input type="hidden" name="variants[<?= (int)$idx ?>][id]" value="<?= (int)$v['id'] ?>">
                                        <?php endif; ?>
                                        <input type="text" class="form-control form-control-sm" name="variants[<?= (int)$idx ?>][variant_name]"
                                               value="<?= esc($v['variant_name'] ?? $v['name'] ?? '') ?>" placeholder="Ej: Color">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="variants[<?= (int)$idx ?>][variant_value]"
                                               value="<?= esc($v['variant_value'] ?? $v['value'] ?? '') ?>" placeholder="Ej: Rojo">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" name="variants[<?= (int)$idx ?>][price_modifier]" step="0.01"
                                               value="<?= esc($v['price_modifier'] ?? '0') ?>">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" name="variants[<?= (int)$idx ?>][stock]" min="0"
                                               value="<?= esc($v['stock'] ?? '0') ?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" name="variants[<?= (int)$idx ?>][sku]"
                                               value="<?= esc($v['sku'] ?? '') ?>" placeholder="SKU">
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-variant-btn">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane fade" id="tab-seo" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="meta_title" class="form-label">Meta título</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="255"
                                   value="<?= esc($product['meta_title'] ?? '') ?>">
                        </div>
                        <div class="col-12">
                            <label for="meta_description" class="form-label">Meta descripción</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="3"><?= esc($product['meta_description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 justify-content-end">
        <a href="<?= base_url('admin/products') ?>" class="btn btn-admin-outline">Cancelar</a>
        <button type="submit" class="btn btn-admin"><?= $isEdit ? 'Guardar cambios' : 'Crear producto' ?></button>
    </div>
</form>

<?php if ($isEdit && !empty($images)): ?>
<?php $prodId = (int)$product['id']; ?>
<?php foreach ($images as $img): ?>
<?php $imgId = (int)($img['id'] ?? 0); ?>
<?php if ($imgId < 1) {
    continue;
} ?>
<form id="delete-product-image-<?= $imgId ?>" method="post" action="<?= base_url('admin/products/' . $prodId . '/images/' . $imgId . '/delete') ?>" class="d-none delete-form">
    <?= csrf_field() ?>
</form>
<?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>