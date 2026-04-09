<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// IMPORTA UNA CLASE O TRAIT
use Kint\Parser\ConstructablePluginInterface;
// IMPORTA UNA CLASE O TRAIT
use Kint\Renderer\Rich\TabPluginInterface;
// IMPORTA UNA CLASE O TRAIT
use Kint\Renderer\Rich\ValuePluginInterface;

/**
 * --------------------------------------------------------------------------
 * Kint
 * --------------------------------------------------------------------------
 *
 * We use Kint's `RichRenderer` and `CLIRenderer`. This area contains options
 * that you can set to customize how Kint works for you.
 *
 * @see https://kint-php.github.io/kint/ for details on these settings.
 */
// DECLARA UNA CLASE
class Kint
// DELIMITADOR DE BLOQUE
{
    // INSTRUCCIÓN O DECLARACIÓN PHP
    /*
    // INSTRUCCIÓN O DECLARACIÓN PHP
    |--------------------------------------------------------------------------
    // INSTRUCCIÓN O DECLARACIÓN PHP
    | Global Settings
    // INSTRUCCIÓN O DECLARACIÓN PHP
    |--------------------------------------------------------------------------
    // CIERRE DE BLOQUE DE DOCUMENTACIÓN
    */

    /**
     * @var list<class-string<ConstructablePluginInterface>|ConstructablePluginInterface>|null
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public $plugins;

    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public int $maxDepth           = 6;
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $displayCalledFrom = true;
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $expanded          = false;

    // INSTRUCCIÓN O DECLARACIÓN PHP
    /*
    // INSTRUCCIÓN O DECLARACIÓN PHP
    |--------------------------------------------------------------------------
    // INSTRUCCIÓN O DECLARACIÓN PHP
    | RichRenderer Settings
    // INSTRUCCIÓN O DECLARACIÓN PHP
    |--------------------------------------------------------------------------
    // CIERRE DE BLOQUE DE DOCUMENTACIÓN
    */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $richTheme = 'aante-light.css';
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $richFolder  = false;

    /**
     * @var array<string, class-string<ValuePluginInterface>>|null
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public $richObjectPlugins;

    /**
     * @var array<string, class-string<TabPluginInterface>>|null
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public $richTabPlugins;

    // INSTRUCCIÓN O DECLARACIÓN PHP
    /*
    // INSTRUCCIÓN O DECLARACIÓN PHP
    |--------------------------------------------------------------------------
    // INSTRUCCIÓN O DECLARACIÓN PHP
    | CLI Settings
    // INSTRUCCIÓN O DECLARACIÓN PHP
    |--------------------------------------------------------------------------
    // CIERRE DE BLOQUE DE DOCUMENTACIÓN
    */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $cliColors      = true;
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $cliForceUTF8   = false;
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $cliDetectWidth = true;
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public int $cliMinWidth     = 40;
// DELIMITADOR DE BLOQUE
}
