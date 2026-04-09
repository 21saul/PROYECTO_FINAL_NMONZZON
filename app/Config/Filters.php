<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CONFIG CI4: APP/CONFIG/FILTERS.PHP
 * =============================================================================
 * REGISTRO Y ALIAS DE FILTROS HTTP (CORS, AUTH, RATE LIMIT, CABECERAS, ETC.).
 * LOS FILTROS SE APLICAN EN Routes O EN CONTROLADORES SEGÚN NECESIDAD DE SEGURIDAD Y RENDIMIENTO.
 * =============================================================================
 */

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    /**
     * ALIAS DE CLASES DE FILTRO PARA USARLOS POR NOMBRE CORTO EN RUTAS Y CONFIGURACIÓN.
     *
     * @var array<string, class-string|list<class-string>>
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => \App\Filters\SecurityHeadersFilter::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
        'ratelimit'     => \App\Filters\RateLimitFilter::class,
        'auth'          => \App\Filters\AuthFilter::class,
        'admin'         => \App\Filters\AdminFilter::class,
        'clientauth'    => \App\Filters\ClientAuthFilter::class,
    ];

    /**
     * FILTROS ESPECIALES QUE EL FRAMEWORK APLICA SIEMPRE (ANTES Y DESPUÉS), INCLUSO SI LA RUTA NO EXISTE.
     *
     * @var array{before: list<string>, after: list<string>}
     */
    public array $required = [
        'before' => [
            'forcehttps', // FORZAR PETICIONES SEGURAS (HTTPS)
            'pagecache',  // CACHÉ DE PÁGINAS WEB
        ],
        'after' => [
            'pagecache',   // CACHÉ DE PÁGINAS WEB
            'performance', // MÉTRICAS DE RENDIMIENTO
            'toolbar',     // BARRA DE DEPURACIÓN
        ],
    ];

    /**
     * FILTROS GLOBALES ANTES Y DESPUÉS DE CADA PETICIÓN (CON EXCEPCIONES DONDE SE INDICA).
     *
     * @var array{
     *     before: array<string, array{except: list<string>|string}>|list<string>,
     *     after: array<string, array{except: list<string>|string}>|list<string>
     * }
     */
    public array $globals = [
        'before' => [
            'csrf' => ['except' => ['api/*', 'stripe/webhook']],
            'honeypot',
            'invalidchars',
        ],
        'after' => [
            'secureheaders',
        ],
    ];

    /**
     * FILTROS APLICADOS SOLO A CIERTOS MÉTODOS HTTP (GET, POST, ETC.).
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * FILTROS ASOCIADOS A PATRONES DE URI ESPECÍFICOS (ANTES O DESPUÉS).
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [];
}