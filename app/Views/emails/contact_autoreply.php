<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/EMAILS/CONTACT_AUTOREPLY.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"></head>
<body style="font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 8px; overflow: hidden;">
        <tr>
            <td style="background: #1a1a1a; padding: 30px; text-align: center;">
                <h1 style="color: #c9a96e; font-family: 'Georgia', serif; margin: 0;">nmonzzon Studio</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <p style="margin: 0 0 15px;">Hola <?= esc($contact['name'] ?? '') ?>,</p>
                <p style="margin: 0 0 15px;">Hemos recibido tu mensaje y te responderemos lo antes posible.</p>
                <p style="margin: 0 0 5px;"><strong>Tu mensaje:</strong></p>
                <blockquote style="border-left: 3px solid #c9a96e; padding-left: 15px; color: #555; margin: 15px 0;">
                    <?= esc($contact['message'] ?? '') ?>
                </blockquote>
                <p style="margin: 0 0 15px;">Tiempo estimado de respuesta: 24-48 horas laborales.</p>
                <p style="margin: 0;">Un saludo,<br><strong>Equipo nmonzzon</strong></p>
            </td>
        </tr>
        <tr>
            <td style="background: #f5f5f5; padding: 20px; text-align: center; font-size: 12px; color: #999;">
                nmonzzon Studio &mdash; Vigo, España
            </td>
        </tr>
    </table>
</body>
</html>