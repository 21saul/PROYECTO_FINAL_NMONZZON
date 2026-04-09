<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Cache\CacheInterface;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Cache\Handlers\ApcuHandler;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Cache\Handlers\DummyHandler;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Cache\Handlers\FileHandler;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Cache\Handlers\MemcachedHandler;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Cache\Handlers\PredisHandler;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Cache\Handlers\RedisHandler;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Cache\Handlers\WincacheHandler;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Config\BaseConfig;

// DECLARA UNA CLASE
class Cache extends BaseConfig
// DELIMITADOR DE BLOQUE
{
    /**
     * --------------------------------------------------------------------------
     * Primary Handler
     * --------------------------------------------------------------------------
     *
     * The name of the preferred handler that should be used. If for some reason
     * it is not available, the $backupHandler will be used in its place.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $handler = 'file';

    /**
     * --------------------------------------------------------------------------
     * Backup Handler
     * --------------------------------------------------------------------------
     *
     * The name of the handler that will be used in case the first one is
     * unreachable. Often, 'file' is used here since the filesystem is
     * always available, though that's not always practical for the app.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $backupHandler = 'dummy';

    /**
     * --------------------------------------------------------------------------
     * Key Prefix
     * --------------------------------------------------------------------------
     *
     * This string is added to all cache item names to help avoid collisions
     * if you run multiple applications with the same cache engine.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $prefix = '';

    /**
     * --------------------------------------------------------------------------
     * Default TTL
     * --------------------------------------------------------------------------
     *
     * The default number of seconds to save items when none is specified.
     *
     * WARNING: This is not used by framework handlers where 60 seconds is
     * hard-coded, but may be useful to projects and modules. This will replace
     * the hard-coded value in a future release.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public int $ttl = 60;

    /**
     * --------------------------------------------------------------------------
     * Reserved Characters
     * --------------------------------------------------------------------------
     *
     * A string of reserved characters that will not be allowed in keys or tags.
     * Strings that violate this restriction will cause handlers to throw.
     * Default: {}()/\@:
     *
     * NOTE: The default set is required for PSR-6 compliance.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $reservedCharacters = '{}()/\@:';

    /**
     * --------------------------------------------------------------------------
     * File settings
     * --------------------------------------------------------------------------
     *
     * Your file storage preferences can be specified below, if you are using
     * the File driver.
     *
     * @var array{storePath?: string, mode?: int}
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $file = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'storePath' => WRITEPATH . 'cache/',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mode'      => 0640,
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * -------------------------------------------------------------------------
     * Memcached settings
     * -------------------------------------------------------------------------
     *
     * Your Memcached servers can be specified below, if you are using
     * the Memcached drivers.
     *
     * @see https://codeigniter.com/user_guide/libraries/caching.html#memcached
     *
     * @var array{host?: string, port?: int, weight?: int, raw?: bool}
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $memcached = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'host'   => '127.0.0.1',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'port'   => 11211,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'weight' => 1,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'raw'    => false,
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * -------------------------------------------------------------------------
     * Redis settings
     * -------------------------------------------------------------------------
     *
     * Your Redis server can be specified below, if you are using
     * the Redis or Predis drivers.
     *
     * @var array{
     *     host?: string,
     *     password?: string|null,
     *     port?: int,
     *     timeout?: int,
     *     async?: bool,
     *     persistent?: bool,
     *     database?: int
     * }
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $redis = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'host'       => '127.0.0.1',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'password'   => null,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'port'       => 6379,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'timeout'    => 0,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'async'      => false, // specific to Predis and ignored by the native Redis extension
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'persistent' => false,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'database'   => 0,
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * --------------------------------------------------------------------------
     * Available Cache Handlers
     * --------------------------------------------------------------------------
     *
     * This is an array of cache engine alias' and class names. Only engines
     * that are listed here are allowed to be used.
     *
     * @var array<string, class-string<CacheInterface>>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $validHandlers = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'apcu'      => ApcuHandler::class,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'dummy'     => DummyHandler::class,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'file'      => FileHandler::class,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'memcached' => MemcachedHandler::class,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'predis'    => PredisHandler::class,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'redis'     => RedisHandler::class,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'wincache'  => WincacheHandler::class,
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * --------------------------------------------------------------------------
     * Web Page Caching: Cache Include Query String
     * --------------------------------------------------------------------------
     *
     * Whether to take the URL query string into consideration when generating
     * output cache files. Valid options are:
     *
     *    false = Disabled
     *    true  = Enabled, take all query parameters into account.
     *            Please be aware that this may result in numerous cache
     *            files generated for the same page over and over again.
     *    ['q'] = Enabled, but only take into account the specified list
     *            of query parameters.
     *
     * @var bool|list<string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public $cacheQueryString = false;

    /**
     * --------------------------------------------------------------------------
     * Web Page Caching: Cache Status Codes
     * --------------------------------------------------------------------------
     *
     * HTTP status codes that are allowed to be cached. Only responses with
     * these status codes will be cached by the PageCache filter.
     *
     * Default: [] - Cache all status codes (backward compatible)
     *
     * Recommended: [200] - Only cache successful responses
     *
     * You can also use status codes like:
     *   [200, 404, 410] - Cache successful responses and specific error codes
     *   [200, 201, 202, 203, 204] - All 2xx successful responses
     *
     * WARNING: Using [] may cache temporary error pages (404, 500, etc).
     * Consider restricting to [200] for production applications to avoid
     * caching errors that should be temporary.
     *
     * @var list<int>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $cacheStatusCodes = [];
// DELIMITADOR DE BLOQUE
}
