<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/ERRORS/HTML/ERROR_404.PHP
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
    <title>404 — Página no encontrada | nmonzzon Studio</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --nmz-black: #1a1a1a; --nmz-accent: #c9a96e; --nmz-off-white: #f5f5f0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--nmz-black);
            color: var(--nmz-off-white);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .container { max-width: 500px; padding: 2rem; }
        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            letter-spacing: 0.2em;
            color: var(--nmz-accent);
            text-transform: uppercase;
            margin-bottom: 2rem;
        }
        .code {
            font-family: 'Playfair Display', serif;
            font-size: 8rem;
            font-weight: 700;
            line-height: 1;
            color: var(--nmz-accent);
            opacity: 0.3;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            margin: 1rem 0;
        }
        p { color: #999; margin-bottom: 2rem; line-height: 1.6; }
        .btn {
            display: inline-block;
            padding: 12px 32px;
            border: 1px solid var(--nmz-accent);
            color: var(--nmz-accent);
            text-decoration: none;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            font-size: 0.85rem;
            transition: all 0.3s;
        }
        .btn:hover { background: var(--nmz-accent); color: var(--nmz-black); }
    </style>
</head>
<body>
    <div class="container">
        <div class="brand">nmonzzon</div>
        <div class="code">404</div>
        <h1>Página no encontrada</h1>
        <p>La página que buscas no existe o ha sido movida.</p>
        <a href="/" class="btn">Volver al inicio</a>
    </div>
</body>
</html>