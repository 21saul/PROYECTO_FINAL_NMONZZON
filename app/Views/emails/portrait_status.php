<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/EMAILS/PORTRAIT_STATUS.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?php // CORREO HTML SOBRE EL ESTADO DEL RETRATO: MENSAJE DE ESTADO, IMÁGENES DE BOCETO O FINAL Y ENLACE A LA CUENTA ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización de tu retrato</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif;">
    <!-- CONTENEDOR EXTERIOR DEL CORREO -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f4f4;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;background-color:#ffffff;border-radius:4px;overflow:hidden;">
                    <!-- CABECERA DE MARCA -->
                    <tr>
                        <td style="background-color:#1a1a1a;padding:20px 24px;text-align:center;">
                            <span style="color:#ffffff;font-size:20px;font-weight:bold;">nmonzzon Studio</span>
                        </td>
                    </tr>
                    <!-- CONTENIDO: ESTADO, REFERENCIA DE PEDIDO E IMÁGENES OPCIONALES -->
                    <tr>
                        <td style="padding:28px 24px;color:#333333;font-size:15px;line-height:1.5;">
                            <p style="margin:0 0 16px 0;">Hola <?= esc($client['name'] ?? '') ?>,</p>
                            <p style="margin:0 0 8px 0;padding:12px 16px;background-color:#faf8f3;border-left:4px solid #c9a96e;color:#1a1a1a;">
                                <?= esc($statusMessage ?? '') ?>
                            </p>
                            <p style="margin:16px 0 8px 0;color:#666666;font-size:13px;">
                                Pedido <strong style="color:#1a1a1a;">#<?= esc($order['order_number'] ?? '') ?></strong>
                                <?php if (! empty($status)) : ?>
                                    · Estado: <strong style="color:#c9a96e;"><?= esc($status) ?></strong>
                                <?php endif; ?>
                            </p>
                            <?php
                            $sketchUrl = ! empty($order['sketch_image']) ? base_url($order['sketch_image']) : '';
                            $finalUrl  = ! empty($order['final_image']) ? base_url($order['final_image']) : '';
                            ?>
                            <?php if ($sketchUrl !== '') : ?>
                                <p style="margin:20px 0 8px 0;font-weight:bold;color:#1a1a1a;">Boceto</p>
                                <p style="margin:0 0 16px 0;">
                                    <img src="<?= esc($sketchUrl, 'attr') ?>" alt="Boceto del retrato" width="560" style="max-width:100%;height:auto;border:1px solid #e0e0e0;border-radius:4px;display:block;">
                                </p>
                            <?php endif; ?>
                            <?php if ($finalUrl !== '') : ?>
                                <p style="margin:20px 0 8px 0;font-weight:bold;color:#1a1a1a;">Retrato final</p>
                                <p style="margin:0 0 16px 0;">
                                    <img src="<?= esc($finalUrl, 'attr') ?>" alt="Retrato final" width="560" style="max-width:100%;height:auto;border:1px solid #e0e0e0;border-radius:4px;display:block;">
                                </p>
                            <?php endif; ?>
                            <?php if (in_array($status ?? '', ['revision', 'delivered'], true)) : ?>
                                <p style="margin:16px 0 0 0;color:#555555;font-size:14px;">
                                    <?php if (($status ?? '') === 'revision') : ?>
                                        Revisa el boceto y, si necesitas cambios, responde a este correo o entra en tu cuenta.
                                    <?php else : ?>
                                        Ya puedes descargar o revisar los detalles del pedido desde tu área de cliente.
                                    <?php endif; ?>
                                </p>
                            <?php endif; ?>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:28px 0 0 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?= esc(base_url('mi-cuenta/retratos'), 'attr') ?>" style="display:inline-block;background-color:#c9a96e;color:#1a1a1a;text-decoration:none;font-weight:bold;padding:14px 28px;border-radius:4px;font-size:15px;">Ver detalles del pedido</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- PIE DEL CORREO -->
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