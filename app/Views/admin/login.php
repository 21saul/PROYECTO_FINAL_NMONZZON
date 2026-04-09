<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ADMIN/LOGIN.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — nmonzzon Studio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --nmz-accent: #c9a96e; --nmz-black: #1a1a1a; }
        body { font-family: 'Inter', sans-serif; background: #f8f9fa; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { max-width: 420px; width: 100%; background: #fff; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); padding: 2.5rem; }
        .login-brand { text-align: center; margin-bottom: 2rem; }
        .login-brand h1 { font-family: 'Playfair Display', serif; font-size: 1.75rem; color: var(--nmz-black); margin-bottom: 0.25rem; }
        .login-brand p { color: #6c757d; font-size: 0.875rem; }
        .form-control:focus { border-color: var(--nmz-accent); box-shadow: 0 0 0 0.2rem rgba(201,169,110,0.15); }
        .btn-login { background: var(--nmz-accent); border: none; color: #fff; padding: 0.65rem; font-weight: 500; border-radius: 8px; width: 100%; }
        .btn-login:hover { background: #b08d4f; color: #fff; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-brand">
            <h1>nmonzzon Studio</h1>
            <p>Panel de Administración</p>
        </div>

        <?php if ($flash = session()->getFlashdata('error')): ?>
        <div class="alert alert-danger py-2 small"><?= esc($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = session()->getFlashdata('success')): ?>
        <div class="alert alert-success py-2 small"><?= esc($flash) ?></div>
        <?php endif; ?>

        <form action="<?= base_url('admin/login') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="email" class="form-label small fw-medium">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email') ?? '') ?>" required autofocus autocomplete="email">
            </div>
            <div class="mb-4">
                <label for="password" class="form-label small fw-medium">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-login">Iniciar sesión</button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= base_url('/') ?>" class="small text-secondary text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Volver al sitio</a>
        </div>
    </div>
</body>
</html>