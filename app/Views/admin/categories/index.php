<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/CATEGORIES/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Categorías') ?>

<?php
$categories = $categories ?? [];
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Categorías</h1>
    <a href="<?= base_url('admin/categories/new') ?>" class="btn btn-admin">
        <i class="bi bi-plus-lg me-1"></i>Nueva categoría
    </a>
</div>

<div class="admin-card mb-4">
    <div class="admin-card-header">
        <h6>Añadir categoría rápida</h6>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/categories') ?>" method="post" enctype="multipart/form-data" class="admin-form row g-3 align-items-end">
            <?= csrf_field() ?>
            <input type="hidden" name="is_active" value="1">
            <div class="col-md-4">
                <label for="inline_name" class="form-label">Nombre</label>
                <input type="text" class="form-control" id="inline_name" name="name" value="<?= esc(old('name')) ?>" required maxlength="100" placeholder="Nombre de la categoría">
            </div>
            <div class="col-md-3">
                <label for="inline_slug" class="form-label">Slug</label>
                <input type="text" class="form-control" id="inline_slug" name="slug" value="<?= esc(old('slug')) ?>" maxlength="120" placeholder="auto desde nombre">
            </div>
            <div class="col-md-2">
                <label for="inline_sort" class="form-label">Orden</label>
                <input type="number" class="form-control" id="inline_sort" name="sort_order" value="<?= esc(old('sort_order', '0')) ?>" step="1">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-admin w-100">Guardar rápido</button>
            </div>
        </form>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h6>Listado</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Slug</th>
                        <th>Orden</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">No hay categorías</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td class="thumb-cell">
                            <?php if (! empty($cat['image'])): ?>
                            <img src="<?= esc(base_url($cat['image']), 'attr') ?>" alt="">
                            <?php else: ?>
                            <div class="d-flex align-items-center justify-content-center bg-light rounded" style="width:48px;height:48px;">
                                <i class="bi bi-image text-muted"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-medium"><?= esc($cat['name'] ?? '') ?></td>
                        <td class="text-muted small"><?= esc($cat['slug'] ?? '') ?></td>
                        <td><?= (int) ($cat['sort_order'] ?? 0) ?></td>
                        <td class="text-end text-nowrap">
                            <a href="<?= base_url('admin/categories/edit/' . (int) ($cat['id'] ?? 0)) ?>" class="btn btn-sm btn-admin-outline me-1" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="<?= base_url('admin/categories/delete/' . (int) ($cat['id'] ?? 0)) ?>" method="post" class="delete-form d-inline">
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
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
(function () {
    const nameInput = document.getElementById('inline_name');
    const slugInput = document.getElementById('inline_slug');
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function () {
            if (slugInput.dataset.touched === '1') return;
            slugInput.value = nameInput.value
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/(^-|-$)/g, '');
        });
        slugInput.addEventListener('input', function () { slugInput.dataset.touched = '1'; });
    }
})();
</script>
<?= $this->endSection() ?>