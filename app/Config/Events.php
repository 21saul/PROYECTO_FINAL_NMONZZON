<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Events\Events;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Exceptions\FrameworkException;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\HotReloader\HotReloader;

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * --------------------------------------------------------------------
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * Application Events
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * --------------------------------------------------------------------
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * Events allow you to tap into the execution of the program without
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * modifying or extending core files. This file provides a central
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * location to define your events, though they can always be added
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * at run-time, also, if needed.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * You create code that can execute by subscribing to events with
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * the 'on()' method. This accepts any form of callable, including
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * Closures, that will be executed when the event is triggered.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * Example:
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *      Events::on('create', [$myInstance, 'myMethod']);
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// INSTRUCCIÓN O DECLARACIÓN PHP
Events::on('pre_system', static function (): void {
    // CONDICIONAL SI
    if (ENVIRONMENT !== 'testing') {
        // CONDICIONAL SI
        if (ini_get('zlib.output_compression')) {
            // LANZA UNA EXCEPCIÓN
            throw FrameworkException::forEnabledZlibOutputCompression();
        // DELIMITADOR DE BLOQUE
        }

        // BUCLE WHILE
        while (ob_get_level() > 0) {
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            ob_end_flush();
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        ob_start(static fn ($buffer) => $buffer);
    // DELIMITADOR DE BLOQUE
    }

    // INSTRUCCIÓN O DECLARACIÓN PHP
    /*
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * --------------------------------------------------------------------
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * Debug Toolbar Listeners.
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * --------------------------------------------------------------------
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * If you delete, they will no longer be collected.
     // CIERRE DE BLOQUE DE DOCUMENTACIÓN
     */
    // CONDICIONAL SI
    if (CI_DEBUG && ! is_cli()) {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        service('toolbar')->respond();
        // COMENTARIO DE LÍNEA EXISTENTE
        // Hot Reload route - for framework use on the hot reloader.
        // CONDICIONAL SI
        if (ENVIRONMENT === 'development') {
            // INSTRUCCIÓN O DECLARACIÓN PHP
            service('routes')->get('__hot-reload', static function (): void {
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                (new HotReloader())->run();
            // INSTRUCCIÓN O DECLARACIÓN PHP
            });
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }
// INSTRUCCIÓN O DECLARACIÓN PHP
});
