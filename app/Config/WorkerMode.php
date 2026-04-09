<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

/**
 * This configuration controls how CodeIgniter behaves when running
 * in worker mode (with FrankenPHP).
 */
// DECLARA UNA CLASE
class WorkerMode
// DELIMITADOR DE BLOQUE
{
    /**
     * Persistent Services
     *
     * List of service names that should persist across requests.
     * These services will NOT be reset between requests.
     *
     * Services not in this list will be reset for each request to prevent
     * state leakage.
     *
     * Recommended persistent services:
     * - `autoloader`: PSR-4 autoloading configuration
     * - `locator`: File locator
     * - `exceptions`: Exception handler
     * - `commands`: CLI commands registry
     * - `codeigniter`: Main application instance
     * - `superglobals`: Superglobals wrapper
     * - `routes`: Router configuration
     * - `cache`: Cache instance
     *
     * @var list<string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $persistentServices = [
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'autoloader',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'locator',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'exceptions',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'commands',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'codeigniter',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'superglobals',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'routes',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'cache',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * Reset Event Listeners
     *
     * List of event names whose listeners should be removed between requests.
     * Use this if you register event listeners inside other event callbacks
     * (rather than at the top level of Config/Events.php), which would cause
     * them to accumulate across requests in worker mode.
     *
     * @var list<string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $resetEventListeners = [];

    /**
     * Force Garbage Collection
     *
     * Whether to force garbage collection after each request.
     * Helps prevent memory leaks at a small performance cost.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $forceGarbageCollection = true;
// DELIMITADOR DE BLOQUE
}
