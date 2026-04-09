<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/EMAILS/LOYALTY_REACTIVATION.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

$base = rtrim((string) config('App')->baseURL, '/');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hace tiempo que no te vemos</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f4f4;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;background-color:#ffffff;border-radius:4px;overflow:hidden;">
                    <tr>
                        <td style="background-color:#1a1a1a;padding:20px 24px;text-align:center;">
                            <span style="color:#ffffff;font-size:20px;font-weight:bold;">nmonzzon Studio</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 24px;color:#333333;font-size:15px;line-height:1.5;">
                            <p style="margin:0 0 16px 0;">Hola <?= esc($client['name'] ?? '') ?>,</p>
                            <p style="margin:0 0 16px 0;">Hace un tiempo que no tenemos noticias tuyas (tu última actividad fue el <?= esc($client['last_activity'] ?? '') ?>). Nos encantaría volver a crear contigo.</p>
                            <p style="margin:0 0 24px 0;">Puedes ver novedades y servicios aquí:</p>
                            <p style="margin:0;">
                                <a href="<?= esc($base) ?>/" style="display:inline-block;background-color:#c9a96e;color:#1a1a1a;text-decoration:none;padding:12px 24px;border-radius:4px;font-weight:bold;">Visitar la web</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>