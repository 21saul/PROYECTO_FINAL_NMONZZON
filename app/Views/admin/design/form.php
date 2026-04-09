<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/DESIGN/FORM.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php
$project = $project ?? null;
$images  = $images ?? [];
$isEdit  = is_array($project) && ! empty($project['id']);
$pageTitle = $isEdit ? 'Editar proyecto de diseño' : 'Nuevo proyecto de diseño';
$this->setVar('pageTitle', $pageTitle);
$formAction = $isEdit ? base_url('admin/design/' . (int) $project['id']) : base_url('admin/design');
$currentType = old('design_type', is_array($project) ? ($project['design_type'] ?? '') : '');
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title"><?= esc($pageTitle) ?></h1>
    <a href="<?= base_url('admin/design') ?>" class="btn btn-admin-outline">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="admin-card">
    <div class="card-body">
        <form action="<?= esc($formAction) ?>" method="post" enctype="multipart/form-data" class="admin-form" novalidate>
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-8">
                    <label for="title" class="form-label">Título</label>
                    <input type="text" name="title" id="title" class="form-control" required maxlength="200"
                           value="<?= esc(old('title', is_array($project) ? ($project['title'] ?? '') : '')) ?>">
                </div>
                <div class="col-md-4">
                    <label for="slug" class="form-label">Slug <span class="text-muted fw-normal">(auto)</span></label>
                    <input type="text" name="slug" id="slug" class="form-control" maxlength="220"
                           value="<?= esc(old('slug', is_array($project) ? ($project['slug'] ?? '') : '')) ?>">
                </div>
                <div class="col-md-6">
                    <label for="design_type" class="form-label">Tipo</label>
                    <select name="design_type" id="design_type" class="form-select">
                        <option value="">— Seleccionar —</option>
                        <?php
                        $types = [
                            'identidad'   => 'Identidad',
                            'packaging'   => 'Packaging',
                            'editorial'   => 'Editorial',
                            'web'         => 'Web',
                            'ilustración' => 'Ilustración',
                            'ilustracion' => 'Ilustración',
                        ];
                        foreach ($types as $val => $label):
                        ?>
                        <option value="<?= esc($val) ?>" <?= $currentType === $val ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check mb-0">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" class="form-check-input"
                            <?= old('is_featured', is_array($project) ? ($project['is_featured'] ?? '') : '') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_featured">Destacado</label>
                    </div>
                </div>
                <div class="col-12">
                    <label for="description" class="form-label">Descripción</label>
                    <textarea name="description" id="description" class="form-control" rows="5"><?= esc(old('description', is_array($project) ? ($project['description'] ?? '') : '')) ?></textarea>
                </div>
                <div class="col-md-4">
                    <label for="sort_order" class="form-label">Orden</label>
                    <input type="number" name="sort_order" id="sort_order" class="form-control" min="0" step="1"
                           value="<?= esc(old('sort_order', is_array($project) ? (string) ($project['sort_order'] ?? 0) : '0')) ?>">
                </div>
            </div>

            <hr class="my-4">

            <div class="mb-4 form-upload-group">
                <label class="form-label">Imagen principal</label>
                <?php
                $feat = is_array($project) ? (string) ($project['featured_image'] ?? '') : '';
                $featSrc = $feat !== '' && str_starts_with($feat, 'http') ? $feat : ($feat !== '' ? base_url($feat) : '');
                ?>
                <?php if ($isEdit && $featSrc !== ''): ?>
                <div class="mb-2">
                    <img src="<?= esc($featSrc) ?>" alt="" class="rounded" style="max-height:120px;width:auto;object-fit:cover;">
                </div>
                <?php endif; ?>
                <div class="upload-zone" tabindex="0" role="button" aria-label="Subir imagen principal">
                    <i class="bi bi-cloud-arrow-up d-block"></i>
                    <span class="small text-muted">Arrastra o haz clic para elegir (máx. 5&nbsp;MB)</span>
                </div>
                <input type="file" name="featured_image" class="upload-zone-input" accept="image/jpeg,image/png,image/gif,image/webp" style="display:none" <?= $isEdit ? '' : 'required' ?>>
                <div class="image-preview-grid"></div>
            </div>

            <div class="mb-0 form-upload-group">
                <label class="form-label">Imágenes adicionales</label>
                <?php if ($isEdit && $images !== []): ?>
                <p class="small text-muted mb-2">Marca para eliminar al guardar.</p>
                <div class="image-preview-grid mb-3">
                    <?php foreach ($images as $img): ?>
                    <div class="image-preview-item">
                        <img src="<?= esc(base_url($img['image_url'] ?? '')) ?>" alt="<?= esc($img['alt_text'] ?? '') ?>">
                        <label class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-75 text-white small mb-0 px-1 py-1 d-flex align-items-center gap-1" style="cursor:pointer;">
                            <input type="checkbox" name="delete_image_ids[]" value="<?= (int) ($img['id'] ?? 0) ?>" class="form-check-input m-0 flex-shrink-0">
                            <span>Eliminar</span>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <div class="upload-zone" tabindex="0" role="button" aria-label="Subir imágenes adicionales">
                    <i class="bi bi-images d-block"></i>
                    <span class="small text-muted">Añadir más imágenes (múltiple)</span>
                </div>
                <input type="file" name="gallery[]" class="upload-zone-input" accept="image/jpeg,image/png,image/gif,image/webp" multiple style="display:none">
                <div class="image-preview-grid"></div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-admin"><?= $isEdit ? 'Guardar cambios' : 'Crear proyecto' ?></button>
                <a href="<?= base_url('admin/design') ?>" class="btn btn-outline-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
(function () {
    document.querySelectorAll('.form-upload-group').forEach(function (group) {
        var zone = group.querySelector('.upload-zone');
        var input = group.querySelector('.upload-zone-input');
        var grid = group.querySelector('.image-preview-grid');
        if (!zone || !input) return;

        zone.addEventListener('click', function () { input.click(); });
        zone.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); }
        });
        ['dragenter', 'dragover'].forEach(function (ev) {
            zone.addEventListener(ev, function (e) { e.preventDefault(); zone.classList.add('dragover'); });
        });
        ['dragleave', 'drop'].forEach(function (ev) {
            zone.addEventListener(ev, function (e) { e.preventDefault(); zone.classList.remove('dragover'); });
        });
        zone.addEventListener('drop', function (e) {
            var dt = e.dataTransfer;
            if (dt.files.length) {
                input.files = dt.files;
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });
        input.addEventListener('change', function () {
            if (!grid) return;
            grid.querySelectorAll('.preview-new').forEach(function (n) { n.remove(); });
            Array.from(input.files).forEach(function (file) {
                if (!file.type.startsWith('image/')) return;
                var reader = new FileReader();
                reader.onload = function (ev) {
                    var div = document.createElement('div');
                    div.className = 'image-preview-item preview-new';
                    div.innerHTML = '<img src="' + ev.target.result + '" alt="Vista previa">';
                    grid.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    });
})();
</script>
<?= $this->endSection() ?>