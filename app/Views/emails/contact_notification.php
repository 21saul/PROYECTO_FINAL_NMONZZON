<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/EMAILS/CONTACT_NOTIFICATION.PHP
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
    <title>Nuevo mensaje de contacto</title>
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
                            <p style="margin:0 0 8px 0;font-size:16px;font-weight:bold;color:#1a1a1a;">Nuevo mensaje desde el formulario de contacto</p>
                            <p style="margin:0 0 20px 0;color:#666666;font-size:13px;">Revisa los datos y responde desde el panel de administración o directamente por correo.</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e0e0e0;border-radius:4px;">
                                <?php if (! empty($contact['name'])) : ?>
                                    <tr>
                                        <td style="padding:10px 16px;background-color:#fafafa;width:32%;color:#666666;font-size:14px;vertical-align:top;">Nombre</td>
                                        <td style="padding:10px 16px;color:#1a1a1a;"><?= esc($contact['name']) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (! empty($contact['email'])) : ?>
                                    <tr>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;background-color:#fafafa;color:#666666;font-size:14px;vertical-align:top;">Email</td>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;"><a href="mailto:<?= esc($contact['email'], 'attr') ?>" style="color:#c9a96e;text-decoration:none;"><?= esc($contact['email']) ?></a></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (! empty($contact['phone'])) : ?>
                                    <tr>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;background-color:#fafafa;color:#666666;font-size:14px;vertical-align:top;">Teléfono</td>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;"><?= esc($contact['phone']) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (! empty($contact['subject'])) : ?>
                                    <tr>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;background-color:#fafafa;color:#666666;font-size:14px;vertical-align:top;">Asunto</td>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;font-weight:bold;color:#1a1a1a;"><?= esc($contact['subject']) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td colspan="2" style="padding:14px 16px;border-top:1px solid #e0e0e0;background-color:#fffef8;">
                                        <span style="display:block;color:#666666;font-size:14px;margin-bottom:8px;">Mensaje</span>
                                        <div style="color:#333333;white-space:pre-wrap;"><?= esc($contact['message'] ?? '') ?></div>
                                    </td>
                                </tr>
                                <?php if (! empty($contact['created_at'])) : ?>
                                    <tr>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;background-color:#fafafa;color:#666666;font-size:14px;">Recibido</td>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;font-size:13px;color:#555555;"><?= esc($contact['created_at']) ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 24px;background-color:#666666;color:#eeeeee;font-size:12px;line-height:1.6;text-align:center;">
                            nmonzzon Studio · hola@nmonzzon.com · Vigo, España
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>