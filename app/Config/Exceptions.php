<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CONFIG CI4: APP/CONFIG/EXCEPTIONS.PHP
 * =============================================================================
 * PLANTILLAS Y COMPORTAMIENTO DE EXCEPCIONES NO CAPTURADAS (HTML/CLI).
 * EN PRODUCCIÓN SE COMBINA CON Boot/production.php PARA NO FILTRAR DETALLES.
 * =============================================================================
 */

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Debug\ExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use Psr\Log\LogLevel;
use Throwable;

/**
 * Setup how the exception handler works.
 */
class Exceptions extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * LOG EXCEPTIONS?
     * --------------------------------------------------------------------------
     * If true, then exceptions will be logged
     * through Services::Log.
     *
     * Default: true
     */
    public bool $log = true;

    /**
     * --------------------------------------------------------------------------
     * DO NOT LOG STATUS CODES
     * --------------------------------------------------------------------------
     * Any status codes here will NOT be logged if logging is turned on.
     * By default, only 404 (Page Not Found) exceptions are ignored.
     *
     * @var list<int>
     */
    public array $ignoreCodes = [404];

    /**
     * --------------------------------------------------------------------------
     * Error Views Path
     * --------------------------------------------------------------------------
     * This is the path to the directory that contains the 'cli' and 'html'
     * directories that hold the views used to generate errors.
     *
     * Default: APPPATH.'Views/errors'
     */
    public string $errorViewPath = APPPATH . 'Views/errors';

    /**
     * --------------------------------------------------------------------------
     * HIDE FROM DEBUG TRACE
     * --------------------------------------------------------------------------
     * Any data that you would like to hide from the debug trace.
     * In order to specify 2 levels, use "/" to separate.
     * ex. ['server', 'setup/password', 'secret_token']
     *
     * @var list<string>
     */
    public array $sensitiveDataInTrace = [];

    /**
     * --------------------------------------------------------------------------
     * WHETHER TO THROW AN EXCEPTION ON DEPRECATED ERRORS
     * --------------------------------------------------------------------------
     * If set to `true`, DEPRECATED errors are only logged and no exceptions are
     * thrown. This option also works for user deprecations.
     */
    public bool $logDeprecations = true;

    /**
     * --------------------------------------------------------------------------
     * LOG LEVEL THRESHOLD FOR DEPRECATIONS
     * --------------------------------------------------------------------------
     * If `$logDeprecations` is set to `true`, this sets the log level
     * to which the deprecation will be logged. This should be one of the log
     * levels recognized by PSR-3.
     *
     * The related `Config\Logger::$threshold` should be adjusted, if needed,
     * to capture logging the deprecations.
     */
    public string $deprecationLogLevel = LogLevel::WARNING;

    /*
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * DEFINE THE HANDLERS USED
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * --------------------------------------------------------------------------
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * Given the HTTP status code, returns exception handler that
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * should be used to deal with this error. By default, it will run CodeIgniter's
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * default handler and display the error information in the expected format
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * for CLI, HTTP, or AJAX requests, as determined by is_cli() and the expected
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * response format.
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * Custom handlers can be returned if you want to handle one or more specific
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * error codes yourself like:
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *      if (in_array($statusCode, [400, 404, 500])) {
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *          return new \App\Libraries\MyExceptionHandler();
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *      }
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *      if ($exception instanceOf PageNotFoundException) {
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *          return new \App\Libraries\MyExceptionHandler();
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *      }
     */
    public function handler(int $statusCode, Throwable $exception): ExceptionHandlerInterface
    {
        return new ExceptionHandler($this);
    }
}