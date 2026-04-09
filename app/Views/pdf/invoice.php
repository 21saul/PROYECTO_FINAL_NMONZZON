<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/PDF/INVOICE.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?php // PLANTILLA HTML PARA FACTURA EN PDF: CABECERA, EMISOR Y CLIENTE, TABLA DE LÍNEAS, TOTALES Y TEXTO LEGAL ?>
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

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items thead th { background: #1a1a1a; color: #ffffff; padding: 10px 12px; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; text-align: left; }
        table.items thead th:last-child,
        table.items thead th:nth-child(3),
        table.items thead th:nth-child(4) { text-align: right; }
        table.items tbody td { padding: 10px 12px; border-bottom: 1px solid #e8e8e8; font-size: 11px; }
        table.items tbody td:last-child,
        table.items tbody td:nth-child(3),
        table.items tbody td:nth-child(4) { text-align: right; }
        table.items tbody tr:nth-child(even) { background: #fafafa; }

        .totals-wrap { display: table; width: 100%; margin-bottom: 30px; }
        .totals-left { display: table-cell; width: 55%; vertical-align: top; }
        .totals-right { display: table-cell; width: 45%; vertical-align: top; }
        .totals-box { background: #f8f7f5; padding: 15px; }
        .totals-row { display: table; width: 100%; margin-bottom: 6px; }
        .totals-row .label { display: table-cell; text-align: left; color: #6b6b6b; }
        .totals-row .value { display: table-cell; text-align: right; }
        .totals-row.grand { border-top: 2px solid #c9a96e; padding-top: 10px; margin-top: 10px; }
        .totals-row.grand .label,
        .totals-row.grand .value { font-size: 16px; font-weight: bold; color: #1a1a1a; }

        .legal { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e8e8e8; }
        .legal p { font-size: 9px; color: #999; line-height: 1.6; }

        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #aaa; }
    </style>
</head>
<body>
<div class="page">
    <!-- CABECERA DEL DOCUMENTO: MARCA Y METADATOS DE LA FACTURA -->
    <div class="header">
        <div class="header-left">
            <div class="brand-name">nmonzzon Studio</div>
            <div class="brand-sub">Arte · Diseño · Creatividad</div>
        </div>
        <div class="header-right">
            <div class="doc-type">FACTURA</div>
            <div class="doc-meta">
                <strong>Nº:</strong> <?= esc($order_number ?? '') ?><br>
                <strong>Fecha:</strong> <?= date('d/m/Y', strtotime($created_at ?? 'now')) ?><br>
                <?php if (!empty($paid_at)): ?>
                <strong>Pagado:</strong> <?= date('d/m/Y', strtotime($paid_at)) ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- EMISOR Y DATOS DEL CLIENTE -->
    <div class="parties">
        <div class="party">
            <div class="party-label">Emisor</div>
            <p><strong>nmonzzon Studio</strong></p>
            <p>CIF: B12345678</p>
            <p>Calle del Arte 42, 1ºA</p>
            <p>36201 Vigo, Pontevedra, España</p>
            <p>hola@nmonzzon.com</p>
        </div>
        <div class="party">
            <div class="party-label">Cliente</div>
            <p><strong><?= esc($shipping_name ?? $customer_name ?? '') ?></strong></p>
            <?php if (!empty($customer_email)): ?>
            <p><?= esc($customer_email) ?></p>
            <?php endif; ?>
            <p><?= esc($shipping_address ?? '') ?></p>
            <p><?= esc(($shipping_postal_code ?? '') . ' ' . ($shipping_city ?? '')) ?></p>
            <p><?= esc($shipping_country ?? 'España') ?></p>
            <?php if (!empty($shipping_phone)): ?>
            <p>Tel: <?= esc($shipping_phone) ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- DETALLE DE PRODUCTOS Y CANTIDADES -->
    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio ud.</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (($items ?? []) as $item): ?>
            <tr>
                <td>
                    <?= esc($item['product_name'] ?? $item['name'] ?? '') ?>
                    <?php if (!empty($item['variant_info'])): ?>
                    <br><small style="color:#999;"><?= esc($item['variant_info']) ?></small>
                    <?php endif; ?>
                </td>
                <td style="text-align:center;"><?= (int)($item['quantity'] ?? 1) ?></td>
                <td><?= number_format((float)($item['unit_price'] ?? $item['price'] ?? 0), 2) ?> €</td>
                <td><?= number_format((float)($item['total_price'] ?? (($item['unit_price'] ?? $item['price'] ?? 0) * ($item['quantity'] ?? 1))), 2) ?> €</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- SUBTOTAL, DESCUENTOS, IVA Y TOTAL -->
    <div class="totals-wrap">
        <div class="totals-left"></div>
        <div class="totals-right">
            <div class="totals-box">
                <div class="totals-row">
                    <span class="label">Subtotal</span>
                    <span class="value"><?= number_format((float)($subtotal ?? 0), 2) ?> €</span>
                </div>
                <?php if (!empty($discount) && (float)$discount > 0): ?>
                <div class="totals-row">
                    <span class="label">Descuento<?= !empty($coupon_code) ? ' (' . esc($coupon_code) . ')' : '' ?></span>
                    <span class="value" style="color:#28a745;">-<?= number_format((float)$discount, 2) ?> €</span>
                </div>
                <?php endif; ?>
                <div class="totals-row">
                    <span class="label">Envío</span>
                    <span class="value"><?= (float)($shipping ?? $shipping_cost ?? 0) <= 0 ? 'Gratis' : number_format((float)($shipping ?? $shipping_cost ?? 0), 2) . ' €' ?></span>
                </div>
                <div class="totals-row">
                    <span class="label">Base imponible</span>
                    <span class="value"><?= number_format((float)($subtotal ?? 0) - (float)($discount ?? 0), 2) ?> €</span>
                </div>
                <div class="totals-row">
                    <span class="label">IVA (21%)</span>
                    <span class="value"><?= number_format((float)($tax ?? 0), 2) ?> €</span>
                </div>
                <div class="totals-row grand">
                    <span class="label">TOTAL</span>
                    <span class="value"><?= number_format((float)($total ?? 0), 2) ?> €</span>
                </div>
            </div>
        </div>
    </div>

    <!-- AVISOS LEGALES Y FORMA DE PAGO -->
    <div class="legal">
        <p>
            Forma de pago: Tarjeta de crédito (Stripe).<br>
            En cumplimiento del Reglamento (UE) 2016/679, le informamos de que sus datos personales serán tratados por nmonzzon Studio con la finalidad de gestionar la relación comercial.<br>
            Esta factura sirve como justificante de compra. Para devoluciones, consulte nuestra política en nmonzzon.com/legal.
        </p>
    </div>

    <!-- PIE CON DATOS FISCALES DE CONTACTO -->
    <div class="footer">
        <p>nmonzzon Studio · CIF B12345678 · Vigo, España · hola@nmonzzon.com · nmonzzon.com</p>
    </div>
</div>
</body>
</html>