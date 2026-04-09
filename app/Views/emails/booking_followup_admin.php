<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/EMAILS/BOOKING_FOLLOWUP_ADMIN.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

$base = rtrim((string) config('App')->baseURL, '/');
$adminUrl = $base . '/admin/bookings/' . (int) ($booking['id'] ?? 0);
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
    <title>Seguimiento reserva</title>
</head>
<body style="margin:0;padding:16px;font-family:Arial,sans-serif;color:#333;">
    <p><strong>Recordatorio interno:</strong> la reserva <strong><?= esc($booking['booking_number'] ?? '') ?></strong> sigue en estado <strong>pending</strong>.</p>
    <ul>
        <li>Cliente: <?= esc($booking['contact_name'] ?? '') ?> — <?= esc($booking['contact_email'] ?? '') ?></li>
        <li>Tipo: <?= esc($eventTypeLabels[$typeKey] ?? $typeKey) ?></li>
        <li>Fecha evento: <?= esc($booking['event_date'] ?? '') ?></li>
        <li>Ciudad: <?= esc($booking['event_city'] ?? '') ?></li>
    </ul>
    <p><a href="<?= esc($adminUrl) ?>">Abrir en el panel</a></p>
</body>
</html>