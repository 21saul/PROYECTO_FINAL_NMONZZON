<?php
/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * VISTA CI4: APP/VIEWS/PARTIALS/CAPTCHA.PHP
 * =============================================================================
 * QUÉ HACE: RENDERIZA EL CAPTCHA DE PUZZLE DESLIZANTE (SLIDER). LA PIEZA SE
 *           ARRASTRA CON EL RATÓN/DEDO HASTA ENCAJARLA EN EL HUECO DEL FONDO.
 * POR QUÍ AQUÍ: MARKUP DESACOPLADO REUTILIZABLE EN TODOS LOS FORMULARIOS PÚBLICOS.
 * =============================================================================
 */

helper('captcha');
$nmzCaptcha = nmz_captcha_generate();
$nmzCaptchaUid = substr($nmzCaptcha['token'], 0, 6);
?>
<div
    class="nmz-captcha nmz-captcha-puzzle"
    data-nmz-captcha-puzzle
    data-refresh-url="<?= esc(site_url('captcha/refresh'), 'attr') ?>"
>
    <div class="nmz-captcha-puzzle__header">
        <i class="bi bi-shield-check" aria-hidden="true"></i>
        <span>Verificación: desliza la pieza hasta encajarla en el hueco.</span>
    </div>

    <?php
    $nmzCaptchaPieceTopPct   = round(100 * $nmzCaptcha['piece_y']    / max(1, $nmzCaptcha['height']), 3);
    $nmzCaptchaPieceWidthPct = round(100 * $nmzCaptcha['piece_size'] / max(1, $nmzCaptcha['width']),  3);
    ?>
    <div
        class="nmz-captcha-puzzle__stage"
        data-captcha-stage
        data-width="<?= (int) $nmzCaptcha['width'] ?>"
        data-height="<?= (int) $nmzCaptcha['height'] ?>"
        data-piece-size="<?= (int) $nmzCaptcha['piece_size'] ?>"
        data-piece-y="<?= (int) $nmzCaptcha['piece_y'] ?>"
        style="--captcha-w: <?= (int) $nmzCaptcha['width'] ?>px; --captcha-ratio-w: <?= (int) $nmzCaptcha['width'] ?>; --captcha-ratio-h: <?= (int) $nmzCaptcha['height'] ?>;"
    >
        <img
            class="nmz-captcha-puzzle__bg"
            data-captcha-bg
            src="<?= esc($nmzCaptcha['bg_url'], 'attr') ?>"
            alt="Desafío visual"
            width="<?= (int) $nmzCaptcha['width'] ?>"
            height="<?= (int) $nmzCaptcha['height'] ?>"
            draggable="false"
        >
        <img
            class="nmz-captcha-puzzle__piece"
            data-captcha-piece
            src="<?= esc($nmzCaptcha['piece_url'], 'attr') ?>"
            alt=""
            width="<?= (int) $nmzCaptcha['piece_size'] ?>"
            height="<?= (int) $nmzCaptcha['piece_size'] ?>"
            style="top: <?= esc($nmzCaptchaPieceTopPct, 'attr') ?>%; width: <?= esc($nmzCaptchaPieceWidthPct, 'attr') ?>%;"
            draggable="false"
        >
        <div class="nmz-captcha-puzzle__status" data-captcha-status aria-live="polite"></div>
    </div>

    <div class="nmz-captcha-puzzle__slider" style="--captcha-w: <?= (int) $nmzCaptcha['width'] ?>px;">
        <div class="nmz-captcha-puzzle__track" data-captcha-track>
            <div class="nmz-captcha-puzzle__progress" data-captcha-progress></div>
            <button
                type="button"
                class="nmz-captcha-puzzle__handle"
                data-captcha-handle
                id="nmz-captcha-handle-<?= esc($nmzCaptchaUid, 'attr') ?>"
                aria-label="Deslizar pieza horizontalmente"
                aria-describedby="nmz-captcha-hint-<?= esc($nmzCaptchaUid, 'attr') ?>"
                aria-valuemin="0"
                aria-valuemax="100"
                aria-valuenow="0"
                role="slider"
            >
                <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <div class="nmz-captcha-puzzle__footer">
        <p id="nmz-captcha-hint-<?= esc($nmzCaptchaUid, 'attr') ?>" class="nmz-captcha-puzzle__hint" data-captcha-hint>
            Mantén pulsado el círculo y arrastra hasta encajar la pieza.
        </p>
        <button type="button" class="nmz-captcha-puzzle__refresh" data-captcha-refresh>
            <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
            <span>Nuevo desafío</span>
        </button>
    </div>

    <input type="hidden" name="captcha_token" data-captcha-token value="<?= esc($nmzCaptcha['token'], 'attr') ?>">
    <input type="hidden" name="captcha_answer" data-captcha-answer value="">
    <noscript>
        <p class="form-text text-danger small mt-2">
            Necesitas JavaScript activado para completar el desafío de seguridad.
        </p>
    </noscript>
</div>
