<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/CATEGORIES/FORM.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php
$category = $category ?? null;
$isEdit   = is_array($category) && ! empty($category['id']);
$pageTitle = $isEdit ? 'Editar categoría' : 'Nueva categoría';
$this->setVar('pageTitle', $pageTitle);
$old = old();
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title"><?= esc($pageTitle) ?></h1>
    <a href="<?= base_url('admin/categories') ?>" class="btn btn-admin-outline btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="admin-card">
    <div class="card-body">
        <form action="<?= $isEdit ? base_url('admin/categories/' . (int) $category['id']) : base_url('admin/categories') ?>"
              method="post"
              enctype="multipart/form-data"
              class="admin-form">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" required maxlength="100"
                           value="<?= esc($old['name'] ?? $category['name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control" id="slug" name="slug" maxlength="120"
                           value="<?= esc($old['slug'] ?? $category['slug'] ?? '') ?>"
                           placeholder="Se genera desde el nombre">
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea class="form-control" id="description" name="description" rows="4"><?= esc($old['description'] ?? $category['description'] ?? '') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Imagen</label>
                    <div class="upload-zone">
                        <i class="bi bi-cloud-arrow-up d-block"></i>
                        <span class="small text-muted">Arrastra o haz clic para subir</span>
                        <input type="file" name="image" class="upload-zone-input visually-hidden" accept="image/jpeg,image/png,image/gif,image/webp">
                    </div>
                    <?php if ($isEdit && ! empty($category['image'])): ?>
                    <div class="image-preview-grid mt-2">
                        <div class="image-preview-item">
                            <img src="<?= esc(base_url($category['image']), 'attr') ?>" alt="">
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="image-preview-grid"></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <label for="icon" class="form-label">Icono</label>
                    <input type="text" class="form-control" id="icon" name="icon" maxlength="100"
                           value="<?= esc($old['icon'] ?? $category['icon'] ?? '') ?>"
                           placeholder="bi-palette">
                    <div class="form-text">Clase Bootstrap Icons, ej. bi-palette</div>
                </div>
                <div class="col-md-3">
                    <label for="sort_order" class="form-label">Orden</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" step="1"
                           value="<?= esc($old['sort_order'] ?? $category['sort_order'] ?? '0') ?>">
                </div>
                <div class="col-12">
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check">
                        <?php
                        $activeVal = $old['is_active'] ?? ($category['is_active'] ?? 1);
                        $activeOn    = (string) $activeVal === '1' || (int) $activeVal === 1;
                        ?>
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $activeOn ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Activa</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-admin"><?= $isEdit ? 'Actualizar' : 'Crear' ?></button>
                <a href="<?= base_url('admin/categories') ?>" class="btn btn-admin-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>