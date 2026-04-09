<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Config\BaseConfig;

// DECLARA UNA CLASE
class CURLRequest extends BaseConfig
// DELIMITADOR DE BLOQUE
{
    /**
     * --------------------------------------------------------------------------
     * CURLRequest Share Connection Options
     * --------------------------------------------------------------------------
     *
     * Share connection options between requests.
     *
     * @var list<int>
     *
     * @see https://www.php.net/manual/en/curl.constants.php#constant.curl-lock-data-connect
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $shareConnectionOptions = [
        // INSTRUCCIÓN O DECLARACIÓN PHP
        CURL_LOCK_DATA_CONNECT,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        CURL_LOCK_DATA_DNS,
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * --------------------------------------------------------------------------
     * CURLRequest Share Options
     * --------------------------------------------------------------------------
     *
     * Whether share options between requests or not.
     *
     * If true, all the options won't be reset between requests.
     * It may cause an error request with unnecessary headers.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $shareOptions = false;
// DELIMITADOR DE BLOQUE
}
