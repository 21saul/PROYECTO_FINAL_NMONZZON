<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Modules\Modules as BaseModules;

/**
 * Modules Configuration.
 *
 * NOTE: This class is required prior to Autoloader instantiation,
 *       and does not extend BaseConfig.
 */
// DECLARA UNA CLASE
class Modules extends BaseModules
// DELIMITADOR DE BLOQUE
{
    /**
     * --------------------------------------------------------------------------
     * Enable Auto-Discovery?
     * --------------------------------------------------------------------------
     *
     * If true, then auto-discovery will happen across all elements listed in
     * $aliases below. If false, no auto-discovery will happen at all,
     * giving a slight performance boost.
     *
     * @var bool
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public $enabled = true;

    /**
     * --------------------------------------------------------------------------
     * Enable Auto-Discovery Within Composer Packages?
     * --------------------------------------------------------------------------
     *
     * If true, then auto-discovery will happen across all namespaces loaded
     * by Composer, as well as the namespaces configured locally.
     *
     * @var bool
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public $discoverInComposer = true;

    /**
     * The Composer package list for Auto-Discovery
     * This setting is optional.
     *
     * E.g.:
     *   [
     *       'only' => [
     *           // List up all packages to auto-discover
     *           'codeigniter4/shield',
     *       ],
     *   ]
     *   or
     *   [
     *       'exclude' => [
     *           // List up packages to exclude.
     *           'pestphp/pest',
     *       ],
     *   ]
     *
     * @var array{only?: list<string>, exclude?: list<string>}
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public $composerPackages = [];

    /**
     * --------------------------------------------------------------------------
     * Auto-Discovery Rules
     * --------------------------------------------------------------------------
     *
     * Aliases list of all discovery classes that will be active and used during
     * the current application request.
     *
     * If it is not listed, only the base application elements will be used.
     *
     * @var list<string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public $aliases = [
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'events',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'filters',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'registrars',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'routes',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'services',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];
// DELIMITADOR DE BLOQUE
}
