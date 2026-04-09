<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/CART/SUCCESS.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?php // PÁGINA DE CONFIRMACIÓN TRAS EL PEDIDO: MENSAJE DE ÉXITO, DATOS DEL PEDIDO Y ENLACES A CUENTA O TIENDA ?>
<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', 'Pedido Confirmado') ?>

<?php $order = $order ?? null; ?>

<?= $this->section('content') ?>

<section
    class="page-hero page-hero--tall"
    style="background-image: url('<?= esc(base_url('uploads/retratos/estilos/estilo_color_sin_caras.jpg'), 'attr') ?>');"
>
    <div class="page-hero-overlay" style="background: linear-gradient(180deg, rgba(26,26,26,.55) 0%, rgba(26,26,26,.82) 100%);"></div>
    <div class="container page-hero-content text-center py-5">
        <?= view('partials/nmz-hero-heading', [
            'nmzHeroCrumbs' => [
                ['label' => 'Inicio', 'url' => base_url('/')],
                ['label' => 'Carrito', 'url' => base_url('carrito')],
                ['label' => 'Confirmación', 'url' => null],
            ],
            'nmzHeroTitle' => 'Pedido confirmado',
        ]) ?>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 text-center px-0 px-sm-2">
                <!-- TARJETA CENTRAL CON ICONO, TEXTO Y DETALLES DEL PEDIDO -->
                <div class="dashboard-card p-4 p-md-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size:4rem;"></i>
                    </div>
                    <h2 class="h4 font-heading mb-3">Gracias por tu compra</h2>

                    <?php if ($order): ?>
                    <p class="text-secondary mb-4">
                        Tu número de pedido es <strong><?= esc($order['order_number'] ?? '') ?></strong>.<br>
                        Te hemos enviado un email de confirmación.
                    </p>
                    <div class="bg-light rounded-3 p-4 mb-4 text-start">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <small class="text-secondary d-block">Total</small>
                                <strong><?= number_format((float)($order['total'] ?? 0), 2, ',', '.') ?> €</strong>
                            </div>
                            <div class="col-12 col-sm-6">
                                <small class="text-secondary d-block">Estado</small>
                                <span class="badge bg-warning text-dark"><?= esc(ucfirst($order['payment_status'] ?? 'pending')) ?></span>
                            </div>
                            <div class="col-12 col-sm-6">
                                <small class="text-secondary d-block">Envío a</small>
                                <span class="text-break"><?= esc(($order['shipping_city'] ?? '') . ', ' . ($order['shipping_country'] ?? '')) ?></span>
                            </div>
                            <div class="col-12 col-sm-6">
                                <small class="text-secondary d-block">Fecha</small>
                                <span><?= date('d/m/Y H:i', strtotime($order['created_at'] ?? 'now')) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <p class="text-secondary mb-4">Tu pedido ha sido registrado correctamente.</p>
                    <?php endif; ?>

                    <!-- ACCIONES POST-COMPRA -->
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <a href="<?= base_url('mi-cuenta/pedidos') ?>" class="btn btn-nmz">Ver mis pedidos</a>
                        <a href="<?= base_url('productos') ?>" class="btn btn-outline-secondary">Seguir comprando</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>