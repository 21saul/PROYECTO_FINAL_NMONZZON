<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CAPTCHACONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, IMAGEN/JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/CAPTCHACONTROLLER.PHP
 * QUÉ HACE: RENDERIZA EL FONDO Y LA PIEZA DEL CAPTCHA DE PUZZLE CON GD, Y EXPONE
 *           UN ENDPOINT DE REFRESH. LOS DATOS SECRETOS VIVEN EN SESIÓN (VER
 *           CAPTCHA_HELPER).
 * POR QUÉ ASÍ: LA X SECRETA NUNCA SE SERIALIZA AL CLIENTE; SOLO APARECE COMO
 *           PÍXELES DE LA IMAGEN, OBLIGANDO A CV PARA BYPASS.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class CaptchaController extends BaseController
{
    public function background(string $token)
    {
        helper('captcha');
        $entry = nmz_captcha_entry($token);
        if ($entry === null) {
            return $this->response->setStatusCode(404)->setBody('');
        }

        $base = $this->renderBase($entry);
        $this->drawCutout($base, $entry);
        $this->drawDecoys($base, $entry);

        return $this->streamPng($base);
    }

    public function piece(string $token)
    {
        helper('captcha');
        $entry = nmz_captcha_entry($token);
        if ($entry === null) {
            return $this->response->setStatusCode(404)->setBody('');
        }

        $size  = (int) $entry['size'];
        $piece = imagecreatetruecolor($size, $size);
        imagesavealpha($piece, true);
        imagealphablending($piece, false);
        $transparent = imagecolorallocatealpha($piece, 0, 0, 0, 127);
        imagefill($piece, 0, 0, $transparent);

        // COPIA EL RECORTE DEL FONDO ORIGINAL (SIN HUECO NI DECOYS)
        $base = $this->renderBase($entry);
        imagealphablending($piece, true);
        imagecopy($piece, $base, 0, 0, (int) $entry['x'], (int) $entry['y'], $size, $size);
        imagedestroy($base);

        $this->applyPieceMask($piece, $size);
        $this->strokePiece($piece, $size);

        return $this->streamPng($piece);
    }

    /**
     * Devuelve un nuevo desafío (token + URLs) sin recargar la página.
     * Acepta ?previous=<token> para liberar la entrada anterior del bag de sesión.
     * Usa GET para no interferir con la regeneración CSRF del formulario padre.
     */
    public function refresh()
    {
        helper('captcha');

        $previous = (string) $this->request->getGet('previous');
        if ($previous !== '' && ctype_xdigit($previous)) {
            $bag = (array) (session()->get('nmz_captcha_bag') ?? []);
            if (isset($bag[$previous])) {
                unset($bag[$previous]);
                session()->set('nmz_captcha_bag', $bag);
            }
        }

        $data = nmz_captcha_generate();

        return $this->response->setJSON([
            'token'      => $data['token'],
            'bg_url'     => $data['bg_url'],
            'piece_url'  => $data['piece_url'],
            'width'      => $data['width'],
            'height'     => $data['height'],
            'piece_size' => $data['piece_size'],
            'piece_y'    => $data['piece_y'],
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Generación GD
    // ──────────────────────────────────────────────────────────────

    private function renderBase(array $entry)
    {
        $w    = (int) $entry['w'];
        $h    = (int) $entry['h'];
        $seed = (int) $entry['seed'];

        $rng = $this->seededRng($seed);

        $img = imagecreatetruecolor($w, $h);
        imagealphablending($img, true);

        // Gradiente vertical entre dos tonos saturados pero oscuros (mejor contraste con pieza)
        $c1 = [$rng(40, 120), $rng(40, 120), $rng(60, 160)];
        $c2 = [$rng(30, 100), $rng(60, 140), $rng(40, 120)];

        for ($y = 0; $y < $h; $y++) {
            $t = $y / max(1, $h - 1);
            $r = (int) ($c1[0] + ($c2[0] - $c1[0]) * $t);
            $g = (int) ($c1[1] + ($c2[1] - $c1[1]) * $t);
            $b = (int) ($c1[2] + ($c2[2] - $c1[2]) * $t);
            $color = imagecolorallocate($img, $r, $g, $b);
            imageline($img, 0, $y, $w, $y, $color);
        }

        // Blobs suaves para dificultar la detección del hueco por contraste plano
        for ($i = 0; $i < 12; $i++) {
            $cx     = $rng(0, $w);
            $cy     = $rng(0, $h);
            $radius = $rng(22, 70);
            $r      = $rng(140, 240);
            $g      = $rng(140, 240);
            $b      = $rng(140, 240);
            $alpha  = $rng(90, 115);
            $color  = imagecolorallocatealpha($img, $r, $g, $b, $alpha);
            imagefilledellipse($img, $cx, $cy, $radius * 2, $radius * 2, $color);
        }

        // Líneas diagonales tenues (ruido estructural)
        for ($i = 0; $i < 6; $i++) {
            $x1    = $rng(-20, $w);
            $y1    = $rng(-20, $h);
            $x2    = $x1 + $rng(80, 240);
            $y2    = $y1 + $rng(-60, 60);
            $color = imagecolorallocatealpha($img, $rng(200, 255), $rng(200, 255), $rng(200, 255), $rng(100, 120));
            imageline($img, $x1, $y1, $x2, $y2, $color);
        }

        // Ruido de píxeles
        for ($i = 0; $i < 600; $i++) {
            $x = $rng(0, $w - 1);
            $y = $rng(0, $h - 1);
            $v = $rng(0, 255);
            $c = imagecolorallocate($img, $v, $v, $v);
            imagesetpixel($img, $x, $y, $c);
        }

        return $img;
    }

    private function drawCutout(&$img, array $entry): void
    {
        $size = (int) $entry['size'];
        $cx   = (int) ($entry['x'] + $size / 2);
        $cy   = (int) ($entry['y'] + $size / 2);

        // Sombra interior del hueco (semitransparente)
        $shadow = imagecolorallocatealpha($img, 0, 0, 0, 60);
        imagefilledellipse($img, $cx, $cy, $size, $size, $shadow);

        // Borde blanco suave
        $outlineOuter = imagecolorallocatealpha($img, 255, 255, 255, 60);
        imageellipse($img, $cx, $cy, $size, $size, $outlineOuter);

        $outlineInner = imagecolorallocatealpha($img, 0, 0, 0, 90);
        imageellipse($img, $cx, $cy, $size - 2, $size - 2, $outlineInner);
    }

    /**
     * Añade 2 huecos falsos con aspecto similar al real, para dificultar bypass por CV.
     * Se generan con el mismo seed que el fondo para que siempre coincidan al re-renderizar.
     */
    private function drawDecoys(&$img, array $entry): void
    {
        $w    = (int) $entry['w'];
        $h    = (int) $entry['h'];
        $size = (int) $entry['size'];
        $seed = (int) $entry['seed'];
        $rng  = $this->seededRng($seed ^ 0x5bd1e995);

        $realCx = (int) ($entry['x'] + $size / 2);
        $attempts = 0;

        $placed = 0;
        while ($placed < 2 && $attempts < 20) {
            $attempts++;
            $cx = $rng((int) ($size * 0.8), $w - (int) ($size * 0.8));
            $cy = $rng((int) ($size * 0.8), $h - (int) ($size * 0.8));

            // No solapes con el hueco real (distancia mínima > size)
            if (abs($cx - $realCx) < $size + 8) {
                continue;
            }

            $shadow = imagecolorallocatealpha($img, 0, 0, 0, 90);
            imagefilledellipse($img, $cx, $cy, $size, $size, $shadow);

            $outline = imagecolorallocatealpha($img, 255, 255, 255, 95);
            imageellipse($img, $cx, $cy, $size, $size, $outline);

            $placed++;
        }
    }

    private function applyPieceMask(&$piece, int $size): void
    {
        $cx = $size / 2;
        $cy = $size / 2;
        $r  = $size / 2 - 1;

        imagealphablending($piece, false);
        $transparent = imagecolorallocatealpha($piece, 0, 0, 0, 127);

        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $dx = $x - $cx;
                $dy = $y - $cy;
                if (($dx * $dx + $dy * $dy) > ($r * $r)) {
                    imagesetpixel($piece, $x, $y, $transparent);
                }
            }
        }
        imagealphablending($piece, true);
    }

    private function strokePiece(&$piece, int $size): void
    {
        $cx    = (int) ($size / 2);
        $cy    = (int) ($size / 2);
        $white = imagecolorallocatealpha($piece, 255, 255, 255, 40);
        $black = imagecolorallocatealpha($piece, 0, 0, 0, 80);
        imageellipse($piece, $cx, $cy, $size - 2, $size - 2, $white);
        imageellipse($piece, $cx, $cy, $size - 4, $size - 4, $black);
    }

    private function streamPng($img)
    {
        ob_start();
        imagepng($img);
        $bin = (string) ob_get_clean();
        imagedestroy($img);

        return $this->response
            ->setHeader('Content-Type', 'image/png')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setBody($bin);
    }

    /**
     * Devuelve un RNG determinista (LCG) a partir del seed, para que el fondo
     * sea reproducible tanto al renderizar el background como al recortar la pieza.
     */
    private function seededRng(int $seed): callable
    {
        $state = $seed & 0x7fffffff;
        if ($state === 0) {
            $state = 1;
        }

        return static function (int $min, int $max) use (&$state): int {
            // LCG: constantes numéricas de Park-Miller
            $state = ($state * 48271) % 0x7fffffff;
            if ($max <= $min) {
                return $min;
            }

            return $min + ($state % ($max - $min + 1));
        };
    }
}
