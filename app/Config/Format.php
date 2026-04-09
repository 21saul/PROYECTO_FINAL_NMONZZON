<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Config\BaseConfig;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Format\JSONFormatter;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Format\XMLFormatter;

// DECLARA UNA CLASE
class Format extends BaseConfig
// DELIMITADOR DE BLOQUE
{
    /**
     * --------------------------------------------------------------------------
     * Available Response Formats
     * --------------------------------------------------------------------------
     *
     * When you perform content negotiation with the request, these are the
     * available formats that your application supports. This is currently
     * only used with the API\ResponseTrait. A valid Formatter must exist
     * for the specified format.
     *
     * These formats are only checked when the data passed to the respond()
     * method is an array.
     *
     * @var list<string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $supportedResponseFormats = [
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'application/json',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'application/xml', // machine-readable XML
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'text/xml', // human-readable XML
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * --------------------------------------------------------------------------
     * Formatters
     * --------------------------------------------------------------------------
     *
     * Lists the class to use to format responses with of a particular type.
     * For each mime type, list the class that should be used. Formatters
     * can be retrieved through the getFormatter() method.
     *
     * @var array<string, string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $formatters = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'application/json' => JSONFormatter::class,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'application/xml'  => XMLFormatter::class,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'text/xml'         => XMLFormatter::class,
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * --------------------------------------------------------------------------
     * Formatters Options
     * --------------------------------------------------------------------------
     *
     * Additional Options to adjust default formatters behaviour.
     * For each mime type, list the additional options that should be used.
     *
     * @var array<string, int>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $formatterOptions = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'application/json' => JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'application/xml'  => 0,
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'text/xml'         => 0,
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * --------------------------------------------------------------------------
     * Maximum depth for JSON encoding.
     * --------------------------------------------------------------------------
     *
     * This value determines how deep the JSON encoder will traverse nested structures.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public int $jsonEncodeDepth = 512;
// DELIMITADOR DE BLOQUE
}
