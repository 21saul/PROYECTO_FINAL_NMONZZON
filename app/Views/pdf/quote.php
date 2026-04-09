<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/PDF/QUOTE.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?php // PLANTILLA HTML PARA PRESUPUESTO EN PDF: EVENTO, PRESTADOR, CLIENTE, DESGLOSE ECONÓMICO Y CONDICIONES ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <!-- ESTILOS INCORPORADOS PARA RENDERIZADO EN PDF -->
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }
        .page { padding: 40px; }

        .header { display: table; width: 100%; margin-bottom: 30px; border-bottom: 3px solid #c9a96e; padding-bottom: 20px; }
        .header-left { display: table-cell; width: 60%; vertical-align: top; }
        .header-right { display: table-cell; width: 40%; vertical-align: top; text-align: right; }
        .brand-name { font-size: 28px; font-weight: bold; color: #1a1a1a; letter-spacing: 1px; margin-bottom: 4px; }
        .brand-sub { font-size: 10px; color: #6b6b6b; text-transform: uppercase; letter-spacing: 2px; }
        .doc-type { font-size: 22px; font-weight: bold; color: #c9a96e; margin-bottom: 8px; }
        .doc-meta { font-size: 10px; color: #6b6b6b; }
        .doc-meta strong { color: #1a1a1a; }

        .parties { display: table; width: 100%; margin-bottom: 30px; }
        .party { display: table-cell; width: 50%; vertical-align: top; }
        .party-label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #c9a96e; font-weight: bold; margin-bottom: 6px; }
        .party p { margin: 2px 0; font-size: 11px; }

        .section-title { font-size: 13px; font-weight: bold; color: #1a1a1a; border-bottom: 2px solid #c9a96e; padding-bottom: 5px; margin: 25px 0 12px; }

        table.details { width: 100%; margin-bottom: 15px; }
        table.details td { padding: 5px 0; font-size: 11px; vertical-align: top; }
        table.details td:first-child { color: #6b6b6b; width: 40%; }
        table.details td:last-child { font-weight: 500; }

        .price-box { background: #f8f7f5; padding: 20px; margin: 25px 0; }
        .price-row { display: table; width: 100%; margin-bottom: 8px; }
        .price-row .label { display: table-cell; text-align: left; color: #6b6b6b; }
        .price-row .value { display: table-cell; text-align: right; }
        .price-row.total { border-top: 2px solid #c9a96e; padding-top: 12px; margin-top: 12px; }
        .price-row.total .label,
        .price-row.total .value { font-size: 18px; font-weight: bold; color: #1a1a1a; }

        .conditions { margin-top: 25px; }
        .conditions h4 { font-size: 11px; font-weight: bold; color: #1a1a1a; margin-bottom: 8px; }
        .conditions ul { padding-left: 15px; }
        .conditions li { font-size: 10px; color: #6b6b6b; margin-bottom: 4px; line-height: 1.5; }

        .validity { margin-top: 20px; padding: 12px; background: #fffbf0; border: 1px solid #c9a96e; text-align: center; font-size: 11px; }
        .validity strong { color: #c9a96e; }

        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #aaa; border-top: 1px solid #e8e8e8; padding-top: 15px; }
    </style>
</head>
<body>
<div class="page">
    <!-- CABECERA: MARCA, TIPO DE DOCUMENTO Y REFERENCIAS -->
    <div class="header">
        <div class="header-left">
            <div class="brand-name">nmonzzon Studio</div>
            <div class="brand-sub">Arte en Vivo · Eventos</div>
        </div>
        <div class="header-right">
            <div class="doc-type">PRESUPUESTO</div>
            <div class="doc-meta">
                <strong>Ref:</strong> <?= esc($booking_number ?? 'LAB-' . date('Ymd')) ?><br>
                <strong>Fecha:</strong> <?= date('d/m/Y') ?><br>
                <strong>Válido hasta:</strong> <?= date('d/m/Y', strtotime('+30 days')) ?>
            </div>
        </div>
    </div>

    <!-- PRESTADOR DEL SERVICIO Y CLIENTE -->
    <div class="parties">
        <div class="party">
            <div class="party-label">Prestador del servicio</div>
            <p><strong>nmonzzon Studio</strong></p>
            <p>CIF: B12345678</p>
            <p>Calle del Arte 42, 1ºA</p>
            <p>36201 Vigo, Pontevedra, España</p>
            <p>hola@nmonzzon.com</p>
        </div>
        <div class="party">
            <div class="party-label">Cliente</div>
            <p><strong><?= esc($contact_name ?? '') ?></strong></p>
            <p><?= esc($contact_email ?? '') ?></p>
            <?php if (!empty($contact_phone)): ?>
            <p>Tel: <?= esc($contact_phone) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- INFORMACIÓN DEL EVENTO Y NOTAS -->
    <div class="section-title">Detalles del evento</div>
    <table class="details">
        <tr>
            <td>Tipo de evento</td>
            <td><?= esc($event_type ?? '') ?></td>
        </tr>
        <tr>
            <td>Fecha del evento</td>
            <td><?= !empty($event_date) ? date('d/m/Y', strtotime($event_date)) : 'Por confirmar' ?></td>
        </tr>
        <tr>
            <td>Ubicación</td>
            <td><?= esc(($event_location ?? '') . (!empty($event_city) ? ', ' . $event_city : '')) ?></td>
        </tr>
        <tr>
            <td>Número de invitados</td>
            <td><?= esc($num_guests ?? 'N/D') ?></td>
        </tr>
        <?php if (!empty($duration_hours)): ?>
        <tr>
            <td>Duración estimada</td>
            <td><?= esc($duration_hours) ?> horas</td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($notes)): ?>
        <tr>
            <td>Notas</td>
            <td><?= esc($notes) ?></td>
        </tr>
        <?php endif; ?>
    </table>

    <!-- IMPORTES, IVA Y TOTAL ESTIMADO -->
    <div class="section-title">Desglose económico</div>
    <div class="price-box">
        <div class="price-row">
            <span class="label">Tarifa base de servicio</span>
            <span class="value"><?= number_format((float)($base_rate ?? 0), 2) ?> €</span>
        </div>
        <?php if (!empty($travel_fee) && (float)$travel_fee > 0): ?>
        <div class="price-row">
            <span class="label">Coste de desplazamiento</span>
            <span class="value"><?= number_format((float)$travel_fee, 2) ?> €</span>
        </div>
        <?php endif; ?>
        <?php if (!empty($extras_fee) && (float)$extras_fee > 0): ?>
        <div class="price-row">
            <span class="label">Extras / Complementos</span>
            <span class="value"><?= number_format((float)$extras_fee, 2) ?> €</span>
        </div>
        <?php endif; ?>
        <div class="price-row">
            <span class="label">Subtotal (sin IVA)</span>
            <span class="value"><?= number_format((float)($subtotal_quote ?? ($base_rate ?? 0) + ($travel_fee ?? 0) + ($extras_fee ?? 0)), 2) ?> €</span>
        </div>
        <div class="price-row">
            <span class="label">IVA (21%)</span>
            <span class="value"><?= number_format((float)($tax_quote ?? (($base_rate ?? 0) + ($travel_fee ?? 0) + ($extras_fee ?? 0)) * 0.21), 2) ?> €</span>
        </div>
        <div class="price-row total">
            <span class="label">TOTAL ESTIMADO</span>
            <span class="value"><?= number_format((float)($total_quote ?? 0), 2) ?> €</span>
        </div>
    </div>

    <!-- TÉRMINOS DEL SERVICIO Y POLÍTICA DE CANCELACIÓN -->
    <div class="conditions">
        <h4>Condiciones del servicio</h4>
        <ul>
            <li>Para confirmar la reserva se requiere un depósito del 30% del total.</li>
            <li>El resto se abonará el día del evento o en los 7 días posteriores a la celebración.</li>
            <li>Cancelaciones con más de 15 días de antelación: devolución del 100% del depósito.</li>
            <li>Cancelaciones entre 7-15 días: devolución del 50% del depósito.</li>
            <li>Cancelaciones con menos de 7 días: no se realizará devolución.</li>
            <li>El material artístico está incluido en el precio.</li>
            <li>Se requiere un espacio cubierto y una mesa de al menos 1m × 0.6m.</li>
            <li>Toma de corriente cercana al área de trabajo (máx. 5m).</li>
        </ul>
    </div>

    <!-- VALIDEZ TEMPORAL DEL PRESUPUESTO -->
    <div class="validity">
        Este presupuesto tiene una validez de <strong>30 días</strong> a partir de la fecha de emisión.
    </div>

    <!-- PIE CON DATOS DE CONTACTO -->
    <div class="footer">
        <p>nmonzzon Studio · CIF B12345678 · Vigo, España · hola@nmonzzon.com · nmonzzon.com</p>
    </div>
</div>
</body>
</html>