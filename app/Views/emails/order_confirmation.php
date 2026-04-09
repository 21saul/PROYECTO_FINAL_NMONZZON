<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/EMAILS/ORDER_CONFIRMATION.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?php // CORREO HTML DE CONFIRMACIÓN DE PEDIDO: CABECERA DE MARCA, SALUDO, TABLA DE PEDIDO Y ENLACE AL ÁREA DE CLIENTE ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de pedido</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif;">
    <!-- CONTENEDOR EXTERIOR DEL CORREO -->
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f4f4f4;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;background-color:#ffffff;border-radius:4px;overflow:hidden;">
                    <!-- BANDA SUPERIOR CON NOMBRE DEL ESTUDIO -->
                    <tr>
                        <td style="background-color:#1a1a1a;padding:20px 24px;text-align:center;">
                            <span style="color:#ffffff;font-size:20px;font-weight:bold;">nmonzzon Studio</span>
                        </td>
                    </tr>
                    <!-- CUERPO: TEXTO, DETALLE DEL PEDIDO Y BOTÓN -->
                    <tr>
                        <td style="padding:28px 24px;color:#333333;font-size:15px;line-height:1.5;">
                            <p style="margin:0 0 16px 0;">Hola <?= esc($client['name'] ?? '') ?>,</p>
                            <p style="margin:0 0 20px 0;">Gracias por tu compra. Hemos registrado tu pedido correctamente.</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:20px;border:1px solid #e0e0e0;border-radius:4px;">
                                <tr>
                                    <td style="padding:12px 16px;background-color:#fafafa;font-weight:bold;color:#1a1a1a;">Número de pedido</td>
                                    <td style="padding:12px 16px;color:#333333;">#<?= esc($order['order_number'] ?? '') ?></td>
                                </tr>
                                <?php if (! empty($order['items']) && is_array($order['items'])) : ?>
                                    <tr>
                                        <td colspan="2" style="padding:12px 16px;border-top:1px solid #e0e0e0;">
                                            <strong style="color:#1a1a1a;">Artículos</strong>
                                            <ul style="margin:8px 0 0 0;padding-left:20px;color:#555555;">
                                                <?php foreach ($order['items'] as $item) : ?>
                                                    <li style="margin-bottom:6px;">
                                                        <?= esc(is_array($item) ? ($item['name'] ?? '') : (string) $item) ?>
                                                        <?php if (is_array($item) && isset($item['quantity'])) : ?>
                                                            <span style="color:#888888;"> × <?= esc((string) $item['quantity']) ?></span>
                                                        <?php endif; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (isset($order['subtotal'])) : ?>
                                    <tr>
                                        <td style="padding:8px 16px;border-top:1px solid #e0e0e0;color:#666666;">Subtotal</td>
                                        <td style="padding:8px 16px;border-top:1px solid #e0e0e0;text-align:right;"><?= esc(number_format((float) $order['subtotal'], 2, ',', '.')) ?> €</td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (isset($order['shipping_cost']) && (float) $order['shipping_cost'] > 0) : ?>
                                    <tr>
                                        <td style="padding:8px 16px;color:#666666;">Envío</td>
                                        <td style="padding:8px 16px;text-align:right;"><?= esc(number_format((float) $order['shipping_cost'], 2, ',', '.')) ?> €</td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td style="padding:12px 16px;border-top:2px solid #c9a96e;font-weight:bold;color:#1a1a1a;">Total</td>
                                    <td style="padding:12px 16px;border-top:2px solid #c9a96e;text-align:right;font-weight:bold;color:#1a1a1a;">
                                        <?= esc(number_format((float) ($order['total'] ?? 0), 2, ',', '.')) ?> €
                                    </td>
                                </tr>
                            </table>
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:24px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="<?= esc(base_url('mi-cuenta/pedidos'), 'attr') ?>" style="display:inline-block;background-color:#c9a96e;color:#1a1a1a;text-decoration:none;font-weight:bold;padding:14px 28px;border-radius:4px;font-size:15px;">Ver mi pedido</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- PIE DEL CORREO CON DATOS DE CONTACTO -->
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