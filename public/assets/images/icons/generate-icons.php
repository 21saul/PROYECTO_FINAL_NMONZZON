<?php
/**
 * Run this script once with: ddev exec php public/assets/images/icons/generate-icons.php
 * It generates PWA placeholder icons using GD.
 */
// ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
$sizes = [72, 96, 128, 144, 192, 384, 512];
// ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
$dir = __DIR__;

// BUCLE FOREACH SOBRE COLECCIÓN
foreach ($sizes as $size) {
    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
    $img = imagecreatetruecolor($size, $size);
    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
    $bg = imagecolorallocate($img, 201, 169, 110); // #c9a96e
    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
    $white = imagecolorallocate($img, 255, 255, 255);
    // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
    imagefill($img, 0, 0, $bg);

    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
    $fontSize = (int) ($size * 0.5);
    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
    $text = 'N';

    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
    $bbox = imagettfbbox($fontSize, 0, __DIR__ . '/../../../../writable/fonts/arial.ttf', $text);
    // CONDICIONAL SI
    if ($bbox === false) {
        // COMENTARIO DE LÍNEA EXISTENTE
        // Fallback: use built-in font
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $fontNum = 5;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $fw = imagefontwidth($fontNum) * strlen($text);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $fh = imagefontheight($fontNum);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $x = (int) (($size - $fw) / 2);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $y = (int) (($size - $fh) / 2);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        imagestring($img, $fontNum, $x, $y, $text, $white);
    // INSTRUCCIÓN O DECLARACIÓN PHP
    } else {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $x = (int) (($size - ($bbox[2] - $bbox[0])) / 2);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $y = (int) (($size - ($bbox[1] - $bbox[7])) / 2 + ($bbox[1] - $bbox[7]));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        imagettftext($img, $fontSize, 0, $x, $y, $white, __DIR__ . '/../../../../writable/fonts/arial.ttf', $text);
    // DELIMITADOR DE BLOQUE
    }

    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
    $path = "{$dir}/icon-{$size}x{$size}.png";
    // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
    imagepng($img, $path);
    // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
    imagedestroy($img);
    // EMITE SALIDA
    echo "Created: icon-{$size}x{$size}.png\n";
// DELIMITADOR DE BLOQUE
}
// EMITE SALIDA
echo "Done!\n";
