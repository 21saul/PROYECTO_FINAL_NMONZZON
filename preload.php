<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Boot;
// IMPORTA UNA CLASE O TRAIT
use Config\Paths;

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *---------------------------------------------------------------
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * Sample file for Preloading
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *---------------------------------------------------------------
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * See https://www.php.net/manual/en/opcache.preloading.php
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * How to Use:
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *   0. Copy this file to your project root folder.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *   1. Set the $paths property of the preload class below.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *   2. Set opcache.preload in php.ini.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *     php.ini:
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 *     opcache.preload=/path/to/preload.php
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// COMENTARIO DE LÍNEA EXISTENTE
// Load the paths config file
// INSTRUCCIÓN O DECLARACIÓN PHP
require __DIR__ . '/app/Config/Paths.php';

// COMENTARIO DE LÍNEA EXISTENTE
// Path to the front controller
// LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

// DECLARA UNA CLASE
class preload
// DELIMITADOR DE BLOQUE
{
    /**
     * @var array Paths to preload.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private array $paths = [
        // INSTRUCCIÓN O DECLARACIÓN PHP
        [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'include' => __DIR__ . '/vendor/codeigniter4/framework/system', // Change this path if using manual installation
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'exclude' => [
                // COMENTARIO DE LÍNEA EXISTENTE
                // Not needed if you don't use them.
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/Database/OCI8/',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/Database/Postgre/',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/Database/SQLite3/',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/Database/SQLSRV/',
                // COMENTARIO DE LÍNEA EXISTENTE
                // Not needed for web apps.
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/Database/Seeder.php',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/Test/',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/CLI/',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/Commands/',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/Publisher/',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/ComposerScripts.php',
                // COMENTARIO DE LÍNEA EXISTENTE
                // Not Class/Function files.
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/Config/Routes.php',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/Language/',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/bootstrap.php',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/util_bootstrap.php',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/rewrite.php',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/Views/',
                // COMENTARIO DE LÍNEA EXISTENTE
                // Errors occur.
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/system/ThirdParty/',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function __construct()
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->loadAutoloader();
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function loadAutoloader(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $paths = new Paths();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'Boot.php';

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        Boot::preload($paths);
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Load PHP files.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function load(): void
    // DELIMITADOR DE BLOQUE
    {
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($this->paths as $path) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $directory = new RecursiveDirectoryIterator($path['include']);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $fullTree  = new RecursiveIteratorIterator($directory);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $phpFiles  = new RegexIterator(
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $fullTree,
                // INSTRUCCIÓN O DECLARACIÓN PHP
                '/.+((?<!Test)+\.php$)/i',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                RecursiveRegexIterator::GET_MATCH,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            );

            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($phpFiles as $key => $file) {
                // BUCLE FOREACH SOBRE COLECCIÓN
                foreach ($path['exclude'] as $exclude) {
                    // CONDICIONAL SI
                    if (str_contains($file[0], $exclude)) {
                        // SALTA A LA SIGUIENTE ITERACIÓN
                        continue 2;
                    // DELIMITADOR DE BLOQUE
                    }
                // DELIMITADOR DE BLOQUE
                }

                // INSTRUCCIÓN O DECLARACIÓN PHP
                require_once $file[0];
                // COMENTARIO DE LÍNEA EXISTENTE
                // Uncomment only for debugging (to inspect which files are included).
                // COMENTARIO DE LÍNEA EXISTENTE
                // Never use this in production - preload scripts must not generate output.
                // COMENTARIO DE LÍNEA EXISTENTE
                // echo 'Loaded: ' . $file[0] . "\n";
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}

// LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
(new preload())->load();
