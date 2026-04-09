<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Config\Publisher as BasePublisher;

/**
 * Publisher Configuration
 *
 * Defines basic security restrictions for the Publisher class
 * to prevent abuse by injecting malicious files into a project.
 */
// DECLARA UNA CLASE
class Publisher extends BasePublisher
// DELIMITADOR DE BLOQUE
{
    /**
     * A list of allowed destinations with a (pseudo-)regex
     * of allowed files for each destination.
     * Attempts to publish to directories not in this list will
     * result in a PublisherException. Files that do no fit the
     * pattern will cause copy/merge to fail.
     *
     * @var array<string, string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public $restrictions = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        ROOTPATH => '*',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        FCPATH   => '#\.(s?css|js|map|html?|xml|json|webmanifest|ttf|eot|woff2?|gif|jpe?g|tiff?|png|webp|bmp|ico|svg)$#i',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];
// DELIMITADOR DE BLOQUE
}
