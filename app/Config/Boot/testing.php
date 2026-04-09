<?php

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * The environment testing is reserved for PHPUnit testing. It has special
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * conditions built into the framework at various places to assist with that.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * You can’t use it for your development.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // INSTRUCCIÓN O DECLARACIÓN PHP
 |--------------------------------------------------------------------------
 // INSTRUCCIÓN O DECLARACIÓN PHP
 | ERROR DISPLAY
 // INSTRUCCIÓN O DECLARACIÓN PHP
 |--------------------------------------------------------------------------
 // INSTRUCCIÓN O DECLARACIÓN PHP
 | In development, we want to show as many errors as possible to help
 // INSTRUCCIÓN O DECLARACIÓN PHP
 | make sure they don't make it to production. And save us hours of
 // INSTRUCCIÓN O DECLARACIÓN PHP
 | painful debugging.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */
// LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
error_reporting(E_ALL);
// LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
ini_set('display_errors', '1');

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // INSTRUCCIÓN O DECLARACIÓN PHP
 |--------------------------------------------------------------------------
 // INSTRUCCIÓN O DECLARACIÓN PHP
 | DEBUG BACKTRACES
 // INSTRUCCIÓN O DECLARACIÓN PHP
 |--------------------------------------------------------------------------
 // INSTRUCCIÓN O DECLARACIÓN PHP
 | If true, this constant will tell the error screens to display debug
 // INSTRUCCIÓN O DECLARACIÓN PHP
 | backtraces along with the other error information. If you would
 // INSTRUCCIÓN O DECLARACIÓN PHP
 | prefer to not see this, set this value to false.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */
// LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
defined('SHOW_DEBUG_BACKTRACE') || define('SHOW_DEBUG_BACKTRACE', true);

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // INSTRUCCIÓN O DECLARACIÓN PHP
 |--------------------------------------------------------------------------
 // INSTRUCCIÓN O DECLARACIÓN PHP
 | DEBUG MODE
 // INSTRUCCIÓN O DECLARACIÓN PHP
 |--------------------------------------------------------------------------
 // INSTRUCCIÓN O DECLARACIÓN PHP
 | Debug mode is an experimental flag that can allow changes throughout
 // INSTRUCCIÓN O DECLARACIÓN PHP
 | the system. It's not widely used currently, and may not survive
 // INSTRUCCIÓN O DECLARACIÓN PHP
 | release of the framework.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */
// LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
defined('CI_DEBUG') || define('CI_DEBUG', true);
