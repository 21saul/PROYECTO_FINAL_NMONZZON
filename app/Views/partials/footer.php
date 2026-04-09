<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/PARTIALS/FOOTER.PHP
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

?>

<?php // PIE DE PÁGINA PÚBLICO ?>
<footer class="footer-nmz position-relative text-white pt-5 pb-3 overflow-hidden" style="background-color: #2d2d2d;">
    <div id="footer-particles" style="position: absolute; inset: 0; z-index: 0; pointer-events: none;"></div>

    <div class="container position-relative" style="z-index: 1;">
        <div class="row g-4 g-lg-5">
            <!-- Columna 1: Servicios -->
            <div class="col-6 col-md-3">
                <h6 class="text-uppercase small fw-semibold mb-3" style="letter-spacing: 0.1em; color: var(--nmz-accent);">Servicios</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="<?= base_url('retratos') ?>" class="link-light link-underline-opacity-0 link-underline-opacity-100-hover">Retratos</a></li>
                    <li class="mb-2"><a href="<?= base_url('arte-en-vivo') ?>" class="link-light link-underline-opacity-0 link-underline-opacity-100-hover">Arte en Vivo</a></li>
                    <li class="mb-2"><a href="<?= base_url('branding') ?>" class="link-light link-underline-opacity-0 link-underline-opacity-100-hover">Branding</a></li>
                    <li class="mb-2"><a href="<?= base_url('diseno') ?>" class="link-light link-underline-opacity-0 link-underline-opacity-100-hover">Diseño</a></li>
                    <li class="mb-0"><a href="<?= base_url('productos') ?>" class="link-light link-underline-opacity-0 link-underline-opacity-100-hover">Productos</a></li>
                </ul>
            </div>

            <!-- Columna 2: Contacto -->
            <div class="col-6 col-md-3">
                <h6 class="text-uppercase small fw-semibold mb-3" style="letter-spacing: 0.1em; color: var(--nmz-accent);">Contacto</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2">
                        <a href="mailto:nmonzzon@hotmail.com" class="link-light link-underline-opacity-0 link-underline-opacity-100-hover text-break d-inline-block">
                            <i class="bi bi-envelope me-1"></i> nmonzzon@hotmail.com
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="tel:+34623964677" class="link-light link-underline-opacity-0 link-underline-opacity-100-hover">
                            <i class="bi bi-telephone me-1"></i> 623 964 677
                        </a>
                    </li>
                    <li class="mb-0" style="color: rgba(255,255,255,0.6);">
                        <i class="bi bi-geo-alt me-1"></i> Vigo, España
                    </li>
                </ul>
            </div>

            <!-- Columna 3: Legal -->
            <div class="col-6 col-md-3">
                <h6 class="text-uppercase small fw-semibold mb-3" style="letter-spacing: 0.1em; color: var(--nmz-accent);">Legal</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="<?= base_url('privacidad') ?>" class="link-light link-underline-opacity-0 link-underline-opacity-100-hover">Política de privacidad</a></li>
                    <li class="mb-0"><a href="<?= base_url('aviso-legal') ?>" class="link-light link-underline-opacity-0 link-underline-opacity-100-hover">Aviso legal</a></li>
                </ul>
            </div>

            <!-- Columna 4: Redes sociales -->
            <div class="col-6 col-md-3">
                <h6 class="text-uppercase small fw-semibold mb-3" style="letter-spacing: 0.1em; color: var(--nmz-accent);">Sígueme</h6>
                <div class="d-flex gap-3">
                    <a href="https://www.instagram.com/nmonzzon/" class="link-light fs-4" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="https://www.tiktok.com/@nmonzzon" class="link-light fs-4" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                        <i class="bi bi-tiktok"></i>
                    </a>
                    <a href="https://wa.me/34623964677" class="link-light fs-4" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                </div>
            </div>
        </div>

        <hr style="border-color: rgba(255,255,255,0.12); margin: 2rem 0 1.5rem;">

        <div class="footer-nmz__legal-bottom text-center">
            <p class="footer-nmz__copyright mb-0">
                &copy; <?= date('Y') ?> nmonzzon Studio. Todos los derechos reservados.
            </p>
            <p class="footer-nmz__encargo-note mb-0 text-break">
                Quien realiza un encargo acepta que el precio incluye impuestos aplicables; no hay devolución ni cambio salvo acuerdo por escrito;
                los datos facilitados pueden usarse para gestionar el encargo; la artista puede elaborar vídeo u otro contenido con el resultado para sus redes, salvo pacto distinto;
                el trabajo no comienza hasta confirmar el pedido y recibir el pago acordado.
                <a href="<?= base_url('aviso-legal') ?>" class="footer-nmz__encargo-link">Más información legal</a>.
            </p>
        </div>
    </div>
</footer>