<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Config\BaseConfig;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Debug\Toolbar\Collectors\Database;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Debug\Toolbar\Collectors\Events;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Debug\Toolbar\Collectors\Files;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Debug\Toolbar\Collectors\Logs;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Debug\Toolbar\Collectors\Routes;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Debug\Toolbar\Collectors\Timers;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Debug\Toolbar\Collectors\Views;

/**
 * --------------------------------------------------------------------------
 * Debug Toolbar
 * --------------------------------------------------------------------------
 *
 * The Debug Toolbar provides a way to see information about the performance
 * and state of your application during that page display. By default it will
 * NOT be displayed under production environments, and will only display if
 * `CI_DEBUG` is true, since if it's not, there's not much to display anyway.
 */
// DECLARA UNA CLASE
class Toolbar extends BaseConfig
// DELIMITADOR DE BLOQUE
{
    /**
     * --------------------------------------------------------------------------
     * Toolbar Collectors
     * --------------------------------------------------------------------------
     *
     * List of toolbar collectors that will be called when Debug Toolbar
     * fires up and collects data from.
     *
     * @var list<class-string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $collectors = [
        // INSTRUCCIÓN O DECLARACIÓN PHP
        Timers::class,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        Database::class,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        Logs::class,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        Views::class,
        // COMENTARIO DE LÍNEA EXISTENTE
        // \CodeIgniter\Debug\Toolbar\Collectors\Cache::class,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        Files::class,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        Routes::class,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        Events::class,
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * --------------------------------------------------------------------------
     * Collect Var Data
     * --------------------------------------------------------------------------
     *
     * If set to false var data from the views will not be collected. Useful to
     * avoid high memory usage when there are lots of data passed to the view.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $collectVarData = true;

    /**
     * --------------------------------------------------------------------------
     * Max History
     * --------------------------------------------------------------------------
     *
     * `$maxHistory` sets a limit on the number of past requests that are stored,
     * helping to conserve file space used to store them. You can set it to
     * 0 (zero) to not have any history stored, or -1 for unlimited history.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public int $maxHistory = 20;

    /**
     * --------------------------------------------------------------------------
     * Toolbar Views Path
     * --------------------------------------------------------------------------
     *
     * The full path to the the views that are used by the toolbar.
     * This MUST have a trailing slash.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $viewsPath = SYSTEMPATH . 'Debug/Toolbar/Views/';

    /**
     * --------------------------------------------------------------------------
     * Max Queries
     * --------------------------------------------------------------------------
     *
     * If the Database Collector is enabled, it will log every query that the
     * the system generates so they can be displayed on the toolbar's timeline
     * and in the query log. This can lead to memory issues in some instances
     * with hundreds of queries.
     *
     * `$maxQueries` defines the maximum amount of queries that will be stored.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public int $maxQueries = 100;

    /**
     * --------------------------------------------------------------------------
     * Watched Directories
     * --------------------------------------------------------------------------
     *
     * Contains an array of directories that will be watched for changes and
     * used to determine if the hot-reload feature should reload the page or not.
     * We restrict the values to keep performance as high as possible.
     *
     * NOTE: The ROOTPATH will be prepended to all values.
     *
     * @var list<string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $watchedDirectories = [
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'app',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * --------------------------------------------------------------------------
     * Watched File Extensions
     * --------------------------------------------------------------------------
     *
     * Contains an array of file extensions that will be watched for changes and
     * used to determine if the hot-reload feature should reload the page or not.
     *
     * @var list<string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $watchedExtensions = [
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'php', 'css', 'js', 'html', 'svg', 'json', 'env',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * --------------------------------------------------------------------------
     * Ignored HTTP Headers
     * --------------------------------------------------------------------------
     *
     * CodeIgniter Debug Toolbar normally injects HTML and JavaScript into every
     * HTML response. This is correct for full page loads, but it breaks requests
     * that expect only a clean HTML fragment.
     *
     * Libraries like HTMX, Unpoly, and Hotwire (Turbo) update parts of the page or
     * manage navigation on the client side. Injecting the Debug Toolbar into their
     * responses can cause invalid HTML, duplicated scripts, or JavaScript errors
     * (such as infinite loops or "Maximum call stack size exceeded").
     *
     * Any request containing one of the following headers is treated as a
     * client-managed or partial request, and the Debug Toolbar injection is skipped.
     *
     * @var array<string, string|null>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $disableOnHeaders = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'X-Requested-With' => 'xmlhttprequest', // AJAX requests
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'HX-Request'       => 'true',           // HTMX requests
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'X-Up-Version'     => null,             // Unpoly partial requests
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];
// DELIMITADOR DE BLOQUE
}
