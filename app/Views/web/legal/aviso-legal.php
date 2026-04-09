<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/WEB/LEGAL/AVISO-LEGAL.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?= $this->extend('layouts/main') ?>

<?php $this->setVar('pageTitle', $title ?? 'Aviso Legal') ?>

<?= $this->section('content') ?>

<article class="legal-page">
    <header class="legal-page__hero">
        <div class="container">
            <nav aria-label="Migas de pan" class="legal-page__nav">
                <ol class="breadcrumb legal-page__breadcrumb mb-0 justify-content-center">
                    <li class="breadcrumb-item">
                        <a href="<?= esc(base_url('/')) ?>"><?= esc('Inicio') ?></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc('Aviso legal') ?></li>
                </ol>
            </nav>
            <h1 class="legal-page__title"><?= esc('Aviso legal') ?></h1>
            <p class="legal-page__lead">Información sobre el titular del sitio, condiciones de uso y propiedad intelectual.</p>
        </div>
    </header>

    <div class="legal-page__body">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-7">
                    <div class="legal-page__paper">
                        <h2 class="legal-page__section-title"><?= esc('1. Titular del sitio web') ?></h2>
                        <p class="legal-page__text">
                            <strong>nmonzzon Studio</strong><br>
                            Vigo, España<br>
                            Correo: <a href="mailto:nmonzzon@hotmail.com" class="legal-page__link">nmonzzon@hotmail.com</a>
                        </p>

                        <h2 class="legal-page__section-title"><?= esc('2. Condiciones de uso') ?></h2>
                        <p class="legal-page__text">
                            El acceso y uso de este sitio web está sujeto a las presentes condiciones. Al acceder, el usuario acepta cumplir con estas condiciones.
                        </p>

                        <h2 class="legal-page__section-title"><?= esc('3. Propiedad intelectual') ?></h2>
                        <p class="legal-page__text">
                            Todos los contenidos de este sitio web (imágenes, diseños, textos, logotipos, ilustraciones) son propiedad de nmonzzon Studio o se usan con licencia. Queda prohibida su reproducción sin autorización.
                        </p>

                        <h2 class="legal-page__section-title"><?= esc('4. Limitación de responsabilidad') ?></h2>
                        <p class="legal-page__text">
                            nmonzzon Studio no se responsabiliza de los daños derivados del uso de este sitio web, interrupciones del servicio o errores en los contenidos.
                        </p>

                        <h2 class="legal-page__section-title"><?= esc('5. Legislación aplicable') ?></h2>
                        <p class="legal-page__text legal-page__text--last">
                            Este aviso legal se rige por la legislación española. Para cualquier controversia, las partes se someten a los juzgados de Vigo.
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