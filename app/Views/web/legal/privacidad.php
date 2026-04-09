<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/LEGAL/PRIVACIDAD.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', $title ?? 'Política de Privacidad') ?>

<?= $this->section('content') ?>

<article class="legal-page">
    <header class="legal-page__hero">
        <div class="container">
            <nav aria-label="Migas de pan" class="legal-page__nav">
                <ol class="breadcrumb legal-page__breadcrumb mb-0 justify-content-center">
                    <li class="breadcrumb-item">
                        <a href="<?= esc(base_url('/')) ?>"><?= esc('Inicio') ?></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc('Privacidad') ?></li>
                </ol>
            </nav>
            <h1 class="legal-page__title"><?= esc('Política de privacidad') ?></h1>
            <p class="legal-page__lead">Cómo tratamos tus datos personales y qué derechos tienes.</p>
        </div>
    </header>

    <div class="legal-page__body">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-7">
                    <div class="legal-page__paper">
                        <h2 class="legal-page__section-title"><?= esc('1. Responsable del tratamiento') ?></h2>
                        <p class="legal-page__text">
                            <strong>nmonzzon Studio</strong><br>
                            Vigo, España<br>
                            Correo: <a href="mailto:nmonzzon@hotmail.com" class="legal-page__link">nmonzzon@hotmail.com</a>
                        </p>

                        <h2 class="legal-page__section-title"><?= esc('2. Datos que recopilamos') ?></h2>
                        <p class="legal-page__text">Recopilamos los datos que nos proporcionas directamente:</p>
                        <ul class="legal-page__list">
                            <li>Nombre, email y teléfono (formularios de contacto y registro).</li>
                            <li>Dirección de envío (para pedidos de productos).</li>
                            <li>Datos de pago (procesados directamente por Stripe; no almacenamos datos de tarjeta).</li>
                            <li>Fotografías de referencia (para pedidos de retratos).</li>
                        </ul>

                        <h2 class="legal-page__section-title"><?= esc('3. Finalidad del tratamiento') ?></h2>
                        <ul class="legal-page__list">
                            <li>Gestionar pedidos y solicitudes de servicio.</li>
                            <li>Comunicaciones relacionadas con el servicio contratado.</li>
                            <li>Mejorar la experiencia de usuario en nuestro sitio web.</li>
                        </ul>

                        <h2 class="legal-page__section-title"><?= esc('4. Base legal') ?></h2>
                        <p class="legal-page__text">Consentimiento del interesado y ejecución del contrato de venta o servicio.</p>

                        <h2 class="legal-page__section-title"><?= esc('5. Derechos del usuario') ?></h2>
                        <p class="legal-page__text">
                            Puedes ejercer tus derechos de acceso, rectificación, supresión, portabilidad, limitación y oposición escribiendo a
                            <a href="mailto:nmonzzon@hotmail.com" class="legal-page__link">nmonzzon@hotmail.com</a>.
                        </p>

                        <h2 class="legal-page__section-title"><?= esc('6. Cookies') ?></h2>
                        <p class="legal-page__text legal-page__text--last">
                            Este sitio utiliza cookies técnicas necesarias para el funcionamiento del carrito de compra y la sesión de usuario. No utilizamos cookies de seguimiento de terceros.
                        </p>

                        <p class="legal-page__meta">
                            <time datetime="<?= esc(date('Y-m-d')) ?>">Última actualización: <?= esc(date('d/m/Y')) ?></time>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</article>

<?= $this->endSection() ?>