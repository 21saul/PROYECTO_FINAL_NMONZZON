<?php

declare(strict_types=1);

/*
 * RUTAS RELATIVAS DE FOTOS DE REFERENCIA: UNA CADENA O JSON ["ruta1","ruta2"].
 */

if (! function_exists('portrait_reference_photo_paths')) {
    /**
     * @return list<string>
     */
    function portrait_reference_photo_paths(?string $stored): array
    {
        if ($stored === null) {
            return [];
        }
        $stored = trim($stored);
        if ($stored === '') {
            return [];
        }
        if ($stored[0] === '[') {
            $decoded = json_decode($stored, true);
            if (is_array($decoded)) {
                $out = [];
                foreach ($decoded as $p) {
                    if (is_string($p) && $p !== '') {
                        $out[] = $p;
                    }
                }

                return $out;
            }
        }

        return [$stored];
    }
}

if (! function_exists('portrait_reference_photo_store_merged')) {
    /**
     * Añade una ruta nueva; una sola ruta se guarda como string, varias como JSON.
     */
    function portrait_reference_photo_store_merged(?string $existing, string $newRelativePath): string
    {
        $paths   = portrait_reference_photo_paths($existing ?? '');
        $paths[] = $newRelativePath;
        $paths   = array_values(array_unique($paths));
        if (count($paths) === 1) {
            return $paths[0];
        }

        return json_encode($paths, JSON_UNESCAPED_SLASHES);
    }
}
