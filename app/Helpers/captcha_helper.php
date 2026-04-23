<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * HELPER: APP/HELPERS/CAPTCHA_HELPER.PHP
 * =============================================================================
 * QUÉ HACE: CAPTCHA DE PUZZLE DESLIZANTE (SLIDER). EL SERVIDOR GUARDA LA POSICIÓN
 *           SECRETA DE UNA PIEZA RECORTADA DE LA IMAGEN; EL CLIENTE DEBE ALINEARLA
 *           ARRASTRANDO EL SLIDER.
 * POR QUÉ AQUÍ: EL TOKEN + LAS COORDENADAS SE ALMACENAN EN SESIÓN; LAS IMÁGENES
 *           SE RENDERIZAN ON-DEMAND VÍA CAPTCHACONTROLLER USANDO EL SEED GUARDADO.
 * =============================================================================
 */

if (! defined('NMZ_CAPTCHA_WIDTH'))       { define('NMZ_CAPTCHA_WIDTH', 320); }
if (! defined('NMZ_CAPTCHA_HEIGHT'))      { define('NMZ_CAPTCHA_HEIGHT', 180); }
if (! defined('NMZ_CAPTCHA_PIECE_SIZE'))  { define('NMZ_CAPTCHA_PIECE_SIZE', 48); }
if (! defined('NMZ_CAPTCHA_TOLERANCE'))   { define('NMZ_CAPTCHA_TOLERANCE', 6); }
if (! defined('NMZ_CAPTCHA_TTL'))         { define('NMZ_CAPTCHA_TTL', 600); }
if (! defined('NMZ_CAPTCHA_BAG_MAX'))     { define('NMZ_CAPTCHA_BAG_MAX', 20); }

if (! function_exists('nmz_captcha_generate')) {
    /**
     * Genera un nuevo desafío de puzzle y guarda sus datos secretos en sesión.
     *
     * @return array{
     *     token: string,
     *     bg_url: string,
     *     piece_url: string,
     *     width: int,
     *     height: int,
     *     piece_size: int,
     *     piece_y: int
     * }
     */
    function nmz_captcha_generate(): array
    {
        $w     = (int) NMZ_CAPTCHA_WIDTH;
        $h     = (int) NMZ_CAPTCHA_HEIGHT;
        $size  = (int) NMZ_CAPTCHA_PIECE_SIZE;
        $token = bin2hex(random_bytes(10));

        // Coordenada secreta: deja margen a la izquierda para que la pieza
        // empiece visible en X=0 y pueda deslizarse hacia la solución.
        $minX = (int) round($size * 1.4);
        $maxX = $w - $size - 8;
        $x    = random_int($minX, max($minX, $maxX));
        $y    = random_int(8, max(8, $h - $size - 8));
        $seed = random_int(1, 2147483647);

        $session = session();
        $bag     = (array) ($session->get('nmz_captcha_bag') ?? []);

        $now = time();
        foreach ($bag as $k => $entry) {
            if (! is_array($entry) || ($now - (int) ($entry['t'] ?? 0)) > NMZ_CAPTCHA_TTL) {
                unset($bag[$k]);
            }
        }
        if (count($bag) > NMZ_CAPTCHA_BAG_MAX) {
            $bag = array_slice($bag, -NMZ_CAPTCHA_BAG_MAX, null, true);
        }

        $bag[$token] = [
            'x'    => $x,
            'y'    => $y,
            'size' => $size,
            'w'    => $w,
            'h'    => $h,
            'seed' => $seed,
            't'    => $now,
        ];
        $session->set('nmz_captcha_bag', $bag);

        return [
            'token'      => $token,
            'bg_url'     => site_url('captcha/bg/' . $token),
            'piece_url'  => site_url('captcha/piece/' . $token),
            'width'      => $w,
            'height'     => $h,
            'piece_size' => $size,
            'piece_y'    => $y,
        ];
    }
}

if (! function_exists('nmz_captcha_entry')) {
    /**
     * Devuelve la entrada completa del bag para un token (sin consumirla).
     * Usado por CaptchaController al renderizar las imágenes.
     */
    function nmz_captcha_entry(?string $token): ?array
    {
        $token = trim((string) $token);
        if ($token === '' || ! ctype_xdigit($token)) {
            return null;
        }

        $bag = (array) (session()->get('nmz_captcha_bag') ?? []);
        if (! isset($bag[$token]) || ! is_array($bag[$token])) {
            return null;
        }
        if ((time() - (int) ($bag[$token]['t'] ?? 0)) > NMZ_CAPTCHA_TTL) {
            return null;
        }

        return $bag[$token];
    }
}

if (! function_exists('nmz_captcha_verify')) {
    /**
     * Valida que la X enviada (posición en píxeles de la imagen del servidor)
     * esté dentro de la tolerancia respecto a la X secreta. Consume el token.
     *
     * Se mantienen los nombres de campos previos (captcha_token / captcha_answer)
     * para no tocar las reglas de los controladores existentes; el valor de
     * captcha_answer ahora es la X normalizada (entero en [0, width]).
     */
    function nmz_captcha_verify(?string $token, ?string $answer): bool
    {
        $token = trim((string) $token);
        if ($token === '' || ! ctype_xdigit($token)) {
            return false;
        }
        $answer = trim((string) $answer);
        if (! preg_match('/^\d{1,4}$/', $answer)) {
            return false;
        }

        $session = session();
        $bag     = (array) ($session->get('nmz_captcha_bag') ?? []);
        if (! isset($bag[$token]) || ! is_array($bag[$token])) {
            return false;
        }

        $entry = $bag[$token];
        unset($bag[$token]);
        $session->set('nmz_captcha_bag', $bag);

        if ((time() - (int) ($entry['t'] ?? 0)) > NMZ_CAPTCHA_TTL) {
            return false;
        }

        $targetX   = (int) ($entry['x'] ?? -1);
        $submitted = (int) $answer;

        return abs($submitted - $targetX) <= NMZ_CAPTCHA_TOLERANCE;
    }
}
