<?php

declare(strict_types=1);

if (! function_exists('branding_parse_service_tags')) {
    /**
     * Normaliza services_provided (array, JSON o texto separado por comas) a lista de etiquetas.
     */
    function branding_parse_service_tags(mixed $raw): array
    {
        if ($raw === null) {
            return [];
        }

        if (! is_array($raw) && ! is_string($raw)) {
            return [];
        }

        if (is_array($raw)) {
            $out = [];
            foreach ($raw as $v) {
                if (is_string($v)) {
                    $t = trim($v);
                    if ($t !== '') {
                        $out[] = $t;
                    }
                }
            }

            return array_values($out);
        }

        $s = trim($raw);
        if ($s === '' || $s === 'Array') {
            return [];
        }

        $decoded = json_decode($s, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return branding_parse_service_tags($decoded);
        }

        return array_values(array_filter(array_map('trim', explode(',', $s))));
    }
}
