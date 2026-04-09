<?php

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * FUNCIONES DE VISTA PARA URLs DE IMÁGENES OPTIMIZADAS Y MARCADO LAZY-LOAD.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * PRIORIZA TRANSFORMACIONES DE CLOUDINARY SI HAY PUBLIC_ID Y EL SERVICIO ESTÁ CONFIGURADO.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// CONDICIONAL SI
if (!function_exists('optimizedImage')) {
    // COMENTARIO DE LÍNEA EXISTENTE
    // DEVUELVE LA MEJOR URL DISPONIBLE: CLOUDINARY CON ANCHO, RUTA ABSOLUTA HTTP O PLACEHOLDER
    // DECLARA UNA FUNCIÓN
    function optimizedImage(?string $imageUrl, ?string $cloudinaryPublicId = null, int $width = 800): string
    // DELIMITADOR DE BLOQUE
    {
        // CONDICIONAL SI
        if ($cloudinaryPublicId) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $cloudinary = new \App\Libraries\CloudinaryService();
            // CONDICIONAL SI
            if ($cloudinary->isAvailable()) {
                // RETORNA UN VALOR AL LLAMADOR
                return $cloudinary->generateUrl($cloudinaryPublicId, ['w' => $width, 'q' => 'auto', 'f' => 'auto']);
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }
        // CONDICIONAL SI
        if ($imageUrl) {
            // CONDICIONAL SI
            if (str_starts_with($imageUrl, 'http')) {
                // RETORNA UN VALOR AL LLAMADOR
                return $imageUrl;
            // DELIMITADOR DE BLOQUE
            }
            // RETORNA UN VALOR AL LLAMADOR
            return base_url($imageUrl);
        // DELIMITADOR DE BLOQUE
        }
        // RETORNA UN VALOR AL LLAMADOR
        return base_url('assets/images/placeholder.webp');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}

// CONDICIONAL SI
if (!function_exists('lazyImage')) {
    // COMENTARIO DE LÍNEA EXISTENTE
    // GENERA UNA ETIQUETA IMG CON PLACEHOLDER INMEDIATO, DATA-SRC REAL Y CLASES PARA CARGA DIFERIDA
    // DECLARA UNA FUNCIÓN
    function lazyImage(string $src, string $alt = '', string $class = '', ?string $cloudinaryId = null): string
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $optimizedSrc = optimizedImage($src, $cloudinaryId);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $placeholderSrc = base_url('assets/images/placeholder.webp');
        // RETORNA UN VALOR AL LLAMADOR
        return sprintf(
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            '<img src="%s" data-src="%s" alt="%s" class="lazy-image %s" loading="lazy">',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            esc($placeholderSrc), esc($optimizedSrc), esc($alt), esc($class)
        // INSTRUCCIÓN O DECLARACIÓN PHP
        );
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}

// URL del fondo editorial del área «Mi cuenta» (hero de todas las pantallas de cliente).
if (! function_exists('nmz_mi_cuenta_hero_bg_url')) {
    function nmz_mi_cuenta_hero_bg_url(): string
    {
        $rel  = 'uploads/mi-cuenta/hero-fondo.png';
        $path = FCPATH . $rel;
        $url  = base_url($rel);
        if (is_file($path)) {
            $url .= '?v=' . filemtime($path);
        }

        return $url;
    }
}
