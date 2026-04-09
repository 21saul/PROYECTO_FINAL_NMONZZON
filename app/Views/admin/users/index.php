<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/USERS/INDEX.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/admin') ?>

<?php $this->setVar('pageTitle', 'Usuarios') ?>

<?php
$users   = $users ?? [];
$filters = $filters ?? ['role' => null];
?>

<?= $this->section('content') ?>

<div class="admin-page-header">
    <h1 class="admin-page-title">Usuarios</h1>
</div>

<div class="admin-filters">
    <form method="get" action="<?= base_url('admin/users') ?>" class="d-flex flex-wrap align-items-center gap-2">
        <select class="form-select" name="role" aria-label="Rol" onchange="this.form.submit()">
            <option value="" <?= ($filters['role'] ?? '') === null || ($filters['role'] ?? '') === '' ? 'selected' : '' ?>>Todos</option>
            <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="client" <?= ($filters['role'] ?? '') === 'client' ? 'selected' : '' ?>>Cliente</option>
        </select>
    </form>
</div>

<div class="admin-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha registro</th>
                        <th>Último login</th>
                        <th>Activo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay usuarios</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <?php $uid = (int) ($u['id'] ?? 0); ?>
                    <tr>
                        <td class="fw-medium"><?= esc($u['name'] ?? '') ?></td>
                        <td class="small"><?= esc($u['email'] ?? '') ?></td>
                        <td>
                            <?php $role = (string) ($u['role'] ?? ''); ?>
                            <span class="badge <?= $role === 'admin' ? 'bg-dark' : 'bg-secondary' ?>"><?= $role === 'admin' ? 'Admin' : 'Cliente' ?></span>
                        </td>
                        <td class="text-muted small"><?= ! empty($u['created_at']) ? esc(date('d/m/Y', strtotime((string) $u['created_at']))) : '—' ?></td>
                        <td class="text-muted small"><?= ! empty($u['last_login_at']) ? esc(date('d/m/Y H:i', strtotime((string) $u['last_login_at']))) : '—' ?></td>
                        <td>
                            <input type="checkbox" class="admin-toggle" role="switch" aria-label="Activo"
                                   data-url="<?= base_url('admin/users/' . $uid . '/toggle') ?>"
                                <?= ! empty($u['is_active']) ? 'checked' : '' ?>>
                        </td>
                        <td class="text-end">
                            <a href="<?= base_url('admin/users/' . $uid) ?>" class="btn btn-sm btn-admin-outline">Ver</a>
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