<?php

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Boot;
// IMPORTA UNA CLASE O TRAIT
use Config\Paths;

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *---------------------------------------------------------------
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * CHECK PHP VERSION
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *---------------------------------------------------------------
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
$minPhpVersion = '8.2'; // If you update this, don't forget to update `spark`.
// CONDICIONAL SI
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
    $message = sprintf(
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $minPhpVersion,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        PHP_VERSION,
    // INSTRUCCIÓN O DECLARACIÓN PHP
    );

    // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    // EMITE SALIDA
    echo $message;

    // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
    exit(1);
// DELIMITADOR DE BLOQUE
}

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *---------------------------------------------------------------
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * SET THE CURRENT DIRECTORY
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *---------------------------------------------------------------
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// COMENTARIO DE LÍNEA EXISTENTE
// Path to the front controller (this file)
// LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// COMENTARIO DE LÍNEA EXISTENTE
// Ensure the current directory is pointing to the front controller's directory
// CONDICIONAL SI
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
    chdir(FCPATH);
// DELIMITADOR DE BLOQUE
}

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *---------------------------------------------------------------
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * BOOTSTRAP THE APPLICATION
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *---------------------------------------------------------------
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * This process sets up the path constants, loads and registers
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * our autoloader, along with Composer's, loads our constants
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * and fires up an environment-specific bootstrapping.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// COMENTARIO DE LÍNEA EXISTENTE
// LOAD OUR PATHS CONFIG FILE
// COMENTARIO DE LÍNEA EXISTENTE
// This is the line that might need to be changed, depending on your folder structure.
// INSTRUCCIÓN O DECLARACIÓN PHP
require FCPATH . '../app/Config/Paths.php';
// COMENTARIO DE LÍNEA EXISTENTE
// ^^^ Change this line if you move your application folder

// ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
$paths = new Paths();

// COMENTARIO DE LÍNEA EXISTENTE
// LOAD THE FRAMEWORK BOOTSTRAP FILE
// INSTRUCCIÓN O DECLARACIÓN PHP
require $paths->systemDirectory . '/Boot.php';

// LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
exit(Boot::bootWeb($paths));
