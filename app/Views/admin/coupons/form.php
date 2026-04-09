<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/COUPONS/FORM.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php
$coupon  = $coupon ?? null;
$isEdit  = is_array($coupon) && ! empty($coupon['id']);
$pageTitle = $isEdit ? 'Editar cupón' : 'Nuevo cupón';
$this->setVar('pageTitle', $pageTitle);
$old = old();
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title"><?= esc($pageTitle) ?></h1>
    <a href="<?= base_url('admin/coupons') ?>" class="btn btn-admin-outline btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="admin-card">
    <div class="card-body">
        <form action="<?= $isEdit ? base_url('admin/coupons/' . (int) $coupon['id']) : base_url('admin/coupons') ?>"
              method="post"
              class="admin-form">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="code" class="form-label">Código</label>
                    <input type="text" class="form-control text-uppercase" id="code" name="code" required maxlength="50"
                           value="<?= esc($old['code'] ?? $coupon['code'] ?? '') ?>"
                           style="text-transform:uppercase">
                </div>
                <div class="col-md-4">
                    <label for="type" class="form-label">Tipo</label>
                    <select class="form-select" id="type" name="type" required>
                        <?php $tp = $old['type'] ?? $coupon['type'] ?? 'percentage'; ?>
                        <option value="percentage" <?= $tp === 'percentage' ? 'selected' : '' ?>>Porcentaje</option>
                        <option value="fixed" <?= $tp === 'fixed' ? 'selected' : '' ?>>Importe fijo</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="value" class="form-label">Valor</label>
                    <input type="text" class="form-control" id="value" name="value" required inputmode="decimal"
                           value="<?= esc($old['value'] ?? $coupon['value'] ?? '') ?>"
                           placeholder="Ej. 10 o 5.50">
                </div>
                <div class="col-md-4">
                    <label for="min_purchase" class="form-label">Compra mínima</label>
                    <input type="text" class="form-control" id="min_purchase" name="min_purchase" inputmode="decimal"
                           value="<?= esc($old['min_purchase'] ?? $coupon['min_purchase'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label for="max_uses" class="form-label">Usos máximos</label>
                    <input type="number" class="form-control" id="max_uses" name="max_uses" min="0" step="1"
                           value="<?= esc($old['max_uses'] ?? $coupon['max_uses'] ?? '') ?>"
                           placeholder="Vacío = ilimitado">
                </div>
                <div class="col-md-4">
                    <label for="valid_from" class="form-label">Válido desde</label>
                    <input type="date" class="form-control" id="valid_from" name="valid_from"
                           value="<?= esc($old['valid_from'] ?? (! empty($coupon['valid_from']) ? substr((string) $coupon['valid_from'], 0, 10) : '')) ?>">
                </div>
                <div class="col-md-4">
                    <label for="valid_until" class="form-label">Válido hasta</label>
                    <input type="date" class="form-control" id="valid_until" name="valid_until"
                           value="<?= esc($old['valid_until'] ?? (! empty($coupon['valid_until']) ? substr((string) $coupon['valid_until'], 0, 10) : '')) ?>">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <input type="hidden" name="is_active" value="0">
                    <div class="form-check mb-0">
                        <?php
                        $ac = $old['is_active'] ?? ($coupon['is_active'] ?? 1);
                        $acOn = (string) $ac === '1' || (int) $ac === 1;
                        ?>
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= $acOn ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Activo</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-admin"><?= $isEdit ? 'Actualizar' : 'Crear' ?></button>
                <a href="<?= base_url('admin/coupons') ?>" class="btn btn-admin-outline">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>