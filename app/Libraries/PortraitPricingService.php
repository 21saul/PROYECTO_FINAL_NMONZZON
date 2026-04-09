<?php

declare(strict_types=1);

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Libraries;

// IMPORTA UNA CLASE O TRAIT
use App\Models\PortraitSizeModel;
// IMPORTA UNA CLASE O TRAIT
use App\Models\PortraitStyleModel;

/**
 * SERVICIO CENTRALIZADO DE CÁLCULO DE PRECIOS DE RETRATOS.
 * UNIFICA LA LÓGICA QUE ANTES ESTABA DUPLICADA EN RETRATOSCONTROLLER Y PORTRAITORDERCONTROLLER.
 */
// DECLARA UNA CLASE
class PortraitPricingService
// DELIMITADOR DE BLOQUE
{
    // COMENTARIO DE LÍNEA EXISTENTE
    // PRECIOS FIJOS PARA ESTILO «COLOR» SEGÚN NÚMERO DE FIGURAS (CATÁLOGO REAL WIX)
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private const COLOR_FIGURE_PRICES = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        1 => 73, 2 => 132, 3 => 197, 4 => 263, 5 => 327,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        6 => 393, 7 => 433, 8 => 495, 9 => 523, 10 => 581,
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * @return array{base_price: float, size_modifier: float, extras_price: float, total_price: float, breakdown: array}
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function calculate(
        // INSTRUCCIÓN O DECLARACIÓN PHP
        int $styleId,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        int $sizeId,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        int $numFigures,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        bool $withFrame = false,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        ?string $frameType = null
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ): array {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $style = model(PortraitStyleModel::class)->find($styleId);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $size  = model(PortraitSizeModel::class)->find($sizeId);

        // CONDICIONAL SI
        if (!$style || !$size) {
            // LANZA UNA EXCEPCIÓN
            throw new \InvalidArgumentException('Estilo o tamaño no encontrado.');
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $slug = strtolower((string) ($style['slug'] ?? ''));
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $isColorStyle = ($slug === 'color' || strcasecmp((string) ($style['name'] ?? ''), 'Color') === 0);

        // CONDICIONAL SI
        if ($isColorStyle && isset(self::COLOR_FIGURE_PRICES[$numFigures])) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $basePrice = (float) self::COLOR_FIGURE_PRICES[$numFigures];
        // INSTRUCCIÓN O DECLARACIÓN PHP
        } else {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $basePrice = (float) ($style['base_price'] ?? 0);
            // CONDICIONAL SI
            if ($numFigures > 1) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $basePrice += ($numFigures - 1) * (float) ($style['base_price'] ?? 0) * 0.25;
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $sizeModifier = (float) ($size['price_modifier'] ?? 0);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $extrasPrice = 0.0;
        // CONDICIONAL SI
        if ($withFrame) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $extrasPrice += ($basePrice + $sizeModifier) * 0.15;
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $totalPrice = round($basePrice + $sizeModifier + $extrasPrice, 2);

        // RETORNA UN VALOR AL LLAMADOR
        return [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'base_price'    => round($basePrice, 2),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'size_modifier' => round($sizeModifier, 2),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'extras_price'  => round($extrasPrice, 2),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'total_price'   => $totalPrice,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'breakdown'     => [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'style_name'  => $style['name'] ?? '',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'size_name'   => $size['name'] ?? '',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'num_figures' => $numFigures,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'with_frame'  => $withFrame,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'frame_type'  => $frameType,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
