<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/EMAILS/BOOKING_CONFIRMATION.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

$eventTypeLabels = [
    'wedding' => 'Boda',
    'corporate' => 'Corporativo',
    'birthday' => 'Cumpleaños',
    'festival' => 'Festival',
    'private' => 'Evento privado',
    'other' => 'Otro',
];
$typeKey = $booking['event_type'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserva recibida</title>
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
                            <p style="margin:0 0 16px 0;">Hola <?= esc($booking['contact_name'] ?? '') ?>,</p>
                            <p style="margin:0 0 20px 0;">Hemos recibido tu solicitud de reserva para <strong style="color:#c9a96e;">Arte en vivo</strong>. Te responderemos en un plazo de <strong>24 a 48 horas</strong> laborables.</p>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #e0e0e0;border-radius:4px;overflow:hidden;">
                                <tr>
                                    <td style="padding:10px 16px;background-color:#fafafa;width:40%;color:#666666;font-size:14px;">Tipo de evento</td>
                                    <td style="padding:10px 16px;color:#1a1a1a;">
                                        <?php if (isset($eventTypeLabels[$typeKey])) : ?>
                                            <?= esc($eventTypeLabels[$typeKey]) ?>
                                        <?php else : ?>
                                            <?= esc($typeKey) ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 16px;border-top:1px solid #e0e0e0;background-color:#fafafa;color:#666666;font-size:14px;">Fecha</td>
                                    <td style="padding:10px 16px;border-top:1px solid #e0e0e0;"><?= esc($booking['event_date'] ?? '') ?></td>
                                </tr>
                                <?php if (! empty($booking['event_start_time']) || ! empty($booking['event_end_time'])) : ?>
                                    <tr>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;background-color:#fafafa;color:#666666;font-size:14px;">Horario</td>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;">
                                            <?= esc(trim(($booking['event_start_time'] ?? '') . (isset($booking['event_end_time']) ? ' – ' . $booking['event_end_time'] : ''))) ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td style="padding:10px 16px;border-top:1px solid #e0e0e0;background-color:#fafafa;color:#666666;font-size:14px;">Ubicación</td>
                                    <td style="padding:10px 16px;border-top:1px solid #e0e0e0;"><?= esc($booking['event_location'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td style="padding:10px 16px;border-top:1px solid #e0e0e0;background-color:#fafafa;color:#666666;font-size:14px;">Ciudad</td>
                                    <td style="padding:10px 16px;border-top:1px solid #e0e0e0;"><?= esc($booking['event_city'] ?? '') ?></td>
                                </tr>
                                <?php if (! empty($booking['event_postal_code'])) : ?>
                                    <tr>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;background-color:#fafafa;color:#666666;font-size:14px;">Código postal</td>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;"><?= esc($booking['event_postal_code']) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <tr>
                                    <td style="padding:10px 16px;border-top:1px solid #e0e0e0;background-color:#fafafa;color:#666666;font-size:14px;">Invitados (aprox.)</td>
                                    <td style="padding:10px 16px;border-top:1px solid #e0e0e0;"><?= esc((string) ($booking['num_guests'] ?? '—')) ?></td>
                                </tr>
                                <?php if (! empty($booking['contact_phone'])) : ?>
                                    <tr>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;background-color:#fafafa;color:#666666;font-size:14px;">Teléfono</td>
                                        <td style="padding:10px 16px;border-top:1px solid #e0e0e0;"><?= esc($booking['contact_phone']) ?></td>
                                    </tr>
                                <?php endif; ?>
                                <?php if (! empty($booking['special_requirements'])) : ?>
                                    <tr>
                                        <td colspan="2" style="padding:12px 16px;border-top:1px solid #e0e0e0;background-color:#fffef8;">
                                            <span style="color:#666666;font-size:14px;display:block;margin-bottom:6px;">Requisitos especiales</span>
                                            <?= esc($booking['special_requirements']) ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                            <p style="margin:24px 0 0 0;color:#555555;font-size:14px;">Si algún dato no es correcto, responde a este correo y lo ajustamos.</p>
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