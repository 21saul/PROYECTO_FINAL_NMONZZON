<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/PORTFOLIO/FORM.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php
$work       = $work ?? null;
$isEdit     = $work !== null && ! empty($work['id']);
$categories = $categories ?? [];
$styles     = $styles ?? [];

$this->setVar('pageTitle', $isEdit ? 'Editar obra' : 'Crear obra');

$action = $isEdit
    ? base_url('admin/portfolio/' . (int) $work['id'])
    : base_url('admin/portfolio');

$existingImage = $isEdit ? (string) ($work['thumbnail_url'] ?? $work['image_url'] ?? '') : '';
$existingUrl   = $existingImage !== '' ? base_url($existingImage) : '';
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title"><?= $isEdit ? 'Editar obra' : 'Crear obra' ?></h1>
    <a href="<?= base_url('admin/portfolio') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="admin-card">
    <div class="card-body">
        <form class="admin-form" action="<?= esc($action, 'attr') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" name="title" required maxlength="200"
                       value="<?= esc(old('title', $work['title'] ?? '')) ?>">
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Seleccionar…</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat['id'] ?? '', 'attr') ?>"
                            <?= (string) old('category_id', $work['category_id'] ?? '') === (string) ($cat['id'] ?? '') ? ' selected' : '' ?>>
                            <?= esc($cat['name'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Descripción</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?= esc(old('description', $work['description'] ?? '')) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="style_tag" class="form-label">Estilo</label>
                <select class="form-select" id="style_tag" name="style_tag">
                    <option value="">—</option>
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
                        <option value="<?= esc($val, 'attr') ?>"
                            <?= (string) old('style_tag', $work['style_tag'] ?? '') === $val ? ' selected' : '' ?>>
                            <?= esc($lab) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Imagen<?= $isEdit ? '' : ' <span class="text-danger">*</span>' ?></label>
                <div class="upload-zone" tabindex="0" role="button" aria-label="Seleccionar imagen">
                    <i class="bi bi-cloud-arrow-up d-block"></i>
                    <span class="small text-muted">Arrastra una imagen o haz clic para elegir</span>
                    <input type="file" class="upload-zone-input visually-hidden" id="image" name="image" accept="image/jpeg,image/png,image/gif,image/webp"<?= $isEdit ? '' : ' required' ?>>
                </div>
                <?php if ($existingUrl !== ''): ?>
                    <div class="image-preview-grid mt-2">
                        <div class="image-preview-item">
                            <img src="<?= esc($existingUrl, 'attr') ?>" alt="Imagen actual">
                        </div>
                    </div>
                    <p class="form-text mb-0">Imagen actual. Sube un archivo para reemplazarla.</p>
                <?php endif; ?>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="form-check mt-2">
                        <input type="hidden" name="is_featured" value="0">
                        <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                            <?= (string) old('is_featured', $isEdit && ! empty($work['is_featured']) ? '1' : '0') === '1' ? ' checked' : '' ?>>
                        <label class="form-check-label" for="is_featured">Destacado</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="sort_order" class="form-label">Orden</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" step="1"
                           value="<?= esc(old('sort_order', (string) ($work['sort_order'] ?? '0'))) ?>">
                </div>
            </div>

            <?php if ($isEdit): ?>
                <div class="form-check mb-3">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                        <?= (string) old('is_active', ($work['is_active'] ?? 1) ? '1' : '0') === '1' ? ' checked' : '' ?>>
                    <label class="form-check-label" for="is_active">Activo (visible en la web)</label>
                </div>
            <?php endif; ?>

            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn btn-admin"><?= $isEdit ? 'Guardar cambios' : 'Crear obra' ?></button>
                <a href="<?= base_url('admin/portfolio') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>