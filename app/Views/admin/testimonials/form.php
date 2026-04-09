<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/TESTIMONIALS/FORM.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php
$testimonial = $testimonial ?? null;
$isEdit      = is_array($testimonial) && ! empty($testimonial['id']);
$pageTitle   = $isEdit ? 'Editar testimonio' : 'Nuevo testimonio';
$this->setVar('pageTitle', $pageTitle);
$old = old();
$ratingVal = (int) ($old['rating'] ?? $testimonial['rating'] ?? 5);
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title"><?= esc($pageTitle) ?></h1>
    <a href="<?= base_url('admin/testimonials') ?>" class="btn btn-admin-outline btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="admin-card">
    <div class="card-body">
        <form action="<?= $isEdit ? base_url('admin/testimonials/' . (int) $testimonial['id']) : base_url('admin/testimonials') ?>"
              method="post"
              enctype="multipart/form-data"
              class="admin-form">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="client_name" class="form-label">Nombre cliente</label>
                    <input type="text" class="form-control" id="client_name" name="client_name" required maxlength="100"
                           value="<?= esc($old['client_name'] ?? $testimonial['client_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="service_type" class="form-label">Tipo servicio</label>
                    <select class="form-select" id="service_type" name="service_type">
                        <?php
                        $st = $old['service_type'] ?? $testimonial['service_type'] ?? '';
                        $opts = [
                            ''            => '— Seleccionar —',
                            'retrato'     => 'Retrato',
                            'arte en vivo' => 'Arte en vivo',
                            'branding'    => 'Branding',
                            'diseño'      => 'Diseño',
                            'eventos'     => 'Eventos',
                            'producto'    => 'Producto',
                        ];
                        foreach ($opts as $val => $label):
                        ?>
                        <option value="<?= esc($val) ?>" <?= (string) $st === (string) $val ? 'selected' : '' ?>><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Imagen</label>
                    <div class="upload-zone">
                        <i class="bi bi-cloud-arrow-up d-block"></i>
                        <span class="small text-muted">Foto del cliente</span>
                        <input type="file" name="avatar" class="upload-zone-input visually-hidden" accept="image/jpeg,image/png,image/gif,image/webp">
                    </div>
                    <?php if ($isEdit && ! empty($testimonial['client_image'])): ?>
                    <div class="image-preview-grid mt-2">
                        <div class="image-preview-item">
                            <img src="<?= esc(base_url($testimonial['client_image']), 'attr') ?>" alt="">
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="image-preview-grid"></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label d-block">Rating (1–5)</label>
                    <div class="star-rating-select d-flex align-items-center gap-1 pt-1" id="rating-stars" role="radiogroup" aria-label="Valoración">
                        <?php for ($s = 1; $s <= 5; $s++): ?>
                        <span class="star-rating-option">
                            <input class="visually-hidden" type="radio" name="rating" id="rating_<?= $s ?>" value="<?= $s ?>" <?= $ratingVal === $s ? 'checked' : '' ?>>
                            <label class="mb-0 star-rating-label" for="rating_<?= $s ?>" data-value="<?= $s ?>"><i class="bi bi-star-fill"></i></label>
                        </span>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="col-12">
                    <label for="content" class="form-label">Contenido</label>
                    <textarea class="form-control" id="content" name="content" rows="5" required><?= esc($old['content'] ?? $testimonial['content'] ?? '') ?></textarea>
                </div>
                <div class="col-md-4">
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check">
                        <?php
                        $ia = $old['is_active'] ?? ($testimonial['is_active'] ?? 1);
                        $iaOn = (string) $ia === '1' || (int) $ia === 1;
                        ?>
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $iaOn ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Activo</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <?php
                        $if = $old['is_featured'] ?? ($testimonial['is_featured'] ?? 0);
                        $ifOn = (string) $if === '1' || (int) $if === 1;
                        ?>
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1" <?= $ifOn ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_featured">Destacado</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="sort_order" class="form-label">Orden</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" step="1"
                           value="<?= esc($old['sort_order'] ?? $testimonial['sort_order'] ?? '0') ?>">
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-admin"><?= $isEdit ? 'Actualizar' : 'Crear' ?></button>
                <a href="<?= base_url('admin/testimonials') ?>" class="btn btn-admin-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('extra_css') ?>
<style>
.star-rating-select .star-rating-label { cursor: pointer; font-size: 1.35rem; line-height: 1; }
.star-rating-select .star-rating-label i { color: #dee2e6; transition: color 0.15s; }
.star-rating-select .star-rating-label.is-active i { color: #ffc107; }
</style>
<?= $this->endSection() ?>

<?= $this->section('extra_js') ?>
<script>
(function () {
    const wrap = document.getElementById('rating-stars');
    if (!wrap) return;
    const labels = wrap.querySelectorAll('.star-rating-label');
    function paint() {
        const checked = wrap.querySelector('input[name="rating"]:checked');
        const v = checked ? parseInt(checked.value, 10) : 0;
        labels.forEach(function (lbl) {
            const n = parseInt(lbl.getAttribute('data-value'), 10);
            lbl.classList.toggle('is-active', n <= v);
        });
    }
    wrap.addEventListener('change', paint);
    labels.forEach(function (lbl) {
        lbl.addEventListener('mouseenter', function () {
            const h = parseInt(lbl.getAttribute('data-value'), 10);
            labels.forEach(function (l2) {
                const n = parseInt(l2.getAttribute('data-value'), 10);
                l2.classList.toggle('is-active', n <= h);
            });
        });
    });
    wrap.addEventListener('mouseleave', paint);
    paint();
})();
</script>
<?= $this->endSection() ?>